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
*  @version  Release: $Revision: 7499 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

abstract class ObjectModelCore
{
	/** @var integer Object id */
	public $id;

	/** @var integer lang id */
	protected $id_lang = NULL;
	
	protected $id_shop = NULL;
	
	/** @var string SQL Table name */
	protected $table = NULL;

	/** @var string SQL Table identifier */
	protected $identifier = NULL;

	/** @var array Required fields for admin panel forms */
 	protected $fieldsRequired = array();

	/** @var fieldsRequiredDatabase */
	protected static $fieldsRequiredDatabase = NULL;

 	/** @var array Maximum fields size for admin panel forms */
 	protected $fieldsSize = array();

 	/** @var array Fields validity functions for admin panel forms */
 	protected $fieldsValidate = array();

	/** @var array Multilingual required fields for admin panel forms */
 	protected $fieldsRequiredLang = array();

 	/** @var array Multilingual maximum fields size for admin panel forms */
 	protected $fieldsSizeLang = array();

 	/** @var array Multilingual fields validity functions for admin panel forms */
 	protected $fieldsValidateLang = array();
 	
 	protected $langMultiShop = false;

	/** @var array tables */
 	protected $tables = array();

 	/** @var array tables */
 	protected $webserviceParameters = array();

	protected static $_cache = array();

	/** @var  string path to image directory. Used for image deletion. */
	protected $image_dir = NULL;
	
	/** @var string file type of image files. Used for image deletion. */
	protected $image_format = 'jpg';

	/**
	 * Returns object validation rules (fields validity)
	 *
	 * @param string $className Child class name for static use (optional)
	 * @return array Validation rules (fields validity)
	 */
	static public function getValidationRules($className = __CLASS__)
	{
		$object = new $className();
		return array(
		'required' => $object->fieldsRequired,
		'size' => $object->fieldsSize,
		'validate' => $object->fieldsValidate,
		'requiredLang' => $object->fieldsRequiredLang,
		'sizeLang' => $object->fieldsSizeLang,
		'validateLang' => $object->fieldsValidateLang);
	}

	/**
	 * Prepare fields for ObjectModel class (add, update)
	 * All fields are verified (pSQL, intval...)
	 *
	 * @return array All object fields
	 */
	public function getFields()	{ return array(); }

	/**
	 * Build object
	 *
	 * @param integer $id Existing object id in order to load object (optional)
	 * @param integer $id_lang Required if object is multilingual (optional)
	 */
	public function __construct($id = NULL, $id_lang = NULL, $id_shop = NULL)
	{
		if ($id_lang != NULL && Validate::isLoadedObject(new Language($id_lang)))
			$this->id_lang = $id_lang;
		elseif ($id_lang != NULL)
			die(Tools::displayError());
		
		if ($this->langMultiShop AND $id_shop == NULL)
			$id_shop = (int)Shop::getCurrentShop(true);
		if ($id_shop AND $this->langMultiShop)
			$this->id_shop = $id_shop;
	 	/* Connect to database and check SQL table/identifier */
	 	if (!Validate::isTableOrIdentifier($this->identifier) OR !Validate::isTableOrIdentifier($this->table))
			die(Tools::displayError());
		$this->identifier = pSQL($this->identifier);
		/* Load object from database if object id is present */
		if ($id)
		{
			if (!isset(self::$_cache[$this->table][(int)$id][(int)$id_shop][(int)$id_lang]))
				self::$_cache[$this->table][(int)($id)][(int)$id_shop][(int)$id_lang] = Db::getInstance()->getRow('
				SELECT *
				FROM `'._DB_PREFIX_.$this->table.'` a '.
				($id_lang ? ('LEFT JOIN `'.pSQL(_DB_PREFIX_.$this->table).'_lang` b ON (a.`'.$this->identifier.'` = b.`'.$this->identifier).'` AND `id_lang` = '.(int)($id_lang).')' : '')
				.' WHERE 1 AND a.`'.$this->identifier.'` = '.(int)$id.
				(($this->langMultiShop AND $id_shop AND $id_lang) ? ' AND b.id_shop='.(int)$id_shop : ''));
			$result = self::$_cache[$this->table][(int)$id][(int)$id_shop][(int)$id_lang];
			if ($result)
			{
				$this->id = (int)($id);
				foreach ($result AS $key => $value)
					if (key_exists($key, $this))
						$this->{$key} = stripslashes($value);
				
				if (!$id_lang AND method_exists($this, 'getTranslationsFieldsChild'))
				{
					$result = Db::getInstance()->ExecuteS('SELECT * FROM `'.pSQL(_DB_PREFIX_.$this->table).'_lang`
																	WHERE `'.$this->identifier.'` = '.(int)$id
																	.(($this->langMultiShop AND $id_shop) ? ' AND `id_shop`='.(int)$id_shop : ''));
					if ($result)
						foreach ($result as $row)
							foreach ($row AS $key => $value)
							{
								if (key_exists($key, $this) AND $key != $this->identifier)
								{
									if (!is_array($this->{$key}))
										$this->{$key} = array();
									else
										$this->{$key}[$row['id_lang']] = stripslashes($value);
								}
							}
				}
			}
		}

		if (!is_array(self::$fieldsRequiredDatabase))
		{
			$fields = $this->getfieldsRequiredDatabase(true);
			if ($fields)
				foreach ($fields AS $row)
					self::$fieldsRequiredDatabase[$row['object_name']][(int)$row['id_required_field']] = pSQL($row['field_name']);
			else
				self::$fieldsRequiredDatabase = array();
		}
	}

	/**
	 * Save current object to database (add or update)
	 *
	 * return boolean Insertion result
	 */
	public function save($nullValues = false, $autodate = true)
	{
		return (int)($this->id) > 0 ? $this->update($nullValues) : $this->add($autodate, $nullValues);
	}

	/**
	 * Add current object to database
	 *
	 * return boolean Insertion result
	 */
	public function add($autodate = true, $nullValues = false)
	{
	 	if (!Validate::isTableOrIdentifier($this->table))
			die(Tools::displayError());

		/* Automatically fill dates */
		if ($autodate AND key_exists('date_add', $this))
			$this->date_add = date('Y-m-d H:i:s');
		if ($autodate AND key_exists('date_upd', $this))
			$this->date_upd = date('Y-m-d H:i:s');
		/* Database insertion */
		if ($nullValues)
			$result = Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_.$this->table, $this->getFields(), 'INSERT');
		else
			$result = Db::getInstance()->autoExecute(_DB_PREFIX_.$this->table, $this->getFields(), 'INSERT');

		if (!$result)
			return false;
		/* Get object id in database */
		$this->id = Db::getInstance()->Insert_ID();
		$assos = Shop::getAssoTables();
		/* Database insertion for multilingual fields related to the object */
		if (method_exists($this, 'getTranslationsFieldsChild'))
		{
			$fields = $this->getTranslationsFieldsChild();
			$shops = Shop::getShops(true, null, true);
			if ($fields AND is_array($fields))
				foreach ($fields AS &$field)
				{
					foreach ($field AS $key => $value)
					 	if (!Validate::isTableOrIdentifier($key))
			 				die(Tools::displayError());
					$field[$this->identifier] = (int)$this->id;
					$result = Db::getInstance()->AutoExecute(_DB_PREFIX_.$this->table.'_lang', $field, 'INSERT') && $result;
					if (isset($assos[$this->table.'_lang']) && $assos[$this->table.'_lang']['type'] == 'fk_shop')
					{
						foreach ($shops as $id_shop)
						{
							if ($this->id_shop == (int)$id_shop)
								continue;
							$field['id_shop'] = (int)$id_shop;
							$result = Db::getInstance()->AutoExecute(_DB_PREFIX_.$this->table.'_lang', $field, 'INSERT') && $result;
						}
					}
				}
		}
		
		if(!Tools::isMultishopActivated())
		{
			if (isset($assos[$this->table]) && $assos[$this->table]['type'] == 'shop')
				$result &= $this->associateTo(array((int)Shop::getCurrentShop(true)), 'shop');
			$assos = GroupShop::getAssoTables();
			if (isset($assos[$this->table]) && $assos[$this->table]['type'] == 'group_shop')
				$result &= $this->associateTo(array((int)Shop::getCurrentGroupShop()), 'group_shop');
		}
		return $result;
	}

	/**
	 * Update current object to database
	 *
	 * return boolean Update result
	 */
	public function update($nullValues = false)
	{
	 	if (!Validate::isTableOrIdentifier($this->identifier) OR !Validate::isTableOrIdentifier($this->table))
			die(Tools::displayError());

		$this->clearCache();
		/* Automatically fill dates */
		if (key_exists('date_upd', $this))
			$this->date_upd = date('Y-m-d H:i:s');

		/* Database update */
		if ($nullValues)
			$result = Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_.$this->table, $this->getFields(), 'UPDATE', '`'.pSQL($this->identifier).'` = '.(int)($this->id));
		else
			$result = Db::getInstance()->autoExecute(_DB_PREFIX_.$this->table, $this->getFields(), 'UPDATE', '`'.pSQL($this->identifier).'` = '.(int)($this->id));
		if (!$result)
			return false;

		// Database update for multilingual fields related to the object 
		if (method_exists($this, 'getTranslationsFieldsChild'))
		{
			$fields = $this->getTranslationsFieldsChild();
			if (is_array($fields))
			{
				foreach ($fields as $field)
				{
					foreach ($field as $key => $value)
						if (!Validate::isTableOrIdentifier($key))
							die(Tools::displayError());
							
					if ($this->langMultiShop)
						$field['id_shop'] = ($this->id_shop ? $this->id_shop : Shop::getCurrentShop(true));

					// used to insert missing lang entries
					$where_lang = '`'.pSQL($this->identifier).'` = '.(int)$this->id.
												' AND `id_lang` = '.(int)$field['id_lang'].
												(($this->langMultiShop AND $this->id_shop) ? ' AND `id_shop`='.$this->id_shop : '');

					$lang_found = Db::getInstance()->getValue('SELECT COUNT(*) FROM `'.pSQL(_DB_PREFIX_.$this->table).'_lang` WHERE '. $where_lang);
					if (!$lang_found)
						$result &= Db::getInstance()->AutoExecute(_DB_PREFIX_.$this->table.'_lang', $field, 'INSERT');
					else
						$result &= Db::getInstance()->AutoExecute(_DB_PREFIX_.$this->table.'_lang', $field, 'UPDATE', $where_lang);
				}
			}
		}
		return $result;
	}

	/**
	 * Delete current object from database
	 *
	 * return boolean Deletion result
	 */
	public function delete()
	{
	 	if (!Validate::isTableOrIdentifier($this->identifier) OR !Validate::isTableOrIdentifier($this->table))
	 		die(Tools::displayError());

		$this->clearCache();

		/* Database deletion */
		$result = Db::getInstance()->Execute('DELETE FROM `'.pSQL(_DB_PREFIX_.$this->table).'` WHERE `'.pSQL($this->identifier).'` = '.(int)($this->id));
		if (!$result)
			return false;

		/* Database deletion for multilingual fields related to the object */
		if (method_exists($this, 'getTranslationsFieldsChild'))
			Db::getInstance()->Execute('DELETE FROM `'.pSQL(_DB_PREFIX_.$this->table).'_lang` WHERE `'.pSQL($this->identifier).'` = '.(int)($this->id));
		return $result;
	}

	/**
	 * Delete several objects from database
	 *
	 * return boolean Deletion result
	 */
	public function deleteSelection($selection)
	{
		if (!is_array($selection) OR !Validate::isTableOrIdentifier($this->identifier) OR !Validate::isTableOrIdentifier($this->table))
			die(Tools::displayError());
		$result = true;
		foreach ($selection AS $id)
		{
			$this->id = (int)($id);
			$result = $result AND $this->delete();
		}
		return $result;
	}

	/**
	 * Toggle object status in database
	 *
	 * return boolean Update result
	 */
	public function toggleStatus()
	{
	 	if (!Validate::isTableOrIdentifier($this->identifier) OR !Validate::isTableOrIdentifier($this->table))
	 		die(Tools::displayError());

	 	/* Object must have a variable called 'active' */
	 	elseif (!key_exists('active', $this))
	 		die(Tools::displayError());

	 	/* Update active status on object */
	 	$this->active = (int)(!$this->active);

		/* Change status to active/inactive */
		return Db::getInstance()->Execute('
		UPDATE `'.pSQL(_DB_PREFIX_.$this->table).'`
		SET `active` = !`active`
		WHERE `'.pSQL($this->identifier).'` = '.(int)($this->id));
	}

	/**
	 * Prepare multilingual fields for database insertion
	 *
	 * @param array $fieldsArray Multilingual fields to prepare
	 * return array Prepared fields for database insertion
	 */
	protected function getTranslationsFields($fieldsArray)
	{
		/* WARNING : Product do not use this function, so do not forget to report any modification if necessary */
	 	if (!Validate::isTableOrIdentifier($this->identifier))
	 		die(Tools::displayError());

		$fields = array();

		if($this->id_lang == NULL)
			foreach (Language::getLanguages() as $language)
				$this->makeTranslationFields($fields, $fieldsArray, $language['id_lang']);
		else
			$this->makeTranslationFields($fields, $fieldsArray, $this->id_lang);

		return $fields;
	}
	
	protected function makeTranslationFields(&$fields, &$fieldsArray, $id_language)
	{
		$fields[$id_language]['id_lang'] = $id_language;
		$fields[$id_language][$this->identifier] = (int)($this->id);
		if ($this->id_shop AND $this->langMultiShop)
			$fields[$id_language]['id_shop'] = (int)$this->id_shop;
		foreach ($fieldsArray as $field)
		{
			/* Check fields validity */
			if (!Validate::isTableOrIdentifier($field))
				die(Tools::displayError());

			/* Copy the field, or the default language field if it's both required and empty */
			if ((!$this->id_lang AND isset($this->{$field}[$id_language]) AND !empty($this->{$field}[$id_language])) 
			OR ($this->id_lang AND isset($this->$field) AND !empty($this->$field)))
				$fields[$id_language][$field] = $this->id_lang ? pSQL($this->$field) : pSQL($this->{$field}[$id_language]);
			elseif (in_array($field, $this->fieldsRequiredLang))
				$fields[$id_language][$field] = $this->id_lang ? pSQL($this->$field) : pSQL($this->{$field}[Configuration::get('PS_LANG_DEFAULT')]);
			else
				$fields[$id_language][$field] = '';
		}
	}

	/**
	 * Check for fields validity before database interaction
	 */
	public function validateFields($die = true, $errorReturn = false)
	{
		$fieldsRequired = array_merge($this->fieldsRequired, (isset(self::$fieldsRequiredDatabase[get_class($this)]) ? self::$fieldsRequiredDatabase[get_class($this)] : array()));
		foreach ($fieldsRequired as $field)
			if (Tools::isEmpty($this->{$field}) AND (!is_numeric($this->{$field})))
			{
				if ($die) die (Tools::displayError().' ('.get_class($this).' -> '.$field.' is empty)');
				return $errorReturn ? get_class($this).' -> '.$field.' is empty' : false;
			}
		foreach ($this->fieldsSize as $field => $size)
			if (isset($this->{$field}) AND Tools::strlen($this->{$field}) > $size)
			{
				if ($die) die (Tools::displayError().' ('.get_class($this).' -> '.$field.' Length '.$size.')');
				return $errorReturn ? get_class($this).' -> '.$field.' Length '.$size : false;
			}
		$validate = new Validate();
		foreach ($this->fieldsValidate as $field => $method)
			if (!method_exists($validate, $method))
				die (Tools::displayError('Validation function not found.').' '.$method);
			elseif (!empty($this->{$field}) AND !call_user_func(array('Validate', $method), $this->{$field}))
			{
				if ($die) die (Tools::displayError().' ('.get_class($this).' -> '.$field.' = '.$this->{$field}.')');
				return $errorReturn ? get_class($this).' -> '.$field.' = '.$this->{$field} : false;
			}
		return true;
	}

	/**
	 * Check for multilingual fields validity before database interaction
	 */
	public function validateFieldsLang($die = true, $errorReturn = false)
	{
		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		foreach ($this->fieldsRequiredLang as $fieldArray)
		{
			if (!is_array($this->{$fieldArray}))
				continue ;
			if (!$this->{$fieldArray} OR !sizeof($this->{$fieldArray}) OR ($this->{$fieldArray}[$defaultLanguage] !== '0' AND empty($this->{$fieldArray}[$defaultLanguage])))
			{
				if ($die) die (Tools::displayError().' ('.get_class($this).'->'.$fieldArray.' '.Tools::displayError('is empty for default language.').')');
				return $errorReturn ? get_class($this).'->'.$fieldArray.' '.Tools::displayError('is empty for default language.') : false;
			}
		}
		foreach ($this->fieldsSizeLang as $fieldArray => $size)
		{
			if (!is_array($this->{$fieldArray}))
				continue ;
			foreach ($this->{$fieldArray} as $k => $value)
				if (Tools::strlen($value) > $size)
				{
					if ($die) die (Tools::displayError().' ('.get_class($this).'->'.$fieldArray.' '.Tools::displayError('Length').' '.$size.' '.Tools::displayError('for language').')');
					return $errorReturn ? get_class($this).'->'.$fieldArray.' '.Tools::displayError('Length').' '.$size.' '.Tools::displayError('for language') : false;
				}
		}
		$validate = new Validate();
		foreach ($this->fieldsValidateLang as $fieldArray => $method)
		{
			if (!is_array($this->{$fieldArray}))
				continue ;
			foreach ($this->{$fieldArray} as $k => $value)
				if (!method_exists($validate, $method))
					die (Tools::displayError('Validation function not found.').' '.$method);
				elseif (!empty($value) AND !call_user_func(array('Validate', $method), $value))
				{
					if ($die) die (Tools::displayError('The following field is invalid according to the validate method ').'<b>'.$method.'</b>:<br/> ('.get_class($this).'->'.$fieldArray.' = '.$value.' '.Tools::displayError('for language').' '.$k.')');
					return $errorReturn ? Tools::displayError('The following field is invalid according to the validate method ').'<b>'.$method.'</b>:<br/> ('. get_class($this).'->'.$fieldArray.' = '.$value.' '.Tools::displayError('for language').' '.$k : false;
				}
		}
		return true;
	}

	static public function displayFieldName($field, $className = __CLASS__, $htmlentities = true)
	{
		global $_FIELDS, $cookie;
		$iso = strtolower(Language::getIsoById($cookie->id_lang ? (int)$cookie->id_lang : Configuration::get('PS_LANG_DEFAULT')));
		@include(_PS_TRANSLATIONS_DIR_.$iso.'/fields.php');

		$key = $className.'_'.md5($field);
		return ((is_array($_FIELDS) AND array_key_exists($key, $_FIELDS)) ? ($htmlentities ? htmlentities($_FIELDS[$key], ENT_QUOTES, 'utf-8') : $_FIELDS[$key]) : $field);
	}

	/**
	* TODO: refactor rename all calls to this to validateController
	*/
	public function validateControler($htmlentities = true)
	{
		return $this->validateController($htmlentities);
	}

	public function validateController($htmlentities = true)
	{
		$errors = array();

		/* Checking for required fields */
		$fieldsRequired = array_merge($this->fieldsRequired, (isset(self::$fieldsRequiredDatabase[get_class($this)]) ? self::$fieldsRequiredDatabase[get_class($this)] : array()));
		foreach ($fieldsRequired AS $field)
		if (($value = Tools::getValue($field, $this->{$field})) == false AND (string)$value != '0')
			if (!$this->id OR $field != 'passwd')
				$errors[] = '<b>'.self::displayFieldName($field, get_class($this), $htmlentities).'</b> '.Tools::displayError('is required.');


		/* Checking for maximum fields sizes */
		foreach ($this->fieldsSize AS $field => $maxLength)
			if (($value = Tools::getValue($field, $this->{$field})) AND Tools::strlen($value) > $maxLength)
				$errors[] = '<b>'.self::displayFieldName($field, get_class($this), $htmlentities).'</b> '.Tools::displayError('is too long.').' ('.Tools::displayError('Maximum length:').' '.$maxLength.')';

		/* Checking for fields validity */
		foreach ($this->fieldsValidate AS $field => $function)
		{
			// Hack for postcode required for country which does not have postcodes
			if ($value = Tools::getValue($field, $this->{$field}) OR ($field == 'postcode' AND $value == '0'))
			{
				if (!Validate::$function($value))
					$errors[] = '<b>'.self::displayFieldName($field, get_class($this), $htmlentities).'</b> '.Tools::displayError('is invalid.');
				else
				{
					if ($field == 'passwd')
					{
						if ($value = Tools::getValue($field))
							$this->{$field} = Tools::encrypt($value);
					}
					else
						$this->{$field} = $value;
				}
			}
		}
		return $errors;
	}

	public function getWebserviceParameters($wsParamsAttributeName = NULL)
	{
		$defaultResourceParameters = array(
			'objectSqlId' => $this->identifier,
			'retrieveData' => array(
				'className' => get_class($this),
				'retrieveMethod' => 'getWebserviceObjectList',
				'params' => array(),
				'table' => $this->table,
			),
			'fields' => array(
				'id' => array('sqlId' => $this->identifier, 'i18n' => false),
			),
		);

		if (is_null($wsParamsAttributeName))
			$wsParamsAttributeName = 'webserviceParameters';


		if (!isset($this->{$wsParamsAttributeName}['objectNodeName']))
			$defaultResourceParameters['objectNodeName'] = $this->table;
		if (!isset($this->{$wsParamsAttributeName}['objectsNodeName']))
			$defaultResourceParameters['objectsNodeName'] = $this->table.'s';

		if (isset($this->{$wsParamsAttributeName}['associations']))
			foreach ($this->{$wsParamsAttributeName}['associations'] as $assocName => &$association)
			{
				if (!array_key_exists('setter', $association) || (isset($association['setter']) && !$association['setter']))
					$association['setter'] = Tools::toCamelCase('set_ws_'.$assocName);
				if (!array_key_exists('getter', $association))
					$association['getter'] = Tools::toCamelCase('get_ws_'.$assocName);
			}


		if (isset($this->{$wsParamsAttributeName}['retrieveData']) && isset($this->{$wsParamsAttributeName}['retrieveData']['retrieveMethod']))
			unset($defaultResourceParameters['retrieveData']['retrieveMethod']);

		$resourceParameters = array_merge_recursive($defaultResourceParameters, $this->{$wsParamsAttributeName});
		if (isset($this->fieldsSize))
			foreach ($this->fieldsSize as $fieldName => $maxSize)
			{
				if (!isset($resourceParameters['fields'][$fieldName]))
					$resourceParameters['fields'][$fieldName] = array('required' => false);
				$resourceParameters['fields'][$fieldName] = array_merge(
					$resourceParameters['fields'][$fieldName],
					$resourceParameters['fields'][$fieldName] = array('sqlId' => $fieldName, 'maxSize' => $maxSize, 'i18n' => false)
				);
			}
		if (isset($this->fieldsValidate))
			foreach ($this->fieldsValidate as $fieldName => $validateMethod)
			{
				if (!isset($resourceParameters['fields'][$fieldName]))
					$resourceParameters['fields'][$fieldName] = array('required' => false);
				$resourceParameters['fields'][$fieldName] = array_merge(
					$resourceParameters['fields'][$fieldName],
					$resourceParameters['fields'][$fieldName] = array(
						'sqlId' => $fieldName,
						'validateMethod' => (
								array_key_exists('validateMethod', $resourceParameters['fields'][$fieldName]) ?
								array_merge($resourceParameters['fields'][$fieldName]['validateMethod'], array($validateMethod)) :
								array($validateMethod)
							),
						'i18n' => false
					)
				);
			}
		if (isset($this->fieldsRequired))
		{
			$fieldsRequired = array_merge($this->fieldsRequired, (isset(self::$fieldsRequiredDatabase[get_class($this)]) ? self::$fieldsRequiredDatabase[get_class($this)] : array()));
			foreach ($fieldsRequired as $fieldRequired)
			{
				if (!isset($resourceParameters['fields'][$fieldRequired]))
					$resourceParameters['fields'][$fieldRequired] = array();
				$resourceParameters['fields'][$fieldRequired] = array_merge(
					$resourceParameters['fields'][$fieldRequired],
					$resourceParameters['fields'][$fieldRequired] = array('sqlId' => $fieldRequired, 'required' => true, 'i18n' => false)
				);
			}
		}
		if (isset($this->fieldsSizeLang))
			foreach ($this->fieldsSizeLang as $fieldName => $maxSize)
			{
				if (!isset($resourceParameters['fields'][$fieldName]))
					$resourceParameters['fields'][$fieldName] = array('required' => false);
				$resourceParameters['fields'][$fieldName] = array_merge(
					$resourceParameters['fields'][$fieldName],
					$resourceParameters['fields'][$fieldName] = array('sqlId' => $fieldName, 'maxSize' => $maxSize, 'i18n' => true)
				);
			}
		if (isset($this->fieldsValidateLang))
			foreach ($this->fieldsValidateLang as $fieldName => $validateMethod)
			{
				if (!isset($resourceParameters['fields'][$fieldName]))
					$resourceParameters['fields'][$fieldName] = array('required' => false);
				$resourceParameters['fields'][$fieldName] = array_merge(
					$resourceParameters['fields'][$fieldName],
					$resourceParameters['fields'][$fieldName] = array(
						'sqlId' => $fieldName,
						'validateMethod' => (
								array_key_exists('validateMethod', $resourceParameters['fields'][$fieldName]) ?
								array_merge($resourceParameters['fields'][$fieldName]['validateMethod'], array($validateMethod)) :
								array($validateMethod)
							),
						'i18n' => true
					)
				);
			}

		if (isset($this->fieldsRequiredLang))
			foreach ($this->fieldsRequiredLang as $field)
			{
				if (!isset($resourceParameters['fields'][$field]))
					$resourceParameters['fields'][$field] = array();
				$resourceParameters['fields'][$field] = array_merge(
					$resourceParameters['fields'][$field],
					$resourceParameters['fields'][$field] = array('sqlId' => $field, 'required' => true, 'i18n' => true)
				);
			}

		if (isset($this->date_add))
			$resourceParameters['fields']['date_add']['setter'] = false;
		if (isset($this->date_upd))
			$resourceParameters['fields']['date_upd']['setter'] = false;

		foreach ($resourceParameters['fields'] as $key => &$resourceParametersField)
			if (!isset($resourceParametersField['sqlId']))
				$resourceParametersField['sqlId'] = $key;
		return $resourceParameters;
	}

	public function getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit)
	{
		$assoc = Shop::getAssoTables();
		
		if (array_key_exists($this->table ,$assoc))
		{
			$multi_shop_join = ' LEFT JOIN `'._DB_PREFIX_.$this->table.'_'.$assoc[$this->table]['type'].'` AS multi_shop_'.$this->table.' ON (main.'.$this->identifier.' = '.'multi_shop_'.$this->table.'.'.$this->identifier.')';
			$class_name = WebserviceRequest::$ws_current_classname;
			foreach ($class_name::$shopIDs as $id_shop)
				$OR[] = ' multi_shop_'.$this->table.'.id_shop = '.$id_shop.' ';
			$multi_shop_filter = ' AND ('.implode('OR', $OR).') ';
			$sql_filter = $multi_shop_filter.' '.$sql_filter;
			$sql_join = $multi_shop_join .' '. $sql_join;
		}
		$query = '
		SELECT DISTINCT main.`'.$this->identifier.'` FROM `'._DB_PREFIX_.$this->table.'` AS main
		'.$sql_join.'
		WHERE 1 '.$sql_filter.'
		'.($sql_sort != '' ? $sql_sort : '').'
		'.($sql_limit != '' ? $sql_limit : '').'
		';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
	}

	public function getFieldsRequiredDatabase($all = false)
	{
		return Db::getInstance()->ExecuteS('
		SELECT id_required_field, object_name, field_name
		FROM '._DB_PREFIX_.'required_field
		'.(!$all ? 'WHERE object_name = \''.pSQL(get_class($this)).'\'' : ''));
	}

	public function addFieldsRequiredDatabase($fields)
	{
		if (!is_array($fields))
			return false;

		if (!Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'required_field WHERE object_name = \''.pSQL(get_class($this)).'\''))
			return false;

		foreach ($fields AS $field)
			if (!Db::getInstance()->AutoExecute(_DB_PREFIX_.'required_field', array('object_name' => pSQL(get_class($this)), 'field_name' => pSQL($field)), 'INSERT'))
				return false;
		return true;
	}

	public function clearCache($all = false)
	{
		if ($all AND isset(self::$_cache[$this->table]))
			unset(self::$_cache[$this->table]);
		elseif ($this->id AND isset(self::$_cache[$this->table][(int)$this->id]))
			unset(self::$_cache[$this->table][(int)$this->id]);
	}

	/**
	 * Check if current object is associated to a shop
	 *
	 * @since 1.5.0
	 * @param int $id_shop
	 * @return bool
	 */
	public function isAssociatedToShop($id_shop = null)
	{
		if (is_null($id_shop))
			$id_shop = Shop::getCurrentShop();
			
		$sql = 'SELECT id_shop
				FROM `'.pSQL(_DB_PREFIX_.$this->table).'_shop`
				WHERE `'.$this->identifier.'`='.(int)$this->id.' AND id_shop='.(int)$id_shop;
		return (bool)Db::getInstance()->getValue($sql);
	}

	/**
	 * This function associate an item to its context
	 * 
	 * @param int|array $id_shops 
	 * @param string $context 
	 * @return boolean
	 */
	public function associateTo($id_shops, $context = 'shop')
	{
		if (!$this->id)
			return;
		$sql = '';
		if (!is_array($id_shops))
			$id_shops = array($id_shops);
		
		foreach ($id_shops as $id_shop)
		{
			if (!$this->isAssociatedToShop((int)$id_shop))
				$sql .= '('.(int)$this->id.','.(int)$id_shop.'),';
		}
		
		if (!empty($sql))
			return Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.$this->table.'_'.$context.'` (`'.$this->identifier.'`, `id_'.$context.'`) VALUES '.rtrim($sql,','));
		return true;
	}
	/**
	 * Check if current object is associated to a group shop
	 *
	 * @since 1.5.0
	 * @param int $id_group_shop
	 * @return bool
	 */
	public function isAssociatedToGroupShop($id_group_shop = null)
	{
		if (is_null($id_group_shop))
			$id_shop = Shop::getCurrentGroupShop();
		
		$sql = 'SELECT id_group_shop
				FROM `'.pSQL(_DB_PREFIX_.$this->table).'_group_shop`
				WHERE `'.$this->identifier.'`='.(int)$this->id.' AND id_group_shop='.(int)$id_group_shop;
		return (bool)Db::getInstance()->getValue($sql);
	}

	public function isLangMultishop()
	{
		return (int)$this->langMultiShop;
	}

	/**
	 * Delete images associated with the object
	 *
	 * @return bool success
	 */
	public function deleteImage()
	{
		if (!$this->id)
			return false;

		/* Deleting object images and thumbnails (cache) */
		if ($this->image_dir)
		{
			if (file_exists($this->image_dir.$this->id.'.'.$this->image_format) 
				&& !unlink($this->image_dir.$this->id.'.'.$this->image_format))
				return false;
		}
		if (file_exists(_PS_TMP_IMG_DIR_.$this->table.'_'.$this->id.'.'.$this->image_format) 
			&& !unlink(_PS_TMP_IMG_DIR_.$this->table.'_'.$this->id.'.'.$this->image_format))
			return false;
		if (file_exists(_PS_TMP_IMG_DIR_.$this->table.'_mini_'.$this->id.'.'.$this->image_format) 
			&& !unlink(_PS_TMP_IMG_DIR_.$this->table.'_mini_'.$this->id.'.'.$this->image_format))
			return false;
			
		$types = ImageType::getImagesTypes();
		foreach ($types AS $image_type)
			if (file_exists($this->image_dir.$this->id.'-'.stripslashes($image_type['name']).'.'.$this->image_format) 
			&& !unlink($this->image_dir.$this->id.'-'.stripslashes($image_type['name']).'.'.$this->image_format))
				return false;
		return true;
	}
}
