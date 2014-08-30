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
	'ACP_STATISTICS'	=> 'Board Statistics',

	'ACP_ACP_STATISTICS_EXPLAIN'	=> 'Board Statistics displays an overview of your visitors. This extension logs all visits of your users. A cron job will analyse statistics at midnight and prune tables according to your settings. You can view statistics of today or overall. A graph is shown for every page to have a easy view of your statistics.',

	'OPTIONS'		=> 'Options',
	'COUNTRIES'		=> 'Countries',
	'REFERRALS'		=> 'Referrals',
	'SEARCHENG'		=> 'Search engines',
	'SEARCHTERMS'	=> 'Search terms',
	'BROWSERS'		=> 'Browsers',
	'CRAWLERS'		=> 'Web Crawlers',
	'SYSTEMS'		=> 'Computer Systems',
	'MODULES'		=> 'Modules',
	'AVERAGES'		=> 'Averages',
	'RESOLUTIONS'	=> 'Screen Resolutions',
	'OVERVIEW'		=> 'Top 10',
	'ADMIN'			=> 'Administer',
	'USERS'			=> 'Users',
	'USERSTATS'		=> 'Users Stats Graphs',
	'LASTVISITS'	=> 'Last visits page',
	'FL_DATE'		=> 'Archive table info',

	// Online
	'TIME'		=> 'Time',
	'USER'		=> 'User',
	'MODULE'	=> 'Module',
	'COUNTRY'	=> 'Country',
	'HOST'		=> 'Host',	
	'IP'		=> 'IP address',

	// Module
	'MODULE_FORUM'	=> 'Modules / Forums',
	'VIEWS'			=> 'Views',
	'PERC'			=> 'Percentage',
	'GRAPH'			=> 'Graph',

	//Config
	'MAX_COUNTRIES'		=> 'Countries',
	'MAX_REFERER'		=> 'Referrals',
	'MAX_SE'			=> 'Search engines',
	'MAX_SE_TERMS'		=> 'Search terms',
	'MAX_BROWSERS'		=> 'Browsers',
	'MAX_CRAWL'			=> 'Web Crawlers',
	'MAX_OS'			=> 'Computer Systems',
	'MAX_MODULES'		=> 'Modules',
	'MAX_USERS'			=> 'Users',
	'MAX_AVERAGES'		=> 'Averages',
	'MAX_SCREENS'		=> 'Screen Resolutions',
	'MAX_ONLINE'		=> 'Online',
	'DELL'				=> 'Delete',
	'SEARCHENG_EXPLAIN'	=> 'Change, edit, add or delete searchengines.',

	'MAX_COUNTRIES_EXPLAIN'		=> 'See explanation at modules.',
	'MAX_REFERER_EXPLAIN'		=> 'See explanation at modules',
	'MAX_SE_EXPLAIN'			=> 'See explanation at modules',
	'MAX_SE_TERMS_EXPLAIN'		=> 'See explanation at modules',
	'MAX_BROWSERS_EXPLAIN'		=> 'See explanation at modules',
	'MAX_CRAWL_EXPLAIN'			=> 'See explanation at modules',
	'MAX_OS_EXPLAIN'			=> 'See explanation at modules',
	'MAX_MODULES_EXPLAIN'		=> 'Maximum records in view display before pagination is in order, in table maximum records for pruning',
	'MAX_USERS_EXPLAIN'			=> 'See explanation at modules',
	'MAX_AVERAGES_EXPLAIN'		=> 'See explanation at modules',
	'MAX_SCREENS_EXPLAIN'		=> 'See explanation at modules',
	'MAX_ONLINE_EXPLAIN'		=> 'See explanation at modules',

	'CUSTOM_PAGES'				=> 'Custom Pages',
	'CUSTOM_PAGES_EXPLAIN'		=> 'Fill in your custom page name and language variable to show your extension in Board Statistics. Select your extension for deleting or change it.',

	'START_SCREEN'				=> 'Start screen',
	'START_SCREEN_EXPLAIN'		=> 'Choose your startscreen for Board Statistics and if you want to display archive or online.',

	'DEL_STAT'					=> 'Empty archive tables',
	'DEL_STAT_EXPLAIN'			=> 'Empty archive tables and reset Board Statistics.',

	'STAT_DELETE_CONFIRM'		=> 'Empty archive tables?',
));
