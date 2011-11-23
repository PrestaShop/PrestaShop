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

class OrderDetailCore extends ObjectModel
{
	/** @var integer */
	public $id_order_detail;

	/** @var integer */
	public $id_order;

	/** @var integer */
	public $id_order_invoice;

	/** @var integer */
	public $product_id;

	/** @var integer */
	public $product_attribute_id;

	/** @var string */
	public $product_name;

	/** @var integer */
	public $product_quantity;

	/** @var integer */
	public $product_quantity_in_stock;

	/** @var integer */
	public $product_quantity_return;

	/** @var integer */
	public $product_quantity_refunded;

	/** @var integer */
	public $product_quantity_reinjected;

	/** @var float */
	public $product_price;

	/** @var float */
	public $unit_price_tax_incl;

	/** @var float */
	public $unit_price_tax_excl;

	/** @var float */
	public $total_price_tax_incl;

	/** @var float */
	public $total_price_tax_excl;

	/** @var float */
	public $reduction_percent;

	/** @var float */
	public $reduction_amount;

	/** @var float */
	public $group_reduction;

	/** @var float */
	public $product_quantity_discount;

	/** @var string */
	public $product_ean13;

	/** @var string */
	public $product_upc;

	/** @var string */
	public $product_reference;

	/** @var string */
	public $product_supplier_reference;

	/** @var float */
	public $product_weight;

	/** @var float */
	public $ecotax;

	/** @var float */
	public $ecotax_tax_rate;

	/** @var integer */
	public $discount_quantity_applied;

	/** @var string */
	public $download_hash;

	/** @var integer */
	public $download_nb;

	/** @var date */
	public $download_deadline;

	/** @var string $tax_name **/
	public $tax_name;

	/** @var float $tax_rate **/
	public $tax_rate;

	protected $tables = array('order_detail');

	protected	$fieldsRequired = array(
		'id_order',
		'product_name',
		'product_quantity',
		'product_price');

	protected	$fieldsValidate = array(
		'id_order' => 'isUnsignedId',
		'id_order_invoice' => 'isUnsignedId',
		'product_id' => 'isUnsignedId',
		'product_attribute_id' => 'isUnsignedId',
		'product_name' => 'isGenericName',
		'product_quantity' => 'isInt',
		'product_quantity_in_stock' => 'isInt',
		'product_quantity_return' => 'isUnsignedInt',
		'product_quantity_refunded' => 'isUnsignedInt',
		'product_quantity_reinjected' => 'isUnsignedInt',
		'product_price' => 'isPrice',
		'reduction_percent' => 'isFloat',
		'reduction_amount' => 'isPrice',
		'group_reduction' => 'isFloat',
		'product_quantity_discount' => 'isFloat',
		'product_ean13' => 'isEan13',
		'product_upc' => 'isUpc',
		'product_reference' => 'isReference',
		'product_supplier_reference' => 'isReference',
		'product_weight' => 'isFloat',
		'tax_name' => 'isGenericName',
		'tax_rate' => 'isFloat',
		'ecotax' => 'isFloat',
		'ecotax_tax_rate' => 'isFloat',
		'discount_quantity_applied' => 'isInt',
		'download_hash' => 'isGenericName',
		'download_nb' => 'isInt',
		'download_deadline' => 'isDateFormat',
		'unit_price_tax_incl' => 'isPrice',
		'unit_price_tax_excl' => 'isPrice',
		'total_price_tax_incl' => 'isPrice',
		'total_price_tax_excl' => 'isPrice'
	);

	protected 	$table = 'order_detail';
	protected 	$identifier = 'id_order_detail';

	protected	$webserviceParameters = array(
	'fields' => array (
		'id_order' => array('xlink_resource' => 'orders'),
		'product_id' => array('xlink_resource' => 'products'),
		'product_attribute_id' => array('xlink_resource' => 'combinations'),
		'product_quantity_reinjected' => array(),
		'group_reduction' => array(),
		'discount_quantity_applied' => array(),
		'download_hash' => array(),
		'download_deadline' => array()
		)
	);

	/** @var bool */
	protected $outOfStock = false;

	/** @var TaxCalculator object */
	protected $tax_calculator = null;

	/** @var Address object */
	protected $vat_address = null;

	/** @var Address object */
	protected $specificPrice = null;

	/** @var Customer object */
	protected $customer = null;

	/** @var Context object */
	protected $context = null;

	public function __construct($id = null, $id_lang = null, $context = null)
	{
		$this->context = $context;
		$id_shop = null;
		if ($this->context != null && isset($this->context->shop))
			$id_shop = $this->context->shop->getID();
		return parent::__construct($id, $id_lang, $id_shop);
	}

	public function getFields()
	{
		$this->validateFields();

		$fields['id_order'] = (int)$this->id_order;
		$fields['id_order_invoice'] = (int)$this->id_order_invoice;
		$fields['product_id'] = (int)$this->product_id;
		$fields['product_attribute_id'] = (int)$this->product_attribute_id;
		$fields['product_name'] = pSQL($this->product_name);
		$fields['product_quantity'] = (int)$this->product_quantity;
		$fields['product_quantity_in_stock'] = (int)$this->product_quantity_in_stock;
		$fields['product_quantity_return'] = (int)$this->product_quantity_return;
		$fields['product_quantity_refunded'] = (int)$this->product_quantity_refunded;
		$fields['product_quantity_reinjected'] = (int)$this->product_quantity_reinjected;
		$fields['product_price'] = (float)$this->product_price;
		$fields['reduction_percent'] = (float)$this->reduction_percent;
		$fields['reduction_amount'] = (float)$this->reduction_amount;
		$fields['group_reduction'] = (float)$this->group_reduction;
		$fields['product_quantity_discount'] = (float)$this->product_quantity_discount;
		$fields['product_ean13'] = pSQL($this->product_ean13);
		$fields['product_upc'] = pSQL($this->product_upc);
		$fields['product_reference'] = pSQL($this->product_reference);
		$fields['product_supplier_reference'] = pSQL($this->product_supplier_reference);
		$fields['product_weight'] = (float)$this->product_weight;
		$fields['tax_name'] = pSQL($this->tax_name);
		$fields['tax_rate'] = (float)$this->tax_rate;
		$fields['ecotax'] = (float)$this->ecotax;
		$fields['ecotax_tax_rate'] = (float)$this->ecotax_tax_rate;
		$fields['download_hash'] = pSQL($this->download_hash);
		$fields['download_nb'] = (int)$this->download_nb;
		$fields['download_deadline'] = pSQL($this->download_deadline);
		$fields['unit_price_tax_incl'] = (float)$this->unit_price_tax_incl;
		$fields['unit_price_tax_excl'] = (float)$this->unit_price_tax_excl;
		$fields['total_price_tax_incl'] = (float)$this->total_price_tax_incl;
		$fields['total_price_tax_excl'] = (float)$this->total_price_tax_excl;

		return $fields;
	}

	public static function getDownloadFromHash($hash)
	{
		if ($hash == '') return false;
		$sql = 'SELECT *
		FROM `'._DB_PREFIX_.'order_detail` od
		LEFT JOIN `'._DB_PREFIX_.'product_download` pd ON (od.`product_id`=pd.`id_product`)
		WHERE od.`download_hash` = \''.pSQL(strval($hash)).'\'
		AND od.`product_attribute_id` = pd.`id_product_attribute`
		AND pd.`active` = 1';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
	}

	public static function incrementDownload($id_order_detail, $increment = 1)
	{
		$sql = 'UPDATE `'._DB_PREFIX_.'order_detail`
			SET `download_nb` = `download_nb` + '.(int)$increment.'
			WHERE `id_order_detail`= '.(int)$id_order_detail.'
			LIMIT 1';
		return Db::getInstance()->execute($sql);
	}

	/**
	 * Returns the tax calculator associated to this order detail.
	 * @since 1.5.0.1
	 * @return TaxCalculator
	 */
	public function getTaxCalculator()
	{
		return OrderDetail::getTaxCalculatorStatic($this->id);
	}

	/**
	 * Return the tax calculator associated to this order_detail
	 * @since 1.5.0.1
	 * @param int $id_order_detail
	 * @return TaxCalculator
	 */
	public static function getTaxCalculatorStatic($id_order_detail)
	{
		$sql = 'SELECT t.*, d.`tax_computation_method`
				FROM `'._DB_PREFIX_.'order_detail_tax` t
				LEFT JOIN `'._DB_PREFIX_.'order_detail` d ON (d.`id_order_detail` = t.`id_order_detail`)
				WHERE d.`id_order_detail` = '.(int)$id_order_detail;

		$computation_method = 1;
		$taxes = array();
		if ($results = Db::getInstance()->executeS($sql))
		{
			foreach ($results as $result)
				$taxes[] = new Tax((int)$result['id_tax']);

			$computation_method = $result['tax_computation_method'];
		}

		return new TaxCalculator($taxes, $computation_method);
	}

	/**
	 * Save the tax calculator
	 * @since 1.5.0.1
	 * @return boolean
	 */
	public function saveTaxCalculator()
	{
		// Nothing to save
		if ($this->tax_calculator == null)
			return true;

		if (!($this->tax_calculator instanceOf TaxCalculator))
			return false;

		if (count($this->tax_calculator->taxes) == 0)
			return true;

		$values = '';
		foreach ($this->tax_calculator->getTaxesAmount($this->unit_price_tax_excl) as $id_tax => $amount)
		{
			$unit_amount = (float)Tools::ps_round($amount, 2);
			$total_amount = $unit_amount * $this->product_quantity;
			$values .= '('.(int)$this->id.','.(float)$id_tax.','.$unit_amount.','.(float)$total_amount.'),';
		}

		$values = rtrim($values, ',');
		$sql = 'INSERT INTO `'._DB_PREFIX_.'order_detail_tax` (id_order_detail, id_tax, unit_amount, total_amount)
				VALUES '.$values;

		return Db::getInstance()->execute($sql);
	}

	/**
	 * Get a detailed order list of an id_order
	 * @param int $id_order
	 * @return array
	 */
	public static function getList($id_order)
	{
		$sql = '
			SELECT *
			FROM `'._DB_PREFIX_.'order_detail`
			WHERE `id_order` = '.(int)$id_order;

		return Db::getInstance()->executeS($sql);
	}

	/*
	 * Set virtual product information
	 * @param array $product
	 */
	protected function setVirtualProductInformation($product)
	{
		// Add some informations for virtual products
		$this->download_deadline = '0000-00-00 00:00:00';
		$this->download_hash = null;

		if ($id_product_download = ProductDownload::getIdFromIdProduct((int)($product['id_product'])))
		{
			$productDownload = new ProductDownload((int)($id_product_download));
			$this->download_deadline = $productDownload->getDeadLine();
			$this->download_hash = $productDownload->getHash();

			unset($productDownload);
		}
	}

	/**
	 * Check the order state
	 * @param array $product
	 * @param int $id_order_state
	 */
	protected function checkProductStock($product, $id_order_state)
	{
		if ($id_order_state != Configuration::get('PS_OS_CANCELED') && $id_order_state != Configuration::get('PS_OS_ERROR'))
		{
			if (StockAvailable::updateQuantity($product['id_product'], $product['id_product_attribute'], -(int)$product['cart_quantity']))
				$product['stock_quantity'] -= $product['cart_quantity'];
			if ($product['stock_quantity'] < 0 && Configuration::get('PS_STOCK_MANAGEMENT'))
				$this->outOfStock = true;
			Product::updateDefaultAttribute($product['id_product']);
		}
	}

	/**
	 * Apply tax to the product
	 * @param object $order
	 * @param array $product
	 */
	protected function setProductTax(Order $order, $product)
	{
		$this->ecotax = Tools::convertPrice(floatval($product['ecotax']), intval($order->id_currency));

		// Exclude VAT
		if (!Tax::excludeTaxeOption())
		{
			$id_tax_rules = (int)Product::getIdTaxRulesGroupByIdProduct((int)$product['id_product']);

			$tax_manager = TaxManagerFactory::getManager($this->vat_address, $id_tax_rules);
			$this->tax_calculator = $tax_manager->getTaxCalculator();
		}

	    $this->ecotax_tax_rate = 0;
	    if (!empty($product['ecotax']))
	     	$this->ecotax_tax_rate = Tax::getProductEcotaxRate($order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});

	   	$this->tax_computation_method = (int)$this->tax_calculator->computation_method;
	}

	/**
	 * Set specific price of the product
	 * @param object $order
	 */
	protected function setSpecificPrice(Order $order)
	{
		$this->reduction_amount = 0.00;
		$this->reduction_percent = 0.00;

		if ($this->specificPrice)
			switch($this->specificPrice['reduction_type'])
			{
				case 'percentage':
					$this->reduction_percent = (float)$this->specificPrice['reduction'] * 100;
					break;
				case 'amount':
					$price = Tools::convertPrice($this->specificPrice['reduction'], $order->id_currency);
					$this->reduction_amount = (float)(!$this->specificPrice['id_currency'] ?
						$price : $this->specificPrice['reduction']);
			}
	}

	/**
	 * Set detailed product price to the order detail
	 * @param object $order
	 * @param object $cart
	 * @param array $product
	 */
	protected function setDetailProductPrice(Order $order, Cart $cart, $product)
	{
        $customer = new Customer((int)$order->id_customer);
        $customer_address = new Address((int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});

        $this->specificPrice = SpecificPrice::getSpecificPrice(
        	(int)$product['id_product'],
			(int)$order->id_shop,
			(int)$order->id_currency,
			(int)$customer_address->id_country,
			(int)$customer->id_default_group,
			(int)$product['cart_quantity']
		);

		$this->product_price = (float)$product['price'];
		$this->unit_price_tax_incl = (float)$product['price_wt'];
		$this->unit_price_tax_excl = (float)$product['price'];
		$this->total_price_tax_incl = (float)$product['total_wt'];
		$this->total_price_tax_excl = (float)$product['total'];

		$this->setSpecificPrice($order);

		$this->group_reduction = (float)(Group::getReduction((int)($order->id_customer)));

		if (isset($this->context->shop))
			$shop_id = $this->context->shop->getID();
		else
			$shop_id = $cart->id_shop;

		$quantityDiscount = SpecificPrice::getQuantityDiscount((int)$product['id_product'], $shop_id,
			(int)$cart->id_currency, (int)$this->vat_address->id_country,
			(int)$this->customer->id_default_group, (int)$product['cart_quantity']);

		$unitPrice = Product::getPriceStatic((int)$product['id_product'], true,
			($product['id_product_attribute'] ? intval($product['id_product_attribute']) : null),
			2, null, false, true, 1, false, (int)$order->id_customer, null, (int)$order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});

		$this->product_quantity_discount = (float)($quantityDiscount ?
			((Product::getTaxCalculationMethod((int)$order->id_customer) == PS_TAX_EXC ?
				Tools::ps_round($unitPrice, 2) : $unitPrice) - $this->tax_calculator->addTaxes($quantityDiscount['price'])) :
				0.00);

		$this->discount_quantity_applied = (($this->specificPrice && $this->specificPrice['from_quantity'] > 1) ? 1 : 0);
	}

	/**
	 * Create an order detail liable to an id_order
	 * @param object $order
	 * @param object $cart
	 * @param array $product
	 * @param int $id_order_status
	 * @param int $id_order_invoice
	 * @param bool $use_taxes set to false if you don't want to use taxes
   */
	protected function create(Order $order, Cart $cart, $product, $id_order_state, $id_order_invoice, $use_taxes = true)
	{
		if ($use_taxes)
			$this->tax_calculator = new TaxCalculator();

		$this->id = null;

		$this->product_id = (int)($product['id_product']);
		$this->product_attribute_id = (int)($product['id_product_attribute'] ? (int)($product['id_product_attribute']) : null);
		$this->product_name = pSQL($product['name'].
			((isset($product['attributes']) && $product['attributes'] != null) ?
				' - '.$product['attributes'] : ''));

		$this->product_quantity = (int)($product['cart_quantity']);
		$this->product_ean13 = empty($product['ean13']) ? null : pSQL($product['ean13']);
		$this->product_upc = empty($product['upc']) ? null : pSQL($product['upc']);
		$this->product_reference = empty($product['reference']) ? null : pSQL($product['reference']);
		$this->product_supplier_reference = empty($product['supplier_reference']) ? null : pSQL($product['supplier_reference']);
		$this->product_weight = (float)($product['id_product_attribute'] ? $product['weight_attribute'] : $product['weight']);

		$productQuantity = (int)(Product::getQuantity($this->product_id, $this->product_attribute_id));
		$this->product_quantity_in_stock = ($productQuantity - (int)($product['cart_quantity']) < 0) ?
			$productQuantity : (int)($product['cart_quantity']);

		$this->setVirtualProductInformation($product);
		$this->checkProductStock($product, $id_order_state);

		if ($use_taxes)
			$this->setProductTax($order, $product);
		$this->setDetailProductPrice($order, $cart, $product);

		// Set order invoice id
		$this->id_order_invoice = (int)$id_order_invoice;

		// Add new entry to the table
		$this->save();

		if ($use_taxes)
			$this->saveTaxCalculator();
		unset($this->tax_calculator);
	}

	/**
	 * Create a list of order detail for a specified id_order using cart
	 * @param object $order
	 * @param object $cart
	 * @param int $id_order_status
	 * @param int $id_order_invoice
	 * @param bool $use_taxes set to false if you don't want to use taxes
	*/
	public function createList(Order $order, Cart $cart, $id_order_state, $product_list, $id_order_invoice = 0, $use_taxes = true)
	{
		$this->vat_address = new Address((int)($order->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
		$this->customer = new Customer((int)($order->id_customer));

		$this->id_order = $order->id;
		$this->outOfStock = false;

		foreach ($product_list as $product)
			$this->create($order, $cart, $product, $id_order_state, $id_order_invoice, $use_taxes);

		unset($this->vat_address);
		unset($products);
		unset($this->customer);
	}

	/**
	 * Get the state of the current stock product
	 * @return array
	*/
	public function getStockState()
	{
		return $this->outOfStock;
	}
}

