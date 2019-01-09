<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license Proprietary
*
*/

namespace forumhulp\statistics\cron\task\core;

/**
* @ignore
*/

class prune_statistics extends \phpbb\cron\task\base
{
	protected $phpbb_root_path;
	protected $php_ext;
	protected $config;
	protected $db;
	protected $online_table;
	protected $config_table;
	protected $archive_table;
	protected $stats_table;

	/**
	* Constructor.
	*
	* @param string $phpbb_root_path The root path
	* @param string $php_ext The PHP extension
	* @param phpbb_config $config The config
	* @param phpbb_db_driver $db The db connection
	*/
	public function __construct($phpbb_root_path, $php_ext, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, $online_table, $config_table, $archive_table, $stats_table)
	{
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->config = $config;
		$this->db = $db;
		$this->online_table = $online_table;
		$this->config_table = $config_table;
		$this->archive_table = $archive_table;
		$this->stats_table = $stats_table;
	}

	/**
	* Runs this cron task.
	*
	* @return null
	*/
	public function run()
	{
		$sql = 'SELECT log FROM ' . $this->config_table;
		$result = $this->db->sql_query($sql);
		$sconfig['log'] = $this->db->sql_fetchfield('log');

		$unique_aray = $module_aray = $browser_aray = $os_aray = $country_aray = $user_aray = $screen_aray = $referer_aray = $search_aray = $group_aray	= array();

		$sql = 'SELECT MIN(time) AS start_time FROM ' . $this->online_table;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$start_time = ($row && $row['start_time']) ?  $row['start_time'] : 0;

		if ($start_time)
		{
			$sql = 'SELECT time, uname, ugroup, agent, ip_addr, module, host, domain, scr_res, page, referer, se_terms FROM ' . $this->online_table . ' 
					WHERE time BETWEEN ' . $start_time . ' AND ' . strtotime("24:00", $start_time) . ' ORDER BY id ASC';
			$result = $this->db->sql_query($sql);
			$starttime = explode(' ', microtime());
			$starttime = $starttime[1] + $starttime[0];
			$row_count = 0;
			include($this->phpbb_root_path . 'ext/forumhulp/statistics/vendor/find_os.' . $this->php_ext);
			while (still_on_time() && $row = $this->db->sql_fetchrow($result))
			{
				$module_aray	= ($row['module'] != '') ? $this->count_array($module_aray, $row['module']) : null;
				$os = new \find_os();

				$os->setUserAgent($row['agent']);
				$browser_aray	= ($row['agent'] != '') ? $this->count_array($browser_aray, $os->getBrowser() . ' ' . $os->getVersion()) : null;
				$os_aray		= ($row['agent'] != '') ? $this->count_array($os_aray, $os->getPlatform()) : null;

				$country_aray	= ($row['domain'] != '') ? $this->count_array($country_aray, $row['domain']) : null;
				$user_aray		= ($row['uname'] != '') ? $this->count_array($user_aray, $row['uname']) : null;
				$screen_aray	= ($row['scr_res'] != '') ? $this->count_array($screen_aray, $row['scr_res']) : null;
				$referer_aray	= ($row['referer'] != '') ? $this->count_array($referer_aray, $this->url_to_domain($row['referer'])) : null;
				$search_aray	= ($row['se_terms'] != '') ? $this->split_array($search_aray, $row['se_terms']) : null;
				$unique_aray	= ($row['ip_addr'] != '') ? $this->count_array($unique_aray, $row['ip_addr']) : null;
				$group_aray		= ($row['ugroup'] != '') ? $this->count_array($group_aray, $row['ugroup']) : null;
				$row_count++;
			}
			$this->db->sql_freeresult($result);

			$unique_visiors = array($start_time => count($unique_aray));

			$this->store($module_aray, 1);
			$this->store($browser_aray, 2);
			$this->store($os_aray, 3);
			$this->store($country_aray, 4);
			$this->store($user_aray, 5);
			$this->store($screen_aray, 6);
			$this->store($referer_aray, 7);
			$this->store($search_aray, 8);
			$this->store_unique_visitors($unique_visiors, 9);
			$this->store($group_aray, 10);

			$sql = 'OPTIMIZE TABLE ' . $this->archive_table;
			$this->db->sql_query($sql);

			unset($module_aray, $browser_aray, $os_aray, $country_aray, $user_aray, $screen_aray, $referer_aray, $search_aray, $unique_visiors, $unique_aray);
			$sql = 'DELETE FROM ' . $this->online_table . ' WHERE time BETWEEN ' . $start_time . ' AND ' . strtotime("24:00", $start_time);
			$this->db->sql_query($sql);
			$sql = 'OPTIMIZE TABLE ' . $this->online_table;
			$this->db->sql_query($sql);

			$sql_ary = array(
				'year'		=> date('Y', $start_time),
				'month'		=> date('n', $start_time),
				'day'		=> date('j', $start_time),
				'hits'		=> $row_count,
			);

			$sql = 'INSERT INTO ' . $this->stats_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary) . ' ON DUPLICATE KEY UPDATE hits = hits + ' . $sql_ary['hits'];

			$this->db->sql_query($sql);
			$mtime = explode(' ', microtime());
			$totaltime = $mtime[0] + $mtime[1] - $starttime;
			$rows_per_second = $row_count / $totaltime;

			($sconfig['log']) ? add_log('admin', 'LOG_STATISTICS_PRUNED', $totaltime, $rows_per_second) : null;
		} else
		{
			($sconfig['log']) ? add_log('admin', 'LOG_STATISTICS_NO_PRUNE') : null;
		}

		$sql = 'SELECT MIN(time) AS start_time FROM ' . $this->online_table;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		if ($row)
		{
			$newtime = ((mktime(0, 0, 0) - $row['start_time']) >= 0) ? time() + 3600 : mktime(0, 0, 0);
		} else
		{
			$newtime = mktime(0, 0, 0);
		}
		$this->config->set('prune_statistics_last_gc', $newtime);
	}

	// Store Archive
	public function store($aray, $cat)
	{
		if (is_array($aray) && sizeof($aray))
		{
			$sconfig = $this->get_config();

			foreach ($aray as $key => $value)
			{
				$sql = 'SELECT COUNT(name) AS counter FROM ' . $this->archive_table . ' WHERE cat = ' . $cat . ' AND name = "' . $key . '"';
				$result = $this->db->sql_query($sql);
				$counter = (int) $this->db->sql_fetchfield('counter');
				if ($counter == 0)
				{
					$sql_ary = array(
						'cat'		=> $cat,
						'name'		=> $key,
						'hits'		=> $value,
						'first'		=> time(),
						'last'		=> time(),
					);

					$sql = 'INSERT INTO ' . $this->archive_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary);
				} else
				{
					$sql = 'UPDATE ' . $this->archive_table . ' SET hits = hits + ' . $value . ', last = ' . time() .' WHERE cat = ' . $cat . ' AND name = "' . $key . '"';
				}
				$this->db->sql_query($sql);
			}

			$sql = 'SELECT hits, last FROM ' . $this->archive_table . ' WHERE cat = ' . $cat . ' ORDER BY hits DESC LIMIT '. $sconfig[$cat] .', 1';
			$result = $this->db->sql_query($sql);
			$prune = $this->db->sql_fetchrow($result);
			if ($prune)
			{
				$sql = 'DELETE FROM ' . $this->archive_table . ' WHERE cat = ' . $cat . ' AND hits < ' . $prune['hits'] . ' AND last < ' . $prune['last'];
				$this->db->sql_query($sql);
			}
		}
	}

	public function store_unique_visitors($array, $cat)
	{
		$sql = 'SELECT id FROM ' . $this->archive_table . ' WHERE cat = ' . $cat . ' AND hits = ' . current($array);
		$result = $this->db->sql_query($sql);
		$id = $this->db->sql_fetchfield('id');

		if ($id)
		{
			$sql = 'UPDATE ' . $this->archive_table . ' SET hits = ' . current($array) . ', name = ' . key($array) . ', last = ' . key($array)  . '  WHERE id = ' . $id;
			$this->db->sql_query($sql);
		} else
		{
			$sql = 'INSERT INTO ' . $this->archive_table . ' (cat, name, hits, first, last) 
					VALUES(' . $cat . ', ' . key($array)  . ', ' . current($array) . ', ' . key($array) . ', ' . key($array) . ')';
			$this->db->sql_query($sql);
		}

		$sql = 'DELETE FROM ' . $this->archive_table . ' WHERE cat = ' . $cat . ' AND id IN 
				(select id from (select id FROM ' . $this->archive_table . ' WHERE cat = ' . $cat . ' ORDER BY hits DESC LIMIT 10, 1000) x)';
		$this->db->sql_query($sql);
	}

	public function get_config()
	{
		$sql = 'SELECT * FROM ' . $this->config_table;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$sconfig = array();
		foreach ($row as $key => $value)
		{
			if (strpos($key, 't_') !== false)
			{
				$sconfig[] = $value;
			}
		}
		return $sconfig;
	}

	public function count_array($aray, $row1)
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

	public function split_array($aray, $row2)
	{
		$words = explode(' ', $row2);
		foreach ($words as $word)
		{
			$aray = $this->count_array($aray, $word);
		}
		return $aray;
	}

	public function url_to_domain($url)
	{
		$host = @parse_url($url, PHP_URL_HOST);
		if (!$host)
		{
			$host = $url;
		}
		if (substr($host, 0, 4) == 'www.')
		{
			$host = substr($host, 4);
		}
		return $host;
	}

	/**
	* Returns whether this cron task can run, given current board configuration.
	*
	* @return bool
	*/
	public function is_runnable()
	{
		return true;
	}

	/**
	* Returns whether this cron task should run now, because enough time
	* has passed since it was last run.
	*
	* @return bool
	*/
	public function should_run()
	{
		return $this->config['prune_statistics_last_gc'] < (time() - $this->config['prune_statistics_gc']) && (date('Y-m-d', time()) != date('Y-m-d', $this->config['prune_statistics_last_gc']));
	}
}
