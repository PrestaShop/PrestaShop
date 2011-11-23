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

class OrderInvoiceCore extends ObjectModel
{
	/** @var integer */
	public $id_order;

	/** @var integer */
	public $number;

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

	/** @var intger */
	public $date_add;

	protected $fieldsRequired = array('id_order', 'number');
	protected $fieldsValidate = array('id_order' => 'isUnsignedId', 'number' => 'isUnsignedId');

	protected $table = 'order_invoice';
	protected $identifier = 'id_order_invoice';

	public function getFields()
	{
		$this->validateFields();

		$fields['id_order'] = (int)$this->id_order;
		$fields['number'] = (int)$this->number;
		$fields['total_discount_tax_excl'] = (float)$this->total_discount_tax_excl;
		$fields['total_discount_tax_incl'] = (float)$this->total_discount_tax_incl;
		$fields['total_paid_tax_excl'] = (float)$this->total_paid_tax_excl;
		$fields['total_paid_tax_incl'] = (float)$this->total_paid_tax_incl;
		$fields['total_products'] = (float)$this->total_products;
		$fields['total_products_wt'] = (float)$this->total_products_wt;
		$fields['total_shipping_tax_excl'] = (float)$this->total_shipping_tax_excl;
		$fields['total_shipping_tax_incl'] = (float)$this->total_shipping_tax_incl;
		$fields['total_wrapping_tax_excl'] = (float)$this->total_wrapping_tax_excl;
		$fields['total_wrapping_tax_incl'] = (float)$this->total_wrapping_tax_incl;
		$fields['date_add'] = pSQL($this->date_add);

		return $fields;
	}

	public function getProductsDetail()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'order_detail` od
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
		foreach ($products AS $row)
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

			$this->setProductImageInformations($row);
			$this->setProductCurrentStock($row);
			$this->setProductPrices($row, $order);
			$this->setProductCustomizedDatas($row, $customized_datas);

			// Add information for virtual product
			if ($row['download_hash'] && !empty($row['download_hash']))
			{
				if ($row['product_attribute_id'] && !empty($row['product_attribute_id']))
					$row['filename'] = ProductDownload::getFilenameFromIdAttribute((int)$row['product_id'], (int)$row['product_attribute_id']);
				else
					$row['filename'] = ProductDownload::getFilenameFromIdProduct((int)$row['product_id']);
				// Get the display filename
				$row['display_filename'] = ProductDownload::getFilenameFromFilename($row['filename']);
			}
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
		$product['current_stock'] = StockManagerFactory::getManager()->getProductPhysicalQuantities($product['product_id'], $product['product_attribute_id'], null, true);
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

	public function setProductPrices(&$row, $order)
	{
		$tax_calculator = OrderDetail::getTaxCalculatorStatic((int)$row['id_order_detail']);
		$row['tax_calculator'] = $tax_calculator;
		$row['tax_rate'] = $tax_calculator->getTotalRate();

		if ($order->getTaxCalculationMethod() == PS_TAX_EXC)
			$row['product_price'] = Tools::ps_round($row['product_price'], 2);
		else
			$row['product_price_wt'] = Tools::ps_round($tax_calculator->addTaxes($row['product_price']), 2);

		$group_reduction = 1;
		if ($row['group_reduction'] > 0)
			$group_reduction =  1 - $row['group_reduction'] / 100;

		if ($row['reduction_percent'] != 0)
		{
			if ($order->getTaxCalculationMethod() == PS_TAX_EXC)
				$row['product_price'] = ($row['product_price'] - $row['product_price'] * ($row['reduction_percent'] * 0.01));
			else
				$row['product_price_wt'] = Tools::ps_round(($row['product_price_wt'] - $row['product_price_wt'] * ($row['reduction_percent'] * 0.01)), 2);
		}

		if ($row['reduction_amount'] != 0)
		{
			if ($this->_taxCalculationMethod == PS_TAX_EXC)
				$row['product_price'] = ($row['product_price'] - ($tax_calculator->removeTaxes($row['reduction_amount'])));
			else
				$row['product_price_wt'] = Tools::ps_round(($row['product_price_wt'] - $row['reduction_amount']), 2);
		}

		if ($row['group_reduction'] > 0)
		{
			if ($this->_taxCalculationMethod == PS_TAX_EXC)
				$row['product_price'] = $row['product_price'] * $group_reduction;
			else
				$row['product_price_wt'] = Tools::ps_round($row['product_price_wt'] * $group_reduction , 2);
		}

		if (($row['reduction_percent'] OR $row['reduction_amount'] OR $row['group_reduction']) AND $order->getTaxCalculationMethod() == PS_TAX_EXC)
			$row['product_price'] = Tools::ps_round($row['product_price'], 2);

		if ($order->getTaxCalculationMethod() == PS_TAX_EXC)
			$row['product_price_wt'] = Tools::ps_round($tax_calculator->addTaxes($row['product_price']), 2) + Tools::ps_round($row['ecotax'] * (1 + $row['ecotax_tax_rate'] / 100), 2);
		else
		{
			$row['product_price_wt_but_ecotax'] = $row['product_price_wt'];
			$row['product_price_wt'] = Tools::ps_round($row['product_price_wt'] + $row['ecotax'] * (1 + $row['ecotax_tax_rate'] / 100), 2);
		}

		$row['total_wt'] = $row['product_quantity'] * $row['product_price_wt'];
		$row['total_price'] = $row['product_quantity'] * $row['product_price'];
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
		WHERE od.`id_order` = '.(int)$this->id_order.'
		AND od.`id_order_invoice` = '.(int)$this->id.'
		AND od.`tax_computation_method` = '.(int)TaxCalculator::ONE_AFTER_ANOTHER_METHOD
		);
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
		WHERE `id_order` = '.(int)$this->id_order.'
		AND `id_order_invoice` = '.(int)$this->id
		);
	}
}