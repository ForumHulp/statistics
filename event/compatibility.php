<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* Inserts new config display_vars into an exisiting display_vars array
* at the given position.
* NOTE: this function is added for compatibility until an official one will come out.
*
* @param array $display_vars An array of existing config display vars
* @param array $add_config_vars An array of new config display vars
* @param array $where Where to place the new config vars,
*              before or after an exisiting config, as an array
*              of the form: array('after' => 'config_name') or
*              array('before' => 'config_name').
* @return array The array of config display vars
*/
function insert_config_array($display_vars, $add_config_vars, $where)
{
	if (is_array($where) && array_key_exists(current($where), $display_vars))
	{
		$position = array_search(current($where), array_keys($display_vars)) + ((key($where) == 'before') ? 0 : 1);
		$display_vars = array_merge(
			array_slice($display_vars, 0, $position),
			$add_config_vars,
			array_slice($display_vars, $position)
		);
	}
	return $display_vars;
}
