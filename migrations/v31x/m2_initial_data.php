<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license Proprietary
*
*/

namespace forumhulp\statistics\migrations\v31x;

/**
 * Migration stage 2: Initial data
 */
class m2_initial_data extends \phpbb\db\migration\migration
{
	/**
	 * Assign migration file dependencies for this migration
	 *
	 * @return array Array of migration files
	 * @static
	 * @access public
	 */
	static public function depends_on()
	{
		return array('\forumhulp\statistics\migrations\v31x\m1_initial_schema');
	}

	/**
	 * Add or update data in the database
	 *
	 * @return array Array of table data
	 * @access public
	 */
	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'update_tables'))),
		);
	}

	/**
	 * Add countries to the database
	 *
	 * @return null
	 * @access public
	 */
	public function update_tables()
	{
		// Load the insert buffer class to perform a buffered multi insert
		$insert_buffer = new \phpbb\db\sql_insert_buffer($this->db, $this->table_prefix . 'statistics_domains');

		$countries = array(
			1 => array('domain' => 'ac', 'description' => 'Ascension Island'),
			2 => array('domain' => 'ad', 'description' => 'Andorra'),
			3 => array('domain' => 'ae', 'description' => 'United Arab Emirates'),
			4 => array('domain' => 'af', 'description' => 'Afghanistan'),
			5 => array('domain' => 'ag', 'description' => 'Antigua and Barbuda'),
			6 => array('domain' => 'ai', 'description' => 'Anguilla'),
			7 => array('domain' => 'al', 'description' => 'Albania'),
			8 => array('domain' => 'am', 'description' => 'Armenia'),
			9 => array('domain' => 'an', 'description' => 'Netherlands Antilles'),
			10 => array('domain' => 'ao', 'description' => 'Angola'),
			11 => array('domain' => 'aq', 'description' => 'Antarctica'),
			12 => array('domain' => 'ar', 'description' => 'Argentina'),
			13 => array('domain' => 'as', 'description' => 'American Samoa'),
			14 => array('domain' => 'at', 'description' => 'Austria'),
			15 => array('domain' => 'au', 'description' => 'Australia'),
			16 => array('domain' => 'aw', 'description' => 'Aruba'),
			17 => array('domain' => 'ax', 'description' => 'land'),
			18 => array('domain' => 'az', 'description' => 'Azerbaijan'),
			19 => array('domain' => 'ba', 'description' => 'Bosnia and Herzegovina'),
			20 => array('domain' => 'bb', 'description' => 'Barbados'),
			21 => array('domain' => 'bd', 'description' => 'Bangladesh'),
			22 => array('domain' => 'be', 'description' => 'Belgium'),
			23 => array('domain' => 'bf', 'description' => 'Burkina Faso'),
			24 => array('domain' => 'bg', 'description' => 'Bulgaria'),
			25 => array('domain' => 'bh', 'description' => 'Bahrain'),
			26 => array('domain' => 'bi', 'description' => 'Burundi'),
			27 => array('domain' => 'bj', 'description' => 'Benin'),
			28 => array('domain' => 'bm', 'description' => 'Bermuda'),
			29 => array('domain' => 'bn', 'description' => 'Brunei'),
			30 => array('domain' => 'bo', 'description' => 'Bolivia'),
			31 => array('domain' => 'br', 'description' => 'Brazil'),
			32 => array('domain' => 'bs', 'description' => 'Bahamas'),
			33 => array('domain' => 'bt', 'description' => 'Bhutan'),
			34 => array('domain' => 'bv', 'description' => 'Bouvet Island'),
			35 => array('domain' => 'bw', 'description' => 'Botswana'),
			36 => array('domain' => 'by', 'description' => 'Belarus'),
			37 => array('domain' => 'bz', 'description' => 'Belize'),
			38 => array('domain' => 'ca', 'description' => 'Canada'),
			39 => array('domain' => 'cc', 'description' => 'Cocos (Keeling) Islands'),
			40 => array('domain' => 'cd', 'description' => 'Democratic Republic of the Congo'),
			41 => array('domain' => 'cf', 'description' => 'Central African Republic'),
			42 => array('domain' => 'cg', 'description' => 'Republic of the Congo'),
			43 => array('domain' => 'ch', 'description' => 'Switzerland'),
			44 => array('domain' => 'ci', 'description' => 'Côte d\'Ivoire'),
			45 => array('domain' => 'ck', 'description' => 'Cook Islands'),
			46 => array('domain' => 'cl', 'description' => 'Chile'),
			47 => array('domain' => 'cm', 'description' => 'Cameroon'),
			48 => array('domain' => 'cn', 'description' => 'People\'s Republic of China'),
			49 => array('domain' => 'co', 'description' => 'Colombia'),
			50 => array('domain' => 'cr', 'description' => 'Costa Rica'),
			51 => array('domain' => 'cs', 'description' => 'Czechoslovakia'),
			52 => array('domain' => 'cu', 'description' => 'Cuba'),
			53 => array('domain' => 'cv', 'description' => 'Cape Verde'),
			54 => array('domain' => 'cw', 'description' => 'Cura‡ao'),
			55 => array('domain' => 'cx', 'description' => 'Christmas Island'),
			56 => array('domain' => 'cy', 'description' => 'Cyprus'),
			57 => array('domain' => 'cz', 'description' => 'Czech Republic'),
			58 => array('domain' => 'dd', 'description' => 'East Germany'),
			59 => array('domain' => 'de', 'description' => 'Germany'),
			60 => array('domain' => 'dj', 'description' => 'Djibouti'),
			61 => array('domain' => 'dk', 'description' => 'Denmark'),
			62 => array('domain' => 'dm', 'description' => 'Dominica'),
			63 => array('domain' => 'do', 'description' => 'Dominican Republic'),
			64 => array('domain' => 'dz', 'description' => 'Algeria'),
			65 => array('domain' => 'ec', 'description' => 'Ecuador'),
			66 => array('domain' => 'ee', 'description' => 'Estonia'),
			67 => array('domain' => 'eg', 'description' => 'Egypt'),
			68 => array('domain' => 'eh', 'description' => 'Western Sahara'),
			69 => array('domain' => 'er', 'description' => 'Eritrea'),
			70 => array('domain' => 'es', 'description' => 'Spain'),
			71 => array('domain' => 'et', 'description' => 'Ethiopia'),
			72 => array('domain' => 'eu', 'description' => 'European Union'),
			73 => array('domain' => 'fi', 'description' => 'Finland'),
			74 => array('domain' => 'fj', 'description' => 'Fiji'),
			75 => array('domain' => 'fk', 'description' => 'Falkland Islands'),
			76 => array('domain' => 'fm', 'description' => 'Federated States of Micronesia'),
			77 => array('domain' => 'fo', 'description' => 'Faroe Islands'),
			78 => array('domain' => 'fr', 'description' => 'France'),
			79 => array('domain' => 'ga', 'description' => 'Gabon'),
			80 => array('domain' => 'gb', 'description' => 'United Kingdom'),
			81 => array('domain' => 'gd', 'description' => 'Grenada'),
			82 => array('domain' => 'ge', 'description' => 'Georgia'),
			83 => array('domain' => 'gf', 'description' => 'French Guiana'),
			84 => array('domain' => 'gg', 'description' => 'Guernsey'),
			85 => array('domain' => 'gh', 'description' => 'Ghana'),
			86 => array('domain' => 'gi', 'description' => 'Gibraltar'),
			87 => array('domain' => 'gl', 'description' => 'Greenland'),
			88 => array('domain' => 'gm', 'description' => 'The Gambia'),
			89 => array('domain' => 'gn', 'description' => 'Guinea'),
			90 => array('domain' => 'gp', 'description' => 'Guadeloupe'),
			91 => array('domain' => 'gq', 'description' => 'Equatorial Guinea'),
			92 => array('domain' => 'gr', 'description' => 'Greece'),
			93 => array('domain' => 'gs', 'description' => 'South Georgia and the South Sandwich Islands'),
			94 => array('domain' => 'gt', 'description' => 'Guatemala'),
			95 => array('domain' => 'gu', 'description' => 'Guam'),
			96 => array('domain' => 'gw', 'description' => 'Guinea-Bissau'),
			97 => array('domain' => 'gy', 'description' => 'Guyana'),
			98 => array('domain' => 'hk', 'description' => 'Hong Kong'),
			99 => array('domain' => 'hm', 'description' => 'Heard Island and McDonald Islands'),
			100 => array('domain' => 'hn', 'description' => 'Honduras'),
			101 => array('domain' => 'hr', 'description' => 'Croatia'),
			102 => array('domain' => 'ht', 'description' => 'Haiti'),
			103 => array('domain' => 'hu', 'description' => 'Hungary'),
			104 => array('domain' => 'id', 'description' => 'Indonesia'),
			105 => array('domain' => 'ie', 'description' => 'Ireland'),
			106 => array('domain' => 'il', 'description' => 'Israel'),
			107 => array('domain' => 'im', 'description' => 'Isle of Man'),
			108 => array('domain' => 'in', 'description' => 'India'),
			109 => array('domain' => 'io', 'description' => 'British Indian Ocean Territory'),
			110 => array('domain' => 'iq', 'description' => 'Iraq'),
			111 => array('domain' => 'ir', 'description' => 'Iran'),
			112 => array('domain' => 'is', 'description' => 'Iceland'),
			113 => array('domain' => 'it', 'description' => 'Italy'),
			114 => array('domain' => 'je', 'description' => 'Jersey'),
			115 => array('domain' => 'jm', 'description' => 'Jamaica'),
			116 => array('domain' => 'jo', 'description' => 'Jordan'),
			117 => array('domain' => 'jp', 'description' => 'Japan'),
			118 => array('domain' => 'ke', 'description' => 'Kenya'),
			119 => array('domain' => 'kg', 'description' => 'Kyrgyzstan'),
			120 => array('domain' => 'kh', 'description' => 'Cambodia'),
			121 => array('domain' => 'ki', 'description' => 'Kiribati'),
			122 => array('domain' => 'km', 'description' => 'Comoros'),
			123 => array('domain' => 'kn', 'description' => 'Saint Kitts and Nevis'),
			124 => array('domain' => 'kp', 'description' => 'Democratic People\'s Republic of Korea'),
			125 => array('domain' => 'kr', 'description' => 'Republic of Korea'),
			126 => array('domain' => 'kw', 'description' => 'Kuwait'),
			127 => array('domain' => 'ky', 'description' => 'Cayman Islands'),
			128 => array('domain' => 'kz', 'description' => 'Kazakhstan'),
			129 => array('domain' => 'la', 'description' => 'Laos'),
			130 => array('domain' => 'lb', 'description' => 'Lebanon'),
			131 => array('domain' => 'lc', 'description' => 'Saint Lucia'),
			132 => array('domain' => 'li', 'description' => 'Liechtenstein'),
			133 => array('domain' => 'lk', 'description' => 'Sri Lanka'),
			134 => array('domain' => 'lr', 'description' => 'Liberia'),
			135 => array('domain' => 'ls', 'description' => 'Lesotho'),
			136 => array('domain' => 'lt', 'description' => 'Lithuania'),
			137 => array('domain' => 'lu', 'description' => 'Luxembourg'),
			138 => array('domain' => 'lv', 'description' => 'Latvia'),
			139 => array('domain' => 'ly', 'description' => 'Libya'),
			140 => array('domain' => 'ma', 'description' => 'Morocco'),
			141 => array('domain' => 'mc', 'description' => 'Monaco'),
			142 => array('domain' => 'md', 'description' => 'Moldova'),
			143 => array('domain' => 'me', 'description' => 'Montenegro'),
			144 => array('domain' => 'mg', 'description' => 'Madagascar'),
			145 => array('domain' => 'mh', 'description' => 'Marshall Islands'),
			146 => array('domain' => 'mk', 'description' => 'Macedonia'),
			147 => array('domain' => 'ml', 'description' => 'Mali'),
			148 => array('domain' => 'mm', 'description' => 'Myanmar'),
			149 => array('domain' => 'mn', 'description' => 'Mongolia'),
			150 => array('domain' => 'mo', 'description' => 'Macau'),
			151 => array('domain' => 'mp', 'description' => 'Northern Mariana Islands'),
			152 => array('domain' => 'mq', 'description' => 'Martinique'),
			153 => array('domain' => 'mr', 'description' => 'Mauritania'),
			154 => array('domain' => 'ms', 'description' => 'Montserrat'),
			155 => array('domain' => 'mt', 'description' => 'Malta'),
			156 => array('domain' => 'mu', 'description' => 'Mauritius'),
			157 => array('domain' => 'mv', 'description' => 'Maldives'),
			158 => array('domain' => 'mw', 'description' => 'Malawi'),
			159 => array('domain' => 'mx', 'description' => 'Mexico'),
			160 => array('domain' => 'my', 'description' => 'Malaysia'),
			161 => array('domain' => 'mz', 'description' => 'Mozambique'),
			162 => array('domain' => 'na', 'description' => 'Namibia'),
			163 => array('domain' => 'nc', 'description' => 'New Caledonia'),
			164 => array('domain' => 'ne', 'description' => 'Niger'),
			165 => array('domain' => 'nf', 'description' => 'Norfolk Island'),
			166 => array('domain' => 'ng', 'description' => 'Nigeria'),
			167 => array('domain' => 'ni', 'description' => 'Nicaragua'),
			168 => array('domain' => 'nl', 'description' => 'Netherlands'),
			169 => array('domain' => 'no', 'description' => 'Norway'),
			170 => array('domain' => 'np', 'description' => 'Nepal'),
			171 => array('domain' => 'nr', 'description' => 'Nauru'),
			172 => array('domain' => 'nu', 'description' => 'Niue'),
			173 => array('domain' => 'nz', 'description' => 'New Zealand'),
			174 => array('domain' => 'om', 'description' => 'Oman'),
			175 => array('domain' => 'pa', 'description' => 'Panama'),
			176 => array('domain' => 'pe', 'description' => 'Peru'),
			177 => array('domain' => 'pf', 'description' => 'French Polynesia'),
			178 => array('domain' => 'pg', 'description' => 'Papua New Guinea'),
			179 => array('domain' => 'ph', 'description' => 'Philippines'),
			180 => array('domain' => 'pk', 'description' => 'Pakistan'),
			181 => array('domain' => 'pl', 'description' => 'Poland'),
			182 => array('domain' => 'pm', 'description' => 'Saint-Pierre and Miquelon'),
			183 => array('domain' => 'pn', 'description' => 'Pitcairn Islands'),
			184 => array('domain' => 'pr', 'description' => 'Puerto Rico'),
			185 => array('domain' => 'ps', 'description' => 'State of Palestine[19]'),
			186 => array('domain' => 'pt', 'description' => 'Portugal'),
			187 => array('domain' => 'pw', 'description' => 'Palau'),
			188 => array('domain' => 'py', 'description' => 'Paraguay'),
			189 => array('domain' => 'qa', 'description' => 'Qatar'),
			190 => array('domain' => 're', 'description' => 'R‚union'),
			191 => array('domain' => 'ro', 'description' => 'Romania'),
			192 => array('domain' => 'rs', 'description' => 'Serbia'),
			193 => array('domain' => 'ru', 'description' => 'Russia'),
			194 => array('domain' => 'rw', 'description' => 'Rwanda'),
			195 => array('domain' => 'sa', 'description' => 'Saudi Arabia'),
			196 => array('domain' => 'sb', 'description' => 'Solomon Islands'),
			197 => array('domain' => 'sc', 'description' => 'Seychelles'),
			198 => array('domain' => 'sd', 'description' => 'Sudan'),
			199 => array('domain' => 'se', 'description' => 'Sweden'),
			200 => array('domain' => 'sg', 'description' => 'Singapore'),
			201 => array('domain' => 'sh', 'description' => 'Saint Helena'),
			202 => array('domain' => 'si', 'description' => 'Slovenia'),
			203 => array('domain' => 'sj', 'description' => 'Svalbard and Jan Mayen Islands'),
			204 => array('domain' => 'sk', 'description' => 'Slovakia'),
			205 => array('domain' => 'sl', 'description' => 'Sierra Leone'),
			206 => array('domain' => 'sm', 'description' => 'San Marino'),
			207 => array('domain' => 'sn', 'description' => 'Senegal'),
			208 => array('domain' => 'so', 'description' => 'Somalia'),
			209 => array('domain' => 'sr', 'description' => 'Suriname'),
			210 => array('domain' => 'ss', 'description' => 'South Sudan'),
			211 => array('domain' => 'st', 'description' => 'São Tomé and Príncipe'),
			212 => array('domain' => 'su', 'description' => 'Soviet Union'),
			213 => array('domain' => 'sv', 'description' => 'El Salvador'),
			214 => array('domain' => 'sx', 'description' => 'Sint Maarten'),
			215 => array('domain' => 'sy', 'description' => 'Syria'),
			216 => array('domain' => 'sz', 'description' => 'Swaziland'),
			217 => array('domain' => 'tc', 'description' => 'Turks and Caicos Islands'),
			218 => array('domain' => 'td', 'description' => 'Chad'),
			219 => array('domain' => 'tf', 'description' => 'French Southern and Antarctic Lands'),
			220 => array('domain' => 'tg', 'description' => 'Togo'),
			221 => array('domain' => 'th', 'description' => 'Thailand'),
			222 => array('domain' => 'tj', 'description' => 'Tajikistan'),
			223 => array('domain' => 'tk', 'description' => 'Tokelau'),
			224 => array('domain' => 'tl', 'description' => 'East Timor'),
			225 => array('domain' => 'tm', 'description' => 'Turkmenistan'),
			226 => array('domain' => 'tn', 'description' => 'Tunisia'),
			227 => array('domain' => 'to', 'description' => 'Tonga'),
			228 => array('domain' => 'tp', 'description' => 'East Timor'),
			229 => array('domain' => 'tr', 'description' => 'Turkey'),
			230 => array('domain' => 'tt', 'description' => 'Trinidad and Tobago'),
			231 => array('domain' => 'tv', 'description' => 'Tuvalu'),
			232 => array('domain' => 'tw', 'description' => 'Taiwan'),
			233 => array('domain' => 'tz', 'description' => 'Tanzania'),
			234 => array('domain' => 'ua', 'description' => 'Ukraine'),
			235 => array('domain' => 'ug', 'description' => 'Uganda'),
			236 => array('domain' => 'uk', 'description' => 'United Kingdom'),
			237 => array('domain' => 'us', 'description' => 'United States of America'),
			238 => array('domain' => 'uy', 'description' => 'Uruguay'),
			239 => array('domain' => 'uz', 'description' => 'Uzbekistan'),
			240 => array('domain' => 'va', 'description' => 'Vatican City'),
			241 => array('domain' => 'vc', 'description' => 'Saint Vincent and the Grenadines'),
			242 => array('domain' => 've', 'description' => 'Venezuela'),
			243 => array('domain' => 'vg', 'description' => 'British Virgin Islands'),
			244 => array('domain' => 'vi', 'description' => 'United States Virgin Islands'),
			245 => array('domain' => 'vn', 'description' => 'Vietnam'),
			246 => array('domain' => 'vu', 'description' => 'Vanuatu'),
			247 => array('domain' => 'wf', 'description' => 'Wallis and Futuna'),
			248 => array('domain' => 'ws', 'description' => 'Samoa'),
			249 => array('domain' => 'ye', 'description' => 'Yemen'),
			250 => array('domain' => 'yt', 'description' => 'Mayotte'),
			251 => array('domain' => 'yu', 'description' => 'SFR Yugoslavia'),
			252 => array('domain' => 'za', 'description' => 'South Africa'),
			253 => array('domain' => 'zm', 'description' => 'Zambia'),
			254 => array('domain' => 'zw', 'description' => 'Zimbabwe'),
			255 => array('domain' => 'com', 'description' => 'Commercial'),
			256 => array('domain' => 'org', 'description' => 'Organization'),
			257 => array('domain' => 'net', 'description' => 'Network'),
			258 => array('domain' => 'lo', 'description' => 'Localhost'),
			259 => array('domain' => 'un', 'description' => 'Unknown'),
		);

		// Insert data
		foreach ($countries as $country)
		{
			$insert_buffer->insert($country);
		}

		// Flush the buffer
		$insert_buffer->flush();


		$insert_buffer = new \phpbb\db\sql_insert_buffer($this->db, $this->table_prefix . 'statistics_se');

		$searche = array(
			1 => array('name' => 'google', 'query' => 'q'),
			2 => array('name' => 'vinden', 'query' => 'q'),
			3 => array('name' => 'voelspriet', 'query' => 'qr'),
			4 => array('name' => 'zoeken.track', 'query' => 'q'),
			5 => array('name' => 'zoekveilig', 'query' => 'zoek'),
			6 => array('name' => 'zoek', 'query' => 'q'),
			7 => array('name' => 'eerstekeuze', 'query' => 'Term'),
			8 => array('name' => 'infoseek.go', 'query' => 'Keywords'),
			9 => array('name' => 'altavista', 'query' => 'q'),
			10 => array('name' => 'srch.overture', 'query' => 'Keywords'),
			11 => array('name' => 'overture', 'query' => 'Keywords'),
			12 => array('name' => 'search.lycos', 'query' => 'query'),
			13 => array('name' => 'zoek.lycos', 'query' => 'query'),
			14 => array('name' => 'ilse', 'query' => 'search_for'),
			15 => array('name' => 'vindex', 'query' => 'search_for'),
			16 => array('name' => 'search.yahoo', 'query' => 'p'),
			17 => array('name' => 'search.msn', 'query' => 'q'),
			18 => array('name' => 'alltheweb', 'query' => 'q'),
			19 => array('name' => 'aolrecherche.aol', 'query' => 'q'),
			20 => array('name' => 'hotbot', 'query' => 'query'),
			21 => array('name' => 'alexa', 'query' => 'q'),
			22 => array('name' => 'mysearch', 'query' => 'searchfor'),
			23 => array('name' => 'search.live', 'query' => 'q'),
			24 => array('name' => 'search.sweetim', 'query' => 'q'),
			25 => array('name' => 'bing', 'query' => 'q'),
			26 => array('name' => 'forumhulp', 'query' => 'keywords'),
		);

		// Insert data
		foreach ($searche as $se)
		{
			$insert_buffer->insert($se);
		}

		// Flush the buffer
		$insert_buffer->flush();

		$insert_buffer = new \phpbb\db\sql_insert_buffer($this->db, $this->table_prefix . 'statistics_config');

		$config = array(
			1 => array('custom_pages' => 'a:0:{}'),
		);

		// Insert data
		foreach ($config as $c)
		{
			$insert_buffer->insert($c);
		}

		// Flush the buffer
		$insert_buffer->flush();
	}
}
