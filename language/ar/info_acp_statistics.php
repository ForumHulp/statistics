<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license Proprietary
*
* Translated By : Bassel Taha Alhitary - www.alhitary.net
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
	'ACP_STATISTICS'			=> 'إحصائيات المنتدى',
	'LOG_STATISTICS_PRUNED'		=> '<strong>تم تهذيب إحصائيات المنتدى</strong><br />» %1$.1f ثواني مستخدمة , %2$.1f صفوف كل ثانية',
	'LOG_STATISTICS_NO_PRUNE'	=> '<strong>إحصائيات المنتدى</strong><br />» لا يوجد سجلات للتهذيب',

	'STAT_DELETE_SUCCESS'		=> 'تم حذف جداول الأرشيف ',
	'STAT_DELETE_ERROR'			=> 'هناك خطأ في عملية حذف جداول الأرشيف.',
	'FH_HELPER_NOTICE'			=> 'Forumhulp helper application does not exist!<br />Download <a href="">forumhulp/helper</a> and copy the helper folder to your forumhulp extension folder.',
	'STATISTICS_NOTICE'			=> '<div class="phpinfo"><p class="entry">إعدادات الضبط لهذه الإضافة موجودة في %1$s » %2$s » %3$s.</p></div>',
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
