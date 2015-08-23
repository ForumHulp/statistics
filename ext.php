<?php
/**
*
* @package Referrers
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\statistics;

class ext extends \phpbb\extension\base
{
	function enable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
				global $user;
				$user->add_lang_ext('forumhulp/statistics', 'info_acp_statistics');
				$user->lang['EXTENSION_ENABLE_SUCCESS'] .= (isset($user->lang['STATISTICS_NOTICE']) ? sprintf($user->lang['STATISTICS_NOTICE'], $user->lang['ACP_CAT_GENERAL'], $user->lang['ACP_QUICK_ACCESS'], $user->lang['ACP_STATISTICS']) : '');

				// Run parent enable step method
				return parent::enable_step($old_state);
			
			break;
			
			default:
			
				// Run parent enable step method
				return parent::enable_step($old_state);
				
			break;
		}
	}
}
