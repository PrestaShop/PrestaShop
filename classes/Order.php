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

class OrderCore extends ObjectModel
{
	/** @var integer Delivery address id */
	public 		$id_address_delivery;

	/** @var integer Invoice address id */
	public 		$id_address_invoice;

	/** @var integer Cart id */
	public 		$id_cart;

	/** @var integer Currency id */
	public 		$id_currency;

	/** @var integer Language id */
	public 		$id_lang;

	/** @var integer Customer id */
	public 		$id_customer;

	/** @var integer Carrier id */
	public 		$id_carrier;

	/** @var string Secure key */
	public		$secure_key;

	/** @var string Payment method id */
	public 		$payment;

	/** @var string Payment module */
	public 		$module;

	/** @var float Currency conversion rate */
	public 		$conversion_rate;

	/** @var boolean Customer is ok for a recyclable package */
	public		$recyclable = 1;

	/** @var boolean True if the customer wants a gift wrapping */
	public		$gift = 0;

	/** @var string Gift message if specified */
	public 		$gift_message;

	/** @var string Shipping number */
	public 		$shipping_number;

	/** @var float Discounts total */
	public 		$total_discounts;

	/** @var float Total to pay */
	public 		$total_paid;

	/** @var float Total really paid */
	public 		$total_paid_real;

	/** @var float Products total */
	public 		$total_products;

	/** @var float Products total tax excluded */
	public 		$total_products_wt;

	/** @var float Shipping total */
	public 		$total_shipping;

	/** @var float Shipping tax rate */
	public 		$carrier_tax_rate;

	/** @var float Wrapping total */
	public 		$total_wrapping;

	/** @var integer Invoice number */
	public 		$invoice_number;

	/** @var integer Delivery number */
	public 		$delivery_number;

	/** @var string Invoice creation date */
	public 		$invoice_date;

	/** @var string Delivery creation date */
	public 		$delivery_date;

	/** @var boolean Order validity (paid and not canceled) */
	public 		$valid;

	/** @var string Object creation date */
	public 		$date_add;

	/** @var string Object last modification date */
	public 		$date_upd;

	protected $tables = array ('orders');

	protected	$fieldsRequired = array('conversion_rate', 'id_address_delivery', 'id_address_invoice', 'id_cart', 'id_currency', 'id_lang', 'id_customer', 'id_carrier', 'payment', 'total_paid', 'total_paid_real', 'total_products', 'total_products_wt');
	protected	$fieldsSize = array('payment' => 32);
	protected	$fieldsValidate = array(
		'id_address_delivery' => 'isUnsignedId',
		'id_address_invoice' => 'isUnsignedId',
		'id_cart' => 'isUnsignedId',
		'id_currency' => 'isUnsignedId',
		'id_lang' => 'isUnsignedId',
		'id_customer' => 'isUnsignedId',
		'id_carrier' => 'isUnsignedId',
		'secure_key' => 'isMd5',
		'payment' => 'isGenericName',
		'recyclable' => 'isBool',
		'gift' => 'isBool',
		'gift_message' => 'isMessage',
		'total_discounts' => 'isPrice',
		'total_paid' => 'isPrice',
		'total_paid_real' => 'isPrice',
		'total_products' => 'isPrice',
		'total_products_wt' => 'isPrice',
		'total_shipping' => 'isPrice',
		'carrier_tax_rate' => 'isFloat',
		'total_wrapping' => 'isPrice',
		'shipping_number' => 'isUrl',
		'conversion_rate' => 'isFloat'
	);

	protected	$webserviceParameters = array(
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
			'module' => array(), // mettre en requis
			'invoice_number' => array(),
			'delivery_number' => array(),
			'invoice_date' => array(),
			'delivery_date' => array(),
			'valid' => array(),
			'current_state' => array('getter' => 'getCurrentState', 'setter' => 'setCurrentState', 'xlink_resource'=> 'order_states'),
			'date_add' => array(),
			'date_upd' => array(),
		),
		'associations' => array(
			'order_rows' => array('resource' => 'order_row', 'setter' => null,
				'fields' => array(
					'id' =>  array(),
					'product_id' => array('required' => true),
					'product_attribute_id' => array('required' => true),
					'product_quantity' => array('required' => true),
					'product_name' => array('setter' => null),
					'product_price' => array('setter' => null),
			)),
		),

	);

	/* MySQL does not allow 'order' for a table name */
	protected 	$table = 'orders';
	protected 	$identifier = 'id_order';
	protected		$_taxCalculationMethod = PS_TAX_EXC;

	protected static $_historyCache = array();

	public function getFields()
	{
		parent::validateFields();

		$fields['id_address_delivery'] = (int)($this->id_address_delivery);
		$fields['id_address_invoice'] = (int)($this->id_address_invoice);
		$fields['id_cart'] = (int)($this->id_cart);
		$fields['id_currency'] = (int)($this->id_currency);
		$fields['id_lang'] = (int)($this->id_lang);
		$fields['id_customer'] = (int)($this->id_customer);
		$fields['id_carrier'] = (int)($this->id_carrier);
		$fields['secure_key'] = pSQL($this->secure_key);
		$fields['payment'] = pSQL($this->payment);
		$fields['module'] = pSQL($this->module);
		$fields['conversion_rate'] = (float)($this->conversion_rate);
		$fields['recyclable'] = (int)($this->recyclable);
		$fields['gift'] = (int)($this->gift);
		$fields['gift_message'] = pSQL($this->gift_message);
		$fields['shipping_number'] = pSQL($this->shipping_number);
		$fields['total_discounts'] = (float)($this->total_discounts);
		$fields['total_paid'] = (float)($this->total_paid);
		$fields['total_paid_real'] = (float)($this->total_paid_real);
		$fields['total_products'] = (float)($this->total_products);
		$fields['total_products_wt'] = (float)($this->total_products_wt);
		$fields['total_shipping'] = (float)($this->total_shipping);
		$fields['carrier_tax_rate'] = (float)($this->carrier_tax_rate);
		$fields['total_wrapping'] = (float)($this->total_wrapping);
		$fields['invoice_number'] = (int)($this->invoice_number);
		$fields['delivery_number'] = (int)($this->delivery_number);
		$fields['invoice_date'] = pSQL($this->invoice_date);
		$fields['delivery_date'] = pSQL($this->delivery_date);
		$fields['valid'] = (int)($this->valid) ? 1 : 0;
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);

		return $fields;
	}

	public function __construct($id = NULL, $id_lang = NULL)
	{
		parent::__construct($id, $id_lang);
		if ($this->id_customer)
		{
			$customer = new Customer((int)($this->id_customer));
			$this->_taxCalculationMethod = Group::getPriceDisplayMethod((int)($customer->id_default_group));
		}
		else
			$this->_taxCalculationMethod = Group::getDefaultPriceDisplayMethod();
	}

	public function getTaxCalculationMethod()
	{
		return (int)($this->_taxCalculationMethod);
	}

	/* Does NOT delete a product but "cancel" it (which means return/refund/delete it depending of the case) */
	public function deleteProduct($order, $orderDetail, $quantity)
	{
		if (!$currentStatus = (int)($this->getCurrentState()))
			return false;

		if ($this->hasBeenDelivered())
		{
			if (!Configuration::get('PS_ORDER_RETURN'))
				die(Tools::displayError());
			$orderDetail->product_quantity_return += (int)($quantity);
			return $orderDetail->update();
		}
		elseif ($this->hasBeenPaid())
		{
			$orderDetail->product_quantity_refunded += (int)($quantity);
			return $orderDetail->update();
		}
		return $this->_deleteProduct($orderDetail, (int)($quantity));
	}

	/* DOES delete the product */
	protected function _deleteProduct($orderDetail, $quantity)
	{
		$price = $orderDetail->product_price * (1 + $orderDetail->tax_rate * 0.01);
		if ($orderDetail->reduction_percent != 0.00)
			$reduction_amount = $price * $orderDetail->reduction_percent / 100;
		elseif ($orderDetail->reduction_amount != '0.000000')
			$reduction_amount = Tools::ps_round($orderDetail->reduction_amount, 2);
		if (isset($reduction_amount) AND $reduction_amount)
			$price = Tools::ps_round($price - $reduction_amount, 2);
		$productPriceWithoutTax = number_format($price / (1 + $orderDetail->tax_rate * 0.01), 2, '.', '');
		$price += Tools::ps_round($orderDetail->ecotax * (1 + $orderDetail->ecotax_tax_rate / 100), 2);
		$productPrice = number_format($quantity * $price, 2, '.', '');
		/* Update cart */
		$cart = new Cart($this->id_cart);
		$cart->updateQty($quantity, $orderDetail->product_id, $orderDetail->product_attribute_id, false, 'down'); // customization are deleted in deleteCustomization
		$cart->update();

		/* Update order */
		$shippingDiff = $this->total_shipping - $cart->getOrderShippingCost();
		$this->total_products -= $productPriceWithoutTax;
		$this->total_products_wt -= $productPrice;
		$this->total_shipping = $cart->getOrderShippingCost();
		/* It's temporary fix for 1.3 version... */
		if ($orderDetail->product_quantity_discount != '0.000000')
			$this->total_paid -= ($productPrice + $shippingDiff);
		else
			$this->total_paid = $cart->getOrderTotal();
		$this->total_paid_real -= ($productPrice + $shippingDiff);

		/* Prevent from floating precision issues (total_products has only 2 decimals) */
		if ($this->total_products < 0)
			$this->total_products = 0;

		/* Prevent from floating precision issues */
		$this->total_paid = number_format($this->total_paid, 2, '.', '');
		$this->total_paid_real = number_format($this->total_paid_real, 2, '.', '');
		$this->total_products = number_format($this->total_products, 2, '.', '');
		$this->total_products_wt = number_format($this->total_products_wt, 2, '.', '');

		/* Update order detail */
		$orderDetail->product_quantity -= (int)($quantity);

		if (!$orderDetail->product_quantity)
		{
			if (!$orderDetail->delete())
				return false;
			if (count($this->getProductsDetail()) == 0)
			{
				global $cookie;
				$history = new OrderHistory();
				$history->id_order = (int)($this->id);
				$history->changeIdOrderState(_PS_OS_CANCELED_, (int)($this->id));
				if (!$history->addWithemail())
					return false;
			}
			return $this->update();
		}
		return $orderDetail->update() AND $this->update();
	}

	public function deleteCustomization($id_customization, $quantity, $orderDetail)
	{
		if (!$currentStatus = (int)($this->getCurrentState()))
			return false;

		if ($this->hasBeenDelivered())
			return Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'customization` SET `quantity_returned` = `quantity_returned` + '.(int)($quantity).' WHERE `id_customization` = '.(int)($id_customization).' AND `id_cart` = '.(int)($this->id_cart).' AND `id_product` = '.(int)($orderDetail->product_id));
		elseif ($this->hasBeenPaid())
			return Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'customization` SET `quantity_refunded` = `quantity_refunded` + '.(int)($quantity).' WHERE `id_customization` = '.(int)($id_customization).' AND `id_cart` = '.(int)($this->id_cart).' AND `id_product` = '.(int)($orderDetail->product_id));
		if (!Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'customization` SET `quantity` = `quantity` - '.(int)($quantity).' WHERE `id_customization` = '.(int)($id_customization).' AND `id_cart` = '.(int)($this->id_cart).' AND `id_product` = '.(int)($orderDetail->product_id)))
			return false;
		if (!Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'customization` WHERE `quantity` = 0'))
			return false;
		return $this->_deleteProduct($orderDetail, (int)($quantity));
	}

	/**
	 * Get order history
	 *
	 * @param integer $id_lang Language id
	 *
	 * @return array History entries ordered by date DESC
	 */
	public function getHistory($id_lang, $id_order_state = false, $no_hidden = false)
	{
		if (!$id_order_state)
			$id_order_state = 0;

		if (!isset(self::$_historyCache[$id_order_state]) OR $no_hidden)
		{
			$id_lang = $id_lang ? (int)($id_lang) : 'o.`id_lang`';
			$result = Db::getInstance()->ExecuteS('
			SELECT oh.*, e.`firstname` AS employee_firstname, e.`lastname` AS employee_lastname, osl.`name` AS ostate_name
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_history` oh ON o.`id_order` = oh.`id_order`
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON os.`id_order_state` = oh.`id_order_state`
			LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)($id_lang).')
			LEFT JOIN `'._DB_PREFIX_.'employee` e ON e.`id_employee` = oh.`id_employee`
			WHERE oh.id_order = '.(int)($this->id).'
			'.($no_hidden ? ' AND os.hidden = 0' : '').'
			'.((int)($id_order_state) ? ' AND oh.`id_order_state` = '.(int)($id_order_state) : '').'
			ORDER BY oh.date_add DESC, oh.id_order_history DESC');
			if ($no_hidden)
				return $result;
			self::$_historyCache[$id_order_state] = $result;
		}
		return self::$_historyCache[$id_order_state];
	}

	public function getProductsDetail()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'order_detail` od
		WHERE od.`id_order` = '.(int)($this->id));
	}


	/**
	 * @return string
	 * @deprecated
	 */
	public function getLastMessage()
	{
		Tools::displayAsDeprecated();
		$sql = 'SELECT `message` FROM `'._DB_PREFIX_.'message` WHERE `id_order` = '.(int)($this->id).' ORDER BY `id_message` desc';
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
		return $result['message'];
	}

	public function getFirstMessage()
	{
		$sql = 'SELECT `message` FROM `'._DB_PREFIX_.'message` WHERE `id_order` = '.(int)($this->id).' ORDER BY `id_message` asc';
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
		return $result['message'];
	}

	public function setProductPrices(&$row)
	{
		if ($this->_taxCalculationMethod == PS_TAX_EXC)
			$row['product_price'] = Tools::ps_round($row['product_price'], 2);
		else
			$row['product_price_wt'] = Tools::ps_round($row['product_price'] * (1 + $row['tax_rate'] / 100), 2);

		if ($row['reduction_percent'])
		{
			if ($this->_taxCalculationMethod == PS_TAX_EXC)
				$row['product_price'] = $row['product_price'] - $row['product_price'] * ($row['reduction_percent'] * 0.01);
			else
				$row['product_price_wt'] = Tools::ps_round($row['product_price_wt'] - $row['product_price_wt'] * ($row['reduction_percent'] * 0.01), 2);
		}

		if ($row['reduction_amount'])
		{
			if ($this->_taxCalculationMethod == PS_TAX_EXC)
				$row['product_price'] = $row['product_price'] - $row['reduction_amount'] / (1 + $row['tax_rate'] / 100);
			else
				$row['product_price_wt'] = Tools::ps_round($row['product_price_wt'] - $row['reduction_amount'], 2);
		}

		if ($row['group_reduction'])
		{
			if ($this->_taxCalculationMethod == PS_TAX_EXC)
				$row['product_price'] = $row['product_price'] - $row['product_price'] * ($row['group_reduction'] * 0.01);
			else
				$row['product_price_wt'] = Tools::ps_round($row['product_price_wt'] - $row['product_price_wt'] * ($row['group_reduction'] * 0.01), 2);
		}

		if (($row['reduction_percent'] OR $row['reduction_amount'] OR $row['group_reduction']) AND $this->_taxCalculationMethod == PS_TAX_EXC)
			$row['product_price'] = Tools::ps_round($row['product_price'], 2);

		if ($this->_taxCalculationMethod == PS_TAX_EXC)
			$row['product_price_wt'] = Tools::ps_round($row['product_price'] * (1 + ($row['tax_rate'] * 0.01)), 2) + Tools::ps_round($row['ecotax'] * (1 + $row['ecotax_tax_rate'] / 100), 2);
		else
		{
			$row['product_price_wt_but_ecotax'] = $row['product_price_wt'];
			$row['product_price_wt'] = Tools::ps_round($row['product_price_wt'] + $row['ecotax'] * (1 + $row['ecotax_tax_rate'] / 100), 2);
		}

		$row['total_wt'] = $row['product_quantity'] * $row['product_price_wt'];
		$row['total_price'] = $row['product_quantity'] * $row['product_price'];
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
		$resultArray = array();
		foreach ($products AS $k => $row)
		{
			// Change qty if selected
			if ($selectedQty)
			{
				$row['product_quantity'] = 0;
				foreach ($selectedProducts AS $key => $id_product)
					if ($row['id_order_detail'] == $id_product)
						$row['product_quantity'] = (int)($selectedQty[$key]);
				if (!$row['product_quantity'])
					continue ;
			}
			$this->setProductPrices($row);

			/* Add information for virtual product */
			if ($row['download_hash'] AND !empty($row['download_hash']))
				$row['filename'] = ProductDownload::getFilenameFromIdProduct($row['product_id']);

			/* Stock product */
			$resultArray[(int)($row['id_order_detail'])] = $row;
		}
		return $resultArray;
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
			SELECT `product_id`, `download_hash`, `download_deadline`
			FROM `'._DB_PREFIX_.'order_detail` od
			WHERE od.`id_order` = '.(int)($this->id).'
				AND `download_hash` <> \'\'';
		return Db::getInstance()->ExecuteS($sql);
	}

	/**
	* Check if order contains (only) virtual products
	* @return boolean true if is a virtual order or false
	*
	*/
	public function isVirtual($strict = true)
	{
		$products = $this->getProducts();
		if (count($products) < 1)
			return false;
		$virtual = false;
		foreach ($products AS $product)
		{
			$pd = ProductDownload::getIdFromIdProduct((int)($product['product_id']));
			if ($pd AND Validate::isUnsignedInt($pd) AND $product['download_hash'])
			{
				if ($strict === false)
					return true;
				$virtual &= true;
			}
		}
		return $virtual;
	}


	/**
	 * Get order discounts
	 *
	 * @return array Discounts with price and quantity
	 */
	public function getDiscounts($details = false)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'order_discount` od '.
		($details ? 'LEFT JOIN `'._DB_PREFIX_.'discount` d ON (d.`id_discount` = od.`id_discount`)' : '').'
		WHERE od.`id_order` = '.(int)($this->id));
	}

	static public function getDiscountsCustomer($id_customer, $id_discount)
	{
		return Db::getInstance()->getValue('
			SELECT COUNT(*) FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN '._DB_PREFIX_.'order_discount od ON (od.id_order = o.id_order)
			WHERE o.id_customer = '.(int)($id_customer).'
			AND od.id_discount = '.(int)($id_discount));
	}

	/**
	 * Get current order state (eg. Awaiting payment, Delivered...)
	 *
	 * @return array Order state details
	 */
	public function getCurrentState()
	{
		$orderHistory = OrderHistory::getLastOrderState($this->id);
		if (!isset($orderHistory) OR !$orderHistory)
			return false;
		return $orderHistory->id;
	}

	/**
	 * Get current order state name (eg. Awaiting payment, Delivered...)
	 *
	 * @return array Order state details
	 */
	public function getCurrentStateFull($id_lang)
	{
		return Db::getInstance()->getRow('
		SELECT oh.`id_order_state`, osl.`name`, os.`logable`
		FROM `'._DB_PREFIX_.'order_history` oh
		LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (osl.`id_order_state` = oh.`id_order_state`)
		LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
		WHERE osl.`id_lang` = '.(int)($id_lang).' AND oh.`id_order` = '.(int)($this->id).'
		ORDER BY `date_add` DESC, `id_order_history` DESC');
	}

	/**
	 * @deprecated
	 */
	public function isLogable()
	{
		Tools::displayAsDeprecated();
		return $this->valid;
	}

	public function hasBeenDelivered()
	{
		return sizeof($this->getHistory((int)($this->id_lang), _PS_OS_DELIVERED_));
	}

	public function hasBeenPaid()
	{
		return sizeof($this->getHistory((int)($this->id_lang), _PS_OS_PAYMENT_));
	}

	public function hasBeenShipped()
	{
		return sizeof($this->getHistory((int)($this->id_lang), _PS_OS_SHIPPING_));
	}

	public function isInPreparation()
	{
		return sizeof($this->getHistory((int)($this->id_lang), _PS_OS_PREPARATION_));
	}

	/**
	 * Get customer orders
	 *
	 * @param integer $id_customer Customer id
	 * @return array Customer orders
	 */
	static public function getCustomerOrders($id_customer)
    {
		global $cookie;

    	$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT o.*, (
				SELECT SUM(od.`product_quantity`)
				FROM `'._DB_PREFIX_.'order_detail` od
				WHERE od.`id_order` = o.`id_order`)
				AS nb_products
        FROM `'._DB_PREFIX_.'orders` o
        WHERE o.`id_customer` = '.(int)($id_customer).'
        GROUP BY o.`id_order`
        ORDER BY o.`date_add` DESC');
		if (!$res)
			return array();

		foreach ($res AS $key => $val)
		{
			$res2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT os.`id_order_state`, osl.`name` AS order_state, os.`invoice`
				FROM `'._DB_PREFIX_.'order_history` oh
				LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
				INNER JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)($cookie->id_lang).')
				WHERE oh.`id_order` = '.(int)($val['id_order']).'
				AND os.`hidden` != 1
				ORDER BY oh.`date_add` DESC, oh.`id_order_history` DESC
				LIMIT 1
			');
			if ($res2)
				$res[$key] = array_merge($res[$key], $res2[0]);
		}
		return $res;
    }

	static public function getOrdersIdByDate($date_from, $date_to, $id_customer = NULL, $type = NULL)
	{
		$sql = '
		SELECT `id_order`
		FROM `'._DB_PREFIX_.'orders`
		WHERE DATE_ADD(date_upd, INTERVAL -1 DAY) <= \''.pSQL($date_to).'\' AND date_upd >= \''.pSQL($date_from).'\''
		.($type ? ' AND '.pSQL(strval($type)).'_number != 0' : '')
		.($id_customer ? ' AND id_customer = '.(int)($id_customer) : '');
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);

		$orders = array();
		foreach ($result AS $order)
			$orders[] = (int)($order['id_order']);
		return $orders;
	}

	/*
	* @deprecated
	*/
	static public function getOrders($limit = NULL)
	{
		Tools::displayAsDeprecated();
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT *
			FROM `'._DB_PREFIX_.'orders`
			ORDER BY `date_add`
			'.((int)$limit ? 'LIMIT 0, '.(int)$limit : ''));
	}

	static public function getOrdersWithInformations($limit = NULL)
	{
		global $cookie;

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT *, (
				SELECT `name`
				FROM `'._DB_PREFIX_.'order_history` oh
				LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (osl.`id_order_state` = oh.`id_order_state`)
				WHERE oh.`id_order` = o.`id_order`
				AND osl.`id_lang` = '.(int)$cookie->id_lang.'
				ORDER BY oh.`date_add` DESC
				LIMIT 1
			) AS `state_name`
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = o.`id_customer`)
			ORDER BY o.`date_add` DESC
			'.((int)$limit ? 'LIMIT 0, '.(int)$limit : ''));
	}

	static public function getOrdersIdInvoiceByDate($date_from, $date_to, $id_customer = NULL, $type = NULL)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT `id_order`
		FROM `'._DB_PREFIX_.'orders`
		WHERE DATE_ADD(invoice_date, INTERVAL -1 DAY) <= \''.pSQL($date_to).'\' AND invoice_date >= \''.pSQL($date_from).'\''
		.($type ? ' AND '.pSQL(strval($type)).'_number != 0' : '')
		.($id_customer ? ' AND id_customer = '.(int)($id_customer) : '').
		' ORDER BY invoice_date ASC');

		$orders = array();
		foreach ($result AS $order)
			$orders[] = (int)($order['id_order']);
		return $orders;
	}

	static public function getOrderIdsByStatus($id_order_state)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT id_order
		FROM '._DB_PREFIX_.'orders o
		WHERE '.(int)$id_order_state.' = (
			SELECT id_order_state
			FROM '._DB_PREFIX_.'order_history oh
			WHERE oh.id_order = o .id_order
			ORDER BY date_add DESC, id_order_history DESC
			LIMIT 1
		)
		ORDER BY invoice_date ASC');

		$orders = array();
		foreach ($result AS $order)
			$orders[] = (int)($order['id_order']);
		return $orders;
	}

    /**
     * Get product total without taxes
     *
     * @return Product total with taxes
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
		if ($this->total_products_wt != '0.00' AND !$products)
			return $this->total_products_wt;
		/* Retro-compatibility (now set directly on the validateOrder() method) */
		if (!$products)
			$products = $this->getProductsDetail();

		$return = 0;
		foreach ($products AS $k => $row)
		{
			$price = Tools::ps_round($row['product_price'] * (1 + $row['tax_rate'] / 100), 2);
			if ($row['reduction_percent'])
				$price -= $price * ($row['reduction_percent'] * 0.01);
			if ($row['reduction_amount'])
				$price -= $row['reduction_amount'] * (1 + ($row['tax_rate'] * 0.01));
			if ($row['group_reduction'])
				$price -= $price * ($row['group_reduction'] * 0.01);
			$price += $row['ecotax'] * (1 + $row['ecotax_tax_rate'] / 100);
			$return += Tools::ps_round($price, 2) * $row['product_quantity'];
		}
		if (!$products)
		{
			$this->total_products_wt = $return;
			$this->update();
		}
		return $return;
	}

	/**
	 * Get customer orders number
	 *
	 * @param integer $id_customer Customer id
	 * @return array Customer orders number
	 */
	static public function getCustomerNbOrders($id_customer)
    {
    	$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT COUNT(`id_order`) AS nb
        FROM `'._DB_PREFIX_.'orders`
        WHERE `id_customer` = '.(int)($id_customer));

		return isset($result['nb']) ? $result['nb'] : 0;
    }

	/**
	 * Get an order by its cart id
	 *
	 * @param integer $id_cart Cart id
	 * @return array Order details
	 */
	static public function getOrderByCartId($id_cart)
    {
    	$result = Db::getInstance()->getRow('
        SELECT `id_order`
        FROM `'._DB_PREFIX_.'orders`
        WHERE `id_cart` = '.(int)($id_cart));

		return isset($result['id_order']) ? $result['id_order'] : false;
    }

    /**
	 * Add a discount to order
	 *
	 * @param integer $id_discount Discount id
	 * @param string $name Discount name
	 * @param float $value Discount value
	 * @return boolean Query sucess or not
	 */
	public function	addDiscount($id_discount, $name, $value)
	{
		return Db::getInstance()->AutoExecute(_DB_PREFIX_.'order_discount', array('id_order' => (int)($this->id), 'id_discount' => (int)($id_discount), 'name' => pSQL($name), 'value' => (float)($value)), 'INSERT');
	}

	/**
	 * Get orders number last week
	 *
	 * @return integer Orders number last week
	 * @deprecated
	 */
	public static function getWeeklyOrders()
	{
		Tools::displayAsDeprecated();
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT COUNT(`id_order`) as nb
		FROM `'._DB_PREFIX_.'orders`
		WHERE YEARWEEK(`date_add`) = YEARWEEK(NOW())');

		return isset($result['nb']) ? $result['nb'] : 0;
	}

	/**
	 * Get sales amount last month
	 *
	 * @return float Sales amount last month
	 * @deprecated
	 */
	public static function getMonthlySales()
	{
		Tools::displayAsDeprecated();
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT SUM(`total_paid`) as nb
		FROM `'._DB_PREFIX_.'orders`
		WHERE MONTH(`date_add`) = MONTH(NOW())
		AND YEAR(`date_add`) = YEAR(NOW())');

		return isset($result['nb']) ? $result['nb'] : 0;
	}

	public function getNumberOfDays()
	{
		$nbReturnDays = (int)(Configuration::get('PS_ORDER_RETURN_NB_DAYS'));
		if (!$nbReturnDays)
			return true;
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT TO_DAYS(NOW()) - TO_DAYS(`delivery_date`)  AS days FROM `'._DB_PREFIX_.'orders`
		WHERE `id_order` = '.(int)($this->id));
		if ($result['days'] <= $nbReturnDays)
			return true;
		return false;
	}


	public function isReturnable()
	{
		return ((int)(Configuration::get('PS_ORDER_RETURN')) == 1 AND (int)($this->getCurrentState()) == _PS_OS_DELIVERED_ AND $this->getNumberOfDays());
	}


    public static function getLastInvoiceNumber()
    {
        return (int)Db::getInstance()->getValue('
        SELECT MAX(`invoice_number`) AS `invoice_number`
		FROM `'._DB_PREFIX_.'orders`
        ');
    }

	public function setInvoice()
	{
		$number = (int)Configuration::get('PS_INVOICE_START_NUMBER');

		if ($number)
 		    Configuration::updateValue('PS_INVOICE_START_NUMBER', false);
 		else
		    $number = '(SELECT `invoice_number`
		                 FROM (
		                    SELECT MAX(`invoice_number`) + 1 AS `invoice_number`
		                    FROM `'._DB_PREFIX_.'orders`)
		                 tmp )';

        // a way to avoid duplicate invoice number
		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'orders`
		SET `invoice_number` = '.$number.', `invoice_date` = \''.date('Y-m-d H:i:s').'\'
		WHERE `id_order` = '.(int)$this->id
		);

        $res = Db::getInstance()->getRow('
        SELECT `invoice_number`, `invoice_date`
        FROM `'._DB_PREFIX_.'orders`
		WHERE `id_order` = '.(int)$this->id
        );

        $this->invoice_date = $res['invoice_date'];
        $this->invoice_number = $res['invoice_number'];
	}

	public function setDelivery()
	{
		// Set delivery number
		$number = (int)(Configuration::get('PS_DELIVERY_NUMBER'));
		if (!(int)($number))
			die(Tools::displayError('Invalid delivery number'));
		$this->delivery_number = $number;
		Configuration::updateValue('PS_DELIVERY_NUMBER', $number + 1);

		// Set delivery date
		$this->delivery_date = date('Y-m-d H:i:s');

		// Update object
		$this->update();
	}

	static public function printPDFIcons($id_order, $tr)
	{
		$order = new Order($id_order);
		$orderState = OrderHistory::getLastOrderState($id_order);
		if (!Validate::isLoadedObject($orderState) OR !Validate::isLoadedObject($order))
			die(Tools::displayError('Invalid objects'));
		echo '<span style="width:20px; margin-right:5px;">';
		if (($orderState->invoice AND $order->invoice_number) AND (int)($tr['product_number']))
			echo '<a href="pdf.php?id_order='.(int)($order->id).'&pdf"><img src="../img/admin/tab-invoice.gif" alt="invoice" /></a>';
		else
			echo '&nbsp;';
		echo '</span>';
		echo '<span style="width:20px;">';
		if ($orderState->delivery AND $order->delivery_number)
			echo '<a href="pdf.php?id_delivery='.(int)($order->delivery_number).'"><img src="../img/admin/delivery.gif" alt="delivery" /></a>';
		else
			echo '&nbsp;';
		echo '</span>';
	}

	static public function getByDelivery($id_delivery)
	{
	    $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
        SELECT id_order
        FROM `'._DB_PREFIX_.'orders`
        WHERE `delivery_number` = '.(int)($id_delivery));
		return new Order((int)($res['id_order']));
	}

	public function getTotalWeight()
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT SUM(product_weight * product_quantity) weight
		FROM '._DB_PREFIX_.'order_detail
		WHERE id_order = '.(int)($this->id));

		return (float)($result['weight']);
	}

	static public function getInvoice($id_invoice)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `invoice_number`, `id_order`
		FROM `'._DB_PREFIX_.'orders`
		WHERE invoice_number = '.(int)($id_invoice));
	}

	public function isAssociatedAtGuest($email)
	{
		if (!$email)
			return false;
		return (bool)Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = o.`id_customer`)
			WHERE o.`id_order` = '.(int)$this->id.'
			AND c.`email` = \''.pSQL($email).'\'
			AND c.`is_guest` = 1
		');
	}

	/**
	 * @param int $id_order
	 * @param int $id_customer optionnal
	 * @return int id_cart
	 */
	static public function getCartIdStatic($id_order, $id_customer = 0)
	{
		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_cart`
			FROM `'._DB_PREFIX_.'orders`
			WHERE `id_order` = '.(int)$id_order.'
			'.($id_customer ? 'AND `id_customer` = '.(int)$id_customer : ''));
	}

	public function getWsOrderRows()
	{
		$query = 'SELECT id_order_detail as `id`, id_order, product_attribute_id, product_quantity, product_name
		FROM `'._DB_PREFIX_.'order_detail`
		WHERE id_order = '.(int)$this->id;
		$result = Db::getInstance()->executeS($query);
		return $result;
	}

	public function setCurrentState($id_order_state)
	{
		$history = new OrderHistory();
		$history->id_order = (int)($this->id);
		$history->changeIdOrderState((int)$id_order_state, (int)($this->id));
	}

	public function addWs($autodate = true, $nullValues = false)
	{
		$paymentModule = Module::getInstanceByName($this->module);
		$id_order_state = 1; // TODO
		$customer = new Customer($this->id_customer);
		$paymentModule->validateOrder($this->id_cart, $id_order_state, $this->total_paid,	$this->payment,	NULL, array(), null, false, $customer->secure_key);
		return true;
	}

	public function deleteAssociations()
	{
		return (Db::getInstance()->Execute('
				DELETE FROM `'._DB_PREFIX_.'order_detail`
				WHERE `id_order` = '.(int)($this->id)) !== false);
	}
}
