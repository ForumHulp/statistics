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
	'ACP_STATISTICS'	=> '掲示板統計情報',

	'ACP_ACP_STATISTICS_EXPLAIN'	=> '掲示板統計情報は訪問者の概要が表示されます。この拡張機能はユーザーのすべての訪問を記録します。Cronジョブは設定に応じてテーブルを切り詰めて深夜に統計情報を分析します。本日または全体的な統計情報を表示することが出来ます。グラフは統計情報の簡単な閲覧を持つそれぞれのページに表示されます。',

	'OPTIONS'		=> 'オプション',
	'COUNTRIES'		=> '国',
	'REFERRALS'		=> '参照',
	'SEARCHENG'		=> '検索エンジン',
	'SEARCHTERMS'	=> '検索語句',
	'BROWSERS'		=> 'ブラウザー',
	'CRAWLERS'		=> 'ウェブクローラー',
	'SYSTEMS'		=> 'OS',
	'MODULES'		=> 'モジュール',
	'AVERAGES'		=> '平均',
	'RESOLUTIONS'	=> '画面解像度',
	'OVERVIEW'		=> 'トップ10',
	'ADMIN'			=> '管理',
	'USERS'			=> 'ユーザー',
	'USERSTATS'		=> 'ユーザー統計グラフ',
	'LASTVISITS'	=> '最新の訪問ページ',
	'FL_DATE'		=> 'アーカイブテーブル情報',
	'UNIQUE'		=> 'Unique visitors',

	// Online
	'TIME'		=> '時間',
	'USER'		=> 'ユーザー',
	'MODULE'	=> 'モジュール',
	'COUNTRY'	=> '国',
	'HOST'		=> 'ホスト',
	'IP'		=> 'IPアドレス',

	// Module
	'MODULE_FORUM'	=> 'モジュール / フォーラム',
	'VIEWS'			=> '閲覧数',
	'PERC'			=> 'パーセント',
	'GRAPH'			=> 'グラフ',

	//Config
	'MAX_COUNTRIES'		=> '国',
	'MAX_REFERER'		=> '参照',
	'MAX_SE'			=> '検索エンジン',
	'MAX_SE_TERMS'		=> '検索語句',
	'MAX_BROWSERS'		=> 'ブラウザー',
	'MAX_CRAWL'			=> 'ウェブクローラー',
	'MAX_OS'			=> 'OS',
	'MAX_MODULES'		=> 'モジュール',
	'MAX_USERS'			=> 'ユーザー',
	'MAX_AVERAGES'		=> '平均',
	'MAX_SCREENS'		=> '画面解像度',
	'MAX_ONLINE'		=> 'オンライン',
	'DELL'				=> '削除',
	'SEARCHENG_EXPLAIN'	=> '検索エンジンを変更、追加、削除します。',

	'MAX_COUNTRIES_EXPLAIN'		=> 'モジュールでの説明を参照してください。',
	'MAX_REFERER_EXPLAIN'		=> 'モジュールでの説明を参照してください。',
	'MAX_SE_EXPLAIN'			=> 'モジュールでの説明を参照してください。',
	'MAX_SE_TERMS_EXPLAIN'		=> 'モジュールでの説明を参照してください。',
	'MAX_BROWSERS_EXPLAIN'		=> 'モジュールでの説明を参照してください。',
	'MAX_CRAWL_EXPLAIN'			=> 'モジュールでの説明を参照してください。',
	'MAX_OS_EXPLAIN'			=> 'モジュールでの説明を参照してください。',
	'MAX_MODULES_EXPLAIN'		=> 'ページネーションの前に表示する順番、切り詰めのテーブルの最大レコード数です。',
	'MAX_USERS_EXPLAIN'			=> 'モジュールでの説明を参照してください。',
	'MAX_AVERAGES_EXPLAIN'		=> 'モジュールでの説明を参照してください。',
	'MAX_SCREENS_EXPLAIN'		=> 'モジュールでの説明を参照してください。',
	'MAX_ONLINE_EXPLAIN'		=> 'モジュールでの説明を参照してください。',

	'CUSTOM_PAGES'				=> 'カスタムページ',
	'CUSTOM_PAGES_EXPLAIN'		=> '掲示板統計情報に表示する言語の変数及びカスタムページ名を記入します。削除または変更をするために拡張機能を選択します。',

	'START_SCREEN'				=> 'スタート画面',
	'START_SCREEN_EXPLAIN'		=> 'アーカイブまたはオンラインを表示したい場合には、掲示板統計情報のスタート画面を選択します。',

	'BOTS_INC'					=> 'ボットを含める',
	'BOTS_INC_EXPLAIN'			=> 'オンライン表示にボットを含めます。',

	'LOG'						=> 'Logs',
	'LOG_EXPLAIN'				=> 'Add logs to maintenance log.',

	'DEL_STAT'					=> 'アーカイブテーブルを空にする',
	'DEL_STAT_EXPLAIN'			=> 'アーカイブテーブルを空にして掲示板統計情報をリセットします。',

	'STAT_DELETE_CONFIRM'		=> 'アーカイブテーブルを空にしますか？',

	'BS_STATUS_TIMEOUT'			=> 'Refresh timeout',
	'BS_STATUS_ERROR'			=> 'Refresh error',
	'BS_STATUS_ERROR_EXPLAIN'	=> 'An error occurred during refreshing the page.',
));
