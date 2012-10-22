<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7499 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

abstract class ObjectModelCore
{
	/**
	 * List of field types
	 */
	const TYPE_INT = 1;
	const TYPE_BOOL = 2;
	const TYPE_STRING = 3;
	const TYPE_FLOAT = 4;
	const TYPE_DATE = 5;
	const TYPE_HTML = 6;
	const TYPE_NOTHING = 7;

	/**
	 * List of data to format
	 */
	const FORMAT_COMMON = 1;
	const FORMAT_LANG = 2;
	const FORMAT_SHOP = 3;

	/**
	 * List of association types
	 */
	const HAS_ONE = 1;
	const HAS_MANY = 2;

	/** @var integer Object id */
	public $id;

	/** @var integer lang id */
	protected $id_lang = null;

	protected $id_shop = null;

	public $id_shop_list = null;

	protected $get_shop_from_context = true;

	protected static $fieldsRequiredDatabase = null;

	/**
	 * @deprecated 1.5.0 This property shouldn't be overloaded anymore in class, use static $definition['table'] property instead
	 */
	protected $table;

	/**
	 * @deprecated 1.5.0 This property shouldn't be overloaded anymore in class, use static $definition['primary'] property instead
	 */
	protected $identifier;

	/**
	 * @deprecated 1.5.0 This property shouldn't be overloaded anymore in class, use static $definition['fields'] property instead
	 */
 	protected $fieldsRequired = array();

	/**
	 * @deprecated 1.5.0 This property shouldn't be overloaded anymore in class, use static $definition['fields'] property instead
	 */
 	protected $fieldsSize = array();

	/**
	 * @deprecated 1.5.0 This property shouldn't be overloaded anymore in class, use static $definition['fields'] property instead
	 */
 	protected $fieldsValidate = array();

	/**
	 * @deprecated 1.5.0 This property shouldn't be overloaded anymore in class, use static $definition['fields'] property instead
	 */
 	protected $fieldsRequiredLang = array();

	/**
	 * @deprecated 1.5.0 This property shouldn't be overloaded anymore in class, use static $definition['fields'] property instead
	 */
 	protected $fieldsSizeLang = array();

	/**
	 * @deprecated 1.5.0 This property shouldn't be overloaded anymore in class, use static $definition['fields'] property instead
	 */
 	protected $fieldsValidateLang = array();

	/**
	 * @deprecated 1.5.0
	 */
 	protected $tables = array();

 	/** @var array tables */
 	protected $webserviceParameters = array();

	/** @var  string path to image directory. Used for image deletion. */
	protected $image_dir = null;

	/** @var string file type of image files. Used for image deletion. */
	protected $image_format = 'jpg';

	/**
	 * @var array Contain object definition
	 * @since 1.5.0
	 */
	public static $definition = array();

	/**
	 * @var array Contain current object definition
	 */
	protected $def;

	/**
	 * @var array List of specific fields to update (all fields if null)
	 */
	protected $update_fields = null;
	
	/**
	 * @var Db An instance of the db in order to avoid calling Db::getInstance() thousands of time
	 */
	protected static $db = false;

	/**
	 * Returns object validation rules (fields validity)
	 *
	 * @param string $class Child class name for static use (optional)
	 * @return array Validation rules (fields validity)
	 */
	public static function getValidationRules($class = __CLASS__)
	{
		$object = new $class();
		return array(
			'required' => $object->fieldsRequired,
			'size' => $object->fieldsSize,
			'validate' => $object->fieldsValidate,
			'requiredLang' => $object->fieldsRequiredLang,
			'sizeLang' => $object->fieldsSizeLang,
			'validateLang' => $object->fieldsValidateLang,
		);
	}

	/**
	 * Build object
	 *
	 * @param int $id Existing object id in order to load object (optional)
	 * @param int $id_lang Required if object is multilingual (optional)
	 * @param int $id_shop ID shop for objects with multishop on langs
	 */
	public function __construct($id = null, $id_lang = null, $id_shop = null)
	{
		if (!ObjectModel::$db)
			ObjectModel::$db = Db::getInstance();
	
		$this->def = self::getDefinition($this);
		$this->setDefinitionRetrocompatibility();

		if ($id_lang !== null)
			$this->id_lang = (Language::getLanguage($id_lang) !== false) ? $id_lang : Configuration::get('PS_LANG_DEFAULT');

		if ($id_shop && $this->isMultishop())
		{
			$this->id_shop = (int)$id_shop;
			$this->get_shop_from_context = false;
		}

		if ($this->isMultishop() && !$this->id_shop)
			$this->id_shop = Context::getContext()->shop->id;

	 	if (!Validate::isTableOrIdentifier($this->def['primary']) || !Validate::isTableOrIdentifier($this->def['table']))
			throw new PrestaShopException('Identifier or table format not valid for class '.get_class($this));

		if ($id)
		{
			// Load object from database if object id is present
			$cache_id = 'objectmodel_'.$this->def['table'].'_'.(int)$id.'_'.(int)$id_shop.'_'.(int)$id_lang;
			if (!Cache::isStored($cache_id))
			{
				$sql = new DbQuery();
				$sql->from($this->def['table'], 'a');
				$sql->where('a.'.$this->def['primary'].' = '.(int)$id);

				// Get lang informations
				if ($id_lang)
				{
					$sql->leftJoin($this->def['table'].'_lang', 'b', 'a.'.$this->def['primary'].' = b.'.$this->def['primary'].' AND b.id_lang = '.(int)$id_lang);
					if ($this->id_shop && !empty($this->def['multilang_shop']))
						$sql->where('b.id_shop = '.$this->id_shop);
				}

				// Get shop informations
				if (Shop::isTableAssociated($this->def['table']))
					$sql->leftJoin($this->def['table'].'_shop', 'c', 'a.'.$this->def['primary'].' = c.'.$this->def['primary'].' AND c.id_shop = '.(int)$this->id_shop);
				if ($row = ObjectModel::$db->getRow($sql))
					Cache::store($cache_id, $row);
			}

			$result = Cache::retrieve($cache_id);
			if ($result)
			{
				$this->id = (int)$id;
				foreach ($result as $key => $value)
					if (array_key_exists($key, $this))
						$this->{$key} = $value;

				if (!$id_lang && isset($this->def['multilang']) && $this->def['multilang'])
				{
					$sql = 'SELECT * FROM `'.pSQL(_DB_PREFIX_.$this->def['table']).'_lang`
							WHERE `'.$this->def['primary'].'` = '.(int)$id
							.(($this->id_shop && $this->isLangMultishop()) ? ' AND `id_shop` = '.$this->id_shop : '');
					$result = ObjectModel::$db->executeS($sql);
					if ($result)
						foreach ($result as $row)
							foreach ($row as $key => $value)
							{
								if (array_key_exists($key, $this) && $key != $this->def['primary'])
								{
									if (!is_array($this->{$key}))
										$this->{$key} = array();
									$this->{$key}[$row['id_lang']] = $value;
								}
							}
				}
			}
		}

		if (!is_array(self::$fieldsRequiredDatabase))
		{
			$fields = $this->getfieldsRequiredDatabase(true);
			if ($fields)
				foreach ($fields as $row)
					self::$fieldsRequiredDatabase[$row['object_name']][(int)$row['id_required_field']] = pSQL($row['field_name']);
			else
				self::$fieldsRequiredDatabase = array();
		}
	}

	/**
	 * Prepare fields for ObjectModel class (add, update)
	 * All fields are verified (pSQL, intval...)
	 *
	 * @return array All object fields
	 */
	public function getFields()
	{
		$this->validateFields();
		$fields = $this->formatFields(self::FORMAT_COMMON);

		// For retro compatibility
		if (Shop::isTableAssociated($this->def['table']))
			$fields = array_merge($fields, $this->getFieldsShop());

		// Ensure that we get something to insert
		if (!$fields && isset($this->id) && Validate::isUnsignedId($this->id))
			$fields[$this->def['primary']] = $this->id;
		return $fields;
	}

	/**
	 * Prepare fields for multishop
	 * Fields are not validated here, we considere they are already validated in getFields() method, this
	 * not the best solution but this is the only one possible for retro compatibility.
	 *
	 * @since 1.5.0
	 * @return array All object fields
	 */
	public function getFieldsShop()
	{
		$fields = $this->formatFields(self::FORMAT_SHOP);
		if (!$fields && isset($this->id) && Validate::isUnsignedId($this->id))
			$fields[$this->def['primary']] = $this->id;
		return $fields;
	}

	/**
	 * Prepare multilang fields
	 *
	 * @since 1.5.0
	 * @return array
	 */
	public function getFieldsLang()
	{
		// Retrocompatibility
		if (method_exists($this, 'getTranslationsFieldsChild'))
			return $this->getTranslationsFieldsChild();

		$this->validateFieldsLang();
		$is_lang_multishop = $this->isLangMultishop();

		$fields = array();
		if ($this->id_lang === null)
			foreach (Language::getLanguages(false) as $language)
			{
				$fields[$language['id_lang']] = $this->formatFields(self::FORMAT_LANG, $language['id_lang']);
				$fields[$language['id_lang']]['id_lang'] = $language['id_lang'];
				if ($this->id_shop && $is_lang_multishop)
					$fields[$language['id_lang']]['id_shop'] = (int)$this->id_shop;
			}
		else
		{
			$fields = array($this->id_lang => $this->formatFields(self::FORMAT_LANG, $this->id_lang));
			$fields[$this->id_lang]['id_lang'] = $this->id_lang;
			if ($this->id_shop && $is_lang_multishop)
				$fields[$this->id_lang]['id_shop'] = (int)$this->id_shop;
		}

		return $fields;
	}

	/**
	 * @since 1.5.0
	 * @param int $type FORMAT_COMMON or FORMAT_LANG or FORMAT_SHOP
	 * @param int $id_lang If this parameter is given, only take lang fields
	 * @return array
	 */
	protected function formatFields($type, $id_lang = null)
	{
		$fields = array();

		// Set primary key in fields
		if (isset($this->id))
			$fields[$this->def['primary']] = $this->id;

		foreach ($this->def['fields'] as $field => $data)
		{
			// Only get fields we need for the type
			// E.g. if only lang fields are filtered, ignore fields without lang => true
			if (($type == self::FORMAT_LANG && empty($data['lang']))
				|| ($type == self::FORMAT_SHOP && empty($data['shop']))
				|| ($type == self::FORMAT_COMMON && (!empty($data['shop']) || !empty($data['lang']))))
				continue;

			if (is_array($this->update_fields))
				if ((!empty($data['lang']) || !empty($data['shop'])) && (empty($this->update_fields[$field]) || ($type == self::FORMAT_LANG && empty($this->update_fields[$field][$id_lang]))))
					continue;

			// Get field value, if value is multilang and field is empty, use value from default lang
			$value = $this->$field;
			if ($type == self::FORMAT_LANG && $id_lang && is_array($value))
			{
				if (!empty($value[$id_lang]))
					$value = $value[$id_lang];
				else if (!empty($data['required']))
					$value = $value[Configuration::get('PS_LANG_DEFAULT')];
				else
					$value = '';
			}

			// Format field value
			$fields[$field] = ObjectModel::formatValue($value, $data['type']);
		}

		return $fields;
	}

	/**
	 * Format a data
	 *
	 * @param mixed $value
	 * @param int $type
	 */
	public static function formatValue($value, $type, $with_quotes = false)
	{
		switch ($type)
		{
			case self::TYPE_INT :
				return (int)$value;

			case self::TYPE_BOOL :
				return (int)$value;

			case self::TYPE_FLOAT :
				return (float)$value;

			case self::TYPE_DATE :
				if (!$value)
					return '0000-00-00';

				if ($with_quotes)
					return '\''.pSQL($value).'\'';
				return pSQL($value);

			case self::TYPE_HTML :
				if ($with_quotes)
					return '\''.pSQL($value, true).'\'';
				return pSQL($value, true);

			case self::TYPE_NOTHING :
				return $value;

			case self::TYPE_STRING :
			default :
				if ($with_quotes)
					return '\''.pSQL($value).'\'';
				return pSQL($value);
		}
	}

	/**
	 * Save current object to database (add or update)
	 *
	 * @param bool $null_values
	 * @param bool $autodate
	 * @return boolean Insertion result
	 */
	public function save($null_values = false, $autodate = true)
	{
		return (int)$this->id > 0 ? $this->update($null_values) : $this->add($autodate, $null_values);
	}

	/**
	 * Add current object to database
	 *
	 * @param bool $null_values
	 * @param bool $autodate
	 * @return boolean Insertion result
	 */
	public function add($autodate = true, $null_values = false)
	{
		if (!ObjectModel::$db)
			ObjectModel::$db = Db::getInstance();

		// @hook actionObject*AddBefore
		Hook::exec('actionObjectAddBefore', array('object' => $this));
		Hook::exec('actionObject'.get_class($this).'AddBefore', array('object' => $this));

		// Automatically fill dates
		if ($autodate && property_exists($this, 'date_add'))
			$this->date_add = date('Y-m-d H:i:s');
		if ($autodate && property_exists($this, 'date_upd'))
			$this->date_upd = date('Y-m-d H:i:s');

			
		if (Shop::isTableAssociated($this->def['table']))
		{
			$id_shop_list = Shop::getContextListShopID();
			if (count($this->id_shop_list) > 0)
				$id_shop_list = $this->id_shop_list;
		}
		
		// Database insertion
		if (isset($this->id) && !Tools::getValue('forceIDs'))
			unset($this->id);
		if (Shop::checkIdShopDefault($this->def['table']))
			$this->id_shop_default = min($id_shop_list);
		if (!$result = ObjectModel::$db->insert($this->def['table'], $this->getFields(), $null_values))
			return false;

		// Get object id in database
		$this->id = ObjectModel::$db->Insert_ID();

		// Database insertion for multishop fields related to the object
		if (Shop::isTableAssociated($this->def['table']))
		{
			$fields = $this->getFieldsShop();
			$fields[$this->def['primary']] = (int)$this->id;

			foreach ($id_shop_list as $id_shop)
			{
				$fields['id_shop'] = (int)$id_shop;
				$result &= ObjectModel::$db->insert($this->def['table'].'_shop', $fields, $null_values);
			}
		}

		if (!$result)
			return false;

		// Database insertion for multilingual fields related to the object
		if (!empty($this->def['multilang']))
		{
			$fields = $this->getFieldsLang();
			if ($fields && is_array($fields))
			{
				$shops = Shop::getCompleteListOfShopsID();
				$asso = Shop::getAssoTable($this->def['table'].'_lang');
				foreach ($fields as $field)
				{
					foreach (array_keys($field) as $key)
						if (!Validate::isTableOrIdentifier($key))
							throw new PrestaShopException('key '.$key.' is not table or identifier, ');
					$field[$this->def['primary']] = (int)$this->id;

					if ($asso !== false && $asso['type'] == 'fk_shop')
					{
						foreach ($shops as $id_shop)
						{
							$field['id_shop'] = (int)$id_shop;
							$result &= ObjectModel::$db->insert($this->def['table'].'_lang', $field);
						}
					}
					else
						$result &= ObjectModel::$db->insert($this->def['table'].'_lang', $field);
				}
			}
		}

		// @hook actionObject*AddAfter
		Hook::exec('actionObjectAddAfter', array('object' => $this));
		Hook::exec('actionObject'.get_class($this).'AddAfter', array('object' => $this));

		return $result;
	}

	/**
	 * Update current object to database
	 *
	 * @param bool $null_values
	 * @return boolean Update result
	 */
	public function update($null_values = false)
	{
		if (!ObjectModel::$db)
			ObjectModel::$db = Db::getInstance();

		// @hook actionObject*UpdateBefore
		Hook::exec('actionObjectUpdateBefore', array('object' => $this));
		Hook::exec('actionObject'.get_class($this).'UpdateBefore', array('object' => $this));

		$this->clearCache();

		// Automatically fill dates
		if (array_key_exists('date_upd', $this))
			$this->date_upd = date('Y-m-d H:i:s');
			
		$id_shop_list = Shop::getContextListShopID();
		if (count($this->id_shop_list) > 0)
			$id_shop_list = $this->id_shop_list;

		if (Shop::checkIdShopDefault($this->def['table']) && !$this->id_shop_default)
			$this->id_shop_default = min($id_shop_list);
		// Database update
		if (!$result = ObjectModel::$db->update($this->def['table'], $this->getFields(), '`'.pSQL($this->def['primary']).'` = '.(int)$this->id, 0, $null_values))
			return false;

		// Database insertion for multishop fields related to the object
		if (Shop::isTableAssociated($this->def['table']))
		{
			$fields = $this->getFieldsShop();
			$fields[$this->def['primary']] = (int)$this->id;
			if (is_array($this->update_fields))
			{
				$update_fields = $this->update_fields;
				$this->update_fields = null;
				$all_fields = $this->getFieldsShop();
				$all_fields[$this->def['primary']] = (int)$this->id;
				$this->update_fields = $update_fields;
			}
			else
				$all_fields = $fields;


			foreach ($id_shop_list as $id_shop)
			{
				$fields['id_shop'] = (int)$id_shop;
				$all_fields['id_shop'] = (int)$id_shop;
				$where = $this->def['primary'].' = '.(int)$this->id.' AND id_shop = '.(int)$id_shop;

				// A little explanation of what we do here : we want to create multishop entry when update is called, but
				// only if we are in a shop context (if we are in all context, we just want to update entries that alread exists)
				$shop_exists = ObjectModel::$db->getValue('SELECT '.$this->def['primary'].' FROM '._DB_PREFIX_.$this->def['table'].'_shop WHERE '.$where);
				if ($shop_exists)
					$result &= ObjectModel::$db->update($this->def['table'].'_shop', $fields, $where, 0, $null_values);
				else if (Shop::getContext() == Shop::CONTEXT_SHOP)
					$result &= ObjectModel::$db->insert($this->def['table'].'_shop', $all_fields, $null_values);
			}
		}

		// Database update for multilingual fields related to the object
		if (isset($this->def['multilang']) && $this->def['multilang'])
		{
			$fields = $this->getFieldsLang();
			if (is_array($fields))
			{
				foreach ($fields as $field)
				{
					foreach (array_keys($field) as $key)
						if (!Validate::isTableOrIdentifier($key))
							throw new PrestaShopException('key '.$key.' is not a valid table or identifier');

					// If this table is linked to multishop system, update / insert for all shops from context
					if ($this->isLangMultishop())
					{
						$id_shop_list = Shop::getContextListShopID();
						if (count($this->id_shop_list) > 0)
							$id_shop_list = $this->id_shop_list;
						foreach ($id_shop_list as $id_shop)
						{
							$field['id_shop'] = (int)$id_shop;
							$where = pSQL($this->def['primary']).' = '.(int)$this->id
										.' AND id_lang = '.(int)$field['id_lang']
										.' AND id_shop = '.(int)$id_shop;

							if (ObjectModel::$db->getValue('SELECT COUNT(*) FROM '.pSQL(_DB_PREFIX_.$this->def['table']).'_lang WHERE '.$where))
								$result &= ObjectModel::$db->update($this->def['table'].'_lang', $field, $where);
							else
								$result &= ObjectModel::$db->insert($this->def['table'].'_lang', $field);
						}
					}
					// If this table is not linked to multishop system ...
					else
					{
						$where = pSQL($this->def['primary']).' = '.(int)$this->id
									.' AND id_lang = '.(int)$field['id_lang'];
						if (Db::getInstance()->getValue('SELECT COUNT(*) FROM '.pSQL(_DB_PREFIX_.$this->def['table']).'_lang WHERE '.$where))
							$result &= ObjectModel::$db->update($this->def['table'].'_lang', $field, $where);
						else
							$result &= ObjectModel::$db->insert($this->def['table'].'_lang', $field, $null_values);
					}
				}
			}
		}

		// @hook actionObject*UpdateAfter
		Hook::exec('actionObjectUpdateAfter', array('object' => $this));
		Hook::exec('actionObject'.get_class($this).'UpdateAfter', array('object' => $this));

		return $result;
	}

	/**
	 * Delete current object from database
	 *
	 * @return boolean Deletion result
	 */
	public function delete()
	{
		if (!ObjectModel::$db)
			ObjectModel::$db = Db::getInstance();

		// @hook actionObject*DeleteBefore
		Hook::exec('actionObjectDeleteBefore', array('object' => $this));
		Hook::exec('actionObject'.get_class($this).'DeleteBefore', array('object' => $this));

		$this->clearCache();
		$result = true;
		// Remove association to multishop table
		if (Shop::isTableAssociated($this->def['table']))
		{
			$id_shop_list = Shop::getContextListShopID();
			if (count($this->id_shop_list))
				$id_shop_list = $this->id_shop_list;

			$result &= ObjectModel::$db->delete($this->def['table'].'_shop', '`'.$this->def['primary'].'`='.(int)$this->id.' AND id_shop IN ('.implode(', ', $id_shop_list).')');
		}

		// Database deletion
		$has_multishop_entries = $this->hasMultishopEntries();
		if ($result && !$has_multishop_entries)
			$result &= ObjectModel::$db->delete($this->def['table'], '`'.pSQL($this->def['primary']).'` = '.(int)$this->id);

		if (!$result)
			return false;

		// Database deletion for multilingual fields related to the object
		if (!empty($this->def['multilang']) && !$has_multishop_entries)
			$result &= ObjectModel::$db->delete($this->def['table'].'_lang', '`'.pSQL($this->def['primary']).'` = '.(int)$this->id);

		// @hook actionObject*DeleteAfter
		Hook::exec('actionObjectDeleteAfter', array('object' => $this));
		Hook::exec('actionObject'.get_class($this).'DeleteAfter', array('object' => $this));

		return $result;
	}

	/**
	 * Delete several objects from database
	 *
	 * @param array $selection
	 * @return bool Deletion result
	 */
	public function deleteSelection($selection)
	{
		$result = true;
		foreach ($selection as $id)
		{
			$this->id = (int)$id;
			$result = $result && $this->delete();
		}
		return $result;
	}

	/**
	 * Toggle object status in database
	 *
	 * @return boolean Update result
	 */
	public function toggleStatus()
	{
	 	// Object must have a variable called 'active'
	 	if (!array_key_exists('active', $this))
			throw new PrestaShopException('property "active" is missing in object '.get_class($this));

	 	// Update active status on object
	 	$this->active = !(int)$this->active;

		// Change status to active/inactive
		return $this->update(false);
	}

	/**
	 * @deprecated 1.5.0 (use getFieldsLang())
	 */
	protected function getTranslationsFields($fields_array)
	{
		$fields = array();

		if ($this->id_lang == null)
			foreach (Language::getLanguages(false) as $language)
				$this->makeTranslationFields($fields, $fields_array, $language['id_lang']);
		else
			$this->makeTranslationFields($fields, $fields_array, $this->id_lang);

		return $fields;
	}

	/**
	 * @deprecated 1.5.0
	 */
	protected function makeTranslationFields(&$fields, &$fields_array, $id_language)
	{
		$fields[$id_language]['id_lang'] = $id_language;
		$fields[$id_language][$this->def['primary']] = (int)$this->id;
		if ($this->id_shop && $this->isLangMultishop())
			$fields[$id_language]['id_shop'] = (int)$this->id_shop;
		foreach ($fields_array as $k => $field)
		{
			$html = false;
			$field_name = $field;
			if (is_array($field))
			{
				$field_name = $k;
				$html = (isset($field['html'])) ? $field['html'] : false;
			}

			/* Check fields validity */
			if (!Validate::isTableOrIdentifier($field_name))
				throw new PrestaShopException('identifier is not table or identifier : '.$field_name);

			// Copy the field, or the default language field if it's both required and empty
			if ((!$this->id_lang && isset($this->{$field_name}[$id_language]) && !empty($this->{$field_name}[$id_language]))
			|| ($this->id_lang && isset($this->$field_name) && !empty($this->$field_name)))
				$fields[$id_language][$field_name] = $this->id_lang ? pSQL($this->$field_name, $html) : pSQL($this->{$field_name}[$id_language], $html);
			else if (in_array($field_name, $this->fieldsRequiredLang))
				$fields[$id_language][$field_name] = pSQL($this->id_lang ? $this->$field_name : $this->{$field_name}[Configuration::get('PS_LANG_DEFAULT')], $html);
			else
				$fields[$id_language][$field_name] = '';
		}
	}

	/**
	 * Check for fields validity before database interaction
	 *
	 * @param bool $die
	 * @param bool $error_return
	 * @return bool|string
	 */
	public function validateFields($die = true, $error_return = false)
	{
		foreach ($this->def['fields'] as $field => $data)
		{
			if (!empty($data['lang']))
				continue;

			if (is_array($this->update_fields) && empty($this->update_fields[$field]))
				continue;

			$message = $this->validateField($field, $this->$field);
			if ($message !== true)
			{
				if ($die)
					throw new PrestaShopException($message);
				return $error_return ? $message : false;
			}
		}

		return true;
	}

	/**
	 * Check for multilingual fields validity before database interaction
	 *
	 * @param bool $die
	 * @param bool $error_return
	 * @return bool|string
	 */
	public function validateFieldsLang($die = true, $error_return = false)
	{
		foreach ($this->def['fields'] as $field => $data)
		{
			if (empty($data['lang']))
				continue;

			$values = $this->$field;
			if (!is_array($values))
				$values = array($this->id_lang => $values);

			foreach ($values as $id_lang => $value)
			{
				if (is_array($this->update_fields) && empty($this->update_fields[$field][$id_lang]))
					continue;

				$message = $this->validateField($field, $value, $id_lang);
				if ($message !== true)
				{
					if ($die)
						throw new PrestaShopException($message);
					return $error_return ? $message : false;
				}
			}
		}

		return true;
	}

	/**
	 * Validate a single field
	 *
	 * @since 1.5.0
	 * @param string $field Field name
	 * @param mixed $value Field value
	 * @param int $id_lang
	 * @return bool|string
	 */
	public function validateField($field, $value, $id_lang = null)
	{
		$data = $this->def['fields'][$field];

		// Check if field is required
		$required_fields = (isset(self::$fieldsRequiredDatabase[get_class($this)])) ? self::$fieldsRequiredDatabase[get_class($this)] : array();
		if (!$id_lang || $id_lang == Configuration::get('PS_LANG_DEFAULT'))
			if (!empty($data['required']) || in_array($field, $required_fields))
				if (Tools::isEmpty($value))
					return 'Property '.get_class($this).'->'.$field.' is empty';

		// Default value
		if (!$value && !empty($data['default']))
		{
			$value = $data['default'];
			$this->$field = $value;
		}

		// Check field values
		if (!empty($data['values']) && is_array($data['values']) && !in_array($value, $data['values']))
			return 'Property '.get_class($this).'->'.$field.' has bad value (allowed values are: '.implode(', ', $data['values']).')';

		// Check field size
		if (!empty($data['size']))
		{
			$size = $data['size'];
			if (!is_array($data['size']))
				$size = array('min' => 0, 'max' => $data['size']);

			$length = Tools::strlen($value);
			if ($length < $size['min'] || $length > $size['max'])
				return 'Property '.get_class($this).'->'.$field.' length ('.$length.') must be between '.$size['min'].' and '.$size['max'];
		}

		// Check field validator
		if (!empty($data['validate']))
		{
			if (!method_exists('Validate', $data['validate']))
				throw new PrestaShopException('Validation function not found. '.$data['validate']);

			if (!empty($value) && !call_user_func(array('Validate', $data['validate']), $value))
				return 'Property '.get_class($this).'->'.$field.' is not valid';
		}

		return true;
	}

	public static function displayFieldName($field, $class = __CLASS__, $htmlentities = true, Context $context = null)
	{
		global $_FIELDS;

		if (file_exists(_PS_TRANSLATIONS_DIR_.Context::getContext()->language->iso_code.'/fields.php'))
			include(_PS_TRANSLATIONS_DIR_.Context::getContext()->language->iso_code.'/fields.php');

		$key = $class.'_'.md5($field);
		return ((is_array($_FIELDS) && array_key_exists($key, $_FIELDS)) ? ($htmlentities ? htmlentities($_FIELDS[$key], ENT_QUOTES, 'utf-8') : $_FIELDS[$key]) : $field);
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
		$required_fields_database = (isset(self::$fieldsRequiredDatabase[get_class($this)])) ? self::$fieldsRequiredDatabase[get_class($this)] : array();
		foreach ($this->def['fields'] as $field => $data)
		{
			// Check if field is required by user
			if (in_array($field, $required_fields_database))
				$data['required'] = true;
			
			// Checking for required fields
			if (isset($data['required']) && $data['required'] && ($value = Tools::getValue($field, $this->{$field})) == false && (string)$value != '0')
				if (!$this->id || $field != 'passwd')
					$errors[] = '<b>'.self::displayFieldName($field, get_class($this), $htmlentities).'</b> '.Tools::displayError('is required.');

			// Checking for maximum fields sizes
			if (isset($data['size']) && ($value = Tools::getValue($field, $this->{$field})) && Tools::strlen($value) > $data['size'])
				$errors[] = sprintf(
					Tools::displayError('%1$s is too long. Maximum length: %2$d'),
					self::displayFieldName($field, get_class($this), $htmlentities),
					$data['size']
				);

			// Checking for fields validity
			// Hack for postcode required for country which does not have postcodes
			if (($value = Tools::getValue($field, $this->{$field})) || ($field == 'postcode' && $value == '0'))
			{
				if (isset($data['validate']) && !Validate::$data['validate']($value) && (!empty($value) || $data['required']))
					$errors[] = '<b>'.self::displayFieldName($field, get_class($this), $htmlentities).'</b> '.Tools::displayError('is invalid.');
				else
				{
					if (isset($data['copy_post']) && !$data['copy_post'])
						continue;
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

	public function getWebserviceParameters($ws_params_attribute_name = null)
	{
		$default_resource_parameters = array(
			'objectSqlId' => $this->def['primary'],
			'retrieveData' => array(
				'className' => get_class($this),
				'retrieveMethod' => 'getWebserviceObjectList',
				'params' => array(),
				'table' => $this->def['table'],
			),
			'fields' => array(
				'id' => array('sqlId' => $this->def['primary'], 'i18n' => false),
			),
		);

		if ($ws_params_attribute_name === null)
			$ws_params_attribute_name = 'webserviceParameters';

		if (!isset($this->{$ws_params_attribute_name}['objectNodeName']))
			$default_resource_parameters['objectNodeName'] = $this->def['table'];
		if (!isset($this->{$ws_params_attribute_name}['objectsNodeName']))
			$default_resource_parameters['objectsNodeName'] = $this->def['table'].'s';

		if (isset($this->{$ws_params_attribute_name}['associations']))
			foreach ($this->{$ws_params_attribute_name}['associations'] as $assoc_name => &$association)
			{
				if (!array_key_exists('setter', $association) || (isset($association['setter']) && !$association['setter']))
					$association['setter'] = Tools::toCamelCase('set_ws_'.$assoc_name);
				if (!array_key_exists('getter', $association))
					$association['getter'] = Tools::toCamelCase('get_ws_'.$assoc_name);
			}

		if (isset($this->{$ws_params_attribute_name}['retrieveData']) && isset($this->{$ws_params_attribute_name}['retrieveData']['retrieveMethod']))
			unset($default_resource_parameters['retrieveData']['retrieveMethod']);

		$resource_parameters = array_merge_recursive($default_resource_parameters, $this->{$ws_params_attribute_name});

		$required_fields = (isset(self::$fieldsRequiredDatabase[get_class($this)]) ? self::$fieldsRequiredDatabase[get_class($this)] : array());
		foreach ($this->def['fields'] as $field_name => $details)
		{
			if (!isset($resource_parameters['fields'][$field_name]))
				$resource_parameters['fields'][$field_name] = array();
			$current_field = array();
			$current_field['sqlId'] = $field_name;
			if (isset($details['size']))
				$current_field['maxSize'] = $details['size'];
			if (isset($details['lang']))
				$current_field['i18n'] = $details['lang'];
			else
				$current_field['i18n'] = false;
			if ((isset($details['required']) && $details['required'] === true) || in_array($field_name, $required_fields))
				$current_field['required'] = true;
			else
				$current_field['required'] = false;
			if (isset($details['validate']))
			{
				$current_field['validateMethod'] = (
 								array_key_exists('validateMethod', $resource_parameters['fields'][$field_name]) ?
 								array_merge($resource_parameters['fields'][$field_name]['validateMethod'], array($details['validate'])) :
 								array($details['validate'])
 							);
			}
			$resource_parameters['fields'][$field_name] = array_merge($resource_parameters['fields'][$field_name], $current_field);
		}
		if (isset($this->date_add))
			$resource_parameters['fields']['date_add']['setter'] = false;
		if (isset($this->date_upd))
			$resource_parameters['fields']['date_upd']['setter'] = false;
		foreach ($resource_parameters['fields'] as $key => $resource_parameters_field)
			if (!isset($resource_parameters_field['sqlId']))
				$resource_parameters['fields'][$key]['sqlId'] = $key;
		return $resource_parameters;
	}

	public function getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit)
	{
		$assoc = Shop::getAssoTable($this->def['table']);
		$class_name = WebserviceRequest::$ws_current_classname;
		$vars = get_class_vars($class_name);
		if ($assoc !== false)
		{
			if ($assoc['type'] !== 'fk_shop')
			{
				$multi_shop_join = ' LEFT JOIN `'._DB_PREFIX_.bqSQL($this->def['table']).'_'.bqSQL($assoc['type']).'`
										AS `multi_shop_'.bqSQL($this->def['table']).'`
										ON (main.`'.bqSQL($this->def['primary']).'` = `multi_shop_'.bqSQL($this->def['table']).'`.`'.bqSQL($this->def['primary']).'`)';
				$sql_filter = 'AND `multi_shop_'.bqSQL($this->def['table']).'`.id_shop = '.Context::getContext()->shop->id.' '.$sql_filter;
				$sql_join = $multi_shop_join.' '.$sql_join;
			}
			else
			{
				$vars = get_class_vars($class_name);
				foreach ($vars['shopIDs'] as $id_shop)
					$or[] = ' main.id_shop = '.(int)$id_shop.' ';
				
				$prepend = '';
				if (count($or))
					$prepend = 'AND ('.implode('OR', $or).')';
				$sql_filter = $prepend.' '.$sql_filter;
			}
		}
		$query = '
		SELECT DISTINCT main.`'.bqSQL($this->def['primary']).'` FROM `'._DB_PREFIX_.bqSQL($this->def['table']).'` AS main
		'.$sql_join.'
		WHERE 1 '.$sql_filter.'
		'.($sql_sort != '' ? $sql_sort : '').'
		'.($sql_limit != '' ? $sql_limit : '');
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
	}

	public function validateFieldsRequiredDatabase($htmlentities = true)
	{
		$errors = array();
		$required_fields = (isset(self::$fieldsRequiredDatabase[get_class($this)])) ? self::$fieldsRequiredDatabase[get_class($this)] : array();

		foreach ($this->def['fields'] as $field => $data)
		{
			if (!in_array($field, $required_fields))
				continue;

			if (!method_exists('Validate', $data['validate']))
				throw new PrestaShopException('Validation function not found. '.$data['validate']);

			$value = Tools::getValue($field);

			if (empty($value))
				$errors[] = sprintf(Tools::displayError('The field %s is required.'), self::displayFieldName($field, get_class($this), $htmlentities));
		}

		return $errors;
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

		if (!Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'required_field WHERE object_name = \''.get_class($this).'\''))
			return false;

		foreach ($fields as $field)
			if (!Db::getInstance()->insert('required_field', array('object_name' => get_class($this), 'field_name' => pSQL($field))))
				return false;
		return true;
	}

	public function clearCache($all = false)
	{
		if ($all)
			Cache::clean('objectmodel_'.$this->def['table'].'_*');
		else if ($this->id)
			Cache::clean('objectmodel_'.$this->def['table'].'_'.(int)$this->id.'_*');
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
		if ($id_shop === null)
			$id_shop = Context::getContext()->shop->id;

		$sql = 'SELECT id_shop
				FROM `'.pSQL(_DB_PREFIX_.$this->def['table']).'_shop`
				WHERE `'.$this->def['primary'].'` = '.(int)$this->id.'
					AND id_shop = '.(int)$id_shop;
		return (bool)Db::getInstance()->getValue($sql);
	}

	/**
	 * This function associate an item to its context
	 *
	 * @param int|array $id_shops
	 * @return boolean
	 */
	public function associateTo($id_shops)
	{
		if (!$this->id)
			return;

		if (!is_array($id_shops))
			$id_shops = array($id_shops);

		$data = array();
		foreach ($id_shops as $id_shop)
		{
			if (!$this->isAssociatedToShop($id_shop))
				$data[] = array(
					$this->def['primary'] => (int)$this->id,
					'id_shop' => (int)$id_shop,
				);
		}

		if ($data)
			return Db::getInstance()->insert($this->def['table'].'_shop', $data);
		return true;
	}

	/**
	 * Get the list of associated id_shop
	 *
	 * @since 1.5.0
	 * @return array
	 */
	public function getAssociatedShops()
	{
		if (!Shop::isTableAssociated($this->def['table']))
			return array();

		$list = array();
		$sql = 'SELECT id_shop FROM `'._DB_PREFIX_.$this->def['table'].'_shop` WHERE `'.$this->def['primary'].'` = '.(int)$this->id;
		foreach (Db::getInstance()->executeS($sql) as $row)
			$list[] = $row['id_shop'];
		return $list;
	}

	/**
	 * @since 1.5.0
	 */
	public function duplicateShops($id)
	{
		if (!Shop::isTableAssociated($this->def['table']))
			return false;

		$sql = 'SELECT id_shop
				FROM '._DB_PREFIX_.$this->def['table'].'_shop
				WHERE '.$this->def['primary'].' = '.(int)$id;
		if ($results = Db::getInstance()->executeS($sql))
		{
			$ids = array();
			foreach ($results as $row)
				$ids[] = $row['id_shop'];
			return $this->associateTo($ids);
		}

		return false;
	}

	/**
	 * Check if there is more than one entries in associated shop table for current entity
	 *
	 * @since 1.5.0
	 * @return bool
	 */
	public function hasMultishopEntries()
	{
		if (!Shop::isTableAssociated($this->def['table']) || !Shop::isFeatureActive())
			return false; 
		return (bool)Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.$this->def['table'].'_shop` WHERE `'.$this->def['primary'].'` = '.(int)$this->id);
	}

	public function isMultishop()
	{
		return Shop::isTableAssociated($this->def['table']) || !empty($this->def['multilang_shop']);
	}

	public function isLangMultishop()
	{
		return !empty($this->def['multilang']) && !empty($this->def['multilang_shop']);
	}

	/**
	 * Update a table and splits the common datas and the shop datas
	 *
	 * @since 1.5.0
	 * @param string $classname
	 * @param array $data
	 * @param string $where
	 * @param string $specific_where Only executed for common table
	 * @return bool
	 */
	public static function updateMultishopTable($classname, $data, $where, $specific_where = '')
	{
		$def = ObjectModel::getDefinition($classname);
		$update_data = array();
		foreach ($data as $field => $value)
		{
			if (!isset($def['fields'][$field]))
				continue;

			if (!empty($def['fields'][$field]['shop']))
			{
				$update_data[] = "a.$field = '$value'";
				$update_data[] = "{$def['table']}_shop.$field = '$value'";
			}
			else
				$update_data[] = "a.$field = '$value'";
		}

		$sql = 'UPDATE '._DB_PREFIX_.$def['table'].' a
				'.Shop::addSqlAssociation($def['table'], 'a').'
				SET '.implode(', ', $update_data).'
				WHERE '.$where;
		return Db::getInstance()->execute($sql);
	}

	/**
	 * Delete images associated with the object
	 *
	 * @return bool success
	 */
	public function deleteImage($force_delete = false)
	{
		if (!$this->id)
			return false;
		
		if ($force_delete || !$this->hasMultishopEntries())
		{
			/* Deleting object images and thumbnails (cache) */
			if ($this->image_dir)
			{
				if (file_exists($this->image_dir.$this->id.'.'.$this->image_format)
					&& !unlink($this->image_dir.$this->id.'.'.$this->image_format))
					return false;
			}
			if (file_exists(_PS_TMP_IMG_DIR_.$this->def['table'].'_'.$this->id.'.'.$this->image_format)
				&& !unlink(_PS_TMP_IMG_DIR_.$this->def['table'].'_'.$this->id.'.'.$this->image_format))
				return false;
			if (file_exists(_PS_TMP_IMG_DIR_.$this->def['table'].'_mini_'.$this->id.'.'.$this->image_format)
				&& !unlink(_PS_TMP_IMG_DIR_.$this->def['table'].'_mini_'.$this->id.'.'.$this->image_format))
				return false;
	
			$types = ImageType::getImagesTypes();
			foreach ($types as $image_type)
				if (file_exists($this->image_dir.$this->id.'-'.stripslashes($image_type['name']).'.'.$this->image_format)
				&& !unlink($this->image_dir.$this->id.'-'.stripslashes($image_type['name']).'.'.$this->image_format))
					return false;
		}
		return true;
	}

	/**
	 * Specify if an ObjectModel is already in database
	 *
	 * @param int $id_entity
	 * @param string $table
	 * @return boolean
	 */
	public static function existsInDatabase($id_entity, $table)
	{
		$row = Db::getInstance()->getRow('
			SELECT `id_'.$table.'` as id
			FROM `'._DB_PREFIX_.$table.'` e
			WHERE e.`id_'.$table.'` = '.(int)$id_entity
		);

		return isset($row['id']);
	}

	/**
	 * This method is allow to know if a entity is currently used
	 * @since 1.5.0.1
	 * @param string $table name of table linked to entity
	 * @param bool $has_active_column true if the table has an active column
	 * @return bool
	 */
	public static function isCurrentlyUsed($table = null, $has_active_column = false)
	{
		if ($table === null)
			$table = self::$definition['table'];

		$query = new DbQuery();
		$query->select('`id_'.pSQL($table).'`');
		$query->from($table);
		if ($has_active_column)
			$query->where('`active` = 1');
		return (bool)Db::getInstance()->getValue($query);
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
		if (isset($data[$this->def['primary']]))
			$this->id = $data[$this->def['primary']];
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
			throw new PrestaShopException("Class '$class' not found");

		$collection = array();
		$rows = array();
		if ($datas)
		{
			$definition = ObjectModel::getDefinition($class);
			if (!array_key_exists($definition['primary'], $datas[0]))
				throw new PrestaShopException("Identifier '{$definition['primary']}' not found for class '$class'");

			foreach ($datas as $row)
			{
				// Get object common properties
				$id = $row[$definition['primary']];
				if (!isset($rows[$id]))
					$rows[$id] = $row;

				// Get object lang properties
				if (isset($row['id_lang']) && !$id_lang)
					foreach ($definition['fields'] as $field => $data)
						if (!empty($data['lang']))
						{
							if (!is_array($rows[$id][$field]))
								$rows[$id][$field] = array();
							$rows[$id][$field][$row['id_lang']] = $row[$field];
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

	/**
	 * Get object definition
	 *
	 * @param string $class Name of object
	 * @param string $field Name of field if we want the definition of one field only
	 * @return array
	 */
	public static function getDefinition($class, $field = null)
	{
		$reflection = new ReflectionClass($class);
		$definition = $reflection->getStaticPropertyValue('definition');
		$definition['classname'] = $class;
		if (!empty($definition['multilang']))
			$definition['associations'][Collection::LANG_ALIAS] = array(
				'type' => self::HAS_MANY,
				'field' => $definition['primary'],
				'foreign_field' => $definition['primary'],
			);

		if ($field)
			return isset($definition['fields'][$field]) ? $definition['fields'][$field] : null;
		return $definition;
	}

	/**
	 * Retrocompatibility for classes without $definition static
	 * Remove this in 1.6 !
	 *
	 * @since 1.5.0
	 */
	protected function setDefinitionRetrocompatibility()
	{
		// Retrocompatibility with $table property ($definition['table'])
		if (isset($this->def['table']))
			$this->table = $this->def['table'];
		else
			$this->def['table'] = $this->table;

		// Retrocompatibility with $identifier property ($definition['primary'])
		if (isset($this->def['primary']))
			$this->identifier = $this->def['primary'];
		else
			$this->def['primary'] = $this->identifier;

		// Check multilang retrocompatibility
		if (method_exists($this, 'getTranslationsFieldsChild'))
			$this->def['multilang'] = true;

		// Retrocompatibility with $fieldsValidate, $fieldsRequired and $fieldsSize properties ($definition['fields'])
		if (isset($this->def['fields']))
		{
			foreach ($this->def['fields'] as $field => $data)
			{
				$suffix = (isset($data['lang']) && $data['lang']) ? 'Lang' : '';
				if (isset($data['validate']))
					$this->{'fieldsValidate'.$suffix}[$field] = $data['validate'];
				if (isset($data['required']) && $data['required'])
					$this->{'fieldsRequired'.$suffix}[] = $field;
				if (isset($data['size']))
					$this->{'fieldsSize'.$suffix}[$field] = $data['size'];
			}
		}
		else
		{
			$this->def['fields'] = array();
			$suffix = (isset($data['lang']) && $data['lang']) ? 'Lang' : '';
			foreach ($this->{'fieldsValidate'.$suffix} as $field => $validate)
				$this->def['fields'][$field]['validate'] = $validate;
			foreach ($this->{'fieldsRequired'.$suffix} as $field)
				$this->def['fields'][$field]['required'] = true;
			foreach ($this->{'fieldsSize'.$suffix} as $field => $size)
				$this->def['fields'][$field]['size'] = $size;
		}
	}

	/**
	 * Return the field value for the specified language if the field is multilang, else the field value.
	 *
	 * @param $field_name
	 * @param null $id_lang
	 * @return mixed
	 * @throws PrestaShopException
	 * @since 1.5
	 */
	public function getFieldByLang($field_name, $id_lang = null)
	{
		$definition = ObjectModel::getDefinition($this);
		// Is field in definition?
		if ($definition && isset($definition['fields'][$field_name]))
		{
			$field = $definition['fields'][$field_name];
			// Is field multilang?
			if (isset($field['lang']) && $field['lang'])
			{
				if (is_array($this->{$field_name}))
					return $this->{$field_name}[$id_lang ? $id_lang : Context::getContext()->language->id];
			}
			return $this->{$field_name};
		}
		else
			throw new PrestaShopException('Could not load field from definition.');
	}

	/**
	 * Set a list of specific fields to update
	 * array(field1 => true, field2 => false, langfield1 => array(1 => true, 2 => false))
	 *
	 * @since 1.5.0
	 * @param array $fields
	 */
	public function setFieldsToUpdate(array $fields)
	{
		$this->update_fields = $fields;
	}
}
