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
*  @version  Release: $Revision: 9991 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class SupplyOrderCore extends ObjectModel
{
	/**
	 * @var int Supplier
	 */
	public $id_supplier;

	/**
	 * @var string Supplier Name
	 */
	public $supplier_name;

	/**
	 * @var int The language id used on the delivery note
	 */
	public $id_lang;

	/**
	 * @var int Warehouse where products will be delivered
	 */
	public $id_warehouse;

	/**
	 * @var int State of the order
	 */
	public $id_supply_order_state;

	/**
	 * @var int Currency used for the order
	 */
	public $id_currency;

	/**
	 * @var int Currency used by default in main global configuration (i.e. by default for all shops)
	 */
	public $id_ref_currency;

	/**
	 * @var string Reference of the order
	 */
	public $reference;

	/**
	 * @var string Date when added
	 */
	public $date_add;

	/**
	 * @var string Date when updated
	 */
	public $date_upd;

	/**
	 * @var string Expected delivery date
	 */
	public $date_delivery_expected;

	/**
	 * @var float Total price without tax
	 */
	public $total_te = 0;

	/**
	 * @var float Total price after discount, without tax
	 */
	public $total_with_discount_te = 0;

	/**
	 * @var float Total price with tax
	 */
	public $total_ti = 0;

	/**
	 * @var float Total tax value
	 */
	public $total_tax = 0;

	/**
	 * @var float Supplier discount rate (for the whole order)
	 */
	public $discount_rate = 0;

	/**
	 * @var float Supplier discount value without tax (for the whole order)
	 */
	public $discount_value_te = 0;

	protected $fieldsRequired = array(
		'id_supplier',
		'supplier_name',
		'id_lang',
		'id_warehouse',
		'id_supply_order_state',
		'id_currency',
		'id_ref_currency',
		'reference',
		'discount_rate',
		'date_delivery_expected'
	);

	protected $fieldsValidate = array(
		'id_supplier' => 'isUnsignedId',
		'supplier_name' => 'isCatalogName',
		'id_lang' => 'isUnsignedId',
		'id_warehouse' => 'isUnsignedId',
		'id_supply_order_state' => 'isUnsignedId',
		'id_currency' => 'isUnsignedId',
		'id_ref_currency' => 'isUnsignedId',
		'reference' => 'isGenericName',
		'date_add' => 'isDate',
		'date_upd' => 'isDate',
		'date_delivery_expected' => 'isDate',
		'total_te' => 'isPrice',
		'total_with_discount_te' => 'isPrice',
		'total_ti' => 'isPrice',
		'total_tax' => 'isPrice',
		'discount_rate' => 'isFloat',
		'discount_value_te' => 'isPrice'
	);

	/**
	 * @var string Database table name
	 */
	protected $table = 'supply_order';

	/**
	 * @var string Database ID name
	 */
	protected $identifier = 'id_supply_order';

	public function getFields()
	{
		$this->validateFields();

		$fields['id_supplier'] = (int)$this->id_supplier;
		$fields['supplier_name'] = pSQL($this->supplier_name);
		$fields['id_lang'] = (int)$this->id_lang;
		$fields['id_warehouse'] = (int)$this->id_warehouse;
		$fields['id_supply_order_state'] = (int)$this->id_supply_order_state;
		$fields['id_currency'] = (int)$this->id_currency;
		$fields['id_ref_currency'] = (int)$this->id_ref_currency;
		$fields['reference'] = pSQL($this->reference);
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		$fields['date_delivery_expected'] = pSQL($this->date_delivery_expected);
		$fields['total_te'] = (float)$this->total_te;
		$fields['total_with_discount_te'] = (float)$this->total_with_discount_te;
		$fields['total_ti'] = (float)$this->total_ti;
		$fields['total_tax'] = (float)$this->total_tax;
		$fields['discount_rate'] = (float)$this->discount_rate;
		$fields['discount_value_te'] = (float)$this->discount_value_te;

		return $fields;
	}

	/**
	 * @see ObjectModel::update()
	 */
	public function update($null_values = false)
	{
		$this->calculatePrices();

		$res = parent::update($null_values);

		if ($res)
			$this->addHistory();

		return $res;
	}

	/**
	 * @see ObjectModel::add()
	 */
	public function add($autodate = true, $null_values = false)
	{
		$this->calculatePrices();

		$res = parent::add($autodate, $null_values);

		if ($res)
			$this->addHistory();

		return $res;
	}

	/**
	 * Check all products in this order and calculate prices
	 * Apply global discount if necessary
	 *
	 * @return array
	 */
	protected function calculatePrices()
	{
		$this->total_te = 0;
		$this->total_with_discount_te = 0;
		$this->total_tax = 0;
		$this->total_ti = 0;
		$is_discount = false;

		if (is_numeric($this->discount_rate) && (float)$this->discount_rate > 0)
			$is_discount = true;

		// gets all product entries in this order
		$entries = $this->getEntriesCollection();

		foreach ($entries as $entry)
		{
			// applys global discount rate on each product if possible
			if ($is_discount)
				$entry->applyGlobalDiscount((float)$this->discount_rate);

			// adds new prices to the total
			$this->total_te += $entry->price_with_discount_te;
			$this->total_with_discount_te += $entry->price_with_order_discount_te;
			$this->total_tax += $entry->tax_value_with_order_discount;
			$this->total_ti = $this->total_tax + $this->total_with_discount_te;
		}

		// applies global discount rate if possible
		if ($is_discount)
			$this->discount_value_te = $this->total_te - $this->total_with_discount_te;
	}

	/**
	 * Retrieves the product entries for the current order
	 *
	 * @return array
	 */
	public function getEntries($id_lang = null)
	{
		if ($id_lang == null)
			$id_lang = Context::getContext()->language->id;

		// build query
		$query = new DbQuery();

		$query->select('
			s.*,
			IFNULL(CONCAT(pl.name, \' : \', GROUP_CONCAT(agl.name, \' - \', al.name SEPARATOR \', \')), pl.name) as name_displayed,
			p.reference as reference,
			p.ean13 as ean13');

		$query->from('supply_order_detail s');

		$query->innerjoin('product_lang pl ON (pl.id_product = s.id_product AND pl.id_lang = '.$id_lang.')');

		$query->leftjoin('product p ON p.id_product = s.id_product');
		$query->leftjoin('product_attribute_combination pac ON (pac.id_product_attribute = s.id_product_attribute)');
		$query->leftjoin('attribute atr ON (atr.id_attribute = pac.id_attribute)');
		$query->leftjoin('attribute_lang al ON (al.id_attribute = atr.id_attribute AND al.id_lang = '.$id_lang.')');
		$query->leftjoin('attribute_group_lang agl ON (agl.id_attribute_group = atr.id_attribute_group AND agl.id_lang = '.$id_lang.')');

		$query->where('s.id_supply_order = '.(int)$this->id);

		$query->groupBy('s.id_supply_order_detail');

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
	}

	/**
	 * Retrieves the product entries collection for the current order
	 *
	 * @return array
	 */
	public function getEntriesCollection($id_lang = null)
	{
		if ($id_lang == null)
			$id_lang = Context::getContext()->language->id;

		// build query
		$query = new DbQuery();
		$query->select('s.*');
		$query->from('supply_order_detail s');
		$query->where('s.id_supply_order = '.(int)$this->id);

		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

		return ObjectModel::hydrateCollection('SupplyOrderDetail', $results);
	}


	/**
	 * Check if the order has entries
	 *
	 * @return bool
	 */
	public function hasEntries()
	{
		$query = new DbQuery();
		$query->select('COUNT(*)');
		$query->from('supply_order_detail s');
		$query->where('s.id_supply_order = '.(int)$this->id);

		return (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query) > 0);
	}

	/**
	 * Check if the current state allow to edit the current order
	 *
	 * @return bool
	 */
	public function isEditable()
	{
		// build query
		$query = new DbQuery();
		$query->select('s.editable');
		$query->from('supply_order_state s');
		$query->where('s.id_supply_order_state = '.(int)$this->id_supply_order_state);

		return (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query) == 1);
	}

	/**
	 * Checks if the current state allows to generate a delivery note for this order
	 *
	 * @return bool
	 */
	public function isDeliveryNoteAvailable()
	{
		// build query
		$query = new DbQuery();
		$query->select('s.delivery_note');
		$query->from('supply_order_state s');
		$query->where('s.id_supply_order_state = '.(int)$this->id_supply_order_state);

		return (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query) == 1);
	}

	/**
	 * Checks if the current state allows add products in stock
	 *
	 * @return bool
	 */
	public function isInReceiptState()
	{
		// build query
		$query = new DbQuery();
		$query->select('s.receipt_state');
		$query->from('supply_order_state s');
		$query->where('s.id_supply_order_state = '.(int)$this->id_supply_order_state);

		return (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query) == 1);
	}

	/**
	 * Historizes the order : its id, its state, and the employee responsible for the current action
	 */
	protected function addHistory()
	{
		$context = Context::getContext();
		$history = new SupplyOrderHistory();
		$history->id_supply_order = $this->id;
		$history->id_state = $this->id_supply_order_state;
		$history->id_employee = (int)$context->employee->id;
		$history->employee_firstname = pSQL($context->employee->firstname);
		$history->employee_lastname = pSQL($context->employee->lastname);

		$history->save();
	}

	/**
	 * Removes all products from the order
	 */
	public function resetProducts()
	{
		$products = $this->getEntriesCollection();

		foreach ($products as $p)
			$p->delete();
	}

	/**
	 * For a given $id_warehouse, tells if it has pending supply orders
	 *
	 * @param int $id_warehouse
	 * @return bool
	 */
	public static function warehouseHasPendingOrders($id_warehouse)
	{
		if (!$id_warehouse)
			return false;

		$query = new DbQuery();
		$query->select('COUNT(so.id_supply_order) as supply_orders');
		$query->from('supply_order so');
		$query->leftJoin('supply_order_state sos ON (so.id_supply_order_state = sos.id_supply_order_state)');
		$query->where('sos.enclosed != 1');
		$query->where('so.id_warehouse = '.(int)$id_warehouse);

		$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
		return ($res > 0);
	}

}