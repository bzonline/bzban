<?php
/**
 *
 * Better ban handling. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Boris Zverev, https://privet.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace bz\bzban\migrations;

class install_acp_module extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['bzban_enabled']);
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v314');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('bzban_enabled', 0)),

			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_BZBAN_TITLE'
			)),
			array('module.add', array(
				'acp',
				'ACP_BZBAN_TITLE',
				array(
					'module_basename'	=> '\bz\bzban\acp\main_module',
					'modes'				=> array('settings', 'bzm'),
				),
			)),
		);
	}
}
