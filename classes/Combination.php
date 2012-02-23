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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CombinationCore extends ObjectModel
{
	public $id_product;

	public $reference;

	public $supplier_reference;

	public $location;

	public $ean13;

	public $upc;

	public $wholesale_price;

	public $price;

	public $ecotax;

	public $quantity;

	public $weight;

	public $default_on;

	public $available_date;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'product_attribute',
		'primary' => 'id_product_attribute',
		'fields' => array(
			'id_product' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'location' => 			array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64),
			'ean13' => 				array('type' => self::TYPE_STRING, 'validate' => 'isEan13', 'size' => 13),
			'upc' => 				array('type' => self::TYPE_STRING, 'validate' => 'isUpc', 'size' => 12),
			'wholesale_price' =>	array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'size' => 27),
			'price' => 				array('type' => self::TYPE_FLOAT, 'validate' => 'isNegativePrice', 'size' => 20),
			'ecotax' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'size' => 20),
			'quantity' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'size' => 10),
			'weight' => 			array('type' => self::TYPE_INT, 'validate' => 'isFloat'),
			'default_on' => 		array('type' => self::TYPE_INT, 'validate' => 'isBool'),
			'available_date' => 	array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
			'reference' => 			array('type' => self::TYPE_STRING, 'size' => 32),
			'supplier_reference' => array('type' => self::TYPE_STRING, 'size' => 32),
		),
	);

	protected	$webserviceParameters = array(
		'objectNodeName' => 'combination',
		'objectsNodeName' => 'combinations',
		'fields' => array(
			'id_product' => array('required' => true, 'xlink_resource'=> 'products'),
		),
		'associations' => array(
			'product_option_values' => array('resource' => 'product_option_value'),
			'images' => array('resource' => 'image'),
		),
	);

	public function delete()
	{
		if (!parent::delete() || $this->deleteAssociations() === false)
			return false;
		return true;
	}

	public function deleteAssociations()
	{
		if (Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'product_attribute_combination`
				WHERE `id_product_attribute` = '.(int)$this->id) === false)
			return false;
		return true;
	}

	public function setAttributes($ids_attribute)
	{
		if ($this->deleteAssociations())
		{
			$sql_values = array();
			foreach ($ids_attribute as $value)
				$sql_values[] = '('.(int)$value.', '.(int)$this->id.')';

			$result = Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'product_attribute_combination` (`id_attribute`, `id_product_attribute`)
				VALUES '.implode(',', $sql_values)
			);

			return $result;
		}
		return false;
	}

	public function setWsProductOptionValues($values)
	{
		$ids_attributes = array();
		foreach ($values as $value)
			$ids_attributes[] = $value['id'];
		return $this->setAttributes($values);
	}

	public function getWsProductOptionValues()
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT id_attribute AS id
			FROM `'._DB_PREFIX_.'product_attribute_combination`
			WHERE id_product_attribute = '.(int)$this->id);

		return $result;
	}

	public function getWsImages()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT `id_image` as id
			FROM `'._DB_PREFIX_.'product_attribute_image`
			WHERE `id_product_attribute` = '.(int)$this->id.'
		');
	}

	public function setImages($ids_image)
	{
		if (Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'product_attribute_image`
			WHERE `id_product_attribute` = '.(int)$this->id) === false)
		return false;

		$sql_values = array();

		foreach ($ids_image as $value)
			$sql_values[] = '('.(int)$this->id.', '.(int)$value.')';

		Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'product_attribute_image` (`id_product_attribute`, `id_image`)
			VALUES '.implode(',', $sql_values)
		);
		return true;
	}

	public function setWsImages($values)
	{
		return $this->setImages($values);
	}

	public function getAttributesName($id_lang)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT al.*
			FROM '._DB_PREFIX_.'product_attribute_combination pac
			JOIN '._DB_PREFIX_.'attribute_lang al ON (pac.id_attribute = al.id_attribute AND al.id_lang='.(int)$id_lang.')
			WHERE pac.id_product_attribute='.(int)$this->id);
	}

	/**
	 * This method is allow to know if a feature is active
	 * @since 1.5.0.1
	 * @return bool
	 */
	public static function isFeatureActive()
	{
		return Configuration::get('PS_COMBINATION_FEATURE_ACTIVE');
	}

	/**
	 * This method is allow to know if a Combination entity is currently used
	 * @since 1.5.0.1
	 * @param $table
	 * @param $has_active_column
	 * @return bool
	 */
	public static function isCurrentlyUsed($table = null, $has_active_column = false)
	{
		return parent::isCurrentlyUsed('product_attribute');
	}

	public static function getIdByReference($id_product, $reference)
	{
		$query = new DbQuery();
		$query->select('pa.id_product_attribute');
		$query->from('product_attribute', 'pa');
		$query->where('pa.reference LIKE \'%'.pSQL($reference).'%\'');
		$query->where('pa.id_product = '.(int)$id_product);

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}
}
