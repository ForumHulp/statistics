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
	'LOG_STATISTICS_PRUNED'		=> '<strong>Estadísticas del foro limpiadas</strong><br />» %1$.1f segundos usados, %2$.1f filas por segundo',
	'LOG_STATISTICS_NO_PRUNE'	=> '<strong>Estadísticas del foro</strong><br />» No hay registros limpiados',

	'STAT_DELETE_SUCCESS'		=> 'Tablas del archivo vaciadas',
	'STAT_DELETE_ERROR'			=> 'Truncate error en el vaciado de tablas del archivo.',
	'FH_HELPER_NOTICE'			=> '¡La aplicación Forumhulp helper no existe!<br />Descargar <a href="https://github.com/ForumHulp/helper" target="_blank">forumhulp/helper</a> y copie la carpeta helper dentro de la carpeta de extensión forumhulp.',
	'STATISTICS_NOTICE'			=> '<div class="phpinfo"><p class="entry">Los ajustes de configuración de está extensión están en %1$s » %2$s » %3$s.</p></div>',
));

// Description of extension
$lang = array_merge($lang, array(
	'DESCRIPTION_PAGE'		=> 'Descripción',
	'DESCRIPTION_NOTICE'	=> 'Nota de la extensión',
	'ext_details' => array(
		'details' => array(
			'DESCRIPTION_1'		=> 'Resumen gráfico de los visitantes',
			'DESCRIPTION_2'		=> 'Posibilidad de ver páginas personalizadas',
			'DESCRIPTION_3'		=> 'Resumen de en línea y total',
			'DESCRIPTION_4'		=> 'Configurable',
		),
		'note' => array(
			'NOTICE_1'			=> 'Gráficos de alta calidad',
			'NOTICE_2'			=> 'Top 10',
			'NOTICE_3'			=> 'Preparado para phpBB 3.2'
		)
	)
));
