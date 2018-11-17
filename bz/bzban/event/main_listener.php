<?php
/**
 *
 * Better ban handling. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Boris Zverev, https://privet.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace bz\bzban\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Better ban handling Event listener.
 */
class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'							=> 'user_setup',
			'core.viewtopic_modify_post_row'			=> 'viewtopic_modify_post_row',
			'core.memberlist_view_profile'				=> 'memberlist_view_profile',
			'core.search_modify_tpl_ary'				=> 'search_modify_tpl_ary',
		);
	}

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	/* @var string phpEx */
	protected $php_ext;

	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\config\db_text */
	protected $db;
	
	/* @var \phpbb\auth\auth */
	protected $auth;
	
	protected $allowed;
	protected $may_ban;
	
	/* @var string phpBB root path */
	protected $phpbb_root_path; // Needed? Only in functions.
	
	/**
	 * Constructor
	 *
	 * @param \phpbb\controller\helper			$helper		Controller helper object
	 * @param \phpbb\template\template			$template	Template object
	 * @param \phpbb\user              			$user       User object
	 * @param string                   			$php_ext    phpEx
	 * @param \phpbb\config\config				$config
	 * @param \phpbb\db\driver\driver_interface	$db
	 * @param \phpbb\auth\auth					$auth
	 * @param string							$phpbb_root_path
	 */
	public function __construct(
		\phpbb\controller\helper $helper,
		\phpbb\template\template $template,
		\phpbb\user $user,
		$php_ext,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\auth\auth $auth,
		$phpbb_root_path
		)
	{
		$this->helper          = $helper;
		$this->template        = $template;
		$this->user            = $user;
		$this->php_ext         = $php_ext;
		$this->config          = $config;
		$this->db              = $db;
		$this->auth            = $auth;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->allowed         = false;
		$this->may_ban         = false;
	}

	/**
	 * Load common language files during user setup
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function user_setup($event)
	{
		$this->allowed = !$this->user->data['is_bot'] &&
						$this->user->data['is_registered'] &&
						$this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel') &&
						$this->config['bzban_enabled'];
			
		if($this->allowed)
		{
			// Show hidden fields to moderators/admins
			$this->may_ban = $this->auth->acl_gets('a_', 'm_') || $this->auth->acl_getf_global('m_');

		}
		
		// what page are we on?
		$page_name = substr($this->user->page['page_name'], 0, strpos($this->user->page['page_name'], '.'));


		// We only care about memberlist and viewtopic
		if (in_array($page_name, array('viewtopic', 'memberlist', 'search', 'app')))
		{
			$lang_set_ext = $event['lang_set_ext'];
			$lang_set_ext[] = array(
				'ext_name' => 'bz/bzban',
				'lang_set' => 'bzban',
			);
			$event['lang_set_ext'] = $lang_set_ext;

			$this->template->assign_vars(array(
				'S_BZBAN_ENABLED' => true,
				'S_BZBAN_MAY_BAN' => $this->may_ban,
			));
		}
	}

	/**
	 * Add a link to the controller in the forum navbar
	 
	 */
	public function viewtopic_modify_post_row($event)
	{
		if($this->allowed)
		{
			$poster_id = $event['post_row']['POSTER_ID'];
			$post_id   = $event['post_row']['POST_ID'];
			$banned    = 0;
			$excluded  = 0;
			$myself    = 0;
			if($this->user->data['user_id']!=$poster_id)
				$myself = 1;

			$sql = 'SELECT * FROM ' . BANLIST_TABLE . ' WHERE ban_userid=' . $poster_id . ' ORDER BY ban_start DESC LIMIT 1';
			if(($res = $this->db->sql_query($sql, 7200)))
			{
				$row = $this->db->sql_fetchrow($res);
				$this->db->sql_freeresult($res);
				
				if($row)
					$ban_end = $row['ban_end'];
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
			
			$event['post_row'] = array_merge($event['post_row'],array(
				'S_BZBAN_BANNED'		=> $banned,
				'S_BZBAN_EXCLUDED'		=> $excluded,
				'S_BZBAN_MYSELF'		=> $myself,
				'U_BZBAN_PAGE_URL'		=> append_sid("{$this->phpbb_root_path}memberlist.$this->php_ext", "mode=viewprofile&amp;u=$poster_id"),
				'BZBAN_TITLE'			=> $this->user->lang('BZBAN_REASON') . ': ' . $row['ban_give_reason'] . ' [' . $ban_until . ']',

				// Button
				'U_BZBAN'	=> $this->helper->route('bz_bzban_controller', array('user_id' => $poster_id, 'post_id' => $post_id)),
			));
			
			$this->template->assign_vars(array(
				'L_BZBAN_USER_BANNED'	=> $this->user->lang('BZBAN_USER_BANNED'),
				'L_BUTTON_BZBAN'		=> $this->user->lang('BUTTON_BZBAN'),
				'L_BZBAN_USER'			=> $this->user->lang('BZBAN_USER'),
			));
		}
	}

	/**
	* Display zodiac on viewing user profile
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function memberlist_view_profile($event)
	{
		if($this->allowed)
		{
			$user_id = $event['member']['user_id'];
			$banned   = 0;
			$excluded = 0;
			
			$sql = 'SELECT * FROM ' . BANLIST_TABLE . ' WHERE ban_userid=' . $user_id . ' ORDER BY ban_start DESC LIMIT 1';
			if(($res = $this->db->sql_query($sql, 7200)))
			{
				$row = $this->db->sql_fetchrow($res);
				$this->db->sql_freeresult($res);
				
				if($row)
				{
					$ban_end = $row['ban_end'];
					$ban_start = $row['ban_start'];
					
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
				else
				{
					$ban_end = -1;
					$ban_start = -1;
				}
				
			}
			
			$this->template->assign_vars(array(
				'S_BZBAN_EXCLUDED'			=> $excluded,
				'S_BZBAN_BANNED'			=> $banned,
				'L_BZBAN_BAN_START'			=> $this->user->format_date($ban_start),
				'L_BZBAN_BAN_END'			=> $ban_until,
				'L_BZBAN_REASON_MOD_MSG'	=> $row['ban_reason'],
				'L_BZBAN_REASON_USR_MSG'	=> $row['ban_give_reason'],
			));
		}
	}
	
	/**
	* Display zodiac on search
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function search_modify_tpl_ary($event)
	{
		if($this->allowed && ($event['show_results'] != 'topics'))
		{
			$banned    = 0;
			$excluded  = 0;
			$poster_id = $event['row']['poster_id'];
			$post_id   = $event['row']['post_id'];
				
			$sql = 'SELECT * FROM ' . BANLIST_TABLE . ' WHERE ban_userid=' . $poster_id . ' ORDER BY ban_start DESC LIMIT 1';
			if(($res = $this->db->sql_query($sql, 7200)))
			{
				$row = $this->db->sql_fetchrow($res);
				$this->db->sql_freeresult($res);
				
				if($row)
					$ban_end = $row['ban_end'];
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
			
			$event['tpl_ary'] = array_merge($event['tpl_ary'],array(
				'S_BZBAN_BANNED'	=> $banned,
				'S_BZBAN_EXCLUDED'			=> $excluded,
				'BZBAN_TITLE'		=> $this->user->lang('BZBAN_REASON') . ': ' . $row['ban_give_reason'] . ' [' . $ban_until . ']',

				// Button
				'U_BZBAN'	=> $this->helper->route('bz_bzban_controller', array('user_id' => $poster_id, 'post_id' => $post_id)),
			));
			
			$this->template->assign_vars(array(
				'L_BZBAN_USER_BANNED'	=> $this->user->lang('BZBAN_USER_BANNED'),
			));
		}
	}
}

