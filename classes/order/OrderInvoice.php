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

class OrderInvoiceCore extends ObjectModel
{
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
		'.Shop::addSqlAssociation('product', 'p').'
		WHERE od.`id_order` = '.(int)$this->id_order.'
		AND od.`id_order_invoice` = '.(int)$this->id);
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
				SELECT id_image
				FROM '._DB_PREFIX_.'product_attribute_image
				WHERE id_product_attribute = '.(int)$product['product_attribute_id']);

		if (!isset($image['id_image']) || !$image['id_image'])
			$id_image = Db::getInstance()->getValue('
				SELECT id_image
				FROM '._DB_PREFIX_.'image
				WHERE id_product = '.(int)($product['product_id']).' AND cover = 1
			');

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
		);
	}

	/**
	 * Returns the correct product taxes breakdown.
	 *
	 * @since 1.5
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
			WHERE od.`id_order` = '.(int)$this->id_order.'
			AND od.`id_order_invoice` = '.(int)$this->id.'
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
			WHERE od.`id_order` = '.(int)$this->id_order.'
			AND od.`id_order_invoice` = '.(int)$this->id.'
			GROUP BY odt.`id_order_detail`
			');

			// sum by taxes
			$tmp_tax_infos = array();
			foreach ($taxes_infos as $tax_infos)
			{
				if (!isset($tmp_tax_infos[$tax_infos['rate']]))
					$tmp_tax_infos[$tax_infos['rate']] = array(
						'total_amount' => 0,
						'name' => 0,
						'total_price_tax_excl' => 0
					);

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
	 * @since 1.5
	 * @return array
	 */
	public function getShippingTaxesBreakdown($order)
	{
		$taxes_breakdown = array();

		$shipping_tax_amount = $this->total_shipping_tax_incl - $this->total_shipping_tax_excl;

		if ($shipping_tax_amount > 0)
			$taxes_breakdown[] = array(
				'rate' => $order->carrier_tax_rate,
				'total_amount' => $shipping_tax_amount
			);

		return $taxes_breakdown;
	}

	/**
	 * Returns the wrapping taxes breakdown
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
		return Db::getInstance()->executeS('
		SELECT `ecotax_tax_rate`, SUM(`ecotax`) as `ecotax_tax_excl`, SUM(`ecotax`) as `ecotax_tax_incl`
		FROM `'._DB_PREFIX_.'order_detail`
		WHERE `id_order` = '.(int)$this->id_order.'
		AND `id_order_invoice` = '.(int)$this->id
		);
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
			WHERE '.(int)$id_order_state.' = (
				SELECT id_order_state
				FROM '._DB_PREFIX_.'order_history oh
				WHERE oh.id_order = o.id_order
				ORDER BY date_add DESC, id_order_history DESC
				LIMIT 1
			)
			'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
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
			AND oi.date_add >= \''.pSQL($date_from).'\'
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
		if (!array_key_exists($this->id, self::$_total_paid_cache))
		{
			self::$_total_paid_cache[$this->id] = 0;
			$payments = OrderPayment::getByInvoiceId($this->id);
			foreach ($payments as $payment)
				self::$_total_paid_cache[$this->id] += $payment->amount;
		}
		return self::$_total_paid_cache[$this->id];
	}

	/**
	 * Rest Paid
	 * @since 1.5.0.2
	 * @return float Rest Paid
	 */
	public function getRestPaid()
	{
		return round($this->total_paid_tax_incl - $this->getTotalPaid(), 2);
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
	 * @return Collection of Order payment
	 */
	public function getOrderPaymentCollection()
	{
		$order_payments = new Collection('OrderPayment');
		$order_payments->where('id_order_invoice', '=', $this->id);
		return $order_payments;
	}

	/**
	 * Get the formatted number of invoice
	 * @since 1.5.0.2
	 * @param int $id_lang for invoice_prefix
	 * @return string
	 */
	public function getInvoiceNumberFormatted($id_lang)
	{
		return '#'.Configuration::get('PS_INVOICE_PREFIX', $id_lang).sprintf('%06d', $this->number);
	}
}