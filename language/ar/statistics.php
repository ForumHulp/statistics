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
	'ACP_STATISTICS'	=> 'إحصائيات المنتدى',

	'ACP_ACP_STATISTICS_EXPLAIN'	=> 'إحصائيات المنتدى تعطيك نبذة كاملة عن الزائرين في منتداك. وتعمل على تسجيل كل زيارات الأعضاء. والوظيفة الرئيسية لهذه الإضافة هي تحليل الإحصائيات في منتصف الليل وتهذيب الجداول بحسب الإعددات التي حددتها. تستطيع مُشاهدة الإحصائيات لهذا اليوم أو لكل الأيام. سيكون لديك صورة سهلة لفهم الإحصائيات بواسطة الرسم البياني لكل صفحة على حده.',

	'OPTIONS'		=> 'الخيارات',
	'COUNTRIES'		=> 'الدول',
	'REFERRALS'		=> 'روابط إلى موقعك',
	'SEARCHENG'		=> 'محركات البحث',
	'SEARCHTERMS'	=> 'شروط البحث',
	'BROWSERS'		=> 'المتصفحات',
	'CRAWLERS'		=> 'الزواحف',
	'SYSTEMS'		=> 'أنظمة الكمبيوتر',
	'MODULES'		=> 'الموديلات',
	'AVERAGES'		=> 'المتوسط',
	'RESOLUTIONS'	=> 'دقة الشاشة',
	'OVERVIEW'		=> 'ال10 الأوائل',
	'ADMIN'			=> 'إدارة',
	'USERS'			=> 'الأعضاء',
	'USERSTATS'		=> 'رسم بياني لإحصائيات الأعضاء',
	'LASTVISITS'	=> 'صفحة آخر الزيارات',
	'FL_DATE'		=> 'معلومات جدول الأرشيف',
	'UGROUPS'		=> 'User groups',
	'UNIQUE'		=> 'زائرين وحيدين',

	'HOV'			=> 'Hourly overview',
	'DOV'			=> 'Daily overview',
	'MOV'			=> 'Monthly overview',
	'YOV'			=> 'Yearly overview',

	// Online
	'TIME'		=> 'الوقت',
	'USER'		=> 'العضو',
	'MODULE'	=> 'الموديل',
	'COUNTRY'	=> 'الدولة',
	'HOST'		=> 'المستضيف',
	'IP'		=> 'رقم الـIP',

	// Module
	'MODULE_FORUM'	=> 'الموديلات / المنتديات',
	'VIEWS'			=> 'مُشاهدات',
	'PERC'			=> 'النسبة',
	'GRAPH'			=> 'رسم بياني',

	// Top10
	'PPD'		=>	'Posts per day',
	'PPM'		=>	'Posts per month',
	'TPD'		=>	'Topics per day',
	'TPM'		=>	'Topics per month',
	'FORUMDAYS'	=>	'Forumdays',
	'APPT'		=>	'Average posts per topic',
	'APPU'		=>	'Average posts per user',

	//Config
	'MAX_COUNTRIES'		=> 'الدول',
	'MAX_REFERER'		=> 'روابط إلى موقعك',
	'MAX_SE'			=> 'محركات البحث',
	'MAX_SE_TERMS'		=> 'شروط البحث',
	'MAX_BROWSERS'		=> 'المتصفحات',
	'MAX_CRAWL'			=> 'الزواحف',
	'MAX_OS'			=> 'أنظمة الكمبيوتر',
	'MAX_MODULES'		=> 'الموديلات',
	'MAX_USERS'			=> 'الأعضاء',
	'MAX_AVERAGES'		=> 'المتوسط',
	'MAX_SCREENS'		=> 'دقة الشاشة',
	'MAX_ONLINE'		=> 'المتواجدين الآن',
	'DELL'				=> 'حذف',
	'SEARCHENG_EXPLAIN'	=> 'تغيير , تعديل , إضافة أو حذف محركات البحث.',

	'MAX_COUNTRIES_EXPLAIN'		=> 'اقرأ الشرح في الموديلات.',
	'MAX_REFERER_EXPLAIN'		=> 'اقرأ الشرح في الموديلات.',
	'MAX_SE_EXPLAIN'			=> 'اقرأ الشرح في الموديلات.',
	'MAX_SE_TERMS_EXPLAIN'		=> 'اقرأ الشرح في الموديلات.',
	'MAX_BROWSERS_EXPLAIN'		=> 'اقرأ الشرح في الموديلات.',
	'MAX_CRAWL_EXPLAIN'			=> 'اقرأ الشرح في الموديلات.',
	'MAX_OS_EXPLAIN'			=> 'اقرأ الشرح في الموديلات.',
	'MAX_MODULES_EXPLAIN'		=> 'الحد الأعلى لعدد السجلات يظهر قبل ترقيم الصفحات بالترتيب , بينما الحد الأقصى للجداول سيكون للتهذيب.',
	'MAX_USERS_EXPLAIN'			=> 'اقرأ الشرح في الموديلات.',
	'MAX_AVERAGES_EXPLAIN'		=> 'اقرأ الشرح في الموديلات.',
	'MAX_SCREENS_EXPLAIN'		=> 'اقرأ الشرح في الموديلات.',
	'MAX_ONLINE_EXPLAIN'		=> 'اقرأ الشرح في الموديلات.',

	'CUSTOM_PAGES'				=> 'صفحات خاصة ',
	'CUSTOM_PAGES_EXPLAIN'		=> 'تستطيع إضافة إسم صفحتك الخاصة و متغير اللغة لعرضها في الإحصائيات. حدد إضافتك للحذف أو لتغييرها.',

	'START_SCREEN'				=> 'بداية الصفحة ',
	'START_SCREEN_EXPLAIN'		=> 'حدد الصفحة التي تريد الذهاب إليها عند النقر على إحصائيات المنتدى و إذا ترغب في عرض الأرشيف أو المتواجدون الآن.',

	'BOTS_INC'					=> 'إدراج محركات البحث',
	'BOTS_INC_EXPLAIN'			=> 'عرض محركات البحث ضمن المتواجدون الآن.',

	'LOG'						=> 'السجلات ',
	'LOG_EXPLAIN'				=> 'إضافة السجلات إلى سجل الصيانة.',

	'DEL_STAT'					=> 'حذف جداول الأرشيف ',
	'DEL_STAT_EXPLAIN'			=> 'حذف أو تفريغ جداول الأرشيف وإعادة ضبط إحصائيات المنتدى.',

	'STAT_DELETE_CONFIRM'		=> 'حذف جداول الأرشيف ?',

	'BS_STATUS_TIMEOUT'			=> 'تحديث الجلسة',
	'BS_STATUS_ERROR'			=> 'تحديث الخطأ',
	'BS_STATUS_ERROR_EXPLAIN'	=> 'هناك خطأ حدث أثناء تحديث الصفحة.',
));
