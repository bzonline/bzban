<?php
/**
 *
 * Better ban handling. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Boris Zverev, https://privet.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace bz\bzban\acp;

/**
 * Better ban handling ACP module info.
 */
class main_info
{
	public function module()
	{
		return array(
			'filename'	=> '\bz\bzban\acp\main_module',
			'title'		=> 'ACP_BZBAN_TITLE',
			'modes'		=> array(
				'settings'	=> array('title'	=> 'ACP_BZBAN_SETTING','auth'	=> 'ext_bz/bzban && acl_a_board','cat'	=> array('ACP_BZBAN_TITLE')),
				'bzm'		=> array('title'	=> 'ACP_BZBAN_BZM','auth'		=> 'ext_bz/bzban && acl_a_board','cat'	=> array('ACP_BZBAN_TITLE')),
			),
		);
	}
}
