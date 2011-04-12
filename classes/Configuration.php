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

class ConfigurationCore extends ObjectModel
{
	public 		$id;

	/** @var string Key */
	public 		$name;

	/** @var string Value */
	public 		$value;

	/** @var string Object creation date */
	public 		$date_add;

	/** @var string Object last modification date */
	public 		$date_upd;

	protected	$fieldsRequired = array('name');
	protected	$fieldsSize = array('name' => 32);
	protected	$fieldsValidate = array('name' => 'isConfigName');

	protected	$table = 'configuration';
	protected 	$identifier = 'id_configuration';

	/** @var array Configuration cache */
	protected static $_CONF;
	/** @var array Configuration multilang cache */
	protected static $_CONF_LANG;

	protected	$webserviceParameters = array(
			'fields' => array(
				'value' => array(),
				'date_add' => array(),
				'date_upd' => array()
		)
	);
	
	protected	$webserviceParametersI18n = array(
			'retrieveData' => array('retrieveMethod' => 'getI18nConfigurationList'),
			'fields' => array(
			'value' => array('i18n' => true),
			'date_add' => array('i18n' => true),
			'date_upd' => array('i18n' => true)
		)
	);
	
	public function getFields()
	{
		parent::validateFields();
		$fields['name'] = pSQL($this->name);
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
		parent::validateFieldsLang();
		return parent::getTranslationsFields(array('value'));
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
	 		die(Tools::displayError());

		if (Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'configuration_lang` WHERE `id_configuration` =
		(SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE `name` = \''.pSQL($key).'\')') AND Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'configuration` WHERE `name` = \''.pSQL($key).'\''))
		{
			unset(self::$_CONF[$key]);
			return true;
		}
		return false;
	}

	/**
	  * Get a single configuration value (in one language only)
	  *
	  * @param string $key Key wanted
	  * @param integer $id_lang Language ID
	  * @return string Value
	  */
	static public function get($key, $id_lang = NULL)
	{
	 	if (!Validate::isConfigName($key))
	 		die(Tools::displayError());
		if ($id_lang AND isset(self::$_CONF_LANG[(int)($id_lang)][$key]))
			return self::$_CONF_LANG[(int)($id_lang)][$key];
		elseif (key_exists($key, self::$_CONF))
			return self::$_CONF[$key];
		return false;
	}

	/**
	  * Set TEMPORARY a single configuration value (in one language only)
	  *
	  * @param string $key Key wanted
	  * @param mixed $values $values is an array if the configuration is multilingual, a single string else.
	  */
	static public function set($key, $values)
	{
		if (!Validate::isConfigName($key))
	 		die(Tools::displayError());
	 	/* Update classic values */
		if (!is_array($values))
			self::$_CONF[$key] = $values;
		/* Update multilingual values */
		else
			/* Add multilingual values */
			foreach ($values as $k => $value)
				self::$_CONF_LANG[(int)($k)][$key] = $value;
	}

	/**
	  * Get a single configuration value (in multiple languages)
	  *
	  * @param string $key Key wanted
	  * @return array Values in multiple languages
	  */
	static public function getInt($key)
	{
		$languages = Language::getLanguages();
		$resultsArray = array();
		foreach($languages as $language)
			$resultsArray[$language['id_lang']] = self::get($key, $language['id_lang']);
		return $resultsArray;
	}

	/**
	  * Get several configuration values (in one language only)
	  *
	  * @param array $keys Keys wanted
	  * @param integer $id_lang Language ID
	  * @return array Values
	  */
	static public function getMultiple($keys, $id_lang = NULL)
	{
	 	if (!is_array($keys) OR !is_array(self::$_CONF) OR ($id_lang AND !is_array(self::$_CONF_LANG)))
	 		die(Tools::displayError());

		$resTab = array();
		if (!$id_lang)
		{
			foreach ($keys AS $key)
				if (key_exists($key, self::$_CONF))
					$resTab[$key] = self::$_CONF[$key];
		}
		elseif (key_exists($id_lang, self::$_CONF_LANG))
			foreach ($keys AS $key)
				if (key_exists($key, self::$_CONF_LANG[(int)($id_lang)]))
					$resTab[$key] = self::$_CONF_LANG[(int)($id_lang)][$key];
		return $resTab;
	}

	/**
	  * Get several configuration values (in multiple languages)
	  *
	  * @param array $keys Keys wanted
	  * @return array Values in multiple languages
	  * @deprecated
	  */
	static public function getMultipleInt($keys)
	{
		Tools::displayAsDeprecated();
		$languages = Language::getLanguages();
		$resultsArray = array();
		foreach($languages as $language)
			$resultsArray[$language['id_lang']] = self::getMultiple($keys, $language['id_lang']);
		return $resultsArray;
	}

	/**
	  * Insert configuration key and value into database
	  *
	  * @param string $key Key
	  * @param string $value Value
	  * @eturn boolean Insert result
	  */
	static protected function _addConfiguration($key, $value = NULL)
	{
		$newConfig = new Configuration();
		$newConfig->name = $key;
		if (!is_null($value))
			$newConfig->value = $value;
		return $newConfig->add();
	}

	/**
	  * Update configuration key and value into database (automatically insert if key does not exist)
	  *
	  * @param string $key Key
	  * @param mixed $values $values is an array if the configuration is multilingual, a single string else.
	  * @param boolean $html Specify if html is authorized in value
	  * @return boolean Update result
	  */
	static public function updateValue($key, $values, $html = false)
	{
		if ($key == NULL) return;
		if (!Validate::isConfigName($key))
	 		die(Tools::displayError());
		$db = Db::getInstance();
		/* Update classic values */
		if (!is_array($values))
		{
			$values = pSQL($values, $html);
		 	if (Configuration::get($key) !== false)
		 	{
				$result = $db->AutoExecute(
					_DB_PREFIX_.'configuration',
					array('value' => $values, 'date_upd' => date('Y-m-d H:i:s')),
					'UPDATE', '`name` = \''.pSQL($key).'\'', true, true);
				self::$_CONF[$key] = stripslashes($values);
			}
			else
			{
				$result = self::_addConfiguration($key, $values);
				if ($result)
					self::$_CONF[$key] = stripslashes($values);
				return $result;
			}
		}

		/* Update multilingual values */
		else
		{
			$result = 1;
			/* Add the key in the configuration table if it does not already exist... */
			$conf = $db->getRow('SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE `name` = \''.pSQL($key).'\'');
			if (!is_array($conf) OR !array_key_exists('id_configuration', $conf))
			{
				self::_addConfiguration($key);
				$conf = $db->getRow('SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE `name` = \''.pSQL($key).'\'');
			}
			/* ... then add multilingual values into configuration_lang table */
			if (!array_key_exists('id_configuration', $conf) OR !(int)($conf['id_configuration']))
				return false;
			foreach ($values as $id_lang => $value)
			{
				$value = pSQL($value, $html);
				$result &= $db->Execute('INSERT INTO `'._DB_PREFIX_.'configuration_lang` (`id_configuration`, `id_lang`, `value`, `date_upd`)
										VALUES ('.$conf['id_configuration'].', '.(int)($id_lang).', \''.$value.'\', NOW())
										ON DUPLICATE KEY UPDATE `value` = \''.$value.'\', `date_upd` = NOW()');
				self::$_CONF_LANG[(int)($id_lang)][$key] = stripslashes($value);
			}
		}
		return $result;
	}

	static public function loadConfiguration()
	{
		/* Configuration */
		self::$_CONF = array();
		$result = Db::getInstance()->ExecuteS('SELECT `name`, `value` FROM `'._DB_PREFIX_.'configuration`');
		if ($result)
			foreach ($result AS $row)
				self::$_CONF[$row['name']] = stripslashes($row['value']);

		/* Multilingual configuration */
		self::$_CONF_LANG = array();
		$result = Db::getInstance()->ExecuteS('
		SELECT c.`name`, cl.`id_lang`, IFNULL(cl.`value`, c.`value`) AS value
		FROM `'._DB_PREFIX_.'configuration_lang` cl
		LEFT JOIN `'._DB_PREFIX_.'configuration` c ON c.id_configuration = cl.id_configuration');
		if ($result === false)
			die(Tools::displayError('Invalid loadConfiguration() SQL query'));
		foreach ($result AS $row)
			self::$_CONF_LANG[(int)($row['id_lang'])][$row['name']] = stripslashes($row['value']);
	}
	
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
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
	}
	
	public function getI18nConfigurationList($sql_join, $sql_filter, $sql_sort, $sql_limit)
	{
		$query = '
		SELECT DISTINCT main.`'.$this->identifier.'` FROM `'._DB_PREFIX_.$this->table.'` main
		'.$sql_join.'
		WHERE id_configuration IN 
		(	SELECT id_configuration
			FROM '._DB_PREFIX_.$this->table.'_lang
		) '.$sql_filter.'
		'.($sql_sort != '' ? $sql_sort : '').'
		'.($sql_limit != '' ? $sql_limit : '').'
		';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
	}
}


