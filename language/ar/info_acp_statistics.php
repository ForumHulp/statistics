<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
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
	'STAT_DELETE_ERROR'			=> 'هناك خطأ في عملية حذف جداول الأرشيف.'
	'STATISTICS_NOTICE'			=> '<div style="width:80%%;margin:20px auto;"><p style="text-align:left;">إعدادات الضبط لهذه الإضافة موجودة في %1$s » %2$s » %3$s.</p></div>',
));
