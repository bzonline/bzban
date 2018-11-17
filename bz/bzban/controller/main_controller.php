<?php
/**
 *
 * Better ban handling. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Boris Zverev, https://privet.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace bz\bzban\controller;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Better ban handling main controller.
 */
class main_controller // implements main_interface
{
	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	protected $phpEx;
	
	/* @var \phpbb\config\db_text */
	protected $db;
	
	/* @var \phpbb\auth\auth */
	protected $auth;
	
	/* @var string phpBB root path */
	protected $phpbb_root_path; // Needed? Only in functions.
	
	/* @var \phpbb\request\request */
	protected $request;

	/** @var ContainerInterface */
//	protected $container;
	
	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config				$config
	 * @param \phpbb\controller\helper			$helper
	 * @param \phpbb\template\template			$template
	 * @param \phpbb\user						$user
	 * @param string							$php_ext
	 * @param \phpbb\db\driver\driver_interface	$db
	 * @param \phpbb\auth\auth					$auth
	 * @param string							$phpbb_root_path
	 * @param \phpbb\request\request 			$request
	 * @param ContainerInterface				$phpbb_container
	 */
	public function __construct(
		\phpbb\config\config $config, 
		\phpbb\controller\helper $helper, 
		\phpbb\template\template $template, 
		\phpbb\user $user,
		$php_ext, 
		\phpbb\db\driver\driver_interface $db, 
		\phpbb\auth\auth $auth, $phpbb_root_path,
		\phpbb\request\request $request
//		,ContainerInterface $phpbb_container
		)
	{
		$this->config          = $config;
		$this->helper          = $helper;
		$this->template        = $template;
		$this->user            = $user;
		$this->phpEx           = $php_ext;
		$this->db              = $db;
		$this->auth            = $auth;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->request         = $request;
//		$this->container       = $phpbb_container;
	}

	/**
	 * Ban controller for route /bzban/{name}
	 *
	 * @param string $name
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object 
	 */
	public function handle($user_id)
	{
		$post_id = $this->request->variable('post_id', '');
		if(!$this->user->data['is_bot'] &&
		    $this->user->data['is_registered'] &&
		    $this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel') &&
		    $this->config['bzban_enabled'])
		{
			$banned   = 0;
			$excluded = 0;
			$ban_id   = 0;
			$sql = 'SELECT username FROM ' . USERS_TABLE . ' WHERE user_id=' . $user_id;
			if($res = $this->db->sql_query($sql))
			{
				$row = $this->db->sql_fetchrow($res);
				$this->db->sql_freeresult($res);
				if($row)
					$name = $row['username'];
				else
					$name = '';
			}
			$sql = 'SELECT * FROM ' . BANLIST_TABLE . ' WHERE ban_userid=' . $user_id . ' ORDER BY ban_start DESC LIMIT 1';
			if(($res = $this->db->sql_query($sql)))
			{
				$row = $this->db->sql_fetchrow($res);			
				$this->db->sql_freeresult($res);
				
				if($row)
				{
					$ban_end = $row['ban_end'];
					$ban_id  = $row['ban_id'];
				}
				else
					$ban_end = -1;
				
				if(($ban_end == 0) || ($ban_end > time()))
				{
					if($row['ban_exclude'] != 0)
						$excluded = 1;
					else
						$banned = 1;
					
					if($ban_end == 0)
					{
						$ban_until = $this->user->lang('BZBAN_FOREVER');
					}
					else
					{
						$ban_until = $this->user->lang('BZBAN_UNTIL') . $this->user->format_date($ban_end);
					}
				}
			}
			$post_url = $this->config['server_protocol'].$this->config['server_name']."/viewtopic.".$this->phpEx."?p=".$post_id."#".$post_id;
			$this->template->assign_vars(array(
				'S_BZBAN_BANNED'			=> $banned,
				'S_BZBAN_EXCLUDED'			=> $excluded,
				'L_TITLE'					=> $this->user->lang('BZBAN_BAN_TITLE'),
				'L_EXPLAIN'					=> $this->user->lang('BZBAN_BAN_EXPLAIN'),
				'L_BZBAN_POST_URL'			=> $post_url,
				'L_BZBAN_BAN_START'			=> $this->user->format_date($row['ban_start']),
				'L_BZBAN_BAN_END'			=> $ban_until,
				'L_BZBAN_REASON_MOD_MSG'	=> $banned ? $row['ban_reason'] : $post_url,
				'L_BZBAN_REASON_USR_MSG'	=> $banned ? $row['ban_give_reason'] : $post_url,
				'L_BZBAN_NAME'				=> $name,
				'S_HIDDEN_FIELDS'			=> '<input type="hidden" name="unban[]" value="'.$ban_id.'" />',
				'U_BZBAN_VALUE'				=> $ban_id ? 'value="'.$ban_id.'"' : '',
				'U_ACTION'					=> append_sid($this->phpbb_root_path."mcp.$this->phpEx", 'i=mcp_ban&amp;mode=user&amp;u='.$user_id),
				'U_FIND_USERNAME'			=> append_sid($this->phpbb_root_path."memberlist.$this->phpEx", 'mode=searchuser&amp;form=bzban_ban&amp;field=ban'),
				));
		}
		return $this->helper->render('bzban_body.html', $user_id);
	}
}
