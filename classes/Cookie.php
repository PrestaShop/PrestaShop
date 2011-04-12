<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class	CookieCore
{
	/** @var array Contain cookie content in a key => value format */
	protected $_content;

	/** @var array Crypted cookie name for setcookie() */
	protected $_name;

	/** @var array expiration date for setcookie() */
	protected $_expire;

	/** @var array Website domain for setcookie() */
	protected $_domain;

	/** @var array Path for setcookie() */
	protected $_path;

	/** @var array cipher tool instance */
	protected $_cipherTool;

	/** @var array cipher tool initialization key */
	protected $_key;

	/** @var array cipher tool initilization vector */
	protected $_iv;
	
	protected $_modified = false;

	/**
	  * Get data if the cookie exists and else initialize an new one
	  *
	  * @param $name Cookie name before encrypting
	  * @param $path
	  */
	public function __construct($name, $path = '', $expire = NULL)
	{
		$this->_content = array();
		$this->_expire = isset($expire) ? (int)($expire) : (time() + 1728000);
		$this->_name = md5($name.Tools::getHttpHost());
		$this->_path = trim(__PS_BASE_URI__.$path, '/\\').'/';
		if ($this->_path{0} != '/') $this->_path = '/'.$this->_path;
		$this->_path = rawurlencode($this->_path);
		$this->_path = str_replace('%2F', '/', $this->_path);
		$this->_path = str_replace('%7E', '~', $this->_path);
		$this->_key = _COOKIE_KEY_;
		$this->_iv = _COOKIE_IV_;
		$this->_domain = $this->getDomain();
		if (Configuration::get('PS_CIPHER_ALGORITHM'))
			$this->_cipherTool = new Rijndael(_RIJNDAEL_KEY_, _RIJNDAEL_IV_);
		else
			$this->_cipherTool = new Blowfish($this->_key, $this->_iv);
		$this->update();
	}
	
	protected function getDomain()
	{
		$r = '!(?:(\w+)://)?(?:(\w+)\:(\w+)@)?([^/:]+)?(?:\:(\d*))?([^#?]+)?(?:\?([^#]+))?(?:#(.+$))?!i';
	    preg_match ($r, Tools::getHttpHost(false, false), $out);
		if (preg_match('/^(((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]{1}[0-9]|[1-9]).)'. 
         '{1}((25[0-5]|2[0-4][0-9]|[1]{1}[0-9]{2}|[1-9]{1}[0-9]|[0-9]).)'. 
         '{2}((25[0-5]|2[0-4][0-9]|[1]{1}[0-9]{2}|[1-9]{1}[0-9]|[0-9]){1}))$/', $out[4]))
			return false;
		if (!strstr(Tools::getHttpHost(false, false), '.'))
			return false;
		$domain = $out[4];
		$subDomains = SubDomain::getSubDomains();
		if ($subDomains === false)
			die(Tools::displayError('Bad SubDomain SQL query.'));
		foreach ($subDomains AS $subDomain)
		{
			$subDomainLength = strlen($subDomain) + 1;
			if (strncmp($subDomain.'.', $domain, $subDomainLength) == 0)
				$domain = substr($domain, $subDomainLength);
		}
		return $domain;
	}

	/**
	  * Set expiration date
	  *
	  * @param integer $expire Expiration time from now
	  */
	function setExpire($expire)
	{
		$this->_expire = (int)($expire);
	}

	/**
	  * Magic method wich return cookie data from _content array
	  *
	  * @param $key key wanted
	  * @return string value corresponding to the key
	  */
	public function __get($key)
	{
		return isset($this->_content[$key]) ? $this->_content[$key] : false;
	}

	/**
	  * Magic method which check if key exists in the cookie
	  *
	  * @param $key key wanted
	  * @return boolean key existence
	  */
	public function __isset($key)
	{
		return isset($this->_content[$key]);
	}

	/**
	  * Magic method wich add data into _content array
	  *
	  * @param $key key desired
	  * @param $value value corresponding to the key
	  */
	public function __set($key, $value)
	{
		if (is_array($value))
			die(Tools::displayError());
		if (preg_match('/¤|\|/', $key.$value))
			throw new Exception('Forbidden chars in cookie');
		if (!$this->_modified AND (!isset($this->_content[$key]) OR (isset($this->_content[$key]) AND $this->_content[$key] != $value)))
			$this->_modified = true;
		$this->_content[$key] = $value;
		$this->write();
	}

	/**
	  * Magic method wich delete data into _content array
	  *
	  * @param $key key wanted
	  */
	public function __unset($key)
	{
		if (isset($this->_content[$key]))
			$this->_modified = true;
		unset($this->_content[$key]);
		$this->write();
	}

	/**
	  * Check customer informations saved into cookie and return customer validity
	  *
	  * @return boolean customer validity
	  */
	public function isLogged($withGuest = false)
	{
		if (!$withGuest AND $this->is_guest == 1)
			return false;
		
		/* Customer is valid only if it can be load and if cookie password is the same as database one */
	 	if ($this->logged == 1 AND $this->id_customer AND Validate::isUnsignedId($this->id_customer) AND Customer::checkPassword((int)($this->id_customer), $this->passwd))
        	return true;
        return false;
	}

	/**
	  * Check employee informations saved into cookie and return employee validity
	  *
	  * @return boolean employee validity
	  */
	public function isLoggedBack()
	{
		/* Employee is valid only if it can be load and if cookie password is the same as database one */
	 	return ($this->id_employee
			AND Validate::isUnsignedId($this->id_employee)
			AND Employee::checkPassword((int)$this->id_employee, $this->passwd)
			AND (!isset($this->_content['remote_addr']) OR $this->_content['remote_addr'] == ip2long(Tools::getRemoteAddr()) OR !Configuration::get('PS_COOKIE_CHECKIP'))
		);
	}

	/**
	  * Delete cookie
	  */
	public function logout()
	{
		$this->_content = array();
		$this->_setcookie();
		unset($_COOKIE[$this->_name]);
		$this->_modified = true;
		$this->write();
	}

	/**
	  * Soft logout, delete everything links to the customer
	  * but leave there affiliate's informations
	  */
	public function mylogout()
	{
		unset($this->_content['id_customer']);
		unset($this->_content['id_guest']);
		unset($this->_content['is_guest']);
		unset($this->_content['id_connections']);
		unset($this->_content['customer_lastname']);
		unset($this->_content['customer_firstname']);
		unset($this->_content['passwd']);
		unset($this->_content['logged']);
		unset($this->_content['email']);
		unset($this->_content['id_cart']);
		unset($this->_content['id_address_invoice']);
		unset($this->_content['id_address_delivery']);
		$this->_modified = true;
		$this->write();
	}
	
	function makeNewLog()
	{
		unset($this->_content['id_customer']);
		unset($this->_content['id_guest']);
		Guest::setNewGuest($this);
		$this->_modified = true;
	}

	/**
	  * Get cookie content
	  */
	function update($nullValues = false)
	{
		
		if (isset($_COOKIE[$this->_name]))
		{
			/* Decrypt cookie content */
			$content = $this->_cipherTool->decrypt($_COOKIE[$this->_name]);

			/* Get cookie checksum */
			$checksum = crc32($this->_iv.substr($content, 0, strrpos($content, '¤') + 2));

			/* Unserialize cookie content */
			$tmpTab = explode('¤', $content);
			foreach ($tmpTab AS $keyAndValue)
			{
				$tmpTab2 = explode('|', $keyAndValue);
				if (sizeof($tmpTab2) == 2)
					 $this->_content[$tmpTab2[0]] = $tmpTab2[1];
			 }
			/* Blowfish fix */
			if (isset($this->_content['checksum']))
				$this->_content['checksum'] = (int)($this->_content['checksum']);

			/* Check if cookie has not been modified */
			if (!isset($this->_content['checksum']) OR $this->_content['checksum'] != $checksum)
				$this->logout();
			
			if (!isset($this->_content['date_add']))
				$this->_content['date_add'] = date('Y-m-d H:i:s');
		}
		else
			$this->_content['date_add'] = date('Y-m-d H:i:s');
		
		//checks if the language exists, if not choose the default language
		if (!Language::getLanguage((int)$this->id_lang))
			$this->id_lang = Configuration::get('PS_LANG_DEFAULT');
		
	}

	/**
	  * Setcookie according to php version
	  */
	protected function _setcookie($cookie = NULL)
	{
		if ($cookie)
		{
			$content = $this->_cipherTool->encrypt($cookie);
			$time = $this->_expire;
		}
		else
		{
			$content = 0;
			$time = time() - 1;
		}
		if (PHP_VERSION_ID <= 50200) /* PHP version > 5.2.0 */
			return setcookie($this->_name, $content, $time, $this->_path, $this->_domain, 0);
		else
			return setcookie($this->_name, $content, $time, $this->_path, $this->_domain, 0, true);
	}

	/**
	  * Save cookie with setcookie()
	  */
	public function write()
	{
		$cookie = '';

		/* Serialize cookie content */
		if (isset($this->_content['checksum'])) unset($this->_content['checksum']);
		foreach ($this->_content AS $key => $value)
			$cookie .= $key.'|'.$value.'¤';

		/* Add checksum to cookie */
		$cookie .= 'checksum|'.crc32($this->_iv.$cookie);

		/* Cookies are encrypted for evident security reasons */
		return $this->_setcookie($cookie);
	}

	/**
	 * Get a family of variables (e.g. "filter_")
	 */
	public function getFamily($origin)
	{
		$result = array();
		if (count($this->_content) == 0)
			return $result;
		foreach ($this->_content AS $key => $value)
			if (strncmp($key, $origin, strlen($origin)) == 0)
				$result[$key] = $value;
		return $result;
	}

	/**
	 *
	 */
	public function unsetFamily($origin)
	{
		$family = $this->getFamily($origin);
		foreach ($family AS $member => $value)
			unset($this->$member);
	}

}
