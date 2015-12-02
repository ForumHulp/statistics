<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license Proprietary
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
	'STAT_DELETE_ERROR'			=> 'Trunkeringsfel under tömning av arkivtabeller',
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
