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

	'ACP_ACP_STATISTICS_EXPLAIN'	=> 'Forumstatistiken visar en �versikt �ver dina bes�kare. Detta plugin loggar alla bes�k. Ett cronjobb analyserar statistiken vid midnatt och t�mmer tabellerna enligt dina inst�llningar. Du kan se dagens statistik eller den totala statistiken. Ett diagram visas f�r varje sida.',

	'OPTIONS'		=> 'Optioner',
	'COUNTRIES'		=> 'L�nder',
	'REFERRALS'		=> 'Referenser',
	'SEARCHENG'		=> 'S�kmotorer',
	'SEARCHTERMS'	=> 'S�kord',
	'BROWSERS'		=> 'Webbl�sare',
	'CRAWLERS'		=> 'S�kspindlar',
	'SYSTEMS'		=> 'Datorsystem',
	'MODULES'		=> 'Moduler',
	'AVERAGES'		=> 'Medelv�rden',
	'RESOLUTIONS'	=> 'Bildsk�rmuppl�sningar',
	'OVERVIEW'		=> 'Topp 10',
	'ADMIN'			=> 'Administrera',
	'USERS'			=> 'Anv�ndare',
	'USERSTATS'		=> 'Anv�ndardiagram',
	'ONLINE'		=> 'Last visits page',
	'LASTVISITS'	=> 'Senaste bes�kta sida',
	'FL_DATE'		=> 'Arkivtabellinformation',
	'UGROUPS'		=> 'User groups',
	'UNIQUE'		=> 'Unique visitors',

	'HOV'			=> 'Hourly overview',
	'DOV'			=> 'Daily overview',
	'MOV'			=> 'Monthly overview',
	'YOV'			=> 'Yearly overview',

	// Online
	'TIME'		=> 'Tid',
	'USER'		=> 'Anv�ndare',
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
	'SEARCHRESULTS'	=> 'Searchresult',

	//Config
	'MAX_COUNTRIES'		=> 'L�nder',
	'MAX_REFERER'		=> 'Referenser',
	'MAX_SE'			=> 'S�kmotorer',
	'MAX_SE_TERMS'		=> 'S�kord',
	'MAX_BROWSERS'		=> 'Webbl�sare',
	'MAX_CRAWL'			=> 'S�kspindlar',
	'MAX_OS'			=> 'Datorsystem',
	'MAX_MODULES'		=> 'Moduler',
	'MAX_USERS'			=> 'Anv�ndare',
	'MAX_AVERAGES'		=> 'Medelv�rden',
	'MAX_SCREENS'		=> 'Bildsk�rmuppl�sningar',
	'MAX_ONLINE'		=> 'Online',
	'DELL'				=> 'Radera',
	'SEARCHENG_EXPLAIN'	=> '�ndra, l�gg till eller ta bort s�kmotorer.',

	'MAX_COUNTRIES_EXPLAIN'		=> 'Se modulbeskrivningen',
	'MAX_REFERER_EXPLAIN'		=> 'Se modulbeskrivningen',
	'MAX_SE_EXPLAIN'			=> 'Se modulbeskrivningen',
	'MAX_SE_TERMS_EXPLAIN'		=> 'Se modulbeskrivningen',
	'MAX_BROWSERS_EXPLAIN'		=> 'Se modulbeskrivningen',
	'MAX_CRAWL_EXPLAIN'			=> 'Se modulbeskrivningen',
	'MAX_OS_EXPLAIN'			=> 'Se modulbeskrivningen',
	'MAX_MODULES_EXPLAIN'		=> 'Maximalt antal poster innan paginering anv�nds, i tabell maximalt antal poster f�r radering',
	'MAX_USERS_EXPLAIN'			=> 'Se modulbeskrivningen',
	'MAX_AVERAGES_EXPLAIN'		=> 'Se modulbeskrivningen',
	'MAX_SCREENS_EXPLAIN'		=> 'Se modulbeskrivningen',
	'MAX_ONLINE_EXPLAIN'		=> 'Se modulbeskrivningen',

	'CUSTOM_PAGES'				=> 'Egna sidor',
	'CUSTOM_PAGES_EXPLAIN'		=> 'Fyll i namnet och spr�kvariablen f�r den egna sidan f�r att lista upp den i forumstatistiken. V�lj ditt plugin f�r att radera eller �ndra det.',

	'START_SCREEN'				=> 'Startbildsk�rm',
	'START_SCREEN_EXPLAIN'		=> 'V�lj din startbildsk�rm f�r forumstatistiken och om du vill visa arkiv eller online.',

	'BOTS_INC'					=> 'Inkludera botar',
	'BOTS_INC_EXPLAIN'			=> 'Inkludera botar i onlinevisningen.',

	'LOG'						=> 'Logs',
	'LOG_EXPLAIN'				=> 'Add logs to maintenance log.',

	'DEL_STAT'					=> 'Radera arkivtabellerna',
	'DEL_STAT_EXPLAIN'			=> 'Radera arkivtabellerna och nollst�ll forumstatistiken.',

	'STAT_DELETE_CONFIRM'		=> 'Radera arkivtabellerna?',

	'BS_STATUS_TIMEOUT'			=> 'Refresh timeout',
	'BS_STATUS_ERROR'			=> 'Refresh error',
	'BS_STATUS_ERROR_EXPLAIN'	=> 'An error occurred during refreshing the page.',
));
