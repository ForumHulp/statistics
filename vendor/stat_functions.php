<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license Proprietary
*
*/

class stat_functions
{
	/**
	* get_config data
	*/
	public static function get_config()
	{
		global $db, $sconfig, $tables, $phpbb_container;

		$sql = 'SELECT * FROM ' . $tables['config'];
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);

		foreach ($row as $key => $value)
		{
			$sconfig['statistics_' . $key] = $value;
		}
	}

	public static function get_search_eng()
	{
		global $db, $tables, $search_eng;
		$search_eng = array();
		$sql = 'SELECT name, query FROM ' . $tables['se'] . ' ORDER BY name ASC';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$search_eng[$row['name']] = $row['query'];
		}
	}

	public static function count_array($aray, $row1)
	{
		$found = 0;
		if (is_array($aray))
		{
			foreach ($aray as $key => $value)
			{
				if ($key == $row1)
				{
					$aray[$row1] += 1;
					$found = 1;
					break;
				}
			}
		}
		if (!$found)
		{
			$aray[$row1] = 1;
		}
		return $aray;
	}

	public static function get_modules()
	{
		global $db, $config, $sconfig, $user;
		$user->add_lang(array('ucp', 'mcp', 'common'));
		$modules = $cp = array();
		$sql = 'SELECT forum_id, forum_name FROM ' . FORUMS_TABLE;
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$modules[$row['forum_id']] = $row['forum_name'];
		}
		$sql = 'SELECT module_langname FROM ' . MODULES_TABLE . ' WHERE module_class = "ucp" OR module_class = "mcp"';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			(isset($user->lang[$row['module_langname']])) ? $modules[$row['module_langname']] = $user->lang[$row['module_langname']] : null;
		}

		$module_pages = array('FORUM_INDEX' => $user->lang['FORUM_INDEX'], 'VIEWING_FAQ' => $user->lang['VIEWING_FAQ'], 'VIEWING_MCP' => $user->lang['VIEWING_MCP'],
							'SEARCHING_FORUMS' => $user->lang['SEARCHING_FORUMS'], 'VIEWING_ONLINE' => $user->lang['VIEWING_ONLINE'], 'VIEWING_MEMBERS' => $user->lang['VIEWING_MEMBERS'],
							'VIEWING_UCP' => $user->lang['VIEWING_UCP']);

		$m = unserialize($sconfig['statistics_custom_pages']);
		if (sizeof($m) > 0)
		{
			foreach($m as $value)
			{
				(isset($user->lang[$value])) ? $cp[$value] = $user->lang[$value] : null;
			}
		}
		return array_replace($module_pages, $modules, $cp);
	}

	public static function online($start = 0, $uaction = '')
	{
		global $db, $config, $sconfig, $user, $tables, $request, $template, $phpbb_container;

		$modules = self::get_modules();

		// sort keys, direction en sql
		$sort_key	= $request->variable('sk', 't');
		$sort_dir	= $request->variable('sd', 'd');
		$sort_by_sql = array('t' => 'time', 'u' => 'uname', 'm' => 'module', 'd' => 'domain', 'h' => 'host', 'g' => 'ugroup');
		$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');
		
		$template->assign_vars(array(
			'U_ACTION'			=> $uaction,
			'S_SORT_KEY'		=> $sort_key,
			'S_SORT_DIR'		=> $sort_dir,
			'VIEW_TABLE' 		=> $request->variable('table', false),
			'SUB_DISPLAY'		=> 'online'
		));

		if (!$sconfig['statistics_botsinc'])
		{
			$sql = 'SET SESSION group_concat_max_len = 15000';
			$db->sql_query($sql);
			$sql = 'SELECT GROUP_CONCAT(CONCAT("""", bot_name, """")) AS bots FROM ' . BOTS_TABLE;
			$result = $db->sql_query($sql);
			$botswhere = ' WHERE uname NOT IN (' . $db->sql_fetchfield('bots') . ')';			
			$db->sql_freeresult($result);
		}

		$sql = 'SELECT COUNT(id) AS total_entries FROM ' . $tables['online'] . ((!$sconfig['statistics_botsinc']) ? $botswhere : '');
		$result = $db->sql_query($sql);
		$total_entries = (int) $db->sql_fetchfield('total_entries');
		$db->sql_freeresult($result);

		$pagination = $phpbb_container->get('pagination');
		$base_url = $uaction . '&amp;screen=online&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir;
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $sconfig['statistics_max_online'], $start);

		$sql = 'SELECT o.time, o.uname, o.ugroup, g.group_name, g.group_type, o.agent, o.ip_addr, o.host, o.domain, d.description, o.module, o.page, o.referer FROM ' . $tables['online'] . ' o
				LEFT JOIN ' . $tables['domain'] . ' d ON (d.domain = o.domain) 
				LEFT JOIN ' . GROUPS_TABLE . ' g ON (g.group_id = o.ugroup) '
				. ((!$sconfig['statistics_botsinc']) ? $botswhere : '') . ' ORDER BY o.' . $sql_sort;

		$result = $db->sql_query_limit($sql, $sconfig['statistics_max_online'], $start);
		$counter = 0;
		$url = generate_board_url() . '/';
		while ($row = $db->sql_fetchrow($result))
		{
			$counter += 1;
			$group_name = ($row['group_type'] == GROUP_SPECIAL) ? $user->lang['G_' . $row['group_name']] : $row['group_name'];
			$template->assign_block_vars('onlinerow', array(
				'COUNTER'   => $start + $counter,
				'TIJD'		=> $user->format_date($row['time'], 'H:i'),
				'DATE'		=> $user->format_date($row['time'], 'D M d, Y'),
				'FLAG'		=> ($row['uname'] != 'Anonymous') ? 'online-user.gif' : 'offline-user.gif',
				'UNAME'		=> $row['uname'],
				'UGROUP'	=> $group_name,
				'AGENT'		=> $row['agent'],
				'MODULE'	=> isset($modules[$row['module']]) ? $modules[$row['module']] : 'Module not found',
				'MODULEURL'	=> $url . $row['page'],
				'DFLAG'		=> $row['domain'].'.png',
				'DDESC'		=> $row['description'],
				'HOST'		=> $row['host'],
				'IP'		=> $row['ip_addr']
				)
			);
		}
	}

	public static function browsers($start = 0, $uaction = '', $overall = 0)
	{
		global $db, $config, $sconfig, $user, $tables, $request, $template, $phpbb_container;

		// sort keys, direction en sql
		$sort_key	= $request->variable('sk', 'd');
		$sort_dir	= $request->variable('sd', 'd');
		$sort_by_sql = array('d' => (($overall) ? 'o.name' : 'agent'), 't' => 'total_per_domain', 'p' => 'percent');
		$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

		$template->assign_vars(array(
			'U_ACTION'			=> $uaction,
			'S_SORT_KEY'		=> $sort_key,
			'S_SORT_DIR'		=> $sort_dir,
			'SUB_DISPLAY'		=> 'graph',
			'SUBTITLE'			=> $user->lang['BROWSERS'],
		));

		$sql = ($overall) ? 'SELECT COUNT(DISTINCT name) AS total_entries, MIN(first) AS firstdate, MAX(last) AS lastdate FROM ' . $tables['archive'] . ' WHERE cat = 2' :
							'SELECT COUNT(DISTINCT agent) AS total_entries FROM ' . $tables['online'];
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$total_entries = $row['total_entries'];
		$db->sql_freeresult($result);

		$template->assign_vars(array('OVERALLTXT'	=> ($overall) ? 'Today' : 'Overall',
									'MINMAXDATE'	=> ($overall && $total_entries) ? '(' .$user->format_date($row['firstdate'], 'd m \'y') . ' - ' .
														$user->format_date($row['lastdate'], 'd m \'y') . ')': '',
									'OVERALLSORT'	=> ($overall) ? '&amp;overall=1' : ''));

		$sql = ($overall) ? 'SELECT o.name, o.hits AS total_per_domain, 
				(SELECT SUM(hits) FROM ' . $tables['archive'] . ' WHERE cat = 2) as total,
				SUM(o.hits) / (SELECT SUM(hits) FROM ' . $tables['archive'] . ' WHERE cat = 2) AS percent 
				FROM ' . $tables['archive'] . ' o
				WHERE cat = 2 GROUP BY o.name ORDER BY ' . $sql_sort :
				'SELECT DISTINCT agent FROM ' . $tables['online'];
		$result = ($overall) ? $db->sql_query_limit($sql, $sconfig['statistics_max_browsers'], $start) :  $db->sql_query($sql);
		$counter = 0;
		$graphstr = '';
		if ($overall)
		{
			while ($row = $db->sql_fetchrow($result))
			{
				$counter += 1;
				$template->assign_block_vars('onlinerow', array(
					'COUNTER'   	=> $start + $counter,
					'NAME'			=> $row['name'],
					'MODULECOUNT'	=> self::roundk($row['total_per_domain']),
					'TMODULECOUNT'	=> $row['total_per_domain'],
					'MODULETOTAL'	=> round((($row['total_per_domain'] / $row['total']) * 100), 1) . ' % (' . self::roundk($row['total_per_domain']) . ' of ' . self::roundk($row['total']) . ')',
					'TMODULETOTAL'	=> round((($row['total_per_domain'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_domain'] . ' of ' . $row['total'] . ')'
					)
				);
				$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($db->sql_escape($row['name'])) . '\', ' . $row['total_per_domain'] . ']';
			}
		} else
		{
			include('find_os.php');
			$browser = new find_os();
			
			$browser_aray = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$browser->setUserAgent($row['agent']);
				$browser_aray = ($row['agent'] != '') ? self::count_array($browser_aray, $browser->getBrowser() . ' ' . $browser->getVersion()) : null;
			}

			$total_entries = sizeof($browser_aray);

			if ($sort_key == 'd')
			{
				($sort_dir == 'd') ? krsort($browser_aray) : ksort($browser_aray);
			} else
			{
				($sort_dir == 'd') ? arsort($browser_aray) : asort($browser_aray);
			}

			$counter = 0;
			$graphstr = '';
			$row['total'] = array_sum($browser_aray);
			foreach (array_slice($browser_aray, $start, $sconfig['statistics_max_browsers'], true) as $row['Browser'] => $row['total_per_browser'])
			{
				$counter += 1;
				$template->assign_block_vars('onlinerow', array(
					'COUNTER'   	=> $start + $counter,
					'NAME'			=> $row['Browser'],
					'MODULECOUNT'	=> $row['total_per_browser'],
					'MODULETOTAL'	=> round((($row['total_per_browser'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_browser'] . ' of ' . $row['total'] . ')'
					)
				);
				$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($db->sql_escape($row['Browser'])) . '\', ' . $row['total_per_browser'] . ']';
			}
		}

		$pagination = $phpbb_container->get('pagination');
		$base_url = $uaction . '&amp;screen=browsers&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir . (($overall)? '&amp;overall=1' : '&amp;overall=0');
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $sconfig['statistics_max_browsers'], $start);

		$template->assign_vars(array('ROWSPAN'		=> $total_entries,
									'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '&amp;overall=0',$base_url) : $base_url.'&amp;overall=1',
									'GRAPH' 		=> '[' . $graphstr . ']'));
	}

	public static function os($start = 0, $uaction = '', $overall = 0)
	{
		global $db, $config, $sconfig, $user, $tables, $request, $template, $phpbb_container;

		// sort keys, direction en sql
		$sort_key	= $request->variable('sk', 'd');
		$sort_dir	= $request->variable('sd', 'd');
		$sort_by_sql = array('d' => (($overall) ? 'o.name' : 'o.agent'), 't' => 'total_per_os', 'p' => 'percent');
		$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

		$template->assign_vars(array(
			'U_ACTION'			=> $uaction,
			'S_SORT_KEY'		=> $sort_key,
			'S_SORT_DIR'		=> $sort_dir,
			'SUB_DISPLAY'		=> 'graph',
			'SUBTITLE'			=> $user->lang['SYSTEMS'],
		));

		$sql = ($overall) ? 'SELECT COUNT(DISTINCT name) AS total_entries, MIN(first) AS firstdate, MAX(last) AS lastdate FROM ' . $tables['archive'] . ' WHERE cat = 3' :
							'SELECT COUNT(DISTINCT agent) AS total_entries FROM ' . $tables['online'];
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$total_entries = $row['total_entries'];
		$db->sql_freeresult($result);

		$template->assign_vars(array('OVERALLTXT'	=> ($overall) ? 'Today' : 'Overall',
									'MINMAXDATE'	=> ($overall && $total_entries) ? '(' .$user->format_date($row['firstdate'], 'd m \'y') . ' - ' .
														$user->format_date($row['lastdate'], 'd m \'y') . ')': '',
									'OVERALLSORT'	=> ($overall) ? '&amp;overall=1' : ''));

		$sql = ($overall) ? 'SELECT o.name, o.hits AS total_per_os,
				(SELECT SUM(hits) FROM ' . $tables['archive'] . ' WHERE cat = 3) as total,
				SUM(o.hits) / (SELECT SUM(hits) FROM ' . $tables['archive'] . ' WHERE cat = 3) AS percent
				FROM ' . $tables['archive'] . ' o
				WHERE cat = 3 GROUP BY o.name ORDER BY ' . $sql_sort :

				'SELECT agent FROM ' . $tables['online'];

		$result = ($overall) ? $db->sql_query_limit($sql, $sconfig['statistics_max_os'], $start) : $db->sql_query($sql);
		$counter = 0;
		$graphstr = '';
		if ($overall)
		{
			while ($row = $db->sql_fetchrow($result))
			{
				$counter += 1;
				$template->assign_block_vars('onlinerow', array(
					'COUNTER'   	=> $start + $counter,
					'NAME'			=> $row['name'],
					'MODULECOUNT'	=> self::roundk($row['total_per_os']),
					'TMODULECOUNT'	=> $row['total_per_os'],
					'MODULETOTAL'	=> round((($row['total_per_os'] / $row['total']) * 100), 1) . ' % (' . self::roundk($row['total_per_os']) . ' of ' . self::roundk($row['total']) . ')',
					'TMODULETOTAL'	=> round((($row['total_per_os'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_os'] . ' of ' . $row['total'] . ')'
					)
				);
				$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($db->sql_escape($row['name'])) . '\', ' . $row['total_per_os'] . ']';
			}
		} else
		{
			include('find_os.php');
			$os = new find_os();
			$os_aray = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$os->setUserAgent($row['agent']);
				$os_aray = ($row['agent'] != '') ? self::count_array($os_aray, $os->getPlatform()) : null;
			}

			$total_entries = sizeof($os_aray);

			if ($sort_key == 'd')
			{
				($sort_dir == 'd') ? krsort($os_aray) : ksort($os_aray);
			} else
			{
				($sort_dir == 'd') ? arsort($os_aray) : asort($os_aray);
			}

			$counter = 0;
			$graphstr = '';
			$row['total'] = array_sum($os_aray);
			foreach (array_slice($os_aray, $start, $sconfig['statistics_max_browsers'], true) as $row['Operating System'] => $row['total_per_os'])
			{
				$counter += 1;
				$template->assign_block_vars('onlinerow', array(
					'COUNTER'   	=> $start + $counter,
					'NAME'			=> $row['Operating System'],
					'MODULECOUNT'	=> $row['total_per_os'],
					'MODULETOTAL'	=> round((($row['total_per_os'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_os'] . ' of ' . $row['total'] . ')'
					)
				);
				$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($db->sql_escape($row['Operating System'])) . '\', ' . $row['total_per_os'] . ']';
			}
		}

		$pagination = $phpbb_container->get('pagination');
		$base_url = $uaction . '&amp;screen=os&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir . (($overall)? '&amp;overall=1' : '&amp;overall=0');
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $sconfig['statistics_max_browsers'], $start);

		$template->assign_vars(array('ROWSPAN'		=> $total_entries,
									'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '&amp;overall=0',$base_url) : $base_url.'&amp;overall=1',
									'GRAPH' 		=> '[' . $graphstr . ']'));
	}

	public static function countries($start = 0, $uaction = '', $overall = 0)
	{
		global $db, $config, $sconfig, $user, $tables, $request, $template, $phpbb_container;

		// sort keys, direction en sql
		$sort_key	= $request->variable('sk', 'd');
		$sort_dir	= $request->variable('sd', 'd');
		$sort_by_sql = array('d' => (($overall) ? 'o.name' : 'o.domain'), 't' => 'total_per_domain', 'p' => 'percent');
		$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

		$template->assign_vars(array(
			'U_ACTION'			=> $uaction,
			'S_SORT_KEY'		=> $sort_key,
			'S_SORT_DIR'		=> $sort_dir,
			'SUB_DISPLAY'		=> 'graph',
			'SUBTITLE'			=> $user->lang['COUNTRIES'],
		));

		$sql = ($overall) ? 'SELECT COUNT(DISTINCT name) AS total_entries, MIN(first) AS firstdate, MAX(last) AS lastdate FROM ' . $tables['archive'] . ' WHERE cat = 4' :
							'SELECT COUNT(DISTINCT domain) AS total_entries FROM ' . $tables['online'];
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$total_entries = $row['total_entries'];
		$db->sql_freeresult($result);

		$pagination = $phpbb_container->get('pagination');
		$base_url = $uaction . '&amp;screen=countries&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir . (($overall)? '&amp;overall=1' : '&amp;overall=0');
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $sconfig['statistics_max_countries'], $start);

		$template->assign_vars(array('ROWSPAN'		=> $total_entries,
									'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '&amp;overall=0',$base_url) : $base_url.'&amp;overall=1',
									'OVERALLTXT'	=> ($overall) ? 'Today' : 'Overall',
									'MINMAXDATE'	=> ($overall && $total_entries) ? '(' .$user->format_date($row['firstdate'], 'd m \'y') . ' - ' .
														$user->format_date($row['lastdate'], 'd m \'y') . ')': '',
									'OVERALLSORT'	=> ($overall) ? '&amp;overall=1' : ''));

		$sql = ($overall) ? 'SELECT d.description, o.hits AS total_per_domain,
				(SELECT SUM(hits) FROM ' . $tables['archive'] . ' WHERE cat = 4) as total,
				SUM(o.hits) / (SELECT SUM(hits) FROM ' . $tables['archive'] . ' WHERE cat = 4) AS percent
				FROM ' . $tables['archive'] . ' o
				LEFT JOIN ' . $tables['domain'] . ' d ON (d.domain = o.name) WHERE cat = 4 GROUP BY o.name ORDER BY ' . $sql_sort :

				'SELECT d.description, COUNT(o.domain) AS total_per_domain,
				(SELECT COUNT(domain) FROM ' . $tables['online'] . ') as total,
				COUNT(o.domain) / (SELECT COUNT(domain) FROM ' . $tables['online'] . ') AS percent
				FROM ' . $tables['online'] . ' o
				LEFT JOIN ' . $tables['domain'] . ' d ON (d.domain = o.domain) GROUP BY o.domain ORDER BY ' . $sql_sort;

		$result = $db->sql_query_limit($sql, $sconfig['statistics_max_countries'], $start);
		$counter = 0;
		$graphstr = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$counter += 1;
			$template->assign_block_vars('onlinerow', array(
				'COUNTER'   	=> $start + $counter,
				'NAME'			=> $row['description'],
				'MODULECOUNT'	=> self::roundk($row['total_per_domain']),
				'TMODULECOUNT'	=> $row['total_per_domain'],
				'MODULETOTAL'	=> round((($row['total_per_domain'] / $row['total']) * 100), 1) . ' % (' . self::roundk($row['total_per_domain']) . ' of ' . self::roundk($row['total']) . ')',
				'TMODULETOTAL'	=> round((($row['total_per_domain'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_domain'] . ' of ' . $row['total'] . ')'
				)
			);
			$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($db->sql_escape($row['description'])) . '\', ' . $row['total_per_domain'] . ']';
		}
		$template->assign_vars(array('GRAPH' => '[' . $graphstr . ']'));
	}

	public static function referrals($start = 0, $uaction = '', $overall = 0)
	{
		global $db, $config, $sconfig, $user, $tables, $request, $template, $phpbb_container;

		// sort keys, direction en sql
		$sort_key	= $request->variable('sk', 'd');
		$sort_dir	= $request->variable('sd', 'd');
		$sort_by_sql = array('d' => 'domain', 't' => 'total_per_referer', 'p' => 'percent');
		$sql_sort = ((!$overall && $sort_key == 'p') ? 'total' :  $sort_by_sql[$sort_key]) . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

		$template->assign_vars(array(
			'U_ACTION'			=> $uaction,
			'S_SORT_KEY'		=> $sort_key,
			'S_SORT_DIR'		=> $sort_dir,
			'SUB_DISPLAY'		=> 'graph',
			'SUBTITLE'			=> $user->lang['REFERRALS'],
		));

		$sql = ($overall) ? 'SELECT COUNT(name) AS total_entries, MIN(first) AS firstdate, MAX(last) AS lastdate FROM ' . $tables['archive'] . ' WHERE cat = 7' :
				'SELECT COUNT(DISTINCT referer) AS total_entries FROM ' . $tables['online'] . ' WHERE referer <> "" AND referer not LIKE "%' . $config['server_name']. '%"';

		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$total_entries = $row['total_entries'];
		$db->sql_freeresult($result);

		$pagination = $phpbb_container->get('pagination');
		$base_url = $uaction . '&amp;screen=referrals&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir . (($overall)? '&amp;overall=1' : '&amp;overall=0');
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $sconfig['statistics_max_referer'], $start);

		$template->assign_vars(array('ROWSPAN' 		=> $total_entries,
									'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '&amp;overall=0',$base_url) : $base_url.'&amp;overall=1',
									'OVERALLTXT'	=> ($overall) ? 'Today' : 'Overall',
									'MINMAXDATE'	=> ($overall && $total_entries) ? '(' .$user->format_date($row['firstdate'], 'd m \'y') . ' - ' .
														$user->format_date($row['lastdate'], 'd m \'y') . ')': '',
									'OVERALLSORT'	=> ($overall) ? '&amp;overall=1' : ''));

		$sql = ($overall) ? 'SELECT name AS domain, hits AS total_per_referer,
				(SELECT SUM(hits) FROM ' . $tables['archive'] . ' WHERE cat = 7 <> "") as total,
				SUM(hits) / (SELECT SUM(hits) FROM ' . $tables['archive'] . ' WHERE cat = 7) AS percent
				FROM ' . $tables['archive'] . ' o WHERE cat = 7
				GROUP BY name ORDER BY ' . $sql_sort :

				'SELECT DISTINCT referer AS domain, COUNT(referer) AS total_per_referer,
				(SELECT COUNT(referer) AS total_entries FROM ' . $tables['online'] . ' WHERE referer <> "" AND referer not LIKE "%' . $config['server_name']. '%") AS total
				 FROM ' . $tables['online'] . ' WHERE referer <> "" AND referer not LIKE "%'. $config['server_name'] . '%"
				 GROUP BY referer ORDER BY ' . $sql_sort;

		$result = $db->sql_query_limit($sql, $sconfig['statistics_max_referer'], $start);
		$counter = 0;
		$graphstr = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$counter += 1;
			$template->assign_block_vars('onlinerow', array(
				'COUNTER'   	=> $start + $counter,
				'NAME'			=> $row['domain'],
				'MODULECOUNT'	=> self::roundk($row['total_per_referer']),
				'TMODULECOUNT'	=> $row['total_per_referer'],
				'MODULETOTAL'	=> round((($row['total_per_referer'] / $row['total']) * 100), 1) . ' % (' . self::roundk($row['total_per_referer']) . ' of ' . self::roundk($row['total']) . ')',
				'TMODULETOTAL'	=> round((($row['total_per_referer'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_referer'] . ' of ' . $row['total'] . ')'
				)
			);
			$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($db->sql_escape(substr($row['domain'], 0, 20))) . '\', ' . $row['total_per_referer'] . ']';
		}
		$template->assign_vars(array('GRAPH' => '[' . $graphstr . ']'));
	}

	public static function se($start = 0, $uaction = '', $overall = 0)
	{
		global $db, $config, $sconfig, $user, $tables, $request, $template, $phpbb_container;

		// sort keys, direction en sql
		$sort_key	= $request->variable('sk', 'd');
		$sort_dir	= $request->variable('sd', 'd');
		$sort_by_sql = array('d' => (($overall) ? 'name' : 'referer'), 't' => 'total_per_referer', 'p' => 'percent');
		$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

		$template->assign_vars(array(
			'U_ACTION'			=> $uaction,
			'S_SORT_KEY'		=> $sort_key,
			'S_SORT_DIR'		=> $sort_dir,
			'SUB_DISPLAY'		=> 'graph',
			'SUBTITLE'			=> $user->lang['SEARCHENG'],
		));

		$sql = 'SELECT COUNT(name) AS total_entries, MIN(first) AS firstdate, MAX(last) AS lastdate FROM ' . $tables['archive'] . ' 
				WHERE name REGEXP (SELECT GROUP_CONCAT(name ORDER BY name SEPARATOR "|") FROM '. $tables['se'] . ')';
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$total_entries = $row['total_entries'];
		$db->sql_freeresult($result);

		$pagination = $phpbb_container->get('pagination');
		$base_url = $uaction . '&amp;screen=se&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir . (($overall)? '&amp;overall=1' : '&amp;overall=0');
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $sconfig['statistics_max_se'], $start);

		$template->assign_vars(array('ROWSPAN' 		=> $total_entries,
									'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '&amp;overall=0',$base_url) : $base_url.'&amp;overall=1',
									'OVERALLTXT'	=> ($overall) ? 'Today' : 'Overall',
									'MINMAXDATE'	=> ($overall && $total_entries) ? '(' .$user->format_date($row['firstdate'], 'd m \'y') . ' - ' .
														$user->format_date($row['lastdate'], 'd m \'y') . ')': '',
									'OVERALLSORT'	=> ($overall) ? '&amp;overall=1' : ''));

		$sql = ($overall) ? 'SELECT name AS referer, hits AS total_per_referer, 
				(SELECT SUM(hits) FROM ' . $tables['archive'] . ' WHERE cat = 7) as total, 
				SUM(hits) / (SELECT SUM(hits) FROM ' . $tables['archive'] . '
				WHERE name REGEXP (SELECT GROUP_CONCAT(name ORDER BY name SEPARATOR "|") FROM '. $tables['se'] . ')) AS percent FROM ' . $tables['archive'] . ' 
				WHERE name REGEXP (SELECT GROUP_CONCAT(name ORDER BY name SEPARATOR "|") FROM '. $tables['se'] . ') AND cat = 7 GROUP BY name ORDER BY ' . $sql_sort :

				'SELECT DISTINCT SUBSTR(referer, 1, IF(LOCATE("?", referer, 8), LOCATE("?", referer, 8) -1, LENGTH(referer))) AS referer, COUNT(referer) AS total_per_referer, 
				(SELECT COUNT(referer) FROM ' . $tables['online'] . ' WHERE referer <> "") as total, 
				COUNT(referer) / (SELECT COUNT(referer) FROM ' . $tables['online'] . ' 
				WHERE referer REGEXP (SELECT GROUP_CONCAT(name ORDER BY name SEPARATOR "|") FROM '. $tables['se'] . ')) AS percent FROM ' . $tables['online'] . ' 
				WHERE referer REGEXP (SELECT GROUP_CONCAT(name ORDER BY name SEPARATOR "|") FROM '. $tables['se'] . ') 
				GROUP BY referer ORDER BY ' . $sql_sort;

		$result = $db->sql_query_limit($sql, $sconfig['statistics_max_se'], $start);
		$counter = 0;
		$graphstr = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$counter += 1;
			$template->assign_block_vars('onlinerow', array(
				'COUNTER'   	=> $start + $counter,
				'NAME'			=> $row['referer'],
				'MODULECOUNT'	=> self::roundk($row['total_per_referer']),
				'TMODULECOUNT'	=> $row['total_per_referer'],
				'MODULETOTAL'	=> round((($row['total_per_referer'] / $row['total']) * 100), 1) . ' % (' . self::roundk($row['total_per_referer']) . ' of ' . self::roundk($row['total']) . ')',
				'TMODULETOTAL'	=> round((($row['total_per_referer'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_referer'] . ' of ' . $row['total'] . ')'
				)
			);
			$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($db->sql_escape($row['referer'])) . '\', ' . $row['total_per_referer'] . ']';
		}
		$template->assign_vars(array('GRAPH' => '[' . $graphstr . ']'));
	}

	public static function ese($uaction = '', $action = '')
	{
		global $db, $config, $user, $tables, $request, $template, $phpbb_container;

		$sql = 'SELECT * FROM ' . $tables['se'] . ' ORDER BY name';
		$result = $db->sql_query($sql);
		$counter = 0;
		while ($row = $db->sql_fetchrow($result))
		{
			$counter += 1;
			$template->assign_block_vars('onlinerow', array(
				'COUNTER'   	=> $counter,
				'NAME'			=> $row['name'],
				'QUERY'			=> $row['query'],
				'ID'			=> $row['id']
				)
			);
		}
		$template->assign_vars(array(
			'U_ACTION'			=> $uaction,
			'SUB_DISPLAY'		=> 'ese',
			'SUBTITLE'			=> $user->lang['SEARCHENG'],
		));
	}

	public static function se_terms($start = 0, $uaction = '', $overall = 0)
	{
		global $db, $config, $sconfig, $user, $tables, $request, $template, $phpbb_container;

		// sort keys, direction en sql
		$sort_key	= $request->variable('sk', 'd');
		$sort_dir	= $request->variable('sd', 'd');
		$sort_by_sql = array('d' => (($overall) ? 'name' : 'referer'), 't' => 'total_per_referer', 'p' => 'percent');
		$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

		$template->assign_vars(array(
			'U_ACTION'			=> $uaction,
			'S_SORT_KEY'		=> $sort_key,
			'S_SORT_DIR'		=> $sort_dir,
			'SUB_DISPLAY'		=> 'graph',
			'SUBTITLE'			=> $user->lang['SEARCHTERMS'],
		));

		$sql = ($overall) ? 'SELECT name AS se_terms, hits AS rowtotal, MIN(first) AS firstdate, MAX(last) AS lastdate FROM ' . $tables['archive'] . ' WHERE cat = 8':
							'SELECT DISTINCT se_terms, COUNT(se_terms) AS rowtotal FROM ' . $tables['online'] . ' WHERE se_terms <> "" GROUP BY se_terms';
		$result = $db->sql_query($sql);
		$se_terms = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$terms = explode(' ', $row['se_terms']);
			foreach($terms as $key)
			{
				isset($se_terms[$key]) ? $se_terms[$key] = (isset($se_terms[$key]) ? $se_terms[$key] + (int) $row['rowtotal'] : (int) $row['rowtotal']) : null;
			}
			($overall) ? $firstdate = $row['firstdate'] : null;
			($overall) ? $lastdate = $row['lastdate'] : null;
		}

		$total_entries = sizeof($se_terms);

		if ($sort_key == 'd')
		{
			($sort_dir == 'd') ? ksort($se_terms) : krsort($se_terms);
		} else
		{
			($sort_dir == 'd') ? asort($se_terms) : arsort($se_terms);
		}

		$pagination = $phpbb_container->get('pagination');
		$base_url = $uaction . '&amp;screen=se_terms&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir . (($overall)? '&amp;overall=1' : '&amp;overall=0');
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $sconfig['statistics_max_se_terms'], $start);

		$template->assign_vars(array('ROWSPAN' 		=> $total_entries,
									'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '&amp;overall=0', $base_url) : $base_url . '&amp;overall=1',
									'OVERALLTXT'	=> ($overall) ? 'Today' : 'Overall',
									'MINMAXDATE'	=> ($overall && $total_entries) ? '(' .$user->format_date($firstdate, 'd m \'y') . ' - ' .
														$user->format_date($lastdate, 'd m \'y') . ')': '',
									'OVERALLSORT'	=> ($overall) ? '&amp;overall=1' : ''));

		$counter = 0;
		$graphstr = '';
		$row['total'] = array_sum($se_terms);
		foreach ($se_terms as $row['referer'] => $row['total_per_referer'])
		{
			$counter += 1;
			$template->assign_block_vars('onlinerow', array(
				'COUNTER'   	=> $start + $counter,
				'NAME'			=> $row['referer'],
				'MODULECOUNT'	=> self::roundk($row['total_per_referer']),
				'TMODULECOUNT'	=> $row['total_per_referer'],
				'MODULETOTAL'	=> round((($row['total_per_referer'] / $row['total']) * 100), 1) . ' % (' . self::roundk($row['total_per_referer']) . ' of ' . self::roundk($row['total']) . ')',
				'TMODULETOTAL'	=> round((($row['total_per_referer'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_referer'] . ' of ' . $row['total'] . ')'
				)
			);
			$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($db->sql_escape($row['referer'])) . '\', ' . $row['total_per_referer'] . ']';
		}
		$template->assign_vars(array('GRAPH' => '[' . $graphstr . ']'));
	}

	public static function crawl($start = 0, $uaction = '', $overall = 0)
	{
		global $db, $config, $sconfig, $user, $tables, $request, $template, $phpbb_container;

		// sort keys, direction en sql
		$sort_key	= $request->variable('sk', 'd');
		$sort_dir	= $request->variable('sd', 'd');
		$sort_by_sql = array('d' => ($overall) ? 'a.name' : 'a.uname', 't' => 'total_per_users', 'p' => 'total');
		$sql_sort = ((!$overall && $sort_key == 't') ? 'total' :  $sort_by_sql[$sort_key]) . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

		$template->assign_vars(array(
			'U_ACTION'			=> $uaction,
			'S_SORT_KEY'		=> $sort_key,
			'S_SORT_DIR'		=> $sort_dir,
			'SUB_DISPLAY'		=> 'graph',
			'SUBTITLE'			=> $user->lang['CRAWLERS'],
		));

		$sql = 'SELECT group_id	FROM ' . GROUPS_TABLE . " WHERE group_name = 'BOTS' AND group_type = " . GROUP_SPECIAL;
		$result = $db->sql_query($sql);
		$group_id = (int) $db->sql_fetchfield('group_id');
		$db->sql_freeresult($result);

		$sql = ($overall) ? 'SELECT  COUNT(DISTINCT a.name) AS total_entries, MIN(first) AS firstdate, MAX(last) AS lastdate
				FROM    ' . $tables['archive'] . ' a
						LEFT JOIN ' . BOTS_TABLE . ' b
							ON a.name = b.bot_name
				WHERE a.cat = 5 AND b.bot_name IS not null' :

		'SELECT  COUNT(DISTINCT a.uname) AS total_entries
				FROM    ' . $tables['online'] . ' a
						LEFT JOIN ' . BOTS_TABLE . ' b
							ON a.uname = b.bot_name
				WHERE  b.bot_name IS not NULL';
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$total_entries = $row['total_entries'];
		$db->sql_freeresult($result);

		$pagination = $phpbb_container->get('pagination');
		$base_url = $uaction . '&amp;screen=crawl&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir . (($overall)? '&amp;overall=1' : '&amp;overall=0');
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $sconfig['statistics_max_crawl'], $start);

		$template->assign_vars(array('ROWSPAN' 		=> $total_entries,
									'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '&amp;overall=0', $base_url) : $base_url . '&amp;overall=1',
									'OVERALLTXT'	=> ($overall) ? 'Today' : 'Overall',
									'MINMAXDATE'	=> ($overall && $total_entries) ? '(' .$user->format_date($row['firstdate'], 'd m \'y') . ' - ' .
														$user->format_date($row['lastdate'], 'd m \'y') . ')': '',
									'OVERALLSORT'	=> ($overall) ? '&amp;overall=1' : ''));

		$sql = ($overall) ? 'SELECT  a.name AS uname, a.hits AS total_per_users, u.user_id,
				(SELECT  sum(a.hits)
				FROM    ' . $tables['archive'] . ' a
						LEFT JOIN ' . BOTS_TABLE . ' b
							ON a.name = b.bot_name
				WHERE  a.cat = 5 AND b.bot_name IS NULL) AS total
				FROM    ' . $tables['archive'] . ' a
						LEFT JOIN ' . BOTS_TABLE . ' b
							ON a.name = b.bot_name
						LEFT JOIN ' . USERS_TABLE . ' u ON u.username = a.name
				WHERE  a.cat = 5 AND b.bot_name IS not NULL ORDER BY ' . $sql_sort :

				'SELECT  a.uname, COUNT(a.uname) AS total_per_users, u.user_id,
				(SELECT  COUNT(a.uname)
				FROM    ' . $tables['online'] . ' a
						LEFT JOIN ' . BOTS_TABLE . ' b
							ON a.uname = b.bot_name
				WHERE  b.bot_name IS NULL) as total
				FROM    ' . $tables['online'] . ' a
						LEFT JOIN ' . BOTS_TABLE . ' b
							ON a.uname = b.bot_name
						LEFT JOIN ' . USERS_TABLE . ' u ON u.username = a.uname
				WHERE  b.bot_name IS not NULL GROUP BY a.uname ORDER BY ' . $sql_sort;

		$result = $db->sql_query_limit($sql, $sconfig['statistics_max_crawl'], $start);
		$counter = 0;
		$graphstr = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$counter += 1;
			$template->assign_block_vars('onlinerow', array(
				'COUNTER'   	=> $start + $counter,
				'NAME'			=> $row['uname'],
				'USER_ID'		=> $row['user_id'],
				'MODULECOUNT'	=> self::roundk($row['total_per_users']),
				'TMODULECOUNT'	=> $row['total_per_users'],
				'MODULETOTAL'	=> round((($row['total_per_users'] / $row['total']) * 100), 1) . ' % (' . self::roundk($row['total_per_users']) . ' of ' . self::roundk($row['total']) . ')',
				'TMODULETOTAL'	=> round((($row['total_per_users'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_users'] . ' of ' . $row['total'] . ')'
				)
			);
			$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($db->sql_escape($row['uname'])) . '\', ' . $row['total_per_users'] . ']';
		}
		$template->assign_vars(array('GRAPH' => '[' . $graphstr . ']'));
	}

	public static function modules($start = 0, $uaction = '', $overall = 0)
	{
		global $db, $config, $sconfig, $user, $tables, $request, $template, $phpbb_container;

		$modules = self::get_modules();
		// sort keys, direction en sql
		$sort_key	= $request->variable('sk', 'm');
		$sort_dir	= $request->variable('sd', 'd');
		$sort_by_sql = array('m' => (($overall) ? 'name' : 'module'), 't' => 'total_per_module', 'p' => 'percent');

		if ($sort_key == 'm')
		{
			($sort_dir == 'd') ? asort($modules) : arsort($modules);
			$moduleorder = implode('\', \'', array_keys($modules));
			$sort_by_sql['m'] = 'FIELD(' . (($overall) ? 'name' : 'module') . ', \'' . $moduleorder . '\')';
		}
		$sql_sort = $sort_by_sql[$sort_key] . (($sort_key == 'm') ? '' : ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC'));

		$template->assign_vars(array(
			'U_ACTION'			=> $uaction,
			'S_SORT_KEY'		=> $sort_key,
			'S_SORT_DIR'		=> $sort_dir,
			'SUB_DISPLAY'		=> 'graph',
			'SUBTITLE'			=> $user->lang['MODULES'],
		));

		$sql = ($overall) ? 'SELECT COUNT(name) AS total_entries, MIN(first) AS firstdate, MAX(last) AS lastdate FROM ' . $tables['archive'] . ' WHERE cat = 1' :
				'SELECT COUNT(DISTINCT module) AS total_entries FROM ' . $tables['online'];
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$total_entries = $row['total_entries'];
		$db->sql_freeresult($result);

		$pagination = $phpbb_container->get('pagination');
		$base_url = $uaction . '&amp;screen=modules&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir . (($overall)? '&amp;overall=1' : '&amp;overall=0');
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $sconfig['statistics_max_modules'], $start);

		$template->assign_vars(array('ROWSPAN'		=> $total_entries,
									'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '&amp;overall=0', $base_url) : $base_url . '&amp;overall=1',
									'OVERALLTXT'	=> ($overall) ? 'Today' : 'Overall',
									'MINMAXDATE'	=> ($overall && $total_entries) ? '(' .$user->format_date($row['firstdate'], 'd m \'y') . ' - ' .
														$user->format_date($row['lastdate'], 'd m \'y') . ')': '',
									'OVERALLSORT'	=> ($overall) ? '&amp;overall=1' : ''));

		$sql = ($overall) ? 'SELECT name AS module, hits AS total_per_module,
				(SELECT SUM(hits) FROM ' . $tables['archive'] . ' WHERE cat = 1) as total,
				SUM(hits) / (SELECT SUM(hits) FROM ' . $tables['archive'] . ' WHERE cat = 1) AS percent
				FROM ' . $tables['archive'] . ' WHERE cat = 1 GROUP BY name ORDER BY ' . $sql_sort :

				'SELECT module, COUNT(module) AS total_per_module,
				(SELECT COUNT(MODULE) FROM ' . $tables['online'] . ') as total,
				COUNT( module) / (SELECT COUNT(module) FROM ' . $tables['online'] . ') AS percent
				FROM ' . $tables['online'] . ' GROUP BY module ORDER BY ' . $sql_sort;

		$result = $db->sql_query_limit($sql, $sconfig['statistics_max_modules'], $start);
		$counter = 0;
		$graphstr = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$counter += 1;
			$template->assign_block_vars('onlinerow', array(
				'COUNTER'   	=> $start + $counter,
				'NAME'			=> isset($modules[$row['module']]) ? $modules[$row['module']] : 'Module not found',
				'MODULECOUNT'	=> self::roundk($row['total_per_module']),
				'TMODULECOUNT'	=> $row['total_per_module'],
				'MODULETOTAL'	=> round((($row['total_per_module'] / $row['total']) * 100), 1) . ' % (' . self::roundk($row['total_per_module']) . ' of ' . self::roundk($row['total']) . ')',
				'TMODULETOTAL'	=> round((($row['total_per_module'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_module'] . ' of ' . $row['total'] . ')'
				)
			);
			$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode(isset($modules[$row['module']]) ? $db->sql_escape($modules[$row['module']]) : 'Module not found') . '\', ' . $row['total_per_module'] . ']';
		}
		$template->assign_vars(array('GRAPH' => '[' . $graphstr . ']'));
	}

	public static function screens($start = 0, $uaction = '', $overall = 0)
	{
		global $db, $config, $sconfig, $user, $tables, $request, $template, $phpbb_container;

		// sort keys, direction en sql
		$sort_key	= $request->variable('sk', 'd');
		$sort_dir	= $request->variable('sd', 'd');
		$sort_by_sql = array('d' => ($overall) ? 'name' : 'o.scr_res', 't' => 'total_per_screen', 'p' => 'percent');
		$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

		$template->assign_vars(array(
			'U_ACTION'			=> $uaction,
			'S_SORT_KEY'		=> $sort_key,
			'S_SORT_DIR'		=> $sort_dir,
			'SUB_DISPLAY'		=> 'graph',
			'SUBTITLE'			=> $user->lang['RESOLUTIONS'],
		));

		$sql = ($overall) ? 'SELECT COUNT(name) AS total_entries, MIN(first) AS firstdate, MAX(last) AS lastdate FROM ' . $tables['archive'] . ' WHERE cat = 6' :
							'SELECT COUNT(DISTINCT scr_res) AS total_entries FROM ' . $tables['online'];
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$total_entries = $row['total_entries'];
		$db->sql_freeresult($result);

		$pagination = $phpbb_container->get('pagination');
		$base_url = $uaction . '&amp;screen=screens&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir . (($overall)? '&amp;overall=1' : '&amp;overall=0');
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $sconfig['statistics_max_screens'], $start);

		$template->assign_vars(array('ROWSPAN' 		=> $total_entries,
									'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '&amp;overall=0', $base_url) : $base_url . '&amp;overall=1',
									'OVERALLTXT'	=> ($overall) ? 'Today' : 'Overall',
									'MINMAXDATE'	=> ($overall && $total_entries) ? '(' .$user->format_date($row['firstdate'], 'd m \'y') . ' - ' .
														$user->format_date($row['lastdate'], 'd m \'y') . ')': '',
									'OVERALLSORT'	=> ($overall) ? '&amp;overall=1' : ''));

		$sql = ($overall) ? 'SELECT name AS scr_res, hits AS total_per_screen, 
				(SELECT SUM(hits) FROM ' . $tables['archive'] . ' WHERE cat = 6) as total,
				SUM(hits) / (SELECT SUM(hits) FROM ' . $tables['archive'] . ' WHERE cat = 6) AS percent
				FROM ' . $tables['archive'] . '
				WHERE cat = 6 GROUP BY name ORDER BY ' . $sql_sort :

				'SELECT o.scr_res, COUNT(o.scr_res) AS total_per_screen,
				(SELECT COUNT(scr_res) FROM ' . $tables['online'] . ') as total,
				COUNT(o.scr_res) / (SELECT COUNT(scr_res) FROM ' . $tables['online'] . ') AS percent
				FROM ' . $tables['online'] . ' o
				GROUP BY o.scr_res ORDER BY ' . $sql_sort;

		$result = $db->sql_query_limit($sql, $sconfig['statistics_max_screens'], $start);
		$counter = 0;
		$graphstr = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$counter += 1;
			$template->assign_block_vars('onlinerow', array(
				'COUNTER'   	=> $start + $counter,
				'NAME'			=> $row['scr_res'],
				'MODULECOUNT'	=> self::roundk($row['total_per_screen']),
				'TMODULECOUNT'	=> $row['total_per_screen'],
				'MODULETOTAL'	=> round((($row['total_per_screen'] / $row['total']) * 100), 1) . ' % (' . self::roundk($row['total_per_screen']) . ' of ' . self::roundk($row['total']) . ')',
				'TMODULETOTAL'	=> round((($row['total_per_screen'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_screen'] . ' of ' . $row['total'] . ')'
				)
			);
			$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($row['scr_res']) . '\', ' . $row['total_per_screen'] . ']';
		}
		$template->assign_vars(array('GRAPH' => '[' . $graphstr . ']'));
	}

	public static function stats($start = 0, $uaction = '')
	{
		global $db, $config, $user, $tables, $request, $template;

		// sort keys, direction en sql
		$sort_key	= $request->variable('sk', 'd');
		$month = $request->variable('m', date('n', time()));
		$year = $request->variable('y', date('Y', time()));

		$template->assign_vars(array(
			'U_ACTION'			=> $uaction,
			'S_SORT_KEY'		=> $sort_key,
			'SUB_DISPLAY'		=> 'stats',
			'PREV'				=> ($sort_key != 'm') ? '&amp;m=' . ($month - 1) . '&amp;y=' . $year : '&amp;m=12&amp;y=' . ($year - 1),
			'NEXT'				=> ($sort_key != 'm') ? '&amp;m=' . ($month + 1) . '&amp;y=' . $year : '&amp;m=12&amp;y=' . ($year + 1),
			'SUBTITLE'			=> $user->lang['AVERAGES'],
		));

		if ($sort_key == 'd')
		{
			$sql =  'SELECT hits, month, day FROM ' . $tables['stats'] . ' WHERE year = ' . $year . ' AND month = ' . $month . ' ORDER BY year, month, day ASC';
		} else if ($sort_key == 'm')
		{
			$sql =  'SELECT SUM(hits) AS hits, month, year FROM ' . $tables['stats'] . ' WHERE year = ' . $year . ' GROUP BY month ORDER BY year, month ASC';
		} else
		{
			$sql =  'SELECT SUM(hits) AS hits, year FROM ' . $tables['stats'] . ' GROUP BY year ORDER BY year ASC';
		}
		$result = $db->sql_query($sql);
		$graphstr = $datestr ='';
		while ($row = $db->sql_fetchrow($result))
		{
			$graphstr .= (($graphstr == '') ? '' : ', ') . '' . $row['hits'] . '';
			$datestr .= (($datestr == '') ? '' : ', ') . '\'' . html_entity_decode(
						(($sort_key == 'd') ? $row['day'] . ' ' . $row['month'] : (($sort_key == 'm') ? $row['month'] . '<br>' . $row['year'] : $row['year']))) . '\'';
		}
		$template->assign_vars(array(
			'STATS' => '[' . $graphstr . ']', 'DATES' => '[' . $datestr . ']',
			'TITLE' => '\'' . (($sort_key == 'd') ? $user->lang['DOV'] . ' ' . date("F",mktime(0,0,0,$month,1,2014)) . ' ' . $year :
						(($sort_key == 'm') ? $user->lang['MOV'] . ' ' . $year : $user->lang['YOV'] . ' ')) . '\''));
	}

	public static function ustats($start = 0, $uaction = '')
	{
		global $db, $config, $user, $tables, $request, $template;

		$template->assign_vars(array(
			'U_ACTION'			=> $uaction,
			'SUB_DISPLAY'		=> 'stats',
			'SUBTITLE'			=> $user->lang['USERSTATS'],
		));

		$sql =  'SELECT  MAX(DATE(FROM_UNIXTIME(time))) AS date, HOUR(FROM_UNIXTIME(time)) as hour, COUNT(id) as hits
				FROM ' . $tables['online'] . ' GROUP BY hour order by date, hour ASC';
		$result = $db->sql_query($sql);
		$graphstr = $datestr ='';
		while ($row = $db->sql_fetchrow($result))
		{
			$graphstr .= (($graphstr == '') ? '' : ', ') . '' . $row['hits'] . '';
			$datestr .= (($datestr == '') ? '' : ', ') . '\'' . html_entity_decode($row['hour']) . '\'';
		}
		$template->assign_vars(array(
			'STATS' => '[' . $graphstr . ']', 'DATES' => '[' . $datestr . ']',
			'TITLE' => '\'' . $user->lang['HOV'] . '\''));
	}

	public static function users($start = 0, $uaction = '', $overall = 0)
	{
		global $db, $config, $sconfig, $user, $tables, $request, $template, $phpbb_container;

		// sort keys, direction en sql
		$sort_key	= $request->variable('sk', 'd');
		$sort_dir	= $request->variable('sd', 'd');
		$sort_by_sql = array('d' => ($overall) ? 'a.name' : 'a.uname', 't' => 'total_per_users', 'p' => 'total');
		$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

		$template->assign_vars(array(
			'U_ACTION'			=> $uaction,
			'S_SORT_KEY'		=> $sort_key,
			'S_SORT_DIR'		=> $sort_dir,
			'SUB_DISPLAY'		=> 'graph',
			'SUBTITLE'			=> $user->lang['USERS'],
		));

		$sql = 'SELECT group_id	FROM ' . GROUPS_TABLE . " WHERE group_name = 'BOTS' AND group_type = " . GROUP_SPECIAL;
		$result = $db->sql_query($sql);
		$group_id = (int) $db->sql_fetchfield('group_id');
		$db->sql_freeresult($result);

		$sql = ($overall) ? 'SELECT  COUNT(DISTINCT a.name) AS total_entries, MIN(first) AS firstdate, MAX(last) AS lastdate
				FROM    ' . $tables['archive'] . ' a
						LEFT JOIN ' . BOTS_TABLE . ' b
							ON a.name = b.bot_name
				WHERE a.cat = 5 AND b.bot_name IS NULL' :

		'SELECT  COUNT(DISTINCT a.uname) AS total_entries
				FROM    ' . $tables['online'] . ' a
						LEFT JOIN ' . BOTS_TABLE . ' b
							ON a.uname = b.bot_name
				WHERE  b.bot_name IS NULL';
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$total_entries = $row['total_entries'];
		$db->sql_freeresult($result);

		$pagination = $phpbb_container->get('pagination');
		$base_url = $uaction . '&amp;screen=users&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir . (($overall)? '&amp;overall=1' : '&amp;overall=0');
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $sconfig['statistics_max_users'], $start);

		$template->assign_vars(array('ROWSPAN' 		=> $total_entries,
									'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '&amp;overall=0', $base_url) : $base_url . '&amp;overall=1',
									'OVERALLTXT'	=> ($overall) ? 'Today' : 'Overall',
									'MINMAXDATE'	=> ($overall && $total_entries) ? '(' .$user->format_date($row['firstdate'], 'd m \'y') . ' - ' .
														$user->format_date($row['lastdate'], 'd m \'y') . ')': '',
									'OVERALLSORT'	=> ($overall) ? '&amp;overall=1' : ''));

		$sql = ($overall) ? 'SELECT  a.name AS uname, a.hits AS total_per_users, u.user_id,
				a.hits / (SELECT  sum(a.hits)
				FROM    ' . $tables['archive'] . ' a
						LEFT JOIN ' . BOTS_TABLE . ' b
							ON a.name = b.bot_name
				WHERE  a.cat = 5 AND b.bot_name IS NULL) AS total
				FROM    ' . $tables['archive'] . ' a
						LEFT JOIN ' . BOTS_TABLE . ' b
							ON a.name = b.bot_name
						LEFT JOIN ' . USERS_TABLE . ' u ON u.username = a.name
				WHERE  a.cat = 5 AND b.bot_name IS NULL ORDER BY ' . $sql_sort :

				'SELECT  a.uname, COUNT(a.uname) AS total_per_users, u.user_id,
				COUNT(a.uname) / (SELECT  COUNT(a.uname)
				FROM    ' . $tables['online'] . ' a
						LEFT JOIN ' . BOTS_TABLE . ' b
							ON a.uname = b.bot_name
				WHERE  b.bot_name IS NULL) as total
				FROM    ' . $tables['online'] . ' a
						LEFT JOIN ' . BOTS_TABLE . ' b
							ON a.uname = b.bot_name
						LEFT JOIN ' . USERS_TABLE . ' u ON u.username = a.uname
				WHERE  b.bot_name IS NULL GROUP BY a.uname ORDER BY ' . $sql_sort;

		$result = $db->sql_query_limit($sql, $sconfig['statistics_max_users'], $start);
		$counter = 0;
		$graphstr = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$counter += 1;
			$template->assign_block_vars('onlinerow', array(
				'COUNTER'   	=> $start + $counter,
				'NAME'			=> $row['uname'],
				'USER_ID'		=> $row['user_id'],
				'MODULECOUNT'	=> self::roundk($row['total_per_users']),
				'TMODULECOUNT'	=> $row['total_per_users'],
				'MODULETOTAL'	=> round((($row['total']) * 100), 1) . ' % (' . self::roundk($row['total_per_users']) . ' of ' . self::roundk($row['total'] * 100) . ')',
				'TMODULETOTAL'	=> round((($row['total']) * 100), 1) . ' % (' . $row['total_per_users'] . ' of ' . $row['total'] * 100 . ')'
				)
			);
			$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($db->sql_escape($row['uname'])) . '\', ' . $row['total_per_users'] . ']';
		}
		$template->assign_vars(array('GRAPH' => '[' . $graphstr . ']'));
	}

	public static function top10($start = 0, $uaction = '')
	{
		global $db, $config, $sconfig, $user, $table_prefix, $tables, $request, $template, $phpbb_container;
		$module_aray = array(0 => 'COUNTRIES', 1 => 'REFERRALS', 2 => 'SEARCHENG', 3 => 'SEARCHTERMS', 4 => 'BROWSERS', 5 => 'CRAWLERS', 6 => 'SYSTEMS', 7 => 'MODULES',
							8 => 'RESOLUTIONS', 9 => 'USERS', 10 => 'FL_DATE', 11 => 'POSTS', 12 => 'UGROUPS', 13 => 'SEARCHRESULTS', 14 => 'UNIQUE');
		$modules = self::get_modules();
		
		if (version_compare($config['version'], '3.2.*', '<'))
		{
			 $db_tools = new \phpbb\db\tools($db);
		} else
		{
			$db_tools_factory = new \phpbb\db\tools\factory();
			$db_tools = $db_tools_factory->get($db);
		}
		$searchresulttabel = $db_tools->sql_table_exists($table_prefix . 'searchresults');

		$sql_aray[] = 'SELECT d.description AS name, o.hits FROM ' . $tables['archive'] . ' o LEFT JOIN ' . $tables['domain'] . ' d ON (d.domain = o.name) 
						WHERE cat = 4 GROUP BY o.name ORDER BY hits DESC';
		$sql_aray[] = 'SELECT name, hits FROM ' . $tables['archive'] . ' WHERE cat = 7 ORDER BY hits DESC';
		$sql_aray[] = 'SELECT name, hits FROM ' . $tables['archive'] . ' WHERE cat = 7 ORDER BY hits DESC';
		$sql_aray[] = 'SELECT name, hits FROM ' . $tables['archive'] . ' WHERE cat = 8 ORDER BY hits DESC';
		$sql_aray[] = 'SELECT name, hits FROM ' . $tables['archive'] . ' WHERE cat = 2 ORDER BY hits DESC';
		$sql_aray[] = 'SELECT a.name, a.hits FROM ' . $tables['archive'] . ' a LEFT JOIN ' . BOTS_TABLE . ' b ON a.name = b.bot_name 
					   WHERE  a.cat = 5 AND b.bot_name IS NOT NULL ORDER BY hits DESC';
		$sql_aray[] = 'SELECT name, hits FROM ' . $tables['archive'] . ' WHERE cat = 3 ORDER BY hits DESC';
		$sql_aray[] = 'SELECT name, hits FROM ' . $tables['archive'] . ' WHERE cat = 1 ORDER BY hits DESC';
		$sql_aray[] = 'SELECT name, hits FROM ' . $tables['archive'] . ' WHERE cat = 6 ORDER BY hits DESC';
		$sql_aray[] = 'SELECT a.name, a.hits FROM ' . $tables['archive'] . ' a LEFT JOIN ' . USERS_TABLE . ' b ON a.name = b.username 
					   WHERE  a.cat = 5 AND b.username IS NOT NULL ORDER BY hits DESC';

		$sql_aray[] = 'SELECT "First startdate" AS name, MIN(first) AS hits FROM ' . $tables['archive'] . ' UNION 
					   SELECT "Last startdate" AS name, MAX(last) AS hits FROM ' . $tables['archive'] . ' 
					   UNION SELECT "Rows" AS name, ROUND(COUNT(id), 0) AS hits FROM ' . $tables['archive'] . ' 
					   UNION SELECT "Table size" AS name, CASE WHEN data_length + index_length < 1024 THEN CONCAT(ROUND(((data_length + index_length)), 1), " B")
					   WHEN data_length + index_length BETWEEN 1024 AND 1048576 THEN CONCAT(ROUND(((data_length + index_length) / 1024), 1), " Kb")
					   WHEN data_length + index_length > 1048576 THEN CONCAT(ROUND(((data_length + index_length) / 1024 / 1024), 1), " Mb")
					   END AS hits FROM information_schema.TABLES 
					   WHERE table_schema = "' . $db->get_db_name() . '" AND table_name = "' . $tables['archive'] . '"' . 
					   (($searchresulttabel) ? ' UNION SELECT "Searchwords" AS name, COUNT(search_key) AS hits FROM ' . $table_prefix . 'searchresults' : '');
		$sql_aray[] = '';
		$sql_aray[] = 'SELECT g.group_name AS name, g.group_type, hits FROM ' . $tables['archive'] . ' LEFT JOIN ' . GROUPS_TABLE . ' g ON g.group_id = name WHERE cat = 10 ORDER BY hits DESC';
		$sql_aray[] = (!$phpbb_container->get('ext.manager')->is_enabled('forumhulp/searchresults')) ?
						'SELECT "<a href=\"https://github.com/ForumHulp/searchresults\" target=\"_blank\">Searchresult extension</a> not enabled" AS name, "" AS hits' :
						'SELECT search_keywords AS name, hits FROM ' . $phpbb_container->getParameter('tables.searchresults') . ' ORDER BY hits DESC';
		$sql_aray[] = 'SELECT name, hits FROM ' . $tables['archive'] . ' WHERE cat = 9 ORDER BY hits DESC';

		$pagination = $phpbb_container->get('pagination');
		$base_url = $uaction . '&amp;screen=top10';
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', 13, 6, $start);

		for($i = $start; $i < min($start + 6, sizeof($module_aray)); $i++)
		{
			$template->assign_block_vars('blocks', array(
				'KEY'			=> $i,
				'TITLE'			=> $user->lang[$module_aray[$i]],
			));

			if ($i < 14)
			{
				$result = $db->sql_query_limit($sql_aray[$i], 10, 0);
				$counter = 0;
				while ($row = $db->sql_fetchrow($result))
				{
					$counter += 1;
					$template->assign_block_vars('blocks.block', array(
						'COUNTER'  	=> $counter,
						'NAME'		=> ($i == 7) ? ((isset($modules[$row['name']])) ? $modules[$row['name']] : 'Not found') : 
										(($i == 12 && $row['group_type'] == GROUP_SPECIAL) ? $user->lang['G_' . $row['name']] : $row['name']),
						'HITS'		=> ($i == 10 && isset($row['hits']) && $counter < 3) ? $user->format_date($row['hits'], 'd m \'y') : self::roundk($row['hits']),
						'THITS'		=> ($i == 10 && isset($row['hits']) && $counter < 3) ? $user->format_date($row['hits']) : $row['hits']
						)
					);
				}
				if ($counter < 10)
				{
					for($counter + 1; $counter + 1 <= 10; $counter++)
					{
						if ($i == 11)
						{
							$aray[0] = array('name' => $user->lang['PPD'], 'hits' => round($config['num_posts'] / ((time() - $config['board_startdate']) / 86400), 1));
							$aray[1] = array('name' => $user->lang['PPM'], 'hits' => round($config['num_posts'] / ((time() - $config['board_startdate']) / 2440800), 1));
							$aray[2] = array('name' => $user->lang['TPD'], 'hits' => round($config['num_topics'] / ((time() - $config['board_startdate']) / 86400), 1));
							$aray[3] = array('name' => $user->lang['TPM'], 'hits' => round($config['num_topics'] / ((time() - $config['board_startdate']) / 2440800), 1));
							$aray[4] = array('name' => $user->lang['FORUMDAYS'], 'hits' => floor((time() - $config['board_startdate']) / 86400));
							$aray[5] = array('name' => $user->lang['APPT'], 'hits' => floor($config['num_posts'] / $config['num_topics']));
							$aray[6] = array('name' => $user->lang['APPU'], 'hits' => floor($config['num_posts'] / $config['num_users']));
						}

						$template->assign_block_vars('blocks.block', array(
							'COUNTER'  	=> $counter + 1,
							'NAME'		=> ($i == 11 && isset($aray[$counter])) ? $aray[$counter]['name'] : '',
							'HITS'		=> ($i == 11 && isset($aray[$counter])) ? $aray[$counter]['hits'] : '',
							)
						);
					}
				}
			} else
			{
				$result = $db->sql_query_limit($sql_aray[$i], 10, 0);
				$counter = 0;
				while ($row = $db->sql_fetchrow($result))
				{
					$counter += 1;
					$template->assign_block_vars('blocks.block', array(
						'COUNTER'  	=> $counter,
						'NAME'		=> $user->format_date($row['name'], 'd F \'y'),
						'HITS'		=> self::roundk($row['hits']),
						'THITS'		=> $row['hits']
						)
					);
				}
				for($counter + 1; $counter + 1 <= 10; $counter++)
				{
					$template->assign_block_vars('blocks.block', array(
						'COUNTER'  	=> $counter + 1,
						'NAME'		=> '',
						'HITS'		=> '',
						'THITS'		=> ''
						)
					);
				}
			}
		}

		$template->assign_vars(array(
			'U_ACTION'			=> $uaction,
			'SUB_DISPLAY'		=> 'top10'
		));
	}

	public static function roundk($value)
	{
		if ($value > 999 && $value <= 999999)
		{
			$result = floor($value / 1000) . ' K';
		} else if ($value > 999999)
		{
			$result = floor($value / 1000000) . ' M';
		} else
		{
			$result = $value;
		}
		return $result;
	}

	public static function config($start = 0, $uaction = '')
	{
		global $db, $config, $sconfig, $user, $tables, $request, $template, $phpbb_container;

		$se_name = $request->variable('se_name', array('' => ''), true);
		$se_query = $request->variable('se_query', array('' => ''), true);
		$se_mark = $request->variable('mark', array('' => 0), true);
		if (sizeof($se_name) == sizeof($se_query) && $request->variable('submit_ese', ''))
		{
			foreach($se_name as $key => $value)
			{
				if ($key != -1)
				{
					$sql = 'UPDATE ' . $tables['se'] . ' SET name = "' . $value . '", query = "' . $se_query[$key] . '" WHERE id  = '. $key;
				} else if ($value != '' && $se_query[$key] != '')
				{
					$sql = 'INSERT INTO ' . $tables['se'] . ' VALUES(NULL, "' . $value . '", "' . $se_query[$key] . '")';
				}
				$db->sql_query($sql);
			}
		}

		if (sizeof($se_mark) && $request->variable('delmarked', ''))
		{
			foreach($se_mark as $key => $value)
			{
				$sql = 'DELETE FROM ' . $tables['se'] . ' WHERE id  = '. $key;
				$db->sql_query($sql);
			}
		}

		if ($request->variable('delall', ''))
		{
			$sql = 'DELETE FROM ' . $tables['se'];
			$db->sql_query($sql);
		}

		$sconfig = $request->variable('config', array('' => 0), true);
		if (sizeof($sconfig))
		{
			$sql = 'UPDATE ' . $tables['config'] . ' SET ' . $db->sql_build_array('UPDATE', $sconfig);
			$db->sql_query($sql);
		}

		if (($request->is_set('dell_custom_page') ||  $request->is_set('submit_custom_page')) && ($request->variable('custom_page', '') != '' && $request->variable('custom_value', '') != ''))
		{
			$sql = 'SELECT custom_pages FROM ' . $tables['config'];
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchfield('custom_pages');
			$row = unserialize($row);
			if ($request->is_set('dell_custom_page'))
			{
				unset($row[$request->variable('custom_page', '')]);
			} else
			{
				$row[$request->variable('custom_page', '')] = strtoupper($request->variable('custom_value', ''));
			}
			asort($row);
			$sql = 'UPDATE ' . $tables['config'] . ' SET custom_pages = "' . $db->sql_escape(serialize($row)) . '"';
			$db->sql_query($sql);
		}

		if ($request->is_set('submit_start_screen'))
		{
			$sql = 'UPDATE ' . $tables['config'] . ' SET start_screen = "' . $request->variable('start_screen', 'online') . '", archive=' . $request->variable('archive', 0) . '';
			$db->sql_query($sql);
		}

		if ($request->is_set('botsincbtn'))
		{
			$sql = 'UPDATE ' . $tables['config'] . ' SET botsinc = "' . $request->variable('botsinc', 0) . '"';
			$db->sql_query($sql);
		}

		if ($request->is_set('logbtn'))
		{
			$sql = 'UPDATE ' . $tables['config'] . ' SET log = "' . $request->variable('log', 0) . '"';
			$db->sql_query($sql);
		}

		if ($request->is_set('submit_del_stat'))
		{
			if (!confirm_box(true))
			{
				confirm_box(false, $user->lang('STAT_DELETE_CONFIRM'), build_hidden_fields(array(
					'i'					=> '\forumhulp\statistics\acp\statistics_module',
					'mode'				=> 'stat',
					'screen'			=> 'config',
					'submit_del_stat'	=> true,
				)));
				trigger_error($user->lang('STAT_DELETE_ERROR'));
			}
			else
			{
				$db->sql_query('TRUNCATE TABLE ' . $tables['archive']);

				add_log('admin', 'STAT_DELETE_SUCCESS');
				if ($request->is_ajax())
				{
					trigger_error('STAT_DELETE_SUCCESS');
				} else
				{
					trigger_error('STAT_DELETE_SUCCESS');
				}
			}
		}

		$sql = 'SELECT * FROM ' . $tables['config'];
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);

		for ($i = 0; $i < floor(sizeof($row) / 2) - 3; $i++)
		{
			$template->assign_block_vars('options', array(
				'KEY'			=> strtoupper(key($row)),
				'TITLE'			=> $user->lang[strtoupper(key($row))],
				'S_EXPLAIN'		=> (isset($user->lang[strtoupper(key($row)) . '_EXPLAIN'])) ? true : false,
				'TITLE_EXPLAIN'	=> (isset($user->lang[strtoupper(key($row)) . '_EXPLAIN'])) ? $user->lang[strtoupper(key($row)) . '_EXPLAIN'] : '',
				'CONTENT'		=> '<input type="number" name="config[' . key($row) . ']" id="config_' . key($row) . '" size="3" value="' . $row[key($row)] . '" /> <input name="config[' . str_replace('max_', 't_', key($row)) . ']" size="3" value="' . $row[str_replace('max_', 't_', key($row))] . '" />'
			));
			next($row);
		}

		$modules_ext = unserialize($row['custom_pages']);
		$options = $optionssc = '';
		if (sizeof($modules_ext) > 0)
		{
			foreach($modules_ext as $key => $value)
			{
				$options .= '<option value="' . $key . '">' . $value . '</option>';
			}
		}

		$module_aray = array('countries' => 'COUNTRIES', 'referrals' => 'REFERRALS', 'se' => 'SEARCHENG', 'se_terms' => 'SEARCHTERMS', 'browsers' => 'BROWSERS', 'crawl' => 'CRAWLERS',
							'os' => 'SYSTEMS', 'modules' => 'MODULES', 'stats' => 'AVERAGES', 'screens' => 'RESOLUTIONS', 'top10' => 'OVERVIEW', 'users' => 'USERS',
							'ustats' => 'USERSTATS', 'default' => 'LASTVISITS');
		foreach($module_aray as $key => $value)
		{
			$selected = ($key == $row['start_screen']) ? ' selected="selected"' : '';
			$optionssc .= '<option value="' . $key . '"' . $selected . '>' . $user->lang[$value] . '</option>';
		}

		$template->assign_vars(array(
			'OPTIONLIST'		=> $options,
			'OPTIONLISTSC'		=> $optionssc,
			'ARCHIVE'			=> ($row['archive']) ? ' checked= "checked"' : '',
			'BOTS_INC_ENABLED'	=> ($row['botsinc']) ? true : false,
			'LOG_ENABLED'		=> ($row['log']) ? true : false,
			'U_ACTION'			=> $uaction . '&amp;screen=config',
			'SUB_DISPLAY'		=> 'config'
		));
	}

	public static function nyi($start = 0, $uaction = '')
	{
		global $db, $config, $user, $tables, $request, $template, $phpbb_container;

		$template->assign_vars(array(
			'U_ACTION'			=> $uaction,
			'SUB_DISPLAY'		=> 'nyi'
		));
	}
}
