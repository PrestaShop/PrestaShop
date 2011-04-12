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

class OrderSlipCore extends ObjectModel
{
	/** @var integer */
	public		$id;

	/** @var integer */
	public 		$id_customer;

	/** @var integer */
	public 		$id_order;

	/** @var float */
	public		$conversion_rate;

	/** @var integer */
	public		$shipping_cost;

	/** @var string Object creation date */
	public 		$date_add;

	/** @var string Object last modification date */
	public 		$date_upd;

	protected $tables = array ('order_slip');

	protected	$fieldsRequired = array ('id_customer', 'id_order', 'conversion_rate');
	protected	$fieldsValidate = array('id_customer' => 'isUnsignedId', 'id_order' => 'isUnsignedId', 'conversion_rate' => 'isFloat');

	protected 	$table = 'order_slip';
	protected 	$identifier = 'id_order_slip';

	public function getFields()
	{
		parent::validateFields();

		$fields['id_customer'] = (int)($this->id_customer);
		$fields['id_order'] = (int)($this->id_order);
		$fields['conversion_rate'] = (float)($this->conversion_rate);
		$fields['shipping_cost'] = (int)($this->shipping_cost);
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		return $fields;
	}

	public function addSlipDetail($orderDetailList, $productQtyList)
	{
		foreach ($orderDetailList as $key => $orderDetail)
		{
			if ($qty = (int)($productQtyList[$key]))
				Db::getInstance()->AutoExecute(_DB_PREFIX_.'order_slip_detail', array('id_order_slip' => (int)($this->id), 'id_order_detail' => (int)($orderDetail), 'product_quantity' => $qty), 'INSERT');
		}
	}

	static public function getOrdersSlip($customer_id, $order_id = false)
	{
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'order_slip`
		WHERE `id_customer` = '.(int)($customer_id).
		($order_id ? ' AND `id_order` = '.(int)($order_id) : '').'
		ORDER BY `date_add` DESC');
	}

	static public function getOrdersSlipDetail($id_order_slip = true, $id_order_detail = false)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS(
		($id_order_detail ? 'SELECT SUM(`product_quantity`) AS `total`' : 'SELECT *').
		'FROM `'._DB_PREFIX_.'order_slip_detail`'
		.($id_order_slip ? ' WHERE `id_order_slip` = '.(int)($id_order_slip) : '')
		.($id_order_detail ? ' WHERE `id_order_detail` = '.(int)($id_order_detail) : ''));
	}

	static public function getOrdersSlipProducts($orderSlipId, $order)
	{
		$discounts = $order->getDiscounts(true);
		$productsRet = self::getOrdersSlipDetail($orderSlipId);
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
				if (sizeof($discounts))
				{
					$order->setProductPrices($product);
					$realProductPrice = $resTab[$key]['product_price'];
					foreach ($discounts as $discount)
					{
						if ($discount['id_discount_type'] == 1)
							$resTab[$key]['product_price'] -= $realProductPrice * ($discount['value'] / 100);
						elseif ($discount['id_discount_type'] == 2)
							$resTab[$key]['product_price'] -= (($discount['value'] * ($product['product_price_wt'] / $order->total_products_wt)) / (1.00 + ($product['tax_rate'] / 100)));
					}

				}
			}
		return $order->getProducts($resTab);
	}

	public function getProducts()
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT *, osd.product_quantity
		FROM `'._DB_PREFIX_.'order_slip_detail` osd
		INNER JOIN `'._DB_PREFIX_.'order_detail` od ON osd.id_order_detail = od.id_order_detail
		WHERE osd.`id_order_slip` = '.(int)$this->id);

		$order = new Order($this->id_order);
		$products = array();
		foreach ($result AS $row)
		{
			$order->setProductPrices($row);
			$products[] = $row;
		}
		return $products;
	}

	static public function getSlipsIdByDate($dateFrom, $dateTo)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT `id_order_slip`
		FROM `'._DB_PREFIX_.'order_slip`
		WHERE `date_add` BETWEEN \''.pSQL($dateFrom).' 00:00:00\' AND \''.pSQL($dateTo).' 23:59:59\'
		ORDER BY `date_add` ASC');

		$slips = array();
		foreach ($result AS $slip)
			$slips[] = (int)$slip['id_order_slip'];
		return $slips;
	}

	static public function createOrderSlip($order, $productList, $qtyList, $shipping_cost = false)
	{
		$currency = new Currency($order->id_currency);
		$orderSlip =  new OrderSlip();
		$orderSlip->id_customer = (int)($order->id_customer);
		$orderSlip->id_order = (int)($order->id);
		$orderSlip->shipping_cost = (int)($shipping_cost);
		$orderSlip->conversion_rate = $currency->conversion_rate;
		if (!$orderSlip->add())
			return false;

		$orderSlip->addSlipDetail($productList, $qtyList);
		return true;
	}
}

