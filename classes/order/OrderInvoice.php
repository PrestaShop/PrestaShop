<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderInvoiceCore extends ObjectModel
{
	const TAX_EXCL = 0;
	const TAX_INCL = 1;
	const DETAIL = 2;

	/** @var integer */
	public $id_order;

	/** @var integer */
	public $number;

	/** @var integer */
	public $delivery_number;

	/** @var integer */
	public $delivery_date = '0000-00-00 00:00:00';

	/** @var float */
	public $total_discount_tax_excl;

	/** @var float */
	public $total_discount_tax_incl;

	/** @var float */
	public $total_paid_tax_excl;

	/** @var float */
	public $total_paid_tax_incl;

	/** @var float */
	public $total_products;

	/** @var float */
	public $total_products_wt;

	/** @var float */
	public $total_shipping_tax_excl;

	/** @var float */
	public $total_shipping_tax_incl;

	/** @var int */
	public $shipping_tax_computation_method;

	/** @var float */
	public $total_wrapping_tax_excl;

	/** @var float */
	public $total_wrapping_tax_incl;

	/** @var string note */
	public $note;

	/** @var intger */
	public $date_add;

	/** @var array Total paid cache */
	protected static $_total_paid_cache = array();

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'order_invoice',
		'primary' => 'id_order_invoice',
		'fields' => array(
			'id_order' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'number' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'delivery_number' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'delivery_date' => 			array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
			'total_discount_tax_excl' =>array('type' => self::TYPE_FLOAT),
			'total_discount_tax_incl' =>array('type' => self::TYPE_FLOAT),
			'total_paid_tax_excl' =>	array('type' => self::TYPE_FLOAT),
			'total_paid_tax_incl' =>	array('type' => self::TYPE_FLOAT),
			'total_products' =>			array('type' => self::TYPE_FLOAT),
			'total_products_wt' =>		array('type' => self::TYPE_FLOAT),
			'total_shipping_tax_excl' =>array('type' => self::TYPE_FLOAT),
			'total_shipping_tax_incl' =>array('type' => self::TYPE_FLOAT),
			'shipping_tax_computation_method' => array('type' => self::TYPE_INT),
			'total_wrapping_tax_excl' =>array('type' => self::TYPE_FLOAT),
			'total_wrapping_tax_incl' =>array('type' => self::TYPE_FLOAT),
			'note' => 					array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 65000),
			'date_add' => 				array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		),
	);

	public function getProductsDetail()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'order_detail` od
		LEFT JOIN `'._DB_PREFIX_.'product` p
		ON p.id_product = od.product_id
		LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop = od.id_shop)
		WHERE od.`id_order` = '.(int)$this->id_order.'
		'.($this->id && $this->number ? ' AND od.`id_order_invoice` = '.(int)$this->id : ''));
	}

	public static function getInvoiceByNumber($id_invoice)
	{
		if (is_numeric($id_invoice))
			$id_invoice = (int)($id_invoice);
		elseif (is_string($id_invoice))
		{
			$matches = array();
			if (preg_match('/^(?:'.Configuration::get('PS_INVOICE_PREFIX', Context::getContext()->language->id).')\s*([0-9]+)$/i', $id_invoice, $matches))
				$id_invoice = $matches[1];
		}
		if (!$id_invoice)
			return false;

		$id_order_invoice = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_order_invoice`
			FROM `'._DB_PREFIX_.'order_invoice`
			WHERE number = '.(int)$id_invoice
		);

		return ($id_order_invoice ? new OrderInvoice($id_order_invoice) : false);
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

		$order = new Order($this->id_order);
		$customized_datas = Product::getAllCustomizedDatas($order->id_cart);

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
			$this->setProductCustomizedDatas($row, $customized_datas);

			// Add information for virtual product
			if ($row['download_hash'] && !empty($row['download_hash']))
			{
				$row['filename'] = ProductDownload::getFilenameFromIdProduct((int)$row['product_id']);
				// Get the display filename
				$row['display_filename'] = ProductDownload::getFilenameFromFilename($row['filename']);
			}

			$row['id_address_delivery'] = $order->id_address_delivery;

			/* Stock product */
			$resultArray[(int)$row['id_order_detail']] = $row;
		}

		if ($customized_datas)
			Product::addCustomizationPrice($resultArray, $customized_datas);

		return $resultArray;
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
	 * @param array &$product
	 */
	protected function setProductCurrentStock(&$product)
	{
		if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
			&& (int)$product['advanced_stock_management'] == 1
			&& (int)$product['id_warehouse'] > 0)
			$product['current_stock'] = StockManagerFactory::getManager()->getProductPhysicalQuantities($product['product_id'], $product['product_attribute_id'], null, true);
		else
			$product['current_stock'] = '--';
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
				WHERE id_product = '.(int)($product['product_id']));

		$product['image'] = null;
		$product['image_size'] = null;

		if ($id_image)
			$product['image'] = new Image($id_image);
	}

	/**
	 * This method returns true if at least one order details uses the
	 * One After Another tax computation method.
	 *
	 * @since 1.5
	 * @return boolean
	 */
	public function useOneAfterAnotherTaxComputationMethod()
	{
		// if one of the order details use the tax computation method the display will be different
		return Db::getInstance()->getValue('
		SELECT od.`tax_computation_method`
		FROM `'._DB_PREFIX_.'order_detail_tax` odt
		LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (od.`id_order_detail` = odt.`id_order_detail`)
		WHERE od.`id_order` = '.(int)$this->id_order.'
		AND od.`id_order_invoice` = '.(int)$this->id.'
		AND od.`tax_computation_method` = '.(int)TaxCalculator::ONE_AFTER_ANOTHER_METHOD
		) || Configuration::get('PS_INVOICE_TAXES_BREAKDOWN');
	}

	/**
	 * Returns the correct product taxes breakdown.
	 *
	 * @since 1.5
	 * @return array
	 */
	public function getProductTaxesBreakdown($order = null)
	{
		Tools::$round_mode = $order->round_mode;
		$tmp_tax_infos = array();
		if ($this->useOneAfterAnotherTaxComputationMethod())
		{
			// sum by taxes
			$taxes_infos = Db::getInstance()->executeS('
			SELECT t.`rate` AS `name`, t.`rate`, SUM(`total_amount`) AS `total_amount`
			FROM `'._DB_PREFIX_.'order_detail_tax` odt
			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = odt.`id_tax`)
			LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (od.`id_order_detail` = odt.`id_order_detail`)
			WHERE od.`id_order` = '.(int)$this->id_order.'
			AND od.`id_order_invoice` = '.(int)$this->id.'
			GROUP BY odt.`id_tax`
			');

			// format response
			foreach ($taxes_infos as $tax_infos)
			{
				$tmp_tax_infos[$tax_infos['rate']]['total_amount'] = $tax_infos['total_amount'];
				$tmp_tax_infos[$tax_infos['rate']]['name'] = $tax_infos['name'];
			}
		}
		else
		{
			// sum by order details in order to retrieve real taxes rate
			$taxes_infos = Db::getInstance()->executeS('
			SELECT t.`rate` AS `name`, od.`total_price_tax_excl` AS total_price_tax_excl, SUM(t.`rate`) AS rate, SUM(`total_amount`) AS `total_amount`, od.`ecotax`, od.`ecotax_tax_rate`, od.`product_quantity`
			FROM `'._DB_PREFIX_.'order_detail_tax` odt
			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = odt.`id_tax`)
			LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (od.`id_order_detail` = odt.`id_order_detail`)
			WHERE od.`id_order` = '.(int)$this->id_order.'
			AND od.`id_order_invoice` = '.(int)$this->id.'
			GROUP BY odt.`id_order_detail`
			');

			// sum by taxes
			$tmp_tax_infos = array();
			$shipping_tax_amount = 0;
			foreach ($order->getCartRules() as $cart_rule)
				if ($cart_rule['free_shipping'])
				{
					$shipping_tax_amount = $this->total_shipping_tax_excl;
					break;
				}

			foreach ($taxes_infos as $tax_infos)
			{
				if (!isset($tmp_tax_infos[$tax_infos['rate']]))
					$tmp_tax_infos[$tax_infos['rate']] = array(
						'total_amount' => 0,
						'name' => 0,
						'total_price_tax_excl' => 0
					);
				$ratio = $tax_infos['total_price_tax_excl'] / $this->total_products;
				$order_reduction_amount = ($this->total_discount_tax_excl - $shipping_tax_amount) * $ratio;
				$tmp_tax_infos[$tax_infos['rate']]['total_amount'] += ($tax_infos['total_amount'] - Tools::ps_round($tax_infos['ecotax'] * $tax_infos['product_quantity'] * $tax_infos['ecotax_tax_rate'] / 100, _PS_PRICE_COMPUTE_PRECISION_));
				$tmp_tax_infos[$tax_infos['rate']]['name'] = $tax_infos['name'];
				$tmp_tax_infos[$tax_infos['rate']]['total_price_tax_excl'] += $tax_infos['total_price_tax_excl'] - $order_reduction_amount - Tools::ps_round($tax_infos['ecotax'] * $tax_infos['product_quantity'], _PS_PRICE_COMPUTE_PRECISION_);
			}
		}

		foreach ($tmp_tax_infos as &$tax)
		{
			$tax['total_amount'] = Tools::ps_round($tax['total_amount'], _PS_PRICE_DISPLAY_PRECISION_);
			$tax['total_price_tax_excl'] = Tools::ps_round($tax['total_price_tax_excl'], _PS_PRICE_DISPLAY_PRECISION_);
		}

		return $tmp_tax_infos;
	}

	/**
	 * Returns the shipping taxes breakdown
	 *
	 * @since 1.5
	 * @return array
	 */
	public function getShippingTaxesBreakdown($order)
	{
		$taxes_breakdown = array();

		// No shipping breakdown if it's free!
		foreach ($order->getCartRules() as $cart_rule)
			if ($cart_rule['free_shipping'])
				return $taxes_breakdown;

		$shipping_tax_amount = $this->total_shipping_tax_incl - $this->total_shipping_tax_excl;

		if ($shipping_tax_amount > 0)
			$taxes_breakdown[] = array(
				'rate' => $order->carrier_tax_rate,
				'total_amount' => $shipping_tax_amount,
				'total_tax_excl' => $this->total_shipping_tax_excl
			);

		return $taxes_breakdown;
	}

	/**
	 * Returns the wrapping taxes breakdown
	 *
	 * @todo
	 * @since 1.5
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
	 * @since 1.5
	 * @return array
	 */
	public function getEcoTaxTaxesBreakdown()
	{
		$result = Db::getInstance()->executeS('
		SELECT `ecotax_tax_rate` as `rate`, SUM(`ecotax` * `product_quantity`) as `ecotax_tax_excl`, SUM(`ecotax` * `product_quantity`) as `ecotax_tax_incl`
		FROM `'._DB_PREFIX_.'order_detail`
		WHERE `id_order` = '.(int)$this->id_order.'
		AND `id_order_invoice` = '.(int)$this->id.'
		GROUP BY `ecotax_tax_rate`');

		$taxes = array();
		foreach ($result as $row)
			if ($row['ecotax_tax_excl'] > 0)
			{
				$row['ecotax_tax_incl'] = Tools::ps_round($row['ecotax_tax_excl'] + ($row['ecotax_tax_excl'] * $row['rate'] / 100), _PS_PRICE_DISPLAY_PRECISION_);
				$row['ecotax_tax_excl'] = Tools::ps_round($row['ecotax_tax_excl'], _PS_PRICE_DISPLAY_PRECISION_);
				$taxes[] = $row;
			}
		return $taxes;
	}

	/**
	 * Returns all the order invoice that match the date interval
	 *
	 * @since 1.5
	 * @static
	 * @param $date_from
	 * @param $date_to
	 * @return array collection of OrderInvoice
	 */
	public static function getByDateInterval($date_from, $date_to)
	{
		$order_invoice_list = Db::getInstance()->executeS('
			SELECT oi.*
			FROM `'._DB_PREFIX_.'order_invoice` oi
			LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = oi.`id_order`)
			WHERE DATE_ADD(oi.date_add, INTERVAL -1 DAY) <= \''.pSQL($date_to).'\'
			AND oi.date_add >= \''.pSQL($date_from).'\'
			'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
			AND oi.number > 0
			ORDER BY oi.date_add ASC
		');

		return ObjectModel::hydrateCollection('OrderInvoice', $order_invoice_list);
	}

	/**
	 * @since 1.5.0.3
	 * @static
	 * @param $id_order_state
	 * @return array collection of OrderInvoice
	 */
	public static function getByStatus($id_order_state)
	{
		$order_invoice_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT oi.*
			FROM `'._DB_PREFIX_.'order_invoice` oi
			LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = oi.`id_order`)
			WHERE '.(int)$id_order_state.' = o.current_state
			'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
			AND oi.number > 0
			ORDER BY oi.`date_add` ASC
		');

		return ObjectModel::hydrateCollection('OrderInvoice', $order_invoice_list);
	}

	/**
	 * @since 1.5.0.3
	 * @static
	 * @param $date_from
	 * @param $date_to
	 * @return array collection of invoice
	 */
	public static function getByDeliveryDateInterval($date_from, $date_to)
	{
		$order_invoice_list = Db::getInstance()->executeS('
			SELECT oi.*
			FROM `'._DB_PREFIX_.'order_invoice` oi
			LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = oi.`id_order`)
			WHERE DATE_ADD(oi.delivery_date, INTERVAL -1 DAY) <= \''.pSQL($date_to).'\'
			AND oi.delivery_date >= \''.pSQL($date_from).'\'
			'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
			ORDER BY oi.delivery_date ASC
		');

		return ObjectModel::hydrateCollection('OrderInvoice', $order_invoice_list);
	}

	/**
	 * @since 1.5
	 * @static
	 * @param $id_order_invoice
	 */
	public static function getCarrier($id_order_invoice)
	{
		$carrier = false;
		if ($id_carrier = OrderInvoice::getCarrierId($id_order_invoice))
			$carrier = new Carrier((int)$id_carrier);

		return $carrier;
	}

	/**
	 * @since 1.5
	 * @static
	 * @param $id_order_invoice
	 */
	public static function getCarrierId($id_order_invoice)
	{
		$sql = 'SELECT `id_carrier`
				FROM `'._DB_PREFIX_.'order_carrier`
				WHERE `id_order_invoice` = '.(int)$id_order_invoice;

		return Db::getInstance()->getValue($sql);
	}

	/**
	 * @static
	 * @param $id
	 * @return OrderInvoice
	 */
	public static function retrieveOneById($id)
	{
		$order_invoice = new OrderInvoice($id);
		if (!Validate::isLoadedObject($order_invoice))
			throw new PrestaShopException('Can\'t load Order Invoice object for id: '.$id);
		return $order_invoice;
	}

	/**
	 * Amounts of payments
	 * @since 1.5.0.2
	 * @return float Total paid
	 */
	public function getTotalPaid()
	{
		$cache_id = 'order_invoice_paid_'.(int)$this->id;
		if (!Cache::isStored($cache_id))
		{
			$amount = 0;
			$payments = OrderPayment::getByInvoiceId($this->id);
			foreach ($payments as $payment)
				$amount += $payment->amount;
			Cache::store($cache_id, $amount);
		}
		return Cache::retrieve($cache_id);
	}

	/**
	 * Rest Paid
	 * @since 1.5.0.2
	 * @return float Rest Paid
	 */
	public function getRestPaid()
	{
		return round($this->total_paid_tax_incl + $this->getSiblingTotal() - $this->getTotalPaid(), 2);
	}


	/**
	 * Return collection of order invoice object linked to the payments of the current order invoice object
	 *
	 * @since 1.5.0.14
     * @return PrestaShopCollection|array Collection of OrderInvoice or empty array
	 */
	public function getSibling()
	{
		$query = new DbQuery();
		$query->select('oip2.id_order_invoice');
		$query->from('order_invoice_payment', 'oip1');
		$query->innerJoin('order_invoice_payment', 'oip2',
			'oip2.id_order_payment = oip1.id_order_payment AND oip2.id_order_invoice <> oip1.id_order_invoice');
		$query->where('oip1.id_order_invoice = '.$this->id);

		$invoices = Db::getInstance()->executeS($query);
		if (!$invoices)
			return array();

		$invoice_list = array();
		foreach ($invoices as $invoice)
			$invoice_list[] = $invoice['id_order_invoice'];

		$payments = new PrestaShopCollection('OrderInvoice');
		$payments->where('id_order_invoice', 'IN', $invoice_list);

		return $payments;
	}


	/**
	 * Return total to paid of sibling invoices
	 *
	 * @param int $mod TAX_EXCL, TAX_INCL, DETAIL
	 *
	 * @since 1.5.0.14
	 */
	public function getSiblingTotal($mod = OrderInvoice::TAX_INCL)
	{
		$query = new DbQuery();
		$query->select('SUM(oi.total_paid_tax_incl) as total_paid_tax_incl, SUM(oi.total_paid_tax_excl) as total_paid_tax_excl');
		$query->from('order_invoice_payment', 'oip1');
		$query->innerJoin('order_invoice_payment', 'oip2',
			'oip2.id_order_payment = oip1.id_order_payment AND oip2.id_order_invoice <> oip1.id_order_invoice');
		$query->leftJoin('order_invoice', 'oi',
			'oi.id_order_invoice = oip2.id_order_invoice');
		$query->where('oip1.id_order_invoice = '.$this->id);

		$row = Db::getInstance()->getRow($query);

		switch ($mod)
		{
			case OrderInvoice::TAX_EXCL:
				return $row['total_paid_tax_excl'];
			case OrderInvoice::TAX_INCL:
				return $row['total_paid_tax_incl'];
			default:
				return $row;
		}
	}

	/**
	 * Get global rest to paid
	 *    This method will return something different of the method getRestPaid if
	 *    there is an other invoice linked to the payments of the current invoice
	 * @since 1.5.0.13
	 */
	public function getGlobalRestPaid()
	{
		static $cache;

		if (!isset($cache[$this->id]))
		{
			$res = Db::getInstance()->getRow('
			SELECT SUM(sub.paid) paid, SUM(sub.to_paid) to_paid
			FROM (
				SELECT
					op.amount as paid, SUM(oi.total_paid_tax_incl) to_paid
				FROM `'._DB_PREFIX_.'order_invoice_payment` oip1
				INNER JOIN `'._DB_PREFIX_.'order_invoice_payment` oip2
					ON oip2.id_order_payment = oip1.id_order_payment
				INNER JOIN `'._DB_PREFIX_.'order_invoice` oi
					ON oi.id_order_invoice = oip2.id_order_invoice
				INNER JOIN `'._DB_PREFIX_.'order_payment` op
					ON op.id_order_payment = oip2.id_order_payment
				WHERE oip1.id_order_invoice = '.(int)$this->id.'
				GROUP BY op.id_order_payment
			) sub');
			$cache[$this->id] = round($res['to_paid'] - $res['paid'], 2);
		}

		return $cache[$this->id];
	}

	/**
	 * @since 1.5.0.2
	 * @return bool Is paid ?
	 */
	public function isPaid()
	{
		return $this->getTotalPaid() == $this->total_paid_tax_incl;
	}

	/**
	 * @since 1.5.0.2
	 * @return PrestaShopCollection Collection of Order payment
	 */
	public function getOrderPaymentCollection()
	{
		return OrderPayment::getByInvoiceId($this->id);
	}

	/**
	 * Get the formatted number of invoice
	 * @since 1.5.0.2
	 * @param int $id_lang for invoice_prefix
	 * @return string
	 */
	public function getInvoiceNumberFormatted($id_lang, $id_shop = null)
	{
		return '#'.Configuration::get('PS_INVOICE_PREFIX', $id_lang, null, $id_shop).sprintf('%06d', $this->number);
	}

	public function saveCarrierTaxCalculator(array $taxes_amount)
	{
		$is_correct = true;
		foreach ($taxes_amount as $id_tax => $amount)
		{
			$sql = 'INSERT INTO `'._DB_PREFIX_.'order_invoice_tax` (`id_order_invoice`, `type`, `id_tax`, `amount`)
					VALUES ('.(int)$this->id.', \'shipping\', '.(int)$id_tax.', '.(float)$amount.')';

			$is_correct &= Db::getInstance()->execute($sql);
		}

		return $is_correct;
	}
}
