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
	'ACP_STATISTICS'			=> '掲示板統計情報',
	'LOG_STATISTICS_PRUNED'		=> '<strong>掲示板統計情報を切り詰めました</strong><br />» %1$.1f 秒使用、1秒辺り %2$.1f 行',
	'LOG_STATISTICS_NO_PRUNE'	=> '<strong>掲示板統計情報</strong><br />» 切り詰めるログはありません',

	'STAT_DELETE_SUCCESS'		=> 'アーカイブテーブルを空にしました',
	'STAT_DELETE_ERROR'			=> 'アーカイブテーブルを空にする際の切り詰めエラーです。'

));
