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
class HTMLTemplatePackageSlipCore extends HTMLTemplate 
{
	public $order;
	public $available_in_your_account = false;

	public function __construct(OrderInvoice $order_invoice, $smarty)
	{
		$this->order_invoice = $order_invoice;
		$this->order = new Order($this->order_invoice->id_order);
		$this->smarty = $smarty;

		// header informations
		$this->date = Tools::displayDate(date('Y-m-d H:i:s'));
		$this->title = HTMLTemplatePackageSlip::l('Package Slip').' #'.sprintf('%06d', $this->order_invoice->id_order);

		// footer informations
		$this->shop = new Shop((int)$this->order->id_shop);
	}

	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getContent()
	{
		$delivery_address = new Address((int)$this->order->id_address_delivery);
		$formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');
		$formatted_invoice_address = '';

		if ($this->order->id_address_delivery != $this->order->id_address_invoice)
		{
			$invoice_address = new Address((int)$this->order->id_address_invoice);
			$formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');
		}
		
		$order_details = $this->order->getProducts();
		$delivery_products = $this->order->getProductsDelivery();
		$deliverd_products = array();
		foreach ($delivery_products as $delivery)
		{
				foreach ($delivery as $product)
				{
					if ($product['shipped'] == 1)
					{
						if (!empty($deliverd_products[$product['product_id'].'_'.$product['product_attribute_id']]))
							$deliverd_products[$product['product_id'].'_'.$product['product_attribute_id']] += $product['product_quantity'];
						else
							$deliverd_products[$product['product_id'].'_'.$product['product_attribute_id']] = $product['product_quantity'];
					}
					else
					{
						if (!empty($deliverd_products[$product['product_id'].'_'.$product['product_attribute_id'].'_current']))
							$deliverd_products[$product['product_id'].'_'.$product['product_attribute_id'].'_current'] += $product['product_quantity'];
						else
							$deliverd_products[$product['product_id'].'_'.$product['product_attribute_id'].'_current'] = $product['product_quantity'];
					}
				}
		}
		foreach ($order_details as &$order_detail)
		{
			$order_detail['warehouse_name'] = '--';
			$order_detail['warehouse_location'] = '--';
			if ($order_detail['id_warehouse'] != 0)
			{
				$warehouse = new Warehouse((int)$order_detail['id_warehouse']);
				$warehouse_location = $warehouse->getProductLocation($order_detail['product_id'], $order_detail['product_attribute_id'], $warehouse->id);
				$order_detail['warehouse_name'] = $warehouse->name;
				if ($warehouse_location != '')
					$order_detail['warehouse_location'] = $warehouse_location;
			}
			if ($order_detail['image'] != null)
			{
				$name = 'product_mini_'.(int)$order_detail['product_id'].(isset($order_detail['product_attribute_id']) ? '_'.(int)$order_detail['product_attribute_id'] : '').'.jpg';
				// generate image cache, only for back office
				$order_detail['image_tag'] = ImageManager::thumbnail(_PS_IMG_DIR_.'p/'.$order_detail['image']->getExistingImgPath().'.jpg', $name, 45, 'jpg', true);
				if (file_exists(_PS_TMP_IMG_DIR_.$name))
					$order_detail['image_size'] = getimagesize(_PS_TMP_IMG_DIR_.$name);
				else
					$order_detail['image_size'] = false;
			}
			if (!empty($deliverd_products))
			{
				if (isset($deliverd_products[$order_detail['product_id'].'_'.$order_detail['product_attribute_id']])) // Removed delivered products qty
					$order_detail['product_quantity'] = $order_detail['product_quantity'] - $deliverd_products[$order_detail['product_id'].'_'.$order_detail['product_attribute_id']];

				if (isset($deliverd_products[$order_detail['product_id'].'_'.$order_detail['product_attribute_id'].'_current'])) // Removed deliverd products qty and qty on unshipped delivery
					$order_detail['product_quantity_current'] = ($order_detail['product_quantity'] - $deliverd_products[$order_detail['product_id'].'_'.$order_detail['product_attribute_id'].'_current']);
			}
		}
	
		$carrier = new Carrier($this->order->id_carrier);
		$carrier->name = ($carrier->name == '0' ? Configuration::get('PS_SHOP_NAME') : $carrier->name);
		$this->smarty->assign(array(
			'order' => $this->order,
			'order_details' => $order_details,
			'delivery_address' => $formatted_delivery_address,
			'invoice_address' => $formatted_invoice_address,
			'order_invoice' => $this->order_invoice,
			'carrier' => $carrier
		));

		return $this->smarty->fetch($this->getTemplate('package-slip'));
	}

	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
	public function getBulkFilename()
	{
		return 'packageslips.pdf';
	}

	/**
	 * Returns the template filename
	 * @return string filename
	 */
	public function getFilename()
	{
		return 'PACKSLIP'.sprintf('%06d', $this->order_invoice->id_order).'.pdf';
	}
}

