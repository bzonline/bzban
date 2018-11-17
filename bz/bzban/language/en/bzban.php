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
	'BZBAN_BAN_TITLE'		=> 'Ban This User',
	'BZBAN_BAN_EXPLAIN'		=> 'Ban this particular user or choose any other',
	'BZBAN_USER_BANNED'		=> 'User banned',
	'BZBAN_BAN'				=> 'Ban',
	'BZBAN_UNBAN'			=> 'Unban',
	'BZBAN_EXCLUDE'			=> 'Never Ban',
	'BZBAN_UNEXCLUDE'		=> 'Allow Baning',
	'BZBAN_USERNAME'		=> 'User name',
	'BZBAN_USER_POST_URL'	=> 'User\'s post URL',
	'BZBAN_BANNED'			=> 'Banned ',
	'BZBAN_UNTIL'			=> 'until ',
	'BZBAN_WAS_BANNED'		=> 'Was banned',
	'BZBAN_REASON'			=> 'Reason',
	'BZBAN_REASON_MOD'		=> 'Reason shown to moderators',
	'BZBAN_REASON_USR'		=> 'Reason shown to users',
	'BZBAN_FOREVER'			=> 'Forever',
	
	'BUTTON_BZBAN'			=> 'Ban',
	'BZBAN_USER'			=> 'Ban user',

	'BZBAN_BUNUNBANEX'		=> 'Choose Ban, Unban or Never Ban',
	
	'BAN_LENGTH'			=> 'Length of ban',
	'BANNED_UNTIL_DATE'		=> 'until %s', // Example: "until Mon 13.Jul.2009, 14:44"
	'BANNED_UNTIL_DURATION'	=> '%1$s (until %2$s)', // Example: "7 days (until Tue 14.Jul.2009, 14:44)"
	'YEAR_MONTH_DAY'		=> '(YYYY-MM-DD)',
));
