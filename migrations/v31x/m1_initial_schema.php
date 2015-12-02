<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license Proprietary
*
*/

namespace forumhulp\statistics\migrations\v31x;

/**
 * Migration stage 1: Initial schema
 */
class m1_initial_schema extends \phpbb\db\migration\migration
{
	/**
	 * Assign migration file dependencies for this migration
	 *
	 * @return array Array of migration files
	 * @static
	 * @access public
	 */
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\gold');
	}

	/**
	 * Add the table schema to the database:
	 *
	 * @return array Array of table schema
	 * @access public
	 */
	public function update_schema()
	{
		return array(
			// We have to create our own tables
			'add_tables'	=> array(
				$this->table_prefix . 'statistics_config'	=> array(
					'COLUMNS'			=> array(
						'max_modules'		=> array('UINT:4', 20),
						'max_browsers'		=> array('UINT:4', 20),
						'max_os'			=> array('UINT:4', 20),
						'max_countries'		=> array('UINT:4', 20),
						'max_users'			=> array('UINT:4', 20),
						'max_screens'		=> array('UINT:4', 20),
						'max_referer'		=> array('UINT:4', 20),
						'max_se_terms'		=> array('UINT:4', 20),
						'max_se'			=> array('UINT:4', 20),
						'max_crawl'			=> array('UINT:4', 20),
						'max_online'		=> array('UINT:4', 20),

						't_modules'			=> array('UINT:4', 100),
						't_browsers'		=> array('UINT:4', 100),
						't_os'				=> array('UINT:4', 100),
						't_countries'		=> array('UINT:4', 100),
						't_users'			=> array('UINT:4', 100),
						't_screens'			=> array('UINT:4', 100),
						't_referer'			=> array('UINT:4', 100),
						't_se_terms'		=> array('UINT:4', 100),
						't_se'				=> array('UINT:4', 100),
						't_crawl'			=> array('UINT:4', 100),
						't_online'			=> array('UINT:4', 100),
						'custom_pages'		=> array('MTEXT_UNI', ''),
						'start_screen'		=> array('VCHAR:25', 'online'),
						'archive'			=> array('UINT:4', 0),
						'botsinc'			=> array('UINT:4', 0),
						'log'				=> array('UINT:4', 0),
					),
				),
				$this->table_prefix . 'statistics_domains'	=> array(
					'COLUMNS'			=> array(
						'id'			=> array('UINT', null, 'auto_increment'),
						'domain'		=> array('VCHAR:20', ''),
						'description'	=> array('VCHAR:50', ''),
					),
					'PRIMARY_KEY'		=> 'id',
					'KEYS'				=> array(
						'domain'		=> array('INDEX', 'domain'),
					)
				),
				$this->table_prefix . 'statistics_se'	=> array(
					'COLUMNS'			=> array(
						'id'			=> array('UINT', null, 'auto_increment'),
						'name'			=> array('VCHAR:25', ''),
						'query'			=> array('VCHAR:255', ''),
					),
					'PRIMARY_KEY'		=> 'id',
					'KEYS'				=> array(
						'name'		=> array('INDEX', 'name'),
					)
				),
				$this->table_prefix . 'statistics_online'	=> array(
					'COLUMNS'			=> array(
						'id'			=> array('UINT', null, 'auto_increment'),
						'time'			=> array('TIMESTAMP', 0),
						'uname' 		=> array('VCHAR:25', ''),
						'ugroup'		=> array('UINT:11', 0),
						'agent' 		=> array('VCHAR:255', ''),
						'ip_addr' 		=> array('VCHAR:50', ''),
						'host' 			=> array('VCHAR:100', ''),
						'domain'		=> array('VCHAR:20', ''),
						'module'		=> array('VCHAR:50', ''),
						'scr_res'		=> array('VCHAR:25', ''),
						'page'			=> array('VCHAR:500', ''),
						'referer'		=> array('TEXT', ''),
						'se_terms' 		=> array('VCHAR:500', ''),
					),
					'PRIMARY_KEY'		=> 'id',
					'KEYS'          	=> array(
						'time'			=> array('INDEX', 'time'),
					)
				),
				$this->table_prefix . 'statistics_archive'	=> array(
					'COLUMNS'			=> array(
						'id'			=> array('UINT', null, 'auto_increment'),
						'cat'			=> array('UINT:4', 0),
						'name'			=> array('TEXT', ''),
						'hits'			=> array('UINT:8', 0),
						'first'			=> array('UINT:11', 0),
						'last'			=> array('UINT:11', 0),
					),
					'PRIMARY_KEY'		=> 'id',
					'KEYS'				=> array(
						'cat'		=> array('INDEX', 'cat'),
						'name'		=> array('INDEX', 'name(255)'),
						'last'		=> array('INDEX', 'last'),
					)
				),
				$this->table_prefix . 'statistics'	=> array(
					'COLUMNS'			=> array(
						'year'			=> array('UINT:4', 0),
						'month'			=> array('UINT:4', 0),
						'day'			=> array('UINT:4', 0),
						'hits'			=> array('UINT:11', 0),
					),
					'KEYS'			=> array(
						'id'		=> array('UNIQUE', array('year', 'month', 'day')),
						'year'		=> array('INDEX', 'year'),
						'month'		=> array('INDEX', 'month'),
						'day'		=> array('INDEX', 'day'),
					)
				),
			)
		);
	}

	/**
	 * Drop the table schema from the database
	 *
	 * @return array Array of table schema
	 * @access public
	 */
	public function revert_schema()
	{
		return array(
			'drop_tables'	=> array(
				$this->table_prefix . 'statistics_config',
				$this->table_prefix . 'statistics_domains',
				$this->table_prefix . 'statistics_online',
				$this->table_prefix . 'statistics_archive',
				$this->table_prefix . 'statistics_se',
				$this->table_prefix . 'statistics'
			),
		);
	}
}
