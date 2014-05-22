<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license	http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderCore extends ObjectModel
{
	const ROUND_ITEM = 1;
	const ROUND_LINE = 2;
	const ROUND_TOTAL = 3;

	
	/** @var integer Delivery address id */
	public $id_address_delivery;

	/** @var integer Invoice address id */
	public $id_address_invoice;

	public $id_shop_group;

	public $id_shop;

	/** @var integer Cart id */
	public $id_cart;

	/** @var integer Currency id */
	public $id_currency;

	/** @var integer Language id */
	public $id_lang;

	/** @var integer Customer id */
	public $id_customer;

	/** @var integer Carrier id */
	public $id_carrier;

	/** @var integer Order Status id */
	public $current_state;

	/** @var string Secure key */
	public $secure_key;

	/** @var string Payment method */
	public $payment;

	/** @var string Payment module */
	public $module;

	/** @var float Currency exchange rate */
	public $conversion_rate;

	/** @var boolean Customer is ok for a recyclable package */
	public $recyclable = 1;

	/** @var boolean True if the customer wants a gift wrapping */
	public $gift = 0;

	/** @var string Gift message if specified */
	public $gift_message;

	/** @var boolean Mobile Theme */
	public $mobile_theme;

	/**
	 * @var string Shipping number
	 * @deprecated 1.5.0.4
	 * @see OrderCarrier->tracking_number
	 */
	public $shipping_number;

	/** @var float Discounts total */
	public $total_discounts;

	public $total_discounts_tax_incl;
	public $total_discounts_tax_excl;

	/** @var float Total to pay */
	public $total_paid;

	/** @var float Total to pay tax included */
	public $total_paid_tax_incl;

	/** @var float Total to pay tax excluded */
	public $total_paid_tax_excl;

	/** @var float Total really paid @deprecated 1.5.0.1 */
	public $total_paid_real;

	/** @var float Products total */
	public $total_products;

	/** @var float Products total tax included */
	public $total_products_wt;

	/** @var float Shipping total */
	public $total_shipping;

	/** @var float Shipping total tax included */
	public $total_shipping_tax_incl;

	/** @var float Shipping total tax excluded */
	public $total_shipping_tax_excl;

	/** @var float Shipping tax rate */
	public $carrier_tax_rate;

	/** @var float Wrapping total */
	public $total_wrapping;

	/** @var float Wrapping total tax included */
	public $total_wrapping_tax_incl;

	/** @var float Wrapping total tax excluded */
	public $total_wrapping_tax_excl;

	/** @var integer Invoice number */
	public $invoice_number;

	/** @var integer Delivery number */
	public $delivery_number;

	/** @var string Invoice creation date */
	public $invoice_date;

	/** @var string Delivery creation date */
	public $delivery_date;

	/** @var boolean Order validity (paid and not canceled) */
	public $valid;

	/** @var string Object creation date */
	public $date_add;

	/** @var string Object last modification date */
	public $date_upd;

	/**
	 * @var string Order reference, this reference is not unique, but unique for a payment
	 */
	public $reference;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'orders',
		'primary' => 'id_order',
		'fields' => array(
			'id_address_delivery' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_address_invoice' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_cart' => 					array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_currency' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_shop_group' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_shop' => 					array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_lang' => 					array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_customer' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_carrier' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'current_state' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'secure_key' => 				array('type' => self::TYPE_STRING, 'validate' => 'isMd5'),
			'payment' => 					array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
			'module' => 					array('type' => self::TYPE_STRING, 'validate' => 'isModuleName', 'required' => true),
			'recyclable' => 				array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'gift' => 						array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'gift_message' => 				array('type' => self::TYPE_STRING, 'validate' => 'isMessage'),
			'mobile_theme' => 				array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'total_discounts' =>			array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_discounts_tax_incl' =>	array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_discounts_tax_excl' =>	array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_paid' => 				array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
			'total_paid_tax_incl' => 		array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_paid_tax_excl' => 		array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_paid_real' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
			'total_products' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
			'total_products_wt' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
			'total_shipping' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_shipping_tax_incl' =>	array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_shipping_tax_excl' =>	array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'carrier_tax_rate' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
			'total_wrapping' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_wrapping_tax_incl' =>	array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_wrapping_tax_excl' =>	array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'shipping_number' => 			array('type' => self::TYPE_STRING, 'validate' => 'isTrackingNumber'),
			'conversion_rate' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true),
			'invoice_number' => 			array('type' => self::TYPE_INT),
			'delivery_number' => 			array('type' => self::TYPE_INT),
			'invoice_date' => 				array('type' => self::TYPE_DATE),
			'delivery_date' => 				array('type' => self::TYPE_DATE),
			'valid' => 						array('type' => self::TYPE_BOOL),
			'reference' => 					array('type' => self::TYPE_STRING),
			'date_add' => 					array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' => 					array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		),
	);

	protected $webserviceParameters = array(
		'objectMethods' => array('add' => 'addWs'),
		'objectNodeName' => 'order',
		'objectsNodeName' => 'orders',
		'fields' => array(
			'id_address_delivery' => array('xlink_resource'=> 'addresses'),
			'id_address_invoice' => array('xlink_resource'=> 'addresses'),
			'id_cart' => array('xlink_resource'=> 'carts'),
			'id_currency' => array('xlink_resource'=> 'currencies'),
			'id_lang' => array('xlink_resource'=> 'languages'),
			'id_customer' => array('xlink_resource'=> 'customers'),
			'id_carrier' => array('xlink_resource'=> 'carriers'),
			'current_state' => array('xlink_resource'=> 'order_states'),
			'module' => array('required' => true),
			'invoice_number' => array(),
			'invoice_date' => array(),
			'delivery_number' => array(),
			'delivery_date' => array(),
			'valid' => array(),
			'date_add' => array(),
			'date_upd' => array(),
		),
		'associations' => array(
			'order_rows' => array('resource' => 'order_row', 'setter' => false, 'virtual_entity' => true,
				'fields' => array(
					'id' =>  array(),
					'product_id' => array('required' => true),
					'product_attribute_id' => array('required' => true),
					'product_quantity' => array('required' => true),
					'product_name' => array('setter' => false),
					'product_reference' => array('setter' => false),
					'product_ean13' => array('setter' => false),
					'product_upc' => array('setter' => false),
					'product_price' => array('setter' => false),
					'unit_price_tax_incl' => array('setter' => false),
					'unit_price_tax_excl' => array('setter' => false),
				)),
		),

	);

	protected $_taxCalculationMethod = PS_TAX_EXC;

	protected static $_historyCache = array();

	public function __construct($id = null, $id_lang = null)
	{
		parent::__construct($id, $id_lang);

		$is_admin = (is_object(Context::getContext()->controller) && Context::getContext()->controller->controller_type == 'admin');
		if ($this->id_customer && !$is_admin)
		{
			$customer = new Customer((int)($this->id_customer));
			$this->_taxCalculationMethod = Group::getPriceDisplayMethod((int)$customer->id_default_group);
		}
		else
			$this->_taxCalculationMethod = Group::getDefaultPriceDisplayMethod();
	}

	/**
	 * @see ObjectModel::getFields()
	 * @return array
	 */
	public function getFields()
	{
		if (!$this->id_lang)
			$this->id_lang = Configuration::get('PS_LANG_DEFAULT', null, null, $this->id_shop);

		return parent::getFields();
	}

	public function add($autodate = true, $null_values = true)
	{
		if (parent::add($autodate, $null_values))
			return SpecificPrice::deleteByIdCart($this->id_cart);
		return false;
	}

	public function getTaxCalculationMethod()
	{
		return (int)($this->_taxCalculationMethod);
	}

	/* Does NOT delete a product but "cancel" it (which means return/refund/delete it depending of the case) */
	public function deleteProduct($order, $orderDetail, $quantity)
	{
		if (!(int)($this->getCurrentState()) || !validate::isLoadedObject($orderDetail))
			return false;

		if ($this->hasBeenDelivered())
		{
			if (!Configuration::get('PS_ORDER_RETURN', null, null, $this->id_shop))
				throw new PrestaShopException('PS_ORDER_RETURN is not defined in table configuration');
			$orderDetail->product_quantity_return += (int)($quantity);
			return $orderDetail->update();
		}
		elseif ($this->hasBeenPaid())
		{
			$orderDetail->product_quantity_refunded += (int)($quantity);
			return $orderDetail->update();
		}
		return $this->_deleteProduct($orderDetail, (int)$quantity);
	}

	/**
	 * This function return products of the orders
	 * It's similar to Order::getProducts but witrh similar outputs of Cart::getProducts
	 *
	 * @return array
	 */
	public function getCartProducts()
	{
		$product_id_list = array();
        	$products = $this->getProducts();
		foreach ($products as &$product)
        	{
			$product['id_product_attribute'] = $product['product_attribute_id'];
			$product['cart_quantity'] = $product['product_quantity'];
			$product_id_list[] = $this->id_address_delivery.'_'
				.$product['product_id'].'_'
				.$product['product_attribute_id'].'_'
				.(isset($product['id_customization']) ? $product['id_customization'] : '0');
	        }
	        unset($product);

		$product_list = array();
		foreach ($products as $product)
		{
			$key = $this->id_address_delivery.'_'
				.$product['id_product'].'_'
				.(isset($product['id_product_attribute']) ? $product['id_product_attribute'] : '0').'_'
				.(isset($product['id_customization']) ? $product['id_customization'] : '0');

			if (in_array($key, $product_id_list))
				$product_list[] = $product;
		}
		return $product_list;
	}

	/* DOES delete the product */
	protected function _deleteProduct($orderDetail, $quantity)
	{
		$product_price_tax_excl = $orderDetail->unit_price_tax_excl * $quantity;
		$product_price_tax_incl = $orderDetail->unit_price_tax_incl * $quantity;
		
		/* Update cart */
		$cart = new Cart($this->id_cart);
		$cart->updateQty($quantity, $orderDetail->product_id, $orderDetail->product_attribute_id, false, 'down'); // customization are deleted in deleteCustomization
		$cart->update();

		/* Update order */
		$shipping_diff_tax_incl = $this->total_shipping_tax_incl - $cart->getPackageShippingCost($this->id_carrier, true, null, $this->getCartProducts());
		$shipping_diff_tax_excl = $this->total_shipping_tax_excl - $cart->getPackageShippingCost($this->id_carrier, false, null, $this->getCartProducts());
		$this->total_shipping -= $shipping_diff_tax_incl;
		$this->total_shipping_tax_excl -= $shipping_diff_tax_excl;
		$this->total_shipping_tax_incl -= $shipping_diff_tax_incl;
		$this->total_products -= $product_price_tax_excl;
		$this->total_products_wt -= $product_price_tax_incl;
		$this->total_paid -= $product_price_tax_incl + $shipping_diff_tax_incl;
		$this->total_paid_tax_incl -= $product_price_tax_incl + $shipping_diff_tax_incl;
		$this->total_paid_tax_excl -= $product_price_tax_excl + $shipping_diff_tax_excl;
		$this->total_paid_real -= $product_price_tax_incl + $shipping_diff_tax_incl;

		$fields = array(
			'total_shipping',
			'total_shipping_tax_excl',
			'total_shipping_tax_incl',
			'total_products',
			'total_products_wt',
			'total_paid',
			'total_paid_tax_incl',
			'total_paid_tax_excl',
			'total_paid_real'
		);
		
		/* Prevent from floating precision issues (total_products has only 2 decimals) */
		foreach ($fields as $field)
			if ($this->{$field} < 0)
				$this->{$field} = 0;

		/* Prevent from floating precision issues */
		foreach ($fields as $field)
			$this->{$field} = number_format($this->{$field}, 2, '.', '');

		/* Update order detail */
		$orderDetail->product_quantity -= (int)$quantity;
		if ($orderDetail->product_quantity == 0)
		{
			if (!$orderDetail->delete())
				return false;
			if (count($this->getProductsDetail()) == 0)
			{
				$history = new OrderHistory();
				$history->id_order = (int)($this->id);
				$history->changeIdOrderState(Configuration::get('PS_OS_CANCELED'), $this);
				if (!$history->addWithemail())
					return false;
			}
			return $this->update();
		}
		else
		{
			$orderDetail->total_price_tax_incl -= $product_price_tax_incl;
			$orderDetail->total_price_tax_excl -= $product_price_tax_excl;
			$orderDetail->total_shipping_price_tax_incl -= $shipping_diff_tax_incl;
			$orderDetail->total_shipping_price_tax_excl -= $shipping_diff_tax_excl;
		}
		return $orderDetail->update() && $this->update();
	}

	public function deleteCustomization($id_customization, $quantity, $orderDetail)
	{
		if (!(int)($this->getCurrentState()))
			return false;

		if ($this->hasBeenDelivered())
			return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'customization` SET `quantity_returned` = `quantity_returned` + '.(int)($quantity).' WHERE `id_customization` = '.(int)($id_customization).' AND `id_cart` = '.(int)($this->id_cart).' AND `id_product` = '.(int)($orderDetail->product_id));
		elseif ($this->hasBeenPaid())
			return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'customization` SET `quantity_refunded` = `quantity_refunded` + '.(int)($quantity).' WHERE `id_customization` = '.(int)($id_customization).' AND `id_cart` = '.(int)($this->id_cart).' AND `id_product` = '.(int)($orderDetail->product_id));
		if (!Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'customization` SET `quantity` = `quantity` - '.(int)($quantity).' WHERE `id_customization` = '.(int)($id_customization).' AND `id_cart` = '.(int)($this->id_cart).' AND `id_product` = '.(int)($orderDetail->product_id)))
			return false;
		if (!Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customization` WHERE `quantity` = 0'))
			return false;
		return $this->_deleteProduct($orderDetail, (int)$quantity);
	}

	/**
	 * Get order history
	 *
	 * @param integer $id_lang Language id
	 * @param integer $id_order_state Filter a specific order status
	 * @param integer $no_hidden Filter no hidden status
	 * @param integer $filters Flag to use specific field filter
	 *
	 * @return array History entries ordered by date DESC
	 */
	public function getHistory($id_lang, $id_order_state = false, $no_hidden = false, $filters = 0)
	{
		if (!$id_order_state)
			$id_order_state = 0;
		
		$logable = false;
		$delivery = false;
		$paid = false;
		$shipped = false;
		if ($filters > 0)
		{
			if ($filters & OrderState::FLAG_NO_HIDDEN)
				$no_hidden = true;
			if ($filters & OrderState::FLAG_DELIVERY)
				$delivery = true;
			if ($filters & OrderState::FLAG_LOGABLE)
				$logable = true;
			if ($filters & OrderState::FLAG_PAID)
				$paid = true;
			if ($filters & OrderState::FLAG_SHIPPED)
				$shipped = true;
		}

		if (!isset(self::$_historyCache[$this->id.'_'.$id_order_state.'_'.$filters]) || $no_hidden)
		{
			$id_lang = $id_lang ? (int)($id_lang) : 'o.`id_lang`';
			$result = Db::getInstance()->executeS('
			SELECT os.*, oh.*, e.`firstname` as employee_firstname, e.`lastname` as employee_lastname, osl.`name` as ostate_name
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_history` oh ON o.`id_order` = oh.`id_order`
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON os.`id_order_state` = oh.`id_order_state`
			LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)($id_lang).')
			LEFT JOIN `'._DB_PREFIX_.'employee` e ON e.`id_employee` = oh.`id_employee`
			WHERE oh.id_order = '.(int)($this->id).'
			'.($no_hidden ? ' AND os.hidden = 0' : '').'
			'.($logable ? ' AND os.logable = 1' : '').'
			'.($delivery ? ' AND os.delivery = 1' : '').'
			'.($paid ? ' AND os.paid = 1' : '').'
			'.($shipped ? ' AND os.shipped = 1' : '').'
			'.((int)($id_order_state) ? ' AND oh.`id_order_state` = '.(int)($id_order_state) : '').'
			ORDER BY oh.date_add DESC, oh.id_order_history DESC');
			if ($no_hidden)
				return $result;
			self::$_historyCache[$this->id.'_'.$id_order_state.'_'.$filters] = $result;
		}
		return self::$_historyCache[$this->id.'_'.$id_order_state.'_'.$filters];
	}

	public function getProductsDetail()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'order_detail` od
		LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.id_product = od.product_id)
		LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop = od.id_shop)
		WHERE od.`id_order` = '.(int)($this->id));
	}

	public function getFirstMessage()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `message`
			FROM `'._DB_PREFIX_.'message`
			WHERE `id_order` = '.(int)$this->id.'
			ORDER BY `id_message`
		');
	}

	/**
	 * Marked as deprecated but should not throw any "deprecated" message
	 * This function is used in order to keep front office backward compatibility 14 -> 1.5
	 * (Order History)
	 *
	 * @deprecated
	 */
	public function setProductPrices(&$row)
	{
		$tax_calculator = OrderDetail::getTaxCalculatorStatic((int)$row['id_order_detail']);
		$row['tax_calculator'] = $tax_calculator;
		$row['tax_rate'] = $tax_calculator->getTotalRate();

		$row['product_price'] = Tools::ps_round($row['unit_price_tax_excl'], 2);
		$row['product_price_wt'] = Tools::ps_round($row['unit_price_tax_incl'], 2);

		$group_reduction = 1;
		if ($row['group_reduction'] > 0)
			$group_reduction = 1 - $row['group_reduction'] / 100;

		$row['product_price_wt_but_ecotax'] = $row['product_price_wt'] - $row['ecotax'];

		$row['total_wt'] = $row['total_price_tax_incl'];
		$row['total_price'] = $row['total_price_tax_excl'];
	}


	/**
	 * Get order products
	 *
	 * @return array Products with price, quantity (with taxe and without)
	 */
	public function getProducts($products = false, $selectedProducts = false, $selectedQty = false)
	{
		if (!$products)
			$products = $this->getProductsDetail();

		$customized_datas = Product::getAllCustomizedDatas($this->id_cart);

		$resultArray = array();
		foreach ($products as $row)
		{
			// Change qty if selected
			if ($selectedQty)
			{
				$row['product_quantity'] = 0;
				foreach ($selectedProducts as $key => $id_product)
					if ($row['id_order_detail'] == $id_product)
						$row['product_quantity'] = (int)($selectedQty[$key]);
				if (!$row['product_quantity'])
					continue;
			}

			$this->setProductImageInformations($row);
			$this->setProductCurrentStock($row);

			// Backward compatibility 1.4 -> 1.5
			$this->setProductPrices($row);

			$this->setProductCustomizedDatas($row, $customized_datas);

			// Add information for virtual product
			if ($row['download_hash'] && !empty($row['download_hash']))
			{
				$row['filename'] = ProductDownload::getFilenameFromIdProduct((int)$row['product_id']);
				// Get the display filename
				$row['display_filename'] = ProductDownload::getFilenameFromFilename($row['filename']);
			}
			
			$row['id_address_delivery'] = $this->id_address_delivery;
			
			/* Stock product */
			$resultArray[(int)$row['id_order_detail']] = $row;
		}

		if ($customized_datas)
			Product::addCustomizationPrice($resultArray, $customized_datas);

		return $resultArray;
	}

	public static function getIdOrderProduct($id_customer, $id_product)
	{
		return (int)Db::getInstance()->getValue('
			SELECT o.id_order
			FROM '._DB_PREFIX_.'orders o
			LEFT JOIN '._DB_PREFIX_.'order_detail od
				ON o.id_order = od.id_order
			WHERE o.id_customer = '.(int)$id_customer.'
				AND od.product_id = '.(int)$id_product.'
			ORDER BY o.date_add DESC
		');
	}

	protected function setProductCustomizedDatas(&$product, $customized_datas)
	{
		$product['customizedDatas'] = null;
		if (isset($customized_datas[$product['product_id']][$product['product_attribute_id']]))
			$product['customizedDatas'] = $customized_datas[$product['product_id']][$product['product_attribute_id']];
		else
			$product['customizationQuantityTotal'] = 0;
	}

	/**
	 *
	 * This method allow to add stock information on a product detail
	 *
	 * If advanced stock management is active, get physical stock of this product in the warehouse associated to the ptoduct for the current order
	 * Else get the available quantity of the product in fucntion of the shop associated to the order
	 *
	 * @param array &$product
	 */
	protected function setProductCurrentStock(&$product)
	{
		if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
			&& (int)$product['advanced_stock_management'] == 1
			&& (int)$product['id_warehouse'] > 0)
			$product['current_stock'] = StockManagerFactory::getManager()->getProductPhysicalQuantities($product['product_id'], $product['product_attribute_id'], (int)$product['id_warehouse'], true);
		else
			$product['current_stock'] = StockAvailable::getQuantityAvailableByProduct($product['product_id'], $product['product_attribute_id'], (int)$this->id_shop);
	}

	/**
	 *
	 * This method allow to add image information on a product detail
	 * @param array &$product
	 */
	protected function setProductImageInformations(&$product)
	{
		if (isset($product['product_attribute_id']) && $product['product_attribute_id'])
			$id_image = Db::getInstance()->getValue('
				SELECT image_shop.id_image
				FROM '._DB_PREFIX_.'product_attribute_image pai'.
				Shop::addSqlAssociation('image', 'pai', true).'
				WHERE id_product_attribute = '.(int)$product['product_attribute_id']);

		if (!isset($id_image) || !$id_image)
			$id_image = Db::getInstance()->getValue('
				SELECT image_shop.id_image
				FROM '._DB_PREFIX_.'image i'.
				Shop::addSqlAssociation('image', 'i', true, 'image_shop.cover=1').'
				WHERE id_product = '.(int)($product['product_id'])
			);

		$product['image'] = null;
		$product['image_size'] = null;

		if ($id_image)
			$product['image'] = new Image($id_image);
	}

	public function getTaxesAverageUsed()
	{
		return Cart::getTaxesAverageUsed((int)($this->id_cart));
	}

	/**
	 * Count virtual products in order
	 *
	 * @return int number of virtual products
	 */
	public function getVirtualProducts()
	{
		$sql = '
			SELECT `product_id`, `product_attribute_id`, `download_hash`, `download_deadline`
			FROM `'._DB_PREFIX_.'order_detail` od
			WHERE od.`id_order` = '.(int)($this->id).'
				AND `download_hash` <> \'\'';
		return Db::getInstance()->executeS($sql);
	}

	/**
	* Check if order contains (only) virtual products
	*
	* @param boolean $strict If false return true if there are at least one product virtual
	* @return boolean true if is a virtual order or false
	*
	*/
	public function isVirtual($strict = true)
	{
		$products = $this->getProducts();
		if (count($products) < 1)
			return false;
		$virtual = true;
		foreach ($products as $product)
		{
			$pd = ProductDownload::getIdFromIdProduct((int)($product['product_id']));
			if ($pd && Validate::isUnsignedInt($pd) && $product['download_hash'] && $product['display_filename'] != '')
			{
				if ($strict === false)
					return true;
			}
			else
				$virtual &= false;
		}
		return $virtual;
	}

	/**
	 * @deprecated 1.5.0.1
	 */
	public function getDiscounts($details = false)
	{
		Tools::displayAsDeprecated();
		return Order::getCartRules();
	}

	public function getCartRules()
	{	
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'order_cart_rule` ocr
		WHERE ocr.`id_order` = '.(int)$this->id);
	}

	public static function getDiscountsCustomer($id_customer, $id_cart_rule)
	{
		$cache_id = 'Order::getDiscountsCustomer_'.(int)$id_customer.'-'.(int)$id_cart_rule;
		if (!Cache::isStored($cache_id))
		{
			$result = (int)Db::getInstance()->getValue('
			SELECT COUNT(*) FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN '._DB_PREFIX_.'order_cart_rule ocr ON (ocr.id_order = o.id_order)
			WHERE o.id_customer = '.(int)$id_customer.'
			AND ocr.id_cart_rule = '.(int)$id_cart_rule);
			Cache::store($cache_id, $result);
		}
		return Cache::retrieve($cache_id);
	}

	/**
	 * Get current order status (eg. Awaiting payment, Delivered...)
	 *
	 * @return int Order status id
	 */
	public function getCurrentState()
	{
		return $this->current_state;
	}

	/**
	 * Get current order status name (eg. Awaiting payment, Delivered...)
	 *
	 * @return array Order status details
	 */
	public function getCurrentStateFull($id_lang)
	{
		return Db::getInstance()->getRow('
			SELECT os.`id_order_state`, osl.`name`, os.`logable`, os.`shipped`
			FROM `'._DB_PREFIX_.'order_state` os
			LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (osl.`id_order_state` = os.`id_order_state`)
			WHERE osl.`id_lang` = '.(int)$id_lang.' AND os.`id_order_state` = '.(int)$this->current_state);
	}

	public function hasBeenDelivered()
	{
		return count($this->getHistory((int)($this->id_lang), false, false, OrderState::FLAG_DELIVERY));
	}
	
	/**
	 * Has products returned by the merchant or by the customer?
	 */
	public function hasProductReturned()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT IFNULL(SUM(ord.product_quantity), SUM(product_quantity_return))
			FROM `'._DB_PREFIX_.'orders` o
			INNER JOIN `'._DB_PREFIX_.'order_detail` od
			ON od.id_order = o.id_order
			LEFT JOIN `'._DB_PREFIX_.'order_return_detail` ord
			ON ord.id_order_detail = od.id_order_detail
			WHERE o.id_order = '.(int)$this->id);
	}

	public function hasBeenPaid()
	{
		return count($this->getHistory((int)($this->id_lang), false, false, OrderState::FLAG_PAID));
	}

	public function hasBeenShipped()
	{
		return count($this->getHistory((int)($this->id_lang), false, false, OrderState::FLAG_SHIPPED));
	}

	public function isInPreparation()
	{
		return count($this->getHistory((int)($this->id_lang), Configuration::get('PS_OS_PREPARATION')));
	}

	/**
	 * Checks if the current order status is paid and shipped
	 *
	 * @return bool
	 */
	public function isPaidAndShipped()
	{
		$order_state = $this->getCurrentOrderState();
		if ($order_state && $order_state->paid && $order_state->shipped)
			return true;
		return false;
	}

	/**
	 * Get customer orders
	 *
	 * @param integer $id_customer Customer id
	 * @param boolean $showHiddenStatus Display or not hidden order statuses
	 * @return array Customer orders
	 */
	public static function getCustomerOrders($id_customer, $showHiddenStatus = false, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT o.*, (SELECT SUM(od.`product_quantity`) FROM `'._DB_PREFIX_.'order_detail` od WHERE od.`id_order` = o.`id_order`) nb_products
		FROM `'._DB_PREFIX_.'orders` o
		WHERE o.`id_customer` = '.(int)$id_customer.'
		GROUP BY o.`id_order`
		ORDER BY o.`date_add` DESC');
		if (!$res)
			return array();

		foreach ($res as $key => $val)
		{
			$res2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT os.`id_order_state`, osl.`name` AS order_state, os.`invoice`, os.`color` as order_state_color
				FROM `'._DB_PREFIX_.'order_history` oh
				LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
				INNER JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$context->language->id.')
			WHERE oh.`id_order` = '.(int)($val['id_order']).(!$showHiddenStatus ? ' AND os.`hidden` != 1' : '').'
				ORDER BY oh.`date_add` DESC, oh.`id_order_history` DESC
			LIMIT 1');

			if ($res2)
				$res[$key] = array_merge($res[$key], $res2[0]);

		}
		return $res;
	}

	public static function getOrdersIdByDate($date_from, $date_to, $id_customer = null, $type = null)
	{
		$sql = 'SELECT `id_order`
				FROM `'._DB_PREFIX_.'orders`
				WHERE DATE_ADD(date_upd, INTERVAL -1 DAY) <= \''.pSQL($date_to).'\' AND date_upd >= \''.pSQL($date_from).'\'
					'.Shop::addSqlRestriction()
					.($type ? ' AND '.pSQL(strval($type)).'_number != 0' : '')
					.($id_customer ? ' AND id_customer = '.(int)($id_customer) : '');
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

		$orders = array();
		foreach ($result as $order)
			$orders[] = (int)($order['id_order']);
		return $orders;
	}

	public static function getOrdersWithInformations($limit = null, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		$sql = 'SELECT *, (
					SELECT osl.`name`
					FROM `'._DB_PREFIX_.'order_state_lang` osl
					WHERE osl.`id_order_state` = o.`current_state`
					AND osl.`id_lang` = '.(int)$context->language->id.'
					LIMIT 1
				) AS `state_name`, o.`date_add` AS `date_add`, o.`date_upd` AS `date_upd`
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = o.`id_customer`)
				WHERE 1
					'.Shop::addSqlRestriction(false, 'o').'
				ORDER BY o.`date_add` DESC
				'.((int)$limit ? 'LIMIT 0, '.(int)$limit : '');
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
	}

	/**
	 * @deprecated since 1.5.0.2
	 *
	 * @static
	 * @param $date_from
	 * @param $date_to
	 * @param $id_customer
	 * @param $type
	 *
	 * @return array
	 */
	public static function getOrdersIdInvoiceByDate($date_from, $date_to, $id_customer = null, $type = null)
	{
		Tools::displayAsDeprecated();
		$sql = 'SELECT `id_order`
				FROM `'._DB_PREFIX_.'orders`
				WHERE DATE_ADD(invoice_date, INTERVAL -1 DAY) <= \''.pSQL($date_to).'\' AND invoice_date >= \''.pSQL($date_from).'\'
					'.Shop::addSqlRestriction()
					.($type ? ' AND '.pSQL(strval($type)).'_number != 0' : '')
					.($id_customer ? ' AND id_customer = '.(int)($id_customer) : '').
				' ORDER BY invoice_date ASC';
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

		$orders = array();
		foreach ($result as $order)
			$orders[] = (int)$order['id_order'];
		return $orders;
	}

	/**
	 * @deprecated 1.5.0.3
	 *
	 * @static
	 * @param $id_order_state
	 * @return array
	 */
	public static function getOrderIdsByStatus($id_order_state)
	{
		Tools::displayAsDeprecated();
		$sql = 'SELECT id_order
				FROM '._DB_PREFIX_.'orders o
				WHERE o.`current_state` = '.(int)$id_order_state.'
				'.Shop::addSqlRestriction(false, 'o').'
				ORDER BY invoice_date ASC';
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

		$orders = array();
		foreach ($result as $order)
			$orders[] = (int)($order['id_order']);
		return $orders;
	}

	/**
	 * Get product total without taxes
	 *
	 * @return Product total without taxes
	 */
	public function getTotalProductsWithoutTaxes($products = false)
	{
		return $this->total_products;
	}

	/**
	 * Get product total with taxes
	 *
	 * @return Product total with taxes
	 */
	public function getTotalProductsWithTaxes($products = false)
	{
		if ($this->total_products_wt != '0.00' && !$products)
			return $this->total_products_wt;
		/* Retro-compatibility (now set directly on the validateOrder() method) */

		if (!$products)
			$products = $this->getProductsDetail();

		$return = 0;
		foreach ($products as $row)
			$return += $row['total_price_tax_incl'];

		if (!$products)
		{
			$this->total_products_wt = $return;
			$this->update();
		}
		return $return;
	}
	
	/**
	 * Get order customer
	 * 
	 * @return Customer $customer
	 */
	public function getCustomer()
	{
		static $customer = null;
		if (is_null($customer))
			$customer = new Customer((int)$this->id_customer);
		
		return $customer;
	}

	/**
	 * Get customer orders number
	 *
	 * @param integer $id_customer Customer id
	 * @return array Customer orders number
	 */
	public static function getCustomerNbOrders($id_customer)
	{
		$sql = 'SELECT COUNT(`id_order`) AS nb
				FROM `'._DB_PREFIX_.'orders`
				WHERE `id_customer` = '.(int)$id_customer
					.Shop::addSqlRestriction();
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

		return isset($result['nb']) ? $result['nb'] : 0;
	}

	/**
	 * Get an order by its cart id
	 *
	 * @param integer $id_cart Cart id
	 * @return array Order details
	 */
	public static function getOrderByCartId($id_cart)
	{
		$sql = 'SELECT `id_order`
				FROM `'._DB_PREFIX_.'orders`
				WHERE `id_cart` = '.(int)($id_cart)
					.Shop::addSqlRestriction();
		$result = Db::getInstance()->getRow($sql);

		return isset($result['id_order']) ? $result['id_order'] : false;
	}

	/**
	 * @deprecated 1.5.0.1
	 * @see Order::addCartRule()
	 * @param int $id_cart_rule
	 * @param string $name
	 * @param float $value
	 * @return bool
	 */
	public function addDiscount($id_cart_rule, $name, $value)
	{
		Tools::displayAsDeprecated();
		return Order::addCartRule($id_cart_rule, $name, array('tax_incl' => $value, 'tax_excl' => '0.00'));
	}

	/**
	 * @since 1.5.0.1
	 * @param int $id_cart_rule
	 * @param string $name
	 * @param array $values
	 * @param int $id_order_invoice
	 * @return bool
	 */
	public function addCartRule($id_cart_rule, $name, $values, $id_order_invoice = 0, $free_shipping = null)
	{
		$order_cart_rule = new OrderCartRule();
		$order_cart_rule->id_order = $this->id;
		$order_cart_rule->id_cart_rule = $id_cart_rule;
		$order_cart_rule->id_order_invoice = $id_order_invoice;
		$order_cart_rule->name = $name;
		$order_cart_rule->value = $values['tax_incl'];
		$order_cart_rule->value_tax_excl = $values['tax_excl'];
		if ($free_shipping === null)
		{
			$cart_rule = new CartRule($id_cart_rule);
			$free_shipping = $cart_rule->free_shipping;
		}
		$order_cart_rule->free_shipping = (int)$free_shipping;
		$order_cart_rule->add();
	}

	public function getNumberOfDays()
	{
		$nbReturnDays = (int)(Configuration::get('PS_ORDER_RETURN_NB_DAYS', null, null, $this->id_shop));
		if (!$nbReturnDays)
			return true;
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT TO_DAYS(NOW()) - TO_DAYS(`delivery_date`)  AS days FROM `'._DB_PREFIX_.'orders`
		WHERE `id_order` = '.(int)($this->id));
		if ($result['days'] <= $nbReturnDays)
			return true;
		return false;
	}

	/**
	 * Can this order be returned by the client?
	 *
	 * @return bool
	 */
	public function isReturnable()
	{
		if (Configuration::get('PS_ORDER_RETURN', null, null, $this->id_shop) && $this->isPaidAndShipped())
			return $this->getNumberOfDays();

		return false;
	}

	public static function getLastInvoiceNumber()
	{
		return Db::getInstance()->getValue('
			SELECT MAX(`number`)
			FROM `'._DB_PREFIX_.'order_invoice`
		');
	}

	public static function setLastInvoiceNumber($order_invoice_id, $id_shop)
	{
		if (!$order_invoice_id)
			return false;

		$number = Configuration::get('PS_INVOICE_START_NUMBER', null, null, $id_shop);
		// If invoice start number has been set, you clean the value of this configuration
		if ($number)
			Configuration::updateValue('PS_INVOICE_START_NUMBER', false, false, null, $id_shop);

		$sql = 'UPDATE `'._DB_PREFIX_.'order_invoice` SET number =';

		if ($number)
			$sql .= (int)$number;
		else
			$sql .= '(SELECT new_number FROM (SELECT (MAX(`number`) + 1) AS new_number
			FROM `'._DB_PREFIX_.'order_invoice`) AS result)';

		$sql .=' WHERE `id_order_invoice` = '.(int)$order_invoice_id;

		return Db::getInstance()->execute($sql);
	}

	public function getInvoiceNumber($order_invoice_id)
	{
		if (!$order_invoice_id)
			return false;

		return Db::getInstance()->getValue('
			SELECT `number`
			FROM `'._DB_PREFIX_.'order_invoice`
			WHERE `id_order_invoice` = '.(int)$order_invoice_id
		);
	}

	/**
	 * This method allows to generate first invoice of the current order
	 */
	public function setInvoice($use_existing_payment = false)
	{
		if (!$this->hasInvoice())
		{
			$order_invoice = new OrderInvoice();
			$order_invoice->id_order = $this->id;
			$order_invoice->number = 0;
			$address = new Address((int)$this->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
			$carrier = new Carrier((int)$this->id_carrier);
			$tax_calculator = $carrier->getTaxCalculator($address);

			$order_invoice->total_discount_tax_excl = $this->total_discounts_tax_excl;
			$order_invoice->total_discount_tax_incl = $this->total_discounts_tax_incl;
			$order_invoice->total_paid_tax_excl = $this->total_paid_tax_excl;
			$order_invoice->total_paid_tax_incl = $this->total_paid_tax_incl;
			$order_invoice->total_products = $this->total_products;
			$order_invoice->total_products_wt = $this->total_products_wt;
			$order_invoice->total_shipping_tax_excl = $this->total_shipping_tax_excl;
			$order_invoice->total_shipping_tax_incl = $this->total_shipping_tax_incl;
			$order_invoice->shipping_tax_computation_method = $tax_calculator->computation_method;
			$order_invoice->total_wrapping_tax_excl = $this->total_wrapping_tax_excl;
			$order_invoice->total_wrapping_tax_incl = $this->total_wrapping_tax_incl;

			// Save Order invoice
			$order_invoice->add();
			$this->setLastInvoiceNumber($order_invoice->id, $this->id_shop);

			$order_invoice->saveCarrierTaxCalculator($tax_calculator->getTaxesAmount($order_invoice->total_shipping_tax_excl));

			// Update order_carrier
			$id_order_carrier = Db::getInstance()->getValue('
				SELECT `id_order_carrier`
				FROM `'._DB_PREFIX_.'order_carrier`
				WHERE `id_order` = '.(int)$order_invoice->id_order.'
				AND (`id_order_invoice` IS NULL OR `id_order_invoice` = 0)');
			
			if ($id_order_carrier)
			{
				$order_carrier = new OrderCarrier($id_order_carrier);
				$order_carrier->id_order_invoice = (int)$order_invoice->id;
				$order_carrier->update();
			}

			// Update order detail
			Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'order_detail`
				SET `id_order_invoice` = '.(int)$order_invoice->id.'
				WHERE `id_order` = '.(int)$order_invoice->id_order);

			// Update order payment
			if ($use_existing_payment)
			{
				$id_order_payments = Db::getInstance()->executeS('
					SELECT DISTINCT op.id_order_payment 
					FROM `'._DB_PREFIX_.'order_payment` op
					INNER JOIN `'._DB_PREFIX_.'orders` o ON (o.reference = op.order_reference)
					LEFT JOIN `'._DB_PREFIX_.'order_invoice_payment` oip ON (oip.id_order_payment = op.id_order_payment)					
					WHERE (oip.id_order != '.(int)$order_invoice->id_order.' OR oip.id_order IS NULL) AND o.id_order = '.(int)$order_invoice->id_order);

				if (count($id_order_payments))
				{
					foreach ($id_order_payments as $order_payment)
						Db::getInstance()->execute('
							INSERT INTO `'._DB_PREFIX_.'order_invoice_payment`
							SET
								`id_order_invoice` = '.(int)$order_invoice->id.',
								`id_order_payment` = '.(int)$order_payment['id_order_payment'].',
								`id_order` = '.(int)$order_invoice->id_order);
					// Clear cache
					Cache::clean('order_invoice_paid_*');
				}
			}

			// Update order cart rule
			Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'order_cart_rule`
				SET `id_order_invoice` = '.(int)$order_invoice->id.'
				WHERE `id_order` = '.(int)$order_invoice->id_order);

			// Keep it for backward compatibility, to remove on 1.6 version
			$this->invoice_date = $order_invoice->date_add;
			$this->invoice_number = $this->getInvoiceNumber($order_invoice->id);
			$this->update();
		}
	}

	public function setDeliveryNumber($order_invoice_id, $id_shop)
	{
		if (!$order_invoice_id)
			return false;

		$id_shop = shop::getTotalShops() > 1 ? $id_shop : null;

		$number = Configuration::get('PS_DELIVERY_NUMBER', null, null, $id_shop);
		// If delivery slip start number has been set, you clean the value of this configuration
		if ($number)
			Configuration::updateValue('PS_DELIVERY_NUMBER', false, false, null, $id_shop);

		$sql = 'UPDATE `'._DB_PREFIX_.'order_invoice` SET delivery_number =';

		if ($number)
			$sql .= (int)$number;
		else
			$sql .= '(SELECT new_number FROM (SELECT (MAX(`delivery_number`) + 1) AS new_number
			FROM `'._DB_PREFIX_.'order_invoice`) AS result)';

		$sql .=' WHERE `id_order_invoice` = '.(int)$order_invoice_id;

		return Db::getInstance()->execute($sql);
	}

	public function getDeliveryNumber($order_invoice_id)
	{
		if (!$order_invoice_id)
			return false;

		return Db::getInstance()->getValue('
			SELECT `delivery_number`
			FROM `'._DB_PREFIX_.'order_invoice`
			WHERE `id_order_invoice` = '.(int)$order_invoice_id
		);
	}

	public function setDelivery()
	{
		// Get all invoice
		$order_invoice_collection = $this->getInvoicesCollection();
		foreach ($order_invoice_collection as $order_invoice)
		{
			if ($order_invoice->delivery_number)
				continue;
				
			// Set delivery number on invoice
			$order_invoice->delivery_number = 0;
			$order_invoice->delivery_date = date('Y-m-d H:i:s');
			// Update Order Invoice
			$order_invoice->update();
			$this->setDeliveryNumber($order_invoice->id, $this->id_shop);
			$this->delivery_number = $this->getDeliveryNumber($order_invoice->id);
		}

		// Keep it for backward compatibility, to remove on 1.6 version
		// Set delivery date
		$this->delivery_date = date('Y-m-d H:i:s');
		// Update object
		$this->update();
	}

	public static function getByDelivery($id_delivery)
	{
		$sql = 'SELECT id_order
				FROM `'._DB_PREFIX_.'orders`
				WHERE `delivery_number` = '.(int)($id_delivery).'
				'.Shop::addSqlRestriction();
		$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
		return new Order((int)($res['id_order']));
	}

	/**
	 * Get a collection of orders using reference
	 * 
	 * @since 1.5.0.14
	 * 
	 * @param string $reference
	 * @return PrestaShopCollection Collection of Order
	 */
	public static function getByReference($reference)
	{
		$orders = new PrestaShopCollection('Order');
		$orders->where('reference', '=', $reference);
		return $orders;
	}

	public function getTotalWeight()
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT SUM(product_weight * product_quantity)
		FROM '._DB_PREFIX_.'order_detail
		WHERE id_order = '.(int)($this->id));
		return (float)($result);
	}

	/**
	 *
	 * @param int $id_invoice
	 * @deprecated 1.5.0.1
	 */
	public static function getInvoice($id_invoice)
	{
		Tools::displayAsDeprecated();
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `invoice_number`, `id_order`
		FROM `'._DB_PREFIX_.'orders`
		WHERE invoice_number = '.(int)($id_invoice));
	}

	public function isAssociatedAtGuest($email)
	{
		if (!$email)
			return false;
		$sql = 'SELECT COUNT(*)
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = o.`id_customer`)
				WHERE o.`id_order` = '.(int)$this->id.'
					AND c.`email` = \''.pSQL($email).'\'
					AND c.`is_guest` = 1
					'.Shop::addSqlRestriction(false, 'c');
		return (bool)Db::getInstance()->getValue($sql);
	}

	/**
	 * @param int $id_order
	 * @param int $id_customer optionnal
	 * @return int id_cart
	 */
	public static function getCartIdStatic($id_order, $id_customer = 0)
	{
		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_cart`
			FROM `'._DB_PREFIX_.'orders`
			WHERE `id_order` = '.(int)$id_order.'
			'.($id_customer ? 'AND `id_customer` = '.(int)$id_customer : ''));
	}

	public function getWsOrderRows()
	{
		$query = '
			SELECT 
			`id_order_detail` as `id`, 
			`product_id`, 
			`product_price`, 
			`id_order`, 
			`product_attribute_id`, 
			`product_quantity`, 
			`product_name`, 
			`product_reference`,
			`product_ean13`,
			`product_upc`,
			`unit_price_tax_incl`, 
			`unit_price_tax_excl`
			FROM `'._DB_PREFIX_.'order_detail`
			WHERE id_order = '.(int)$this->id;
		$result = Db::getInstance()->executeS($query);
		return $result;
	}

	/** Set current order status
	 * @param int $id_order_state
	 * @param int $id_employee (/!\ not optional except for Webservice.
	 */
	public function setCurrentState($id_order_state, $id_employee = 0)
	{
		if (empty($id_order_state))
			return false;
		$history = new OrderHistory();
		$history->id_order = (int)$this->id;
		$history->id_employee = (int)$id_employee;
		$history->changeIdOrderState((int)$id_order_state, $this);
		$res = Db::getInstance()->getRow('
			SELECT `invoice_number`, `invoice_date`, `delivery_number`, `delivery_date`
			FROM `'._DB_PREFIX_.'orders`
			WHERE `id_order` = '.(int)$this->id);
		$this->invoice_date = $res['invoice_date'];
		$this->invoice_number = $res['invoice_number'];
		$this->delivery_date = $res['delivery_date'];
		$this->delivery_number = $res['delivery_number'];
		$this->update();

		$history->addWithemail();		
	}

	public function addWs($autodate = true, $nullValues = false)
	{
		$paymentModule = Module::getInstanceByName($this->module);
		$customer = new Customer($this->id_customer);
		$paymentModule->validateOrder($this->id_cart, Configuration::get('PS_OS_WS_PAYMENT'), $this->total_paid, $this->payment, null, array(), null, false, $customer->secure_key);
		$this->id = $paymentModule->currentOrder;
		return true;
	}

	public function deleteAssociations()
	{
		return (Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'order_detail`
				WHERE `id_order` = '.(int)($this->id)) !== false);
	}

	/**
	 * This method return the ID of the previous order
	 * @since 1.5.0.1
	 * @return int
	 */
	public function getPreviousOrderId()
	{
		return Db::getInstance()->getValue('
			SELECT id_order
			FROM '._DB_PREFIX_.'orders
			WHERE id_order < '.(int)$this->id.'
			ORDER BY id_order DESC');
	}

	/**
	 * This method return the ID of the next order
	 * @since 1.5.0.1
	 * @return int
	 */
	public function getNextOrderId()
	{
		return Db::getInstance()->getValue('
		SELECT id_order
		FROM '._DB_PREFIX_.'orders
		WHERE id_order > '.(int)$this->id.'
		ORDER BY id_order ASC');
	}

	/**
	 * Get the an order detail list of the current order
	 * @return array
	 */
	public function getOrderDetailList()
	{
		return OrderDetail::getList($this->id);
	}

	/**
	 * Gennerate a unique reference for orders generated with the same cart id
	 * This references, is usefull for check payment
	 *
	 * @return String
	 */
	public static function generateReference()
	{
		return strtoupper(Tools::passwdGen(9, 'NO_NUMERIC'));
	}

	public function orderContainProduct($id_product)
	{
		$product_list = $this->getOrderDetailList();
		foreach ($product_list as $product)
			if ($product['product_id'] == (int)$id_product)
				return true;
		return false;
	}
	/**
	 * This method returns true if at least one order details uses the
	 * One After Another tax computation method.
	 *
	 * @since 1.5.0.1
	 * @return boolean
	 */
	public function useOneAfterAnotherTaxComputationMethod()
	{
		// if one of the order details use the tax computation method the display will be different
		return Db::getInstance()->getValue('
		SELECT od.`tax_computation_method`
		FROM `'._DB_PREFIX_.'order_detail_tax` odt
		LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (od.`id_order_detail` = odt.`id_order_detail`)
		WHERE od.`id_order` = '.(int)$this->id.'
		AND od.`tax_computation_method` = '.(int)TaxCalculator::ONE_AFTER_ANOTHER_METHOD
		);
	}

	/**
	 * This method allows to get all Order Payment for the current order
	 * @since 1.5.0.1
	 * @return PrestaShopCollection Collection of OrderPayment
	 */
	public function getOrderPaymentCollection()
	{
		$order_payments = new PrestaShopCollection('OrderPayment');
		$order_payments->where('order_reference', '=', $this->reference);
		return $order_payments;
	}

	/**
	 *
	 * This method allows to add a payment to the current order
	 * @since 1.5.0.1
	 * @param float $amount_paid
	 * @param string $payment_method
	 * @param string $payment_transaction_id
	 * @param Currency $currency
	 * @param string $date
	 * @param OrderInvoice $order_invoice
	 * @return bool
	 */
	public function addOrderPayment($amount_paid, $payment_method = null, $payment_transaction_id = null, $currency = null, $date = null, $order_invoice = null)
	{
		$order_payment = new OrderPayment();
		$order_payment->order_reference = $this->reference;
		$order_payment->id_currency = ($currency ? $currency->id : $this->id_currency);
		// we kept the currency rate for historization reasons
		$order_payment->conversion_rate = ($currency ? $currency->conversion_rate : 1);
		// if payment_method is define, we used this
		$order_payment->payment_method = ($payment_method ? $payment_method : $this->payment);
		$order_payment->transaction_id = $payment_transaction_id;
		$order_payment->amount = $amount_paid;
		$order_payment->date_add = ($date ? $date : null);

		// Update total_paid_real value for backward compatibility reasons
		if ($order_payment->id_currency == $this->id_currency)
			$this->total_paid_real += $order_payment->amount;
		else
			$this->total_paid_real += Tools::ps_round(Tools::convertPrice($order_payment->amount, $order_payment->id_currency, false), 2);

		// We put autodate parameter of add method to true if date_add field is null
		$res = $order_payment->add(is_null($order_payment->date_add)) && $this->update();
		
		if (!$res)
			return false;
	
		if (!is_null($order_invoice))
		{
			$res = Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'order_invoice_payment` (`id_order_invoice`, `id_order_payment`, `id_order`)
			VALUES('.(int)$order_invoice->id.', '.(int)$order_payment->id.', '.(int)$this->id.')');

			// Clear cache
			Cache::clean('order_invoice_paid_*');
		}
		
		return $res;
	}

	/**
	 * Returns the correct product taxes breakdown.
	 *
	 * Get all documents linked to the current order
	 *
	 * @since 1.5.0.1
	 * @return array
	 */
	public function getDocuments()
	{
		$invoices = $this->getInvoicesCollection()->getResults();
		$delivery_slips = $this->getDeliverySlipsCollection()->getResults();
		// @TODO review
		foreach ($delivery_slips as $delivery)
		{
			$delivery->is_delivery = true;
			$delivery->date_add = $delivery->delivery_date;
		}
		$order_slips = $this->getOrderSlipsCollection()->getResults();

		$documents = array_merge($invoices, $order_slips, $delivery_slips);
		usort($documents, array('Order', 'sortDocuments'));

		return $documents;
	}

	public function getReturn()
	{
		return OrderReturn::getOrdersReturn($this->id_customer, $this->id);
	}

	/**
	 * @return array return all shipping method for the current order
	 * state_name sql var is now deprecated - use order_state_name for the state name and carrier_name for the carrier_name
	 */
	public function getShipping()
	{
		return Db::getInstance()->executeS('
			SELECT DISTINCT oc.`id_order_invoice`, oc.`weight`, oc.`shipping_cost_tax_excl`, oc.`shipping_cost_tax_incl`, c.`url`, oc.`id_carrier`, c.`name` as `carrier_name`, oc.`date_add`, "Delivery" as `type`, "true" as `can_edit`, oc.`tracking_number`, oc.`id_order_carrier`, osl.`name` as order_state_name, c.`name` as state_name
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_history` oh
				ON (o.`id_order` = oh.`id_order`)
			LEFT JOIN `'._DB_PREFIX_.'order_carrier` oc
				ON (o.`id_order` = oc.`id_order`)
			LEFT JOIN `'._DB_PREFIX_.'carrier` c
				ON (oc.`id_carrier` = c.`id_carrier`)
			LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl
				ON (oh.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)Context::getContext()->language->id.')
			WHERE o.`id_order` = '.(int)$this->id.'
			GROUP BY c.id_carrier');
	}


	/**
	 *
	 * Get all order_slips for the current order
	 * @since 1.5.0.2
	 * @return PrestaShopCollection Collection of OrderSlip
	 */
	public function getOrderSlipsCollection()
	{
		$order_slips = new PrestaShopCollection('OrderSlip');
		$order_slips->where('id_order', '=', $this->id);
		return $order_slips;
	}

	/**
	 *
	 * Get all invoices for the current order
	 * @since 1.5.0.1
	 * @return PrestaShopCollection Collection of OrderInvoice
	 */
	public function getInvoicesCollection()
	{
		$order_invoices = new PrestaShopCollection('OrderInvoice');
		$order_invoices->where('id_order', '=', $this->id);
		return $order_invoices;
	}

	/**
	 *
	 * Get all delivery slips for the current order
	 * @since 1.5.0.2
	 * @return PrestaShopCollection Collection of OrderInvoice
	 */
	public function getDeliverySlipsCollection()
	{
		$order_invoices = new PrestaShopCollection('OrderInvoice');
		$order_invoices->where('id_order', '=', $this->id);
		$order_invoices->where('delivery_number', '!=', '0');
		return $order_invoices;
	}

	/**
	 * Get all not paid invoices for the current order
	 * @since 1.5.0.2
	 * @return PrestaShopCollection Collection of Order invoice not paid
	 */
	public function getNotPaidInvoicesCollection()
	{
		$invoices = $this->getInvoicesCollection();
		foreach ($invoices as $key => $invoice)
			if ($invoice->isPaid())
				unset($invoices[$key]);
		return $invoices;
	}

	/**
	 * Get total paid
	 *
	 * @since 1.5.0.1
	 * @param Currency $currency currency used for the total paid of the current order
	 * @return float amount in the $currency
	 */
	public function getTotalPaid($currency = null)
	{
		if (!$currency)
			$currency = new Currency($this->id_currency);

		$total = 0;
		// Retrieve all payments
		$payments = $this->getOrderPaymentCollection();
		foreach ($payments as $payment)
		{
			if ($payment->id_currency == $currency->id)
				$total += $payment->amount;
			else
			{
				$amount = Tools::convertPrice($payment->amount, $payment->id_currency, false);
				if ($currency->id == Configuration::get('PS_DEFAULT_CURRENCY', null, null, $this->id_shop))
					$total += $amount;
				else
					$total += Tools::convertPrice($amount, $currency->id, true);
			}
		}

		return Tools::ps_round($total, 2);
	}

	/**
	 * Get the sum of total_paid_tax_incl of the orders with similar reference
	 *
	 * @since 1.5.0.1
	 * @return float
	 */
	public function getOrdersTotalPaid()
	{
		return Db::getInstance()->getValue('
			SELECT SUM(total_paid_tax_incl)
			FROM `'._DB_PREFIX_.'orders`
			WHERE `reference` = \''.pSQL($this->reference).'\'
			AND `id_cart` = '.(int)$this->id_cart
		);
	}

	/**
	 *
	 * This method allows to change the shipping cost of the current order
	 * @since 1.5.0.1
	 * @param float $amount
	 * @return bool
	 */
	public function updateShippingCost($amount)
	{
		$difference = $amount - $this->total_shipping;
		// if the current amount is same as the new, we return true
		if ($difference == 0)
			return true;

		// update the total_shipping value
		$this->total_shipping = $amount;
		// update the total of this order
		$this->total_paid += $difference;

		// update database
		return $this->update();
	}

	/**
	 * Returns the correct product taxes breakdown.
	 *
	 * @since 1.5.0.1
	 * @return array
	 */
	public function getProductTaxesBreakdown()
	{
		$tmp_tax_infos = array();
		if ($this->useOneAfterAnotherTaxComputationMethod())
		{
			// sum by taxes
			$taxes_by_tax = Db::getInstance()->executeS('
			SELECT odt.`id_order_detail`, t.`name`, t.`rate`, SUM(`total_amount`) AS `total_amount`
			FROM `'._DB_PREFIX_.'order_detail_tax` odt
			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = odt.`id_tax`)
			LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (od.`id_order_detail` = odt.`id_order_detail`)
			WHERE od.`id_order` = '.(int)$this->id.'
			GROUP BY odt.`id_tax`
			');

			// format response
			$tmp_tax_infos = array();
			foreach ($taxes_infos as $tax_infos)
			{
				$tmp_tax_infos[$tax_infos['rate']]['total_amount'] = $tax_infos['tax_amount'];
				$tmp_tax_infos[$tax_infos['rate']]['name'] = $tax_infos['name'];
			}
		}
		else
		{
			// sum by order details in order to retrieve real taxes rate
			$taxes_infos = Db::getInstance()->executeS('
			SELECT odt.`id_order_detail`, t.`rate` AS `name`, SUM(od.`total_price_tax_excl`) AS total_price_tax_excl, SUM(t.`rate`) AS rate, SUM(`total_amount`) AS `total_amount`
			FROM `'._DB_PREFIX_.'order_detail_tax` odt
			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = odt.`id_tax`)
			LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (od.`id_order_detail` = odt.`id_order_detail`)
			WHERE od.`id_order` = '.(int)$this->id.'
			GROUP BY odt.`id_order_detail`
			');

			// sum by taxes
			$tmp_tax_infos = array();
			foreach ($taxes_infos as $tax_infos)
			{
				if (!isset($tmp_tax_infos[$tax_infos['rate']]))
					$tmp_tax_infos[$tax_infos['rate']] = array('total_amount' => 0,
																'name' => 0,
																'total_price_tax_excl' => 0);

				$tmp_tax_infos[$tax_infos['rate']]['total_amount'] += $tax_infos['total_amount'];
				$tmp_tax_infos[$tax_infos['rate']]['name'] = $tax_infos['name'];
				$tmp_tax_infos[$tax_infos['rate']]['total_price_tax_excl'] += $tax_infos['total_price_tax_excl'];
			}
		}

		return $tmp_tax_infos;
	}

	/**
	 * Returns the shipping taxes breakdown
	 *
	 * @since 1.5.0.1
	 * @return array
	 */
	public function getShippingTaxesBreakdown()
	{
		$taxes_breakdown = array();

		$shipping_tax_amount = $this->total_shipping_tax_incl - $this->total_shipping_tax_excl;

		if ($shipping_tax_amount > 0)
			$taxes_breakdown[] = array(
				'rate' => $this->carrier_tax_rate,
				'total_amount' => $shipping_tax_amount
			);

		return $taxes_breakdown;
	}

	/**
	 * Returns the wrapping taxes breakdown
	 * @todo

	 * @since 1.5.0.1
	 * @return array
	 */
	public function getWrappingTaxesBreakdown()
	{
		$taxes_breakdown = array();
		return $taxes_breakdown;
	}

	/**
	 * Returns the ecotax taxes breakdown
	 *
	 * @since 1.5.0.1
	 * @return array
	 */
	public function getEcoTaxTaxesBreakdown()
	{
		return Db::getInstance()->executeS('
		SELECT `ecotax_tax_rate`, SUM(`ecotax`) as `ecotax_tax_excl`, SUM(`ecotax`) as `ecotax_tax_incl`
		FROM `'._DB_PREFIX_.'order_detail`
		WHERE `id_order` = '.(int)$this->id
		);
	}

	/**
	 *
	 * Has invoice return true if this order has already an invoice
	 * @return bool
	 */
	public function hasInvoice()
	{
		if (Db::getInstance()->getRow('
				SELECT *
				FROM `'._DB_PREFIX_.'order_invoice`
				WHERE `id_order` =  '.(int)$this->id))
			return true;
		return false;
	}

	/**
	 * Get warehouse associated to the order
	 *
	 * return array List of warehouse
	 */
	public function getWarehouseList()
	{
		$results = Db::getInstance()->executeS('
			SELECT id_warehouse
			FROM `'._DB_PREFIX_.'order_detail`
			WHERE `id_order` =  '.(int)$this->id.'
			GROUP BY id_warehouse');
		if (!$results)
			return array();

			$warehouse_list = array();
		foreach ($results as $row)
			$warehouse_list[] = $row['id_warehouse'];

		return $warehouse_list;
	}

	/**
	 * @since 1.5.0.4
	 * @return OrderState or null if Order haven't a state
	 */
	public function getCurrentOrderState()
	{
		if ($this->current_state)
			return new OrderState($this->current_state);
		return null;
	}

	/**
	 * @see ObjectModel::getWebserviceObjectList()
	 */
	public function getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit)
	{
		$sql_filter .= Shop::addSqlRestriction(Shop::SHARE_ORDER, 'main');
		return parent::getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit);
	}
	
	/**
	 * Get all other orders with the same reference
	 * 
	 * @since 1.5.0.13
	 */
	public function getBrother()
	{
		$collection = new PrestaShopCollection('order');
		$collection->where('reference', '=', $this->reference);
		$collection->where('id_order', '<>', $this->id);
		return $collection;
	}
	
	/**
	 * Get a collection of order payments
	 * 
	 * @since 1.5.0.13
	 */
	public function getOrderPayments()
	{
		return OrderPayment::getByOrderReference($this->reference);
	}

	/**
	 * Return a unique reference like : GWJTHMZUN#2
	 * 
	 * With multishipping, order reference are the same for all orders made with the same cart
	 * in this case this method suffix the order reference by a # and the order number
	 * 
	 * @since 1.5.0.14
	 */
	public function getUniqReference()
	{
		$query = new DbQuery();
		$query->select('MIN(id_order) as min, MAX(id_order) as max');
		$query->from('orders');
		$query->where('id_cart = '.(int)$this->id_cart);
		
		$order = Db::getInstance()->getRow($query);
		
		if ($order['min'] == $order['max'])
			return $this->reference;
		else
			return $this->reference.'#'.($this->id + 1 - $order['min']);
	}
	
	/**
	 * Return a unique reference like : GWJTHMZUN#2
	 * 
	 * With multishipping, order reference are the same for all orders made with the same cart
	 * in this case this method suffix the order reference by a # and the order number
	 * 
	 * @since 1.5.0.14
	 */
	public static function getUniqReferenceOf($id_order)
	{
		$order = new Order($id_order);
		return $order->getUniqReference();
	}
	
	/**
	 * Return id of carrier
	 * 
	 * Get id of the carrier used in order
	 * 
	 * @since 1.5.5.0
	 */	
	public function getIdOrderCarrier()	
	{
		return (int)Db::getInstance()->getValue('
				SELECT `id_order_carrier`
				FROM `'._DB_PREFIX_.'order_carrier`
				WHERE `id_order` = '.(int)$this->id);
	}

	public static function sortDocuments($a, $b)
	{
		if ($a->date_add == $b->date_add)
			return 0;
		return ($a->date_add < $b->date_add) ? -1 : 1;
	}
}
