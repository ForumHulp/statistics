<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
* @translated into Swedish by Holger (http://www.maskinisten.net)
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
	'ACP_STATISTICS'			=> 'Forumstatistik',
	'LOG_STATISTICS_PRUNED'		=> '<strong>Forumstatistik raderad</strong><br />Â» %1$.1f sekunder, %2$.1f rader per sekund',
	'LOG_STATISTICS_NO_PRUNE'	=> '<strong>Forumstatistik</strong><br />Â» inga poster raderade',

	'STAT_DELETE_SUCCESS'		=> 'Arkivtabeller tömda',
	'STAT_DELETE_ERROR'			=> 'Trunkeringsfel under tömning av arkivtabeller'

));
