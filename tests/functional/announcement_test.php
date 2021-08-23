<?php
/**
*
* Board Announcements extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\boardannouncements\tests\functional;

use phpbb\boardannouncements\acp\board_announcements_module;

/**
* @group functional
*/
class announcement_test extends \phpbb_functional_test_case
{
	/**
	* Define the extensions to be tested
	*
	* @return array vendor/name of extension(s) to test
	*/
	protected static function setup_extensions()
	{
		return array('phpbb/boardannouncements');
	}

	protected function setUp(): void
	{
		parent::setUp();
		$this->add_lang_ext('phpbb/boardannouncements', array('boardannouncements_acp', 'info_acp_board_announcements'));
	}

	/**
	* Test board announcement ACP page and save settings
	*/
	public function test_set_acp_settings()
	{
		$this->login();
		$this->admin_login();

		// Load ACP page
		$crawler = self::request('GET', 'adm/index.php?i=\phpbb\boardannouncements\acp\board_announcements_module&amp;mode=settings&sid=' . $this->sid);

		// Test that our settings fields are found
		$this->assertContainsLang('BOARD_ANNOUNCEMENTS_ENABLE', $crawler->text());
		$this->assertContainsLang('BOARD_ANNOUNCEMENTS_INDEX_ONLY', $crawler->text());
		$this->assertContainsLang('BOARD_ANNOUNCEMENTS_USERS', $crawler->text());
		$this->assertContainsLang('BOARD_ANNOUNCEMENTS_DISMISS', $crawler->text());
		$this->assertContainsLang('BOARD_ANNOUNCEMENTS_BGCOLOR', $crawler->text());
		$this->assertContainsLang('BOARD_ANNOUNCEMENTS_TEXT', $crawler->text());

		// Set some form values
		$form = $crawler->selectButton($this->lang('SUBMIT'))->form();
		$values = array(
			'board_announcements_enable'	=> true,
			'board_announcements_index_only'=> true,
			'board_announcements_users'		=> 0,
			'board_announcements_dismiss'	=> true,
			'board_announcements_bgcolor'	=> 'ff0000',
			'board_announcements_text'		=> 'This is a board announcement test.',
		);
		$form->setValues($values);

		// Submit form and test success
		$crawler = self::submit($form);
		$this->assertContainsLang('BOARD_ANNOUNCEMENTS_UPDATED', $crawler->text());

		// Confirm the log entry has been added correctly
		$crawler = self::request('GET', 'adm/index.php?i=acp_logs&mode=admin&sid=' . $this->sid);
		self::assertStringContainsString(strip_tags($this->lang('BOARD_ANNOUNCEMENTS_UPDATED_LOG')), $crawler->text());
	}

	/**
	* Test loading the board announcement as a user
	*/
	public function test_view_as_user()
	{
		$this->login();
		$crawler = self::request('GET', 'index.php');
		self::assertStringContainsString('This is a board announcement test.', $crawler->filter('#phpbb_announcement')->text());
	}

	/**
	 * Test board announcement displays on index only (as configured in test_set_acp_settings)
	 */
	public function test_view_off_index()
	{
		$this->login();
		$crawler = self::request('GET', 'memberlist.php');
		self::assertCount(0, $crawler->filter('#phpbb_announcement'));
	}

	/**
	* Test loading the board announcement as a newly registered user
	*/
	public function test_view_as_new_user()
	{
		$this->create_user('new_user1');
		$this->login('new_user1');

		$crawler = self::request('GET', 'index.php');
		self::assertStringContainsString('This is a board announcement test.', $crawler->filter('#phpbb_announcement')->text());

		// Verify that new users won't see the announcement if it's Guest only
		$this->db->sql_query('UPDATE ' . CONFIG_TABLE . ' SET config_value = ' .
			(int) board_announcements_module::GUESTS . "
			WHERE config_name = 'board_announcements_users'");
		$this->purge_cache();
		$crawler = self::request('GET', 'index.php');
		self::assertStringNotContainsString('This is a board announcement test.', $crawler->text());
	}

	/**
	* Test loading the board announcement as a guest
	*/
	public function test_view_as_guest()
	{
		$crawler = self::request('GET', 'index.php');
		self::assertStringContainsString($this->lang('LOGIN'), $crawler->filter('.navbar')->text());
		self::assertStringContainsString('This is a board announcement test.', $crawler->filter('#phpbb_announcement')->text());
	}

	/**
	* Test closing the board announcement
	*/
	public function test_close_announcement()
	{
		$this->login();

		self::request('GET', 'app.php/boardannouncements/close?hash=' . $this->mock_link_hash('close_boardannouncement') . '&sid=' . $this->sid);
		$crawler = self::request('GET', 'index.php');
		self::assertCount(0, $crawler->filter('#phpbb_announcement'));
	}

	/**
	* Test closing the board announcement failure
	*/
	public function test_close_announcement_fail()
	{
		$this->login();

		// Wrong hash
		$crawler = self::request('GET', 'app.php/boardannouncements/close?hash=wrong&sid=' . $this->sid, array(), false);
		self::assert_response_status_code(403);
		$this->assertContainsLang('NO_AUTH_OPERATION', $crawler->text());

		// No hash
		$crawler = self::request('GET', 'app.php/boardannouncements/close?sid=' . $this->sid, array(), false);
		self::assert_response_status_code(403);
		$this->assertContainsLang('NO_AUTH_OPERATION', $crawler->text());
	}

	/**
	* Create a link hash for the user 'admin'
	*
	* @param string  $link_name The name of the link
	* @return string the hash
	*/
	protected function mock_link_hash($link_name)
	{
		$this->get_db();

		$sql = "SELECT user_form_salt
			FROM phpbb_users
			WHERE username = 'admin'";
		$result = $this->db->sql_query($sql);
		$user_form_salt = $this->db->sql_fetchfield('user_form_salt');
		$this->db->sql_freeresult($result);

		return substr(sha1($user_form_salt . $link_name), 0, 8);
	}
}
