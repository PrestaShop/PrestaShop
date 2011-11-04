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

	protected	$fieldsRequired = array(
		'id_product',
	);
	protected	$fieldsSize = array(
		'reference' => 32,
		'supplier_reference' => 32,
		'location' => 64,
		'ean13' => 13,
		'upc' => 12,
		'wholesale_price' => 27,
		'price' => 20,
		'ecotax' => 20,
		'quantity' => 10
	);
	protected	$fieldsValidate = array(
		'id_product' => 'isUnsignedId',
		'location' => 'isGenericName',
		'ean13' => 'isEan13',
		'upc' => 'isUpc',
		'wholesale_price' => 'isPrice',
		'price' => 'isPrice',
		'ecotax' => 'isPrice',
		'quantity' => 'isUnsignedInt',
		'weight' => 'isFloat',
		'default_on' => 'isBool',
		'available_date' => 'isDateFormat',
	);

	protected $table = 'product_attribute';
	protected $identifier = 'id_product_attribute';

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

	public function getFields()
	{
		$this->validateFields();
		$fields['id_product'] = (int)($this->id_product);
		$fields['reference'] = pSQL($this->reference);
		$fields['supplier_reference'] = pSQL($this->supplier_reference);
		$fields['location'] = pSQL($this->location);
		$fields['ean13'] = pSQL($this->ean13);
		$fields['upc'] = pSQL($this->upc);
		$fields['wholesale_price'] = pSQL($this->wholesale_price);
		$fields['price'] = pSQL($this->price);
		$fields['ecotax'] = pSQL($this->ecotax);
		$fields['quantity'] = (int)($this->quantity);
		$fields['weight'] = pSQL($this->weight);
		$fields['default_on'] = (int)($this->default_on);
		$fields['available_date'] = pSQL($this->available_date);
		return $fields;
	}

	public function delete()
	{
		if (!parent::delete() OR $this->deleteAssociations() === false)
			return false;
		return true;
	}

	public function deleteAssociations()
	{
		if (
			Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'product_attribute_combination`
				WHERE `id_product_attribute` = '.(int)($this->id)) === false
			)
			return false;
		return true;
	}

	public function setWsProductOptionValues($values)
	{
		if ($this->deleteAssociations())
		{
			$sqlValues = array();
			foreach ($values as $value)
				$sqlValues[] = '('.(int)$value['id'].', '.(int)$this->id.')';
			$result = Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'product_attribute_combination` (`id_attribute`, `id_product_attribute`)
				VALUES '.implode(',', $sqlValues)
			);
			return $result;
		}
		return false;
	}

	public function getWsProductOptionValues()
	{
		$result = Db::getInstance()->executeS('SELECT id_attribute AS id from `'._DB_PREFIX_.'product_attribute_combination` WHERE id_product_attribute = '.(int)$this->id);
		return $result;
	}

	public function getWsImages()
	{
		return Db::getInstance()->executeS('
		SELECT `id_image` as id
		FROM `'._DB_PREFIX_.'product_attribute_image`
		WHERE `id_product_attribute` = '.(int)($this->id).'
		');
	}

	public function setWsImages($values)
	{
		if (Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'product_attribute_image`
			WHERE `id_product_attribute` = '.(int)($this->id)) === false)
		return false;
		$sqlValues = array();
		foreach ($values as $value)
			$sqlValues[] = '('.(int)$this->id.', '.(int)$value['id'].')';
		Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'product_attribute_image` (`id_product_attribute`, `id_image`)
			VALUES '.implode(',', $sqlValues)
		);
		return true;
	}
	
	public function getAttributesName($id_lang)
	{
		return Db::getInstance()->executeS('SELECT al.*
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
}


