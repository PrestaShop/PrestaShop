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
*  @version  Release: $Revision: 7227 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ConfigurationCore extends ObjectModel
{
	public 		$id;

	/** @var string Key */
	public 		$name;

	public		$id_group_shop;
	public		$id_shop;

	/** @var string Value */
	public 		$value;

	/** @var string Object creation date */
	public 		$date_add;

	/** @var string Object last modification date */
	public 		$date_upd;

	protected	$fieldsRequired = array('name');
	protected	$fieldsSize = array('name' => 32);
	protected	$fieldsValidate = array('name' => 'isConfigName', 'id_group_shop' => 'isUnsignedId', 'id_shop' => 'isUnsignedId');

	protected	$table = 'configuration';
	protected 	$identifier = 'id_configuration';

	/** @var array Configuration cache */
	protected static $_CONF;

	/** @var array Vars types */
	protected static $types = array();

	protected $webserviceParameters = array(
		'fields' => array(
			'value' => array(),
		)
	);

	public function getFields()
	{
		$this->validateFields();
		$fields['name'] = pSQL($this->name);
		$fields['id_group_shop'] = $this->id_group_shop;
		$fields['id_shop'] = $this->id_shop;
		$fields['value'] = pSQL($this->value);
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		return $fields;
	}

	/**
	  * Check then return multilingual fields for database interaction
	  *
	  * @return array Multilingual fields
	  */
	public function getTranslationsFieldsChild()
	{
		if (!is_array($this->value))
			return true;
		$this->validateFieldsLang();
		return $this->getTranslationsFields(array('value'));
	}

	/**
	 * Return ID a configuration key
	 *
	 * @param string $key
	 * @param int $shopGroupID
	 * @param int $shopID
	 */
	public static function getIdByName($key, $shopGroupID = null, $shopID = null)
	{
		self::getShopFromContext($shopGroupID, $shopID);
		$sql = 'SELECT id_configuration
				FROM '._DB_PREFIX_.'configuration
				WHERE name = \''.pSQL($key).'\''
					.Configuration::sqlRestriction($shopGroupID, $shopID);
		return (int)Db::getInstance()->getValue($sql);
	}

	/**
	 * Load all configuration data
	 */
	static public function loadConfiguration()
	{
		self::$_CONF = array();
		$sql = 'SELECT c.`name`, cl.`id_lang`, IF(cl.`id_lang` IS NULL, c.`value`, cl.`value`) AS value, c.id_group_shop, c.id_shop
				FROM `'._DB_PREFIX_.'configuration` c
				LEFT JOIN `'._DB_PREFIX_.'configuration_lang` cl ON (c.id_configuration = cl.id_configuration)';
		if (!$results = Db::getInstance()->executeS($sql))
			return;

		foreach ($results as $row)
		{
			$lang = ($row['id_lang']) ? $row['id_lang'] : 0;
			self::$types[$row['name']] = ($lang) ? 'lang' : 'normal';
			if (!isset(self::$_CONF[$lang]))
				self::$_CONF[$lang] = array(
					'global' => array(),
					'group' => array(),
					'shop' => array(),
				);

			// Do not remove (int) cast
			Configuration::set($row['name'], array($lang => $row['value']), (int)$row['id_group_shop'], (int)$row['id_shop']);
		}
	}

	/**
	  * Get a single configuration value (in one language only)
	  *
	  * @param string $key Key wanted
	  * @param integer $id_lang Language ID
	  * @return string Value
	  */
	static public function get($key, $langID = NULL, $shopGroupID = NULL, $shopID = NULL)
	{
		self::getShopFromContext($shopGroupID, $shopID);
		$langID = (int)$langID;
		if (!isset(self::$_CONF[$langID]))
			$langID = 0;

		// If conf if not initialized, try manual query
		if (!self::$_CONF)
			return Db::getInstance()->getValue('SELECT `value` FROM '._DB_PREFIX_.'configuration WHERE `name` = \''.pSQL($key).'\'');

		if ($shopID && Configuration::hasKey($key, $langID, null, $shopID))
			return self::$_CONF[$langID]['shop'][$shopID][$key];
		else if ($shopGroupID && Configuration::hasKey($key, $langID, $shopGroupID))
			return self::$_CONF[$langID]['group'][$shopGroupID][$key];
		else if (Configuration::hasKey($key, $langID))
			return self::$_CONF[$langID]['global'][$key];
		return false;
	}
	
	static public function getGlobalValue($key, $langID = NULL)
	{
		return self::get($key, $langID, 0, 0);
	}

	/**
	  * Get a single configuration value (in multiple languages)
	  *
	  * @param string $key Key wanted
	  * @param int $shopGroupID
	  * @param int $shopID
	  * @return array Values in multiple languages
	  */
	static public function getInt($key, $id_group_shop = NULL, $id_shop = NULL)
	{
		$languages = Language::getLanguages();
		$resultsArray = array();
		foreach ($languages as $language)
			$resultsArray[$language['id_lang']] = self::get($key, $language['id_lang'], $id_group_shop, $id_shop);
		return $resultsArray;
	}

	/**
	  * Get several configuration values (in one language only)
	  *
	  * @param array $keys Keys wanted
	  * @param integer $id_lang Language ID
	  * @return array Values
	  */
	static public function getMultiple($keys, $langID = NULL, $shopGroupID = NULL, $shopID = NULL)
	{
	 	if (!is_array($keys))
	 		throw new PrestashopException('keys var is not an array');

		$langID = (int)$langID;
		self::getShopFromContext($shopGroupID, $shopID);

	 	$results = array();
	 	foreach ($keys as $key)
	 		$results[$key] = Configuration::get($key, $langID, $shopGroupID, $shopID);
		return $results;
	}

	/**
	 * Check if key exists in configuration
	 *
	 * @param string $key
	 * @param int $id_lang
	 * @param int $shopGroupID
	 * @param int $shopID
	 * @return bool
	 */
	public static function hasKey($key, $langID = null, $shopGroupID = null, $shopID = null)
	{
		$langID = (int)$langID;
		if ($shopID)
			return isset(self::$_CONF[$langID]['shop'][$shopID]) && array_key_exists($key, self::$_CONF[$langID]['shop'][$shopID]);
		else if ($shopGroupID)
			return isset(self::$_CONF[$langID]['group'][$shopGroupID]) && array_key_exists($key, self::$_CONF[$langID]['group'][$shopGroupID]);
		return isset(self::$_CONF[$langID]['global']) && array_key_exists($key, self::$_CONF[$langID]['global']);
	}

	/**
	  * Set TEMPORARY a single configuration value (in one language only)
	  *
	  * @param string $key Key wanted
	  * @param mixed $values $values is an array if the configuration is multilingual, a single string else.
	  * @param int $shopGroupID
	  * @param int $shopID
	  */
	static public function set($key, $values, $id_group_shop = NULL, $id_shop = NULL)
	{
		if (!Validate::isConfigName($key))
			die(Tools::displayError());
		self::getShopFromContext($id_group_shop, $id_shop);

		if (!is_array($values))
			$values = array($values);

		foreach ($values as $lang => $value)
		{
			if ($id_shop)
				self::$_CONF[$lang]['shop'][$id_shop][$key] = $value;
			else if ($id_group_shop)
				self::$_CONF[$lang]['group'][$id_group_shop][$key] = $value;
			else
				self::$_CONF[$lang]['global'][$key] = $value;
		}
	}

	/**
	 * Update configuration key for global context only
	 *
	 * @param string $key
	 * @param mixed $values
	 * @param bool $html
	 * @return bool
	 */
	static public function updateGlobalValue($key, $values, $html = false)
	{
		return Configuration::updateValue($key, $values, $html, 0, 0);
	}

	/**
	  * Update configuration key and value into database (automatically insert if key does not exist)
	  *
	  * @param string $key Key
	  * @param mixed $values $values is an array if the configuration is multilingual, a single string else.
	  * @param boolean $html Specify if html is authorized in value
	  * @param int $shopGroupID
	  * @param int $shopID
	  * @return boolean Update result
	  */
	static public function updateValue($key, $values, $html = false, $shopGroupID = null, $shopID = null)
	{
		if (!Validate::isConfigName($key))
	 		die(Tools::displayError());
		self::getShopFromContext($shopGroupID, $shopID);

		if (!is_array($values))
			$values = array($values);

		$result = true;
		foreach ($values as $lang => $value)
		{
			if ($value == Configuration::get($key, $lang, $shopGroupID, $shopID))
				continue;

			// If key already exists, update value
			if (Configuration::hasKey($key, $lang, $shopGroupID, $shopID))
			{
				if (!$lang)
				{
					// Update config not linked to lang
					$result &= Db::getInstance()->AutoExecute(_DB_PREFIX_.'configuration', array(
						'value' => pSQL($value, $html),
						'date_upd' => date('Y-m-d H:i:s'),
					), 'UPDATE', '`name` = \''.pSQL($key).'\''.Configuration::sqlRestriction($shopGroupID, $shopID), true, true);
				}
				else
				{
					// Update multi lang
					$sql = 'UPDATE '._DB_PREFIX_.'configuration_lang cl
							SET cl.value = \''.pSQL($value, $html).'\',
								cl.date_upd = NOW()
							WHERE cl.id_lang = '.(int)$lang.'
								AND cl.id_configuration = (
									SELECT c.id_configuration
									FROM '._DB_PREFIX_.'configuration c
									WHERE c.name = \''.pSQL($key).'\''
										.Configuration::sqlRestriction($shopGroupID, $shopID)
								.')';
					$result &= Db::getInstance()->execute($sql);
				}
			}
			// If key does not exists, create it
			else
			{
				if (!$configID = Configuration::getIdByName($key, $shopGroupID, $shopID))
				{
					$newConfig = new Configuration();
					$newConfig->name = $key;
					if ($shopID)
						$newConfig->id_shop = (int)$shopID;
					if ($shopGroupID)
						$newConfig->id_group_shop = (int)$shopGroupID;
					if (!$lang)
						$newConfig->value = $value;
					$result &= $newConfig->add(true, true);
					$configID = $newConfig->id;
				}

				if ($lang)
				{
					$result &= Db::getInstance()->autoExecute(_DB_PREFIX_.'configuration_lang', array(
						'id_configuration' =>	$configID,
						'id_lang' =>			$lang,
						'value' =>				pSQL($value, $html),
						'date_upd' =>			date('Y-m-d H:i:s'),
					), 'INSERT');
				}
			}

			Configuration::set($key, $value, $shopGroupID, $shopID);
		}

		return $result;
	}

	/**
	  * Delete a configuration key in database (with or without language management)
	  *
	  * @param string $key Key to delete
	  * @return boolean Deletion result
	  */
	static public function deleteByName($key)
	{
	 	if (!Validate::isConfigName($key))
			return false;

		$sql = 'DELETE FROM `'._DB_PREFIX_.'configuration_lang`
				WHERE `id_configuration` = (
					SELECT `id_configuration`
					FROM `'._DB_PREFIX_.'configuration`
					WHERE `name` = \''.pSQL($key).'\'
				)';
		$result = Db::getInstance()->execute($sql);

		$sql = 'DELETE FROM `'._DB_PREFIX_.'configuration`
				WHERE `name` = \''.pSQL($key).'\'';
		$result2 = Db::getInstance()->execute($sql);
		return ($result && $result2);
	}

	/**
	 * Delete configuration key from current context.
	 *
	 * @param string $key
	 */
	public static function deleteFromContext($key)
	{
		list($shopID, $shopGroupID) = Shop::getContext();
		if (!$shopID && !$shopGroupID)
			return;

		$id = Configuration::getIdByName($key, $shopGroupID, $shopID);
		$sql = 'DELETE FROM '._DB_PREFIX_.'configuration
				WHERE id_configuration = '.$id;
		Db::getInstance()->execute($sql);

		$sql = 'DELETE FROM '._DB_PREFIX_.'configuration_lang
				WHERE id_configuration = '.$id;
		Db::getInstance()->execute($sql);
	}

	/**
	 * Check if configuration var is defined in given context
	 *
	 * @param string $key
	 * @param int $langID
	 * @param int $context
	 */
	public static function hasContext($key, $langID, $context)
	{
		list($shopID, $shopGroupID) = Shop::getContext();
		if ($context == Shop::CONTEXT_SHOP && Configuration::hasKey($key, $langID, null, $shopID))
			return true;
		else if ($context == Shop::CONTEXT_GROUP && Configuration::hasKey($key, $langID, $shopGroupID))
			return true;
		else if ($context == Shop::CONTEXT_ALL && Configuration::hasKey($key, $langID))
			return true;
		return false;
	}

	public static function isOverridenByCurrentContext($key)
	{
		if (Configuration::isLangKey($key))
		{
			$testContext = false;
			foreach (Language::getLanguages(false) as $lang)
				if ((Context::shop() == Shop::CONTEXT_SHOP && Configuration::hasContext($key, $lang['id_lang'], Shop::CONTEXT_SHOP))
					|| (Context::shop() == Shop::CONTEXT_GROUP && Configuration::hasContext($key, $lang['id_lang'], Shop::CONTEXT_GROUP)))
						$testContext = true;
		}
		else
		{
			$testContext = ((Context::shop() == Shop::CONTEXT_SHOP && Configuration::hasContext($key, null, Shop::CONTEXT_SHOP))
							|| (Context::shop() == Shop::CONTEXT_GROUP && Configuration::hasContext($key, null, Shop::CONTEXT_GROUP))) ? true : false;
		}

		return (Shop::isFeatureActive() && Context::shop() != Shop::CONTEXT_ALL && $testContext);
	}

	/**
	 * Check if a key was loaded as multi lang
	 *
	 * @param string $key
	 * @return bool
	 */
	public static function isLangKey($key)
	{
		return (isset(self::$types[$key]) && self::$types[$key] == 'lang') ? true : false;
	}

	/**
	 * Fill $id_group_shop and $id_shop vars from correct context
	 *
	 * @param int $id_group_shop
	 * @param int $id_shop
	 */
	protected static function getShopFromContext(&$id_group_shop, &$id_shop)
	{
		list($shopID, $shopGroupID) = Shop::getContext();
		if (is_null($id_shop))
			$id_shop = $shopID;
		if (is_null($id_group_shop))
			$id_group_shop = $shopGroupID;

		$id_shop = (int)$id_shop;
		$id_group_shop = (int)$id_group_shop;
	}

	/**
	 * Add SQL restriction on shops for configuration table
	 *
	 * @param int $shopGroupID
	 * @param int $shopID
	 * @return string
	 */
	protected static function sqlRestriction($shopGroupID, $shopID)
	{
		if ($shopID)
			return ' AND id_shop = '.$shopID;
		else if ($shopGroupID)
			return ' AND id_group_shop = '.$shopGroupID.' AND id_shop IS NULL';
		else
			return ' AND id_group_shop IS NULL AND id_shop IS NULL';
	}

	/**
	 * This method is override to allow TranslatedConfiguration entity
	 *
	 * @param $sql_join
	 * @param $sql_filter
	 * @param $sql_sort
	 * @param $sql_limit
	 * @return array
	 */
	public function getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit)
	{
		$query = '
		SELECT DISTINCT main.`'.$this->identifier.'` FROM `'._DB_PREFIX_.$this->table.'` main
		'.$sql_join.'
		WHERE id_configuration NOT IN
		(	SELECT id_configuration
			FROM '._DB_PREFIX_.$this->table.'_lang
		) '.$sql_filter.'
		'.($sql_sort != '' ? $sql_sort : '').'
		'.($sql_limit != '' ? $sql_limit : '').'
		';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
	}
}
