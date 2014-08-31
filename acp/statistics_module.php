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
		$user->add_lang_ext('forumhulp/statistics', 'statistics');

		$tables['config']	= $phpbb_container->getParameter('tables.config_table');
		$tables['online'] 	= $phpbb_container->getParameter('tables.online_table');
		$tables['domain'] 	= $phpbb_container->getParameter('tables.domain_table');
		$tables['se']	  	= $phpbb_container->getParameter('tables.se_table');
		$tables['archive']	= $phpbb_container->getParameter('tables.archive_table');
		$tables['stats']	= $phpbb_container->getParameter('tables.stats_table');

		stat_functions::get_config();

		$action		= $request->variable('action', '');
		$screen		= $request->variable('screen', $config['statistics_start_screen']);
		$start		= $request->variable('start', 0);
		$overall	= $request->variable('overall', $config['statistics_archive']);

		$this->tpl_name = 'acp_statistics';
		$this->page_title = 'ACP_STATISTICS';
		$template->assign_vars(array('EXT_PATH' => $phpbb_path_helper->update_web_root_path($phpbb_extension_manager->get_extension_path('forumhulp/statistics', true)),
									'U_ACTION'	=> $this->u_action,
									'ACT'		=> $screen));
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
				stat_functions::nyi($start, $this->u_action, $overall);
			break;

			case 'countries':
				stat_functions::countries($start, $this->u_action, $overall);
			break;

			case 'browsers':
				stat_functions::browsers($start, $this->u_action, $overall);
			break;

			case 'os':
				stat_functions::os($start, $this->u_action, $overall);
			break;

			case 'referrals':
				stat_functions::referrals($start, $this->u_action, $overall);
			break;

			case 'se':
				stat_functions::se($start, $this->u_action, $overall);
			break;

			case 'ese':
				$this->tpl_name = 'subdisplays/ese';
				stat_functions::ese($this->u_action, $action);
			break;

			case 'se_terms':
				stat_functions::se_terms($start, $this->u_action, $overall);
			break;

			case 'crawl':
				stat_functions::crawl($start, $this->u_action, $overall);
			break;

			case 'modules':
				stat_functions::modules($start, $this->u_action, $overall);
			break;

			case 'screens':
				stat_functions::screens($start, $this->u_action, $overall);
			break;

			case 'stats':
				stat_functions::stats($start, $this->u_action);
			break;

			case 'ustats':
				stat_functions::ustats($start, $this->u_action);
			break;

			case 'users':
				stat_functions::users($start, $this->u_action, $overall);
			break;

			case 'config':
				stat_functions::config($start, $this->u_action);
			break;

			case 'top10':
				stat_functions::top10($start, $this->u_action);
			break;

			default:
				stat_functions::online($start, $this->u_action);
			break;
		}
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
