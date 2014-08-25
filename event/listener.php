<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\statistics\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	protected $config;
	protected $helper;
	protected $user;
	protected $db;
	protected $online_table;
	protected $config_table;
	protected $se_table;
	protected $php_ext;

    /**
    * Constructor
    *
    * @param \phpbb\controller\helper    $helper        Controller helper object
    */
    public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\user $user, \phpbb\db\driver\driver_interface $db, 
								$online_table, $config_table, $se_table, $php_ext)
    {
        $this->config = $config;
		$this->helper = $helper;
		$this->user = $user;
		$this->db = $db;
		$this->online_table = $online_table;
		$this->config_table = $config_table;
		$this->se_table = $se_table;
		$this->php_ext = $php_ext;
    }

    static public function getSubscribedEvents()
    {
        return array(
			'core.page_footer'					=> 'get_ref',
		);
    }
	
	/**
	 * @param object $event The event object
	 * @return null
	 * @access public
	 */

	public function get_modules()
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
		return $modules;
	}

	function get_ref($event)
	{
		global $request, $template;
	
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

			if ($this->user->page['forum'] && is_numeric($this->user->page['forum']))
			{
				$data['module'] = $this->user->page['forum'];
			} else if ($this->user->page['page_dir'] == '' && !$request->is_set('i'))
			{
				$module_pages = array('index.php' => 'FORUM_INDEX', 'faq.php' => 'VIEWING_FAQ', 'mcp.php' => 'VIEWING_MCP', 'search.php' => 'SEARCHING_FORUMS', 
									  'viewonline.php', 'VIEWING_ONLINE', 'memberlist.php' => 'VIEWING_MEMBERS', 'ucp.php' => 'VIEWING_UCP');
				
				$sql = 'SELECT custom_pages FROM ' . $this->config_table;
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchfield('custom_pages');
				$row = unserialize($row);
				
				if (sizeof($row) > 0)
				{
					foreach($row as $key => $value)
					{
						$module_pages[$key] = $value;
					}
				}
				$data['module'] = (isset($module_pages[$this->user->page['page_name']])) ? $module_pages[$this->user->page['page_name']] : NULL;
				
			} else
			{
				if (is_numeric($request->variable('i', '')))
				{
					$sql = 'SELECT module_langname FROM ' . MODULES_TABLE . ' WHERE module_id = ' . $request->variable('i', 0);
					$result = $this->db->sql_query($sql);
					$module_langname = $this->db->sql_fetchfield('module_langname');
					$data['module'] = $module_langname;
				} else
				{
					$modules = $this->get_modules();
					(in_array(strtoupper($request->variable('i', '')), $modules)) ? $data['module'] = strtoupper($request->variable('i', '')) : NULL;
				}
			}
			
			if (isset($data['module']) && $data['module'] !== '')
			{
				$data['host'] = @gethostbyaddr(($this->user->data['session_ip']));
				$aray = explode('.', $data['host']);
				$data['domain'] = strtolower($aray[sizeof($aray) -1]);
				$data['domain'] = (!file_exists('./ext/forumhulp/statistics//adm/style/images/flags/' . $data['domain'] . '.gif')) ? 'lo' : $data['domain'];

				if (!$request->is_set($this->config['cookie_name'] . '_statistics_res', \phpbb\request\request_interface::COOKIE))
				{
					$template->assign_vars(array('ACOOKIE' => true, 'COOKIENAME' => $this->config['cookie_name']));
					$data['screen_res'] = '1024x768x32';
				} else 
				{
					$data['screen_res'] = $request->variable($this->config['cookie_name'] . '_statistics_res', '1024x768x32', false, \phpbb\request\request_interface::COOKIE);
				}
		
				$fields = array(
					'time'			=> time(),
					'uname'			=> $this->user->data['username'],
					'agent'			=> $this->user->browser,
					'ip_addr'		=> $this->user->data['session_ip'],
					'host'			=> $data['host'],
					'domain'		=> $data['domain'],
					'module'		=> $data['module'],
					'scr_res'		=> $data['screen_res'],
					'referer'		=> isset($ref_url) ? $ref_url: '',
					'page'			=> $this->user->page['page'],
					'se_terms'		=> isset($searchwords) ? $searchwords: '',
				);
				$sql = 'INSERT INTO ' . $this->online_table . ' ' . $this->db->sql_build_array('INSERT', $fields);
				$this->db->sql_query($sql);
			}
		}
	}
}