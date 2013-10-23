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
class HTMLTemplateDeliverySlipCore extends HTMLTemplate
{
	public $order;

	public function __construct(OrderInvoice $order_invoice, $smarty)
	{
		$this->order_invoice = $order_invoice;
		$this->order = new Order($this->order_invoice->id_order);
		$this->smarty = $smarty;

		// header informations
		$date = $this->order->invoice_date;
		if (Configuration::get('PS_EDS') && Configuration::get('PS_EDS_INVOICE_DELIVERED') &&  $this->order_invoice->delivery_number > 0)
		{
			$this->order_delivery = new OrderDelivery($this->order_invoice->id_order);
			$date = $this->order_delivery->getDeliveryDate($this->order_invoice->delivery_number, $this->order_invoice->id_order);
		}

		$this->date = Tools::displayDate($date);

		$title = 'Delivery';
		if ($this->getOrderTemplate($this->order->id) == 'delivery-slip-sampleorder')
			$title = 'Sample Delivery';

		if (Configuration::get('PS_EDS') && Configuration::get('PS_EDS_INVOICE_DELIVERED') &&  $this->order_invoice->delivery_number > 0)
			$this->title = HTMLTemplateDeliverySlip::l($title).' #'.Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id).sprintf('%06d', $this->order_invoice->id).'-'.$this->order_invoice->delivery_number;
		else
			$this->title = HTMLTemplateDeliverySlip::l($title).' #'.Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id).sprintf('%06d', $this->order_invoice->delivery_number);

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
		$delivery_address = new Address((int)$this->order->id_address_delivery);
		$formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');
		$formatted_invoice_address = '';

		if ($this->order->id_address_delivery != $this->order->id_address_invoice)
		{
			$invoice_address = new Address((int)$this->order->id_address_invoice);
			$formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');
		}

		if (Configuration::get('PS_EDS') && Configuration::get('PS_EDS_INVOICE_DELIVERED') &&  $this->order_invoice->delivery_number > 0)
		{
			$products = $this->order->getProductsDelivery(false, false, false, $this->order_invoice->delivery_number); // get only deliverd products
			if ($products)
				$products = $products[$this->order_invoice->delivery_number];
			else
				$products = $this->order_invoice->getProducts();
		}
		else
			$products = $this->order_invoice->getProducts();

		$sample_text = false;
		if (Configuration::get('PS_EDS') && Configuration::get('PS_EDS_SAMPLE_TEXT', (int)Context::getContext()->language->id, null, (int)$this->order->id_shop) != '')
			$sample_text = Configuration::get('PS_EDS_SAMPLE_TEXT', (int)Context::getContext()->language->id, null, (int)$this->order->id_shop);

		$carrier = new Carrier($this->order->id_carrier);
		$carrier->name = ($carrier->name == '0' ? Configuration::get('PS_SHOP_NAME') : $carrier->name);
		$this->smarty->assign(array(
			'order' => $this->order,
			'order_details' => $products,
			'delivery_address' => $formatted_delivery_address,
			'invoice_address' => $formatted_invoice_address,
			'order_invoice' => $this->order_invoice,
			'carrier' => $carrier,
			'sample_text' => $sample_text,
		));

		return $this->smarty->fetch($this->getTemplateByCountry($country->iso_code, $this->order->id));
	}

	/**
	 * Returns the invoice template associated to the country iso_code
	 * @param string $iso_country
	 */
	protected function getTemplateByCountry($iso_country, $id_order)
	{
		// set default slip
		$file = 'delivery-slip';
		if (Configuration::get('PS_EDS'))
		{
			if ($order_template = $this->getOrderTemplate($id_order)) // get slip specific for order
				return $this->getTemplate($order_template);

			$file = Configuration::get('PS_DELIVERY_MODEL'); // set default slip set by eds settings
		}

		// try to fetch the iso template
		$template = $this->getTemplate($file.'.'.$iso_country);

		// else use the default one
		if (!$template)
			$template = $this->getTemplate($file);

		return $template;
	}

	protected function getOrderTemplate($id_order)
	{
			$template = Db::getInstance()->executeS('
			SELECT `delivery-slip`
			FROM `'._DB_PREFIX_.'order_template`
			WHERE `id_order` = '.$id_order);

			if ($template)
				$template = $template[0]['delivery-slip'];
			return $template;
	}

	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
	public function getBulkFilename()
	{
		return 'deliveries.pdf';
	}

	/**
	 * Returns the template filename
	 * @return string filename
	 */
	public function getFilename()
	{
		if (Configuration::get('PS_EDS') && Configuration::get('PS_EDS_INVOICE_DELIVERED') &&  $this->order_invoice->delivery_number > 0)
			return Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id, null, $this->order->id_shop).sprintf('%06d', $this->order_invoice->id).'-'.$this->order_invoice->delivery_number.'.pdf';
		else
			return Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id, null, $this->order->id_shop).sprintf('%06d', $this->order_invoice->id).'.pdf';
	}
}

