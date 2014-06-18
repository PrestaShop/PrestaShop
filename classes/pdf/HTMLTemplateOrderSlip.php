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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5
 */
class HTMLTemplateOrderSlipCore extends HTMLTemplateInvoice
{
	public $order;
	public $order_slip;

	public function __construct(OrderSlip $order_slip, $smarty)
	{
		$this->order_slip = $order_slip;
		$this->order = new Order((int)$order_slip->id_order);

		$products = OrderSlip::getOrdersSlipProducts($this->order_slip->id, $this->order);
		$customized_datas = Product::getAllCustomizedDatas((int)$this->order->id_cart);
		Product::addCustomizationPrice($products, $customized_datas);

		$this->order->products = $products;
		$this->smarty = $smarty;

		// header informations
		$this->date = Tools::displayDate($this->order_slip->date_add);
		$this->title = HTMLTemplateOrderSlip::l('Order slip #').Configuration::get('PS_CREDIT_SLIP_PREFIX', Context::getContext()->language->id).sprintf('%06d', (int)$this->order_slip->id);

		// footer informations
		$this->shop = new Shop((int)$this->order->id_shop);
	}

	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getContent()
	{
		$invoice_address = new Address((int)$this->order->id_address_invoice);
		$formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');
		$formatted_delivery_address = '';

		if ($this->order->id_address_delivery != $this->order->id_address_invoice)
		{
			$delivery_address = new Address((int)$this->order->id_address_delivery);
			$formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');
		}

		$customer = new Customer((int)$this->order->id_customer);

		$this->order->total_products = $this->order->total_products_wt = 0;
		foreach ($this->order->products as &$product)
		{
			$product['total_price_tax_excl'] = $product['unit_price_tax_excl'] * $product['product_quantity'];
			$product['total_price_tax_incl'] = $product['unit_price_tax_incl'] * $product['product_quantity'];
			if ($this->order_slip->partial == 1)
			{
				$order_slip_detail = Db::getInstance()->getRow('
					SELECT * FROM `'._DB_PREFIX_.'order_slip_detail`
					WHERE `id_order_slip` = '.(int)$this->order_slip->id.'
					AND `id_order_detail` = '.(int)$product['id_order_detail']);

				$product['total_price_tax_excl'] = $order_slip_detail['amount_tax_excl'];
				$product['total_price_tax_incl'] = $order_slip_detail['amount_tax_incl'];
			}
			$this->order->total_products += $product['total_price_tax_excl'];
			$this->order->total_products_wt += $product['total_price_tax_incl'];
			$this->order->total_paid_tax_excl = $this->order->total_products;
			$this->order->total_paid_tax_incl = $this->order->total_products_wt;
		}
		unset($product); // remove reference
		if ($this->order_slip->shipping_cost == 0)
			$this->order->total_shipping_tax_incl = $this->order->total_shipping_tax_excl = 0;

		if ($this->order_slip->partial == 1 && $this->order_slip->shipping_cost_amount > 0)
			$this->order->total_shipping_tax_incl = $this->order_slip->shipping_cost_amount;

		$tax = new Tax();
		$tax->rate = $this->order->carrier_tax_rate;
		$tax_calculator = new TaxCalculator(array($tax));
		$this->order->total_shipping_tax_excl = Tools::ps_round($tax_calculator->removeTaxes($this->order_slip->shipping_cost_amount), 2);
		

		$this->order->total_paid_tax_incl += $this->order->total_shipping_tax_incl;
		$this->order->total_paid_tax_excl += $this->order->total_shipping_tax_excl;

		$this->smarty->assign(array(
			'order' => $this->order,
			'order_slip' => $this->order_slip,
			'order_details' => $this->order->products,
			'delivery_address' => $formatted_delivery_address,
			'invoice_address' => $formatted_invoice_address,
			'tax_excluded_display' => Group::getPriceDisplayMethod((int)$customer->id_default_group),
			'tax_tab' => $this->getTaxTabContent(),
		));

		return $this->smarty->fetch($this->getTemplate('order-slip'));
	}

	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
	public function getBulkFilename()
	{
		return 'order-slips.pdf';
	}

	/**
	 * Returns the template filename
	 * @return string filename
	 */
	public function getFilename()
	{
		return 'order-slip-'.sprintf('%06d', $this->order_slip->id).'.pdf';
	}

	/**
	 * Returns the tax tab content
	 */
	public function getTaxTabContent()
	{
		$address = new Address((int)$this->order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
		$tax_exempt = Configuration::get('VATNUMBER_MANAGEMENT')
							&& !empty($address->vat_number)
							&& $address->id_country != Configuration::get('VATNUMBER_COUNTRY');

		$this->smarty->assign(array(
			'tax_exempt' => $tax_exempt,
			'use_one_after_another_method' => $this->order->useOneAfterAnotherTaxComputationMethod(),
			'product_tax_breakdown' => $this->getProductTaxesBreakdown(),
			'shipping_tax_breakdown' => $this->getShippingTaxesBreakdown(),
			'order' => $this->order,
			'ecotax_tax_breakdown' => $this->order_slip->getEcoTaxTaxesBreakdown(),
			'is_order_slip' => true
		));

		return $this->smarty->fetch($this->getTemplate('invoice.tax-tab'));
	}


	public function getProductTaxesBreakdown()
	{
		$tmp_tax_infos = array();
		$infos = array(
					'total_price_tax_excl' => 0,
					'total_amount' => 0
				);

		foreach ($this->order_slip->getOrdersSlipDetail((int)$this->order_slip->id) as $order_slip_details)
		{
			$tax_calculator = OrderDetail::getTaxCalculatorStatic((int)$order_slip_details['id_order_detail']);
			$tax_amount = $tax_calculator->getTaxesAmount($order_slip_details['amount_tax_excl']);

			if ($this->order->useOneAfterAnotherTaxComputationMethod())
			{
				foreach ($tax_amount as $tax_id => $amount)
				{
					$tax = new Tax((int)$tax_id);
					if (!isset($total_tax_amount[$tax->rate]))
					{
						$tmp_tax_infos[$tax->rate]['name'] = $tax->name;
						$tmp_tax_infos[$tax->rate]['total_price_tax_excl'] = $order_slip_details['amount_tax_excl'];
						$tmp_tax_infos[$tax->rate]['total_amount'] = $amount;
					}
					else
					{
						$tmp_tax_infos[$tax->rate]['total_price_tax_excl'] += $order_slip_details['amount_tax_excl'];
						$tmp_tax_infos[$tax->rate]['total_amount'] += $amount;
					}
				}
			} 
			else 
			{
				$tax_rate = 0;
				foreach ($tax_amount as $tax_id => $amount)
				{
					$tax = new Tax((int)$tax_id);
					$tax_rate = $tax->rate;
					$infos['total_price_tax_excl'] += (float)Tools::ps_round($order_slip_details['amount_tax_excl'], 2);
					$infos['total_amount'] += (float)Tools::ps_round($amount, 2);
				}
				$tmp_tax_infos[(string)number_format($tax_rate, 3)] = $infos;
			}
		}
		
		// Delete ecotax from the total
		$ecotax =  $this->order_slip->getEcoTaxTaxesBreakdown();
		if ($ecotax)
			foreach ($tmp_tax_infos as $rate => &$row)
			{
				if (!isset($ecotax[$rate]))
					continue;
				$row['total_price_tax_excl'] -= $ecotax[$rate]['ecotax_tax_excl'];
				$row['total_amount'] -= ($ecotax[$rate]['ecotax_tax_incl'] - $ecotax[$rate]['ecotax_tax_excl']);
			}
		
		return $tmp_tax_infos;
	}

	public function getShippingTaxesBreakdown()
	{
		$taxes_breakdown = array();
		$tax = new Tax();
		$tax->rate = $this->order->carrier_tax_rate;

		$tax_calculator = new TaxCalculator(array($tax));

		$total_tax_excl = $tax_calculator->removeTaxes($this->order_slip->shipping_cost_amount);
		$shipping_tax_amount = $this->order_slip->shipping_cost_amount - $total_tax_excl;

		if ($shipping_tax_amount > 0)
			$taxes_breakdown[] = array(
				'rate' =>  $this->order->carrier_tax_rate,
				'total_amount' => $shipping_tax_amount,
				'total_tax_excl' => $total_tax_excl,
			);

		return $taxes_breakdown;
	}
}


