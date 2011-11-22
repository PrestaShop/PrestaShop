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
	protected $id_lang = null;

	protected $id_shop = null;

	private $getShopFromContext = true;

	/** @var string SQL Table name */
	protected $table = null;

	/** @var string SQL Table identifier */
	protected $identifier = null;

	/** @var array Required fields for admin panel forms */
 	protected $fieldsRequired = array();

	/** @var fieldsRequiredDatabase */
	protected static $fieldsRequiredDatabase = null;

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
	protected $image_dir = null;

	/** @var string file type of image files. Used for image deletion. */
	protected $image_format = 'jpg';

	/**
	 * Returns object validation rules (fields validity)
	 *
	 * @param string $className Child class name for static use (optional)
	 * @return array Validation rules (fields validity)
	 */
	public static function getValidationRules($className = __CLASS__)
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
	public function __construct($id = null, $id_lang = null, $id_shop = null)
	{
		if (!is_null($id_lang))
			$this->id_lang = (Language::getLanguage($id_lang) !== false) ? $id_lang : Configuration::get('PS_LANG_DEFAULT');

		if ($id_shop && $this->langMultiShop)
		{
			$this->id_shop = (int)$id_shop;
			$this->getShopFromContext = false;
		}

		if ($this->langMultiShop && !$this->id_shop)
			$this->id_shop = Context::getContext()->shop->getID(true);

	 	if (!Validate::isTableOrIdentifier($this->identifier) || !Validate::isTableOrIdentifier($this->table))
			throw new PrestashopException(Tools::displayError());

		if ($id)
		{
			// Load object from database if object id is present
			if (!isset(self::$_cache[$this->table][(int)$id][(int)$id_shop][(int)$id_lang]))
			{
				$sql = 'SELECT *
						FROM `'._DB_PREFIX_.$this->table.'` a '.
						($id_lang ? ('LEFT JOIN `'.pSQL(_DB_PREFIX_.$this->table).'_lang` b ON (a.`'.$this->identifier.'` = b.`'.$this->identifier).'` AND `id_lang` = '.(int)($id_lang).')' : '')
						.' WHERE 1 AND a.`'.$this->identifier.'` = '.(int)$id.
							(($this->id_shop AND $id_lang) ? ' AND b.id_shop = '.$this->id_shop : '');
				self::$_cache[$this->table][(int)($id)][(int)$id_shop][(int)$id_lang] = Db::getInstance()->getRow($sql);
			}

			$result = self::$_cache[$this->table][(int)$id][(int)$id_shop][(int)$id_lang];
			if ($result)
			{
				$this->id = (int)($id);
				foreach ($result AS $key => $value)
					if (key_exists($key, $this))
						$this->{$key} = $value;

				if (!$id_lang AND method_exists($this, 'getTranslationsFieldsChild'))
				{
					$sql = 'SELECT * FROM `'.pSQL(_DB_PREFIX_.$this->table).'_lang`
							WHERE `'.$this->identifier.'` = '.(int)$id
							.(($this->id_shop) ? ' AND `id_shop` = '.$this->id_shop : '');
					$result = Db::getInstance()->executeS($sql);
					if ($result)
						foreach ($result as $row)
							foreach ($row AS $key => $value)
							{
								if (key_exists($key, $this) AND $key != $this->identifier)
								{
									if (!is_array($this->{$key}))
										$this->{$key} = array();

									// @Todo: stripslashes() MUST BE removed in 1.4.6 and later, but is kept in 1.4.5 for a compatibility issue
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
			throw new PrestashopException('not table or identifier : '.$this->table);

		/* Hook */
		Hook::exec('actionObject'.get_class($this).'AddBefore');

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
					foreach (array_keys($field) AS $key)
					 	if (!Validate::isTableOrIdentifier($key))
			 				throw new PrestashopException('key '.$key.' is not table or identifier, ');
					$field[$this->identifier] = (int)$this->id;

					if (isset($assos[$this->table.'_lang']) && $assos[$this->table.'_lang']['type'] == 'fk_shop')
					{
						foreach ($shops as $id_shop)
						{
							$field['id_shop'] = (int)$id_shop;
							$result &= Db::getInstance()->AutoExecute(_DB_PREFIX_.$this->table.'_lang', $field, 'INSERT');
						}
					}
					else
						$result &= Db::getInstance()->AutoExecute(_DB_PREFIX_.$this->table.'_lang', $field, 'INSERT');
				}
		}

		if (!Shop::isFeatureActive())
		{
			if (isset($assos[$this->table]) && $assos[$this->table]['type'] == 'shop')
				$result &= $this->associateTo(Context::getContext()->shop->getID(true), 'shop');

			$assos = GroupShop::getAssoTables();
			if (isset($assos[$this->table]) && $assos[$this->table]['type'] == 'group_shop')
				$result &= $this->associateTo(Context::getContext()->shop->getGroupID(), 'group_shop');
		}

		/* Hook */
		Hook::exec('actionObject'.get_class($this).'AddAfter');

		return $result;
	}

	/**
	 * Update current object to database
	 *
	 * @return boolean Update result
	 */
	public function update($nullValues = false)
	{
	 	if (!Validate::isTableOrIdentifier($this->identifier) OR !Validate::isTableOrIdentifier($this->table))
			throw new PrestashopException('wrong identifier or table:'.$this->identifier.', table: '.$this->table);

		/* Hook */
		Hook::exec('actionObject'.get_class($this).'UpdateBefore');

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
					foreach (array_keys($field) as $key)
						if (!Validate::isTableOrIdentifier($key))
							throw new PrestashopException('key '.$key.' is not a valid table or identifier');

					// If this table is linked to multishop system, update / insert for all shops from context
					if ($this->langMultiShop)
					{
						$listShops = ($this->id_shop && !$this->getShopFromContext) ? array($this->id_shop) : Context::getContext()->shop->getListOfID();
						foreach ($listShops as $shop)
						{
							$field['id_shop'] = $shop;
							$where = pSQL($this->identifier).' = '.(int)$this->id
										.' AND id_lang = '.(int)$field['id_lang']
										.' AND id_shop = '.$field['id_shop'];

							if (Db::getInstance()->getValue('SELECT COUNT(*) FROM '.pSQL(_DB_PREFIX_.$this->table).'_lang WHERE '.$where))
								$result &= Db::getInstance()->AutoExecute(_DB_PREFIX_.$this->table.'_lang', $field, 'UPDATE', $where);
							else
								$result &= Db::getInstance()->AutoExecute(_DB_PREFIX_.$this->table.'_lang', $field, 'INSERT');
						}
					}
					// If this table is not linked to multishop system ...
					else
					{
						$where = pSQL($this->identifier).' = '.(int)$this->id
									.' AND id_lang = '.(int)$field['id_lang'];
						if (Db::getInstance()->getValue('SELECT COUNT(*) FROM '.pSQL(_DB_PREFIX_.$this->table).'_lang WHERE '.$where))
							$result &= Db::getInstance()->AutoExecute(_DB_PREFIX_.$this->table.'_lang', $field, 'UPDATE', $where);
						else
							$result &= Db::getInstance()->AutoExecute(_DB_PREFIX_.$this->table.'_lang', $field, 'INSERT');
					}
				}
			}
		}

		/* Hook */
		Hook::exec('actionObject'.get_class($this).'UpdateAfter');

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
			throw new PrestashopException('wrong identifier or table:'.$this->identifier.', table: '.$this->table);

		/* Hook */
		Hook::exec('actionObject'.get_class($this).'DeleteBefore');

		$this->clearCache();

		/* Database deletion */
		$result = Db::getInstance()->execute('DELETE FROM `'.pSQL(_DB_PREFIX_.$this->table).'` WHERE `'.pSQL($this->identifier).'` = '.(int)($this->id));
		if (!$result)
			return false;

		/* Database deletion for multilingual fields related to the object */
		if (method_exists($this, 'getTranslationsFieldsChild'))
			Db::getInstance()->execute('DELETE FROM `'.pSQL(_DB_PREFIX_.$this->table).'_lang` WHERE `'.pSQL($this->identifier).'` = '.(int)($this->id));

		$assos = Shop::getAssoTables();
		if (isset($assos[$this->table]) && $assos[$this->table]['type'] == 'shop')
			Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.$this->table.'_shop` WHERE `'.$this->identifier.'`='.(int)$this->id);

		$assos = GroupShop::getAssoTables();
		if (isset($assos[$this->table]) && $assos[$this->table]['type'] == 'group_shop')
			Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.$this->table.'_group_shop` WHERE `'.$this->identifier.'`='.(int)$this->id);
	
		/* Hook */
		Hook::exec('actionObject'.get_class($this).'DeleteAfter');

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
			throw new PrestashopException('selection is not an array, or identifier or table:'.$this->identifier.', table: '.$this->table);
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
			throw new PrestashopException('identifier or table:'.$this->identifier.', table: '.$this->table);

	 	/* Object must have a variable called 'active' */
	 	elseif (!key_exists('active', $this))
			throw new PrestashopException('property "active is missing in object '.get_class($this));

	 	/* Update active status on object */
	 	$this->active = (int)(!$this->active);

		/* Change status to active/inactive */
		return Db::getInstance()->execute('
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
	 		throw new PrestashopException('identifier is not table or identifier : '.$this->identifier);

		$fields = array();

		if($this->id_lang == NULL)
			foreach (Language::getLanguages(false) as $language)
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
		foreach ($fieldsArray as $k => $field)
		{
			$html = false;
			$fieldName = $field;
			if (is_array($field))
			{
				$fieldName = $k;
				$html = (isset($field['html'])) ? $field['html'] : false;
			}

			/* Check fields validity */
			if (!Validate::isTableOrIdentifier($fieldName))
				throw new PrestashopException('identifier is not table or identifier : '.$fieldName);

			/* Copy the field, or the default language field if it's both required and empty */
			if ((!$this->id_lang AND isset($this->{$fieldName}[$id_language]) AND !empty($this->{$fieldName}[$id_language]))
			OR ($this->id_lang AND isset($this->$fieldName) AND !empty($this->$fieldName)))
				$fields[$id_language][$fieldName] = $this->id_lang ? pSQL($this->$fieldName, $html) : pSQL($this->{$fieldName}[$id_language], $html);
			elseif (in_array($fieldName, $this->fieldsRequiredLang))
				$fields[$id_language][$fieldName] = $this->id_lang ? pSQL($this->$fieldName, $html) : pSQL($this->{$fieldName}[Configuration::get('PS_LANG_DEFAULT')], $html);
			else
				$fields[$id_language][$fieldName] = '';
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
				if ($die)
					throw new PrestashopException('property empty : '.get_class($this).'->'.$field);
				return $errorReturn ? get_class($this).' -> '.$field.' is empty' : false;
			}
		foreach ($this->fieldsSize as $field => $size)
			if (isset($this->{$field}) AND Tools::strlen($this->{$field}) > $size)
			{
				if ($die)
					throw new PrestashopException('fieldsize error : '.get_class($this).'->'.$field.' > '.$size);
				return $errorReturn ? get_class($this).' -> '.$field.' Length '.$size : false;
			}
		$validate = new Validate();
		foreach ($this->fieldsValidate as $field => $method)
			if (!method_exists($validate, $method))
				throw new PrestashopException('Validation function not found. '.$method);
			elseif (!empty($this->{$field}) AND !call_user_func(array('Validate', $method), $this->{$field}))
			{
				if ($die)
					throw new PrestashopException('Field not valid : '.get_class($this).'->'.$field.' = '.$this->{$field});
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
				if ($die)
					throw new PrestashopException('empty for default language : '.get_class($this).'->'.$fieldArray);
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
					if ($die)
						throw new PrestashopException('fieldsize error '.get_class($this).'->'.$fieldArray.' length of '.$size.' for language');
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
					throw new PrestashopException('Validation function not found for lang: '.$method);
				elseif (!empty($value) AND !call_user_func(array('Validate', $method), $value))
				{
					if ($die)
						throw new PrestashopException('Field not valid : '.get_class($this).'->'.$field.' = '.$this->{$field}. 'for language '.$k);
					return $errorReturn ? Tools::displayError('The following field is invalid according to the validate method ').'<b>'.$method.'</b>:<br/> ('. get_class($this).'->'.$fieldArray.' = '.$value.' '.Tools::displayError('for language').' '.$k : false;
				}
		}
		return true;
	}

	static public function displayFieldName($field, $className = __CLASS__, $htmlentities = true, Context $context = null)
	{
		global $_FIELDS;
		@include(_PS_TRANSLATIONS_DIR_.Context::getContext()->language->iso_code.'/fields.php');

		$key = $className.'_'.md5($field);
		return ((is_array($_FIELDS) AND array_key_exists($key, $_FIELDS)) ? ($htmlentities ? htmlentities($_FIELDS[$key], ENT_QUOTES, 'utf-8') : $_FIELDS[$key]) : $field);
	}

	/**
	* TODO: refactor rename all calls to this to validateController
	* @deprecated since 1.5 use validateController instead
	*/
	public function validateControler($htmlentities = true)
	{
		Tools::displayAsDeprecated();
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
				if (!Validate::$function($value) && (!empty($value) || in_array($field, $this->fieldsRequired)))
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
			$vars = get_class_vars($class_name);
			foreach ($vars['shopIDs'] as $id_shop)
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
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
	}

	public function getFieldsRequiredDatabase($all = false)
	{
		return Db::getInstance()->executeS('
		SELECT id_required_field, object_name, field_name
		FROM '._DB_PREFIX_.'required_field
		'.(!$all ? 'WHERE object_name = \''.pSQL(get_class($this)).'\'' : ''));
	}

	public function addFieldsRequiredDatabase($fields)
	{
		if (!is_array($fields))
			return false;

		if (!Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'required_field WHERE object_name = \''.pSQL(get_class($this)).'\''))
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
			$id_shop = Context::getContext()->shop->getID();

		$sql = 'SELECT id_shop
				FROM `'.pSQL(_DB_PREFIX_.$this->table).'_shop`
				WHERE `'.$this->identifier.'` = '.(int)$this->id.'
					AND id_shop = '.(int)$id_shop;
		return (bool)Db::getInstance()->getValue($sql);
	}

	/**
	 * This function associate an item to its context
	 *
	 * @param int|array $id_shops
	 * @param string $type
	 * @return boolean
	 */
	public function associateTo($id_shops, $type = 'shop')
	{
		if (!$this->id)
			return;
		$sql = '';
		if (!is_array($id_shops))
			$id_shops = array($id_shops);

		foreach ($id_shops as $id_shop)
		{
			if (($type == 'shop' && !$this->isAssociatedToShop($id_shop)) || ($type == 'group_shop' && !$this->isAssociatedToGroupShop($id_shop)))
				$sql .= '('.(int)$this->id.','.(int)$id_shop.'),';
		}

		if (!empty($sql))
			return Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.$this->table.'_'.$type.'` (`'.$this->identifier.'`, `id_'.$type.'`) VALUES '.rtrim($sql,','));
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
			$id_group_shop = Context::getContext()->shop->getGroupID();

		$sql = 'SELECT id_group_shop
				FROM `'.pSQL(_DB_PREFIX_.$this->table).'_group_shop`
				WHERE `'.$this->identifier.'`='.(int)$this->id.' AND id_group_shop='.(int)$id_group_shop;
		return (bool)Db::getInstance()->getValue($sql);
	}

	/**
	 * @since 1.5.0
	 */
	public function duplicateShops($id)
	{
		$asso = Shop::getAssoTables();
		if (!isset($asso[$this->table]) || $asso[$this->table]['type'] != 'shop')
			return false;

		$sql = 'SELECT id_shop
				FROM '._DB_PREFIX_.$this->table.'_shop
				WHERE '.$this->identifier.' = '.(int)$id;
		if ($results = Db::getInstance()->executeS($sql))
		{
			$ids = array();
			foreach ($results as $row)
				$ids[] = $row['id_shop'];
			return $this->associateTo($ids);
		}

		return false;
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

	/**
	* Specify if an ObjectModel is already in database
	*
	* @param $id_entity entity id
	* @return boolean
	*/
	public static function existsInDatabase($id_entity, $table)
	{
		$row = Db::getInstance()->getRow('
		SELECT `id_'.$table.'` as id
		FROM `'._DB_PREFIX_.$table.'` e
		WHERE e.`id_'.$table.'` = '.(int)($id_entity));

		return isset($row['id']);
	}

	/**
	 * This method is allow to know if a entity is currently used
	 * @since 1.5.0.1
	 * @param string $table name of table linked to entity
	 * @param bool $has_active_column true if the table has an active column
	 * @return bool
	 */
	public static function isCurrentlyUsed($table, $has_active_column = false)
	{
		$query = new DbQuery();
		$query->select('`id_'.pSQL($table).'`');
		$query->from(pSQL($table));
		if ($has_active_column)
			$query->where('`active` = 1');
		return (bool)Db::getInstance()->getValue($query);
	}

	/**
	 * Get object identifier name
	 *
	 * @since 1.5.0
	 * @return string
	 */
	public function getIdentifier()
	{
		return $this->identifier;
	}

	/**
	 * Get list of fields related to language to validate
	 *
	 * @since 1.5.0
	 * @return array
	 */
	public function getFieldsValidateLang()
	{
		return $this->fieldsValidateLang;
	}

	/**
	 * Fill an object with given data. Data must be an array with this syntax: array(objProperty => value, objProperty2 => value, etc.)
	 *
	 * @since 1.5.0
	 * @param array $data
	 * @param int $id_lang
	 */
	public function hydrate(array $data, $id_lang = null)
	{
		$this->id_lang = $id_lang;
		if (isset($data[$this->identifier]))
			$this->id = $data[$this->identifier];
		foreach ($data as $key => $value)
			if (array_key_exists($key, $this))
				$this->$key = $value;
	}

	/**
	 * Fill (hydrate) a list of objects in order to get a collection of these objects
	 *
	 * @since 1.5.0
	 * @param string $class Class of objects to hydrate
	 * @param array $datas List of data (multi-dimensional array)
	 * @param int $id_lang
	 * @return array
	 */
	public static function hydrateCollection($class, array $datas, $id_lang = null)
	{
		if (!class_exists($class))
			throw new PrestashopException("Class '$class' not found");

		$collection = array();
		$group_keys = array();
		$rows = array();
		$identifier = $fieldsValidateLang = null;
		if ($datas)
		{
			// Get identifier and lang fields
			$obj = new $class;
			if (!$obj instanceof ObjectModel)
				throw new PrestashopException("Class '$class' must be an instance of class ObjectModel");
			$identifier = $obj->getIdentifier();
			$fieldsValidateLang = $obj->getFieldsValidateLang();

			// Check primary key
			if (!array_key_exists($identifier, $datas[0]))
				throw new PrestashopException("Identifier '$identifier' not found for class '$class'");

			foreach ($datas as $row)
			{
				// Get object common properties
				$id = $row[$identifier];
				if (!isset($rows[$id]))
					$rows[$id] = $row;

				// Get object lang properties
				if (isset($row['id_lang']) && !$id_lang)
				{
					foreach ($fieldsValidateLang as $field => $validator)
					{
						if (!$id_lang)
						{
							if (!is_array($rows[$id][$field]))
								$rows[$id][$field] = array();
							$rows[$id][$field][$row['id_lang']] = $row[$field];
						}
					}
				}
			}
		}

		// Hydrate objects
		foreach ($rows as $row)
		{
			$obj = new $class;
			$obj->hydrate($row, $id_lang);
			$collection[] = $obj;
		}
		return $collection;
	}
}
