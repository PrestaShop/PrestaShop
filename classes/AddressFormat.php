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

class AddressFormatCore extends ObjectModel
{
	/** @var integer */
	public $id_address_format;
	
	/** @var integer */
	public $id_country;

	/** @var string */
	public $format;


	protected	$fieldsRequired = array ('format');	
	protected	$fieldsValidate = array ('format' => 'isGenericName');

	/* MySQL does not allow 'order detail' for a table name */
	protected	$table = 'address_format';
	protected 	$identifier = 'id_country';

	public function getFields()
	{
		parent::validateFields();

		$fields['id_country'] = (int)($this->id_country);
		$fields['format'] = pSQL($this->format);
		
		return $fields;
	}


	public function checkFormatFields()
	{
		$out = true;
		$addr_f_validate = Address::getFieldsValidate();

		$fields_format = explode("\n", $this->format);
		foreach($fields_format as $field_line)
		{
			$fields = explode(' ', trim($field_line));
			foreach($fields as $field_item)
			{
				$field_item = trim($field_item);
				if (!isset($addr_f_validate[$field_item]) && !isset($addr_f_validate['id_'.$field_item]))
					$out = false;
			}
		}
		return $out;
	}
	
	/**
	 * Returns address format fields in array by country
	 * 
	 * @param Integer PS_COUNTRY.id 
	 * @return Array String field address format
	 */
	public static function getOrderedAddressFields($id_country)
	{
		$out = array();
		$field_set = explode("\n", self::getAddressCountryFormat($id_country));
		foreach ($field_set as $field_item)
		{
			$out[] = trim($field_item);
		}
		return $out;
	}

	/**
	 * Returns address format by country if not defined using default country
	 * 
	 * @param Integer PS_COUNTRY.id 
	 * @return String field address format
	 */
	public static function getAddressCountryFormat($id_country)
	{
		$out = ''; 
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
		global $defaultCountry;
		$out = $this->_getFormatDB($id_country);
		if (strlen(trim($out)) == 0)
		{
			$out = $this->_getFormatDB($defaultCountry->id);
		}
		return $out;
	}

	private function _getFormatDB($id_country)
	{
		$result = Db::getInstance()->getRow('
		SELECT format 
		FROM `'._DB_PREFIX_.$this->table.'`
		WHERE `id_country` = '.(int)($id_country));

		return isset($result['format']) ? $result['format'] : false;
	}
}

