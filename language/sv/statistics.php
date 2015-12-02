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
	'ACP_STATISTICS'	=> 'Forumstatistik',

	'ACP_ACP_STATISTICS_EXPLAIN'	=> 'Forumstatistiken visar en översikt över dina besökare. Detta plugin loggar alla besök. Ett cronjobb analyserar statistiken vid midnatt och tömmer tabellerna enligt dina inställningar. Du kan se dagens statistik eller den totala statistiken. Ett diagram visas för varje sida.',

	'OPTIONS'		=> 'Optioner',
	'COUNTRIES'		=> 'Länder',
	'REFERRALS'		=> 'Referenser',
	'SEARCHENG'		=> 'Sökmotorer',
	'SEARCHTERMS'	=> 'Sökord',
	'BROWSERS'		=> 'Webbläsare',
	'CRAWLERS'		=> 'Sökspindlar',
	'SYSTEMS'		=> 'Datorsystem',
	'MODULES'		=> 'Moduler',
	'AVERAGES'		=> 'Medelvärden',
	'RESOLUTIONS'	=> 'Bildskärmupplösningar',
	'OVERVIEW'		=> 'Topp 10',
	'ADMIN'			=> 'Administrera',
	'USERS'			=> 'Användare',
	'USERSTATS'		=> 'Användardiagram',
	'LASTVISITS'	=> 'Senaste besökta sida',
	'FL_DATE'		=> 'Arkivtabellinformation',
	'UGROUPS'		=> 'User groups',
	'UNIQUE'		=> 'Unique visitors',

	'HOV'			=> 'Hourly overview',
	'DOV'			=> 'Daily overview',
	'MOV'			=> 'Monthly overview',
	'YOV'			=> 'Yearly overview',

	// Online
	'TIME'		=> 'Tid',
	'USER'		=> 'Användare',
	'MODULE'	=> 'Modul',
	'COUNTRY'	=> 'Land',
	'HOST'		=> 'Host',
	'IP'		=> 'IP-adress',

	// Module
	'MODULE_FORUM'	=> 'Moduler / Forum',
	'VIEWS'			=> 'Visningar',
	'PERC'			=> 'Procentsats',
	'GRAPH'			=> 'Diagram',

	// Top10
	'PPD'		=>	'Posts per day',
	'PPM'		=>	'Posts per month',
	'TPD'		=>	'Topics per day',
	'TPM'		=>	'Topics per month',
	'FORUMDAYS'	=>	'Forumdays',
	'APPT'		=>	'Average posts per topic',
	'APPU'		=>	'Average posts per user',

	//Config
	'MAX_COUNTRIES'		=> 'Länder',
	'MAX_REFERER'		=> 'Referenser',
	'MAX_SE'			=> 'Sökmotorer',
	'MAX_SE_TERMS'		=> 'Sökord',
	'MAX_BROWSERS'		=> 'Webbläsare',
	'MAX_CRAWL'			=> 'Sökspindlar',
	'MAX_OS'			=> 'Datorsystem',
	'MAX_MODULES'		=> 'Moduler',
	'MAX_USERS'			=> 'Användare',
	'MAX_AVERAGES'		=> 'Medelvärden',
	'MAX_SCREENS'		=> 'Bildskärmupplösningar',
	'MAX_ONLINE'		=> 'Online',
	'DELL'				=> 'Radera',
	'SEARCHENG_EXPLAIN'	=> 'Ändra, lägg till eller ta bort sökmotorer.',

	'MAX_COUNTRIES_EXPLAIN'		=> 'Se modulbeskrivningen',
	'MAX_REFERER_EXPLAIN'		=> 'Se modulbeskrivningen',
	'MAX_SE_EXPLAIN'			=> 'Se modulbeskrivningen',
	'MAX_SE_TERMS_EXPLAIN'		=> 'Se modulbeskrivningen',
	'MAX_BROWSERS_EXPLAIN'		=> 'Se modulbeskrivningen',
	'MAX_CRAWL_EXPLAIN'			=> 'Se modulbeskrivningen',
	'MAX_OS_EXPLAIN'			=> 'Se modulbeskrivningen',
	'MAX_MODULES_EXPLAIN'		=> 'Maximalt antal poster innan paginering används, i tabell maximalt antal poster för radering',
	'MAX_USERS_EXPLAIN'			=> 'Se modulbeskrivningen',
	'MAX_AVERAGES_EXPLAIN'		=> 'Se modulbeskrivningen',
	'MAX_SCREENS_EXPLAIN'		=> 'Se modulbeskrivningen',
	'MAX_ONLINE_EXPLAIN'		=> 'Se modulbeskrivningen',

	'CUSTOM_PAGES'				=> 'Egna sidor',
	'CUSTOM_PAGES_EXPLAIN'		=> 'Fyll i namnet och språkvariablen för den egna sidan för att lista upp den i forumstatistiken. Välj ditt plugin för att radera eller ändra det.',

	'START_SCREEN'				=> 'Startbildskärm',
	'START_SCREEN_EXPLAIN'		=> 'Välj din startbildskärm för forumstatistiken och om du vill visa arkiv eller online.',

	'BOTS_INC'					=> 'Inkludera botar',
	'BOTS_INC_EXPLAIN'			=> 'Inkludera botar i onlinevisningen.',

	'DEL_STAT'					=> 'Radera arkivtabellerna',
	'DEL_STAT_EXPLAIN'			=> 'Radera arkivtabellerna och nollställ forumstatistiken.',

	'STAT_DELETE_CONFIRM'		=> 'Radera arkivtabellerna?',

	'BS_STATUS_TIMEOUT'			=> 'Refresh timeout',
	'BS_STATUS_ERROR'			=> 'Refresh error',
	'BS_STATUS_ERROR_EXPLAIN'	=> 'An error occurred during refreshing the page.',
));
