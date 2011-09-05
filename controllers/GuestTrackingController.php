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
*  @version  Release: $Revision: 7095 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class GuestTrackingControllerCore extends FrontController
{
	public $php_self = 'guest-tracking';
	
	public function preProcess()
	{
		parent::preProcess();
		
		if ($this->context->customer->isLogged())
			Tools::redirect('index.php?controller=history');
	}
	
	public function process()
	{
		global $link;
		
		parent::process();
		
		if ($id_order = Tools::getValue('id_order') AND $email = Tools::getValue('email'))
		{
			$order = new Order((int)$id_order);
			if (!Validate::isLoadedObject($order))
			    $this->errors[] = Tools::displayError('Invalid order');
			elseif (!$order->isAssociatedAtGuest($email))
			    $this->errors[] = Tools::displayError('Invalid order');
			else
			{
				$customer = new Customer((int)$order->id_customer);
			    $id_order_state = (int)($order->getCurrentState());
			    $carrier = new Carrier((int)($order->id_carrier), (int)($order->id_lang));
			    $addressInvoice = new Address((int)($order->id_address_invoice));
			    $addressDelivery = new Address((int)($order->id_address_delivery));
				$inv_adr_fields = AddressFormat::getOrderedAddressFields($addressInvoice->id_country);
				$dlv_adr_fields = AddressFormat::getOrderedAddressFields($addressDelivery->id_country);
			    
				$invoiceAddressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($addressInvoice, $inv_adr_fields);
				$deliveryAddressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($addressDelivery, $dlv_adr_fields);
				
			    if ($order->total_discounts > 0)
			    	$this->context->smarty->assign('total_old', (float)($order->total_paid - $order->total_discounts));
			    $products = $order->getProducts();
			    $customizedDatas = Product::getAllCustomizedDatas((int)($order->id_cart));
			    Product::addCustomizationPrice($products, $customizedDatas);
	
			    $this->processAddressFormat($addressDelivery, $addressInvoice);	
			    $this->context->smarty->assign(array(
			    	'shop_name' => Configuration::get('PS_SHOP_NAME'),
			    	'order' => $order,
			    	'return_allowed' => false,
			    	'currency' => new Currency($order->id_currency),
			    	'order_state' => (int)($id_order_state),
			    	'invoiceAllowed' => (int)(Configuration::get('PS_INVOICE')),
			    	'invoice' => (OrderState::invoiceAvailable((int)($id_order_state)) AND $order->invoice_number),
			    	'order_history' => $order->getHistory($this->context->language->id, false, true),
			    	'products' => $products,
			    	'discounts' => $order->getDiscounts(),
			    	'carrier' => $carrier,
			    	'address_invoice' => $addressInvoice,
			    	'invoiceState' => (Validate::isLoadedObject($addressInvoice) AND $addressInvoice->id_state) ? new State((int)($addressInvoice->id_state)) : false,
			    	'address_delivery' => $addressDelivery,
			    	'deliveryState' => (Validate::isLoadedObject($addressDelivery) AND $addressDelivery->id_state) ? new State((int)($addressDelivery->id_state)) : false,
			    	'is_guest' => true,
			    	'group_use_tax' => (Group::getPriceDisplayMethod($customer->id_default_group) == PS_TAX_INC),
			    	'CUSTOMIZE_FILE' => Product::CUSTOMIZE_FILE,
			    	'CUSTOMIZE_TEXTFIELD' => Product::CUSTOMIZE_TEXTFIELD,
			    	'use_tax' => Configuration::get('PS_TAX'),
			    	'customizedDatas' => $customizedDatas,
			    	'invoiceAddressFormatedValues' => $invoiceAddressFormatedValues,
			    	'deliveryAddressFormatedValues' => $deliveryAddressFormatedValues));
			    if ($carrier->url AND $order->shipping_number)
			    	$this->context->smarty->assign('followup', str_replace('@', $order->shipping_number, $carrier->url));
			    $this->context->smarty->assign('HOOK_ORDERDETAILDISPLAYED', Module::hookExec('orderDetailDisplayed', array('order' => $order)));
			    Module::hookExec('OrderDetail', array('carrier' => $carrier, 'order' => $order));
				
				if (Tools::isSubmit('submitTransformGuestToCustomer'))
				{
					$customer = new Customer((int)$order->id_customer);
					if (!Validate::isLoadedObject($customer))
						$this->errors[] = Tools::displayError('Invalid customer');
					if (!$customer->transformToCustomer($this->context->language->id, Tools::getValue('password')))
						$this->errors[] = Tools::displayError('An error occurred while transforming guest to customer.');
					if (!Tools::getValue('password'))
						$this->errors[] = Tools::displayError('Invalid password');
					else
						$this->context->smarty->assign('transformSuccess', true);
				}
			}
			if (sizeof($this->errors))
				/* Handle brute force attacks */
				sleep(1);
		}
		
		$this->context->smarty->assign(array(
			'action' => $link->getPageLink('guest-tracking.php'),
			'errors' => $this->errors
		));
	}
	
	public function setMedia()
	{
		parent::setMedia();
		
		$this->addCSS(_THEME_CSS_DIR_.'history.css');
		$this->addCSS(_THEME_CSS_DIR_.'addresses.css');
	}
	
	public function displayContent()
	{
		parent::displayContent();
		
		$this->context->smarty->display(_PS_THEME_DIR_.'guest-tracking.tpl');
	}

	private function processAddressFormat(Address $delivery, Address $invoice)
	{
		$inv_adr_fields = AddressFormat::getOrderedAddressFields($invoice->id_country, false, true);
		$dlv_adr_fields = AddressFormat::getOrderedAddressFields($delivery->id_country, false, true);

		$this->context->smarty->assign('inv_adr_fields', $inv_adr_fields);
		$this->context->smarty->assign('dlv_adr_fields', $dlv_adr_fields);

	}
}
