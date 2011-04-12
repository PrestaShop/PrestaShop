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

class AttributeCore extends ObjectModel
{	
	/** @var integer Group id which attribute belongs */
	public		$id_attribute_group;
	
	/** @var string Name */
	public 		$name;
	public		$color;
	
	public		$default;
	
 	protected 	$fieldsRequired = array('id_attribute_group');
	protected 	$fieldsValidate = array('id_attribute_group' => 'isUnsignedId', 'color' => 'isColor');
 	protected 	$fieldsRequiredLang = array('name');
 	protected 	$fieldsSizeLang = array('name' => 64);
 	protected 	$fieldsValidateLang = array('name' => 'isGenericName');
		
	protected 	$table = 'attribute';
	protected 	$identifier = 'id_attribute';
	
	protected	$webserviceParameters = array(
		'objectsNodeName' => 'product_option_values',
		'objectNodeName' => 'product_option_value',
		'fields' => array(
			'id_attribute_group' => array('xlink_resource'=> 'product_options'),
			'default' => array(),
		),
	);

	public function getFields()
	{
		parent::validateFields();

		$fields['id_attribute_group'] = (int)($this->id_attribute_group);
		$fields['color'] = pSQL($this->color);

		return $fields;
	}
	
	/**
	* Check then return multilingual fields for database interaction
	*
	* @return array Multilingual fields
	*/
	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();
		return parent::getTranslationsFields(array('name'));
	}

	public function delete()
	{
		if (($result = Db::getInstance()->ExecuteS('SELECT `id_product_attribute` FROM `'._DB_PREFIX_.'product_attribute_combination` WHERE `'.$this->identifier.'` = '.(int)($this->id))) === false)
			return false;
		$combinationIds = array();
		if (Db::getInstance()->numRows())
		{
			foreach ($result AS $row)
				$combinationIds[] = (int)($row['id_product_attribute']);
			if (Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'product_attribute_combination` WHERE `'.$this->identifier.'` = '.(int)($this->id)) === false)
				return false;
			if (Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'product_attribute` WHERE `id_product_attribute` IN ('.implode(', ', $combinationIds).')') === false)
				return false;
		}
		return parent::delete();
	}

	/**
	 * Get all attributes for a given language
	 *
	 * @param integer $id_lang Language id
	 * @param boolean $notNull Get only not null fields if true
	 * @return array Attributes
	 */
	static public function getAttributes($id_lang, $notNull = false)
	{
		return Db::getInstance()->ExecuteS('
		SELECT ag.*, agl.*, a.`id_attribute`, al.`name`, agl.`name` AS `attribute_group`
		FROM `'._DB_PREFIX_.'attribute_group` ag
		LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute_group` = ag.`id_attribute_group`
		LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)($id_lang).')
		'.($notNull ? 'WHERE a.`id_attribute` IS NOT NULL AND al.`name` IS NOT NULL' : '').'
		ORDER BY agl.`name` ASC, al.`name` ASC');
	}
	
	/**
	 * Get quantity for a given attribute combinaison
	 * Check if quantity is enough to deserve customer
	 *
	 * @param integer $id_product_attribute Product attribute combinaison id
	 * @param integer $qty Quantity needed
	 * @return boolean Quantity is available or not
	 */
	static public function checkAttributeQty($id_product_attribute, $qty)
	{ 		
		$result = Db::getInstance()->getRow('
		SELECT `quantity`
		FROM `'._DB_PREFIX_.'product_attribute`
		WHERE `id_product_attribute` = '.(int)($id_product_attribute));

		return ($result AND ($qty <= $result['quantity']));
	}

	/**
	 * Get quantity for product with attributes quantity
	 *
	 * @acces public static
	 * @param integer $id_product
	 * @return mixed Quantity or false
	 */
	static public function getAttributeQty($id_product)
	{
		$row = Db::getInstance()->getRow('
		SELECT SUM(quantity) as quantity
		FROM `'._DB_PREFIX_.'product_attribute` 
		WHERE `id_product` = '.(int)($id_product));
		
		if ($row['quantity'] !== NULL)
			return (int)($row['quantity']);
		return false;
	}

	/**
	 * Update array with veritable quantity
	 *
	 * @acces public static
	 * @param array &$arr
	 * return bool
	 */
	static public function updateQtyProduct(&$arr)
	{
		$id_product = (int)($arr['id_product']);
		$qty = self::getAttributeQty($id_product);
		
		if ($qty !== false)
		{
			$arr['quantity'] = (int)($qty);
			return true;
		}
		return false;
	}

	public function isColorAttribute()
	{
		if (!Db::getInstance()->getRow('
			SELECT `is_color_group` FROM `'._DB_PREFIX_.'attribute_group` WHERE `id_attribute_group` = (
				SELECT `id_attribute_group` FROM `'._DB_PREFIX_.'attribute` WHERE `id_attribute` = '.(int)($this->id).')
				AND is_color_group = 1'))
			return false;
		return Db::getInstance()->NumRows();
	}
	
	/**
	 * Get minimal quantity for product with attributes quantity
	 *
	 * @acces public static
	 * @param integer $id_product_attribute
	 * @return mixed Minimal Quantity or false
	 */
	static public function getAttributeMinimalQty($id_product_attribute)
	{
		$row = Db::getInstance()->getValue('
		SELECT minimal_quantity
		FROM `'._DB_PREFIX_.'product_attribute` 
		WHERE `id_product_attribute` = '.(int)($id_product_attribute));
		
		if ($row['quantity'] !== NULL)
			return (int)($row['quantity']);
		return false;
	}
	
}

