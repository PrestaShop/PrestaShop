<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ConfigurationCore extends ObjectModel
{
	public $id;

	/** @var string Key */
	public $name;

	public $id_shop_group;
	public $id_shop;

	/** @var string Value */
	public $value;

	/** @var string Object creation date */
	public $date_add;

	/** @var string Object last modification date */
	public $date_upd;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'configuration',
		'primary' => 'id_configuration',
		'multilang' => true,
		'fields' => array(
			'name' => 			array('type' => self::TYPE_STRING, 'validate' => 'isConfigName', 'required' => true, 'size' => 32),
			'id_shop_group' => 	array('type' => self::TYPE_NOTHING, 'validate' => 'isUnsignedId'),
			'id_shop' => 		array('type' => self::TYPE_NOTHING, 'validate' => 'isUnsignedId'),
			'value' => 			array('type' => self::TYPE_STRING),
			'date_add' => 		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' => 		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		),
	);

	/** @var array Configuration cache */
	protected static $_CONF;

	/** @var array Vars types */
	protected static $types = array();

	protected $webserviceParameters = array(
		'fields' => array(
			'value' => array(),
		)
	);

	/**
	  * @see ObjectModel::getFieldsLang()
	  * @return array Multilingual fields
	  */
	public function getFieldsLang()
	{
		if (!is_array($this->value))
			return true;
		return $this->getFieldsLang();
	}

	/**
	 * Return ID a configuration key
	 *
	 * @param string $key
	 * @param int $id_shop_group
	 * @param int $id_shop
	 */
	public static function getIdByName($key, $id_shop_group = null, $id_shop = null)
	{
		if ($id_shop === null)
			$id_shop = Shop::getContextShopID(true);
		if ($id_shop_group === null)
			$id_shop_group = Shop::getContextShopGroupID(true);

		$sql = 'SELECT id_configuration
				FROM '._DB_PREFIX_.'configuration
				WHERE name = \''.pSQL($key).'\''
					.Configuration::sqlRestriction($id_shop_group, $id_shop);
		return (int)Db::getInstance()->getValue($sql);
	}

	/**
	 * Load all configuration data
	 */
	public static function loadConfiguration()
	{
		self::$_CONF = array();
		$sql = 'SELECT c.`name`, cl.`id_lang`, IF(cl.`id_lang` IS NULL, c.`value`, cl.`value`) AS value, c.id_shop_group, c.id_shop
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

			if ($row['id_shop'])
				self::$_CONF[$lang]['shop'][$row['id_shop']][$row['name']] = $row['value'];
			else if ($row['id_shop_group'])
				self::$_CONF[$lang]['group'][$row['id_shop_group']][$row['name']] = $row['value'];
			else
				self::$_CONF[$lang]['global'][$row['name']] = $row['value'];
		}
	}

	/**
	  * Get a single configuration value (in one language only)
	  *
	  * @param string $key Key wanted
	  * @param integer $id_lang Language ID
	  * @return string Value
	  */
	public static function get($key, $id_lang = null, $id_shop_group = null, $id_shop = null)
	{
		if (defined('_PS_DO_NOT_LOAD_CONFIGURATION_') && _PS_DO_NOT_LOAD_CONFIGURATION_)
			return false;
		
		// If conf if not initialized, try manual query
		if (!self::$_CONF)
		{
			Configuration::loadConfiguration();
			if (!self::$_CONF)
				return Db::getInstance()->getValue('SELECT `value` FROM `'._DB_PREFIX_.'configuration` WHERE `name` = "'.pSQL($key).'"');
		}
		$id_lang = (int)$id_lang;
		if ($id_shop === null)
			$id_shop = Shop::getContextShopID(true);
		if ($id_shop_group === null)
			$id_shop_group = Shop::getContextShopGroupID(true);

		if (!isset(self::$_CONF[$id_lang]))
			$id_lang = 0;
			
		if ($id_shop && Configuration::hasKey($key, $id_lang, null, $id_shop))
			return self::$_CONF[$id_lang]['shop'][$id_shop][$key];
		elseif ($id_shop_group && Configuration::hasKey($key, $id_lang, $id_shop_group))
			return self::$_CONF[$id_lang]['group'][$id_shop_group][$key];
		elseif (Configuration::hasKey($key, $id_lang))
			return self::$_CONF[$id_lang]['global'][$key];
		return false;
	}
	
	public static function getGlobalValue($key, $id_lang = null)
	{
		return Configuration::get($key, $id_lang, 0, 0);
	}

	/**
	  * Get a single configuration value (in multiple languages)
	  *
	  * @param string $key Key wanted
	  * @param int $id_shop_group
	  * @param int $id_shop
	  * @return array Values in multiple languages
	  */
	public static function getInt($key, $id_shop_group = null, $id_shop = null)
	{
		$languages = Language::getLanguages();
		$resultsArray = array();
		foreach ($languages as $language)
			$resultsArray[$language['id_lang']] = Configuration::get($key, $language['id_lang'], $id_shop_group, $id_shop);
		return $resultsArray;
	}

	/**
	  * Get several configuration values (in one language only)
	  *
	  * @param array $keys Keys wanted
	  * @param integer $id_lang Language ID
	  * @return array Values
	  */
	public static function getMultiple($keys, $id_lang = null, $id_shop_group = null, $id_shop = null)
	{
	 	if (!is_array($keys))
	 		throw new PrestaShopException('keys var is not an array');

		$id_lang = (int)$id_lang;
		if ($id_shop === null)
			$id_shop = Shop::getContextShopID(true);
		if ($id_shop_group === null)
			$id_shop_group = Shop::getContextShopGroupID(true);

	 	$results = array();
	 	foreach ($keys as $key)
	 		$results[$key] = Configuration::get($key, $id_lang, $id_shop_group, $id_shop);
		return $results;
	}

	/**
	 * Check if key exists in configuration
	 *
	 * @param string $key
	 * @param int $id_lang
	 * @param int $id_shop_group
	 * @param int $id_shop
	 * @return bool
	 */
	public static function hasKey($key, $id_lang = null, $id_shop_group = null, $id_shop = null)
	{
		$id_lang = (int)$id_lang;
		if ($id_shop)
			return isset(self::$_CONF[$id_lang]['shop'][$id_shop]) && array_key_exists($key, self::$_CONF[$id_lang]['shop'][$id_shop]);
		elseif ($id_shop_group)
			return isset(self::$_CONF[$id_lang]['group'][$id_shop_group]) && array_key_exists($key, self::$_CONF[$id_lang]['group'][$id_shop_group]);
		return isset(self::$_CONF[$id_lang]['global']) && array_key_exists($key, self::$_CONF[$id_lang]['global']);
	}

	/**
	  * Set TEMPORARY a single configuration value (in one language only)
	  *
	  * @param string $key Key wanted
	  * @param mixed $values $values is an array if the configuration is multilingual, a single string else.
	  * @param int $id_shop_group
	  * @param int $id_shop
	  */
	public static function set($key, $values, $id_shop_group = null, $id_shop = null)
	{
		if (!Validate::isConfigName($key))
			die(Tools::displayError());

		if ($id_shop === null)
			$id_shop = Shop::getContextShopID(true);
		if ($id_shop_group === null)
			$id_shop_group = Shop::getContextShopGroupID(true);

		if (!is_array($values))
			$values = array($values);

		foreach ($values as $lang => $value)
		{
			if ($id_shop)
				self::$_CONF[$lang]['shop'][$id_shop][$key] = $value;
			else if ($id_shop_group)
				self::$_CONF[$lang]['group'][$id_shop_group][$key] = $value;
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
	public static function updateGlobalValue($key, $values, $html = false)
	{
		return Configuration::updateValue($key, $values, $html, 0, 0);
	}

	/**
	  * Update configuration key and value into database (automatically insert if key does not exist)
	  *
	  * @param string $key Key
	  * @param mixed $values $values is an array if the configuration is multilingual, a single string else.
	  * @param boolean $html Specify if html is authorized in value
	  * @param int $id_shop_group
	  * @param int $id_shop
	  * @return boolean Update result
	  */
	public static function updateValue($key, $values, $html = false, $id_shop_group = null, $id_shop = null)
	{
		if (!Validate::isConfigName($key))
			die(Tools::displayError());

		if ($id_shop === null)
			$id_shop = Shop::getContextShopID(true);
		if ($id_shop_group === null)
			$id_shop_group = Shop::getContextShopGroupID(true);

		if (!is_array($values))
		{
			$is_i18n = false;
			$values = array($values);
		}
		else
			$is_i18n = true;

		$result = true;
		foreach ($values as $lang => $value)
		{
			if ($value === Configuration::get($key, $lang, $id_shop_group, $id_shop))
				continue;

			// If key already exists, update value
			if (Configuration::hasKey($key, $lang, $id_shop_group, $id_shop))
			{
				if (!$lang)
				{
					// Update config not linked to lang
					$result &= Db::getInstance()->update('configuration', array(
						'value' => pSQL($value, $html),
						'date_upd' => date('Y-m-d H:i:s'),
					), '`name` = \''.pSQL($key).'\''.Configuration::sqlRestriction($id_shop_group, $id_shop), 1, true);
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
										.Configuration::sqlRestriction($id_shop_group, $id_shop)
								.')';
					$result &= Db::getInstance()->execute($sql);
				}
			}
			// If key does not exists, create it
			else
			{
				if (!$configID = Configuration::getIdByName($key, $id_shop_group, $id_shop))
				{
					$newConfig = new Configuration();
					$newConfig->name = $key;
					if ($id_shop)
						$newConfig->id_shop = (int)$id_shop;
					if ($id_shop_group)
						$newConfig->id_shop_group = (int)$id_shop_group;
					if (!$lang)
						$newConfig->value = $value;
					$result &= $newConfig->add(true, true);
					$configID = $newConfig->id;
				}

				if ($lang)
				{
					$result &= Db::getInstance()->insert('configuration_lang', array(
						'id_configuration' =>	$configID,
						'id_lang' =>			$lang,
						'value' =>				pSQL($value, $html),
						'date_upd' =>			date('Y-m-d H:i:s'),
					));
				}
			}
		}

		Configuration::set($key, $values, $id_shop_group, $id_shop);

		return $result;
	}

	/**
	 * Delete a configuration key in database (with or without language management)
	 *
	 * @param string $key Key to delete
	 * @return boolean Deletion result
	 */
	public static function deleteByName($key)
	{
	 	if (!Validate::isConfigName($key))
			return false;

		$result = Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'configuration_lang`
		WHERE `id_configuration` IN (
			SELECT `id_configuration`
			FROM `'._DB_PREFIX_.'configuration`
			WHERE `name` = "'.pSQL($key).'"
		)');

		$result2 = Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'configuration`
		WHERE `name` = "'.pSQL($key).'"');
		
		self::$_CONF = null;
		
		return ($result && $result2);
	}

	/**
	 * Delete configuration key from current context.
	 *
	 * @param string $key
	 */
	public static function deleteFromContext($key)
	{
		if (Shop::getContext() == Shop::CONTEXT_ALL)
			return;
		
		$id_shop = null;
		$id_shop_group = Shop::getContextShopGroupID(true);
		if (Shop::getContext() == Shop::CONTEXT_SHOP)
			$id_shop = Shop::getContextShopID(true);

		$id = Configuration::getIdByName($key, $id_shop_group, $id_shop);
		Db::getInstance()->execute('
		DELETE FROM '._DB_PREFIX_.'configuration
		WHERE id_configuration = '.(int)$id);
		Db::getInstance()->execute('
		DELETE FROM '._DB_PREFIX_.'configuration_lang
		WHERE id_configuration = '.(int)$id);
		
		self::$_CONF = null;
	}

	/**
	 * Check if configuration var is defined in given context
	 *
	 * @param string $key
	 * @param int $id_lang
	 * @param int $context
	 */
	public static function hasContext($key, $id_lang, $context)
	{
		if (Shop::getContext() == Shop::CONTEXT_ALL)
			$id_shop = $id_shop_group = null;
		elseif (Shop::getContext() == Shop::CONTEXT_GROUP)
		{
			$id_shop_group = Shop::getContextShopGroupID(true);
			$id_shop = null;
		}
		else
		{
			$id_shop_group = Shop::getContextShopGroupID(true);
			$id_shop = Shop::getContextShopID(true);
		}

		if ($context == Shop::CONTEXT_SHOP && Configuration::hasKey($key, $id_lang, null, $id_shop))
			return true;
		elseif ($context == Shop::CONTEXT_GROUP && Configuration::hasKey($key, $id_lang, $id_shop_group))
			return true;
		elseif ($context == Shop::CONTEXT_ALL && Configuration::hasKey($key, $id_lang))
			return true;
		return false;
	}

	public static function isOverridenByCurrentContext($key)
	{
		if (Configuration::isLangKey($key))
		{
			$testContext = false;
			foreach (Language::getLanguages(false) as $lang)
				if ((Shop::getContext() == Shop::CONTEXT_SHOP && Configuration::hasContext($key, $lang['id_lang'], Shop::CONTEXT_SHOP))
					|| (Shop::getContext() == Shop::CONTEXT_GROUP && Configuration::hasContext($key, $lang['id_lang'], Shop::CONTEXT_GROUP)))
						$testContext = true;
		}
		else
		{
			$testContext = ((Shop::getContext() == Shop::CONTEXT_SHOP && Configuration::hasContext($key, null, Shop::CONTEXT_SHOP))
							|| (Shop::getContext() == Shop::CONTEXT_GROUP && Configuration::hasContext($key, null, Shop::CONTEXT_GROUP))) ? true : false;
		}

		return (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL && $testContext);
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
	 * Add SQL restriction on shops for configuration table
	 *
	 * @param int $id_shop_group
	 * @param int $id_shop
	 * @return string
	 */
	protected static function sqlRestriction($id_shop_group, $id_shop)
	{
		if ($id_shop)
			return ' AND id_shop = '.(int)$id_shop;
		elseif ($id_shop_group)
			return ' AND id_shop_group = '.(int)$id_shop_group.' AND id_shop IS NULL';
		else
			return ' AND id_shop_group IS NULL AND id_shop IS NULL';
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
		SELECT DISTINCT main.`'.bqSQL($this->def['primary']).'` FROM `'._DB_PREFIX_.bqSQL($this->def['table']).'` main
		'.$sql_join.'
		WHERE id_configuration NOT IN
		(	SELECT id_configuration
			FROM '._DB_PREFIX_.bqSQL($this->def['table']).'_lang
		) '.$sql_filter.'
		'.($sql_sort != '' ? $sql_sort : '').'
		'.($sql_limit != '' ? $sql_limit : '').'
		';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
	}
}
