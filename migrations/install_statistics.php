<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\statistics\migrations;

class install_statistics extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['statistics_version']) && version_compare($this->config['statistics_version'], '3.1.0.RC5', '>=');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_schema()
	{
		return array(
			// We have to create our own tables
			'add_tables'	=> array(
				$this->table_prefix . 'statistics_config'	=> array(
					'COLUMNS'			=> array(
						'max_modules'		=> array('UINT:4', 20),
						'max_browsers'		=> array('UINT:4', 20),
						'max_os'			=> array('UINT:4', 20),
						'max_countries'		=> array('UINT:4', 20),
						'max_users'			=> array('UINT:4', 20),
						'max_screens'		=> array('UINT:4', 20),
						'max_referer'		=> array('UINT:4', 20),
						'max_se_terms'		=> array('UINT:4', 20),
						'max_se'			=> array('UINT:4', 20),
						'max_crawl'			=> array('UINT:4', 20),
						'max_online'		=> array('UINT:4', 20),

						't_modules'			=> array('UINT:4', 100),
						't_browsers'		=> array('UINT:4', 100),
						't_os'				=> array('UINT:4', 100),
						't_countries'		=> array('UINT:4', 100),
						't_users'			=> array('UINT:4', 100),
						't_screens'			=> array('UINT:4', 100),
						't_referer'			=> array('UINT:4', 100),
						't_se_terms'		=> array('UINT:4', 100),
						't_se'				=> array('UINT:4', 100),
						't_crawl'			=> array('UINT:4', 100),
						't_online'			=> array('UINT:4', 100),
						'custom_pages'		=> array('MTEXT_UNI', ''),
						'start_screen'		=> array('VCHAR:25', 'default'),
						'archive'			=> array('UINT:4', 0),
						'botsinc'			=> array('UINT:4', 0),
					),
				),
				$this->table_prefix . 'statistics_domains'	=> array(
					'COLUMNS'			=> array(
						'id'			=> array('UINT', null, 'auto_increment'),
						'domain'		=> array('VCHAR:20', ''),
						'description'	=> array('VCHAR:50', ''),
					),
					'PRIMARY_KEY'		=> 'id',
					'KEYS'				=> array(
						'domain'		=> array('INDEX', 'domain'),
					)
				),
				$this->table_prefix . 'statistics_se'	=> array(
					'COLUMNS'			=> array(
						'id'			=> array('UINT', null, 'auto_increment'),
						'name'			=> array('VCHAR:25', ''),
						'query'			=> array('VCHAR:255', ''),
					),
					'PRIMARY_KEY'		=> 'id',
					'KEYS'				=> array(
						'name'		=> array('INDEX', 'name'),
					)
				),
				$this->table_prefix . 'statistics_online'	=> array(
					'COLUMNS'			=> array(
						'id'			=> array('UINT', null, 'auto_increment'),
						'time'			=> array('TIMESTAMP', 0),
						'uname' 		=> array('VCHAR:25', ''),
						'agent' 		=> array('VCHAR:255', ''),
						'ip_addr' 		=> array('VCHAR:25', ''),
						'host' 			=> array('VCHAR:100', ''),
						'domain'		=> array('VCHAR:20', ''),
						'module'		=> array('VCHAR:50', ''),
						'scr_res'		=> array('VCHAR:25', ''),
						'page'			=> array('VCHAR:255', ''),
						'referer'		=> array('VCHAR:500', ''),
						'se_terms' 		=> array('VCHAR:500', ''),
					),
					'PRIMARY_KEY'		=> 'id',
					'KEYS'          	=> array(
						'time'			=> array('INDEX', 'time'),
					)
				),
				$this->table_prefix . 'statistics_archive'	=> array(
					'COLUMNS'			=> array(
						'id'			=> array('UINT', null, 'auto_increment'),
						'cat'			=> array('UINT:4', 0),
						'name'			=> array('VCHAR:255', ''),
						'hits'			=> array('UINT:8', 0),
						'first'			=> array('UINT:11', 0),
						'last'			=> array('UINT:11', 0),
					),
					'PRIMARY_KEY'		=> 'id',
					'KEYS'				=> array(
						'cat'		=> array('INDEX', 'cat'),
						'name'		=> array('INDEX', 'name'),
						'last'		=> array('INDEX', 'last'),
					)
				),
				$this->table_prefix . 'statistics'	=> array(
					'COLUMNS'			=> array(
						'year'			=> array('UINT:4', 0),
						'month'			=> array('UINT:4', 0),
						'day'			=> array('UINT:4', 0),
						'hits'			=> array('UINT:11', 0),
					),
					'KEYS'			=> array(
						'id'		=> array('UNIQUE', array('year', 'month', 'day')),
						'year'		=> array('INDEX', 'year'),
						'month'		=> array('INDEX', 'month'),
						'day'		=> array('INDEX', 'day'),
					)
				),
			)
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables' => array(
				$this->table_prefix . 'statistics_config',
				$this->table_prefix . 'statistics_domains',
				$this->table_prefix . 'statistics_online',
				$this->table_prefix . 'statistics_archive',
				$this->table_prefix . 'statistics_se',
				$this->table_prefix . 'statistics'
			)
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('statistics_version', '3.1.0.RC5')),
			array('config.add', array('delete_statistics_gc', 86400)),
			array('config.add', array('delete_statistics_last_gc', 0, 1)),
			array('config.add', array('statistics_archive', 0, 1)),
			array('module.add', array(
				'acp', 'ACP_QUICK_ACCESS', array(
					'module_basename'	=> '\forumhulp\statistics\acp\statistics_module',
					'auth'				=> 'ext_forumhulp/statistics && acl_a_viewlogs',
					'modes'				=> array('stat'),
				),
			)),
			array('custom', array(
				array(&$this, 'update_tables')
			)),
		);
	}

	public function update_tables()
	{
		global $db;
		// before we fill anything in this table, we truncate it.
		$db->sql_query('TRUNCATE TABLE ' . $this->table_prefix . 'statistics_config');
		$sql = 'INSERT INTO ' . $this->table_prefix . 'statistics_config' . ' (custom_pages) VALUES("a:0:{}")';
		$db->sql_query($sql);

		$db->sql_query('TRUNCATE TABLE ' . $this->table_prefix . 'statistics_domains');

		$sql = 'INSERT INTO ' . $this->table_prefix . 'statistics_domains VALUES
				(1, "ac", "Ascension Island"), (2, "ad", "Andorra"), (3, "ae", "United Arab Emirates"), (4, "af", "Afghanistan"), (5, "ag", "Antigua and Barbuda"), (6, "ai", "Anguilla"),
				(7, "al", "Albania"), (8, "am", "Armenia"), (9, "an", "Netherlands Antilles"), (10, "ao", "Angola"), (11, "aq", "Antarctica"), (12, "ar", "Argentina"), 
				(13, "as", "American Samoa"), (14, "at", "Austria"), (15, "au", "Australia"), (16, "aw", "Aruba"), (17, "ax", "land"), (18, "az", "Azerbaijan"), 
				(19, "ba", "Bosnia and Herzegovina"), (20, "bb", "Barbados"), (21, "bd", "Bangladesh"), (22, "be", "Belgium"), (23, "bf", "Burkina Faso"), (24, "bg", "Bulgaria"),
				(25, "bh", "Bahrain"), (26, "bi", "Burundi"), (27, "bj", "Benin"), (28, "bm", "Bermuda"), (29, "bn", "Brunei"), (30, "bo", "Bolivia"), (31, "br", "Brazil"),
				(32, "bs", "Bahamas"), (33, "bt", "Bhutan"), (34, "bv", "Bouvet Island"), (35, "bw", "Botswana"), (36, "by", "Belarus"), (37, "bz", "Belize"), (38, "ca", "Canada"),
				(39, "cc", "Cocos (Keeling) Islands"), (40, "cd", "Democratic Republic of the Congo"), (41, "cf", "Central African Republic"), (42, "cg", "Republic of the Congo"),
				(43, "ch", "Switzerland"), (44, "ci", "Côte d\'Ivoire"), (45, "ck", "Cook Islands"), (46, "cl", "Chile"), (47, "cm", "Cameroon"), (48, "cn", "People\'s Republic of China"),
				(49, "co", "Colombia"), (50, "cr", "Costa Rica"), (51, "cs", "Czechoslovakia"), (52, "cu", "Cuba"), (53, "cv", "Cape Verde"), (54, "cw", "Cura‡ao"), 
				(55, "cx", "Christmas Island"), (56, "cy", "Cyprus"), (57, "cz", "Czech Republic"), (58, "dd", "East Germany"), (59, "de", "Germany"), (60, "dj", "Djibouti"),
				(61, "dk", "Denmark"), (62, "dm", "Dominica"), (63, "do", "Dominican Republic"), (64, "dz", "Algeria"), (65, "ec", "Ecuador"), (66, "ee", "Estonia"),(67, "eg", "Egypt"),
				(68, "eh", "Western Sahara"), (69, "er", "Eritrea"), (70, "es", "Spain"), (71, "et", "Ethiopia"), (72, "eu", "European Union"), (73, "fi", "Finland"), (74, "fj", "Fiji"),
				(75, "fk", "Falkland Islands"), (76, "fm", "Federated States of Micronesia"), (77, "fo", "Faroe Islands"), (78, "fr", "France"), (79, "ga", "Gabon"), 
				(80, "gb", "United Kingdom"), (81, "gd", "Grenada"), (82, "ge", "Georgia"), (83, "gf", "French Guiana"), (84, "gg", "Guernsey"), (85, "gh", "Ghana"), 
				(86, "gi", "Gibraltar"), (87, "gl", "Greenland"), (88, "gm", "The Gambia"), (89, "gn", "Guinea"), (90, "gp", "Guadeloupe"), (91, "gq", "Equatorial Guinea"),
				(92, "gr", "Greece"), (93, "gs", "South Georgia and the South Sandwich Islands"), (94, "gt", "Guatemala"), (95, "gu", "Guam"), (96, "gw", "Guinea-Bissau"),
				(97, "gy", "Guyana"), (98, "hk", "Hong Kong"), (99, "hm", "Heard Island and McDonald Islands"), (100, "hn", "Honduras"), (101, "hr", "Croatia"), (102, "ht", "Haiti"),
				(103, "hu", "Hungary"), (104, "id", "Indonesia"), (105, "ie", "Ireland"), (106, "il", "Israel"), (107, "im", "Isle of Man"), (108, "in", "India"), 
				(109, "io", "British Indian Ocean Territory"), (110, "iq", "Iraq"), (111, "ir", "Iran"), (112, "is", "Iceland"), (113, "it", "Italy"), (114, "je", "Jersey"),
				(115, "jm", "Jamaica"), (116, "jo", "Jordan"), (117, "jp", "Japan"), (118, "ke", "Kenya"), (119, "kg", "Kyrgyzstan"), (120, "kh", "Cambodia"), (121, "ki", "Kiribati"),
				(122, "km", "Comoros"), (123, "kn", "Saint Kitts and Nevis"), (124, "kp", "Democratic People\'s Republic of Korea"), (125, "kr", "Republic of Korea"), (126, "kw", "Kuwait"),
				(127, "ky", "Cayman Islands"), (128, "kz", "Kazakhstan"), (129, "la", "Laos"), (130, "lb", "Lebanon"), (131, "lc", "Saint Lucia"), (132, "li", "Liechtenstein"),
				(133, "lk", "Sri Lanka"), (134, "lr", "Liberia"), (135, "ls", "Lesotho"), (136, "lt", "Lithuania"), (137, "lu", "Luxembourg"), (138, "lv", "Latvia"), (139, "ly", "Libya"),
				(140, "ma", "Morocco"), (141, "mc", "Monaco"), (142, "md", "Moldova"), (143, "me", "Montenegro"), (144, "mg", "Madagascar"), (145, "mh", "Marshall Islands"),
				(146, "mk", "Macedonia"), (147, "ml", "Mali"), (148, "mm", "Myanmar"), (149, "mn", "Mongolia"), (150, "mo", "Macau"), (151, "mp", "Northern Mariana Islands"),
				(152, "mq", "Martinique"), (153, "mr", "Mauritania"), (154, "ms", "Montserrat"), (155, "mt", "Malta"), (156, "mu", "Mauritius"), (157, "mv", "Maldives"), 
				(158, "mw", "Malawi"), (159, "mx", "Mexico"), (160, "my", "Malaysia"), (161, "mz", "Mozambique"), (162, "na", "Namibia"), (163, "nc", "New Caledonia"), (164, "ne", "Niger"),
				(165, "nf", "Norfolk Island"), (166, "ng", "Nigeria"), (167, "ni", "Nicaragua"), (168, "nl", "Netherlands"), (169, "no", "Norway"), (170, "np", "Nepal"), 
				(171, "nr", "Nauru"), (172, "nu", "Niue"), (173, "nz", "New Zealand"), (174, "om", "Oman"), (175, "pa", "Panama"), (176, "pe", "Peru"), (177, "pf", "French Polynesia"),
				(178, "pg", "Papua New Guinea"), (179, "ph", "Philippines"), (180, "pk", "Pakistan"), (181, "pl", "Poland"), (182, "pm", "Saint-Pierre and Miquelon"), 
				(183, "pn", "Pitcairn Islands"), (184, "pr", "Puerto Rico"), (185, "ps", "State of Palestine[19]"), (186, "pt", "Portugal"), (187, "pw", "Palau"), (188, "py", "Paraguay"),
				(189, "qa", "Qatar"), (190, "re", "R‚union"), (191, "ro", "Romania"), (192, "rs", "Serbia"), (193, "ru", "Russia"), (194, "rw", "Rwanda"), (195, "sa", "Saudi Arabia"),
				(196, "sb", "Solomon Islands"), (197, "sc", "Seychelles"), (198, "sd", "Sudan"), (199, "se", "Sweden"), (200, "sg", "Singapore"), (201, "sh", "Saint Helena"),
				(202, "si", "Slovenia"), (203, "sj", "Svalbard and Jan Mayen Islands"), (204, "sk", "Slovakia"), (205, "sl", "Sierra Leone"), (206, "sm", "San Marino"), 
				(207, "sn", "Senegal"), (208, "so", "Somalia"),	(209, "sr", "Suriname"), (210, "ss", "South Sudan"), (211, "st", "São Tomé and Príncipe"), (212, "su", "Soviet Union"),
				(213, "sv", "El Salvador"), (214, "sx", "Sint Maarten"), (215, "sy", "Syria"), (216, "sz", "Swaziland"), (217, "tc", "Turks and Caicos Islands"), (218, "td", "Chad"),
				(219, "tf", "French Southern and Antarctic Lands"), (220, "tg", "Togo"), (221, "th", "Thailand"), (222, "tj", "Tajikistan"), (223, "tk", "Tokelau"), 
				(224, "tl", "East Timor"), (225, "tm", "Turkmenistan"), (226, "tn", "Tunisia"), (227, "to", "Tonga"), (228, "tp", "East Timor"), (229, "tr", "Turkey"),
				(230, "tt", "Trinidad and Tobago"), (231, "tv", "Tuvalu"), (232, "tw", "Taiwan"), (233, "tz", "Tanzania"), (234, "ua", "Ukraine"), (235, "ug", "Uganda"),
				(236, "uk", "United Kingdom"), (237, "us", "United States of America"), (238, "uy", "Uruguay"), (239, "uz", "Uzbekistan"), (240, "va", "Vatican City"), 
				(241, "vc", "Saint Vincent and the Grenadines"), (242, "ve", "Venezuela"), (243, "vg", "British Virgin Islands"), (244, "vi", "United States Virgin Islands"),
				(245, "vn", "Vietnam"), (246, "vu", "Vanuatu"), (247, "wf", "Wallis and Futuna"), (248, "ws", "Samoa"), (249, "ye", "Yemen"), (250, "yt", "Mayotte"), 
				(251, "yu", "SFR Yugoslavia"), (252, "za", "South Africa"), (253, "zm", "Zambia"), (254, "zw", "Zimbabwe"), (255, "com", "Commercial"),	(256, "org", "Organization"), 
				(257, "net", "Network"), (258, "lo", "Localhost"), (259, "un", "Unknown")';
		$db->sql_query($sql);

		$db->sql_query('TRUNCATE TABLE ' . $this->table_prefix . 'statistics_se');
		$sql = 'INSERT INTO ' . $this->table_prefix . 'statistics_se' . ' VALUES (1, "google", "q"), (2, "vinden", "q"), (3, "voelspriet", "qr"), (4, "zoeken.track", "q"),
			(5, "zoekveilig", "zoek"), (6, "zoek", "q"), (7, "eerstekeuze", "Terms"), (8, "infoseek.go", "Keywords"), (9, "altavista", "q"), 
			(10, "srch.overture", "Keywords"), (11, "overture", "Keywords"), (12, "search.lycos", "query"), (13, "zoek.lycos", "query"), (14, "ilse", "search_for"),
			(15, "vindex", "search_for"), (16, "search.yahoo", "p"), (17, "search.msn", "q"), (18, "alltheweb", "q"), (19, "aolrecherche.aol", "q"),
			(20, "hotbot", "query"), (21, "alexa", "q"), (22, "mysearch", "searchfor"), (23, "search.live", "q"), (24, "search.sweetim", "q"),
			(25, "bing", "q"), (26, "forumhulp", "keywords")';
		$db->sql_query($sql);
	}
}
