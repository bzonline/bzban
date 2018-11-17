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
	'ACP_BZBAN_TITLE'			=> 'МОДУЛЬ BZBAN',
	'ACP_BZBAN_SETTING'			=> 'Настройки',
	'ACP_BZBAN_BZM'				=> 'Форма BZM',
));
