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
*  @version  Release: $Revision: 7310 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AddressFormatCore extends ObjectModel
{
	/** @var integer */
	public $id_address_format;
	
	/** @var integer */
	public $id_country;

	/** @var string */
	public $format;

	private $_errorFormatList = array();

	protected	$fieldsRequired = array ('format');	
	protected	$fieldsValidate = array ('format' => 'isGenericName');

	/* MySQL does not allow 'order detail' for a table name */
	protected	$table = 'address_format';
	protected $identifier = 'id_country';
	
	static public $requireFormFieldsList = array(
		'firstname',
		'name',
		'address1',
		'city',
		'postcode',
		'Country:name',
		'State:name');
	
	static public $forbiddenProperyList = array(
		'deleted',
		'date_add',
		'other',
		'alias',
		'secure_key',
		'note',
		'newsletter',
		'ip_registration_newsletter',
		'newsletter_date_add',
		'optin',
		'passwd',
		'last_passwd_gen',
		'active',
		'is_guest',
		'date_upd',
		'years',
		'days',
		'months',
		'description',
		'meta_description',
		'short_description',
		'link_rewrite',
		'meta_title',
		'meta_keywords',
		'display_tax_label',
		'need_zip_code',
		'contains_states',
		'call_prefixes',
		'call_prefix');

	static public $forbiddenClassList = array(
		'Manufacturer',
		'Supplier');

	public function getFields()
	{
		parent::validateFields();

		$fields['id_country'] = (int)($this->id_country);
		$fields['format'] = pSQL($this->format);
		
		return $fields;
	}

	/*
	 * Check if the the association of the field name and a class name
	 * is valide
	 * @className is the name class
	 * @fieldName is a property name
	 * @isIdField boolean to know if we have to allowed a property name started by 'id_'
	 */
	private function _checkValidateClassField($className, $fieldName, $isIdField)
	{
		$isValide = false;

		if (!class_exists($className))
			$this->_errorFormatList[] = Tools::displayError('This class name doesn\'t exist').
			': '.$className;
		else
		{
			$obj = new $className();
			$reflect = new ReflectionObject($obj);
			
			// Check if the property is accessible
			$publicProperties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
			foreach($publicProperties as $property)
			{
				$propertyName = $property->getName();
				if (($propertyName == $fieldName) && ($isIdField ||
						(!preg_match('#id|id_\w#', $propertyName, $match))))
					$isValide = true;
			}
			
			if (!$isValide)
				$this->_errorFormatList[] = Tools::displayError('This property doesn\'t exist in the class or is forbidden').
				': '.$className.': '.$fieldName;
				
			unset($obj);
			unset($reflect);
		}
		return $isValide;
	}
	
	/*
	 * Verify the existence of a field name and check the availability
	 * of an association between a field name and a class (ClassName:fieldName)
	 * if the separator is overview
	 * @patternName is the composition of the class and field name
	 * @fieldsValidate contains the list of available field for the Address class
	 */
	private function _checkLiableAssociation($patternName, $fieldsValidate)
	{
		$patternName = trim($patternName);
		$cleanedLine = '';
		
		if ($associationName = explode(':', $patternName))
		{
			$totalNameUsed = count($associationName);
			if ($totalNameUsed > 2)
				$this->_errorFormatList[] = Tools::displayError('This assocation contains too much key name');
			else if ($totalNameUsed == 1)
			{
				$associationName[0] = strtolower($associationName[0]);
				$cleanedLine = $associationName[0];
				if (in_array($associationName[0], self::$forbiddenProperyList) || 
						!$this->_checkValidateClassField('Address', $associationName[0], false))
					$this->_errorFormatList[] = Tools::displayError('This name isn\'t allowed').': '.
						$associationName[0];
			}
			else if ($totalNameUsed == 2)
			{
				if (empty($associationName[0]) || empty($associationName[1]))
					$this->_errorFormatList[] = Tools::displayError('Syntax error with this pattern').': '.$patternName;
				else
				{
					$associationName[0] = ucfirst($associationName[0]);
					$associationName[1] = strtolower($associationName[1]);
					
					if (in_array($associationName[0], self::$forbiddenClassList))
						$this->_errorFormatList[] = Tools::displayError('This name isn\'t allowed').': '.
							$associationName[0];
					else
					{
					// Check if the id field name exist in the Address class 
					$this->_checkValidateClassField('Address', 'id_'.strtolower($associationName[0]), true);
					
					// Check if the field name exist in the class write by the user
					$this->_checkValidateClassField($associationName[0], $associationName[1], false);
					$cleanedLine = $associationName[0].':'.$associationName[1];
				}
			}
		}
		}
		return (strlen($cleanedLine)) ? $cleanedLine.' ' : '';
	}

	/*
	 * Check if the set fields are valide
	 */
	public function checkFormatFields()
	{
		$cleanedContent = '';
		$this->_errorFormatList = array();
		$fieldsValidate = Address::getFieldsValidate();

		$multipleLineFields = explode("\n", $this->format);
		if ($multipleLineFields && is_array($multipleLineFields))
			foreach($multipleLineFields as $lineField)
			{
				$lineField = str_replace(array("\n", "\t", "\r\n", "\r"), '', $lineField);
				if (strlen($lineField))
				{
					$patternsName = explode(' ', trim($lineField));
					if ($patternsName && is_array($patternsName))
					{
						foreach($patternsName as $patternName)
							$cleanedContent .= $this->_checkLiableAssociation($patternName, $fieldsValidate);
						$cleanedContent = trim($cleanedContent)."\r\n";
					}
				}
			}
		$this->format = $cleanedContent;
		return (count($this->_errorFormatList)) ? false : true;
	}
	
	/*
	 * Returns the error list
	 */
	public function getErrorList()
	{
		return $this->_errorFormatList;
	}

	/*
	 * Returns the formatted fields with associated values
	 * 
	 * @address is an instancied Address object
	 * @addressFormat is the format
	 * @return double Array
	 */
	public static function getFormattedAddressFieldsValues($address, $addressFormat)
	{
		global $cookie;
		
		$tab = array();
		$temporyObject = array();
		
		// Check if $address exist and it's an instanciate object of Address
		if ($address && ($address instanceof Address))
			foreach($addressFormat as $lineNum => $line)
			{
				if (($keyList = explode(' ', $line)) && is_array($keyList))
					foreach($keyList as $pattern)
						if ($associateName = explode(':', $pattern))
						{
							$totalName = count($associateName);
							if ($totalName == 1 && isset($address->{$associateName[0]}))
								$tab[$associateName[0]] = $address->{$associateName[0]};
							else 
							{
								$tab[$pattern] = '';
								
								// Check if the property exist in both classes
								if (($totalName == 2) && class_exists($associateName[0]) &&
									Tools::property_exists($associateName[0], $associateName[1]) &&
									Tools::property_exists($address, 'id_'.strtolower($associateName[0])))
								{
									$idFieldName = 'id_'.strtolower($associateName[0]);

									if (!isset($temporyObject[$associateName[0]]))
										$temporyObject[$associateName[0]] = new $associateName[0]($address->{$idFieldName});
									if ($temporyObject[$associateName[0]])
										$tab[$pattern] = (is_array($temporyObject[$associateName[0]]->{$associateName[1]})) ?
											((isset($temporyObject[$associateName[0]]->{$associateName[1]}[(isset($cookie) ? $cookie->id_lang : Configuration::get('PS_LANG_DEFAULT'))])) ? 
											$temporyObject[$associateName[0]]->{$associateName[1]}[(isset($cookie) ? $cookie->id_lang : Configuration::get('PS_LANG_DEFAULT'))] : '') :
											$temporyObject[$associateName[0]]->{$associateName[1]};
								}
							}
					}
			}
		// Free the instanciate objects
		foreach($temporyObject as $objectName => &$object)
			unset($object);
		return $tab;
	}
	
	/*
	 * Generates the full address text
	 * @address is an instanciate object of Address class
	 * @patternrules is a defined rules array to avoid some pattern
	 * @newLine is a string containing the newLine format
	 * @separator is a string containing the separator format
	 */
	public static function generateAddress(Address $address, $patternRules, $newLine = "\r\n", $separator = ' ', $style = array())
	{
		$addressFields = AddressFormat::getOrderedAddressFields($address->id_country);
		$addressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($address, $addressFields);
		
		$addressText = '';
		foreach ($addressFields as $line)
			if (($patternsList = explode(' ', $line)))
				{
					$tmpText = '';
					foreach($patternsList as $pattern)
						if (!in_array($pattern, $patternRules['avoid']))
							$tmpText .= (isset($addressFormatedValues[$pattern])) ?
								(((isset($style[$pattern])) ? 
									(sprintf($style[$pattern], $addressFormatedValues[$pattern])) : 
									$addressFormatedValues[$pattern]).$separator) : '';
					$tmpText = trim($tmpText);
					$addressText .= (!empty($tmpText)) ? $tmpText.$newLine: '';
				}
		return $addressText;
	}
	
	/**
	* Returns selected fields required for an address in an array according to a selection hash
	* @return array String values 
	*/
	public static function getValidateFields($className)
	{
		$propertyList = array();
		
		if (class_exists($className))
		{
			$object = new $className();
			$reflect = new ReflectionObject($object);
			
			// Check if the property is accessible
			$publicProperties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
			foreach($publicProperties as $property)
			{
				$propertyName = $property->getName();
				if ((!in_array($propertyName, AddressFormat::$forbiddenProperyList)) && 
						(!preg_match('#id|id_\w#', $propertyName, $match)))
					$propertyList[] = $propertyName;
			}
			unset($object);
			unset($reflect);
		}
		return $propertyList;
	}
	
	/*
	 * Return a list of liable class of the className
	 */
	public static function getLiableClass($className)
	{
		$objectList = array();

		if (class_exists($className))
		{
			$object = new $className();
			$reflect = new ReflectionObject($object);

			// Get all the name object liable to the Address class
			$publicProperties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
			foreach($publicProperties as $property)
			{
				$propertyName = $property->getName();
				if (preg_match('#id_\w#', $propertyName, $match) && strlen($propertyName) > 3)
				{
					$nameObject = ucfirst(substr($propertyName, 3));
					if (!in_array($nameObject, self::$forbiddenClassList) && 
							class_exists($nameObject))
						$objectList[$nameObject] = new $nameObject();
				}
			}
			unset($object);
			unset($reflect);
		}
		return $objectList;
	}
	
	/**
	 * Returns address format fields in array by country
	 * 
	 * @param Integer PS_COUNTRY.id if null using default country 
	 * @return Array String field address format
	 */
	public static function getOrderedAddressFields($id_country = 0, $split_all = false)
	{
		$out = array();
		$field_set = explode("\n", self::getAddressCountryFormat($id_country));
		foreach ($field_set as $field_item)
			if ($split_all)
				foreach(explode(' ',$field_item) as $word_item)
					$out[] = trim($word_item);
			else
				$out[] = trim($field_item);
		return $out;
	}

	/**
	 * Returns address format by country if not defined using default country
	 * 
	 * @param Integer PS_COUNTRY.id 
	 * @return String field address format
	 */
	public static function getAddressCountryFormat($id_country = 0)
	{
		$out = '';
		$id_country = (int) $id_country;

 		if ($id_country <= 0)
		{
			$selectedCountry = (int)(Configuration::get('PS_COUNTRY_DEFAULT'));
		}

		$tmp_obj = new AddressFormat();
		$tmp_obj->id_country = $id_country;
		$out = $tmp_obj->getFormat($tmp_obj->id_country);
		unset($tmp_obj);
		return $out;
	}

	/**
	 * Returns address format by country
	 * 
	 * @param Integer PS_COUNTRY.id 
	 * @return String field address format
	 */
	public function getFormat($id_country)
	{
		$out = $this->_getFormatDB($id_country);
		
		if (strlen(trim($out)) == 0)
			$out = $this->_getFormatDB(Configuration::get('PS_COUNTRY_DEFAULT'));
		return $out;
	}

	private function _getFormatDB($id_country)
	{
		$result = Db::getInstance()->getRow('
		SELECT format 
		FROM `'._DB_PREFIX_.$this->table.'`
		WHERE `id_country` = '.(int)($id_country));

		return isset($result['format']) ? trim($result['format']) : '';
	}
}

