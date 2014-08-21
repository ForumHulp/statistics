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
		return isset($this->config['statistics_version']) && version_compare($this->config['statistics_version'], '3.1.0', '>=');
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
					),
				),
				$this->table_prefix . 'statistics_domains'	=> array(
					'COLUMNS'			=> array(
						'id'			=> array('UINT', NULL, 'auto_increment'),
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
						'id'			=> array('UINT', NULL, 'auto_increment'),
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
						'id'			=> array('UINT', NULL, 'auto_increment'),
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
						'id'			=> array('UINT', NULL, 'auto_increment'),
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
				$this->table_prefix . 'statistics_archive'
			)
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('statistics_version', '3.1.0')),
			array('config.add', array('delete_statistics_gc', 86400)),
			array('config.add', array('delete_statistics_last_gc', 0, 1)),
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
		$sql = 'INSERT INTO ' . $this->table_prefix . 'statistics_config' . ' (max_modules) VALUES(20)';
		$db->sql_query($sql);

		$db->sql_query('TRUNCATE TABLE ' . $this->table_prefix . 'statistics_domains');
		
		$sql = 'INSERT INTO ' . $this->table_prefix . 'statistics_domains VALUES
			(1, "ac", "Ascension Island"), (2, "ad", "Andorra"), (3, "ae", "United Arab Emirates"), (4, "af", "Afghanistan"), (5, "ag", "Antigua and Barbuda"), (6, "ai", "Anguilla"),
			(7, "al", "Albania"), (8, "am", "Armenia"), (9, "an", "Netherlands Antilles"), (10, "ao", "Angola"), (11, "aq", "Antartica"), (12, "ar", "Argentina"), 
			(13, "as", "American Samoa"), (14, "au", "Australia"), (15, "aw", "Aruba"), (16, "az", "Azerbaijan"), (17, "ba", "Bosnia and Herzegovina"), (18, "bb", "Barbados"), 
			(19, "bd", "Bangladesh"), (20, "be", "Belgium"), (21, "bf", "Burkina Faso"), (22, "bg", "Bulgaria"), (23, "bh", "Bahrain"), (24, "bi", "Burundi"), (25, "bj", "Benin"), 
			(26, "bm", "Bermuda"), (27, "bn", "Brunei Darussalam"), (28, "bo", "Bolivia"), (29, "br", "Brazil"), (30, "bs", "Bahamas"), (31, "bt", "Bhutan"), (32, "bv", "Bouvet Island"), 
			(33, "bw", "Botswana"), (34, "by", "Belarus"), (35, "bz", "Belize"), (36, "ca", "Canada"), (37, "cc", "Cocos (Keeling) Islands"), 
			(38, "cd", "Congo, The democratic republic of the"), (39, "cf", "Central African Republic"), (40, "cg", "Congo"), (41, "ch", "Switzerland"), (42, "ci", "Cote d\'Ivoire"), 
			(43, "ck", "Cook Islands"), (44, "cl", "Chile"), (45, "cm", "Cameroon"), (46, "cn", "China"), (47, "co", "Colombia"), (48, "cr", "Costa Rica"),(49, "cu", "Cuba"), 
			(50, "cv", "Cape Verde"), (51, "cx", "Christmas Island"), (52, "cy", "Cyprus"), (53, "cz", "Czech Republic"), (54, "de", "Germany"), (55, "dj", "Djibouti"), 
			(56, "dk", "Denmark"), (57, "dm", "Dominica"), (58, "do", "Dominican Republic"), (59, "dz", "Algeria"), (60, "ec", "Ecuador"), (61, "ee", "Estonia"), (62, "eg", "Egypt"), 
			(63, "eh", "Western Sahara"), (64, "er", "Eritrea"), (65, "es", "Spain"), (66, "et", "Ethiopia"), (67, "fi", "Finland"), (68, "fj", "Fiji"),
			(69, "fk", "Falkland Islands (Malvinas)"), (70, "fm", "Micronesia, Federal State of"), (71, "fo", "Faroe Islands"), (72, "fr", "France"), (73, "ga", "Gabon"), 
			(74, "gd", "Grenada"), (75, "ge", "Georgia"), (76, "gf", "French Guiana"), (77, "gg", "Guernsey"), (78, "gh", "Ghana"), (79, "gi", "Gibraltar"), (80, "gl", "Greenland"), 
			(81, "gm", "Gambia"), (82, "gn", "Guinea"), (83, "gp", "Guadeloupe"), (84, "gq", "Equatorial Guinea"), (85, "gr", "Greece"), 
			(86, "gs", "South Georgia and the South Sandwich Islands"), (87, "gt", "Guatemala"), (88, "gu", "Guam"), (89, "gw", "Guinea-Bissau"), (90, "gy", "Guyana"), 
			(91, "hk", "Hong Kong"), (92, "hm", "Heard and McDonald Islands"), (93, "hn", "Honduras"), (94, "hr", "Croatia"), (95, "ht", "Haiti"), (96, "hu", "Hungary"), 
			(97, "id", "Indonesia"), (98, "ie", "Ireland"), (99, "il", "Israel"), (100, "im", "Isle of Man"), (101, "in", "India"), (102, "io", "British Indian Ocean Territory"), 
			(103, "iq", "Iraq"), (104, "ir", "Iran, Islamic Republic of"), (105, "is", "Iceland"), (106, "it", "Italy"), (107, "je", "Jersey"), (108, "jm", "Jamaica"), 
			(109, "jo", "Jordan"), (110, "jp", "Japan"), (111, "ke", "Kenya"), (112, "kg", "Kyrgyzstan"), (113, "kh", "Cambodia"), (114, "ki", "Kiribati"), (115, "km", "Comoros"), 
			(116, "kn", "Saint Kitts and Nevis"), (117, "kp", "Korea, Democratic People\'s Republic of"), (118, "kr", "Korea, Republic of"), (119, "kw", "Kuwait"), 
			(120, "ky", "Cayman Islands"), (121, "kz", "Kazakhstan"), (122, "la", "Lao People\'s Democratic Republic"), (123, "lb", "Lebanon"), (124, "lc", "Saint Lucia"), 
			(125, "li", "Liechtenstein"), (126, "lk", "Sri Lanka"), (127, "lr", "Liberia"), (128, "ls", "Lesotho"), (129, "lt", "Lithuania"), (130, "lu", "Luxembourg"), 
			(131, "lv", "Latvia"), (132, "ly", "Libyan Arab Jamahiriya"), (133, "ma", "Morocco"), (134, "mc", "Monaco"), (135, "md", "Moldova, Republic of"), (136, "mg", "Madagascar"), 
			(137, "mh", "Marshall Islands"), (138, "mk", "Macedonia, The Former Yugoslav Republic of"), (139, "ml", "Mali"), (140, "mm", "Myanmar"), (141, "mn", "Mongolia"), 
			(142, "mo", "Macao"), (143, "mp", "Northern Mariana Islands"), (144, "mq", "Martinique"), (145, "mr", "Mauritania"), (146, "ms", "Montserrat"), (147, "mt", "Malta"),
			(148, "mu", "Mauritius"), (149, "mv", "Maldives"), (150, "mw", "Malawi"), (151, "mx", "Mexico"), (152, "my", "Malaysia"), (153, "mz", "Mozambique"),
			(154, "na", "Namibia"), (155, "nc", "New Caledonia"), (156, "ne", "Niger"), (157, "nf", "Norfolk Island"), (158, "ng", "Nigeria"), (159, "ni", "Nicaragua"),
			(160, "nl", "Netherlands"), (161, "no", "Norway"), (162, "np", "Nepal"), (163, "nr", "Nauru"), (164, "nu", "Niue"), (165, "nz", "New Zealand"), (166, "om", "Oman"),
			(167, "pa", "Panama"), (168, "pe", "Peru"), (169, "pf", "French Polynesia"), (170, "pg", "Papua New Guinea"), (171, "ph", "Philippines"), (172, "pk", "Pakistan"),
			(173, "pl", "Poland"), (174, "pm", "St. Pierre and Miquelon"), (175, "pn", "Pitcairn Island"), (176, "pr", "Puerto Rico"), (177, "pt", "Portugal"), (178, "pw", "Palau"),
			(179, "py", "Paraguay"), (180, "qa", "Qatar"), (181, "re", "Reunion"), (182, "ro", "Romania"), (183, "ru", "Russian Federation"), (184, "rw", "Rwanda"),
			(185, "sa", "Saudi Arabia"), (186, "sb", "Solomon Islands"), (187, "sc", "Seychelles"), (188, "sd", "Sudan"), (189, "se", "Sweden"), (190, "sg", "Singapore"),
			(191, "sh", "St. Helena"), (192, "si", "Slovenia"), (193, "sj", "Svalbard and Jan Mayen Islands"), (194, "sk", "Slovakia"), (195, "sl", "Sierra Leone"),
			(196, "sm", "San Marino"), (197, "sn", "Senegal"), (198, "so", "Somalia"), (199, "sr", "Suriname"), (200, "st", "Sao Tome and Principe"), (201, "sv", "El Salvador"),
			(202, "sy", "Syrian Arab Republic"), (203, "sz", "Swaziland"), (204, "tc", "Turks and Ciacos Islands"), (205, "td", "Chad"), (206, "tf", "French Southern Territories"),
			(207, "tg", "Togo"), (208, "th", "Thailand"), (209, "tj", "Tajikistan"), (210, "tk", "Tokelau"), (211, "tm", "Turkmenistan"), (212, "tn", "Tunisia"), (213, "to", "Tonga"),
			(214, "tp", "East Timor"), (215, "tr", "Turkey"), (216, "tt", "Trinidad and Tobago"), (217, "tv", "Tuvalu"), (218, "tw", "Taiwan, Province of China"), 
			(219, "tz", "Tanzania, United Republic of"), (220, "ua", "Ukraine"), (221, "ug", "Uganda"),  (222, "uk", "United Kingdom"), (223, "gb", "United Kingdom"), 
			(224, "um", "US Minor Outlying Islands"), (225, "us", "United States"), (226, "uy", "Uruguay"), (227, "uz", "Uzbekistan"), (228, "va", "Holy See (Vatican City State)"),
			(229, "vc", "Saint Vincent and the Grenadines"), (230, "ve", "Venezuela"), (231, "vg", "Virgin Islands (British)"), (232, "vi", "Virgin Islands (USA)"),
			(233, "vn", "Viet nam"), (234, "vu", "Vanuatu"), (235, "wf", "Wallis and Futuna Islands"), (236, "ws", "Western Samoa"), (237, "ye", "Yemen"), (238, "yt", "Mayotte"),
			(239, "yu", "Serbia and Montenegro"), (240, "za", "South Africa"), (241, "zm", "Zambia"), (242, "zr", "Zaire"), (243, "zw", "Zimbabwe"), (244, "com", "COM"),
			(245, "net", "NET"), (246, "org", "ORG"), (247, "edu", "Education"), (248, "int", "INT"), (249, "arpa", "ARPA"), (250, "at", "Austria"), (251, "gov", "Governement"),
			(252, "mil", "Miltary"), (253, "su", "Ex U.S.R.R."), (254, "reverse", "Reverse"), (255, "biz", "Businesses"), (256, "info", "INFO"), (257, "name", "NAME"),
			(258, "pro", "PRO"), (259, "coop", "COOP"), (260, "aero", "AERO"), (261, "museum", "MUSEUM"), (262, "tv", "TV"), (263, "cs", "Serbia and Montenegro"), 
			(264, "ps", "Palestinian Territory, Occupied"), (265, "ws", "Samoa")';		
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
