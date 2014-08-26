<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\statistics\acp;

include('includes/stat_functions.php');
use \stat_functions;

class statistics_module
{
	public $u_action;

	function main($id, $mode)
	{
		global $db, $config, $phpbb_root_path, $user, $template, $request, $phpbb_extension_manager, $phpbb_container, $phpbb_path_helper, $tables;
;
		$user->add_lang_ext('forumhulp/statistics', 'statistics');
		
		$tables['config']	= $phpbb_container->getParameter('tables.config_table');
		$tables['online'] 	= $phpbb_container->getParameter('tables.online_table');
		$tables['domain'] 	= $phpbb_container->getParameter('tables.domain_table');
		$tables['se']	  	= $phpbb_container->getParameter('tables.se_table');
		$tables['archive']	= $phpbb_container->getParameter('tables.archive_table');
		$tables['stats']	= $phpbb_container->getParameter('tables.stats_table');
		
		stat_functions::get_config();

		$this->tpl_name = 'acp_statistics';
		$template->assign_vars(array('EXT_PATH' => $phpbb_path_helper->update_web_root_path($phpbb_extension_manager->get_extension_path('forumhulp/statistics', true)),
									 'U_ACTION'	=> $this->u_action));

		$action		= $request->variable('action', '');
		$screen		= $request->variable('screen', '');
		$start		= $request->variable('start', 0);
		$overall	= $request->variable('overall', 0);
		
		switch ($screen)
		{
			case 'info':
				$user->add_lang(array('install', 'acp/extensions', 'migrator'));
				$ext_name = 'forumhulp/statistics';
				$md_manager = new \phpbb\extension\metadata_manager($ext_name, $config, $phpbb_extension_manager, $template, $user, $phpbb_root_path);
				try
				{
					$this->metadata = $md_manager->get_metadata('all');
				}
				catch(\phpbb\extension\exception $e)
				{
					trigger_error($e, E_USER_WARNING);
				}

				$md_manager->output_template_data();

				try
				{
					$updates_available = $this->version_check($md_manager, $request->variable('versioncheck_force', false));

					$template->assign_vars(array(
						'S_UP_TO_DATE'		=> empty($updates_available),
						'S_VERSIONCHECK'	=> true,
						'UP_TO_DATE_MSG'	=> $user->lang(empty($updates_available) ? 'UP_TO_DATE' : 'NOT_UP_TO_DATE', $md_manager->get_metadata('display-name')),
					));

					foreach ($updates_available as $branch => $version_data)
					{
						$template->assign_block_vars('updates_available', $version_data);
					}
				}
				catch (\RuntimeException $e)
				{
					$template->assign_vars(array(
						'S_VERSIONCHECK_STATUS'			=> $e->getCode(),
						'VERSIONCHECK_FAIL_REASON'		=> ($e->getMessage() !== $user->lang('VERSIONCHECK_FAIL')) ? $e->getMessage() : '',
					));
				}

				$template->assign_vars(array(
					'U_BACK'	=> $this->u_action,
				));

				$this->tpl_name = 'acp_ext_details';
				break;

			case 'nyi':
				$template->assign_vars(array('ACT' => 'nyi'));
				$this->page_title = 'ACP_STATISTICS';
				stat_functions::nyi($start, $this->u_action, $overall);
			break;

			case 'countries':
				$template->assign_vars(array('ACT' => 'countries'));
				$this->page_title = 'ACP_STATISTICS';
				stat_functions::countries($start, $this->u_action, $overall);
			break;

			case 'browsers':
				$template->assign_vars(array('ACT' => 'browsers'));
				$this->page_title = 'ACP_STATISTICS';
				stat_functions::browsers($start, $this->u_action, $overall);
			break;

			case 'os':
				$template->assign_vars(array('ACT' => 'os'));
				$this->page_title = 'ACP_STATISTICS';
				stat_functions::os($start, $this->u_action, $overall);
			break;

			case 'referrals':
				$template->assign_vars(array('ACT' => 'referrals'));
				$this->page_title = 'ACP_STATISTICS';
				stat_functions::referrals($start, $this->u_action, $overall);
			break;

			case 'se':
				$template->assign_vars(array('ACT' => 'se'));
				$this->page_title = 'ACP_STATISTICS';
				stat_functions::se($start, $this->u_action, $overall);
			break;

			case 'se_terms':
				$template->assign_vars(array('ACT' => 'se_terms'));
				$this->page_title = 'ACP_STATISTICS';
				stat_functions::se_terms($start, $this->u_action, $overall);
			break;

			case 'crawl':
				$template->assign_vars(array('ACT' => 'crawl'));
				$this->page_title = 'ACP_STATISTICS';
				stat_functions::crawl($start, $this->u_action, $overall);
			break;

			case 'modules':
				$template->assign_vars(array('ACT' => 'modules'));
				$this->page_title = 'ACP_STATISTICS';
				stat_functions::modules($start, $this->u_action, $overall);
			break;

			case 'screens':
				$template->assign_vars(array('ACT' => 'screens'));
				$this->page_title = 'ACP_STATISTICS';
				stat_functions::screens($start, $this->u_action, $overall);
			break;

			case 'stats':
				$template->assign_vars(array('ACT' => 'stats'));
				$this->page_title = 'ACP_STATISTICS';
				stat_functions::stats($start, $this->u_action);
			break;

			case 'ustats':
				$template->assign_vars(array('ACT' => 'ustats'));
				$this->page_title = 'ACP_STATISTICS';
				stat_functions::ustats($start, $this->u_action);
			break;

			case 'users':
				$template->assign_vars(array('ACT' => 'users'));
				$this->page_title = 'ACP_STATISTICS';
				stat_functions::users($start, $this->u_action, $overall);
			break;

			case 'config':
				$template->assign_vars(array('ACT' => 'config'));
				$this->page_title = 'ACP_STATISTICS';
				stat_functions::CONFIG($start, $this->u_action);
			break;

			default:
				$template->assign_vars(array('ACT' => 'default'));
				$this->page_title = 'ACP_STATISTICS';
				
				stat_functions::online($start, $this->u_action);
				
			break;
		}
	}





	protected function ref_manage($id, $mode)
	{
		global $db, $user, $auth, $template, $cache, $request, $phpbb_container;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

		// define vars here
		$action		= $request->variable('action', '');
		$ref_id		= $request->variable('id', 0);
		$start		= $request->variable('start', 0);
		$deletemark = $request->variable('delmarked', false, false, \phpbb\request\request_interface::POST);
		$deleteall	= $request->variable('delall', false, false, \phpbb\request\request_interface::POST);
		$mark		= $request->variable('mark', array(0));

		// sort keys
		$sort_key	= $request->variable('sk', 'l');
		$sort_dir	= $request->variable('sd', 'd');

		// form name
		$form_name	= 'acp_referrers';
		add_form_key($form_name);

		// whois (special case)
		if ( $action == 'whois' )
		{
			include $phpbb_root_path . 'includes/functions_user.' . $phpEx;

			$this->page_title	= 'WHOIS';
			$user->add_lang('acp/users');
			$this->tpl_name		= 'simple_body';

			$ref_ip				= $request->variable('ref_ip', '');
			$domain				= gethostbyaddr($ref_ip);
			$ipwhois			= user_ipwhois($ref_ip);

			$template->assign_vars(array(
				'MESSAGE_TITLE' => $user->lang('IP_WHOIS_FOR', $domain),
				'MESSAGE_TEXT' => nl2br($ipwhois),
			));

			return;
		}

		if ( $deletemark || $deleteall )
		{
			if ( confirm_box(true) )
			{
				$sql_where = '';

				if ( $deletemark && sizeof($mark) )
				{
					$sql_in = array();
					foreach ( $mark as $marked )
					{
						$sql_in[] = $marked;
					}
					$sql_where = ' WHERE ' . $db->sql_in_set('ref_id', $sql_in);
					unset($sql_in, $marked);

					// get hosts for logs
					$sql = 'SELECT ref_host FROM ' . $this->referrerss_table . $sql_where;
					$result = $db->sql_query($sql);

					$host_list = array();
					while ( $row = $db->sql_fetchrow($result) )
					{
						$host_list[] = $row['ref_host'];
					}
					$db->sql_freeresult($result);
				}

				if ( $sql_where )
				{
					$sql = 'DELETE FROM ' . $this->referrerss_table . $sql_where;
					$db->sql_query($sql);

					add_log('admin', 'LOG_REFERRER_REMOVED', implode(', ', array_unique($host_list)), (int) $this->db->sql_affectedrows());
				}
				else if ( $deleteall )
				{
					// clear table
					$db->sql_query('TRUNCATE TABLE ' . $this->referrerss_table);
					add_log('admin', 'LOG_REFERRER_REMOVED_ALL');
				}
			} else
			{
				confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
					'i'			=> $id,
					'start'		=> $start,
					'delmarked'	=> $deletemark,
					'delall'	=> $deleteall,
					'mark'		=> $mark,
					'sk'		=> $sort_key,
					'sd'		=> $sort_dir,
					'mode'		=> $mode,
					'id'		=> $ref_id,
					'action'	=> $action,
				)));
			}
		}

		// sorting
		$sort_by_sql = array('h' => 'ref_host', 'v' => 'ref_hits', 'f' => 'ref_first', 'l' => 'ref_last');

		// define sort sql for use in displaying referrers
		$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

		$sql = 'SELECT * FROM ' . $this->referrerss_table . ' ORDER BY ' . $sql_sort;
		$result = $db->sql_query_limit($sql, $config['topics_per_page'], $start);

		while ( $row = $db->sql_fetchrow($result) )
		{
			$template->assign_block_vars('row', array(
				'REF_ID'		=> (int) $row['ref_id'],
				'REF_HOST'		=> $row['ref_host'],
				'REF_URL'		=> $row['ref_url'],
				'REF_IP'		=> $row['ref_ip'],
				'REF_HITS'		=> (int) $row['ref_hits'],
				'REF_FIRST'		=> $user->format_date($row['ref_first']),
				'REF_LAST'		=> $user->format_date($row['ref_last']),

				'U_WHOIS'		=> $this->u_action . '&amp;action=whois&amp;ref_ip=' . $row['ref_ip'],
				'U_DELETE'		=> $this->u_action . '&amp;action=delete&amp;id=' . $row['ref_id'],
			));	
		}
		$db->sql_freeresult($result);

		// used for pagination
		$sql = 'SELECT COUNT(ref_id) AS total_entries FROM ' . $this->referrerss_table . 
				' ORDER BY ' . $sql_sort;
		$result = $db->sql_query($sql);
		$count = (int) $db->sql_fetchfield('total_entries');
		$db->sql_freeresult($result);

		$pagination = $phpbb_container->get('pagination');
		$base_url = $this->u_action . '&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir;
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $count, $config['topics_per_page'], $start);

		// send to template
		$template->assign_vars(array(
			'U_ACTION'			=> $this->u_action,
			'S_SORT_KEY'		=> $sort_key,
			'S_SORT_DIR'		=> $sort_dir,

			'S_REFDEL'			=> $auth->acl_get('a_clearlogs'),
		));
	}



	/**
	* Check the version and return the available updates.
	*
	* @param \phpbb\extension\metadata_manager $md_manager The metadata manager for the version to check.
	* @param bool $force_update Ignores cached data. Defaults to false.
	* @param bool $force_cache Force the use of the cache. Override $force_update.
	* @return string
	* @throws RuntimeException
	*/
	protected function version_check(\phpbb\extension\metadata_manager $md_manager, $force_update = false, $force_cache = false)
	{
		global $cache, $config, $user;
		$meta = $md_manager->get_metadata('all');

		if (!isset($meta['extra']['version-check']))
		{
			throw new \RuntimeException($this->user->lang('NO_VERSIONCHECK'), 1);
		}

		$version_check = $meta['extra']['version-check'];

		$version_helper = new \phpbb\version_helper($cache, $config, $user);
		$version_helper->set_current_version($meta['version']);
		$version_helper->set_file_location($version_check['host'], $version_check['directory'], $version_check['filename']);
		$version_helper->force_stability($config['extension_force_unstable'] ? 'unstable' : null);

		return $updates = $version_helper->get_suggested_updates($force_update, $force_cache);
	}
}