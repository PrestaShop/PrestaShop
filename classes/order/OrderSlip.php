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

class OrderSlipCore extends ObjectModel
{
	/** @var integer */
	public $id;

	/** @var integer */
	public $id_customer;

	/** @var integer */
	public $id_order;

	/** @var float */
	public $conversion_rate;

	/** @var integer */
	public $amount;

	/** @var integer */
	public $shipping_cost;

	/** @var integer */
	public $shipping_cost_amount;

	/** @var integer */
	public $partial;

	/** @var string Object creation date */
	public $date_add;

	/** @var string Object last modification date */
	public $date_upd;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'order_slip',
		'primary' => 'id_order_slip',
		'fields' => array(
			'id_customer' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_order' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'conversion_rate' => 		array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true),
			'amount' => 				array('type' => self::TYPE_INT),
			'shipping_cost' => 			array('type' => self::TYPE_INT),
			'shipping_cost_amount' =>	array('type' => self::TYPE_FLOAT),
			'partial' =>				array('type' => self::TYPE_INT),
			'date_add' => 				array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' => 				array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		),
	);

	public function addSlipDetail($orderDetailList, $productQtyList)
	{
		foreach ($orderDetailList as $key => $orderDetail)
		{
			if ($qty = (int)($productQtyList[$key]))
				Db::getInstance()->insert('order_slip_detail', array(
					'id_order_slip' => (int)$this->id,
					'id_order_detail' => (int)$orderDetail,
					'product_quantity' => $qty,
				));
		}
	}

	public static function getOrdersSlip($customer_id, $order_id = false)
	{
		return Db::getInstance()->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'order_slip`
		WHERE `id_customer` = '.(int)($customer_id).
		($order_id ? ' AND `id_order` = '.(int)($order_id) : '').'
		ORDER BY `date_add` DESC');
	}

	public static function getOrdersSlipDetail($id_order_slip = true, $id_order_detail = false)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
		($id_order_detail ? 'SELECT SUM(`product_quantity`) AS `total`' : 'SELECT *').
		'FROM `'._DB_PREFIX_.'order_slip_detail`'
		.($id_order_slip ? ' WHERE `id_order_slip` = '.(int)($id_order_slip) : '')
		.($id_order_detail ? ' WHERE `id_order_detail` = '.(int)($id_order_detail) : ''));
	}

	// TODO clean getProducts($resTab) => now getProducts method don't use his parameters
	public static function getOrdersSlipProducts($orderSlipId, $order)
	{
		$cart_rules = $order->getCartRules(true);
		$productsRet = OrderSlip::getOrdersSlipDetail($orderSlipId);
		$products = $order->getProductsDetail();

		$tmp = array();
		foreach ($productsRet as $slip_detail)
			$tmp[$slip_detail['id_order_detail']] = $slip_detail['product_quantity'];
		$resTab = array();
		foreach ($products as $key => $product)
			if (isset($tmp[$product['id_order_detail']]))
			{
				$resTab[$key] = $product;
				$resTab[$key]['product_quantity'] = $tmp[$product['id_order_detail']];
				if (count($cart_rules))
				{
					$order->setProductPrices($product);
					$realProductPrice = $resTab[$key]['product_price'];
					// Todo : must be updated to use the cart rules
					foreach ($cart_rules as $cart_rule)
					{
						if ($cart_rule['reduction_percent'])
							$resTab[$key]['product_price'] -= $realProductPrice * ($cart_rule['reduction_percent'] / 100);
						elseif ($cart_rule['reduction_amount'])
							$resTab[$key]['product_price'] -= (($cart_rule['reduction_amount'] * ($product['product_price_wt'] / $order->total_products_wt)) / (1.00 + ($product['tax_rate'] / 100)));
					}

				}
			}
		return $order->getProducts($resTab);
	}

	public function getProducts()
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *, osd.product_quantity
		FROM `'._DB_PREFIX_.'order_slip_detail` osd
		INNER JOIN `'._DB_PREFIX_.'order_detail` od ON osd.id_order_detail = od.id_order_detail
		WHERE osd.`id_order_slip` = '.(int)$this->id);

		$order = new Order($this->id_order);
		$products = array();
		foreach ($result as $row)
		{
			$order->setProductPrices($row);
			$products[] = $row;
		}
		return $products;
	}

	public static function getSlipsIdByDate($dateFrom, $dateTo)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT `id_order_slip`
		FROM `'._DB_PREFIX_.'order_slip`
		WHERE `date_add` BETWEEN \''.pSQL($dateFrom).' 00:00:00\' AND \''.pSQL($dateTo).' 23:59:59\'
		ORDER BY `date_add` ASC');

		$slips = array();
		foreach ($result as $slip)
			$slips[] = (int)$slip['id_order_slip'];
		return $slips;
	}

	public static function createOrderSlip($order, $productList, $qtyList, $shipping_cost = false)
	{
		$currency = new Currency($order->id_currency);
		$orderSlip = new OrderSlip();
		$orderSlip->id_customer = (int)($order->id_customer);
		$orderSlip->id_order = (int)($order->id);
		$orderSlip->shipping_cost = (int)($shipping_cost);
		$orderSlip->conversion_rate = $currency->conversion_rate;
		$orderSlip->partial = 0;
		if (!$orderSlip->add())
			return false;

		$orderSlip->addSlipDetail($productList, $qtyList);
		return true;
	}

	public static function createPartialOrderSlip($order, $amount, $shipping_cost_amount, $order_detail_list)
	{
		$currency = new Currency($order->id_currency);
		$orderSlip = new OrderSlip();
		$orderSlip->id_customer = (int)($order->id_customer);
		$orderSlip->id_order = (int)($order->id);
		$orderSlip->amount = (float)($amount);
		$orderSlip->shipping_cost = false;
		$orderSlip->shipping_cost_amount = (float)($shipping_cost_amount);
		$orderSlip->conversion_rate = $currency->conversion_rate;
		$orderSlip->partial = 1;
		if (!$orderSlip->add())
			return false;

		$orderSlip->addPartialSlipDetail($order_detail_list);
		return true;
	}

	public function addPartialSlipDetail($order_detail_list)
	{
		foreach ($order_detail_list as $id_order_detail => $tab)
		{
			$tab['amount_tax_excl'] = $tab['amount_tax_incl'] = $tab['amount'];
			$id_tax = (int)Db::getInstance()->getValue('SELECT `id_tax` FROM `'._DB_PREFIX_.'order_detail_tax` WHERE `id_order_detail` = '.(int)$id_order_detail);
			if ($id_tax > 0)
			{
				$rate = (float)Db::getInstance()->getValue('SELECT `rate` FROM `'._DB_PREFIX_.'tax` WHERE `id_tax` = '.(int)$id_tax);
				if ($rate > 0)
				{
					$rate = 1 + ($rate / 100);
					$tab['amount_tax_excl'] = $tab['amount_tax_excl'] / $rate;
				}
			}
			
			$insertOrderSlip = array(
				'id_order_slip' => (int)($this->id),
				'id_order_detail' => (int)($id_order_detail),
				'product_quantity' => (int)($tab['quantity']),
				'amount_tax_excl' => (float)($tab['amount_tax_excl']),
				'amount_tax_incl' => (float)($tab['amount_tax_incl']),
			);
			Db::getInstance()->insert('order_slip_detail', $insertOrderSlip);
		}
	}

}

