<?php
/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ToolsCore
{
	protected static $file_exists_cache = array();
	protected static $_forceCompile;
	protected static $_caching;
	protected static $_user_plateform;
	protected static $_user_browser;

	/**
	* Random password generator
	*
	* @param integer $length Desired length (optional)
	* @param string $flag Output type (NUMERIC, ALPHANUMERIC, NO_NUMERIC)
	* @return string Password
	*/
	public static function passwdGen($length = 8, $flag = 'ALPHANUMERIC')
	{
		switch ($flag)
		{
			case 'NUMERIC':
				$str = '0123456789';
				break;
			case 'NO_NUMERIC':
				$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
			default:
				$str = 'abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
		}

		for ($i = 0, $passwd = ''; $i < $length; $i++)
			$passwd .= Tools::substr($str, mt_rand(0, Tools::strlen($str) - 1), 1);
		return $passwd;
	}

	public static function strReplaceFirst($search, $replace, $subject, $cur = 0)
	{
		return (strpos($subject, $search, $cur))?substr_replace($subject, $replace, (int)strpos($subject, $search, $cur), strlen($search)):$subject;
	}

	/**
	 * Redirect user to another page
	 *
	 * @param string $url Desired URL
	 * @param string $baseUri Base URI (optional)
	 * @param Link $link
	 * @param string|array $headers A list of headers to send before redirection
	 */
	public static function redirect($url, $base_uri = __PS_BASE_URI__, Link $link = null, $headers = null)
	{
		if (!$link)
			$link = Context::getContext()->link;

		if (strpos($url, 'http://') === false && strpos($url, 'https://') === false && $link)
		{
			if (strpos($url, $base_uri) === 0)
				$url = substr($url, strlen($base_uri));
			if (strpos($url, 'index.php?controller=') !== false && strpos($url, 'index.php/') == 0)
			{
				$url = substr($url, strlen('index.php?controller='));
				if (Configuration::get('PS_REWRITING_SETTINGS'))
					$url = Tools::strReplaceFirst('&', '?', $url);
			}

			$explode = explode('?', $url);
			// don't use ssl if url is home page
			// used when logout for example
			$use_ssl = !empty($url);
			$url = $link->getPageLink($explode[0], $use_ssl);
			if (isset($explode[1]))
				$url .= '?'.$explode[1];
		}

		// Send additional headers
		if ($headers)
		{
			if (!is_array($headers))
				$headers = array($headers);

			foreach ($headers as $header)
				header($header);
		}

		header('Location: '.$url);
		exit;
	}

	/**
	* Redirect URLs already containing PS_BASE_URI
	*
	* @param string $url Desired URL
	*/
	public static function redirectLink($url)
	{
		if (!preg_match('@^https?://@i', $url))
		{
			if (strpos($url, __PS_BASE_URI__) !== false && strpos($url, __PS_BASE_URI__) == 0)
				$url = substr($url, strlen(__PS_BASE_URI__));
			if (strpos($url, 'index.php?controller=') !== false && strpos($url, 'index.php/') == 0)
				$url = substr($url, strlen('index.php?controller='));
			$explode = explode('?', $url);
			$url = Context::getContext()->link->getPageLink($explode[0]);
			if (isset($explode[1]))
				$url .= '?'.$explode[1];
		}
		header('Location: '.$url);
		exit;
	}

	/**
	* Redirect user to another admin page
	*
	* @param string $url Desired URL
	*/
	public static function redirectAdmin($url)
	{
		header('Location: '.$url);
		exit;
	}

	/**
	 * getShopProtocol return the available protocol for the current shop in use
	 * SSL if Configuration is set on and available for the server
	 * @static
	 * @return String
	 */
	public static function getShopProtocol()
	{
		$protocol = (Configuration::get('PS_SSL_ENABLED') || (!empty($_SERVER['HTTPS'])
			&& Tools::strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
		return $protocol;
	}

	/**
	 * getProtocol return the set protocol according to configuration (http[s])
	 * @param bool $use_ssl true if require ssl
	 * @return String (http|https)
	 */
	public static function getProtocol($use_ssl = null)
	{
		return (!is_null($use_ssl) && $use_ssl ? 'https://' : 'http://');
	}

	/**
	 * getHttpHost return the <b>current</b> host used, with the protocol (http or https) if $http is true
	 * This function should not be used to choose http or https domain name.
	 * Use Tools::getShopDomain() or Tools::getShopDomainSsl instead
	 *
	 * @param boolean $http
	 * @param boolean $entities
	 * @return string host
	 */
	public static function getHttpHost($http = false, $entities = false, $ignore_port = false)
	{
		$host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
		if ($ignore_port && $pos = strpos($host, ':'))
			$host = substr($host, 0, $pos);
		if ($entities)
			$host = htmlspecialchars($host, ENT_COMPAT, 'UTF-8');
		if ($http)
			$host = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$host;
		return $host;
	}

	/**
	 * getShopDomain returns domain name according to configuration and ignoring ssl
	 *
	 * @param boolean $http if true, return domain name with protocol
	 * @param boolean $entities if true,
	 * @return string domain
	 */
	public static function getShopDomain($http = false, $entities = false)
	{
		if (!$domain = ShopUrl::getMainShopDomain())
			$domain = Tools::getHttpHost();
		if ($entities)
			$domain = htmlspecialchars($domain, ENT_COMPAT, 'UTF-8');
		if ($http)
			$domain = 'http://'.$domain;
		return $domain;
	}

	/**
	 * getShopDomainSsl returns domain name according to configuration and depending on ssl activation
	 *
	 * @param boolean $http if true, return domain name with protocol
	 * @param boolean $entities if true,
	 * @return string domain
	 */
	public static function getShopDomainSsl($http = false, $entities = false)
	{
		if (!$domain = ShopUrl::getMainShopDomainSSL())
			$domain = Tools::getHttpHost();
		if ($entities)
			$domain = htmlspecialchars($domain, ENT_COMPAT, 'UTF-8');
		if ($http)
			$domain = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$domain;
		return $domain;
	}

	/**
	* Get the server variable SERVER_NAME
	*
	* @return string server name
	*/
	public static function getServerName()
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_SERVER']) && $_SERVER['HTTP_X_FORWARDED_SERVER'])
			return $_SERVER['HTTP_X_FORWARDED_SERVER'];
		return $_SERVER['SERVER_NAME'];
	}

	/**
	* Get the server variable REMOTE_ADDR, or the first ip of HTTP_X_FORWARDED_FOR (when using proxy)
	*
	* @return string $remote_addr ip of client
	*/
	public static function getRemoteAddr()
	{
		// This condition is necessary when using CDN, don't remove it.
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && (!isset($_SERVER['REMOTE_ADDR']) || preg_match('/^127\..*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^172\.16.*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^192\.168\.*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^10\..*/i', trim($_SERVER['REMOTE_ADDR']))))
		{
			if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ','))
			{
				$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				return $ips[0];
			}
			else
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		return $_SERVER['REMOTE_ADDR'];
	}

	/**
	* Check if the current page use SSL connection on not
	*
	* @return bool uses SSL
	*/
	public static function usingSecureMode()
	{
		if (isset($_SERVER['HTTPS']))
			return in_array(Tools::strtolower($_SERVER['HTTPS']), array(1, 'on'));
		// $_SERVER['SSL'] exists only in some specific configuration
		if (isset($_SERVER['SSL']))
			return in_array(Tools::strtolower($_SERVER['SSL']), array(1, 'on'));
		// $_SERVER['REDIRECT_HTTPS'] exists only in some specific configuration
		if (isset($_SERVER['REDIRECT_HTTPS']))
			return in_array(Tools::strtolower($_SERVER['REDIRECT_HTTPS']), array(1, 'on'));
		if (isset($_SERVER['HTTP_SSL']))
			return in_array(Tools::strtolower($_SERVER['HTTP_SSL']), array(1, 'on'));

		return false;
	}

	/**
	* Get the current url prefix protocol (https/http)
	*
	* @return string protocol
	*/
	public static function getCurrentUrlProtocolPrefix()
	{
		if (Tools::usingSecureMode())
			return 'https://';
		else
			return 'http://';
	}

	/**
	* Secure an URL referrer
	*
	* @param string $referrer URL referrer
	* @return string secured referrer
	*/
	public static function secureReferrer($referrer)
	{
		if (preg_match('/^http[s]?:\/\/'.Tools::getServerName().'(:'._PS_SSL_PORT_.')?\/.*$/Ui', $referrer))
			return $referrer;
		return __PS_BASE_URI__;
	}

	/**
	* Get a value from $_POST / $_GET
	* if unavailable, take a default value
	*
	* @param string $key Value key
	* @param mixed $default_value (optional)
	* @return mixed Value
	*/
	public static function getValue($key, $default_value = false)
	{
		if (!isset($key) || empty($key) || !is_string($key))
			return false;
		$ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $default_value));

		if (is_string($ret) === true)
			$ret = urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret)));
		return !is_string($ret)? $ret : stripslashes($ret);
	}

	public static function getIsset($key)
	{
		if (!isset($key) || empty($key) || !is_string($key))
			return false;
		return isset($_POST[$key]) ? true : (isset($_GET[$key]) ? true : false);
	}

	/**
	* Change language in cookie while clicking on a flag
	*
	* @return string iso code
	*/
	public static function setCookieLanguage($cookie = null)
	{
		if (!$cookie)
			$cookie = Context::getContext()->cookie;
		/* If language does not exist or is disabled, erase it */
		if ($cookie->id_lang)
		{
			$lang = new Language((int)$cookie->id_lang);
			if (!Validate::isLoadedObject($lang) || !$lang->active || !$lang->isAssociatedToShop())
				$cookie->id_lang = null;
		}

		/* Automatically detect language if not already defined, detect_language is set in Cookie::update */
		if ((!$cookie->id_lang || isset($cookie->detect_language)) && isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			$array  = explode(',', Tools::strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']));
			$string = $array[0];
			
			if (Validate::isLanguageCode($string))
			{
				$lang = Language::getLanguageByIETFCode($string);
				if (Validate::isLoadedObject($lang) && $lang->active && $lang->isAssociatedToShop())
				{
					Context::getContext()->language = $lang;
					$cookie->id_lang = (int)$lang->id;
				}
			}
		}

		if (isset($cookie->detect_language))
			unset($cookie->detect_language);

		/* If language file not present, you must use default language file */
		if (!$cookie->id_lang || !Validate::isUnsignedId($cookie->id_lang))
			$cookie->id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$iso = Language::getIsoById((int)$cookie->id_lang);
		@include_once(_PS_THEME_DIR_.'lang/'.$iso.'.php');
		
		return $iso;
	}

	/**
	 * Set cookie id_lang
	 */
	public static function switchLanguage(Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();
		
		// Install call the dispatcher and so the switchLanguage
		// Stop this method by checking the cookie
		if (!isset($context->cookie))
			return;

		if (($iso = Tools::getValue('isolang')) && Validate::isLanguageIsoCode($iso) && ($id_lang = (int)Language::getIdByIso($iso)))
			$_GET['id_lang'] = $id_lang;

		// update language only if new id is different from old id
		// or if default language changed
		$cookie_id_lang = $context->cookie->id_lang;
		$configuration_id_lang = Configuration::get('PS_LANG_DEFAULT');
		if ((($id_lang = (int)Tools::getValue('id_lang')) && Validate::isUnsignedId($id_lang) && $cookie_id_lang != (int)$id_lang)
			|| (($id_lang == $configuration_id_lang) && Validate::isUnsignedId($id_lang) && $id_lang != $cookie_id_lang))
		{
			$context->cookie->id_lang = $id_lang;
			$language = new Language($id_lang);
			if (Validate::isLoadedObject($language) && $language->active)
				$context->language = $language;

			$params = $_GET;
			if (Configuration::get('PS_REWRITING_SETTINGS') || !Language::isMultiLanguageActivated())
				unset($params['id_lang']);
		}
	}

	/**
	 * Set cookie currency from POST or default currency
	 *
	 * @return Currency object
	 */
	public static function setCurrency($cookie)
	{
		if (Tools::isSubmit('SubmitCurrency'))
			if (isset($_POST['id_currency']) && is_numeric($_POST['id_currency']))
			{
				$currency = Currency::getCurrencyInstance($_POST['id_currency']);
				if (is_object($currency) && $currency->id && !$currency->deleted && $currency->isAssociatedToShop())
					$cookie->id_currency = (int)$currency->id;
			}
		
		$currency = null;
		if ((int)$cookie->id_currency)
			$currency = Currency::getCurrencyInstance((int)$cookie->id_currency);
		if (!Validate::isLoadedObject($currency) || (bool)$currency->deleted || !(bool)$currency->active)
			$currency = Currency::getCurrencyInstance(Configuration::get('PS_CURRENCY_DEFAULT'));

		$cookie->id_currency = (int)$currency->id;
		if ($currency->isAssociatedToShop())
			return $currency;
		else
		{
			// get currency from context
			$currency = Shop::getEntityIds('currency', Context::getContext()->shop->id, true, true);
			if (isset($currency[0]) && $currency[0]['id_currency'])
			{
				$cookie->id_currency = $currency[0]['id_currency'];
				return Currency::getCurrencyInstance((int)$cookie->id_currency);
			}
		}
			
		return $currency;
	}

	/**
	* Return price with currency sign for a given product
	*
	* @param float $price Product price
	* @param object|array $currency Current currency (object, id_currency, NULL => context currency)
	* @return string Price correctly formated (sign, decimal separator...)
	*/
	public static function displayPrice($price, $currency = null, $no_utf8 = false, Context $context = null)
	{
		if (!is_numeric($price))
			return $price;
		if (!$context)
			$context = Context::getContext();
		if ($currency === null)
			$currency = $context->currency;
		// if you modified this function, don't forget to modify the Javascript function formatCurrency (in tools.js)
		elseif (is_int($currency))
			$currency = Currency::getCurrencyInstance((int)$currency);

		if (is_array($currency))
		{
			$c_char = $currency['sign'];
			$c_format = $currency['format'];
			$c_decimals = (int)$currency['decimals'] * _PS_PRICE_DISPLAY_PRECISION_;
			$c_blank = $currency['blank'];
		}
		elseif (is_object($currency))
		{
			$c_char = $currency->sign;
			$c_format = $currency->format;
			$c_decimals = (int)$currency->decimals * _PS_PRICE_DISPLAY_PRECISION_;
			$c_blank = $currency->blank;
		}
		else
			return false;

		$blank = ($c_blank ? ' ' : '');
		$ret = 0;
		if (($is_negative = ($price < 0)))
			$price *= -1;
		$price = Tools::ps_round($price, $c_decimals);

		/*
		* If the language is RTL and the selected currency format contains spaces as thousands separator
		* then the number will be printed in reverse since the space is interpreted as separating words.
		* To avoid this we replace the currency format containing a space with the one containing a comma (,) as thousand
		* separator when the language is RTL.
		*
		* TODO: This is not ideal, a currency format should probably be tied to a language, not to a currency.
		*/
		if(($c_format == 2) && ($context->language->is_rtl == 1))
		{
			$c_format = 4;
		}

		switch ($c_format)
		{
			/* X 0,000.00 */
			case 1:
				$ret = $c_char.$blank.number_format($price, $c_decimals, '.', ',');
				break;
			/* 0 000,00 X*/
			case 2:
				$ret = number_format($price, $c_decimals, ',', ' ').$blank.$c_char;
				break;
			/* X 0.000,00 */
			case 3:
				$ret = $c_char.$blank.number_format($price, $c_decimals, ',', '.');
				break;
			/* 0,000.00 X */
			case 4:
				$ret = number_format($price, $c_decimals, '.', ',').$blank.$c_char;
				break;
			/* X 0'000.00  Added for the switzerland currency */
			case 5:
				$ret = $c_char.$blank.number_format($price, $c_decimals, '.', "'");
				break;
		}
		if ($is_negative)
			$ret = '-'.$ret;
		if ($no_utf8)
			return str_replace('€', chr(128), $ret);
		return $ret;
	}

	// Just to fix a bug
	// Need real CLDR functions
	public static function displayNumber($number, $currency)
	{
		if (is_array($currency))
			$format = $currency['format'];
		elseif (is_object($currency))
			$format = $currency->format;

		return number_format($number, 0, '.', in_array($format, array(1, 4)) ? ',': ' ');
	}

	public static function displayPriceSmarty($params, &$smarty)
	{
		if (array_key_exists('currency', $params))
		{
			$currency = Currency::getCurrencyInstance((int)($params['currency']));
			if (Validate::isLoadedObject($currency))
				return Tools::displayPrice($params['price'], $currency, false);
		}
		return Tools::displayPrice($params['price']);
	}

	/**
	* Return price converted
	*
	* @param float $price Product price
	* @param object|array $currency Current currency object
	* @param boolean $to_currency convert to currency or from currency to default currency
	* @param Context $context
	* @return float Price
	*/
	public static function convertPrice($price, $currency = null, $to_currency = true, Context $context = null)
	{
		static $default_currency = null;

		if ($default_currency === null)
			$default_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');

		if (!$context)
			$context = Context::getContext();
		if ($currency === null)
			$currency = $context->currency;
		elseif (is_numeric($currency))
			$currency = Currency::getCurrencyInstance($currency);

		$c_id = (is_array($currency) ? $currency['id_currency'] : $currency->id);
		$c_rate = (is_array($currency) ? $currency['conversion_rate'] : $currency->conversion_rate);

		if ($c_id != $default_currency)
		{
			if ($to_currency)
				$price *= $c_rate;
			else
				$price /= $c_rate;
		}

		return $price;
	}

	/**
	 *
	 * Convert amount from a currency to an other currency automatically
	 * @param float $amount
	 * @param Currency $currency_from if null we used the default currency
	 * @param Currency $currency_to if null we used the default currency
	 */
	public static function convertPriceFull($amount, Currency $currency_from = null, Currency $currency_to = null)
	{
		if ($currency_from === $currency_to)
			return $amount;

		if ($currency_from === null)
			$currency_from = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

		if ($currency_to === null)
			$currency_to = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

		if ($currency_from->id == Configuration::get('PS_CURRENCY_DEFAULT'))
			$amount *= $currency_to->conversion_rate;
		else
		{
            $conversion_rate = ($currency_from->conversion_rate == 0 ? 1 : $currency_from->conversion_rate);
			// Convert amount to default currency (using the old currency rate)
			$amount = Tools::ps_round($amount / $conversion_rate, 2);
			// Convert to new currency
			$amount *= $currency_to->conversion_rate;
		}
		return Tools::ps_round($amount, 2);
	}

	/**
	* Display date regarding to language preferences
	*
	* @param array $params Date, format...
	* @param object $smarty Smarty object for language preferences
	* @return string Date
	*/
	public static function dateFormat($params, &$smarty)
	{
		return Tools::displayDate($params['date'], null, (isset($params['full']) ? $params['full'] : false));
	}

	/**
	* Display date regarding to language preferences
	*
	* @param string $date Date to display format UNIX
	* @param integer $id_lang Language id DEPRECATED
	* @param boolean $full With time or not (optional)
	* @param string $separator DEPRECATED
	* @return string Date
	*/
	public static function displayDate($date, $id_lang = null, $full = false, $separator = null)
	{
		if ($id_lang !== null)
			Tools::displayParameterAsDeprecated('id_lang');
		if ($separator !== null)
			Tools::displayParameterAsDeprecated('separator');	
			
		if (!$date || !($time = strtotime($date)))
			return $date;

		if ($date == '0000-00-00 00:00:00' || $date == '0000-00-00')
			return '';

		if (!Validate::isDate($date) || !Validate::isBool($full))
			throw new PrestaShopException('Invalid date');

		$context = Context::getContext();
		$date_format = ($full ? $context->language->date_format_full : $context->language->date_format_lite);
		return date($date_format, $time);
	}

	/**
	* Sanitize a string
	*
	* @param string $string String to sanitize
	* @param boolean $full String contains HTML or not (optional)
	* @return string Sanitized string
	*/
	public static function safeOutput($string, $html = false)
	{
		if (!$html)
			$string = strip_tags($string);
		return @Tools::htmlentitiesUTF8($string, ENT_QUOTES);
	}

	public static function htmlentitiesUTF8($string, $type = ENT_QUOTES)
	{
		if (is_array($string))
			return array_map(array('Tools', 'htmlentitiesUTF8'), $string);

		return htmlentities((string)$string, $type, 'utf-8');
	}

	public static function htmlentitiesDecodeUTF8($string)
	{
		if (is_array($string))
		{
			$string = array_map(array('Tools', 'htmlentitiesDecodeUTF8'), $string);
			return (string)array_shift($string);
		}
		return html_entity_decode((string)$string, ENT_QUOTES, 'utf-8');
	}

	public static function safePostVars()
	{
		if (!isset($_POST) || !is_array($_POST))
			$_POST = array();
		else
			$_POST = array_map(array('Tools', 'htmlentitiesUTF8'), $_POST);
	}

	/**
	* Delete directory and subdirectories
	*
	* @param string $dirname Directory name
	*/
	public static function deleteDirectory($dirname, $delete_self = true)
	{
		$dirname = rtrim($dirname, '/').'/';
		if (file_exists($dirname))
			if ($files = scandir($dirname))
			{
				foreach ($files as $file)
    				if ($file != '.' && $file != '..' && $file != '.svn')
    				{
    					if (is_dir($dirname.$file))
    						Tools::deleteDirectory($dirname.$file, true);
    					elseif (file_exists($dirname.$file))
						{
							@chmod($dirname.$file, 0777); // NT ?
							unlink($dirname.$file);
						}
    				}
				if ($delete_self && file_exists($dirname))
					if (!rmdir($dirname))
					{
						@chmod($dirname, 0777); // NT ?
                        return false;
					}
                return true;                    
			}
        return false;
    }

    /**
	* Delete file
	*
	* @param string File path
	* @param array  Excluded files
	*/
    public static function deleteFile($file, $exclude_files = array())
    {
		if (isset($exclude_files) && !is_array($exclude_files))
			$exclude_files = array($exclude_files);

		if (file_exists($file) && is_file($file) && array_search(basename($file), $exclude_files) === FALSE)
		{
			@chmod($dirname.$file, 0777); // NT ?
			unlink($file);
		}
    }
        
	/**
	* Clear XML cache folder
 	*/
	public static function clearXMLCache()
	{
		$themes = array();
		foreach (Theme::getThemes() as $theme)
			$themes[] = $theme->directory;

		foreach (scandir(_PS_ROOT_DIR_.'/config/xml') as $file)
		{
			$path_info = pathinfo($file, PATHINFO_EXTENSION);
			if (($path_info == 'xml') && ($file != 'default.xml') && !in_array(basename($file, '.'.$path_info), $themes))
				self::deleteFile(_PS_ROOT_DIR_.'/config/xml/'.$file);
		}
	}

	/**
	* Display an error according to an error code
	*
	* @param string $string Error message
	* @param boolean $htmlentities By default at true for parsing error message with htmlentities
	*/
	public static function displayError($string = 'Fatal error', $htmlentities = true, Context $context = null)
	{
		global $_ERRORS;

		if (is_null($context))
			$context = Context::getContext();

		@include_once(_PS_TRANSLATIONS_DIR_.$context->language->iso_code.'/errors.php');

		if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_ && $string == 'Fatal error')
			return ('<pre>'.print_r(debug_backtrace(), true).'</pre>');
		if (!is_array($_ERRORS))
			return $htmlentities ? Tools::htmlentitiesUTF8($string) : $string;
		$key = md5(str_replace('\'', '\\\'', $string));
		$str = (isset($_ERRORS) && is_array($_ERRORS) && array_key_exists($key, $_ERRORS)) ? $_ERRORS[$key] : $string;
		return $htmlentities ? Tools::htmlentitiesUTF8(stripslashes($str)) : $str;
	}

	/**
	 * Display an error with detailed object
	 *
	 * @param mixed $object
	 * @param boolean $kill
	 * @return $object if $kill = false;
	 */
	public static function dieObject($object, $kill = true)
	{
		echo '<xmp style="text-align: left;">';
		print_r($object);
		echo '</xmp><br />';

		if ($kill)
			die('END');

		return $object;
	}

	/**
	* Display a var dump in firebug console
	*
	* @param object $object Object to display
	*/
	public static function fd($object, $type = 'log')
	{
		$types = array('log', 'debug', 'info', 'warn', 'error', 'assert');
		
		if(!in_array($type, $types))
			$type = 'log';
		
		echo '
			<script type="text/javascript">
				console.'.$type.'('.Tools::jsonEncode($object).');
			</script>
		';
	}

	/**
	* ALIAS OF dieObject() - Display an error with detailed object
	*
	* @param object $object Object to display
	*/
	public static function d($object, $kill = true)
	{
		return (Tools::dieObject($object, $kill));
	}
	
	public static function debug_backtrace($start = 0, $limit = null)
	{
		$backtrace = debug_backtrace();
		array_shift($backtrace);
		for ($i = 0; $i < $start; ++$i)
			array_shift($backtrace);
		
		echo '
		<div style="margin:10px;padding:10px;border:1px solid #666666">
			<ul>';
		$i = 0;
		foreach ($backtrace as $id => $trace)
		{
			if ((int)$limit && (++$i > $limit ))
				break;
			$relative_file = (isset($trace['file'])) ? 'in /'.ltrim(str_replace(array(_PS_ROOT_DIR_, '\\'), array('', '/'), $trace['file']), '/') : '';
			$current_line = (isset($trace['line'])) ? ':'.$trace['line'] : '';

			echo '<li>
				<b>'.((isset($trace['class'])) ? $trace['class'] : '').((isset($trace['type'])) ? $trace['type'] : '').$trace['function'].'</b>
				'.$relative_file.$current_line.'
			</li>';
		}
		echo '</ul>
		</div>';
	}

	/**
	* ALIAS OF dieObject() - Display an error with detailed object but don't stop the execution
	*
	* @param object $object Object to display
	*/
	public static function p($object)
	{
		return (Tools::dieObject($object, false));
	}

	/**
	* Check if submit has been posted
	*
	* @param string $submit submit name
	*/
	public static function isSubmit($submit)
	{
		return (
			isset($_POST[$submit]) || isset($_POST[$submit.'_x']) || isset($_POST[$submit.'_y'])
			|| isset($_GET[$submit]) || isset($_GET[$submit.'_x']) || isset($_GET[$submit.'_y'])
		);
	}

	/**
	 * @deprecated 1.5.0
	 */
	public static function getMetaTags($id_lang, $page_name, $title = '')
	{
		Tools::displayAsDeprecated();
		return Meta::getMetaTags($id_lang, $page_name, $title);
	}

	/**
	 * @deprecated 1.5.0
	 */
	public static function getHomeMetaTags($id_lang, $page_name)
	{
		Tools::displayAsDeprecated();
		return Meta::getHomeMetas($id_lang, $page_name);
	}

	/**
	 * @deprecated 1.5.0
	 */
	public static function completeMetaTags($meta_tags, $default_value, Context $context = null)
	{
		Tools::displayAsDeprecated();
		return Meta::completeMetaTags($meta_tags, $default_value, $context);
	}

	/**
	* Encrypt password
	*
	* @param string $passwd String to encrypt
	*/
	public static function encrypt($passwd)
	{
		return md5(_COOKIE_KEY_.$passwd);
	}

	/**
	* Encrypt data string
	*
	* @param string $data String to encrypt
	*/
	public static function encryptIV($data)
	{
		return md5(_COOKIE_IV_.$data);
	}

	/**
	* Get token to prevent CSRF
	*
	* @param string $token token to encrypt
	*/
	public static function getToken($page = true, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();
		if ($page === true)
			return (Tools::encrypt($context->customer->id.$context->customer->passwd.$_SERVER['SCRIPT_NAME']));
		else
			return (Tools::encrypt($context->customer->id.$context->customer->passwd.$page));
	}

	/**
	* Tokenize a string
	*
	* @param string $string string to encript
	*/
	public static function getAdminToken($string)
	{
		return !empty($string) ? Tools::encrypt($string) : false;
	}

	public static function getAdminTokenLite($tab, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();
		return Tools::getAdminToken($tab.(int)Tab::getIdFromClassName($tab).(int)$context->employee->id);
	}

	public static function getAdminTokenLiteSmarty($params, &$smarty)
	{
		$context = Context::getContext();
		return Tools::getAdminToken($params['tab'].(int)Tab::getIdFromClassName($params['tab']).(int)$context->employee->id);
	}

	/**
	* Get a valid URL to use from BackOffice
	*
	* @param string $url An URL to use in BackOffice
	* @param boolean $entites Set to true to use htmlentities function on URL param
	*/
	public static function getAdminUrl($url = null, $entities = false)
	{
		$link = Tools::getHttpHost(true).__PS_BASE_URI__;

		if (isset($url))
			$link .= ($entities ? Tools::htmlentitiesUTF8($url) : $url);

		return $link;
	}

	/**
	* Get a valid image URL to use from BackOffice
	*
	* @param string $image Image name
	* @param boolean $entites Set to true to use htmlentities function on image param
	*/
	public static function getAdminImageUrl($image = null, $entities = false)
	{
		return Tools::getAdminUrl(basename(_PS_IMG_DIR_).'/'.$image, $entities);
	}

	/**
	* Get the user's journey
	*
	* @param integer $id_category Category ID
	* @param string $path Path end
	* @param boolean $linkOntheLastItem Put or not a link on the current category
	* @param string [optionnal] $categoryType defined what type of categories is used (products or cms)
	*/
	public static function getPath($id_category, $path = '', $link_on_the_item = false, $category_type = 'products', Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		$id_category = (int)$id_category;
		if ($id_category == 1)
			return '<span class="navigation_end">'.$path.'</span>';

		$pipe = Configuration::get('PS_NAVIGATION_PIPE');
		if (empty($pipe))
			$pipe = '>';

		$full_path = '';
		if ($category_type === 'products')
		{
			$interval = Category::getInterval($id_category);
			$id_root_category = $context->shop->getCategory();
			$interval_root = Category::getInterval($id_root_category);
			if ($interval)
			{
				$sql = 'SELECT c.id_category, cl.name, cl.link_rewrite
						FROM '._DB_PREFIX_.'category c
						LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = c.id_category'.Shop::addSqlRestrictionOnLang('cl').')
						'.Shop::addSqlAssociation('category', 'c').'
						WHERE c.nleft <= '.$interval['nleft'].'
							AND c.nright >= '.$interval['nright'].'
							AND c.nleft >= '.$interval_root['nleft'].'
							AND c.nright <= '.$interval_root['nright'].'
							AND cl.id_lang = '.(int)$context->language->id.'
							AND c.active = 1
							AND c.level_depth > '.(int)$interval_root['level_depth'].'
						ORDER BY c.level_depth ASC';
				$categories = Db::getInstance()->executeS($sql);

				$n = 1;
				$n_categories = count($categories);
				foreach ($categories as $category)
				{
					$full_path .=
					(($n < $n_categories || $link_on_the_item) ? '<a href="'.Tools::safeOutput($context->link->getCategoryLink((int)$category['id_category'], $category['link_rewrite'])).'" title="'.htmlentities($category['name'], ENT_NOQUOTES, 'UTF-8').'">' : '').
					htmlentities($category['name'], ENT_NOQUOTES, 'UTF-8').
					(($n < $n_categories || $link_on_the_item) ? '</a>' : '').
					(($n++ != $n_categories || !empty($path)) ? '<span class="navigation-pipe">'.$pipe.'</span>' : '');
				}

				return $full_path.$path;
			}
		}
		else if ($category_type === 'CMS')
		{
			$category = new CMSCategory($id_category, $context->language->id);
			if (!Validate::isLoadedObject($category))
				die(Tools::displayError());
			$category_link = $context->link->getCMSCategoryLink($category);

			if ($path != $category->name)
				$full_path .= '<a href="'.Tools::safeOutput($category_link).'">'.htmlentities($category->name, ENT_NOQUOTES, 'UTF-8').'</a><span class="navigation-pipe">'.$pipe.'</span>'.$path;
			else
				$full_path = ($link_on_the_item ? '<a href="'.Tools::safeOutput($category_link).'">' : '').htmlentities($path, ENT_NOQUOTES, 'UTF-8').($link_on_the_item ? '</a>' : '');

			return Tools::getPath($category->id_parent, $full_path, $link_on_the_item, $category_type);
		}
	}

	/**
	* @param string [optionnal] $type_cat defined what type of categories is used (products or cms)
	*/
	public static function getFullPath($id_category, $end, $type_cat = 'products', Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		$id_category = (int)$id_category;
		$pipe = (Configuration::get('PS_NAVIGATION_PIPE') ? Configuration::get('PS_NAVIGATION_PIPE') : '>');

		$default_category = 1;
		if ($type_cat === 'products')
		{
			$default_category = $context->shop->getCategory();
			$category = new Category($id_category, $context->language->id);
		}
		else if ($type_cat === 'CMS')
		    $category = new CMSCategory($id_category, $context->language->id);

		if (!Validate::isLoadedObject($category))
			$id_category = $default_category;
		if ($id_category == $default_category)
			return htmlentities($end, ENT_NOQUOTES, 'UTF-8');

		return Tools::getPath($id_category, $category->name, true, $type_cat).'<span class="navigation-pipe">'.$pipe.'</span> <span class="navigation_product">'.htmlentities($end, ENT_NOQUOTES, 'UTF-8').'</span>';
	}

	/**
	 * Return the friendly url from the provided string
	 *
	 * @param string $str
	 * @param bool $utf8_decode (deprecated)
	 * @return string
	 */
	public static function link_rewrite($str, $utf8_decode = null)
	{
		if ($utf8_decode !== null)
			Tools::displayParameterAsDeprecated('utf8_decode');
		return Tools::str2url($str);
	}

	/**
	 * Return a friendly url made from the provided string
	 * If the mbstring library is available, the output is the same as the js function of the same name
	 *
	 * @param string $str
	 * @return string
	 */
	public static function str2url($str)
	{
		static $allow_accented_chars = null;

		if ($allow_accented_chars === null)
			$allow_accented_chars = Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');

		$str = trim($str);

		if (function_exists('mb_strtolower'))
			$str = mb_strtolower($str, 'utf-8');
		if (!$allow_accented_chars)
			$str = Tools::replaceAccentedChars($str);

		// Remove all non-whitelist chars.
		if ($allow_accented_chars)
			$str = preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]-\pL]/u', '', $str);
		else
			$str = preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]-]/','', $str);

		$str = preg_replace('/[\s\'\:\/\[\]\-]+/', ' ', $str);
		$str = str_replace(array(' ', '/'), '-', $str);

		// If it was not possible to lowercase the string with mb_strtolower, we do it after the transformations.
		// This way we lose fewer special chars.
		if (!function_exists('mb_strtolower'))
			$str = Tools::strtolower($str);

		return $str;
	}

	/**
	 * Replace all accented chars by their equivalent non accented chars.
	 *
	 * @param string $str
	 * @return string
	 */
	public static function replaceAccentedChars($str)
	{
		/* One source among others:
			http://www.tachyonsoft.com/uc0000.htm
			http://www.tachyonsoft.com/uc0001.htm
			http://www.tachyonsoft.com/uc0004.htm
		*/
		$patterns = array(

			/* Lowercase */
			/* a  */ '/[\x{00E0}\x{00E1}\x{00E2}\x{00E3}\x{00E4}\x{00E5}\x{0101}\x{0103}\x{0105}\x{0430}]/u',
			/* b  */ '/[\x{0431}]/u',
			/* c  */ '/[\x{00E7}\x{0107}\x{0109}\x{010D}\x{0446}]/u',
			/* d  */ '/[\x{010F}\x{0111}\x{0434}]/u',
			/* e  */ '/[\x{00E8}\x{00E9}\x{00EA}\x{00EB}\x{0113}\x{0115}\x{0117}\x{0119}\x{011B}\x{0435}\x{044D}]/u',
			/* f  */ '/[\x{0444}]/u',
			/* g  */ '/[\x{011F}\x{0121}\x{0123}\x{0433}\x{0491}]/u',
			/* h  */ '/[\x{0125}\x{0127}]/u',
			/* i  */ '/[\x{00EC}\x{00ED}\x{00EE}\x{00EF}\x{0129}\x{012B}\x{012D}\x{012F}\x{0131}\x{0438}\x{0456}]/u',
			/* j  */ '/[\x{0135}\x{0439}]/u',
			/* k  */ '/[\x{0137}\x{0138}\x{043A}]/u',
			/* l  */ '/[\x{013A}\x{013C}\x{013E}\x{0140}\x{0142}\x{043B}]/u',
			/* m  */ '/[\x{043C}]/u',
			/* n  */ '/[\x{00F1}\x{0144}\x{0146}\x{0148}\x{0149}\x{014B}\x{043D}]/u',
			/* o  */ '/[\x{00F2}\x{00F3}\x{00F4}\x{00F5}\x{00F6}\x{00F8}\x{014D}\x{014F}\x{0151}\x{043E}]/u',
			/* p  */ '/[\x{043F}]/u',
			/* r  */ '/[\x{0155}\x{0157}\x{0159}\x{0440}]/u',
			/* s  */ '/[\x{015B}\x{015D}\x{015F}\x{0161}\x{0441}]/u',
			/* ss */ '/[\x{00DF}]/u',
			/* t  */ '/[\x{0163}\x{0165}\x{0167}\x{0442}]/u',
			/* u  */ '/[\x{00F9}\x{00FA}\x{00FB}\x{00FC}\x{0169}\x{016B}\x{016D}\x{016F}\x{0171}\x{0173}\x{0443}]/u',
			/* v  */ '/[\x{0432}]/u',
			/* w  */ '/[\x{0175}]/u',
			/* y  */ '/[\x{00FF}\x{0177}\x{00FD}\x{044B}]/u',
			/* z  */ '/[\x{017A}\x{017C}\x{017E}\x{0437}]/u',
			/* ae */ '/[\x{00E6}]/u',
			/* ch */ '/[\x{0447}]/u',
			/* kh */ '/[\x{0445}]/u',
			/* oe */ '/[\x{0153}]/u',
			/* sh */ '/[\x{0448}]/u',
			/* shh*/ '/[\x{0449}]/u',
			/* ya */ '/[\x{044F}]/u',
			/* ye */ '/[\x{0454}]/u',
			/* yi */ '/[\x{0457}]/u',
			/* yo */ '/[\x{0451}]/u',
			/* yu */ '/[\x{044E}]/u',
			/* zh */ '/[\x{0436}]/u',

			/* Uppercase */
			/* A  */ '/[\x{0100}\x{0102}\x{0104}\x{00C0}\x{00C1}\x{00C2}\x{00C3}\x{00C4}\x{00C5}\x{0410}]/u',
			/* B  */ '/[\x{0411}]]/u',
			/* C  */ '/[\x{00C7}\x{0106}\x{0108}\x{010A}\x{010C}\x{0426}]/u',
			/* D  */ '/[\x{010E}\x{0110}\x{0414}]/u',
			/* E  */ '/[\x{00C8}\x{00C9}\x{00CA}\x{00CB}\x{0112}\x{0114}\x{0116}\x{0118}\x{011A}\x{0415}\x{042D}]/u',
			/* F  */ '/[\x{0424}]/u',
			/* G  */ '/[\x{011C}\x{011E}\x{0120}\x{0122}\x{0413}\x{0490}]/u',
			/* H  */ '/[\x{0124}\x{0126}]/u',
			/* I  */ '/[\x{0128}\x{012A}\x{012C}\x{012E}\x{0130}\x{0418}\x{0406}]/u',
			/* J  */ '/[\x{0134}\x{0419}]/u',
			/* K  */ '/[\x{0136}\x{041A}]/u',
			/* L  */ '/[\x{0139}\x{013B}\x{013D}\x{0139}\x{0141}\x{041B}]/u',
			/* M  */ '/[\x{041C}]/u',
			/* N  */ '/[\x{00D1}\x{0143}\x{0145}\x{0147}\x{014A}\x{041D}]/u',
			/* O  */ '/[\x{00D3}\x{014C}\x{014E}\x{0150}\x{041E}]/u',
			/* P  */ '/[\x{041F}]/u',
			/* R  */ '/[\x{0154}\x{0156}\x{0158}\x{0420}]/u',
			/* S  */ '/[\x{015A}\x{015C}\x{015E}\x{0160}\x{0421}]/u',
			/* T  */ '/[\x{0162}\x{0164}\x{0166}\x{0422}]/u',
			/* U  */ '/[\x{00D9}\x{00DA}\x{00DB}\x{00DC}\x{0168}\x{016A}\x{016C}\x{016E}\x{0170}\x{0172}\x{0423}]/u',
			/* V  */ '/[\x{0412}]/u',
			/* W  */ '/[\x{0174}]/u',
			/* Y  */ '/[\x{0176}\x{042B}]/u',
			/* Z  */ '/[\x{0179}\x{017B}\x{017D}\x{0417}]/u',
			/* AE */ '/[\x{00C6}]/u',
			/* CH */ '/[\x{0427}]/u',
			/* KH */ '/[\x{0425}]/u',
			/* OE */ '/[\x{0152}]/u',
			/* SH */ '/[\x{0428}]/u',
			/* SHH*/ '/[\x{0429}]/u',
			/* YA */ '/[\x{042F}]/u',
			/* YE */ '/[\x{0404}]/u',
			/* YI */ '/[\x{0407}]/u',
			/* YO */ '/[\x{0401}]/u',
			/* YU */ '/[\x{042E}]/u',
			/* ZH */ '/[\x{0416}]/u');
			
			// ö to oe
			// å to aa
			// ä to ae

		$replacements = array(
				'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 'ss', 't', 'u', 'v', 'w', 'y', 'z', 'ae', 'ch', 'kh', 'oe', 'sh', 'shh', 'ya', 'ye', 'yi', 'yo', 'yu', 'zh',
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'Y', 'Z', 'AE', 'CH', 'KH', 'OE', 'SH', 'SHH', 'YA', 'YE', 'YI', 'YO', 'YU', 'ZH'
			);

		return preg_replace($patterns, $replacements, $str);
	}

	/**
	* Truncate strings
	*
	* @param string $str
	* @param integer $max_length Max length
	* @param string $suffix Suffix optional
	* @return string $str truncated
	*/
	/* CAUTION : Use it only on module hookEvents.
	** For other purposes use the smarty function instead */
	public static function truncate($str, $max_length, $suffix = '...')
	{
	 	if (Tools::strlen($str) <= $max_length)
	 		return $str;
	 	$str = utf8_decode($str);
	 	return (utf8_encode(substr($str, 0, $max_length - Tools::strlen($suffix)).$suffix));
	}

	/*Copied from CakePHP String utility file*/
	public static function truncateString($text, $length = 120, $options = array())
	{
		$default = array(
			'ellipsis' => '...', 'exact' => true, 'html' => true
		);

		$options = array_merge($default, $options);
		extract($options);

		if ($html)
		{
			if (Tools::strlen(preg_replace('/<.*?>/', '', $text)) <= $length) 
				return $text;

			$totalLength = Tools::strlen(strip_tags($ellipsis));
			$openTags = array();
			$truncate = '';
			preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);

			foreach ($tags as $tag)
			{
				if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2]))
				{
					if (preg_match('/<[\w]+[^>]*>/s', $tag[0]))
						array_unshift($openTags, $tag[2]);
					elseif (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag))
					{
						$pos = array_search($closeTag[1], $openTags);
						if ($pos !== false)
							array_splice($openTags, $pos, 1);
					}
				}
				$truncate .= $tag[1];
				$contentLength = Tools::strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));

				if ($contentLength + $totalLength > $length)
				{
					$left = $length - $totalLength;
					$entitiesLength = 0;

					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE))
					{
						foreach ($entities[0] as $entity)
						{
							if ($entity[1] + 1 - $entitiesLength <= $left)
							{
								$left--;
								$entitiesLength += Tools::strlen($entity[0]);
							}
							else
								break;
						}
					}

					$truncate .= Tools::substr($tag[3], 0, $left + $entitiesLength);
					break;
				}
				else
				{
					$truncate .= $tag[3];
					$totalLength += $contentLength;
				}

				if ($totalLength >= $length)
					break;
			}
		}
		else
		{
			if (Tools::strlen($text) <= $length)
				return $text;

			$truncate = Tools::substr($text, 0, $length - Tools::strlen($ellipsis));
		}

		if (!$exact)
		{
			$spacepos = Tools::strrpos($truncate, ' ');
			if ($html)
			{
				$truncateCheck = Tools::substr($truncate, 0, $spacepos);
				$lastOpenTag = Tools::strrpos($truncateCheck, '<');
				$lastCloseTag =  Tools::strrpos($truncateCheck, '>');

				if ($lastOpenTag > $lastCloseTag)
				{
					preg_match_all('/<[\w]+[^>]*>/s', $truncate, $lastTagMatches);
					$lastTag = array_pop($lastTagMatches[0]);
					$spacepos =  Tools::strrpos($truncate, $lastTag) + Tools::strlen($lastTag);
				}

				$bits = Tools::substr($truncate, $spacepos);
				preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);

				if (!empty($droppedTags))
				{
					if (!empty($openTags))
					{
						foreach ($droppedTags as $closingTag)
							if (!in_array($closingTag[1], $openTags))
								array_unshift($openTags, $closingTag[1]);
					}
					else
					{
						foreach ($droppedTags as $closingTag)
							$openTags[] = $closingTag[1];
					}
				}
			}

			$truncate = Tools::substr($truncate, 0, $spacepos);
		}

		$truncate .= $ellipsis;

		if ($html)
			foreach ($openTags as $tag)
				$truncate .= '</' . $tag . '>';

		return $truncate;
	}

	public static function normalizeDirectory($directory)
	{
		$last = $directory[strlen($directory) - 1];

		if (in_array($last, array('/', '\\')))
		{
			$directory[strlen($directory) - 1] = DIRECTORY_SEPARATOR;
			return $directory;
		}

		$directory .= DIRECTORY_SEPARATOR;
		return $directory;
	}

	/**
	* Generate date form
	*
	* @param integer $year Year to select
	* @param integer $month Month to select
	* @param integer $day Day to select
	* @return array $tab html data with 3 cells :['days'], ['months'], ['years']
	*
	*/
	public static function dateYears()
	{
		$tab = array();
		for ($i = date('Y'); $i >= 1900; $i--)
			$tab[] = $i;
		return $tab;
	}

	public static function dateDays()
	{
		$tab = array();
		for ($i = 1; $i != 32; $i++)
			$tab[] = $i;
		return $tab;
	}

	public static function dateMonths()
	{
		$tab = array();
		for ($i = 1; $i != 13; $i++)
			$tab[$i] = date('F', mktime(0, 0, 0, $i, date('m'), date('Y')));
		return $tab;
	}

	public static function hourGenerate($hours, $minutes, $seconds)
	{
	    return implode(':', array($hours, $minutes, $seconds));
	}

	public static function dateFrom($date)
	{
		$tab = explode(' ', $date);
		if (!isset($tab[1]))
		    $date .= ' '.Tools::hourGenerate(0, 0, 0);
		return $date;
	}

	public static function dateTo($date)
	{
		$tab = explode(' ', $date);
		if (!isset($tab[1]))
		    $date .= ' '.Tools::hourGenerate(23, 59, 59);
		return $date;
	}

	public static function strtolower($str)
	{
		if (is_array($str))
			return false;
		if (function_exists('mb_strtolower'))
			return mb_strtolower($str, 'utf-8');
		return strtolower($str);
	}

	public static function strlen($str, $encoding = 'UTF-8')
	{
		if (is_array($str))
			return false;
		$str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
		if (function_exists('mb_strlen'))
			return mb_strlen($str, $encoding);
		return strlen($str);
	}

	public static function stripslashes($string)
	{
		if (_PS_MAGIC_QUOTES_GPC_)
			$string = stripslashes($string);
		return $string;
	}

	public static function strtoupper($str)
	{
		if (is_array($str))
			return false;
		if (function_exists('mb_strtoupper'))
			return mb_strtoupper($str, 'utf-8');
		return strtoupper($str);
	}

	public static function substr($str, $start, $length = false, $encoding = 'utf-8')
	{
		if (is_array($str))
			return false;
		if (function_exists('mb_substr'))
			return mb_substr($str, (int)$start, ($length === false ? Tools::strlen($str) : (int)$length), $encoding);
		return substr($str, $start, ($length === false ? Tools::strlen($str) : (int)$length));
	}

	public static function strpos($str, $find, $offset = 0, $encoding = 'UTF-8')
	{
		if (function_exists('mb_strpos'))
			return mb_strpos($str, $find, $offset, $encoding);
		return strpos($str, $find, $offset);
	}

	public static function strrpos($str, $find, $offset = 0, $encoding = 'utf-8')
	{
		if (function_exists('mb_strrpos'))
			return mb_strrpos($str, $find, $offset, $encoding);
		return strrpos($str, $find, $offset);
	}	

	public static function ucfirst($str)
	{
		return Tools::strtoupper(Tools::substr($str, 0, 1)).Tools::substr($str, 1);
	}

	public static function ucwords($str)
	{
		if (function_exists('mb_convert_case'))
			return mb_convert_case($str, MB_CASE_TITLE);
		return ucwords(Tools::strtolower($str));
	}

	public static function orderbyPrice(&$array, $order_way)
	{
		foreach ($array as &$row)
			$row['price_tmp'] = Product::getPriceStatic($row['id_product'], true, ((isset($row['id_product_attribute']) && !empty($row['id_product_attribute'])) ? (int)$row['id_product_attribute'] : null), 2);
		if (Tools::strtolower($order_way) == 'desc')
			uasort($array, 'cmpPriceDesc');
		else
			uasort($array, 'cmpPriceAsc');
		foreach ($array as &$row)
			unset($row['price_tmp']);
	}

	public static function iconv($from, $to, $string)
	{
		if (function_exists('iconv'))
			return iconv($from, $to.'//TRANSLIT', str_replace('¥', '&yen;', str_replace('£', '&pound;', str_replace('€', '&euro;', $string))));
		return html_entity_decode(htmlentities($string, ENT_NOQUOTES, $from), ENT_NOQUOTES, $to);
	}

	public static function isEmpty($field)
	{
		return ($field === '' || $field === null);
	}

	/**
	 * returns the rounded value of $value to specified precision, according to your configuration;
	 *
	 * @note : PHP 5.3.0 introduce a 3rd parameter mode in round function
	 *
	 * @param float $value
	 * @param int $precision
	 * @return float
	 */
	public static function ps_round($value, $precision = 0)
	{
		static $method = null;

		if ($method == null)
			$method = (int)Configuration::get('PS_PRICE_ROUND_MODE');

		if ($method == PS_ROUND_UP)
			return Tools::ceilf($value, $precision);
		elseif ($method == PS_ROUND_DOWN)
			return Tools::floorf($value, $precision);
		return round($value, $precision);
	}

	/**
	 * returns the rounded value down of $value to specified precision
	 *
	 * @param float $value
	 * @param int $precision
	 * @return float
	 */
	public static function ceilf($value, $precision = 0)
	{
		$precision_factor = $precision == 0 ? 1 : pow(10, $precision);
		$tmp = $value * $precision_factor;
		$tmp2 = (string)$tmp;
		// If the current value has already the desired precision
		if (strpos($tmp2, '.') === false)
			return ($value);
		if ($tmp2[strlen($tmp2) - 1] == 0)
			return $value;
		return ceil($tmp) / $precision_factor;
	}

	/**
	 * returns the rounded value up of $value to specified precision
	 *
	 * @param float $value
	 * @param int $precision
	 * @return float
	 */
	public static function floorf($value, $precision = 0)
	{
		$precision_factor = $precision == 0 ? 1 : pow(10, $precision);
		$tmp = $value * $precision_factor;
		$tmp2 = (string)$tmp;
		// If the current value has already the desired precision
		if (strpos($tmp2, '.') === false)
			return ($value);
		if ($tmp2[strlen($tmp2) - 1] == 0)
			return $value;
		return floor($tmp) / $precision_factor;
	}

	/**
	 * file_exists() wrapper with cache to speedup performance
	 *
	 * @param string $filename File name
	 * @return boolean Cached result of file_exists($filename)
	 */
	public static function file_exists_cache($filename)
	{
		if (!isset(self::$file_exists_cache[$filename]))
			self::$file_exists_cache[$filename] = file_exists($filename);
		return self::$file_exists_cache[$filename];
	}

	/**
	 * file_exists() wrapper with a call to clearstatcache prior
	 *
	 * @param string $filename File name
	 * @return boolean Cached result of file_exists($filename)
	 */
	public static function file_exists_no_cache($filename)
	{
		clearstatcache();
		return file_exists($filename);
	}

	public static function file_get_contents($url, $use_include_path = false, $stream_context = null, $curl_timeout = 5)
	{
		if ($stream_context == null && preg_match('/^https?:\/\//', $url))
			$stream_context = @stream_context_create(array('http' => array('timeout' => $curl_timeout)));
		if (in_array(ini_get('allow_url_fopen'), array('On', 'on', '1')) || !preg_match('/^https?:\/\//', $url))
			return @file_get_contents($url, $use_include_path, $stream_context);
		elseif (function_exists('curl_init'))
		{
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($curl, CURLOPT_TIMEOUT, $curl_timeout);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			if ($stream_context != null) {
				$opts = stream_context_get_options($stream_context);
				if (isset($opts['http']['method']) && Tools::strtolower($opts['http']['method']) == 'post')
				{
					curl_setopt($curl, CURLOPT_POST, true);
					if (isset($opts['http']['content']))
					{
						parse_str($opts['http']['content'], $datas);
						curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
					}
				}
			}
			$content = curl_exec($curl);
			curl_close($curl);
			return $content;
		}
		else
			return false;
	}

	public static function simplexml_load_file($url, $class_name = null)
	{
		return @simplexml_load_string(Tools::file_get_contents($url), $class_name);
	}
	
	public static function copy($source, $destination, $stream_context = null)
	{
		if (is_null($stream_context) && !preg_match('/^https?:\/\//', $source))
			return @copy($source, $destination);
		return @file_put_contents($destination, Tools::file_get_contents($source, false, $stream_context));
	}

	/**
	 * @deprecated as of 1.5 use Media::minifyHTML()
	 */
	public static function minifyHTML($html_content)
	{
		Tools::displayAsDeprecated();
		return Media::minifyHTML($html_content);
	}

	/**
	* Translates a string with underscores into camel case (e.g. first_name -> firstName)
	* @prototype string public static function toCamelCase(string $str[, bool $capitalise_first_char = false])
	*/
	public static function toCamelCase($str, $catapitalise_first_char = false)
	{
		$str = Tools::strtolower($str);
		if ($catapitalise_first_char)
			$str = Tools::ucfirst($str);
		return preg_replace_callback('/_+([a-z])/', create_function('$c', 'return strtoupper($c[1]);'), $str);
	}

	/**
	 * Transform a CamelCase string to underscore_case string
	 *
	 * @param string $string
	 * @return string
	 */
	public static function toUnderscoreCase($string)
	{
		// 'CMSCategories' => 'cms_categories'
		// 'RangePrice' => 'range_price'
		return Tools::strtolower(trim(preg_replace('/([A-Z][a-z])/', '_$1', $string), '_'));
	}

	public static function getBrightness($hex)
	{
		$hex = str_replace('#', '', $hex);
		$r = hexdec(substr($hex, 0, 2));
		$g = hexdec(substr($hex, 2, 2));
		$b = hexdec(substr($hex, 4, 2));
		return (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
	}

	/**
	* @deprecated as of 1.5 use Media::minifyHTMLpregCallback()
	*/
	public static function minifyHTMLpregCallback($preg_matches)
	{
		Tools::displayAsDeprecated();
		return Media::minifyHTMLpregCallback($preg_matches);
	}

	/**
	* @deprecated as of 1.5 use Media::packJSinHTML()
	*/
	public static function packJSinHTML($html_content)
	{
		Tools::displayAsDeprecated();
		return Media::packJSinHTML($html_content);
	}

	/**
	* @deprecated as of 1.5 use Media::packJSinHTMLpregCallback()
	*/
	public static function packJSinHTMLpregCallback($preg_matches)
	{
		Tools::displayAsDeprecated();
		return Media::packJSinHTMLpregCallback($preg_matches);
	}

	/**
	* @deprecated as of 1.5 use Media::packJS()
	*/
	public static function packJS($js_content)
	{
		Tools::displayAsDeprecated();
		return Media::packJS($js_content);
	}


	public static function parserSQL($sql)
	{
		if (strlen($sql) > 0)
		{
			require_once(_PS_TOOL_DIR_.'parser_sql/php-sql-parser.php');
			$parser = new parserSql($sql);
			return $parser->parsed;
		}
		return false;
	}

	/**
	 * @deprecated as of 1.5 use Media::minifyCSS()
	 */
	public static function minifyCSS($css_content, $fileuri = false)
	{
		Tools::displayAsDeprecated();
		return Media::minifyCSS($css_content, $fileuri);
	}

	public static function replaceByAbsoluteURL($matches)
	{
		global $current_css_file;
		$protocol_link = Tools::getCurrentUrlProtocolPrefix();
		if (array_key_exists(1, $matches) && array_key_exists(2, $matches))
		{
			if (!preg_match('/^(?:https?:)?\/\//iUs', $matches[2]))
			{
				$tmp = dirname($current_css_file).'/'.$matches[2];
				return $matches[1].$protocol_link.Tools::getMediaServer($tmp).$tmp;
			} 
			else
				return $matches[0];
		}
		return false;
	}

	/**
	 * addJS load a javascript file in the header
	 *
	 * @deprecated as of 1.5 use FrontController->addJS()
	 * @param mixed $js_uri
	 * @return void
	 */
	public static function addJS($js_uri)
	{
		Tools::displayAsDeprecated();
		$context = Context::getContext();
		$context->controller->addJs($js_uri);
	}

	/**
	 * @deprecated as of 1.5 use FrontController->addCSS()
	 */
	public static function addCSS($css_uri, $css_media_type = 'all')
	{
		Tools::displayAsDeprecated();
		$context = Context::getContext();
		$context->controller->addCSS($css_uri, $css_media_type);
	}

	/**
	* @deprecated as of 1.5 use Media::cccCss()
	*/
	public static function cccCss($css_files)
	{
		Tools::displayAsDeprecated();
		return Media::cccCss($css_files);
	}


	/**
	* @deprecated as of 1.5 use Media::cccJS()
	*/
	public static function cccJS($js_files)
	{
		Tools::displayAsDeprecated();
		return Media::cccJS($js_files);
	}

	protected static $_cache_nb_media_servers = null;

	public static function getMediaServer($filename)
	{
		if (self::$_cache_nb_media_servers === null && defined('_MEDIA_SERVER_1_') && defined('_MEDIA_SERVER_2_') && defined('_MEDIA_SERVER_3_'))
		{
			if (_MEDIA_SERVER_1_ == '')
				self::$_cache_nb_media_servers = 0;
			elseif (_MEDIA_SERVER_2_ == '')
				self::$_cache_nb_media_servers = 1;
			elseif (_MEDIA_SERVER_3_ == '')
				self::$_cache_nb_media_servers = 2;
			else
				self::$_cache_nb_media_servers = 3;
		}

		if (self::$_cache_nb_media_servers && ($id_media_server = (abs(crc32($filename)) % self::$_cache_nb_media_servers + 1)))
			return constant('_MEDIA_SERVER_'.$id_media_server.'_');

		return Tools::usingSecureMode() ? Tools::getShopDomainSSL() : Tools::getShopDomain();
	}

	public static function generateHtaccess($path = null, $rewrite_settings = null, $cache_control = null, $specific = '', $disable_multiviews = null, $medias = false, $disable_modsec = null)
	{
		if (defined('PS_INSTALLATION_IN_PROGRESS') && $rewrite_settings === null)
			return true;

		// Default values for parameters
		if (is_null($path))
			$path = _PS_ROOT_DIR_.'/.htaccess';
		if (is_null($cache_control))
			$cache_control = (int)Configuration::get('PS_HTACCESS_CACHE_CONTROL');
		if (is_null($disable_multiviews))
			$disable_multiviews = (int)Configuration::get('PS_HTACCESS_DISABLE_MULTIVIEWS');

		if ($disable_modsec === null)
			$disable_modsec =  (int)Configuration::get('PS_HTACCESS_DISABLE_MODSEC');

		// Check current content of .htaccess and save all code outside of prestashop comments
		$specific_before = $specific_after = '';
		if (file_exists($path))
		{
			$content = file_get_contents($path);
			if (preg_match('#^(.*)\# ~~start~~.*\# ~~end~~[^\n]*(.*)$#s', $content, $m))
			{
				$specific_before = $m[1];
				$specific_after = $m[2];
			}
			else
			{
				// For retrocompatibility
				if (preg_match('#\# http://www\.prestashop\.com - http://www\.prestashop\.com/forums\s*(.*)<IfModule mod_rewrite\.c>#si', $content, $m))
					$specific_before = $m[1];
				else
					$specific_before = $content;
			}
		}

		// Write .htaccess data
		if (!$write_fd = fopen($path, 'w'))
			return false;
		if ($specific_before)
			fwrite($write_fd, trim($specific_before)."\n\n");

		$domains = array();
		foreach (ShopUrl::getShopUrls() as $shop_url)
		{
			if (!isset($domains[$shop_url->domain]))
				$domains[$shop_url->domain] = array();

			$domains[$shop_url->domain][] = array(
				'physical' =>	$shop_url->physical_uri,
				'virtual' =>	$shop_url->virtual_uri,
				'id_shop' =>	$shop_url->id_shop
			);
			
			if ($shop_url->domain == $shop_url->domain_ssl)
				continue;
			
			if (!isset($domains[$shop_url->domain_ssl]))
				$domains[$shop_url->domain_ssl] = array();

			$domains[$shop_url->domain_ssl][] = array(
				'physical' =>	$shop_url->physical_uri,
				'virtual' =>	$shop_url->virtual_uri,
				'id_shop' =>	$shop_url->id_shop
			);
		}

		// Write data in .htaccess file
		fwrite($write_fd, "# ~~start~~ Do not remove this comment, Prestashop will keep automatically the code outside this comment when .htaccess will be generated again\n");
		fwrite($write_fd, "# .htaccess automaticaly generated by PrestaShop e-commerce open-source solution\n");
		fwrite($write_fd, "# http://www.prestashop.com - http://www.prestashop.com/forums\n\n");

		if ($disable_modsec)
			fwrite($write_fd, "<IfModule mod_security.c>\nSecFilterEngine Off\nSecFilterScanPOST Off\n</IfModule>\n\n");

		// RewriteEngine
		fwrite($write_fd, "<IfModule mod_rewrite.c>\n");

		// Ensure HTTP_MOD_REWRITE variable is set in environment
		fwrite($write_fd, "<IfModule mod_env.c>\n");
		fwrite($write_fd, "SetEnv HTTP_MOD_REWRITE On\n");
		fwrite($write_fd, "</IfModule>\n\n");

		// Disable multiviews ?
		if ($disable_multiviews)
			fwrite($write_fd, "\n# Disable Multiviews\nOptions -Multiviews\n\n");

		fwrite($write_fd, "RewriteEngine on\n");
	
		if (!$medias && defined('_MEDIA_SERVER_1_') && defined('_MEDIA_SERVER_2_') && defined('_MEDIA_SERVER_3_'))
			$medias = array(_MEDIA_SERVER_1_, _MEDIA_SERVER_2_, _MEDIA_SERVER_3_);
		
		$media_domains = '';
		if ($medias[0] != '')
			$media_domains = 'RewriteCond %{HTTP_HOST} ^'.$medias[0].'$ [OR]'."\n";
		if ($medias[1] != '')
			$media_domains .= 'RewriteCond %{HTTP_HOST} ^'.$medias[1].'$ [OR]'."\n";
		if ($medias[2] != '')
			$media_domains .= 'RewriteCond %{HTTP_HOST} ^'.$medias[2].'$ [OR]'."\n";

		if (Configuration::get('PS_WEBSERVICE_CGI_HOST'))
			fwrite($write_fd, "RewriteCond %{HTTP:Authorization} ^(.*)\nRewriteRule . - [E=HTTP_AUTHORIZATION:%1]\n\n");

		foreach ($domains as $domain => $list_uri)
		{
			$physicals = array();
			foreach ($list_uri as $uri)
			{
				if (Shop::isFeatureActive())
					fwrite($write_fd, 'RewriteCond %{HTTP_HOST} ^'.$domain.'$'."\n");
				fwrite($write_fd, 'RewriteRule . - [E=REWRITEBASE:'.$uri['physical'].']'."\n");
				
				// Webservice
				fwrite($write_fd, 'RewriteRule ^api/?(.*)$ %{ENV:REWRITEBASE}webservice/dispatcher.php?url=$1 [QSA,L]'."\n\n");

				if (!$rewrite_settings)
					$rewrite_settings = (int)Configuration::get('PS_REWRITING_SETTINGS', null, null, (int)$uri['id_shop']);

				$domain_rewrite_cond = 'RewriteCond %{HTTP_HOST} ^'.$domain.'$'."\n";
				// Rewrite virtual multishop uri
				if ($uri['virtual'])
				{
					if (!$rewrite_settings)
					{
						fwrite($write_fd, $media_domains);
						if (Shop::isFeatureActive())
							fwrite($write_fd, $domain_rewrite_cond);
						fwrite($write_fd, 'RewriteRule ^'.trim($uri['virtual'], '/').'/?$ '.$uri['physical'].$uri['virtual']."index.php [L,R]\n");
					}
					else
					{
						fwrite($write_fd, $media_domains);
						if (Shop::isFeatureActive())
							fwrite($write_fd, $domain_rewrite_cond);
						fwrite($write_fd, 'RewriteRule ^'.trim($uri['virtual'], '/').'$ '.$uri['physical'].$uri['virtual']." [L,R]\n");
					}
					fwrite($write_fd, $media_domains);
					if (Shop::isFeatureActive())
						fwrite($write_fd, $domain_rewrite_cond);
					fwrite($write_fd, 'RewriteRule ^'.ltrim($uri['virtual'], '/').'(.*) '.$uri['physical']."$1 [L]\n\n");
				}			

				if ($rewrite_settings)
				{
					// Compatibility with the old image filesystem
					fwrite($write_fd, "# Images\n");
					if (Configuration::get('PS_LEGACY_IMAGES'))
					{
						fwrite($write_fd, $media_domains);
						if (Shop::isFeatureActive())
							fwrite($write_fd, $domain_rewrite_cond);
						fwrite($write_fd, 'RewriteRule ^([a-z0-9]+)\-([a-z0-9]+)(\-[_a-zA-Z0-9-]*)(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/p/$1-$2$3$4.jpg [L]'."\n");
						fwrite($write_fd, $media_domains);
						if (Shop::isFeatureActive())
							fwrite($write_fd, $domain_rewrite_cond);
						fwrite($write_fd, 'RewriteRule ^([0-9]+)\-([0-9]+)(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/p/$1-$2$3.jpg [L]'."\n");
					}

					// Rewrite product images < 100 millions
					for ($i = 1; $i <= 8; $i++)
					{
						$img_path = $img_name = '';
						for ($j = 1; $j <= $i; $j++)
						{
							$img_path .= '$'.$j.'/';
							$img_name .= '$'.$j;
						}
						$img_name .= '$'.$j;
						fwrite($write_fd, $media_domains);
						if (Shop::isFeatureActive())
							fwrite($write_fd, $domain_rewrite_cond);
						fwrite($write_fd, 'RewriteRule ^'.str_repeat('([0-9])', $i).'(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/p/'.$img_path.$img_name.'$'.($j + 1).".jpg [L]\n");
					}
					fwrite($write_fd, $media_domains);
					if (Shop::isFeatureActive())
						fwrite($write_fd, $domain_rewrite_cond);
					fwrite($write_fd, 'RewriteRule ^c/([0-9]+)(\-[\.*_a-zA-Z0-9-]*)(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/c/$1$2$3.jpg [L]'."\n");
					fwrite($write_fd, $media_domains);
					if (Shop::isFeatureActive())
						fwrite($write_fd, $domain_rewrite_cond);
					fwrite($write_fd, 'RewriteRule ^c/([a-zA-Z_-]+)(-[0-9]+)?/.+\.jpg$ %{ENV:REWRITEBASE}img/c/$1$2.jpg [L]'."\n");
				}
				
				fwrite($write_fd, "# AlphaImageLoader for IE and fancybox\n");
				if (Shop::isFeatureActive())
					fwrite($write_fd, $domain_rewrite_cond);
				fwrite($write_fd, 'RewriteRule ^images_ie/?([^/]+)\.(jpe?g|png|gif)$ js/jquery/plugins/fancybox/images/$1.$2 [L]'."\n");
			}
			// Redirections to dispatcher
			if ($rewrite_settings)
			{
				fwrite($write_fd, "\n# Dispatcher\n");
				fwrite($write_fd, "RewriteCond %{REQUEST_FILENAME} -s [OR]\n");
				fwrite($write_fd, "RewriteCond %{REQUEST_FILENAME} -l [OR]\n");
				fwrite($write_fd, "RewriteCond %{REQUEST_FILENAME} -d\n");
				if (Shop::isFeatureActive())
					fwrite($write_fd, $domain_rewrite_cond);
				fwrite($write_fd, "RewriteRule ^.*$ - [NC,L]\n");
				if (Shop::isFeatureActive())
					fwrite($write_fd, $domain_rewrite_cond);
				fwrite($write_fd, "RewriteRule ^.*\$ %{ENV:REWRITEBASE}index.php [NC,L]\n");
			}
		}

		fwrite($write_fd, "</IfModule>\n\n");

		fwrite($write_fd, "AddType application/vnd.ms-fontobject .eot\n");
		fwrite($write_fd, "AddType font/ttf .ttf\n");
		fwrite($write_fd, "AddType font/otf .otf\n");
		fwrite($write_fd, "AddType application/x-font-woff .woff\n\n");

		// Cache control
		if ($cache_control)
		{
			$cache_control = "<IfModule mod_expires.c>
	ExpiresActive On
	ExpiresByType image/gif \"access plus 1 month\"
	ExpiresByType image/jpeg \"access plus 1 month\"
	ExpiresByType image/png \"access plus 1 month\"
	ExpiresByType text/css \"access plus 1 week\"
	ExpiresByType text/javascript \"access plus 1 week\"
	ExpiresByType application/javascript \"access plus 1 week\"
	ExpiresByType application/x-javascript \"access plus 1 week\"
	ExpiresByType image/x-icon \"access plus 1 year\"
	ExpiresByType image/svg+xml \"access plus 1 year\"
	ExpiresByType image/vnd.microsoft.icon \"access plus 1 year\"
	ExpiresByType application/font-woff \"access plus 1 year\"
	ExpiresByType application/x-font-woff \"access plus 1 year\"
	ExpiresByType application/vnd.ms-fontobject \"access plus 1 year\"
	ExpiresByType font/opentype \"access plus 1 year\"
	ExpiresByType font/ttf \"access plus 1 year\"
	ExpiresByType font/otf \"access plus 1 year\"
	ExpiresByType application/x-font-ttf \"access plus 1 year\"
	ExpiresByType application/x-font-otf \"access plus 1 year\"
</IfModule>

FileETag INode MTime Size
<IfModule mod_deflate.c>
	<IfModule mod_filter.c>
		AddOutputFilterByType DEFLATE text/html text/css text/javascript application/javascript application/x-javascript
	</IfModule>
</IfModule>\n\n";
			fwrite($write_fd, $cache_control);
		}

		// In case the user hasn't rewrite mod enabled
		fwrite($write_fd, "#If rewrite mod isn't enabled\n");

		// Do not remove ($domains is already iterated upper)
		reset($domains);
		$domain = current($domains);
		fwrite($write_fd, 'ErrorDocument 404 '.$domain[0]['physical']."index.php?controller=404\n\n");

		fwrite($write_fd, "# ~~end~~ Do not remove this comment, Prestashop will keep automatically the code outside this comment when .htaccess will be generated again");
		if ($specific_after)
			fwrite($write_fd, "\n\n".trim($specific_after));
		fclose($write_fd);
		
		if (!defined('PS_INSTALLATION_IN_PROGRESS'))
			Hook::exec('actionHtaccessCreate');

		return true;
	}

	public static function generateIndex()
	{
		if (defined('_DB_PREFIX_') && Configuration::get('PS_DISABLE_OVERRIDES'))
			PrestaShopAutoload::getInstance()->_include_override_path = false;
		PrestaShopAutoload::getInstance()->generateIndex();
	}
	
	public static function getDefaultIndexContent()
	{
		return '<?php
/*
* 2007-'.date('Y').' PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-'.date('Y').' PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

header("Location: ../");
exit;
';
	}

	/**
	 * jsonDecode convert json string to php array / object
	 *
	 * @param string $json
	 * @param boolean $assoc  (since 1.4.2.4) if true, convert to associativ array
	 * @return array
	 */
	public static function jsonDecode($json, $assoc = false)
	{
		if (function_exists('json_decode'))
			return json_decode($json, $assoc);
		else
		{
			include_once(_PS_TOOL_DIR_.'json/json.php');
			$pear_json = new Services_JSON(($assoc) ? SERVICES_JSON_LOOSE_TYPE : 0);
			return $pear_json->decode($json);
		}
	}

	/**
	 * Convert an array to json string
	 *
	 * @param array $data
	 * @return string json
	 */
	public static function jsonEncode($data)
	{
		if (function_exists('json_encode'))
			return json_encode($data);
		else
		{
			include_once(_PS_TOOL_DIR_.'json/json.php');
			$pear_json = new Services_JSON();
			return $pear_json->encode($data);
		}
	}

	/**
	 * Display a warning message indicating that the method is deprecated
	 */
	public static function displayAsDeprecated($message = null)
	{
		$backtrace = debug_backtrace();
		$callee = next($backtrace);
		$class = isset($callee['class']) ? $callee['class'] : null;
		if ($message === null)
			$message = 'The function '.$callee['function'].' (Line '.$callee['line'].') is deprecated and will be removed in the next major version.';
		$error = 'Function <b>'.$callee['function'].'()</b> is deprecated in <b>'.$callee['file'].'</b> on line <b>'.$callee['line'].'</b><br />';

		Tools::throwDeprecated($error, $message, $class);
	}

	/**
	 * Display a warning message indicating that the parameter is deprecated
	 */
	public static function displayParameterAsDeprecated($parameter)
	{
		$backtrace = debug_backtrace();
		$callee = next($backtrace);
		$error = 'Parameter <b>'.$parameter.'</b> in function <b>'.(isset($callee['function']) ? $callee['function'] : '').'()</b> is deprecated in <b>'.$callee['file'].'</b> on line <b>'.(isset($callee['line']) ? $callee['line'] : '(undefined)').'</b><br />';
		$message = 'The parameter '.$parameter.' in function '.$callee['function'].' (Line '.(isset($callee['line']) ? $callee['line'] : 'undefined').') is deprecated and will be removed in the next major version.';
		$class = isset($callee['class']) ? $callee['class'] : null;

		Tools::throwDeprecated($error, $message, $class);
	}

	public static function displayFileAsDeprecated()
	{
		$backtrace = debug_backtrace();
		$callee = current($backtrace);
		$error = 'File <b>'.$callee['file'].'</b> is deprecated<br />';
		$message = 'The file '.$callee['file'].' is deprecated and will be removed in the next major version.';
		$class = isset($callee['class']) ? $callee['class'] : null;
		
		Tools::throwDeprecated($error, $message, $class);
	}

	protected static function throwDeprecated($error, $message, $class)
	{
		if (_PS_DISPLAY_COMPATIBILITY_WARNING_)
		{
			trigger_error($error, E_USER_WARNING);
			PrestaShopLogger::addLog($message, 3, $class);
		}
	}

	public static function enableCache($level = 1, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();
		$smarty = $context->smarty;
		if (!Configuration::get('PS_SMARTY_CACHE'))
			return;
		if ($smarty->force_compile == 0 && $smarty->caching == $level)
			return;
		self::$_forceCompile = (int)$smarty->force_compile;
		self::$_caching = (int)$smarty->caching;
		$smarty->force_compile = 0;
		$smarty->caching = (int)$level;
		$smarty->cache_lifetime = 31536000; // 1 Year
	}

	public static function restoreCacheSettings(Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		if (isset(self::$_forceCompile))
			$context->smarty->force_compile = (int)self::$_forceCompile;
		if (isset(self::$_caching))
			$context->smarty->caching = (int)self::$_caching;
	}

	public static function isCallable($function)
	{
		$disabled = explode(',', ini_get('disable_functions'));
		return (!in_array($function, $disabled) && is_callable($function));
	}

	public static function pRegexp($s, $delim)
	{
		$s = str_replace($delim, '\\'.$delim, $s);
		foreach (array('?', '[', ']', '(', ')', '{', '}', '-', '.', '+', '*', '^', '$', '`', '"', '%') as $char)
			$s = str_replace($char, '\\'.$char, $s);
		return $s;
	}

	public static function str_replace_once($needle, $replace, $haystack)
	{
		$pos = false;
		if ($needle)
			$pos = strpos($haystack, $needle);
		if ($pos === false)
			return $haystack;
		return substr_replace($haystack, $replace, $pos, strlen($needle));
	}

	/**
	 * Function property_exists does not exist in PHP < 5.1
	 *
	 * @deprecated since 1.5.0 (PHP 5.1 required, so property_exists() is now natively supported)
	 * @param object or class $class
	 * @param string $property
	 * @return boolean
	 */
	public static function property_exists($class, $property)
	{
		Tools::displayAsDeprecated();

		if (function_exists('property_exists'))
			return property_exists($class, $property);

		if (is_object($class))
			$vars = get_object_vars($class);
		else
			$vars = get_class_vars($class);

		return array_key_exists($property, $vars);
	}

	/**
	 * @desc identify the version of php
	 * @return string
	 */
	public static function checkPhpVersion()
	{
		$version = null;

		if (defined('PHP_VERSION'))
			$version = PHP_VERSION;
		else
			$version  = phpversion('');

		//Case management system of ubuntu, php version return 5.2.4-2ubuntu5.2
		if (strpos($version, '-') !== false)
			$version  = substr($version, 0, strpos($version, '-'));

		return $version;
	}

	/**
	 * @desc try to open a zip file in order to check if it's valid
	 * @return bool success
	 */
	public static function ZipTest($from_file)
	{
		if (class_exists('ZipArchive', false))
		{
			$zip = new ZipArchive();
			return ($zip->open($from_file, ZIPARCHIVE::CHECKCONS) === true);
		}
		else
		{
			require_once(_PS_ROOT_DIR_.'/tools/pclzip/pclzip.lib.php');
			$zip = new PclZip($from_file);
			return ($zip->privCheckFormat() === true);
		}
	}

	public static function getSafeModeStatus()
	{
		if (!$safe_mode = @ini_get('safe_mode'))
			$safe_mode = '';
		return in_array(Tools::strtolower($safe_mode), array(1, 'on'));
	}

	/**
	 * @desc extract a zip file to the given directory
	 * @return bool success
	 */
	public static function ZipExtract($from_file, $to_dir)
	{
		if (!file_exists($to_dir))
			mkdir($to_dir, 0777);
		if (class_exists('ZipArchive', false))
		{
			$zip = new ZipArchive();
			if ($zip->open($from_file) === true && $zip->extractTo($to_dir) && $zip->close())
				return true;
			return false;
		}
		else
		{
			require_once(_PS_ROOT_DIR_.'/tools/pclzip/pclzip.lib.php');
			$zip = new PclZip($from_file);
			$list = $zip->extract(PCLZIP_OPT_PATH, $to_dir, PCLZIP_OPT_REPLACE_NEWER);
			foreach ($list as $file)
				if ($file['status'] != 'ok' && $file['status'] != 'already_a_directory')
					return false;
			return true;
		}
	}

	public static function chmodr($path, $filemode)
	{
	    if (!is_dir($path))
	        return @chmod($path, $filemode);
	    $dh = opendir($path);
	    while (($file = readdir($dh)) !== false)
		{
	        if ($file != '.' && $file != '..')
			{
	            $fullpath = $path.'/'.$file;
	            if (is_link($fullpath))
	                return false;
	            elseif (!is_dir($fullpath) && !@chmod($fullpath, $filemode))
	                    return false;
	            elseif (!Tools::chmodr($fullpath, $filemode))
	                return false;
	        }
	    }
	    closedir($dh);
	    if (@chmod($path, $filemode))
	        return true;
	    else
	        return false;
	}

	/**
	 * Get products order field name for queries.
	 *
	 * @param string $type by|way
	 * @param string $value If no index given, use default order from admin -> pref -> products
	 * @param bool|\bool(false)|string $prefix
	 *
	 * @return string Order by sql clause
	 */
	public static function getProductsOrder($type, $value = null, $prefix = false)
	{
		switch ($type)
		{
			case 'by' :
				$list = array(0 => 'name', 1 => 'price', 2 => 'date_add', 3 => 'date_upd', 4 => 'position', 5 => 'manufacturer_name', 6 => 'quantity');
				$value = (is_null($value) || $value === false || $value === '') ? (int)Configuration::get('PS_PRODUCTS_ORDER_BY') : $value;
				$value = (isset($list[$value])) ? $list[$value] : ((in_array($value, $list)) ? $value : 'position');
				$order_by_prefix = '';
				if ($prefix)
				{
					if ($value == 'id_product' || $value == 'date_add' || $value == 'date_upd' || $value == 'price')
						$order_by_prefix = 'p.';
					elseif ($value == 'name')
						$order_by_prefix = 'pl.';
					elseif ($value == 'manufacturer_name' && $prefix)
					{
						$order_by_prefix = 'm.';
						$value = 'name';
					}
					elseif ($value == 'position' || empty($value))
						$order_by_prefix = 'cp.';
				}

				return $order_by_prefix.$value;
			break;

			case 'way' :
				$value = (is_null($value) || $value === false || $value === '') ? (int)Configuration::get('PS_PRODUCTS_ORDER_WAY') : $value;
				$list = array(0 => 'asc', 1 => 'desc');
				return ((isset($list[$value])) ? $list[$value] : ((in_array($value, $list)) ? $value : 'asc'));
			break;
		}
	}

	/**
	 * Convert a shorthand byte value from a PHP configuration directive to an integer value
	 * @param string $value value to convert
	 * @return int
	 */
	public static function convertBytes($value)
	{
		if (is_numeric($value))
			return $value;
		else
		{
			$value_length = strlen($value);
			$qty = (int)substr($value, 0, $value_length - 1 );
			$unit = Tools::strtolower(substr($value, $value_length - 1));
			switch ($unit)
			{
				case 'k':
					$qty *= 1024;
					break;
				case 'm':
					$qty *= 1048576;
					break;
				case 'g':
					$qty *= 1073741824;
					break;
			}
			return $qty;
		}
	}

	/**
	* @deprecated as of 1.5 use Controller::getController('PageNotFoundController')->run();
   	*/
	public static function display404Error()
	{
		header('HTTP/1.1 404 Not Found');
		header('Status: 404 Not Found');
		include(dirname(__FILE__).'/../404.php');
		die;
	}

	/**
	 * Concat $begin and $end, add ? or & between strings
	 *
	 * @since 1.5.0
	 * @param string $begin
	 * @param string $end
	 * @return string
	 */
	public static function url($begin, $end)
	{
		return $begin.((strpos($begin, '?') !== false) ? '&' : '?').$end;
	}

	/**
	 * Display error and dies or silently log the error.
	 *
	 * @param string $msg
	 * @param bool $die
	 * @return bool success of logging
	 */
	public static function dieOrLog($msg, $die = true)
	{
		if ($die || (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_))
			die($msg);
		return PrestaShopLogger::addLog($msg);
	}

	/**
	 * Convert \n and \r\n and \r to <br />
	 *
	 * @param string $string String to transform
	 * @return string New string
	 */
	public static function nl2br($str)
	{
		return str_replace(array("\r\n", "\r", "\n"), '<br />', $str);
	}

	/**
	 * Clear cache for Smarty
	 *
	 * @param Smarty $smarty
	 */
	public static function clearCache($smarty = null, $tpl = false, $cache_id = null, $compile_id = null)
	{
		if ($smarty === null)
			$smarty = Context::getContext()->smarty;

		if ($smarty === null)
			return;

		if (!$tpl && $cache_id === null && $compile_id === null)
			return $smarty->clearAllCache();

		return $smarty->clearCache($tpl, $cache_id, $compile_id);
	}

	/**
	 * Clear compile for Smarty
 	*/
	public static function clearCompile($smarty = null)
	{
		if ($smarty === null)
			$smarty = Context::getContext()->smarty;

		if ($smarty === null)
			return;

		return $smarty->clearCompiledTemplate();
	}

	/**
	* Clear Smarty cache and compile folders
 	*/
	public static function clearSmartyCache()
	{
		$smarty = Context::getContext()->smarty;
		Tools::clearCache($smarty);
		Tools::clearCompile($smarty);
	}

	public static function clearColorListCache($id_product = false)
	{

		// Change template dir if called from the BackOffice
		$current_template_dir = Context::getContext()->smarty->getTemplateDir();
		Context::getContext()->smarty->setTemplateDir(_PS_THEME_DIR_);
		Tools::clearCache(null, 'product-list-colors.tpl', ($id_product ? 'productlist_colors|'.(int)$id_product.'|'.(int)Context::getContext()->shop->id : 'productlist_colors'));
		Context::getContext()->smarty->setTemplateDir($current_template_dir);
	}

	/**
	 * getMemoryLimit allow to get the memory limit in octet
	 *
	 * @since 1.4.5.0
	 * @return int the memory limit value in octet
	 */
	public static function getMemoryLimit()
	{
		$memory_limit = @ini_get('memory_limit');

		return Tools::getOctets($memory_limit);
	}

	/**
	 * getOctet allow to gets the value of a configuration option in octet
	 *
	 * @since 1.5.0
	 * @return int the value of a configuration option in octet
	 */
	public static function getOctets($option)
	{
		if (preg_match('/[0-9]+k/i', $option))
			return 1024 * (int)$option;

		if (preg_match('/[0-9]+m/i', $option))
			return 1024 * 1024 * (int)$option;

		if (preg_match('/[0-9]+g/i', $option))
			return 1024 * 1024 * 1024 * (int)$option;

		return $option;
	}

	/**
	 *
	 * @return bool true if the server use 64bit arch
	 */
	public static function isX86_64arch()
	{
		return (PHP_INT_MAX == '9223372036854775807');
	}

	/**
	 *
	 * @return bool true if php-cli is used
	 */
	public static function isPHPCLI()
	{
		return (defined('STDIN') || (Tools::strtolower(php_sapi_name()) == 'cli' && (!isset($_SERVER['REMOTE_ADDR']) || empty($_SERVER['REMOTE_ADDR']))));
	}

	public static function argvToGET($argc, $argv)
	{
		if ($argc <= 1)
			return;

		// get the first argument and parse it like a query string
		parse_str($argv[1], $args);
		if (!is_array($args) || !count($args))
			return;
		$_GET = array_merge($args, $_GET);
		$_SERVER['QUERY_STRING'] = $argv[1];
	}

	/**
	 * Get max file upload size considering server settings and optional max value
	 *
	 * @param int $max_size optional max file size
	 * @return int max file size in bytes
	 */
	public static function getMaxUploadSize($max_size = 0)
	{
		$post_max_size = Tools::convertBytes(ini_get('post_max_size'));
		$upload_max_filesize = Tools::convertBytes(ini_get('upload_max_filesize'));
		if ($max_size > 0)
			$result = min($post_max_size, $upload_max_filesize, $max_size);
		else
			$result = min($post_max_size, $upload_max_filesize);
		return $result;
	}

	/**
	 * apacheModExists return true if the apache module $name is loaded
	 * @TODO move this method in class Information (when it will exist)
	 *
	 * Notes: This method requires either apache_get_modules or phpinfo()
	 * to be available. With CGI mod, we cannot get php modules
	 *
	 * @param string $name module name
	 * @return boolean true if exists
	 * @since 1.4.5.0
	 */
	public static function apacheModExists($name)
	{
		if (function_exists('apache_get_modules'))
		{
			static $apache_module_list = null;

			if (!is_array($apache_module_list))
				$apache_module_list = apache_get_modules();

			// we need strpos (example, evasive can be evasive20)
			foreach ($apache_module_list as $module)
			{
				if (strpos($module, $name) !== false)
					return true;
			}
		}
		return false;
	}

	/**
	 * Copy the folder $src into $dst, $dst is created if it do not exist
	 * @param      $src
	 * @param      $dst
	 * @param bool $del if true, delete the file after copy
	 */
	public static function recurseCopy($src, $dst, $del = false)
	{
		if (!Tools::file_exists_cache($src))
			return false;
		$dir = opendir($src);

		if (!Tools::file_exists_cache($dst))
			mkdir($dst);
		while (false !== ($file = readdir($dir)))
		{
			if (($file != '.') && ($file != '..'))
			{
				if (is_dir($src.DIRECTORY_SEPARATOR.$file))
					self::recurseCopy($src.DIRECTORY_SEPARATOR.$file, $dst.DIRECTORY_SEPARATOR.$file, $del);
				else
				{
					copy($src.DIRECTORY_SEPARATOR.$file, $dst.DIRECTORY_SEPARATOR.$file);
					if ($del && is_writable($src.DIRECTORY_SEPARATOR.$file))
						unlink($src.DIRECTORY_SEPARATOR.$file);
				}
			}
		}
		closedir($dir);
		if ($del && is_writable($src))
			rmdir($src);
	}

	/**
	 * @params string $path Path to scan
	 * @params string $ext Extention to filter files
	 * @params string $dir Add this to prefix output for example /path/dir/*
	 *
	 * @return array List of file found
	 * @since 1.5.0
	 */
	public static function scandir($path, $ext = 'php', $dir = '', $recursive = false)
	{
		$path = rtrim(rtrim($path, '\\'), '/').'/';
		$real_path = rtrim(rtrim($path.$dir, '\\'), '/').'/';
		$files = scandir($real_path);
		if (!$files)
			return array();

		$filtered_files = array();

		$real_ext = false;
		if (!empty($ext))
			$real_ext = '.'.$ext;
		$real_ext_length = strlen($real_ext);

		$subdir = ($dir) ? $dir.'/' : '';
		foreach ($files as $file)
		{
			if (!$real_ext || (strpos($file, $real_ext) && strpos($file, $real_ext) == (strlen($file) - $real_ext_length)))
				$filtered_files[] = $subdir.$file;

			if ($recursive && $file[0] != '.' && is_dir($real_path.$file))
				foreach (Tools::scandir($path, $ext, $subdir.$file, $recursive) as $subfile)
					$filtered_files[] = $subfile;
		}
		return $filtered_files;
	}


	/**
	 * Align version sent and use internal function
	 *
	 * @static
	 * @param $v1
	 * @param $v2
	 * @param string $operator
	 * @return mixed
	 */
	public static function version_compare($v1, $v2, $operator = '<')
	{
		Tools::alignVersionNumber($v1, $v2);
		return version_compare($v1, $v2, $operator);
	}

	/**
	 * Align 2 version with the same number of sub version
	 * version_compare will work better for its comparison :)
	 * (Means: '1.8' to '1.9.3' will change '1.8' to '1.8.0')
	 * @static
	 * @param $v1
	 * @param $v2
	 */
	public static function alignVersionNumber(&$v1, &$v2)
	{
		$len1 = count(explode('.', trim($v1, '.')));
		$len2 = count(explode('.', trim($v2, '.')));
		$len = 0;
		$str = '';

		if ($len1 > $len2)
		{
			$len = $len1 - $len2;
			$str = &$v2;
		}
		else if ($len2 > $len1)
		{
			$len = $len2 - $len1;
			$str = &$v1;
		}

		for ($len; $len > 0; $len--)
			$str .= '.0';
	}

	public static function modRewriteActive()
	{
		if (Tools::apacheModExists('mod_rewrite'))
			return true;
		if ((isset($_SERVER['HTTP_MOD_REWRITE']) && Tools::strtolower($_SERVER['HTTP_MOD_REWRITE']) == 'on') || Tools::strtolower(getenv('HTTP_MOD_REWRITE')) == 'on')
				return true;
		return false;
	}

	public static function unSerialize($serialized, $object = false)
	{
		if (is_string($serialized) && (strpos($serialized, 'O:') === false || !preg_match('/(^|;|{|})O:[0-9]+:"/', $serialized)) && !$object || $object)
			return @unserialize($serialized);

		return false;
	}
	
	/**
	 * Reproduce array_unique working before php version 5.2.9 
	 * @param array $array
	 * @return array
	 */
	public static function arrayUnique($array)
	{
		if (version_compare(phpversion(), '5.2.9', '<'))
			return array_unique($array);
		else
			return array_unique($array, SORT_REGULAR);
	}

	/**
	 * Delete unicode class from regular expression patterns
	 * @param string $pattern
	 * @return pattern
	 */
	public static function cleanNonUnicodeSupport($pattern)
	{
		if (!defined('PREG_BAD_UTF8_OFFSET'))
			return $pattern;
		return preg_replace('/\\\[px]\{[a-z]\}{1,2}|(\/[a-z]*)u([a-z]*)$/i', "$1$2", $pattern);
	}

	protected static $is_addons_up = true;
	public static function addonsRequest($request, $params = array())
	{
		if (!self::$is_addons_up)
			return false;

		$postData = http_build_query(array(
			'version' => isset($params['version']) ? $params['version'] : _PS_VERSION_,
			'iso_lang' => Tools::strtolower(isset($params['iso_lang']) ? $params['iso_lang'] : Context::getContext()->language->iso_code),
			'iso_code' => Tools::strtolower(isset($params['iso_country']) ? $params['iso_country'] : Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'))),
			'shop_url' => isset($params['shop_url']) ? $params['shop_url'] : Tools::getShopDomain(),
			'mail' => isset($params['email']) ? $params['email'] : Configuration::get('PS_SHOP_EMAIL')
		));

		$protocols = array('https');
		switch ($request)
		{
			case 'native':
				$protocols[] = 'http';
				$postData .= '&method=listing&action=native';
				break;
			case 'native_all':
				$protocols[] = 'http';
				$postData .= '&method=listing&action=native&iso_code=all';
				break;
			case 'must-have':
				$protocols[] = 'http';
				$postData .= '&method=listing&action=must-have';
				break;
			case 'must-have-themes':
				$protocols[] = 'http';
				$postData .= '&method=listing&action=must-have-themes';
				break;
			case 'customer':
				$postData .= '&method=listing&action=customer&username='.urlencode(trim(Context::getContext()->cookie->username_addons)).'&password='.urlencode(trim(Context::getContext()->cookie->password_addons));
				break;
			case 'customer_themes':
				$postData .= '&method=listing&action=customer-themes&username='.urlencode(trim(Context::getContext()->cookie->username_addons)).'&password='.urlencode(trim(Context::getContext()->cookie->password_addons));
				break;
			case 'check_customer':
				$postData .= '&method=check_customer&username='.urlencode($params['username_addons']).'&password='.urlencode($params['password_addons']);
				break;
			case 'check_module':
				$postData .= '&method=check&module_name='.urlencode($params['module_name']).'&module_key='.urlencode($params['module_key']);
				break;
			case 'module':
				$postData .= '&method=module&id_module='.urlencode($params['id_module']);
				if (isset($params['username_addons']) && isset($params['password_addons']))
					$postData .= '&username='.urlencode($params['username_addons']).'&password='.urlencode($params['password_addons']);
				else
					$protocols[] = 'http';
				break;
			case 'install-modules':
				$protocols[] = 'http';
				$postData .= '&method=listing&action=install-modules';
				break;
			default:
				return false;
		}

		$context = stream_context_create(array(
			'http' => array(
				'method'=> 'POST',
				'content' => $postData,
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'timeout' => 5,
			)
		));
		
		foreach ($protocols as $protocol)
			if ($content = Tools::file_get_contents($protocol.'://api.addons.prestashop.com', false, $context))
				return $content;

		self::$is_addons_up = false;
		return false;
	}

	public static function fileAttachment($input = 'fileUpload')
	{
		$fileAttachment = null;
		if (isset($_FILES[$input]['name']) && !empty($_FILES[$input]['name']) && !empty($_FILES[$input]['tmp_name']))
		{
			$fileAttachment['rename'] = uniqid(). Tools::strtolower(substr($_FILES[$input]['name'], -5));	
			$fileAttachment['content'] = file_get_contents($_FILES[$input]['tmp_name']);
			$fileAttachment['tmp_name'] = $_FILES[$input]['tmp_name'];
			$fileAttachment['name'] = $_FILES[$input]['name'];
			$fileAttachment['mime'] = $_FILES[$input]['type'];
			$fileAttachment['error'] = $_FILES[$input]['error'];
		}
		return $fileAttachment;
	}
	
	public static function changeFileMTime($file_name)
	{
		touch($file_name);
	}
	
	public static function waitUntilFileIsModified($file_name, $timeout = 180)
	{
		@ini_set('max_execution_time', $timeout);
		if (($time_limit = ini_get('max_execution_time')) === null)
			$time_limit = 30;

		$time_limit -= 5;
		$start_time = microtime(true);
		$last_modified = @filemtime($file_name);

		while(true)
		{
			if (((microtime(true) - $start_time) > $time_limit) || @filemtime($file_name) > $last_modified)
				break;
			clearstatcache();
			usleep(300);
		}
	}
	
	/**
	 * Delete a substring from another one starting from the right
	 * @param string $str
	 * @param string $str_search
	 * @return string
	 */
	public static function rtrimString($str, $str_search)
	{
		$length_str = strlen($str_search);
		if (strlen($str) >= $length_str && substr($str, -$length_str) == $str_search)
			$str = substr($str, 0, -$length_str);
		return $str;
	}

	/**
	 * Format a number into a human readable format
	 * e.g. 24962496 => 23.81M
	 * @param     $size
	 * @param int $precision
	 *
	 * @return string
	 */
	public static function formatBytes($size, $precision = 2)
	{
		if (!$size)
			return '0';
		$base = log($size) / log(1024);
		$suffixes = array('', 'k', 'M', 'G', 'T');

		return round(pow(1024, $base - floor($base)), $precision).$suffixes[floor($base)];
	}

	public static function boolVal($value)
	{
		if (empty($value))
			$value = false;
		return (bool)$value;
	}

	public static function getUserPlatform()
	{
		if (isset(self::$_user_plateform))
			return self::$_user_plateform;

		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		self::$_user_plateform = 'unknown';

		if (preg_match('/linux/i', $user_agent))
			self::$_user_plateform = 'Linux';
		elseif (preg_match('/macintosh|mac os x/i', $user_agent))
			self::$_user_plateform = 'Mac';
		elseif (preg_match('/windows|win32/i', $user_agent))
			self::$_user_plateform = 'Windows';

		return self::$_user_plateform;
	}

	public static function getUserBrowser()
	{
		if (isset(self::$_user_browser))
			return self::$_user_browser;

		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		self::$_user_browser = 'unknown';

		if(preg_match('/MSIE/i',$user_agent) && !preg_match('/Opera/i',$user_agent))
			self::$_user_browser = 'Internet Explorer';
		elseif(preg_match('/Firefox/i',$user_agent))
			self::$_user_browser = 'Mozilla Firefox';
		elseif(preg_match('/Chrome/i',$user_agent))
			self::$_user_browser = 'Google Chrome';
		elseif(preg_match('/Safari/i',$user_agent))
			self::$_user_browser = 'Apple Safari';
		elseif(preg_match('/Opera/i',$user_agent))
			self::$_user_browser = 'Opera';
		elseif(preg_match('/Netscape/i',$user_agent))
			self::$_user_browser = 'Netscape';

		return self::$_user_browser;
	}

	/**
	  * Allows to display the category description without HTML tags and slashes
	  *
	  * @return string
	  */
	public static function getDescriptionClean($description)
	{
		return strip_tags(stripslashes($description));
	}

	public static function purifyHTML($html)
	{
		static $use_html_purifier = null;
		static $purifier = null;

		if (defined('PS_INSTALLATION_IN_PROGRESS') || !Configuration::configurationIsLoaded())
			return $html;

		if ($use_html_purifier === null)
			$use_html_purifier = (bool)Configuration::get('PS_USE_HTMLPURIFIER');

		if ($use_html_purifier)
		{
			if ($purifier === null)
			{
				$config = HTMLPurifier_Config::createDefault();
				$config->set('Attr.EnableID', true);
				$config->set('HTML.Trusted', true);
				$config->set('Cache.SerializerPath', _PS_CACHE_DIR_.'purifier');

				if (Configuration::get('PS_ALLOW_HTML_IFRAME'))
				{
					$config->set('HTML.SafeIframe', true);
					$config->set('HTML.SafeObject', true);
					$config->set('URI.SafeIframeRegexp','/.*/');
				}
				$purifier = new HTMLPurifier($config);
			}
			if (_PS_MAGIC_QUOTES_GPC_)
				$html = stripslashes($html);

			$html = $purifier->purify($html);

			if (_PS_MAGIC_QUOTES_GPC_)
				$html = addslashes($html);
		}

		return $html;
	}
}

/**
 * Compare 2 prices to sort products
 *
 * @param float $a
 * @param float $b
 * @return integer
 */
/* Externalized because of a bug in PHP 5.1.6 when inside an object */
function cmpPriceAsc($a, $b)
{
	if ((float)$a['price_tmp'] < (float)$b['price_tmp'])
		return (-1);
	elseif ((float)$a['price_tmp'] > (float)$b['price_tmp'])
		return (1);
	return 0;
}

function cmpPriceDesc($a, $b)
{
	if ((float)$a['price_tmp'] < (float)$b['price_tmp'])
		return 1;
	elseif ((float)$a['price_tmp'] > (float)$b['price_tmp'])
		return -1;
	return 0;
}
