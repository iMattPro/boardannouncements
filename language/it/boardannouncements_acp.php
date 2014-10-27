<?php
/**
*
* Board Announcements extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/
/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
exit;
}
if (empty($lang) || !is_array($lang))
{
$lang = array();
}
// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//
$lang = array_merge($lang, array(
'BOARD_ANNOUNCEMENTS_SETTINGS'	=> 'Impostazione annunci',
'BOARD_ANNOUNCEMENTS_SETTINGS_EXPLAIN'	=> 'In questa pagina è possibile creare e gestire un annuncio che sarà mostrato in ogni pagina del sito.',
'BOARD_ANNOUNCEMENTS_ENABLE'	=> 'Abilita annunci',
'BOARD_ANNOUNCEMENTS_GUESTS'	=> 'Annunci visibili agli ospiti',
'BOARD_ANNOUNCEMENTS_BGCOLOR'	=> 'Colore di sfondo per gli annunci',
'BOARD_ANNOUNCEMENTS_BGCOLOR_EXPLAIN'	=> 'Qui è possibile cambiare il colore di sfondo dell\'annuncio specificandone il codice esadecimale (ad esempio FFFF80). Lasciare vuoto questo campo per usare il colore predefinito.',
'BOARD_ANNOUNCEMENTS_TEXT'	=> 'Messaggio annuncio',
'BOARD_ANNOUNCEMENTS_PREVIEW'	=> 'Anteprima annuncio',
'BOARD_ANNOUNCEMENTS_UPDATED'	=> 'L\'annuncio è stato aggiornato.',
));
