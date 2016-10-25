<?php
/**
*
* @package Statistics
* @copyright (c) 2014 ForumHulp.com
* @license Proprietary
*
*/

class find_os
{
	protected $_agent;
	protected $_browser_name;
	protected $_version;
	protected $_platform;
	protected $_os;

	public function __construct($_agent = '', $_browser_name= '', $_version = '', $_platform = '', $_os = '')
	{
		$this->_agent = $_agent;
		$this->_browser_name = $_browser_name;
		$this->_version = $_version;
		$this->_platform = $_platform;
		$this->_os = $_os;
	}
	const BROWSER_UNKNOWN = 'unknown';
	const VERSION_UNKNOWN = 'unknown';

	const BROWSER_OPERA = 'Opera'; // http://www.opera.com/
	const BROWSER_OPERA_MINI = 'Opera Mini'; // http://www.opera.com/mini/
	const BROWSER_WEBTV = 'WebTV'; // http://www.webtv.net/pc/
	const BROWSER_IE = 'Internet Explorer'; // http://www.microsoft.com/ie/
	const BROWSER_POCKET_IE = 'Pocket Internet Explorer'; // http://en.wikipedia.org/wiki/Internet_Explorer_Mobile
	const BROWSER_KONQUEROR = 'Konqueror'; // http://www.konqueror.org/
	const BROWSER_ICAB = 'iCab'; // http://www.icab.de/
	const BROWSER_OMNIWEB = 'OmniWeb'; // http://www.omnigroup.com/applications/omniweb/
	const BROWSER_FIREBIRD = 'Firebird'; // http://www.ibphoenix.com/
	const BROWSER_FIREFOX = 'Firefox'; // http://www.mozilla.com/en-US/firefox/firefox.html
	const BROWSER_ICEWEASEL = 'Iceweasel'; // http://www.geticeweasel.org/
	const BROWSER_SHIRETOKO = 'Shiretoko'; // http://wiki.mozilla.org/Projects/shiretoko
	const BROWSER_MOZILLA = 'Mozilla'; // http://www.mozilla.com/en-US/
	const BROWSER_AMAYA = 'Amaya'; // http://www.w3.org/Amaya/
	const BROWSER_LYNX = 'Lynx'; // http://en.wikipedia.org/wiki/Lynx
	const BROWSER_SAFARI = 'Safari'; // http://apple.com
	const BROWSER_IPHONE = 'iPhone'; // http://apple.com
	const BROWSER_IPOD = 'iPod'; // http://apple.com
	const BROWSER_IPAD = 'iPad'; // http://apple.com
	const BROWSER_CHROME = 'Chrome'; // http://www.google.com/chrome
	const BROWSER_ANDROID = 'Android'; // http://www.android.com/
	const BROWSER_GOOGLEBOT = 'GoogleBot'; // http://en.wikipedia.org/wiki/Googlebot
	const BROWSER_SLURP = 'Yahoo! Slurp'; // http://en.wikipedia.org/wiki/Yahoo!_Slurp
	const BROWSER_W3CVALIDATOR = 'W3C Validator'; // http://validator.w3.org/
	const BROWSER_BLACKBERRY = 'BlackBerry'; // http://www.blackberry.com/
	const BROWSER_ICECAT = 'IceCat'; // http://en.wikipedia.org/wiki/GNU_IceCat
	const BROWSER_NOKIA_S60 = 'Nokia S60 OSS Browser'; // http://en.wikipedia.org/wiki/Web_Browser_for_S60
	const BROWSER_NOKIA = 'Nokia Browser'; // * all other WAP-based browsers on the Nokia Platform
	const BROWSER_MSN = 'MSN Browser'; // http://explorer.msn.com/
	const BROWSER_MSNBOT = 'MSN Bot'; // http://search.msn.com/msnbot.htm
	const BROWSER_BINGBOT = 'Bing Bot'; // http://en.wikipedia.org/wiki/Bingbot

	const BROWSER_NETSCAPE_NAVIGATOR = 'Netscape Navigator'; // http://browser.netscape.com/ (DEPRECATED)
	const BROWSER_GALEON = 'Galeon'; // http://galeon.sourceforge.net/ (DEPRECATED)
	const BROWSER_NETPOSITIVE = 'NetPositive'; // http://en.wikipedia.org/wiki/NetPositive (DEPRECATED)
	const BROWSER_PHOENIX = 'Phoenix'; // http://en.wikipedia.org/wiki/History_of_Mozilla_Firefox (DEPRECATED)

	const PLATFORM_UNKNOWN = 'unknown';
	const OPERATING_SYSTEM_UNKNOWN = 'unknown';


	public function setUserAgent($agent_string)
	{
		$this->reset();
		$this->_agent = $agent_string;
		$this->determine();
	}

	/**
	 * The name of the browser.  All return types are from the class contants
	 * @return string Name of the browser
	 */
	public function getBrowser()
	{
		return $this->_browser_name;
	}


	/**
	 * The name of the platform.  All return types are from the class contants
	 * @return string Name of the browser
	 */
	public function getPlatform()
	{
		return $this->_platform;
	}
	
	/**
	 * Reset all properties
	 */
	public function reset()
	{
		$this->_agent = '';
		$this->_browser_name = self::BROWSER_UNKNOWN;
		$this->_version = self::VERSION_UNKNOWN;
		$this->_platform = self::PLATFORM_UNKNOWN;
		$this->_os = self::OPERATING_SYSTEM_UNKNOWN;
	}

	/**
	 * Protected routine to calculate and determine what the browser is in use (including platform)
	 */
	protected function determine()
	{
		$this->checkPlatform();
		$this->checkBrowsers();
	}


	/**
	 * Set the name of the browser
	 * @param $browser string The name of the Browser
	 */
	public function setBrowser($browser)
	{
		$this->_browser_name = $browser;
	}


	/**
	 * Set the name of the platform
	 * @param string $platform The name of the Platform
	 */
	public function setPlatform($platform)
	{
		$this->_platform = $platform;
	}
	
	/**
	 * The version of the browser.
	 * @return string Version of the browser (will only contain alpha-numeric characters and a period)
	 */
	public function getVersion()
	{
		return $this->_version;
	}

	/**
	 * Set the version of the browser
	 * @param string $version The version of the Browser
	 */
	public function setVersion($version)
	{
		$this->_version = preg_replace('/[^0-9,.,a-z,A-Z-]/', '', $version);
	}
	
	/**
	 * Determine the user's platform (last updated 1.7)
	 */
	protected function checkPlatform()
	{
		$os_array = array(
							'/windows nt 10/i'      =>  'Windows 10',
							'/windows nt 6.3/i'     =>  'Windows 8.1',
							'/windows nt 6.2/i'     =>  'Windows 8',
							'/windows nt 6.1/i'     =>  'Windows 7',
							'/windows nt 6.0/i'     =>  'Windows Vista',
							'/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
							'/windows nt 5.1/i'     =>  'Windows XP',
							'/windows xp/i'         =>  'Windows XP',
							'/windows nt 5.0/i'     =>  'Windows 2000',
							'/windows me/i'         =>  'Windows ME',
							'/win98/i'              =>  'Windows 98',
							'/win95/i'              =>  'Windows 95',
							'/win16/i'              =>  'Windows 3.11',
							'/macintosh|mac os x/i' =>  'Mac OS X',
							'/mac_powerpc/i'        =>  'Mac OS 9',
							'/linux/i'              =>  'Linux',
							'/ubuntu/i'             =>  'Ubuntu',
							'/iphone/i'             =>  'iPhone',
							'/ipod/i'               =>  'iPod',
							'/ipad/i'               =>  'iPad',
							'/android/i'            =>  'Android',
							'/blackberry/i'         =>  'BlackBerry',
							'/webos/i'              =>  'Mobile'
						);

		foreach ($os_array as $regex => $value) {
		if (preg_match($regex, $this->_agent)) {
				$this->_platform = $value;
				break;
			}
		}
	}

	/**
	 * Protected routine to determine the browser type
	 * @return boolean True if the browser was detected otherwise false
	 */
	protected function checkBrowsers()
	{
		return (
			// well-known, well-used
			// Special Notes:
			// (1) Opera must be checked before FireFox due to the odd
			//     user agents used in some older versions of Opera
			// (2) WebTV is strapped onto Internet Explorer so we must
			//     check for WebTV before IE
			// (3) (deprecated) Galeon is based on Firefox and needs to be
			//     tested before Firefox is tested
			// (4) OmniWeb is based on Safari so OmniWeb check must occur
			//     before Safari
			// (5) Netscape 9+ is based on Firefox so Netscape checks
			//     before FireFox are necessary
			$this->checkBrowserWebTv() ||
			$this->checkBrowserInternetExplorer() ||
			$this->checkBrowserOpera() ||
			$this->checkBrowserGaleon() ||
			$this->checkBrowserNetscapeNavigator9Plus() ||
			$this->checkBrowserFirefox() ||
			$this->checkBrowserChrome() ||
			$this->checkBrowserOmniWeb() ||

			// common mobile
			$this->checkBrowserAndroid() ||
			$this->checkBrowseriPad() ||
			$this->checkBrowseriPod() ||
			$this->checkBrowseriPhone() ||
			$this->checkBrowserBlackBerry() ||
			$this->checkBrowserNokia() ||

			// common bots
			$this->checkBrowserGoogleBot() ||
			$this->checkBrowserMSNBot() ||
			$this->checkBrowserBingBot() ||
			$this->checkBrowserSlurp() ||

			// WebKit base check (post mobile and others)
			$this->checkBrowserSafari() ||

			// everyone else
			$this->checkBrowserNetPositive() ||
			$this->checkBrowserFirebird() ||
			$this->checkBrowserKonqueror() ||
			$this->checkBrowserIcab() ||
			$this->checkBrowserPhoenix() ||
			$this->checkBrowserAmaya() ||
			$this->checkBrowserLynx() ||
			$this->checkBrowserShiretoko() ||
			$this->checkBrowserIceCat() ||
			$this->checkBrowserIceweasel() ||
			$this->checkBrowserW3CValidator() ||
			$this->checkBrowserMozilla() /* Mozilla is such an open standard that you must check it last */
		);
	}

	/**
	 * Determine if the user is using a BlackBerry (last updated 1.7)
	 * @return boolean True if the browser is the BlackBerry browser otherwise false
	 */
	protected function checkBrowserBlackBerry()
	{
		if (stripos($this->_agent, 'blackberry') !== false) {
			$aresult = explode("/", stristr($this->_agent, "BlackBerry"));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion($aversion[0]);
			$this->_browser_name = self::BROWSER_BLACKBERRY;
			return true;
		}
		return false;
	}


	/**
	 * Determine if the browser is the GoogleBot or not (last updated 1.7)
	 * @return boolean True if the browser is the GoogletBot otherwise false
	 */
	protected function checkBrowserGoogleBot()
	{
		if (stripos($this->_agent, 'googlebot') !== false) {
			$aresult = explode('/', stristr($this->_agent, 'googlebot'));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion(str_replace(';', '', $aversion[0]));
			$this->_browser_name = self::BROWSER_GOOGLEBOT;
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is the MSNBot or not (last updated 1.9)
	 * @return boolean True if the browser is the MSNBot otherwise false
	 */
	protected function checkBrowserMSNBot()
	{
		if (stripos($this->_agent, "msnbot") !== false) {
			$aresult = explode("/", stristr($this->_agent, "msnbot"));
			$aversion = explode(" ", $aresult[1]);
			$this->setVersion(str_replace(";", "", $aversion[0]));
			$this->_browser_name = self::BROWSER_MSNBOT;
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is the BingBot or not (last updated 1.9)
	 * @return boolean True if the browser is the BingBot otherwise false
	 */
	protected function checkBrowserBingBot()
	{
		if (stripos($this->_agent, "bingbot") !== false) {
			$aresult = explode("/", stristr($this->_agent, "bingbot"));
			$aversion = explode(" ", $aresult[1]);
			$this->setVersion(str_replace(";", "", $aversion[0]));
			$this->_browser_name = self::BROWSER_BINGBOT;
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is the W3C Validator or not (last updated 1.7)
	 * @return boolean True if the browser is the W3C Validator otherwise false
	 */
	protected function checkBrowserW3CValidator()
	{
		if (stripos($this->_agent, 'W3C-checklink') !== false) {
			$aresult = explode('/', stristr($this->_agent, 'W3C-checklink'));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion($aversion[0]);
			$this->_browser_name = self::BROWSER_W3CVALIDATOR;
			return true;
		} else if (stripos($this->_agent, 'W3C_Validator') !== false) {
			// Some of the Validator versions do not delineate w/ a slash - add it back in
			$ua = str_replace("W3C_Validator ", "W3C_Validator/", $this->_agent);
			$aresult = explode('/', stristr($ua, 'W3C_Validator'));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion($aversion[0]);
			$this->_browser_name = self::BROWSER_W3CVALIDATOR;
			return true;
		} else if (stripos($this->_agent, 'W3C-mobileOK') !== false) {
			$this->_browser_name = self::BROWSER_W3CVALIDATOR;
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is the Yahoo! Slurp Robot or not (last updated 1.7)
	 * @return boolean True if the browser is the Yahoo! Slurp Robot otherwise false
	 */
	protected function checkBrowserSlurp()
	{
		if (stripos($this->_agent, 'slurp') !== false) {
			$aresult = explode('/', stristr($this->_agent, 'Slurp'));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion($aversion[0]);
			$this->_browser_name = self::BROWSER_SLURP;
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Internet Explorer or not (last updated 1.7)
	 * @return boolean True if the browser is Internet Explorer otherwise false
	 */
	protected function checkBrowserInternetExplorer()
	{
	//  Test for IE11
	if( stripos($this->_agent,'Trident/7.0; rv:11.0') !== false ) {
		$this->setBrowser(self::BROWSER_IE);
		$this->setVersion('11.0');
		return true;
	}
		// Test for v1 - v1.5 IE
		else if (stripos($this->_agent, 'microsoft internet explorer') !== false) {
			$this->setBrowser(self::BROWSER_IE);
			$this->setVersion('1.0');
			$aresult = stristr($this->_agent, '/');
			if (preg_match('/308|425|426|474|0b1/i', $aresult)) {
				$this->setVersion('1.5');
			}
			return true;
		} // Test for versions > 1.5
		else if (stripos($this->_agent, 'msie') !== false && stripos($this->_agent, 'opera') === false) {
			// See if the browser is the odd MSN Explorer
			if (stripos($this->_agent, 'msnb') !== false) {
				$aresult = explode(' ', stristr(str_replace(';', '; ', $this->_agent), 'MSN'));
				$this->setBrowser(self::BROWSER_MSN);
				$this->setVersion(str_replace(array('(', ')', ';'), '', $aresult[1]));
				return true;
			}
			$aresult = explode(' ', stristr(str_replace(';', '; ', $this->_agent), 'msie'));
			$this->setBrowser(self::BROWSER_IE);
			$this->setVersion(str_replace(array('(', ')', ';'), '', $aresult[1]));
			if(stripos($this->_agent, 'IEMobile') !== false) {
				$this->setBrowser(self::BROWSER_POCKET_IE);
			}
			return true;
		} // Test for versions > IE 10
		else if(stripos($this->_agent, 'trident') !== false) {
			$this->setBrowser(self::BROWSER_IE);
			$result = explode('rv:', $this->_agent);
			$this->setVersion(preg_replace('/[^0-9.]+/', '', $result[1]));
			$this->_agent = str_replace(array("Mozilla", "Gecko"), "MSIE", $this->_agent);
		} // Test for Pocket IE
		else if (stripos($this->_agent, 'mspie') !== false || stripos($this->_agent, 'pocket') !== false) {
			$aresult = explode(' ', stristr($this->_agent, 'mspie'));
			$this->setPlatform(self::PLATFORM_WINDOWS_CE);
			$this->setBrowser(self::BROWSER_POCKET_IE);

			if (stripos($this->_agent, 'mspie') !== false) {
				$this->setVersion($aresult[1]);
			} else {
				$aversion = explode('/', $this->_agent);
				$this->setVersion($aversion[1]);
			}
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Opera or not (last updated 1.7)
	 * @return boolean True if the browser is Opera otherwise false
	 */
	protected function checkBrowserOpera()
	{
		if (stripos($this->_agent, 'opera mini') !== false) {
			$resultant = stristr($this->_agent, 'opera mini');
			if (preg_match('/\//', $resultant)) {
				$aresult = explode('/', $resultant);
				$aversion = explode(' ', $aresult[1]);
				$this->setVersion($aversion[0]);
			} else {
				$aversion = explode(' ', stristr($resultant, 'opera mini'));
				$this->setVersion($aversion[1]);
			}
			$this->_browser_name = self::BROWSER_OPERA_MINI;
			return true;
		} else if (stripos($this->_agent, 'opera') !== false) {
			$resultant = stristr($this->_agent, 'opera');
			if (preg_match('/Version\/(1*.*)$/', $resultant, $matches)) {
				$this->setVersion($matches[1]);
			} else if (preg_match('/\//', $resultant)) {
				$aresult = explode('/', str_replace("(", " ", $resultant));
				$aversion = explode(' ', $aresult[1]);
				$this->setVersion($aversion[0]);
			} else {
				$aversion = explode(' ', stristr($resultant, 'opera'));
				$this->setVersion(isset($aversion[1]) ? $aversion[1] : "");
			}
			if (stripos($this->_agent, 'Opera Mobi') !== false) {
			}
			$this->_browser_name = self::BROWSER_OPERA;
			return true;
		} else if (stripos($this->_agent, 'OPR') !== false) {
			$resultant = stristr($this->_agent, 'OPR');
			if (preg_match('/\//', $resultant)) {
				$aresult = explode('/', str_replace("(", " ", $resultant));
				$aversion = explode(' ', $aresult[1]);
				$this->setVersion($aversion[0]);
			}
			if (stripos($this->_agent, 'Mobile') !== false) {
			}
			$this->_browser_name = self::BROWSER_OPERA;
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Chrome or not (last updated 1.7)
	 * @return boolean True if the browser is Chrome otherwise false
	 */
	protected function checkBrowserChrome()
	{
		if (stripos($this->_agent, 'Chrome') !== false) {
			$aresult = explode('/', stristr($this->_agent, 'Chrome'));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion($aversion[0]);
			$this->setBrowser(self::BROWSER_CHROME);
			//Chrome on Android
			if (stripos($this->_agent, 'Android') !== false) {
				if (stripos($this->_agent, 'Mobile') !== false) {
				} else {
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is WebTv or not (last updated 1.7)
	 * @return boolean True if the browser is WebTv otherwise false
	 */
	protected function checkBrowserWebTv()
	{
		if (stripos($this->_agent, 'webtv') !== false) {
			$aresult = explode('/', stristr($this->_agent, 'webtv'));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion($aversion[0]);
			$this->setBrowser(self::BROWSER_WEBTV);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is NetPositive or not (last updated 1.7)
	 * @return boolean True if the browser is NetPositive otherwise false
	 */
	protected function checkBrowserNetPositive()
	{
		if (stripos($this->_agent, 'NetPositive') !== false) {
			$aresult = explode('/', stristr($this->_agent, 'NetPositive'));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion(str_replace(array('(', ')', ';'), '', $aversion[0]));
			$this->setBrowser(self::BROWSER_NETPOSITIVE);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Galeon or not (last updated 1.7)
	 * @return boolean True if the browser is Galeon otherwise false
	 */
	protected function checkBrowserGaleon()
	{
		if (stripos($this->_agent, 'galeon') !== false) {
			$aresult = explode(' ', stristr($this->_agent, 'galeon'));
			$aversion = explode('/', $aresult[0]);
			$this->setVersion($aversion[1]);
			$this->setBrowser(self::BROWSER_GALEON);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Konqueror or not (last updated 1.7)
	 * @return boolean True if the browser is Konqueror otherwise false
	 */
	protected function checkBrowserKonqueror()
	{
		if (stripos($this->_agent, 'Konqueror') !== false) {
			$aresult = explode(' ', stristr($this->_agent, 'Konqueror'));
			$aversion = explode('/', $aresult[0]);
			$this->setVersion($aversion[1]);
			$this->setBrowser(self::BROWSER_KONQUEROR);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is iCab or not (last updated 1.7)
	 * @return boolean True if the browser is iCab otherwise false
	 */
	protected function checkBrowserIcab()
	{
		if (stripos($this->_agent, 'icab') !== false) {
			$aversion = explode(' ', stristr(str_replace('/', ' ', $this->_agent), 'icab'));
			$this->setVersion($aversion[1]);
			$this->setBrowser(self::BROWSER_ICAB);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is OmniWeb or not (last updated 1.7)
	 * @return boolean True if the browser is OmniWeb otherwise false
	 */
	protected function checkBrowserOmniWeb()
	{
		if (stripos($this->_agent, 'omniweb') !== false) {
			$aresult = explode('/', stristr($this->_agent, 'omniweb'));
			$aversion = explode(' ', isset($aresult[1]) ? $aresult[1] : "");
			$this->setVersion($aversion[0]);
			$this->setBrowser(self::BROWSER_OMNIWEB);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Phoenix or not (last updated 1.7)
	 * @return boolean True if the browser is Phoenix otherwise false
	 */
	protected function checkBrowserPhoenix()
	{
		if (stripos($this->_agent, 'Phoenix') !== false) {
			$aversion = explode('/', stristr($this->_agent, 'Phoenix'));
			$this->setVersion($aversion[1]);
			$this->setBrowser(self::BROWSER_PHOENIX);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Firebird or not (last updated 1.7)
	 * @return boolean True if the browser is Firebird otherwise false
	 */
	protected function checkBrowserFirebird()
	{
		if (stripos($this->_agent, 'Firebird') !== false) {
			$aversion = explode('/', stristr($this->_agent, 'Firebird'));
			$this->setVersion($aversion[1]);
			$this->setBrowser(self::BROWSER_FIREBIRD);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Netscape Navigator 9+ or not (last updated 1.7)
	 * NOTE: (http://browser.netscape.com/ - Official support ended on March 1st, 2008)
	 * @return boolean True if the browser is Netscape Navigator 9+ otherwise false
	 */
	protected function checkBrowserNetscapeNavigator9Plus()
	{
		if (stripos($this->_agent, 'Firefox') !== false && preg_match('/Navigator\/([^ ]*)/i', $this->_agent, $matches)) {
			$this->setVersion($matches[1]);
			$this->setBrowser(self::BROWSER_NETSCAPE_NAVIGATOR);
			return true;
		} else if (stripos($this->_agent, 'Firefox') === false && preg_match('/Netscape6?\/([^ ]*)/i', $this->_agent, $matches)) {
			$this->setVersion($matches[1]);
			$this->setBrowser(self::BROWSER_NETSCAPE_NAVIGATOR);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Shiretoko or not (https://wiki.mozilla.org/Projects/shiretoko) (last updated 1.7)
	 * @return boolean True if the browser is Shiretoko otherwise false
	 */
	protected function checkBrowserShiretoko()
	{
		if (stripos($this->_agent, 'Mozilla') !== false && preg_match('/Shiretoko\/([^ ]*)/i', $this->_agent, $matches)) {
			$this->setVersion($matches[1]);
			$this->setBrowser(self::BROWSER_SHIRETOKO);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Ice Cat or not (http://en.wikipedia.org/wiki/GNU_IceCat) (last updated 1.7)
	 * @return boolean True if the browser is Ice Cat otherwise false
	 */
	protected function checkBrowserIceCat()
	{
		if (stripos($this->_agent, 'Mozilla') !== false && preg_match('/IceCat\/([^ ]*)/i', $this->_agent, $matches)) {
			$this->setVersion($matches[1]);
			$this->setBrowser(self::BROWSER_ICECAT);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Nokia or not (last updated 1.7)
	 * @return boolean True if the browser is Nokia otherwise false
	 */
	protected function checkBrowserNokia()
	{
		if (preg_match("/Nokia([^\/]+)\/([^ SP]+)/i", $this->_agent, $matches)) {
			$this->setVersion($matches[2]);
			if (stripos($this->_agent, 'Series60') !== false || strpos($this->_agent, 'S60') !== false) {
				$this->setBrowser(self::BROWSER_NOKIA_S60);
			} else {
				$this->setBrowser(self::BROWSER_NOKIA);
			}
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Firefox or not (last updated 1.7)
	 * @return boolean True if the browser is Firefox otherwise false
	 */
	protected function checkBrowserFirefox()
	{
		if (stripos($this->_agent, 'safari') === false) {
			if (preg_match("/Firefox[\/ \(]([^ ;\)]+)/i", $this->_agent, $matches)) {
				$this->setVersion($matches[1]);
				$this->setBrowser(self::BROWSER_FIREFOX);
				//Firefox on Android
				if (stripos($this->_agent, 'Android') !== false) {
					if (stripos($this->_agent, 'Mobile') !== false) {
					} else {
					}
				}
				return true;
			} else if (preg_match("/Firefox$/i", $this->_agent, $matches)) {
				$this->setVersion("");
				$this->setBrowser(self::BROWSER_FIREFOX);
				return true;
			}
		}
		return false;
	}

	/**
	 * Determine if the browser is Firefox or not (last updated 1.7)
	 * @return boolean True if the browser is Firefox otherwise false
	 */
	protected function checkBrowserIceweasel()
	{
		if (stripos($this->_agent, 'Iceweasel') !== false) {
			$aresult = explode('/', stristr($this->_agent, 'Iceweasel'));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion($aversion[0]);
			$this->setBrowser(self::BROWSER_ICEWEASEL);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Mozilla or not (last updated 1.7)
	 * @return boolean True if the browser is Mozilla otherwise false
	 */
	protected function checkBrowserMozilla()
	{
		if (stripos($this->_agent, 'mozilla') !== false && preg_match('/rv:[0-9].[0-9][a-b]?/i', $this->_agent) && stripos($this->_agent, 'netscape') === false) {
			$aversion = explode(' ', stristr($this->_agent, 'rv:'));
			preg_match('/rv:[0-9].[0-9][a-b]?/i', $this->_agent, $aversion);
			$this->setVersion(str_replace('rv:', '', $aversion[0]));
			$this->setBrowser(self::BROWSER_MOZILLA);
			return true;
		} else if (stripos($this->_agent, 'mozilla') !== false && preg_match('/rv:[0-9]\.[0-9]/i', $this->_agent) && stripos($this->_agent, 'netscape') === false) {
			$aversion = explode('', stristr($this->_agent, 'rv:'));
			$this->setVersion(str_replace('rv:', '', $aversion[0]));
			$this->setBrowser(self::BROWSER_MOZILLA);
			return true;
		} else if (stripos($this->_agent, 'mozilla') !== false && preg_match('/mozilla\/([^ ]*)/i', $this->_agent, $matches) && stripos($this->_agent, 'netscape') === false) {
			$this->setVersion($matches[1]);
			$this->setBrowser(self::BROWSER_MOZILLA);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Lynx or not (last updated 1.7)
	 * @return boolean True if the browser is Lynx otherwise false
	 */
	protected function checkBrowserLynx()
	{
		if (stripos($this->_agent, 'lynx') !== false) {
			$aresult = explode('/', stristr($this->_agent, 'Lynx'));
			$aversion = explode(' ', (isset($aresult[1]) ? $aresult[1] : ""));
			$this->setVersion($aversion[0]);
			$this->setBrowser(self::BROWSER_LYNX);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Amaya or not (last updated 1.7)
	 * @return boolean True if the browser is Amaya otherwise false
	 */
	protected function checkBrowserAmaya()
	{
		if (stripos($this->_agent, 'amaya') !== false) {
			$aresult = explode('/', stristr($this->_agent, 'Amaya'));
			$aversion = explode(' ', $aresult[1]);
			$this->setVersion($aversion[0]);
			$this->setBrowser(self::BROWSER_AMAYA);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Safari or not (last updated 1.7)
	 * @return boolean True if the browser is Safari otherwise false
	 */
	protected function checkBrowserSafari()
	{
		if (stripos($this->_agent, 'Safari') !== false
			&& stripos($this->_agent, 'iPhone') === false
			&& stripos($this->_agent, 'iPod') === false) {

			$aresult = explode('/', stristr($this->_agent, 'Version'));
			if (isset($aresult[1])) {
				$aversion = explode(' ', $aresult[1]);
				$this->setVersion($aversion[0]);
			} else {
				$this->setVersion(self::VERSION_UNKNOWN);
			}
			$this->setBrowser(self::BROWSER_SAFARI);
			return true;
		}
		return false;
	}


	/**
	 * Detect Version for the Safari browser on iOS devices
	 * @return boolean True if it detects the version correctly otherwise false
	 */
	protected function getSafariVersionOnIos()
	{
		$aresult = explode('/',stristr($this->_agent,'Version'));
		if( isset($aresult[1]) )
		{
			$aversion = explode(' ',$aresult[1]);
			$this->setVersion($aversion[0]);
			return true;
		}
		return false;
	}

	/**
	 * Detect Version for the Chrome browser on iOS devices
	 * @return boolean True if it detects the version correctly otherwise false
	 */
	protected function getChromeVersionOnIos()
	{
		$aresult = explode('/',stristr($this->_agent,'CriOS'));
		if( isset($aresult[1]) )
		{
			$aversion = explode(' ',$aresult[1]);
			$this->setVersion($aversion[0]);
			$this->setBrowser(self::BROWSER_CHROME);
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is iPhone or not (last updated 1.7)
	 * @return boolean True if the browser is iPhone otherwise false
	 */
	protected function checkBrowseriPhone()
	{
		if( stripos($this->_agent,'iPhone') !== false ) {
			$this->setVersion(self::VERSION_UNKNOWN);
			$this->setBrowser(self::BROWSER_IPHONE);
			$this->getSafariVersionOnIos();
			$this->getChromeVersionOnIos();
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is iPad or not (last updated 1.7)
	 * @return boolean True if the browser is iPad otherwise false
	 */
	protected function checkBrowseriPad()
	{
		if( stripos($this->_agent,'iPad') !== false ) {
			$this->setVersion(self::VERSION_UNKNOWN);
			$this->setBrowser(self::BROWSER_IPAD);
			$this->getSafariVersionOnIos();
			$this->getChromeVersionOnIos();
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is iPod or not (last updated 1.7)
	 * @return boolean True if the browser is iPod otherwise false
	 */
	protected function checkBrowseriPod()
	{
		if( stripos($this->_agent,'iPod') !== false ) {
			$this->setVersion(self::VERSION_UNKNOWN);
			$this->setBrowser(self::BROWSER_IPOD);
			$this->getSafariVersionOnIos();
			$this->getChromeVersionOnIos();
			return true;
		}
		return false;
	}

	/**
	 * Determine if the browser is Android or not (last updated 1.7)
	 * @return boolean True if the browser is Android otherwise false
	 */
	protected function checkBrowserAndroid()
	{
		if (stripos($this->_agent, 'Android') !== false) {
			$aresult = explode(' ', stristr($this->_agent, 'Android'));
			if (isset($aresult[1])) {
				$aversion = explode(' ', $aresult[1]);
				$this->setVersion($aversion[0]);
			} else {
				$this->setVersion(self::VERSION_UNKNOWN);
			}
			if (stripos($this->_agent, 'Mobile') !== false) {
			} else {
			}
			$this->setBrowser(self::BROWSER_ANDROID);
			return true;
		}
		return false;
	}
}

