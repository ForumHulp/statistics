<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\statistics\cron\task\core;

if (!class_exists('find_os'))
{
	include('find_os.' . $this->php_ext);
}
use find_os;

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

class delete_statistics extends \phpbb\cron\task\base
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
		global $phpbb_container, $info;

		$os = new find_os();
		$module_aray = $browser_aray = $os_aray = $country_aray = $user_aray = $screen_aray = $referer_aray = $search_aray = array();
		$sql = 'SELECT time, uname, agent, ip_addr, module, host, domain, scr_res, page, referer, se_terms FROM ' . $this->online_table . ' ORDER BY id ASC';
		$result = $this->db->sql_query($sql);
		$starttime = explode(' ', microtime());
		$starttime = $starttime[1] + $starttime[0];
		$row_count = $start_day = 0;
		while (still_on_time() && $row = $this->db->sql_fetchrow($result))
		{
			$module_aray	= ($row['module'] != '') ? $this->count_array($module_aray, $row['module']) : null;

			$os->setUserAgent($row['agent']);
			$browser_aray	= ($row['agent'] != '') ? $this->count_array($browser_aray, $os->getBrowser() . ' ' . $os->getVersion()) : null;
			$os_aray		= ($row['agent'] != '') ? $this->count_array($os_aray, $os->getPlatform()) : null;

			$country_aray	= ($row['domain'] != '') ? $this->count_array($country_aray, $row['domain']) : null;
			$user_aray		= ($row['uname'] != '') ? $this->count_array($user_aray, $row['uname']) : null;
			$screen_aray	= ($row['scr_res'] != '') ? $this->count_array($screen_aray, $row['scr_res']) : null;
			$referer_aray	= ($row['referer'] != '') ? $this->count_array($referer_aray, $this->url_to_domain($row['referer'])) : null;
			$search_aray	= ($row['se_terms'] != '') ? $this->split_array($search_aray, $row['se_terms']) : null;
			$row_count++;
			$start_day = (!$start_day) ? $row['time'] : $start_day;
		}
		$this->db->sql_freeresult($result);

		$this->store($module_aray, 1);
		$this->store($browser_aray, 2);
		$this->store($os_aray, 3);
		$this->store($country_aray, 4);
		$this->store($user_aray, 5);
		$this->store($screen_aray, 6);
		$this->store($referer_aray, 7);
		$this->store($search_aray, 8);

		unset($module_aray, $browser_aray, $os_aray, $country_aray, $user_aray, $screen_aray, $referer_aray, $search_aray);
		$sql = 'TRUNCATE TABLE ' . $this->online_table;
		$this->db->sql_query($sql);
		$sql = 'OPTIMIZE TABLE ' . $this->online_table;
		$this->db->sql_query($sql);

		$sql_ary = array(
			'year'		=> date('Y', $start_day),
			'month'		=> date('n', $start_day),
			'day'		=> date('j', $start_day),
			'hits'		=> $row_count,
		);

		$sql = 'INSERT INTO ' . $this->stats_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary) . ' ON DUPLICATE KEY UPDATE hits = hits + ' . $sql_ary['hits'];

		$this->db->sql_query($sql);
		$mtime = explode(' ', microtime());
		$totaltime = $mtime[0] + $mtime[1] - $starttime;
		$rows_per_second = $row_count / $totaltime;

		add_log('admin', 'LOG_STATISTICS_PRUNED', $totaltime, $rows_per_second);
		$this->config->set('delete_statistics_last_gc', strtotime('midnight', time()));
	}

	// Store Archive
	public function store($aray, $cat)
	{
		global $phpbb_container;

		if (sizeof($aray))
		{
			$this->archive_table	= $phpbb_container->getParameter('tables.archive_table');

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

				$sql = 'SELECT hits, last FROM ' . $this->archive_table . ' WHERE cat = ' . $cat . ' ORDER BY hits DESC LIMIT '. $sconfig[$cat] .', 1';
				$result = $this->db->sql_query($sql);
				$prune = $this->db->sql_fetchrow($result);
				if ($prune)
				{
					$sql = 'DELETE FROM ' . $this->archive_table . ' WHERE cat = ' . $cat . ' AND hits < ' . $prune['hits'] . ' AND last < ' . $prune['last'];
					$this->db->sql_query($sql);
				}
			}
			$sql = 'OPTIMIZE TABLE ' . $this->archive_table;
			$this->db->sql_query($sql);
		}
	}

	public function get_config()
	{
		global $db, $phpbb_container;

		$sql = 'SELECT * FROM ' . $this->config_table;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
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
		foreach($words as $word)
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
		return $this->config['delete_statistics_last_gc'] < (time() - $this->config['delete_statistics_gc']) && (date('Y-m-d', time()) != date('Y-m-d', $this->config['delete_statistics_last_gc']));
	}
}
