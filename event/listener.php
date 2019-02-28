<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license Proprietary
*
*/

namespace forumhulp\statistics\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use phpbb\config\config;
use phpbb\request\request;
use phpbb\user;
use phpbb\db\driver\driver_interface;
use phpbb\template\template;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	protected $config;
	protected $request;
	protected $user;
	protected $db;
	protected $template;
	protected $cache;
	protected $container;
	protected $online_table;
	protected $config_table;
	protected $se_table;
	protected $php_ext;

	/**
	* Constructor
	*
	*/
	public function __construct(config $config, request $request, user $user, driver_interface $db, template $template, \phpbb\cache\driver\driver_interface $cache, ContainerInterface $container, $online_table, $config_table, $se_table, $php_ext)
	{
		$this->config		= $config;
		$this->request		= $request;
		$this->user			= $user;
		$this->db			= $db;
		$this->template		= $template;
		$this->cache		= $cache;
		$this->container	= $container;
		$this->online_table = $online_table;
		$this->config_table = $config_table;
		$this->se_table		= $se_table;
		$this->php_ext		= $php_ext;
		
		$this->modules		=  $this->get_modules();
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.page_footer'	=> 'get_ref',
		);
	}

	function get_ref($event)
	{
		if ($this->user->page['script_path'] != '/adm/')
		{
			$ref_url = strtolower($this->user->referer);

			if (!empty($ref_url) && (strpos($ref_url, $this->config['server_name']) === false))
			{
				if (strpos($ref_url, 'sid=') !== false)
				{
					$ref_url = preg_replace('/(\?)?(&amp;|&)?sid=[a-z0-9]+/', '', $ref_url);
					$ref_url = preg_replace("/$this->php_ext(&amp;|&)+?/", "$this->php_ext?", $ref_url);
				}

				$data['referer'] = parse_url(htmlspecialchars(strip_tags($ref_url)));

				$sql = 'SELECT DISTINCT query FROM ' . $this->se_table . ' WHERE name like "%' . substr($data['referer']['host'], 0, strrpos($data['referer']['host'], '.')) . '"';
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);

				if ($row && strpos($data['referer']['query'], $row['query'] . '=') !== false)
				{
					parse_str($data['referer']['query'], $query);
					$searchwords = trim($query[$row['query']]);
					$searchwords = str_replace(array('%3d','%27'), array('',''), $searchwords);
					$searchwords = str_replace(array('=','\''), array('',''), $searchwords);
					$searchwords = urldecode($searchwords);
					$searchwords = str_replace(',', ' ', $searchwords);
					$searchwords = str_replace('+', ' ', $searchwords);
				}
			} else
			{
				$ref_url = '';
			}

			if ($this->request->is_set('i'))
			{
				if (is_numeric($this->request->variable('i', '')))
				{
					$data['module'] = $this->request->variable('i', 0);
				} else
				{
					$data['module'] = ltrim(strtoupper($this->request->variable('i', '')), '-');

					if ($this->request->is_set('mode'))
					{
						$data['module'] = (isset($this->modules[substr($data['module'], 0, 3) . '_' . strtoupper($this->request->variable('mode', ''))]) ?
											substr($data['module'], 0, 3) . '_' . strtoupper($this->request->variable('mode', '')) : 
											(isset($this->modules[$data['module']]) ? $this->modules[$data['module']] : 
											$data['module'] . '_' . strtoupper($this->request->variable('mode', ''))));
					}
				}
			} else if (strpos($this->user->page['page_name'], 'app.php') !== false)
			{
				$path_parts = pathinfo($this->user->page['page_name']);
				$path_parts['dirname'] = strtok(strtolower(str_replace('app.php/', '', $path_parts['dirname'])), '/');
				$data['module'] = (isset($this->modules[$path_parts['dirname']])) ? $this->modules[$path_parts['dirname']] : null;
				if ($this->container->get('ext.manager')->is_enabled('forumhulp/portal'))
				{
					$data['module'] = (isset($this->modules[$path_parts['filename']])) ? $this->modules[$path_parts['filename']] : null;
				}
			} else if (!$this->user->page['forum'])
			{
				$data['module'] = $this->modules[$this->user->page['page_name']];
			} else
			{
				$data['module'] = $this->user->page['forum'];
			}

			if (isset($data['module']) && $data['module'] !== '')
			{
				if (($this->ip_cache = $this->cache->get('_ip_cache')) !== false)
				{
					if (!isset($this->ip_cache[$this->user->data['session_ip']]))
					{
						if ($this->user->data['session_ip'] != '127.0.0.1' && !$x = @fsockopen('www.ip-api.com', 80, $errno, $errstr, 10))
						{
							$ip_query = file_get_contents('http://ip-api.com/json/' . $this->user->data['session_ip'] . '?fields=status,countryCode,reverse');
							$ip_aray = json_decode($ip_query, true);

							$this->ip_cache[$this->user->data['session_ip']]['countryCode'] = strtolower($ip_aray['countryCode']);
							$this->ip_cache[$this->user->data['session_ip']]['reverse'] = strtolower($ip_aray['reverse']);
							$this->ip_cache = array_slice($this->ip_cache, -50, 50, true);
							$this->cache->put('_ip_cache', $this->ip_cache);
						} else
						{
							$this->ip_cache[$this->user->data['session_ip']]['reverse'] = @gethostbyaddr(($this->user->data['session_ip']));
							$aray = explode('.', $this->ip_cache[$this->user->data['session_ip']]['reverse']);
							$this->ip_cache[$this->user->data['session_ip']]['countryCode'] = ($this->user->data['session_ip'] == '127.0.0.1') ? 'lo' : strtolower($aray[sizeof($aray) -1]);
						}
					}
				} else
				{
					if ($this->user->data['session_ip'] != '127.0.0.1' && fsockopen('www.ip-api.com', 80))
					{
						$ip_query = file_get_contents('http://ip-api.com/json/' . $this->user->data['session_ip'] . '?fields=status,countryCode,reverse');
						$ip_aray = json_decode($ip_query, true);

						$this->ip_cache[$this->user->data['session_ip']]['countryCode'] = strtolower($ip_aray['countryCode']);
						$this->ip_cache[$this->user->data['session_ip']]['reverse'] = strtolower($ip_aray['reverse']);

						$this->cache->put('_ip_cache', $this->ip_cache);
					} else
					{
						$this->ip_cache[$this->user->data['session_ip']]['reverse'] = @gethostbyaddr(($this->user->data['session_ip']));
						$aray = explode('.', $this->ip_cache[$this->user->data['session_ip']]['reverse']);
						$this->ip_cache[$this->user->data['session_ip']]['countryCode'] = ($this->user->data['session_ip'] == '127.0.0.1') ? 'lo' : strtolower($aray[sizeof($aray) -1]);
					}
				}

				$data['host'] = $this->ip_cache[$this->user->data['session_ip']]['reverse'];
				$data['domain'] = strtolower($this->ip_cache[$this->user->data['session_ip']]['countryCode']);

				$data['domain'] = (!file_exists('./ext/forumhulp/statistics//adm/style/images/flags/' . $data['domain'] . '.png')) ? 'un' : $data['domain'];

				if (!$this->request->is_set($this->config['cookie_name'] . '_statistics_res', \phpbb\request\request_interface::COOKIE))
				{
					$this->template->assign_vars(array('ACOOKIE' => true, 'COOKIENAME' => $this->config['cookie_name']));
					$data['screen_res'] = '1920x1080x24';
				} else
				{
					$data['screen_res'] = $this->request->variable($this->config['cookie_name'] . '_statistics_res', '1920x1080x24', false, \phpbb\request\request_interface::COOKIE);
				}

				$fields = array(
					'time'			=> time(),
					'uname'			=> $this->user->data['username'],
					'ugroup'		=> (int) $this->user->data['group_id'],
					'agent'			=> $this->user->browser,
					'ip_addr'		=> $this->user->data['session_ip'],
					'host'			=> $data['host'],
					'domain'		=> $data['domain'],
					'module'		=> $data['module'],
					'scr_res'		=> $data['screen_res'],
					'referer'		=> isset($ref_url) ? $ref_url : '',
					'page'			=> $this->user->page['page'],
					'se_terms'		=> isset($searchwords) ? $searchwords : '',
				);
				$sql = 'INSERT INTO ' . $this->online_table . ' ' . $this->db->sql_build_array('INSERT', $fields);
				$this->db->sql_query($sql);
			}
		}
	}

	/**
	 * @return modules
	 * @access public
	 */
	public function get_modules()
	{
		if (($this->modules = $this->cache->get('_modules_stats')) === false)
		{
			$this->modules = ['index.php' => 'FORUM_INDEX', 'help' => 'VIEWING_FAQ', 'mcp.php' => 'VIEWING_MCP', 'search.php' => 'SEARCHING_FORUMS',
							  'viewonline.php' => 'VIEWING_ONLINE', 'memberlist.php' => 'VIEWING_MEMBERS', 'ucp.php' => 'VIEWING_UCP'];
			
			if (defined('FORUMS_TABLE'))
			{
				$sql = 'SELECT forum_id, forum_name FROM ' . FORUMS_TABLE;
				$result = $this->db->sql_query($sql);
				while ($row = $this->db->sql_fetchrow($result))
				{
					$this->modules[$row['forum_id']] = $row['forum_name'];
				}
			}

			if ($this->container->get('ext.manager')->is_enabled('forumhulp/portal'))
			{
				$sql = 'SELECT a.route, c.title FROM ' . $this->container->getParameter('forumhulp.tables.articles') . ' a 
						LEFT JOIN ' . $this->container->getParameter('forumhulp.tables.categories') . ' c ON c.cid = a.cid';
				$result = $this->db->sql_query($sql);
				while ($row = $this->db->sql_fetchrow($result))
				{
					$this->modules[$row['route']] = $row['title'];			
				}
			}
			
			$sql = 'SELECT module_id, module_basename, module_langname FROM ' . MODULES_TABLE . " WHERE module_class IN ('ucp', 'mcp')";
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				if (strpos($row['module_basename'], '\\') !== false)
				{
					$this->modules[strtoupper(ltrim(str_replace('\\', '-', $row['module_basename']), '-'))] = $row['module_langname'];
				}
				$this->modules[$row['module_langname']] = $row['module_langname'];
				$this->modules[$row['module_id']] = $row['module_langname'];
			}
			
			$sql = 'SELECT custom_pages FROM ' . $this->config_table;
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchfield('custom_pages');
			$row = unserialize($row);
	
			if (sizeof($row) > 0)
			{
				foreach ($row as $key => $value)
				{
					$this->modules[$key] = $value;
				}
			}
			$this->cache->put('_modules_stats', $this->modules, 3600);
		}
		return $this->modules;
	}
}
