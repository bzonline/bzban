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
 * Better ban handling ACP module.
 */
class main_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	public function main($id, $mode)
	{
		global $phpbb_container, $config, $request, $template, $user;

		/** @var \phpbb\language\language $lang */
		$lang = $phpbb_container->get('language');

		// Add the board rules ACP lang file
		$lang->add_lang('bzban_acp', 'bz/bzban');

		// Requests
		$action = $request->variable('action', '');
		$language = $request->variable('language', 0);
		$parent_id = $request->variable('parent_id', 0);

		// Load the "settings" or "manage" module modes
		switch ($mode)
		{
			case 'settings':
			
				$this->tpl_name = 'acp_settings';
				$this->page_title = $user->lang('ACP_BZBAN_TITLE');
				
				add_form_key('setup/bzban');

				if ($request->is_set_post('submit'))
				{
					if (!check_form_key('setup/bzban'))
					{
						trigger_error('FORM_INVALID');
					}

					$config->set('bzban_enabled', $request->variable('bzban_enabled', 0));

					trigger_error($user->lang('ACP_BZBAN_SETTING_SAVED') . adm_back_link($this->u_action));
				}

			case 'bzm':
				$this->tpl_name = 'acp_bzm';
				$this->page_title = $user->lang('ACP_BZBAN_TITLE');
				
				add_form_key('setup/bzban');

				if ($request->is_set_post('submit'))
				{
					if (!check_form_key('setup/bzban'))
					{
						trigger_error('FORM_INVALID');
					}

					$config->set('bzban_enabled', $request->variable('bzban_enabled', 0));

					trigger_error($user->lang('ACP_BZBAN_SETTING_SAVED') . adm_back_link($this->u_action));
				}

				$template->assign_vars(array(
					'U_ACTION'				=> $this->u_action,
					'S_BZBAN_ENABLED'		=> $config['bzban_enabled'],
				));
			break;

			case 'manage':
				// Load a template from adm/style for our ACP page
//				$this->tpl_name = 'bzban_manage';

				// Set the page title for our ACP page
//				$this->page_title = $lang->lang('ACP_BZBAN_MANAGE');

				// Perform any actions submitted by the user
				switch ($action)
				{
					case 'add':
						// Set the page title for our ACP page
//						$this->page_title = $lang->lang('ACP_BOARDRULES_CREATE_RULE');

						// Load the add rule handle in the admin controller
//						$admin_controller->add_rule($language, $parent_id);

						// Return to stop execution of this script
						return;
					break;

					case 'delete':
						// Delete a rule
//						$admin_controller->delete_rule($rule_id);
					break;
				}
			break;
		}
	}
}
