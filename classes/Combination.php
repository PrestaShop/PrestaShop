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
			'product_option_values' => array('resource' => 'product_option_value', 'xlink_resource'=> 'product_option_values'),
		),
	);

	public function getFields()
	{
		parent::validateFields();
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
			Db::getInstance()->Execute('
				DELETE FROM `'._DB_PREFIX_.'product_attribute_combination`
				WHERE `id_product_attribute` = '.(int)($this->id)) === false
			||
			Db::getInstance()->Execute('
				DELETE FROM `'._DB_PREFIX_.'product_attribute_image`
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
			$result = Db::getInstance()->Execute('
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

}


