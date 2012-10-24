<?php
/**
*
* @package pun
* @copyright (c) 2012 David King (imkingdavid)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
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

$lang = array_merge($lang, [
	'VIEWING_MY_NOTES'	=> 'My notes',

	'NOTE_NOT_FOUND_ERROR'	=> 'The requested note does not exist.',

	'RETURN_TO_NOTE'		=> [
		1	=> 'View your note',
		2	=> 'View your notes',
	],

	'UPDATING_NOTE'			=> 'Updating note',
	'CREATING_NOTE'			=> 'Creating note',
	'NOTE_CREATED'			=> 'Your note has been created.',
	'NOTE_UPDATED'			=> 'Your note has been updated.',
]);
