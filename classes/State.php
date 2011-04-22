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

class StateCore extends ObjectModel
{
	/** @var integer Country id which state belongs */
	public 		$id_country;

	/** @var integer Zone id which state belongs */
	public 		$id_zone;

	/** @var string 2 letters iso code */
	public 		$iso_code;

	/** @var string Name */
	public 		$name;

	/** @var boolean Status for delivery */
	public		$active = true;

 	protected 	$fieldsRequired = array('id_country', 'id_zone', 'iso_code', 'name');
 	protected 	$fieldsSize = array('iso_code' => 4, 'name' => 32);
 	protected 	$fieldsValidate = array('id_country' => 'isUnsignedId', 'id_zone' => 'isUnsignedId', 'iso_code' => 'isStateIsoCode', 'name' => 'isGenericName', 'active' => 'isBool');

	protected 	$table = 'state';
	protected 	$identifier = 'id_state';

	protected	$webserviceParameters = array(
		'fields' => array(
			'id_zone' => array('xlink_resource'=> 'zones'),
			'id_country' => array('xlink_resource'=> 'countries')
		),
	);

	public function getFields()
	{
		parent::validateFields();
		$fields['id_country'] = (int)($this->id_country);
		$fields['id_zone'] = (int)($this->id_zone);
		$fields['iso_code'] = pSQL(strtoupper($this->iso_code));
		$fields['name'] = pSQL($this->name);
		$fields['active'] = (int)($this->active);
		return $fields;
	}

	public static function getStates($id_lang = false, $active = false)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT `id_state`, `id_country`, `id_zone`, `iso_code`, `name`, `active`
		FROM `'._DB_PREFIX_.'state`
		'.($active ? 'WHERE active = 1' : '').'
		ORDER BY `name` ASC');
	}

	/**
	* Get a state name with its ID
	*
	* @param integer $id_state Country ID
	* @return string State name
	*/
	static public function getNameById($id_state)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `name`
		FROM `'._DB_PREFIX_.'state`
		WHERE `id_state` = '.(int)($id_state));

        return $result['name'];
    }

	/**
	* Get a state id with its name
	*
	* @param string $id_state Country ID
	* @return integer state id
	*/
	static public function getIdByName($state)
    {
	  	$result = Db::getInstance()->getRow('
		SELECT `id_state`
		FROM `'._DB_PREFIX_.'state`
		WHERE `name` LIKE \''.pSQL($state).'\'');

        return ((int)($result['id_state']));
    }

	/**
	* Get a state id with its iso code
	*
	* @param string $iso_code Iso code
	* @return integer state id
	*/
	static public function getIdByIso($iso_code)
    {
	  	return Db::getInstance()->getValue('
			SELECT `id_state`
			FROM `'._DB_PREFIX_.'state`
			WHERE `iso_code` = \''.pSQL($iso_code).'\''
		);
    }

	/**
	* Delete a state only if is not in use
	*
	* @return boolean
	*/
	public function delete()
	{
		if (!Validate::isTableOrIdentifier($this->identifier) OR !Validate::isTableOrIdentifier($this->table))
			die(Tools::displayError());

		if (!$this->isUsed())
		{
			/* Database deletion */
			$result = Db::getInstance()->Execute('DELETE FROM `'.pSQL(_DB_PREFIX_.$this->table).'` WHERE `'.pSQL($this->identifier).'` = '.(int)($this->id));
			if (!$result)
				return false;

			/* Database deletion for multilingual fields related to the object */
			if (method_exists($this, 'getTranslationsFieldsChild'))
				Db::getInstance()->Execute('DELETE FROM `'.pSQL(_DB_PREFIX_.$this->table).'_lang` WHERE `'.pSQL($this->identifier).'` = '.(int)($this->id));
			return $result;
		}
		else
			return false;
	}

	/**
	 * Check if a state is used
	 *
	 * @return boolean
	 */
	public function isUsed()
	{
		return ($this->countUsed() > 0);
	}

	/**
	 * Returns the number of utilisation of a state
	 *
	 * @return integer count for this state
	 */
	public function countUsed()
	{
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT COUNT(*) AS nb_used
		FROM `'._DB_PREFIX_.'address`
		WHERE `'.pSQL($this->identifier).'` = '.(int)($this->id));
		return $row['nb_used'];
	}

    public static function getStatesByIdCountry($id_country)
    {
        if (empty($id_country))
            die(Tools::displayError());

        return Db::getInstance()->ExecuteS('
        SELECT *
        FROM `'._DB_PREFIX_.'state` s
        WHERE s.`id_country` = '.(int)$id_country
        );
    }

	public static function hasCounties($id_state)
	{
		return sizeof(County::getCounties((int)$id_state));
	}
	
	public static function getIdZone($id_state)
	{
		if (!Validate::isUnsignedId($id_state))
			die(Tools::displayError());

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `id_zone`
		FROM `'._DB_PREFIX_.'state`
		WHERE `id_state` = '.(int)($id_state));
	}
}

