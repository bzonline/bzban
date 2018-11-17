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
	'BZBAN_BAN_TITLE'		=> 'Бан Участника',
	'BZBAN_BAN_EXPLAIN'		=> 'Вы можете забанить этого конкретного участника или выбрать любого другого',
	'BZBAN_USER_BANNED'		=> 'Участник забанен',
	'BZBAN_BAN'				=> 'Забанить',
	'BZBAN_UNBAN'			=> 'Разбанить',
	'BZBAN_EXCLUDE'			=> 'Никогда Не Банить',
	'BZBAN_UNEXCLUDE'		=> 'Можно Банить',
	'BZBAN_USERNAME'		=> 'Ник участника',
	'BZBAN_USER_POST_URL'	=> 'URL сообщения участника',
	'BZBAN_BANNED'			=> 'Забанен ',
	'BZBAN_UNTIL'			=> 'до ',
	'BZBAN_WAS_BANNED'		=> 'Был забанен',
	'BZBAN_REASON'			=> 'Причина',
	'BZBAN_REASON_MOD'		=> 'Причина, показываемая модераторам',
	'BZBAN_REASON_USR'		=> 'Причина, показываемая участникам',
	'BZBAN_FOREVER'			=> 'Навсегда',
	
	'BUTTON_BZBAN'			=> 'Забанить',
	'BZBAN_USER'			=> 'Забанить участника',

	'BZBAN_BUNUNBANEX'		=> 'Выберите Забанить, Разбанить или Никогда не банить',
	
	'BAN_LENGTH'			=> 'Продолжительность блокировки',
	'BANNED_UNTIL_DATE'		=> 'до %s', // Example: "until Mon 13.Jul.2009, 14:44"
	'BANNED_UNTIL_DURATION'	=> '%1$s (до %2$s)', // Example: "7 days (until Tue 14.Jul.2009, 14:44)"
	'YEAR_MONTH_DAY'		=> '(ГГГГ-ММ-ДД)',
));
