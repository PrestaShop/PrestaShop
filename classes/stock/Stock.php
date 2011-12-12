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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * Represents the products kept in warehouses
 *
 * @since 1.5.0
 */
class StockCore extends ObjectModel
{
	/** @var int identifier of the warehouse */
	public $id_warehouse;

	/** @var int identifier of the product */
	public $id_product;

	/** @var int identifier of the product attribute if necessary */
	public $id_product_attribute;

	/** @var string Product reference */
	public $reference;

	/** @var int Product EAN13 */
	public $ean13;

	/** @var string UPC */
	public $upc;

	/** @var int the physical quantity in stock for the current product in the current warehouse */
	public $physical_quantity;

	/** @var int the usable quantity (for sale) of the current physical quantity */
	public $usable_quantity;

	/** @var int the unit price without tax forthe current product */
	public $price_te;

	

	

	

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'stock',
		'primary' => 'id_stock',
		'fields' => array(
			'id_warehouse' => array('type' => 'FILL_ME', 'validate' => 'isUnsignedId', 'required' => true),
			'id_product' => array('type' => 'FILL_ME', 'validate' => 'isUnsignedId', 'required' => true),
			'id_product_attribute' => array('type' => 'FILL_ME', 'validate' => 'isUnsignedId', 'required' => true),
			'reference' => array('type' => 'FILL_ME', 'validate' => 'isReference'),
			'ean13' => array('type' => 'FILL_ME', 'validate' => 'isEan13'),
			'upc' => array('type' => 'FILL_ME', 'validate' => 'isUpc'),
			'physical_quantity' => array('type' => 'FILL_ME', 'validate' => 'isUnsignedInt', 'required' => true),
			'usable_quantity' => array('type' => 'FILL_ME', 'validate' => 'isInt', 'required' => true),
			'price_te' => array('type' => 'FILL_ME', 'validate' => 'isPrice', 'required' => true),
		),
	);


	public function getFields()
	{
		$this->validateFields();

		$fields['id_warehouse'] = (int)$this->id_warehouse;
		$fields['id_product'] = (int)$this->id_product;
		$fields['id_product_attribute'] = (int)$this->id_product_attribute;
		$fields['reference'] = pSQL($this->reference);
		$fields['ean13'] = pSQL($this->ean13);
		$fields['upc'] = pSQL($this->upc);
		$fields['physical_quantity'] = (int)$this->physical_quantity;
		$fields['usable_quantity'] = (int)$this->usable_quantity;
		$fields['price_te'] = (float)round($this->price_te, 6);

		return $fields;
	}

	/**
	 * @see ObjectModel::update()
	 */
	public function update($null_values = false)
	{
		$this->getProductInformations();

		return parent::update($null_values);
	}

	/**
	 * @see ObjectModel::add()
	 */
	public function add($autodate = true, $null_values = false)
	{
		$this->getProductInformations();

		return parent::add($autodate, $null_values);
	}

	/**
	 * Try to get reference, ean13 and upc information on current product
	 * and store it in stock for stock_mvt integrity and history use
	 */
	protected function getProductInformations()
	{
		if ((int)$this->id_product_attribute > 0)
		{
			$query = new DbQuery();
			$query->select('reference, ean13, upc');
			$query->from('product_attribute');
			$query->where('id_product = '.(int)$this->id_product);
			$query->where('id_product_attribute = '.(int)$this->id_product_attribute);

			foreach (Db::getInstance()->executeS($query) as $row)
			{
				$this->reference = $row['reference'];
				$this->ean13 = $row['ean13'];
				$this->upc = $row['upc'];
			}
		}
		else
		{
			$product = new Product((int)$this->id_product);
			if (Validate::isLoadedObject($product))
			{
				$this->reference = $product->reference;
				$this->ean13 = $product->ean13;
				$this->upc = $product->upc;
			}
		}
	}
}