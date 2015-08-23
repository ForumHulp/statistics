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
	'LOG_STATISTICS_PRUNED'		=> '<strong>Estadísticas del foro limpiadas</strong><br />» %1$.1f segundos usados, %2$.1f filas por segundo',
	'LOG_STATISTICS_NO_PRUNE'	=> '<strong>Estadísticas del foro</strong><br />» No hay registros limpiados',

	'STAT_DELETE_SUCCESS'		=> 'Tablas del archivo vaciadas',
	'STAT_DELETE_ERROR'			=> 'Truncate error en el vaciado de tablas del archivo.',
	'STATISTICS_NOTICE'			=> '<div style="width:80%%;margin:20px auto;"><p style="text-align:left;">Config setting of this extension are in %1$s » %2$s » %3$s.</p></div>',
));
