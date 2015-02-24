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

/**
 * @since 1.5
 */
class HTMLTemplateInvoiceCore extends HTMLTemplate
{
	public $order;
	public $available_in_your_account = false;

	public function __construct(OrderInvoice $order_invoice, $smarty)
	{
		$this->order_invoice = $order_invoice;
		$this->order = new Order((int)$this->order_invoice->id_order);
		$this->smarty = $smarty;

		// header informations
		$this->date = Tools::displayDate($order_invoice->date_add);

		$id_lang = Context::getContext()->language->id;
		$this->title = $order_invoice->getInvoiceNumberFormatted($id_lang);
		// footer informations
		$this->shop = new Shop((int)$this->order->id_shop);
	}

	private function computeLayout($tax_excluded_display, $display_product_images)
	{
		$layout = array(
			'reference' => array(
				'width' => 40
			),
			'unit_price_tax_excl' => array(
				'width' => 0
			),
			'discount' => array(
				'width' => 0
			),
			'quantity' => array(
				'width' => 0
			),
			'total' => array(
				'width' => 0
			)
		);

		if (!$tax_excluded_display)
		{
			$layout['unit_price_tax_incl'] = array('width' => 0);
		}

		if ($display_product_images)
		{
			$layout['image'] = array('width' => 0);
		}

		$total_width = 0;
		$free_columns_count = 0;
		foreach ($layout as $data)
		{
			if ($data['width'] === 0)
			{
				++$free_columns_count;
			}
			$total_width += $data['width'];
		}

		$delta = 100 - $total_width;

		foreach ($layout as $row => $data)
		{
			if ($data['width'] === 0)
			{
				$layout[$row]['width'] = $delta / $free_columns_count;
			}
		}

		$layout['_colCount'] = count($layout);

		return $layout;
	}

	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getContent()
	{
		$invoice_address = new Address((int)$this->order->id_address_invoice);
		$country = new Country((int)$invoice_address->id_country);

		$formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');
		$formatted_delivery_address = '';

		if ($this->order->id_address_delivery != $this->order->id_address_invoice)
		{
			$delivery_address = new Address((int)$this->order->id_address_delivery);
			$formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');
		}

		$customer = new Customer((int)$this->order->id_customer);

		$order_details = $this->order_invoice->getProducts();
		if (Configuration::get('PS_PDF_IMG_INVOICE'))
		{
			foreach ($order_details as &$order_detail)
			{
				if ($order_detail['image'] != null)
				{
					$name = 'product_mini_'.(int)$order_detail['product_id'].(isset($order_detail['product_attribute_id']) ? '_'.(int)$order_detail['product_attribute_id'] : '').'.jpg';
					$order_detail['image_tag'] = ImageManager::thumbnail(_PS_IMG_DIR_.'p/'.$order_detail['image']->getExistingImgPath().'.jpg', $name, 45, 'jpg', false);
					if (file_exists(_PS_TMP_IMG_DIR_.$name))
						$order_detail['image_size'] = getimagesize(_PS_TMP_IMG_DIR_.$name);
					else
						$order_detail['image_size'] = false;
				}
			}
			unset($order_detail); // don't overwrite the last order_detail later
		}

		$cart_rules = $this->order->getCartRules($this->order_invoice->id);
		$free_shipping = false;
		foreach ($cart_rules as $key => $cart_rule)
		{
			if ($cart_rule['free_shipping'])
			{
				$free_shipping = true;
				/**
				 * Adjust cart rule value to remove the amount of the shipping.
				 * We're not interested in displaying the shipping discount as it is already shown as "Free Shipping".
				 */
				$cart_rules[$key]['value_tax_excl'] -= $this->order_invoice->total_shipping_tax_excl;
				$cart_rules[$key]['value'] -= $this->order_invoice->total_shipping_tax_incl;

				/**
				 * Don't display cart rules that are only about free shipping and don't create
				 * a discount on products.
				 */
				if ($cart_rules[$key]['value'] == 0)
					unset($cart_rules[$key]);
			}
		}

		$product_taxes = 0;
		foreach ($this->order_invoice->getProductTaxesBreakdown($this->order) as $details)
		{
			$product_taxes += $details['total_amount'];
		}

		$product_discounts_tax_excl = $this->order_invoice->total_discount_tax_excl;
		$product_discounts_tax_incl = $this->order_invoice->total_discount_tax_incl;
		if ($free_shipping)
		{
			$product_discounts_tax_excl -= $this->order_invoice->total_shipping_tax_excl;
			$product_discounts_tax_incl -= $this->order_invoice->total_shipping_tax_incl;
		}

		$products_after_discounts_tax_excl = $this->order_invoice->total_products - $product_discounts_tax_excl;
		$products_after_discounts_tax_incl = $this->order_invoice->total_products_wt - $product_discounts_tax_incl;

		$shipping_tax_excl = $free_shipping ? 0 : $this->order_invoice->total_shipping_tax_excl;
		$shipping_tax_incl = $free_shipping ? 0 : $this->order_invoice->total_shipping_tax_incl;
		$shipping_taxes = $shipping_tax_incl - $shipping_tax_excl;

		$wrapping_taxes = $this->order_invoice->total_wrapping_tax_incl - $this->order_invoice->total_wrapping_tax_excl;

		$total_taxes = $this->order_invoice->total_paid_tax_incl - $this->order_invoice->total_paid_tax_excl;

		$footer = array(
			'products_before_discounts_tax_excl' => $this->order_invoice->total_products,
			'product_discounts_tax_excl' => $product_discounts_tax_excl,
			'products_after_discounts_tax_excl' => $products_after_discounts_tax_excl,
			'products_before_discounts_tax_incl' => $this->order_invoice->total_products_wt,
			'product_discounts_tax_incl' => $product_discounts_tax_incl,
			'products_after_discounts_tax_incl' => $products_after_discounts_tax_incl,
			'product_taxes' => $product_taxes,
			'shipping_tax_excl' => $shipping_tax_excl,
			'shipping_taxes' => $shipping_taxes,
			'shipping_tax_incl' => $shipping_tax_incl,
			'wrapping_tax_excl' => $this->order_invoice->total_wrapping_tax_excl,
			'wrapping_taxes' => $wrapping_taxes,
			'wrapping_tax_incl' => $this->order_invoice->total_wrapping_tax_incl,
			'ecotax_taxes' => $total_taxes - $product_taxes - $wrapping_taxes - $shipping_taxes,
			'total_taxes' => $total_taxes,
			'total_paid_tax_excl' => $this->order_invoice->total_paid_tax_excl,
			'total_paid_tax_incl' => $this->order_invoice->total_paid_tax_incl
		);

		foreach ($footer as $key => $value) {
			$footer[$key] = Tools::ps_round($value, _PS_PRICE_COMPUTE_PRECISION_, $this->order->round_mode);
		}

		/**
		 * Need the $round_mode for the tests.
		 */
		$round_type = null;
		switch ($this->order->round_type)
		{
			case Order::ROUND_TOTAL:
				$round_type = 'total';
				break;
			case Order::ROUND_LINE;
				$round_type = 'line';
				break;
			case Order::ROUND_ITEM:
				$round_type = 'item';
				break;
			default:
				$round_type = 'line';
				break;
		}

		$display_product_images = Configuration::get('PS_PDF_IMG_INVOICE');
		$tax_excluded_display = Group::getPriceDisplayMethod($customer->id_default_group);

		$data = array(
			'order' => $this->order,
			'order_details' => $order_details,
			'cart_rules' => $cart_rules,
			'delivery_address' => $formatted_delivery_address,
			'invoice_address' => $formatted_invoice_address,
			'tax_excluded_display' => $tax_excluded_display,
			'display_product_images' => $display_product_images,
			'layout' => $this->computeLayout($tax_excluded_display, $display_product_images),
			'tax_tab' => $this->getTaxTabContent(),
			'customer' => $customer,
			'footer' => $footer,
			'ps_price_compute_precision' => _PS_PRICE_COMPUTE_PRECISION_,
			'round_type' => $round_type
		);

		if (Tools::getValue('debug'))
			die(json_encode($data));

		$this->smarty->assign($data);

		return $this->smarty->fetch($this->getTemplateByCountry($country->iso_code));
	}

	/**
	 * Returns the tax tab content
	 */
	public function getTaxTabContent()
	{
		$debug = Tools::getValue('debug');

		$address = new Address((int)$this->order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
		$tax_exempt = Configuration::get('VATNUMBER_MANAGEMENT')
							&& !empty($address->vat_number)
							&& $address->id_country != Configuration::get('VATNUMBER_COUNTRY');
		$carrier = new Carrier($this->order->id_carrier);

		$data = array(
			'tax_exempt' => $tax_exempt,
			'use_one_after_another_method' => $this->order_invoice->useOneAfterAnotherTaxComputationMethod(),
			'display_tax_bases_in_breakdowns' => $this->order_invoice->displayTaxBasesInProductTaxesBreakdown(),
			'product_tax_breakdown' => $this->order_invoice->getProductTaxesBreakdown($this->order),
			'shipping_tax_breakdown' => $this->order_invoice->getShippingTaxesBreakdown($this->order),
			'ecotax_tax_breakdown' => $this->order_invoice->getEcoTaxTaxesBreakdown(),
			'wrapping_tax_breakdown' => $this->order_invoice->getWrappingTaxesBreakdown(),
			'order' => $debug ? null : $this->order,
			'order_invoice' => $debug ? null : $this->order_invoice,
			'carrier' => $debug ? null : $carrier
		);

		if ($debug)
			return $data;

		$this->smarty->assign($data);

		return $this->smarty->fetch($this->getTemplate('invoice.tax-tab'));
	}

	/**
	 * Returns the invoice template associated to the country iso_code
	 * @param string $iso_country
	 */
	protected function getTemplateByCountry($iso_country)
	{
		$file = Configuration::get('PS_INVOICE_MODEL');

		// try to fetch the iso template
		$template = $this->getTemplate($file.'.'.$iso_country);

		// else use the default one
		if (!$template)
			$template = $this->getTemplate($file);

		return $template;
	}

	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
	public function getBulkFilename()
	{
		return 'invoices.pdf';
	}

	/**
	 * Returns the template filename
	 * @return string filename
	 */
	public function getFilename()
	{
		return Configuration::get('PS_INVOICE_PREFIX', Context::getContext()->language->id, null, $this->order->id_shop).sprintf('%06d', $this->order_invoice->number).'.pdf';
	}
}
