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

class AttributeGroupCore extends ObjectModel
{
 	/** @var string Name */
	public 		$name;
	public		$is_color_group;
	
	/** @var string Public Name */
	public 		$public_name;	
	
	protected	$fieldsRequired = array();
	protected	$fieldsValidate = array('is_color_group' => 'isBool');
 	protected 	$fieldsRequiredLang = array('name', 'public_name');
 	protected 	$fieldsSizeLang = array('name' => 64, 'public_name' => 64);
 	protected 	$fieldsValidateLang = array('name' => 'isGenericName', 'public_name' => 'isGenericName');
		
	protected 	$table = 'attribute_group';
	protected 	$identifier = 'id_attribute_group';
	
	protected	$webserviceParameters = array(
		'objectsNodeName' => 'product_options',
		'objectNodeName' => 'product_option',
		'fields' => array(),
		'associations' => array(
			'product_option_values' => array('resource' => 'product_option_value',
			'fields' => array(
					'id' => array(),
			),
			),
		),
	);

	public function getFields()
	{
		parent::validateFields();

		$fields['is_color_group'] = (int)($this->is_color_group);

		return $fields;
	}
	
	public function add($autodate = true, $nullValues = false)
	{
	 	return parent::add($autodate, true);
	}
	
	/**
	* Check then return multilingual fields for database interaction
	*
	* @return array Multilingual fields
	*/
	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();
		return parent::getTranslationsFields(array('name', 'public_name'));
	}

	static public function cleanDeadCombinations()
	{
		$attributeCombinations = Db::getInstance()->ExecuteS('SELECT pac.`id_attribute`, pa.`id_product_attribute` FROM `'._DB_PREFIX_.'product_attribute` pa LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pa.`id_product_attribute` = pac.`id_product_attribute`)');
		$toRemove = array();
		foreach ($attributeCombinations AS $attributeCombination)
			if ((int)($attributeCombination['id_attribute']) == 0)
				$toRemove[] = (int)($attributeCombination['id_product_attribute']);
		if (!empty($toRemove) AND Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'product_attribute` WHERE `id_product_attribute` IN ('.implode(', ', $toRemove).')') === false)
			return false;
		return true;
	}

	public function delete()
	{
		/* Select children in order to find linked combinations */
		$attributeIds = Db::getInstance()->ExecuteS('SELECT `id_attribute` FROM `'._DB_PREFIX_.'attribute` WHERE `id_attribute_group` = '.(int)($this->id));
		if ($attributeIds === false)
			return false;
		/* Removing attributes to the found combinations */
		$toRemove = array();
		foreach ($attributeIds AS $attribute)
			$toRemove[] = (int)($attribute['id_attribute']);
		if (!empty($toRemove) AND Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'product_attribute_combination` WHERE `id_attribute` IN ('.implode(', ', $toRemove).')') === false)
			return false;
		/* Remove combinations if they do not possess attributes anymore */
		if (!self::cleanDeadCombinations())
			return false;
	 	/* Also delete related attributes */
		if (Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'attribute_lang` WHERE `id_attribute` IN (SELECT id_attribute FROM `'._DB_PREFIX_.'attribute` WHERE `id_attribute_group` = '.(int)($this->id).')') === false OR Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'attribute` WHERE `id_attribute_group` = '.(int)($this->id)) === false)
			return false;
		return parent::delete();
	}
	
	/**
	 * Get all attributes for a given language / group
	 *
	 * @param integer $id_lang Language id
	 * @param boolean $id_attribute_group Attribute group id
	 * @return array Attributes
	 */
	static public function getAttributes($id_lang, $id_attribute_group)
	{
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'attribute` a
		LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)($id_lang).')
		WHERE a.`id_attribute_group` = '.(int)($id_attribute_group).'
		ORDER BY `name`');
	}
	
	/**
	 * Get all attributes groups for a given language
	 *
	 * @param integer $id_lang Language id
	 * @return array Attributes groups
	 */
	static public function getAttributesGroups($id_lang)
	{
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'attribute_group` ag
		LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND `id_lang` = '.(int)($id_lang).')
		ORDER BY `name` ASC');
	}
	
	/**
	 * Delete several objects from database
	 *
	 * return boolean Deletion result
	 */
	public function deleteSelection($selection)
	{
		/* Also delete Attributes */
		foreach ($selection AS $value) {
			$obj = new AttributeGroup($value);
			if (!$obj->delete())
				return false;
		}
		return true;
	}
	
	public function setWsProductOptionValues($values)
	{
		$ids = array();
		foreach ($values as $value)
			$ids[] = intval($value['id']);
		$result = Db::getInstance()->Execute('
			DELETE FROM `'._DB_PREFIX_.'attribute`
			WHERE `id_attribute_group` = '.(int)$this->id.'
			AND `id_attribute` NOT IN ('.implode(',', $ids).')'
		);
		$ok = true;
		foreach ($values as $value)
		{
			$result = Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'attribute`
				SET `id_attribute_group` = '.(int)$this->id.'
				WHERE `id_attribute` = '.(int)$value['id']
			);
			if ($result === false)
				$ok = false;
		}
		return $ok;
	}
	
	public function getWsProductOptionValues()
	{
		$result = Db::getInstance()->executeS('SELECT id_attribute AS id from `'._DB_PREFIX_.'attribute` WHERE id_attribute_group = '.(int)$this->id);
		return $result;
	}
}

