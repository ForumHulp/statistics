<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license Proprietary
*
*/

namespace forumhulp\statistics\migrations\v32x;

/**
 * Migration stage 1: Config data
 */
class m1_config_data extends \phpbb\db\migration\migration
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
		return array('\forumhulp\statistics\migrations\v31x\m3_config_data');
	}

	/**
	 * Add or update data in the database
	 *
	 * @return array Array of table data
	 * @access public
	 */
	public function update_data()
	{
		return array(
			array('config.add', array('statistics_gc', $this->config['prune_statistics_gc'])),
			array('config.add', array('statistics_last_gc', $this->config['prune_statistics_last_gc'], 1)),
			array('config.remove', array('prune_statistics_gc')),
			array('config.remove', array('prune_statistics_last_gc')),
		);
	}
}
