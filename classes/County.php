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
*  @license	http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CountyCore extends ObjectModel
{
	public $id;
	public $name;
	public $id_state;
	public $active;

	protected 	$fieldsRequired = array('name');
	protected 	$fieldsSize = array('name' => 64);
	protected 	$fieldsValidate = array('name' => 'isGenericName', 'id_state' => 'isUnsignedId', 'active' => 'isBool');

	protected 	$table = 'county';
	protected 	$identifier = 'id_county';

	private static $_cache_get_counties = array();
	private static $_cache_county_zipcode = array();

	const USE_BOTH_TAX = 0;
	const USE_COUNTY_TAX = 1;
	const USE_STATE_TAX = 2;

	protected	$webserviceParameters = array(
		'fields' => array(
			'id_state' => array('xlink_resource'=> 'states'),
		),
	);

	public function getFields()
	{
		parent::validateFields();
		$fields['id_state'] = (int)($this->id_state);
		$fields['name'] = pSQL($this->name);
		$fields['active'] = (int)($this->active);
		return $fields;
	}

	public function delete()
	{
		$id = $this->id;
		parent::delete();

		// remove associated zip codes & tax rule
		return (County::deleteZipCodeByIdCounty($id) AND TaxRule::deleteTaxRuleByIdCounty($id));
	}

	public static function getCounties($id_state)
	{
		if (!isset(self::$_cache_get_counties[$id_state]))
		{
			self::$_cache_get_counties[$id_state] = Db::getInstance()->ExecuteS('
			SELECT * FROM `'._DB_PREFIX_.'county`
			WHERE `id_state` = '.(int)$id_state
			);
		}

		return self::$_cache_get_counties[$id_state];
	}

	// return the list of associated zipcode
	public function getZipCodes()
	{
		return Db::getInstance()->ExecuteS('
		SELECT * FROM `'._DB_PREFIX_.'county_zip_code`
		WHERE `id_county` = '.(int)$this->id.'
		ORDER BY `from_zip_code` ASC'
		);
	}

	public function addZipCodes($zip_codes)
	{
		list($from, $to) = $this->breakDownZipCode($zip_codes);

		if ($from == 0)
			return false;

		return Db::getInstance()->Execute(
		'INSERT INTO `'._DB_PREFIX_.'county_zip_code` (`id_county`, `from_zip_code`, `to_zip_code`)
		VALUES ('.(int)$this->id.','.(int)$from.','.(int)$to.')'
		);
	}


	public function removeZipCodes($zip_codes)
	{
		list($from, $to) = $this->breakDownZipCode($zip_codes);

		if ($from == 0)
			return false;

		return Db::getInstance()->Execute('
		DELETE FROM `'._DB_PREFIX_.'county_zip_code`
		WHERE `id_county` = '.(int)$this->id.'
		AND `from_zip_code` = '.(int)$from.'
		AND `to_zip_code` = '.(int)$to
		);
	}


	public function breakDownZipCode($zip_codes)
	{
		$zip_codes = preg_split('/-/', $zip_codes);

		if (sizeof($zip_codes) == 2)
		{
			$from = $zip_codes[0];
			$to   = $zip_codes[1];
			if ($zip_codes[0] > $zip_codes[1])
			{
				$from = $zip_codes[1];
				$to   = $zip_codes[0];
			}
			else if ($zip_codes[0] == $zip_codes[1])
			{
				$from = $zip_codes[0];
				$to   = 0;
			}
		}
		else if (sizeof($zip_codes) == 1)
		{
			$from = $zip_codes[0];
			$to = 0;
		}

		if (!Validate::isInt($from) OR !Validate::isInt($to))
		{
			$from = 0;
			$to = 0;
		}

		return array($from, $to);
	}

	public static function getIdCountyByZipCode($id_state, $zip_code)
	{
		if (!isset(self::$_cache_county_zipcode[$id_state.'-'.$zip_code]))
		{
			self::$_cache_county_zipcode[$id_state.'-'.$zip_code] = Db::getInstance()->getValue('
			SELECT DISTINCT c.`id_county` FROM `'._DB_PREFIX_.'county` c
			LEFT JOIN `'._DB_PREFIX_.'county_zip_code` cz ON (c.`id_county` = cz.`id_county`)
			WHERE `id_state` = '.(int)$id_state.'
			AND cz.`from_zip_code` >= '.(int)$zip_code.'
			AND cz.`to_zip_code` <= '.(int)$zip_code
			);
		}

		return self::$_cache_county_zipcode[$id_state.'-'.$zip_code];
	}

	public function isZipCodeRangePresent($zip_codes)
	{
		$res = false;
		list($from, $to) = $this->breakDownZipCode($zip_codes);

		if ($from == 0)
			return false;

		if ($to != 0)
		{
			$res = Db::getInstance()->getValue('
			SELECT COUNT(*) FROM `'._DB_PREFIX_.'county_zip_code` cz
			LEFT JOIN `'._DB_PREFIX_.'county` c ON (c.`id_county` = cz.`id_county`)
			LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = c.`id_state`)
			WHERE `from_zip_code` >= '.(int)$from.'
			AND `to_zip_code` <= '.(int)$to.'
			AND s.`id_country` = (SELECT `id_country`
										 FROM `'._DB_PREFIX_.'state` s
										 LEFT JOIN `'._DB_PREFIX_.'county` c ON (c.`id_state` = s.`id_state`)
										 WHERE `id_county` = '.(int)$this->id.'
										)'
			);
		}

		return ($res OR County::isZipCodePresent($from) OR County::isZipCodePresent($to));
	}

	public function isZipCodePresent($zip_code)
	{

		if ($zip_code == 0)
			return false;

		return (bool) Db::getInstance()->getValue('
		SELECT COUNT(*) FROM `'._DB_PREFIX_.'county_zip_code` cz
		LEFT JOIN `'._DB_PREFIX_.'county` c ON (c.`id_county` = cz.`id_county`)
		LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = c.`id_state`)
		WHERE
		(`from_zip_code` <= '.(int)$zip_code.' AND `to_zip_code` >= '.(int)$zip_code.')
		OR
		(`from_zip_code` = '.(int)$zip_code.')
		AND s.`id_country` = (SELECT `id_country`
									 FROM `'._DB_PREFIX_.'state` s
									 LEFT JOIN `'._DB_PREFIX_.'county` c ON (c.`id_state` = s.`id_state`)
									 WHERE `id_county` = '.(int)$this->id.'
									)'
		);
	}

	public static function deleteZipCodeByIdCounty($id_county)
	{
		return Db::getInstance()->Execute(
		'DELETE FROM `'._DB_PREFIX_.'county_zip_code`
		WHERE `id_county` = '.(int)$id_county
		);
	}


	public static function getIdCountyByNameAndIdState($name, $id_state)
	{
		return Db::getInstance()->getValue('
		SELECT `id_county` FROM `'._DB_PREFIX_.'county`
		WHERE `name` = \''.pSQL($name).'\'
		AND `id_state` = '.(int)$id_state
		);
	}

}

