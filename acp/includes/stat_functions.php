<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
if (!class_exists('find_os'))
{
	include('find_os.' . $this->php_ext);
}

class stat_functions
{

	/**
	* get_config data
	*/
	public static function get_config()
	{
		global $db, $config, $tables, $phpbb_container;
	
		$sql = 'SELECT * FROM ' . $tables['config'];
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		
		foreach ($row as $key => $value)
		{
			$config['statistics_' . $key] = $value;
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
		if (!$found) $aray[$row1] = 1;
		return $aray;
	}
	
	public static function get_modules()
	{
		global $db, $user;
		$user->add_lang(array('ucp', 'mcp', 'common'));
		$modules = array();
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
			(isset($user->lang[$row['module_langname']])) ? $modules[$row['module_langname']] = $user->lang[$row['module_langname']] : NULL;
		}
		
		$module_pages = array('FORUM_INDEX' => $user->lang['FORUM_INDEX'], 'VIEWING_FAQ' => $user->lang['VIEWING_FAQ'], 'VIEWING_MCP' => $user->lang['VIEWING_MCP'], 
							  'SEARCHING_FORUMS' => $user->lang['SEARCHING_FORUMS'], 'VIEWING_ONLINE' => $user->lang['VIEWING_ONLINE'], 'VIEWING_MEMBERS' => $user->lang['VIEWING_MEMBERS'], 
							  'VIEWING_UCP' => $user->lang['VIEWING_UCP']);
		return array_replace($module_pages, $modules);
	}

	public static function online($start = 0, $uaction = '')
	{
		global $db, $config, $user, $tables, $request, $template, $phpbb_container;

		$modules = self::get_modules();

		// sort keys, direction en sql
		$sort_key	= $request->variable('sk', 't');
		$sort_dir	= $request->variable('sd', 'd');
		$sort_by_sql = array('t' => 'time', 'u' => 'uname', 'm' => 'module', 'd' => 'domain', 'h' => 'host');
		$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

		$template->assign_vars(array(
			'U_ACTION'			=> $uaction,
			'S_SORT_KEY'		=> $sort_key,
			'S_SORT_DIR'		=> $sort_dir,
			'VIEW_TABLE' 		=> $request->variable('table', false),
			'SUB_DISPLAY'		=> 'online'
		));

		$sql = 'SELECT COUNT(id) AS total_entries FROM ' . $tables['online'];
		$result = $db->sql_query($sql);
		$total_entries = (int) $db->sql_fetchfield('total_entries');
		$db->sql_freeresult($result);
		
		$pagination = $phpbb_container->get('pagination');
		$base_url = $uaction . '&amp;screen=online&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir;
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $config['statistics_max_online'], $start);

		$sql = 'SELECT o.time, o.uname, o.agent, o.ip_addr, o.host, o.domain, d.description, o.module, o.page, o.referer FROM ' . $tables['online'] . ' o
				LEFT JOIN ' . $tables['domain'] . ' d ON (d.domain = o.domain) 
				ORDER BY o.' . $sql_sort;
		
		$result = $db->sql_query_limit($sql, $config['statistics_max_online'], $start);
		$counter = 0;
		while ($row = $db->sql_fetchrow($result))
		{
			$counter += 1;
			$template->assign_block_vars('onlinerow', array(
				'COUNTER'   => $start + $counter,
				'TIJD'		=> $user->format_date($row['time'], 'H:i'),
				'FLAG'		=> ($row['uname'] != 'Anonymous') ? 'online-user.gif' : 'offline-user.gif',
				'UNAME'		=> $row['uname'],
				'AGENT'		=> $row['agent'],
				'MODULE'	=> isset($modules[$row['module']]) ? $modules[$row['module']] : '',
				'MODULEURL'	=> '/' . $row['page'],
				'DFLAG'		=> $row['domain'].'.gif',
				'DDESC'		=> $row['description'],
				'HOST'		=> $row['host'],
				'IP'		=> $row['ip_addr']
				)
			);
		}
	}

	public static function browsers($start = 0, $uaction = '', $overall = 0)
	{
		global $db, $config, $user, $tables, $request, $template, $phpbb_container;

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
			'OVERALL'			=> ($overall) ? '<a href="">Today</a>' : '<a href="">Overall</a>'
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
		$result = ($overall) ? $db->sql_query_limit($sql, $config['statistics_max_browsers'], $start) :  $db->sql_query($sql);
		$counter = 0; $graphstr = '';
		if ($overall)
		{
			while ($row = $db->sql_fetchrow($result))
			{
				$counter += 1;
				$template->assign_block_vars('onlinerow', array(
					'COUNTER'   	=> $start + $counter,
					'NAME'			=> $row['name'],
					'MODULECOUNT'	=> $row['total_per_domain'],
					'MODULETOTAL'	=> round((($row['total_per_domain'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_domain'] . ' of ' . $row['total'] . ')'
					)
				);
				$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($row['name']) . '\', ' . $row['total_per_domain'] . ']';
			}
		} else
		{
			$browser = new find_os();
			$browser_aray = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$browser->setUserAgent($row['agent']);
				$browser_aray = ($row['agent'] != '') ? self::count_array($browser_aray, $browser->getBrowser() . ' ' . $browser->getVersion()) : NULL;
			}
		
			$total_entries = sizeof($browser_aray);
	
			if ($sort_key == 'd')
			{
				($sort_dir == 'd') ? krsort($browser_aray) : ksort($browser_aray);
			} else
			{
				($sort_dir == 'd') ? arsort($browser_aray) : asort($browser_aray);
			}

			$counter = 0; $graphstr = ''; $row['total'] = array_sum($browser_aray);
			foreach (array_slice($browser_aray, $start,$config['statistics_max_browsers'], true) as $row['Browser'] => $row['total_per_browser'])
			{
				$counter += 1;
				$template->assign_block_vars('onlinerow', array(
					'COUNTER'   	=> $start + $counter,
					'NAME'			=> $row['Browser'],
					'MODULECOUNT'	=> $row['total_per_browser'],
					'MODULETOTAL'	=> round((($row['total_per_browser'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_browser'] . ' of ' . $row['total'] . ')'
					)
				);
				$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($row['Browser']) . '\', ' . $row['total_per_browser'] . ']';
			}
		}

		$pagination = $phpbb_container->get('pagination');
		$base_url = $uaction . '&amp;screen=browsers&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir . (($overall)? '&amp;overall=1' : '');
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $config['statistics_max_browsers'], $start);

		$template->assign_vars(array('ROWSPAN'		=> $total_entries,
									 'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '',$base_url) : $base_url.'&amp;overall=1',
									 'GRAPH' 		=> '[' . $graphstr . ']'));
	}
	
	public static function os($start = 0, $uaction = '', $overall = 0)
	{
		global $db, $config, $user, $tables, $request, $template, $phpbb_container;

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
			'SUBTITLE'			=> $user->lang['BROWSERS'],
			'OVERALL'			=> ($overall) ? '<a href="">Today</a>' : '<a href="">Overall</a>'
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

		$result = ($overall) ? $db->sql_query_limit($sql, $config['statistics_max_os'], $start) : $db->sql_query($sql);
		$counter = 0; $graphstr = '';
		if ($overall)
		{
			while ($row = $db->sql_fetchrow($result))
			{
				$counter += 1;
				$template->assign_block_vars('onlinerow', array(
					'COUNTER'   	=> $start + $counter,
					'NAME'			=> $row['name'],
					'MODULECOUNT'	=> $row['total_per_os'],
					'MODULETOTAL'	=> round((($row['total_per_os'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_os'] . ' of ' . $row['total'] . ')'
					)
				);
				$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($row['name']) . '\', ' . $row['total_per_os'] . ']';
			}
		} else
		{
			$os = new find_os();
			$os_aray = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$os->setUserAgent($row['agent']);
				$os_aray = ($row['agent'] != '') ? self::count_array($os_aray, $os->getPlatform()) : NULL;
			}
		
			$total_entries = sizeof($os_aray);
	
			if ($sort_key == 'd')
			{
				($sort_dir == 'd') ? krsort($os_aray) : ksort($os_aray);
			} else
			{
				($sort_dir == 'd') ? arsort($os_aray) : asort($os_aray);
			}

			$counter = 0; $graphstr = ''; $row['total'] = array_sum($os_aray);
			foreach (array_slice($os_aray, $start, $config['statistics_max_browsers'], true) as $row['Operating System'] => $row['total_per_os'])
			{
				$counter += 1;
				$template->assign_block_vars('onlinerow', array(
					'COUNTER'   	=> $start + $counter,
					'NAME'			=> $row['Operating System'],
					'MODULECOUNT'	=> $row['total_per_os'],
					'MODULETOTAL'	=> round((($row['total_per_os'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_os'] . ' of ' . $row['total'] . ')'
					)
				);
				$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($row['Operating System']) . '\', ' . $row['total_per_os'] . ']';
			}
		}

		$pagination = $phpbb_container->get('pagination');
		$base_url = $uaction . '&amp;screen=os&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir . (($overall)? '&amp;overall=1' : '');
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $config['statistics_max_browsers'], $start);

		$template->assign_vars(array('ROWSPAN'		=> $total_entries,
									 'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '',$base_url) : $base_url.'&amp;overall=1',
									 'GRAPH' 		=> '[' . $graphstr . ']'));
	}

	public static function countries($start = 0, $uaction = '', $overall = 0)
	{
		global $db, $config, $user, $tables, $request, $template, $phpbb_container;

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
			'OVERALL'			=> ($overall) ? '<a href="">Today</a>' : '<a href="">Overall</a>'
		));

		$sql = ($overall) ? 'SELECT COUNT(DISTINCT name) AS total_entries, MIN(first) AS firstdate, MAX(last) AS lastdate FROM ' . $tables['archive'] . ' WHERE cat = 4' : 
							'SELECT COUNT(DISTINCT domain) AS total_entries FROM ' . $tables['online'];;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$total_entries = $row['total_entries'];
		$db->sql_freeresult($result);

		$pagination = $phpbb_container->get('pagination');
		$base_url = $uaction . '&amp;screen=countries&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir . (($overall)? '&amp;overall=1' : '');
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $config['statistics_max_countries'], $start);

		$template->assign_vars(array('ROWSPAN'		=> $total_entries,
									 'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '',$base_url) : $base_url.'&amp;overall=1',
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
		
		$result = $db->sql_query_limit($sql, $config['statistics_max_countries'], $start);
		$counter = 0; $graphstr = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$counter += 1;
			$template->assign_block_vars('onlinerow', array(
				'COUNTER'   	=> $start + $counter,
				'NAME'			=> $row['description'],
				'MODULECOUNT'	=> $row['total_per_domain'],
				'MODULETOTAL'	=> round((($row['total_per_domain'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_domain'] . ' of ' . $row['total'] . ')'
				)
			);
			$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($row['description']) . '\', ' . $row['total_per_domain'] . ']';
		}
		$template->assign_vars(array('GRAPH' => '[' . $graphstr . ']'));
	}

	public static function referrals($start = 0, $uaction = '', $overall = 0 )
	{
		global $db, $config, $user, $tables, $request, $template, $phpbb_container;

		// sort keys, direction en sql
		$sort_key	= $request->variable('sk', 'd');
		$sort_dir	= $request->variable('sd', 'd');
		$sort_by_sql = array('d' => 'domain', 't' => 'total_per_referer', 'p' => 'percent');
		$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

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
		$base_url = $uaction . '&amp;screen=referrals&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir . (($overall)? '&amp;overall=1' : '');
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $config['statistics_max_referer'], $start);

		$template->assign_vars(array('ROWSPAN' 		=> $total_entries,
									 'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '',$base_url) : $base_url.'&amp;overall=1',
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
				
		$result = $db->sql_query_limit($sql, $config['statistics_max_referer'], $start);
		$counter = 0; $graphstr = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$counter += 1;
			$template->assign_block_vars('onlinerow', array(
				'COUNTER'   	=> $start + $counter,
				'NAME'			=> $row['domain'],
				'MODULECOUNT'	=> $row['total_per_referer'],
				'MODULETOTAL'	=> round((($row['total_per_referer'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_referer'] . ' of ' . $row['total'] . ')'
				)
			);
			$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode(substr($row['domain'], 0, 20)) . '\', ' . $row['total_per_referer'] . ']';
		}
		$template->assign_vars(array('GRAPH' => '[' . $graphstr . ']'));
	}

	public static function se($start = 0, $uaction = '', $overall = 0)
	{
		global $db, $config, $user, $tables, $request, $template, $phpbb_container;

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
		$base_url = $uaction . '&amp;screen=se&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir . (($overall)? '&amp;overall=1' : '');
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $config['statistics_max_se'], $start);

		$template->assign_vars(array('ROWSPAN' 		=> $total_entries,
									 'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '',$base_url) : $base_url.'&amp;overall=1',
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

		$result = $db->sql_query_limit($sql, $config['statistics_max_se'], $start);
		$counter = 0; $graphstr = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$counter += 1;
			$template->assign_block_vars('onlinerow', array(
				'COUNTER'   	=> $start + $counter,
				'NAME'			=> $row['referer'],
				'MODULECOUNT'	=> $row['total_per_referer'],
				'MODULETOTAL'	=> round((($row['total_per_referer'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_referer'] . ' of ' . $row['total'] . ')'
				)
			);
			$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($row['referer']) . '\', ' . $row['total_per_referer'] . ']';
		}
		$template->assign_vars(array('GRAPH' => '[' . $graphstr . ']'));
	}

	public static function se_terms($start = 0, $uaction = '', $overall = 0)
	{
		global $db, $config, $user, $tables, $request, $template, $phpbb_container;

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
				$se_terms[$key] = isset($se_terms[$key]) ? $se_terms[$key] + (int) $row['rowtotal'] : (int) $row['rowtotal'];
			}
			($overall) ? $firstdate = $row['firstdate'] : NULL;
			($overall) ? $lastdate = $row['lastdate'] : NULL;
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
		$base_url = $uaction . '&amp;screen=se_terms&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir . (($overall)? '&amp;overall=1' : '');
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $config['statistics_max_se_terms'], $start);

		$template->assign_vars(array('ROWSPAN' 		=> $total_entries,
									 'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '', $base_url) : $base_url . '&amp;overall=1',
									 'OVERALLTXT'	=> ($overall) ? 'Today' : 'Overall',
									 'MINMAXDATE'	=> ($overall && $total_entries) ? '(' .$user->format_date($firstdate, 'd m \'y') . ' - ' . 
									 					$user->format_date($lastdate, 'd m \'y') . ')': '',
									 'OVERALLSORT'	=> ($overall) ? '&amp;overall=1' : ''));


		$counter = 0; $graphstr = ''; $row['total'] = array_sum($se_terms);
		foreach ($se_terms as $row['referer'] => $row['total_per_referer'])
		{
			$counter += 1;
			$template->assign_block_vars('onlinerow', array(
				'COUNTER'   	=> $start + $counter,
				'NAME'			=> $row['referer'],
				'MODULECOUNT'	=> $row['total_per_referer'],
				'MODULETOTAL'	=> round((($row['total_per_referer'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_referer'] . ' of ' . $row['total'] . ')'
				)
			);
			$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($row['referer']) . '\', ' . $row['total_per_referer'] . ']';
		}
		$template->assign_vars(array('GRAPH' => '[' . $graphstr . ']'));
	}
	
	public static function crawl($start = 0, $uaction = '', $overall = 0)
	{
		global $db, $config, $user, $tables, $request, $template, $phpbb_container;

		// sort keys, direction en sql
		$sort_key	= $request->variable('sk', 'd');
		$sort_dir	= $request->variable('sd', 'd');
		$sort_by_sql = array('d' => ($overall) ? 'a.name' : 'a.uname', 't' => 'total_per_users', 'p' => 'percent');
		$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

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
				WHERE a.cat = 5 AND b.bot_name IS not NULL' :
		
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
		$base_url = $uaction . '&amp;screen=crawl&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir;
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $config['statistics_max_crawl'], $start);

		$template->assign_vars(array('ROWSPAN' 		=> $total_entries,
									 'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '', $base_url) : $base_url . '&amp;overall=1',
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
		
		$result = $db->sql_query_limit($sql, $config['statistics_max_crawl'], $start);
		$counter = 0; $graphstr = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$counter += 1;
			$template->assign_block_vars('onlinerow', array(
				'COUNTER'   	=> $start + $counter,
				'NAME'			=> $row['uname'],
				'USER_ID'		=> $row['user_id'],
				'MODULECOUNT'	=> $row['total_per_users'],
				'MODULETOTAL'	=> round((($row['total_per_users'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_users'] . ' of ' . $row['total'] . ')'
				)
			);
			$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($row['uname']) . '\', ' . $row['total_per_users'] . ']';
		}
		$template->assign_vars(array('GRAPH' => '[' . $graphstr . ']'));
	}

	public static function modules($start = 0, $uaction = '', $overall = 0)
	{
		global $db, $config, $user, $tables, $request, $template, $phpbb_container;

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
				'SELECT COUNT(DISTINCT module) AS total_entries FROM ' . $tables['online'];;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$total_entries = $row['total_entries'];
		$db->sql_freeresult($result);

		$pagination = $phpbb_container->get('pagination');
		$base_url = $uaction . '&amp;screen=modules&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir;
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $config['statistics_max_modules'], $start);

		$template->assign_vars(array('ROWSPAN'		=> $total_entries,
									 'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '', $base_url) : $base_url . '&amp;overall=1',
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
		
		$result = $db->sql_query_limit($sql, $config['statistics_max_modules'], $start);
		$counter = 0; $graphstr = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$counter += 1;
			$template->assign_block_vars('onlinerow', array(
				'COUNTER'   	=> $start + $counter,
				'NAME'			=> $modules[$row['module']],
				'MODULECOUNT'	=> $row['total_per_module'],
				'MODULETOTAL'	=> round((($row['total_per_module'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_module'] . ' of ' . $row['total'] . ')'
				)
			);
			$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($modules[$row['module']]) . '\', ' . $row['total_per_module'] . ']';
		}
		$template->assign_vars(array('GRAPH' => '[' . $graphstr . ']'));
	}

	public static function screens($start = 0, $uaction = '', $overall = 0)
	{
		global $db, $config, $user, $tables, $request, $template, $phpbb_container;

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
				'SELECT COUNT(DISTINCT scr_res) AS total_entries FROM ' . $tables['online'];;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$total_entries = $row['total_entries'];
		$db->sql_freeresult($result);

		$pagination = $phpbb_container->get('pagination');
		$base_url = $uaction . '&amp;screen=screens&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir;
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $config['statistics_max_screens'], $start);

		$template->assign_vars(array('ROWSPAN' 		=> $total_entries,
									 'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '', $base_url) : $base_url . '&amp;overall=1',
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
		
		$result = $db->sql_query_limit($sql, $config['statistics_max_screens'], $start);
		$counter = 0; $graphstr = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$counter += 1;
			$template->assign_block_vars('onlinerow', array(
				'COUNTER'   	=> $start + $counter,
				'NAME'			=> $row['scr_res'],
				'MODULECOUNT'	=> $row['total_per_screen'],
				'MODULETOTAL'	=> round((($row['total_per_screen'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_screen'] . ' of ' . $row['total'] . ')'
				)
			);
			$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($row['scr_res']) . '\', ' . $row['total_per_screen'] . ']';
		}
		$template->assign_vars(array('GRAPH' => '[' . $graphstr . ']'));
	}

	public static function users($start = 0, $uaction = '', $overall = 0)
	{
		global $db, $config, $user, $tables, $request, $template, $phpbb_container;

		// sort keys, direction en sql
		$sort_key	= $request->variable('sk', 'd');
		$sort_dir	= $request->variable('sd', 'd');
		$sort_by_sql = array('d' => ($overall) ? 'a.name' : 'a.uname', 't' => 'total_per_users', 'p' => 'percent');
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
		$base_url = $uaction . '&amp;screen=users&amp;sk=' . $sort_key . '&amp;sd=' . $sort_dir;
		$pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_entries, $config['statistics_max_users'], $start);

		$template->assign_vars(array('ROWSPAN' 		=> $total_entries,
									 'OVERALL'		=> ($overall) ? str_replace('&amp;overall=1', '', $base_url) : $base_url . '&amp;overall=1',
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
				WHERE  a.cat = 5 AND b.bot_name IS NULL ORDER BY ' . $sql_sort :
				
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
				WHERE  b.bot_name IS NULL GROUP BY a.uname ORDER BY ' . $sql_sort;
		
		$result = $db->sql_query_limit($sql, $config['statistics_max_users'], $start);
		$counter = 0; $graphstr = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$counter += 1;
			$template->assign_block_vars('onlinerow', array(
				'COUNTER'   	=> $start + $counter,
				'NAME'			=> $row['uname'],
				'USER_ID'		=> $row['user_id'],
				'MODULECOUNT'	=> $row['total_per_users'],
				'MODULETOTAL'	=> round((($row['total_per_users'] / $row['total']) * 100), 1) . ' % (' . $row['total_per_users'] . ' of ' . $row['total'] . ')'
				)
			);
			$graphstr .= (($graphstr == '') ? '' : ', ') . '[\'' . html_entity_decode($row['uname']) . '\', ' . $row['total_per_users'] . ']';
		}
		$template->assign_vars(array('GRAPH' => '[' . $graphstr . ']'));
	}

	public static function config($start = 0, $uaction = '' )
	{
		global $db, $config, $user, $tables, $request, $template, $phpbb_container;

		$sconfig = $request->variable('config', array('' => 0), true);
		if (sizeof($sconfig))
		{
			$sql = 'UPDATE ' . $tables['config'] . ' SET ' . $db->sql_build_array('UPDATE', $sconfig);
			$db->sql_query($sql);
		}
		
		$sql = 'SELECT * FROM ' . $tables['config'];
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);

		for ($i = 0; $i < sizeof($row) / 2; $i++)
		{
			$template->assign_block_vars('options', array(
				'KEY'			=> strtoupper(key($row)),
				'TITLE'			=> $user->lang[strtoupper(key($row))],
				'S_EXPLAIN'		=> (isset($user->lang[strtoupper(key($row)) . '_EXPLAIN'])) ? true : false,
				'TITLE_EXPLAIN'	=> (isset($user->lang[strtoupper(key($row)) . '_EXPLAIN'])) ? $user->lang[strtoupper(key($row)) . '_EXPLAIN'] : '',
				'CONTENT'		=> '<input type="number" name="config[' . key($row) . ']" id="config_' . key($row) . '" size="3" value="' . $row[key($row)] . '" /> 
									<input name="config[' . str_replace('max_', 't_', key($row)) . ']" size="3" value="' . $row[str_replace('max_', 't_', key($row))] . '" />' 
			));
			next($row);
		}

		$template->assign_vars(array(
			'U_ACTION'			=> $uaction . '&amp;screen=config',
			'SUB_DISPLAY'		=> 'config'
		));
	}
	
	public static function nyi($start = 0, $uaction = '' )
	{
		global $db, $config, $user, $tables, $request, $template, $phpbb_container;

		$template->assign_vars(array(
			'U_ACTION'			=> $uaction,
			'SUB_DISPLAY'		=> 'nyi'
		));
	}

}
