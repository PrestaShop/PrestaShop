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

	protected static $cache_reduction = array();
	protected static $group_price_display_method = array();

	protected	$webserviceParameters = array();

	public function getFields()
	{
		$this->validateFields();
		if (isset($this->id))
			$fields['id_group'] = (int)$this->id;
		$fields['reduction'] = (float)$this->reduction;
		$fields['price_display_method'] = (int)$this->price_display_method;
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
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT g.`id_group`, g.`reduction`, g.`price_display_method`, gl.`name`
		FROM `'._DB_PREFIX_.'group` g
		LEFT JOIN `'._DB_PREFIX_.'group_lang` AS gl ON (g.`id_group` = gl.`id_group` AND gl.`id_lang` = '.(int)$id_lang.')
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
		return Db::getInstance()->executeS('
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
		if (!isset(self::$cache_reduction['customer'][(int)$id_customer]))
        {
            $id_group = $id_customer ? Customer::getDefaultGroupId((int)$id_customer) : (int)Configuration::get('PS_CUSTOMER_GROUP');
			self::$cache_reduction['customer'][(int)$id_customer] = Group::getReductionByIdGroup($id_group);
        }
		return self::$cache_reduction['customer'][(int)$id_customer];
	}

	public static function getReductionByIdGroup($id_group)
	{
		if (!isset(self::$cache_reduction['group'][$id_group]))
		{
			self::$cache_reduction['group'][$id_group] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `reduction`
			FROM `'._DB_PREFIX_.'group`
			WHERE `id_group` = '.(int)$id_group);
		}
		return self::$cache_reduction['group'][$id_group];
	}

	public static function getPriceDisplayMethod($id_group)
	{
		if (!isset(self::$group_price_display_method[$id_group]))
			self::$group_price_display_method[$id_group] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `price_display_method`
			FROM `'._DB_PREFIX_.'group`
			WHERE `id_group` = '.(int)$id_group);
		return self::$group_price_display_method[$id_group];
	}

	public static function getDefaultPriceDisplayMethod()
	{
		return self::getPriceDisplayMethod((int)Configuration::get('PS_CUSTOMER_GROUP'));
	}

	public function add($autodate = true, $null_values = false)
	{
		if (parent::add($autodate, $null_values))
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
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_group` WHERE `id_group` = '.(int)$this->id);
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customer_group` WHERE `id_group` = '.(int)$this->id);
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category_group` WHERE `id_group` = '.(int)$this->id);
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'group_reduction` WHERE `id_group` = '.(int)$this->id);
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_group_reduction_cache` WHERE `id_group` = '.(int)$this->id);
			$this->truncateModulesRestrictions($this->id);

			// Refresh cache of feature detachable
			Configuration::updateGlobalValue('PS_GROUP_FEATURE_ACTIVE', self::isCurrentlyUsed());

			// Add default group (id 3) to customers without groups
			Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'customer_group` (
				SELECT c.id_customer, '.(int)Configuration::get('PS_CUSTOMER_GROUP').' FROM `'._DB_PREFIX_.'customer` c
				LEFT JOIN `'._DB_PREFIX_.'customer_group` cg
				ON cg.id_customer = c.id_customer
				WHERE cg.id_customer IS NULL)');

			// Set to the customer the default group
			// Select the minimal id from customer_group
			Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'customer` cg
				SET id_default_group =
					IFNULL((
						SELECT min(id_group) FROM `'._DB_PREFIX_.'customer_group`
						WHERE id_customer = cg.id_customer),
						'.(int)Configuration::get('PS_CUSTOMER_GROUP').')
				WHERE `id_default_group` = '.(int)$this->id);

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
		// We don't use the parent method, for specific clause reason (id_group != 3)
		return (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_group`
			FROM `'._DB_PREFIX_.'group`
			WHERE `id_group` != '.(int)Configuration::get('PS_CUSTOMER_GROUP').'
		');
	}

	/**
	 * Truncate all modules restrictions for the group
	 * @param integer id_group
	 * @return boolean result
	 */
	public static function truncateModulesRestrictions($id_group)
	{
		return Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'group_module_restriction`
		WHERE `id_group` = '.(int)$id_group);
	}

	/**
	 * Truncate all restrictions by module
	 * @param integer id_module
	 * @return boolean result
	 */
	public static function truncateRestrictionsByModule($id_module)
	{
		return Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'group_module_restriction`
		WHERE `id_module` = '.(int)$id_module);
	}

	/**
	 * Adding restrictions modules to the group with id $id_group
	 * @param integer id_group
	 * @param array modules
	 * @param integer authorized
	 */
	public static function addModulesRestrictions($id_group, $modules, $authorized)
	{
		if (!is_array($modules) AND !empty($modules))
			return false;
		else
		{
			//delete all record for this group
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'group_module_restriction` WHERE `id_group` = '.(int)$id_group.' AND `authorized` = '.(int)$authorized);
			$sql = 'INSERT INTO `'._DB_PREFIX_.'group_module_restriction` (`id_group`, `id_module`, `authorized`) VALUES ';
			foreach ($modules as $mod)
				$sql .= '("'.(int)$id_group.'", "'.(int)$mod.'", "'.(int)$authorized.'"),';
			// removing last comma to avoid SQL error
			$sql = substr($sql, 0, strlen($sql) - 1);
			Db::getInstance()->execute($sql);
		}
	}

	/**
	 * Add restrictions for a new module
	 * We authorize every groups to the new module
	 * @param integer id_module
	 */
	public static function addRestrictionsForModule($id_module)
	{
		$groups = Group::getGroups(Context::getContext()->language->id);
		$sql = 'INSERT INTO `'._DB_PREFIX_.'group_module_restriction` (`id_group`, `id_module`, `authorized`) VALUES ';
		foreach ($groups as $g)
			$sql .= '("'.(int)$g['id_group'].'", "'.(int)$id_module.'", "1"),';
		// removing last comma to avoid SQL error
		$sql = substr($sql, 0, strlen($sql) - 1);
		Db::getInstance()->execute($sql);
	}
}


