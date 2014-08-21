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
	'ACP_STATISTICS'	=> 'Estadísticas del foro',

	'ACP_ACP_STATISTICS_EXPLAIN'	=> 'Estadísticas del foro muestra una visión general de sus visitantes. Esta extensión registra todas las visitas de sus usuarios. Una tarea cron analizará las estadísticas sobre medianoche y purgará según su propia configuración. Puede ver las estadísticas de hoy o globales. Se muestra un gráfico para cada página y así tener una visión sencilla de sus estadísticas.',

	'OPTIONS'		=> 'Opciones',
	'COUNTRIES'		=> 'Países',
	'REFERRALS'		=> 'Referidos',
	'SEARCHENG'		=> 'Motores de búsqueda',
	'SEARCHTERMS'	=> 'Términos de búsqueda',
	'BROWSERS'		=> 'Navegadores',
	'CRAWLERS'		=> 'Rastreadores web',
	'SYSTEMS'		=> 'Sistemas operativos',
	'MODULES'		=> 'Módulos',
	'AVERAGES'		=> 'Promedios',
	'RESOLUTIONS'	=> 'Resoluciones de pantalla',
	'OVERVIEW'		=> 'Información general',
	'ADMIN'			=> 'Administrar',
	'USERS'			=> 'Usuarios',
	'USERSTATS'		=> 'Gráficos de estadísticas de usuarios',
	'LASTVISITS'	=> 'Página de últimas visitas',

	// Online
    'TIME'		=> 'Fecha',
    'USER'		=> 'Usuario',
    'MODULE'	=> 'Módulo',
    'COUNTRY'	=> 'País',
	'HOST'		=> 'Host',	
    'IP'		=> 'Dirección IP',
	
	// Module
	'MODULE_FORUM'	=> 'Módulos / Foros',
	'VIEWS'			=> 'Vistas',
	'PERC'			=> 'Porcentaje',
	'GRAPH'			=> 'Gráfico',
	
	//Config
	'MAX_COUNTRIES'		=> 'Países',
	'MAX_REFERER'		=> 'Referidos',
	'MAX_SE'			=> 'Motores de búsqueda',
	'MAX_SE_TERMS'		=> 'Términos de búsqueda',
	'MAX_BROWSERS'		=> 'Navegadores',
	'MAX_CRAWL'			=> 'Rastreadores web',
	'MAX_OS'			=> 'Sistemas operativos',
	'MAX_MODULES'		=> 'Módulos',
	'MAX_USERS'			=> 'Usuarios',
	'MAX_AVERAGES'		=> 'Promedios',
	'MAX_SCREENS'		=> 'Resoluciones de pantalla',
	'MAX_ONLINE'		=> 'Online',

	'MAX_COUNTRIES_EXPLAIN'		=> 'Número máximo de registros en la vista de pantalla antes de la paginación está en orden, en la tabla de registros máximos para purgar',
	'MAX_REFERER_EXPLAIN'		=> 'Número máximo de registros en la vista de pantalla antes de la paginación está en orden, en la tabla de registros máximos para purgar',
	'MAX_SE_EXPLAIN'			=> 'Número máximo de registros en la vista de pantalla antes de la paginación está en orden, en la tabla de registros máximos para purgar',
	'MAX_SE_TERMS_EXPLAIN'		=> 'Número máximo de registros en la vista de pantalla antes de la paginación está en orden, en la tabla de registros máximos para purgar',
	'MAX_BROWSERS_EXPLAIN'		=> 'Número máximo de registros en la vista de pantalla antes de la paginación está en orden, en la tabla de registros máximos para purgar',
	'MAX_CRAWL_EXPLAIN'			=> 'Número máximo de registros en la vista de pantalla antes de la paginación está en orden, en la tabla de registros máximos para purgar',
	'MAX_OS_EXPLAIN'			=> 'Número máximo de registros en la vista de pantalla antes de la paginación está en orden, en la tabla de registros máximos para purgar',
	'MAX_MODULES_EXPLAIN'		=> 'Número máximo de registros en la vista de pantalla antes de la paginación está en orden, en la tabla de registros máximos para purgar',
	'MAX_USERS_EXPLAIN'			=> 'Número máximo de registros en la vista de pantalla antes de la paginación está en orden, en la tabla de registros máximos para purgar',
	'MAX_AVERAGES_EXPLAIN'		=> 'Número máximo de registros en la vista de pantalla antes de la paginación está en orden, en la tabla de registros máximos para purgar',
	'MAX_SCREENS_EXPLAIN'		=> 'Número máximo de registros en la vista de pantalla antes de la paginación está en orden, en la tabla de registros máximos para purgar',
	'MAX_ONLINE_EXPLAIN'		=> 'Número máximo de registros en la vista de pantalla antes de la paginación está en orden, en la tabla de registros máximos para purgar',

));