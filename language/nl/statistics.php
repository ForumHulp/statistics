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
	'OVERVIEW'		=> 'Algemeen Overzicht',
	'ADMIN'			=> 'Admininstratie',
	'USERS'			=> 'Gebruikers',
	'USERSTATS'		=> 'Bezoekers Stats Grafieken',
	'LASTVISITS'	=> 'Laatste Pagina bezoeken',
	
	// Online
    'TIME'		=> 'Tijd',
    'USER'		=> 'Gebruiker',
    'MODULE'	=> 'Module',
    'COUNTRY'	=> 'Land',
	'HOST'		=> 'Host',	
    'IP'		=> 'IP adres',
	
));