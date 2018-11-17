<?php
/**
 *
 * Better ban handling. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Boris Zverev, https://privet.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_BZBAN_ENABLE'			=> 'Enable Better Ban Handling?',
	'ACP_BZBAN_SETTING_SAVED'	=> 'Settings have been saved successfully!',

//	'ACP_BZBAN_TITLE'			=> 'Ban Module',
	'ACP_BZBAN_SELECT_LANGUAGE'		=> 'Select language',
));
