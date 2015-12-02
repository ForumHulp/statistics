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
	'ACP_STATISTICS'			=> '掲示板統計情報',
	'LOG_STATISTICS_PRUNED'		=> '<strong>掲示板統計情報を切り詰めました</strong><br />» %1$.1f 秒使用、1秒辺り %2$.1f 行',
	'LOG_STATISTICS_NO_PRUNE'	=> '<strong>掲示板統計情報</strong><br />» 切り詰めるログはありません',

	'STAT_DELETE_SUCCESS'		=> 'アーカイブテーブルを空にしました',
	'STAT_DELETE_ERROR'			=> 'アーカイブテーブルを空にする際の切り詰めエラーです。',
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
