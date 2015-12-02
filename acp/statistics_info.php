<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license Proprietary
*
*/

namespace forumhulp\statistics\acp;

class statistics_info
{
	function module()
	{
		return array(
			'filename'	=> 'forumhulp\statistics\acp\statistics_info',
			'title'		=> 'ACP_STATISTICS',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'stat'	=> array(
					'title'	=> 'ACP_STATISTICS',
					'auth'	=> 'ext_forumhulp/statistics && acl_a_viewlogs',
					'cat'	=> array('ACP_QUICK_ACCESS')
				),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}
