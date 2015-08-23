<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_STATISTICS'	=> 'آمار و اطلاعات انجمن',
	'LOG_STATISTICS_PRUNED'		=> '<strong>Board Statistics pruned</strong><br />» %1$.1f seconds used, %2$.1f  rows per second',
	'LOG_STATISTICS_NO_PRUNE'	=> '<strong>Board Statistics</strong><br />» No records pruned',

	'STAT_DELETE_SUCCESS'		=> 'Archive tables emptyed',
	'STAT_DELETE_ERROR'			=> 'Truncate error emptying archive tables.',
	'STATISTICS_NOTICE'			=> '<div style="width:80%%;margin:20px auto;"><p style="text-align:left;">Config setting of this extension are in %1$s » %2$s » %3$s.</p></div>',
));
