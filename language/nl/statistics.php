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
	'ACP_STATISTICS'	=> 'Statistics',

	'ACP_ACP_STATISTICS_EXPLAIN'	=> 'Statistics displays an overview of your user\'s visits in Quick Access module of ACP. This extension logs all visits of your users. A cron job will prune statistics. .',


	'OPTIONS'		=> 'Opties',
	'COUNTRIES'		=> 'Landen',
	'REFERRALS'		=> 'Referrals',
	'SEARCHENG'		=> 'Zoek Machines',
	'SEARCHTERMS'	=> 'Zoek Termen',
	'BROWSERS'		=> 'Browsers',
	'CRAWLERS'		=> 'Web Crawlers',
	'SYSTEMS'		=> 'Computer Systemen',
	'MODULES'		=> 'Modules',
	'AVERAGES'		=> 'Gemiddelden',
	'RESOLUTIONS'	=> 'Scherm Resoluties',
	'OVERVIEW'		=> 'Top 10',
	'ADMIN'			=> 'Admininstratie',
	'USERS'			=> 'Gebruikers',
	'USERSTATS'		=> 'Bezoekers Stats Grafieken',
	'ONLINE'		=> 'Last visits page',
	'LASTVISITS'	=> 'Laatste Pagina bezoeken',
	'FL_DATE'		=> 'Archief tabel info',
	'UGROUPS'		=> 'Gebruikers groupen',
	'UNIQUE'		=> 'Unieke bezoekers',

	'HOV'			=> 'Hourly overview',
	'DOV'			=> 'Daily overview',
	'MOV'			=> 'Monthly overview',
	'YOV'			=> 'Yearly overview',

	// Online
	'TIME'		=> 'Tijd',
	'USER'		=> 'Gebruiker',
	'MODULE'	=> 'Module',
	'COUNTRY'	=> 'Land',
	'HOST'		=> 'Host',
	'IP'		=> 'IP adres',

	// Module
	'MODULE_FORUM'	=> 'Modules / Forums',
	'VIEWS'			=> 'Hits',
	'PERC'			=> 'Percentage',
	'GRAPH'			=> 'Grafiek',

	// Top10
	'PPD'		=>	'Posts per day',
	'PPM'		=>	'Posts per month',
	'TPD'		=>	'Topics per day',
	'TPM'		=>	'Topics per month',
	'FORUMDAYS'	=>	'Forumdays',
	'APPT'		=>	'Average posts per topic',
	'APPU'		=>	'Average posts per user',
	'SEARCHRESULTS'	=> 'Searchresult',

	//Config
	'MAX_COUNTRIES'		=> 'Landen',
	'MAX_REFERER'		=> 'Referrals',
	'MAX_SE'			=> 'Zoek machines',
	'MAX_SE_TERMS'		=> 'Zoek termen',
	'MAX_BROWSERS'		=> 'Browsers',
	'MAX_CRAWL'			=> 'Web Crawlers',
	'MAX_OS'			=> 'Computer Systemen',
	'MAX_MODULES'		=> 'Modules',
	'MAX_USERS'			=> 'Gebruikers',
	'MAX_AVERAGES'		=> 'Gemilden',
	'MAX_SCREENS'		=> 'Scherm Resoluties',
	'MAX_ONLINE'		=> 'Online',
	'DELL'				=> 'Delete',
	'SEARCHENG_EXPLAIN'	=> 'Change, edit, add or delete searchengines.',

	'MAX_COUNTRIES_EXPLAIN'		=> 'Aantal records in online modus per pagina, in table maximum aantal records.',
	'MAX_REFERER_EXPLAIN'		=> 'Aantal records in online modus per pagina, in table maximum aantal records',
	'MAX_SE_EXPLAIN'			=> 'Aantal records in online modus per pagina, in table maximum aantal records',
	'MAX_SE_TERMS_EXPLAIN'		=> 'Aantal records in online modus per pagina, in table maximum aantal records',
	'MAX_BROWSERS_EXPLAIN'		=> 'Aantal records in online modus per pagina, in table maximum aantal records',
	'MAX_CRAWL_EXPLAIN'			=> 'Aantal records in online modus per pagina, in table maximum aantal records',
	'MAX_OS_EXPLAIN'			=> 'Aantal records in online modus per pagina, in table maximum aantal records',
	'MAX_MODULES_EXPLAIN'		=> 'Aantal records in online modus per pagina, in table maximum aantal records',
	'MAX_USERS_EXPLAIN'			=> 'Aantal records in online modus per pagina, in table maximum aantal records',
	'MAX_AVERAGES_EXPLAIN'		=> 'Aantal records in online modus per pagina, in table maximum aantal records',
	'MAX_SCREENS_EXPLAIN'		=> 'Aantal records in online modus per pagina, in table maximum aantal records',
	'MAX_ONLINE_EXPLAIN'		=> 'Aantal records in online modus per pagina, in table maximum aantal records',

	'CUSTOM_PAGES'				=> 'Custom Pagina\'s',
	'CUSTOM_PAGES_EXPLAIN'		=> 'Pagina naam en taal variabele van jouw extensie voor Board Statistics. Selecteer jouw extensie om te verwijderen of te wijzigen.',

	'START_SCREEN'				=> 'Start screen',
	'START_SCREEN_EXPLAIN'		=> 'Choose your startscreen for Board Statistics and if you want to display archive or online.',

	'BOTS_INC'					=> 'Include bots',
	'BOTS_INC_EXPLAIN'			=> 'Include bots in online display.',

	'LOG'						=> 'Logs',
	'LOG_EXPLAIN'				=> 'Add logs to maintenance log.',

	'DEL_STAT'					=> 'Empty archive tables',
	'DEL_STAT_EXPLAIN'			=> 'Empty archive tables and reset Board Statistics.',

	'STAT_DELETE_CONFIRM'		=> 'Empty archive tables?',

	'BS_STATUS_TIMEOUT'			=> 'Refresh timeout',
	'BS_STATUS_ERROR'			=> 'Refresh error',
	'BS_STATUS_ERROR_EXPLAIN'	=> 'An error occurred during refreshing the page.',
));
