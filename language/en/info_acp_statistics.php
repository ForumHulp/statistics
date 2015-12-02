<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license Proprietary
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
	'ACP_STATISTICS'			=> 'Board Statistics',
	'LOG_STATISTICS_PRUNED'		=> '<strong>Board Statistics pruned</strong><br />» %1$.1f seconds used, %2$.1f  rows per second',
	'LOG_STATISTICS_NO_PRUNE'	=> '<strong>Board Statistics</strong><br />» No records pruned',

	'STAT_DELETE_SUCCESS'		=> 'Archive tables emptyed',
	'STAT_DELETE_ERROR'			=> 'Truncate error emptying archive tables.',
	'FH_HELPER_NOTICE'			=> 'Forumhulp helper application does not exist!<br />Download <a href="">forumhulp/helper</a> and copy the helper folder to your forumhulp extension folder.',
	'STATISTICS_NOTICE'			=> '<div class="phpinfo"><p class="entry">Config setting of this extension are in %1$s » %2$s » %3$s.</p></div>',
));

// Description of extension
$lang = array_merge($lang, array(
	'DESCRIPTION_PAGE'		=> 'Description',
	'DESCRIPTION_NOTICE'	=> 'Extension note',
	'ext_details' => array(
		'details' => array(
			'DESCRIPTION_1'		=> 'Graphical overview of visitors',
			'DESCRIPTION_2'		=> 'Possibile to view custom pages',
			'DESCRIPTION_3'		=> 'Online and total overviews',
			'DESCRIPTION_4'		=> 'Configurable',
		),
		'note' => array(
			'NOTICE_1'			=> 'Highchart graphics',
			'NOTICE_2'			=> 'Top 10',
			'NOTICE_3'			=> 'phpBB 3.2 ready'
		)
	)
));
