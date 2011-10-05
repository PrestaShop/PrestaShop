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

class GroupCore extends ObjectModel
{
	public $id;

	/** @var string Lastname */
	public $name;

	/** @var string Reduction */
	public $reduction;

	/** @var int Price display method (tax inc/tax exc) */
	public $price_display_method;

	/** @var string Object creation date */
	public $date_add;

	/** @var string Object last modification date */
	public $date_upd;

	protected $tables = array ('group');

	protected $fieldsRequired = array('price_display_method');
	protected $fieldsSize = array();
	protected $fieldsValidate = array('reduction' => 'isFloat', 'price_display_method' => 'isPriceDisplayMethod');

	protected	$fieldsRequiredLang = array('name');
	protected	$fieldsSizeLang = array('name' => 32);
	protected	$fieldsValidateLang = array('name' => 'isGenericName');

	protected $table = 'group';
	protected $identifier = 'id_group';

	protected static $_cacheReduction = array();
	protected static $_groupPriceDisplayMethod = array();

	protected	$webserviceParameters = array();

	public function getFields()
	{
		$this->validateFields();
		if (isset($this->id))
			$fields['id_group'] = (int)($this->id);
		$fields['reduction'] = (float)($this->reduction);
		$fields['price_display_method'] = (int)($this->price_display_method);
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);

		return $fields;
	}

	public function getTranslationsFieldsChild()
	{
		if (!$this->validateFieldsLang())
			return false;
		return $this->getTranslationsFields(array('name'));
	}

	public static function getGroups($id_lang)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT g.`id_group`, g.`reduction`, g.`price_display_method`, gl.`name`
		FROM `'._DB_PREFIX_.'group` g
		LEFT JOIN `'._DB_PREFIX_.'group_lang` AS gl ON (g.`id_group` = gl.`id_group` AND gl.`id_lang` = '.(int)($id_lang).')
		ORDER BY g.`id_group` ASC');
	}

	public function getCustomers($count = false, $start = 0, $limit = 0)
	{
		if ($count)
			return Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'customer_group` cg
			LEFT JOIN `'._DB_PREFIX_.'customer` c ON (cg.`id_customer` = c.`id_customer`)
			WHERE cg.`id_group` = '.(int)$this->id.'
			AND c.`deleted` != 1');
		return Db::getInstance()->ExecuteS('
		SELECT cg.`id_customer`, c.*
		FROM `'._DB_PREFIX_.'customer_group` cg
		LEFT JOIN `'._DB_PREFIX_.'customer` c ON (cg.`id_customer` = c.`id_customer`)
		WHERE cg.`id_group` = '.(int)$this->id.'
		AND c.`deleted` != 1
		ORDER BY cg.`id_customer` ASC
		'.($limit > 0 ? 'LIMIT '.(int)$start.', '.(int)$limit : ''));
	}

	public static function getReduction($id_customer = null)
	{
		if (!isset(self::$_cacheReduction['customer'][(int)$id_customer]))
			self::$_cacheReduction['customer'][(int)$id_customer] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `reduction`
			FROM `'._DB_PREFIX_.'group`
			WHERE `id_group` = '.((int)$id_customer ? Customer::getDefaultGroupId((int)$id_customer) : 1));
		return self::$_cacheReduction['customer'][(int)$id_customer];
		}

	public static function getReductionByIdGroup($id_group)
	{
		if (!isset(self::$_cacheReduction['group'][$id_group]))
		{
			self::$_cacheReduction['group'][$id_group] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `reduction`
			FROM `'._DB_PREFIX_.'group`
			WHERE `id_group` = '.(int)$id_group);
		}
		return self::$_cacheReduction['group'][$id_group];
	}

	public static function getPriceDisplayMethod($id_group)
	{
		if (!isset(self::$_groupPriceDisplayMethod[$id_group]))
			self::$_groupPriceDisplayMethod[$id_group] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `price_display_method`
			FROM `'._DB_PREFIX_.'group`
			WHERE `id_group` = '.(int)$id_group);
		return self::$_groupPriceDisplayMethod[$id_group];
	}

	public static function getDefaultPriceDisplayMethod()
	{
		return self::getPriceDisplayMethod(1);
	}

	public function add($autodate = true, $nullValues = false)
	{
		if (parent::add($autodate, $nullValues))
		{
			Category::setNewGroupForHome((int)$this->id);

			// Set cache of feature detachable to true
			Configuration::updateGlobalValue('PS_GROUP_FEATURE_ACTIVE', '1');
			return true;
		}
		return false;
	}

	public function delete()
	{
		if ($this->id == _PS_DEFAULT_CUSTOMER_GROUP_)
			return false;
		if (parent::delete())
		{
			Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'customer_group` WHERE `id_group` = '.(int)$this->id);
			Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'category_group` WHERE `id_group` = '.(int)$this->id);
			Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'group_reduction` WHERE `id_group` = '.(int)$this->id);
			Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'product_group_reduction_cache` WHERE `id_group` = '.(int)$this->id);
			Discount::deleteByIdGroup((int)$this->id);

			// Refresh cache of feature detachable
			Configuration::updateGlobalValue('PS_GROUP_FEATURE_ACTIVE', self::isCurrentlyUsed());

			// Add default group (id 1) to customers without groups
			Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'customer_group` (
				SELECT c.id_customer, 1 FROM `'._DB_PREFIX_.'customer` c
				LEFT JOIN `'._DB_PREFIX_.'customer_group` cg
				ON cg.id_customer = c.id_customer
				WHERE cg.id_customer IS NULL)');

			// Set to the customer the default group
			// Select the minimal id from customer_group
			Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'customer` cg
				SET id_default_group =
					IFNULL((
						SELECT min(id_group) FROM `'._DB_PREFIX_.'customer_group`
						WHERE id_customer = cg.id_customer),
						1)
				WHERE `id_default_group` = '.(int)$this->id);

			Discount::deleteByIdGroup($this->id);

			return true;
		}
		return false;
	}

	/**
	 * This method is allow to know if a feature is used or active
	 * @since 1.5.0.1
	 * @return bool
	 */
	public static function isFeatureActive()
	{
		return Configuration::get('PS_GROUP_FEATURE_ACTIVE');
	}

	/**
	 * This method is allow to know if a Discount entity is currently used
	 * @since 1.5.0.1
	 * @param $table
	 * @param $has_active_column
	 * @return bool
	 */
	public static function isCurrentlyUsed($table = null, $has_active_column = false)
	{
		// We don't use the parent method, for specific clause reason (id_group != 1)
		return (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_group`
			FROM `'._DB_PREFIX_.'group`
			WHERE `id_group` != 1
		');
	}
}


