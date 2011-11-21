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
*  @version  Release: $Revision: 7040 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ValidateCore
{
	public static function isIp2Long($ip)
	{
		return preg_match('#^-?[0-9]+$#', (string)$ip);
	}

	public static function isAnything($data)
	{
		return true;
	}

 	/**
	* Check for e-mail validity
	*
	* @param string $email e-mail address to validate
	* @return boolean Validity is ok or not
	*/
	public static function isEmail($email, $required = true)
    {
    	return !empty($email) AND preg_match('/^[a-z0-9!#$%&\'*+\/=?^`{}|~_-]+[.a-z0-9!#$%&\'*+\/=?^`{}|~_-]*@[a-z0-9]+[._a-z0-9-]*\.[a-z0-9]+$/ui', $email);
    }

    /**
	* Check for module URL validity
	*
	* @param string $url module URL to validate
	* @param array $errors Reference array for catching errors
	* @return boolean Validity is ok or not
	*/
	public static function isModuleUrl($url, &$errors)
	{
		if (!$url OR $url == 'http://')
			$errors[] = Tools::displayError('Please specify module URL');
		elseif (substr($url, -4) != '.tar' AND substr($url, -4) != '.zip' AND substr($url, -4) != '.tgz' AND substr($url, -7) != '.tar.gz')
			$errors[] = Tools::displayError('Unknown archive type');
		else
		{
			if ((strpos($url, 'http')) === false)
				$url = 'http://'.$url;
			if (!is_array(@get_headers($url)))
				$errors[] = Tools::displayError('Invalid URL');
		}
		if (!sizeof($errors))
			return true;
		return false;

	}

	/**
	* Check for MD5 string validity
	*
	* @param string $md5 MD5 string to validate
	* @return boolean Validity is ok or not
	*/
	public static function isMd5($md5)
	{
		return preg_match('/^[a-f0-9A-F]{32}$/', $md5);
	}

	/**
	* Check for SHA1 string validity
	*
	* @param string $sha1 SHA1 string to validate
	* @return boolean Validity is ok or not
	*/
	public static function isSha1($sha1)
	{
		return preg_match('/^[a-fA-F0-9]{40}$/', $sha1);
	}

	/**
	* Check for a float number validity
	*
	* @param float $float Float number to validate
	* @return boolean Validity is ok or not
	*/
    public static function isFloat($float)
    {
		return strval((float)($float)) == strval($float);
	}

    public static function isUnsignedFloat($float)
    {
			return strval((float)($float)) == strval($float) AND $float >= 0;
	}

	/**
	* Check for a float number validity
	*
	* @param float $float Float number to validate
	* @return boolean Validity is ok or not
	*/
    public static function isOptFloat($float)
    {
		return empty($float) OR self::isFloat($float);
	}

	/**
	* Check for a carrier name validity
	*
	* @param string $name Carrier name to validate
	* @return boolean Validity is ok or not
	*/
	public static function isCarrierName($name)
	{
		return empty($name) OR preg_match('/^[^<>;=#{}]*$/u', $name);
	}

	/**
	* Check for an image size validity
	*
	* @param string $size Image size to validate
	* @return boolean Validity is ok or not
	*/
	public static function isImageSize($size)
	{
		return preg_match('/^[0-9]{1,4}$/', $size);
	}

	/**
	* Check for name validity
	*
	* @param string $name Name to validate
	* @return boolean Validity is ok or not
	*/
	public static function isName($name)
	{
		return preg_match('/^[^0-9!<>,;?=+()@#"°{}_$%:]*$/u', stripslashes($name));
	}

	/**
	* Check for hook name validity
	*
	* @param string $hook Hook name to validate
	* @return boolean Validity is ok or not
	*/
	public static function isHookName($hook)
	{
		return preg_match('/^[a-zA-Z0-9_-]+$/', $hook);
	}

	/**
	* Check for sender name validity
	*
	* @param string $mailName Sender name to validate
	* @return boolean Validity is ok or not
	*/
	public static function isMailName($mailName)
	{
		return preg_match('/^[^<>;=#{}]*$/u', $mailName);
	}

	/**
	* Check for e-mail subject validity
	*
	* @param string $mailSubject e-mail subject to validate
	* @return boolean Validity is ok or not
	*/
	public static function isMailSubject($mailSubject)
	{
		return preg_match('/^[^<>]*$/u', $mailSubject);
	}

	/**
	* Check for module name validity
	*
	* @param string $moduleName Module name to validate
	* @return boolean Validity is ok or not
	*/
	public static function isModuleName($moduleName)
	{
		return preg_match('/^[a-zA-Z0-9_-]+$/', $moduleName);
	}

	/**
	* Check for template name validity
	*
	* @param string $tplName Template name to validate
	* @return boolean Validity is ok or not
	*/
	public static function isTplName($tplName)
	{
		return preg_match('/^[a-zA-Z0-9_-]+$/', $tplName);
	}

	/**
	* Check for image type name validity
	*
	* @param string $type Image type name to validate
	* @return boolean Validity is ok or not
	*/
	public static function isImageTypeName($type)
	{
		return preg_match('/^[a-zA-Z0-9_ -]+$/', $type);
	}

	/**
	* Check for price validity
	*
	* @param string $price Price to validate
	* @return boolean Validity is ok or not
	*/
	public static function isPrice($price)
	{
		return preg_match('/^[0-9]{1,10}(\.[0-9]{1,9})?$/', $price);
	}

	/**
	* Check for language code (ISO) validity
	*
	* @param string $isoCode Language code (ISO) to validate
	* @return boolean Validity is ok or not
	*/
	public static function isLanguageIsoCode($isoCode)
	{
		return preg_match('/^[a-zA-Z]{2,3}$/', $isoCode);
	}

	public static function isLanguageCode($s)
	{
		return preg_match('/^[a-zA-Z]{2}(-[a-zA-Z]{2})?$/', $s);
	}

	public static function isStateIsoCode($isoCode)
	{
		return preg_match('/^[a-zA-Z0-9]{1,3}((-)[a-zA-Z0-9]{1,3})?$/', $isoCode);
	}

	public static function isNumericIsoCode($isoCode)
	{
		return preg_match('/^[0-9]{2,3}$/', $isoCode);
	}

	/**
	* Check for voucher name validity
	*
	* @param string $voucher voucher to validate
	* @return boolean Validity is ok or not
	*/
	public static function isDiscountName($voucher)
	{
		return preg_match('/^[^!<>,;?=+()@"°{}_$%:]{3,32}$/u', $voucher);
	}

	/**
	* Check for product or category name validity
	*
	* @param string $name Product or category name to validate
	* @return boolean Validity is ok or not
	*/
	public static function isCatalogName($name)
	{
		return preg_match('/^[^<>;=#{}]*$/u', $name);
	}

	/**
	* Check for a message validity
	*
	* @param string $message Message to validate
	* @return boolean Validity is ok or not
	*/
	public static function isMessage($message)
	{
		return !preg_match('/[<>{}]/i', $message);
	}

	/**
	* Check for a country name validity
	*
	* @param string $name Country name to validate
	* @return boolean Validity is ok or not
	*/
	public static function isCountryName($name)
	{
		return preg_match('/^[a-zA-Z -]+$/', $name);
	}

	/**
	* Check for a link (url-rewriting only) validity
	*
	* @param string $link Link to validate
	* @return boolean Validity is ok or not
	*/
	public static function isLinkRewrite($link)
	{
		return (boolean)preg_match('/^[_a-zA-Z0-9-]+$/', $link);
	}

	/**
	* Check for a postal address validity
	*
	* @param string $address Address to validate
	* @return boolean Validity is ok or not
	*/
	public static function isAddress($address)
	{
		return empty($address) OR preg_match('/^[^!<>?=+@{}_$%]*$/u', $address);
	}

	/**
	* Check for city name validity
	*
	* @param string $city City name to validate
	* @return boolean Validity is ok or not
	*/
	public static function isCityName($city)
	{
		return preg_match('/^[^!<>;?=+@#"°{}_$%]*$/u', $city);
	}

	/**
	* Check for search query validity
	*
	* @param string $search Query to validate
	* @return boolean Validity is ok or not
	*/
	public static function isValidSearch($search)
	{
		return preg_match('/^[^<>;=#{}]{0,64}$/u', $search);
	}

	/**
	* Check for standard name validity
	*
	* @param string $name Name to validate
	* @return boolean Validity is ok or not
	*/
	public static function isGenericName($name)
	{
		return empty($name) OR preg_match('/^[^<>;=#{}]*$/u', $name);
	}

	/**
	* Check for HTML field validity (no XSS please !)
	*
	* @param string $html HTML field to validate
	* @return boolean Validity is ok or not
	*/
	public static function isCleanHtml($html)
	{
		$jsEvent = 'onmousedown|onmousemove|onmmouseup|onmouseover|onmouseout|onload|onunload|onfocus|onblur|onchange|onsubmit|ondblclick|onclick|onkeydown|onkeyup|onkeypress|onmouseenter|onmouseleave|onerror';
		return (!preg_match('/<[ \t\n]*script/i', $html) && !preg_match('/<?.*('.$jsEvent.')[ \t\n]*=/i', $html)  && !preg_match('/.*script\:/i', $html));
	}

	/**
	* Check for product reference validity
	*
	* @param string $reference Product reference to validate
	* @return boolean Validity is ok or not
	*/
	public static function isReference($reference)
	{
		return preg_match('/^[^<>;={}]*$/u', $reference);
	}

	/**
	* Check for password validity
	*
	* @param string $passwd Password to validate
	* @return boolean Validity is ok or not
	*/
	public static function isPasswd($passwd, $size = 5)
	{
		return preg_match('/^[.a-zA-Z_0-9-!@#$%\^&*()]{'.(int)$size.',32}$/', $passwd);
	}

	public static function isPasswdAdmin($passwd)
	{
		return self::isPasswd($passwd, 8);
	}

	/**
	* Check for configuration key validity
	*
	* @param string $configName Configuration key to validate
	* @return boolean Validity is ok or not
	*/
	public static function isConfigName($configName)
	{
		return preg_match('/^[a-zA-Z_0-9-]+$/', $configName);
	}

	/**
	* Check date formats like http://php.net/manual/en/function.date.php
	*
	* @param string $date_format date format to check
	* @return boolean Validity is ok or not
	*/
	public static function isPhpDateFormat($date_format)
	{
		// We can't really check if this is valid or not, because this is a string and you can write whatever you want in it. That's why only < et > are forbidden (HTML)
		return preg_match('/^[^<>]+$/', $date_format);
	}

	/**
	* Check for date format
	*
	* @param string $date Date to validate
	* @return boolean Validity is ok or not
	*/
	public static function isDateFormat($date)
	{
		return (bool)preg_match('/^([0-9]{4})-((0?[0-9])|(1[0-2]))-((0?[0-9])|([1-2][0-9])|(3[01]))( [0-9]{2}:[0-9]{2}:[0-9]{2})?$/', $date);
	}

	/**
	* Check for date validity
	*
	* @param string $date Date to validate
	* @return boolean Validity is ok or not
	*/
	public static function isDate($date)
	{
		if (!preg_match('/^([0-9]{4})-((0?[0-9])|(1[0-2]))-((0?[0-9])|([1-2][0-9])|(3[01]))( [0-9]{2}:[0-9]{2}:[0-9]{2})?$/', $date, $matches))
			return false;
		return checkdate((int)$matches[2], (int)$matches[5], (int)$matches[0]);
	}

	/**
	* Check for birthDate validity
	*
	* @param string $date birthdate to validate
	* @return boolean Validity is ok or not
	*/
	public static function isBirthDate($date)
	{
	 	if (empty($date) || $date == '0000-00-00')
	 		return true;
	 	if (preg_match('/^([0-9]{4})-((0?[1-9])|(1[0-2]))-((0?[1-9])|([1-2][0-9])|(3[01]))( [0-9]{2}:[0-9]{2}:[0-9]{2})?$/', $date, $birthDate)) {
			 if ($birthDate[1] >= date('Y') - 9)
	 			return false;
	 		return true;
	 	}
		return false;
	}

	/**
	* Check for boolean validity
	*
	* @param boolean $bool Boolean to validate
	* @return boolean Validity is ok or not
	*/
	public static function isBool($bool)
	{
		return is_null($bool) OR is_bool($bool) OR preg_match('/^0|1$/', $bool);
	}

	/**
	* Check for phone number validity
	*
	* @param string $phoneNumber Phone number to validate
	* @return boolean Validity is ok or not
	*/
	public static function isPhoneNumber($phoneNumber)
	{
		return preg_match('/^[+0-9. ()-]*$/', $phoneNumber);
	}

	/**
	* Check for barcode validity (EAN-13)
	*
	* @param string $ean13 Barcode to validate
	* @return boolean Validity is ok or not
	*/
	public static function isEan13($ean13)
	{
		return !$ean13 OR preg_match('/^[0-9]{0,13}$/', $ean13);
	}

	/**
	* Check for barcode validity (UPC)
	*
	* @param string $upc Barcode to validate
	* @return boolean Validity is ok or not
	*/
	public static function isUpc($upc)
	{
		return !$upc OR preg_match('/^[0-9]{0,12}$/', $upc);
	}

	/**
	* Check for postal code validity
	*
	* @param string $postcode Postal code to validate
	* @return boolean Validity is ok or not
	*/
	public static function isPostCode($postcode)
	{
		return empty($postcode) OR preg_match('/^[a-zA-Z 0-9-]+$/', $postcode);
	}

	/**
	* Check for zip code format validity
	*
	* @param string $zip_code zip code format to validate
	* @return boolean Validity is ok or not
	*/
	public static function isZipCodeFormat($zip_code)
	{
		if (!empty($zip_code))
			return preg_match('/^[NLCnlc 0-9-]+$/', $zip_code);
		return true;
	}

	/**
	* Check for table or identifier validity
	* Mostly used in database for ordering : ASC / DESC
	*
	* @param string $orderWay Keyword to validate
	* @return boolean Validity is ok or not
	*/
	public static function isOrderWay($orderWay)
	{
		return ($orderWay === 'ASC' | $orderWay === 'DESC' | $orderWay === 'asc' | $orderWay === 'desc');
	}

	/**
	* Check for table or identifier validity
	* Mostly used in database for ordering : ORDER BY field
	*
	* @param string $orderBy Field to validate
	* @return boolean Validity is ok or not
	*/
	public static function isOrderBy($orderBy)
	{
		return preg_match('/^[a-zA-Z0-9_-]+$/', $orderBy);
	}

	/**
	* Check for table or identifier validity
	* Mostly used in database for table names and id_table
	*
	* @param string $table Table/identifier to validate
	* @return boolean Validity is ok or not
	*/
	public static function isTableOrIdentifier($table)
	{
		return preg_match('/^[a-zA-Z0-9_-]+$/', $table);
	}

	/**
	* Check for values list validity
	* Mostly used in database for insertions (A,B,C),(A,B,C)...
	*
	* @param string $list List to validate
	* @return boolean Validity is ok or not
	*/
	public static function isValuesList($list)
	{
		return true;
		/* For history reason, we keep this line */
		// return preg_match('/^[0-9,\'(). NULL]+$/', $list);
	}

	/**
	* Check for tags list validity
	*
	* @param string $list List to validate
	* @return boolean Validity is ok or not
	*/
	public static function isTagsList($list)
	{
		return preg_match('/^[^!<>;?=+#"°{}_$%]*$/u', $list);
	}

	/**
	* Check for an integer validity
	*
	* @param integer $id Integer to validate
	* @return boolean Validity is ok or not
	*/
	public static function isInt($value)
	{
		return ((string)(int)$value === (string)$value OR $value === false);
	}

	/**
	* Check for an integer validity (unsigned)
	*
	* @param integer $id Integer to validate
	* @return boolean Validity is ok or not
	*/
	public static function isUnsignedInt($value)
	{
		return (preg_match('#^[0-9]+$#', (string)$value) AND $value < 4294967296 AND $value >= 0);
	}

	/**
	* Check for an integer validity (unsigned)
	* Mostly used in database for auto-increment
	*
	* @param integer $id Integer to validate
	* @return boolean Validity is ok or not
	*/
	public static function isUnsignedId($id)
	{
		return self::isUnsignedInt($id); /* Because an id could be equal to zero when there is no association */
	}

	public static function isNullOrUnsignedId($id)
	{
		return is_null($id) OR self::isUnsignedId($id);
	}

	/**
	* Check object validity
	*
	* @param integer $object Object to validate
	* @return boolean Validity is ok or not
	*/
	public static function isLoadedObject($object)
	{
		return is_object($object) AND $object->id;
	}

	/**
	* Check object validity
	*
	* @param integer $object Object to validate
	* @return boolean Validity is ok or not
	*/
	public static function isColor($color)
	{
		return preg_match('/^(#[0-9a-fA-F]{6}|[a-zA-Z0-9-]*)$/', $color);
	}

	/**
	* Check url valdity (disallowed empty string)
	*
	* @param string $url Url to validate
	* @return boolean Validity is ok or not
	*/
	public static function isUrl($url)
	{
		return preg_match('/^[~:#,%&_=\(\)\.\? \+\-@\/a-zA-Z0-9]+$/', $url);
	}

	/**
	* Check url validity (allowed empty string)
	*
	* @param string $url Url to validate
	* @return boolean Validity is ok or not
	*/
	public static function isUrlOrEmpty($url)
	{
		return empty($url) || self::isUrl($url);
	}

	/**
	* Check object validity
	*
	* @param integer $object Object to validate
	* @return boolean Validity is ok or not
	*/
	public static function isAbsoluteUrl($url)
	{
		if (!empty($url))
			return preg_match('/^https?:\/\/[,:#%&_=\(\)\.\? \+\-@\/a-zA-Z0-9]+$/', $url);
		return true;
	}

	public static function isMySQLEngine($engine)
	{
		return (in_array($engine, array('InnoDB', 'MyISAM')));
	}

	public static function isUnixName($data)
	{
		return preg_match('/^[a-z0-9\._-]+$/ui', $data);
	}

	public static function isTablePrefix($data)
	{
		// Even if "-" is theorically allowed, it will be considered a syntax error if you do not add backquotes (`) around the table name
		return preg_match('/^[a-z0-9_]+$/ui', $data);
	}

	/**
	* Check for standard name file validity
	*
	* @param string $name Name to validate
	* @return boolean Validity is ok or not
	*/
	public static function isFileName($name)
	{
		return preg_match('/^[a-zA-Z0-9_.-]*$/', $name);
	}

	/**
	* Check for admin panel tab name validity
	*
	* @param string $name Name to validate
	* @return boolean Validity is ok or not
	*/
	public static function isTabName($name)
	{
		return preg_match('/^[a-zA-Z0-9_-]*$/', $name);
	}

	public static function isWeightUnit($unit)
	{
		return preg_match('/^[a-zA-Z]{1,3}$/', $unit);
	}

	public static function isDistanceUnit($unit)
	{
		return preg_match('/^[a-zA-Z]{1,2}$/', $unit);
	}

	public static function isSubDomainName($subDomainName)
	{
		return preg_match('/^[a-zA-Z0-9-_]*$/', $subDomainName);
	}

	public static function isVoucherDescription($text)
	{
		return preg_match('/^([^<>{}]|<br \/>)*$/i', $text);
	}

	/**
	* Check if the value is a sort direction value (DESC/ASC)
	*
	* @param char $value
	* @return boolean Validity is ok or not
	*/
	public static function IsSortDirection($value)
	{
		return (!is_null($value) AND ($value === 'ASC' OR $value === 'DESC'));
	}

	/**
	* Customization fields' label validity
	*
	* @param integer $object Object to validate
	* @return boolean Validity is ok or not
	*/
	public static function isLabel($label)
	{
		return (preg_match('/^[^{}<>]*$/u', $label));
	}

	/**
	* Price display method validity
	*
	* @param integer $data Data to validate
	* @return boolean Validity is ok or not
	*/
	public static function isPriceDisplayMethod($data)
	{
		return ($data == PS_TAX_EXC OR $data == PS_TAX_INC);
	}

	/**
	 * @param string $dni to validate
	 * @return bool
	 */
	public static function isDniLite($dni)
	{
		return empty($dni) OR (bool)preg_match('/^[0-9A-Za-z-.]{1,16}$/U', $dni);
	}

	/**
	 * Check if $data is a PrestaShop cookie object
	 *
	 * @param mixed $data to validate
	 * @return bool
	 */
	public static function isCookie($data)
	{
		return (is_object($data) AND get_class($data) == 'Cookie');
	}

	/**
	* Price display method validity
	*
	* @param string $data Data to validate
	* @return boolean Validity is ok or not
	*/
	public static function isString($data)
	{
		return is_string($data);
	}

	/**
	* Check if the data is a reduction type (amout or percentage)
	*
	* @param string $data Data to validate
	* @return boolean Validity is ok or not
	*/
	public static function isReductionType($data)
	{
		return ($data === 'amount' || $data === 'percentage');
	}

	/**
	* Check for bool_id
	*
	* @param string $ids
	* @return boolean Validity is ok or not
	*/
	public static function isBool_Id($ids)
	{
		return (bool)preg_match('#^[01]_[0-9]+$#', $ids);
	}

	/**
	* Check the localization pack part selected
	*
	* @param string $data Localization pack to check
	* @return boolean Validity is ok or not
	*/
	public static function isLocalizationPackSelection($data)
	{
		return ($data === 'states' OR $data === 'taxes' OR $data === 'currencies' OR $data === 'languages' OR $data === 'units');
	}

	/**
	* Check for PHP serialized data
	*
	* @param string $data Serialized data to validate
	* @return boolean Validity is ok or not
	*/
	public static function isSerializedArray($data)
	{
		return ($data == NULL) OR (bool)(is_string($data) AND preg_match('/^a:[0-9]+:{.*;}$/s', $data));
	}

	/**
	* Check for Latitude/Longitude
	*
	* @param string $data Coordinate to validate
	* @return boolean Validity is ok or not
	*/
	public static function isCoordinate($data)
	{
		return ($data == NULL) OR (bool)(preg_match('/^\-?[0-9]{1,8}\.[0-9]{1,8}$/s', $data));
	}

	/**
	* Check for Language Iso Code
	*
	* @param string $iso_code
	* @return boolean Validity is ok or not
	*/
	public static function isLangIsoCode($iso_code)
	{
		return (bool)(preg_match('/^[a-zA-Z]{2,3}$/s', $iso_code));
	}

	/**
	* Check for Language File Name
	*
	* @param string $file_name
	* @return boolean Validity is ok or not
	*/
	public static function isLanguageFileName($file_name)
	{
		return (bool)(preg_match('/^[a-zA-Z]{2,3}\.gzip$/s', $file_name));
	}

	/**
	 *
	 * @param array $ids
	 * @return boolean return true if the array contain only unsigned int value
	 */
	public static function isArrayWithIds($ids)
	{
		if (sizeof($ids))
			foreach($ids as $id)
				if ($id == 0 || !self::isUnsignedInt($id))
					return false;
		return true;
	}

	/**
	 *
	 * @param array $zones
	 * @return boolean return true if array contain all value required for an image map zone
	 */
	public static function isSceneZones($zones)
	{
		foreach ($zones as $zone)
		{
			if (!isset($zone['x1']) || !self::isUnsignedInt($zone['x1']))
				return false;
			if (!isset($zone['y1']) || !self::isUnsignedInt($zone['y1']))
				return false;
			if (!isset($zone['width']) || !self::isUnsignedInt($zone['width']))
				return false;
			if (!isset($zone['height']) || !self::isUnsignedInt($zone['height']))
				return false;
			if (!isset($zone['id_product']) || !self::isUnsignedInt($zone['id_product']))
				return false;
		}
		return true;
	}

	/**
	 *
	 * @param array $stock_management
	 * @return boolean return true if is a valide stock management
	 */
	public static function isStockManagement($stock_management)
	{
		if (!in_array($stock_management, array('WA', 'FIFO', 'LIFO')))
			return false;
		return true;
	}
}

