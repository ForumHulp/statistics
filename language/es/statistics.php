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
	'FL_DATE'		=> 'Información de la trabla de archivo',
	'UGROUPS'		=> 'Grupo de usuarios',
	'UNIQUE'		=> 'Visitantes únicos',

	'HOV'			=> 'Resumen horario',
	'DOV'			=> 'Resumen diario',
	'MOV'			=> 'Resumen mensual',
	'YOV'			=> 'Resumen anual',

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

	// Top10
	'PPD'		=>	'Mensajes por día',
	'PPM'		=>	'Mensajes por mes',
	'TPD'		=>	'Temas por día',
	'TPM'		=>	'Temas por mes',
	'FORUMDAYS'	=>	'Días del foro',
	'APPT'		=>	'Promedio de mensajes por tema',
	'APPU'		=>	'Promedio de mensajes por usuario',

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
	'MAX_ONLINE'		=> 'Conectado',
	'DELL'				=> 'Borrar',

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

	'CUSTOM_PAGES'				=> 'Páginas personalizadas',
	'CUSTOM_PAGES_EXPLAIN'		=> 'Complete el nombre de su página personalizada y variable de lenguaje para mostrar su extensión en Estadísticas del foro. Seleccione su extensión para borrar o cambiarlo.',

	'START_SCREEN'				=> 'Pantalla de inicio',
	'START_SCREEN_EXPLAIN'		=> 'Elija su pantalla de inicio de Estadísticas del foro y si desea mostrar el archivo o en línea.',

	'BOTS_INC'					=> 'Incluir robots',
	'BOTS_INC_EXPLAIN'			=> 'Incluir robots en la pantalla de conectados.',

	'LOG'						=> 'Registros',
	'LOG_EXPLAIN'				=> 'Añadir registros al registro de mantenimiento.',

	'DEL_STAT'					=> 'Vaciar tablas de archivo',
	'DEL_STAT_EXPLAIN'			=> 'Vaciar tablas de archivo y resetear Estadísticas del foro.',

	'STAT_DELETE_CONFIRM'		=> '¿Vaciar tablas de archivo?',

	'BS_STATUS_TIMEOUT'			=> 'Tiempo de espera de actualización',
	'BS_STATUS_ERROR'			=> 'Error de actualización',
	'BS_STATUS_ERROR_EXPLAIN'	=> 'Se ha producido un error durante la actualización de la página.',
));
