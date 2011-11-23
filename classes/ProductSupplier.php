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
*  @version  Release: $Revision: 7310 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
/**
 * @since 1.5.0
 */
class ProductSupplierCore extends ObjectModel
{
	/**
	 * @var integer product ID
	 * */
	public $id_product;

	/**
	 * @var integer product attribute ID
	 * */
	public $id_product_attribute;

	/**
	 * @var integer the supplier ID
	 * */
	public $id_supplier;

	/**
	 * @var string The supplier reference of the product
	 * */
	public $product_supplier_reference;

 	protected $fieldsRequired = array('id_product', 'id_product_attribute', 'id_supplier');
 	protected $fieldsSize = array('supplier_reference' => 32);
 	protected $fieldsValidate = array(
 		'product_supplier_reference' => 'isReference',
 		'id_product' => 'isUnsignedId',
 		'id_product_attribute' => 'isUnsignedId',
 		'id_supplier' => 'isUnsignedId',
 	);

	protected $table = 'product_supplier';
	protected $identifier = 'id_product_supplier';

	public function getFields()
	{
		$this->validateFields();

		$fields['id_product'] = (int)$this->id_product;
		$fields['id_product_attribute'] = (int)$this->id_product_attribute;
		$fields['id_supplier'] = (int)$this->id_supplier;
		$fields['product_supplier_reference'] = pSQL($this->product_supplier_reference);

		return $fields;
	}

	/**
	 * For a given product and supplier, get the product reference
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @param int $id_supplier
	 * @return array
	 */
	public static function getProductSupplierReference($id_product, $id_product_attribute, $id_supplier)
	{
		// build query
		$query = new DbQuery();
		$query->select('ps.product_supplier_reference');
		$query->from('product_supplier ps');
		$query->where('ps.id_product = '.(int)$id_product.'
			AND ps.id_product_attribute = '.(int)$id_product_attribute.'
			AND ps.id_supplier = '.(int)$id_supplier
		);

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}

	/**
	 * For a given product and supplier, get the ProductSupplier corresponding ID
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @param int $id_supplier
	 * @return array
	 */
	public static function getIdByProductAndSupplier($id_product, $id_product_attribute, $id_supplier)
	{
		// build query
		$query = new DbQuery();
		$query->select('ps.id_product_supplier');
		$query->from('product_supplier ps');
		$query->where('ps.id_product = '.(int)$id_product.'
			AND ps.id_product_attribute = '.(int)$id_product_attribute.'
			AND ps.id_supplier = '.(int)$id_supplier
		);

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}

	/**
	 * For a given product, retrieves its suppliers
	 *
	 * @param int $id_product
	 * @param int $group_by_supplier
	 * @return array
	 */
	public static function getSupplierCollection($id_product, $group_by_supplier = true)
	{
		// build query
		$query = new DbQuery();
		$query->select('*');
		$query->from('product_supplier ps');
		$query->where('ps.id_product = '.(int)$id_product);

		if ($group_by_supplier)
			$query->groupBy('ps.id_supplier');

		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

		return ObjectModel::hydrateCollection('ProductSupplier', $results);
	}

	public function delete()
	{
		$res = parent::delete();

		if ($res && $this->id_product_attribute == 0)
		{
			$items = self::getSupplierCollection($this->id_product, false);
			foreach ($items as &$item)
			{
				if ($item->id_product_attribute > 0)
					$item->delete();
			}
		}

		return $res;
	}
}
