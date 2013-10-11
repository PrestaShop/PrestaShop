<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
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

	public function __construct(OrderInvoice $order_invoice, $smarty,$delivery_nr = false)
	{
		$this->order_invoice = $order_invoice;
		$this->order = new Order((int)$this->order_invoice->id_order);
		$this->smarty = $smarty;
		$this->delivery_nr = $delivery_nr;

		// header informations
		$this->date = Tools::displayDate($order_invoice->date_add);

		$id_lang = Context::getContext()->language->id;
		$this->title = HTMLTemplateInvoice::l('Invoice ').' #'.Configuration::get('PS_INVOICE_PREFIX', $id_lang, null, (int)$this->order->id_shop).sprintf('%06d', $order_invoice->number) . ($this->delivery_nr ? '-' .$this->delivery_nr : '');
		// footer informations
		$this->shop = new Shop((int)$this->order->id_shop);
	}

	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getContent()
	{
		$country = new Country((int)$this->order->id_address_invoice);
		$invoice_address = new Address((int)$this->order->id_address_invoice);
		$formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');
		$formatted_delivery_address = '';

		if ($this->order->id_address_delivery != $this->order->id_address_invoice)
		{
			$delivery_address = new Address((int)$this->order->id_address_delivery);
			$formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');
		}

		$customer = new Customer((int)$this->order->id_customer);
		
		if($this->delivery_nr) {
			$products = $this->order->getProductsDelivery(false,false,false,$this->delivery_nr); // get only deliverd products
			$products = $products[$this->delivery_nr];
			$total_paid_tax_incl = 0;
			$total_paid_tax_excl = 0;
			foreach($products as &$product) {
				$product['product_quantity'] = $product['delivery_qty'];
					$total_price_tax_incl_product = ($product['unit_price_tax_incl'] * $product['delivery_qty']);
					$total_price_tax_excl_product = ($product['unit_price_tax_excl'] * $product['delivery_qty']);
					$total_paid_tax_incl += $total_price_tax_incl_product;
					$total_paid_tax_excl += $total_price_tax_excl_product;
			}
				$product['total_price_tax_incl'] = $total_price_tax_incl_product;
				$product['total_price_tax_excl'] = $total_price_tax_excl_product;
				$this->order_invoice->total_products_wt = $total_paid_tax_incl;
				$this->order_invoice->total_products = $total_paid_tax_excl;
		} else {
			$products = $this->order_invoice->getProducts();
		}

		$this->smarty->assign(array(
			'order' => $this->order,
			'order_details' => $products,
			'cart_rules' => $this->order->getCartRules($this->order_invoice->id),
			'delivery_address' => $formatted_delivery_address,
			'invoice_address' => $formatted_invoice_address,
			'tax_excluded_display' => Group::getPriceDisplayMethod($customer->id_default_group),
			'tax_tab' => $this->getTaxTabContent(),
			'customer' => $customer
		));

		return $this->smarty->fetch($this->getTemplateByCountry($country->iso_code));
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
			$carrier = new Carrier($this->order->id_carrier);
			
			$this->smarty->assign(array(
				'tax_exempt' => $tax_exempt,
				'use_one_after_another_method' => $this->order_invoice->useOneAfterAnotherTaxComputationMethod(),
				'product_tax_breakdown' => $this->order_invoice->getProductTaxesBreakdown(),
				'shipping_tax_breakdown' => $this->order_invoice->getShippingTaxesBreakdown($this->order),
				'ecotax_tax_breakdown' => $this->order_invoice->getEcoTaxTaxesBreakdown(),
				'wrapping_tax_breakdown' => $this->order_invoice->getWrappingTaxesBreakdown(),
				'order' => $this->order,
				'order_invoice' => $this->order_invoice,
				'carrier' => $carrier
			));

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
		return Configuration::get('PS_INVOICE_PREFIX', Context::getContext()->language->id, null, $this->order->id_shop).sprintf('%06d', $this->order_invoice->number) . ($this->delivery_nr ? '-' .$this->delivery_nr : '')  .'.pdf';
	}
}

