<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license Proprietary
*
*/

namespace forumhulp\statistics\acp;

class statistics_module
{
	public $u_action;

	function main($id, $mode)
	{
		global $db, $config, $sconfig, $phpbb_root_path, $user, $template, $request, $phpbb_extension_manager, $phpbb_container, $phpbb_path_helper, $tables;
		$user->add_lang_ext('forumhulp/statistics', 'statistics');

	function __construct(\phpbb\user $user, \phpbb\extension\manager $extension_manager, $phpbb_root_path)
	{
		$this->extension_manager	= $extension_manager;
		$this->phpbb_root_path		= $phpbb_root_path;
	}
		$tables['config']	= $phpbb_container->getParameter('tables.config_table');
		$tables['online'] 	= $phpbb_container->getParameter('tables.online_table');
		$tables['domain'] 	= $phpbb_container->getParameter('tables.domain_table');
		$tables['se']	  	= $phpbb_container->getParameter('tables.se_table');
		$tables['archive']	= $phpbb_container->getParameter('tables.archive_table');
		$tables['stats']	= $phpbb_container->getParameter('tables.stats_table');
		include($phpbb_root_path . 'ext/forumhulp/statistics/vendor/stat_functions.php');

		\stat_functions::get_config();

		$action		= $request->variable('action', '');
		$screen		= $request->variable('screen', $sconfig['statistics_start_screen']);
		$start		= $request->variable('start', 0);
		$overall	= (int) $request->variable('overall', $config['statistics_archive']);

		if ($overall != (int) $config['statistics_archive'])
		{
			$config->set('statistics_archive', (int) $overall);
		}

		$this->tpl_name = 'acp_statistics';
		$this->page_title = 'ACP_STATISTICS';
		$template->assign_vars(array('EXT_PATH' => $phpbb_path_helper->update_web_root_path($phpbb_extension_manager->get_extension_path('forumhulp/statistics', true)),
									'U_ACTION'	=> $this->u_action,
									'ACT'		=> $screen));
		switch ($screen)
		{
			case 'info':
				$user->add_lang_ext('forumhulp/statistics', 'info_acp_statistics');
				$phpbb_container->get('forumhulp.helper')->detail('forumhulp/statistics');
				$this->tpl_name = 'acp_ext_details';
				break;

			case 'nyi':
				\stat_functions::nyi($start, $this->u_action, $overall);
			break;

			case 'countries':
				\stat_functions::countries($start, $this->u_action, $overall);
			break;

			case 'browsers':
				\stat_functions::browsers($start, $this->u_action, $overall);
			break;

			case 'os':
				\stat_functions::os($start, $this->u_action, $overall);
			break;

			case 'referrals':
				\stat_functions::referrals($start, $this->u_action, $overall);
			break;

			case 'se':
				\stat_functions::se($start, $this->u_action, $overall);
			break;

			case 'ese':
				$this->tpl_name = 'subdisplays/ese';
				\stat_functions::ese($this->u_action, $action);
			break;

			case 'se_terms':
				\stat_functions::se_terms($start, $this->u_action, $overall);
			break;

			case 'crawl':
				\stat_functions::crawl($start, $this->u_action, $overall);
			break;

			case 'modules':
				\stat_functions::modules($start, $this->u_action, $overall);
			break;

			case 'screens':
				\stat_functions::screens($start, $this->u_action, $overall);
			break;

			case 'stats':
				\stat_functions::stats($start, $this->u_action);
			break;

			case 'ustats':
				\stat_functions::ustats($start, $this->u_action);
			break;

			case 'users':
				\stat_functions::users($start, $this->u_action, $overall);
			break;

			case 'config':
				\stat_functions::config($start, $this->u_action);
			break;

			case 'top10':
				\stat_functions::top10($start, $this->u_action);
			break;

			default:
				\stat_functions::online($start, $this->u_action);
			break;
		}
	}
}
