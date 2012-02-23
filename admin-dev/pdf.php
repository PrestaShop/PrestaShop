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

define('_PS_ADMIN_DIR_', getcwd());

include(_PS_ADMIN_DIR_.'/../config/config.inc.php');

if (!Context::getContext()->employee->id)
	Tools::redirectAdmin('index.php?controller=AdminLogin');

$function_array = array(
	'pdf' => 'generateInvoicePDF',
	'id_order_slip' => 'generateOrderSlipPDF',
	'id_delivery' => 'generateDeliverySlipPDF',
	'delivery' => 'generateDeliverySlipPDF',
	'invoices' => 'generateInvoicesPDF',
	'invoices2' => 'generateInvoicesPDF2',
	'slips' => 'generateOrderSlipsPDF',
	'deliveryslips' => 'generateDeliverySlipsPDF',
	'id_supply_order' => 'generateSupplyOrderFormPDF'
);

foreach ($function_array as $var => $function)
	if (isset($_GET[$var]))
	{
		call_user_func($function);
		die;
	}

function generateSupplyOrderFormPDF()
{
	if (!Tools::isSubmit('id_supply_order'))
		die (Tools::displayError('Missing supply order ID'));

	$id_supply_order = (int)Tools::getValue('id_supply_order');
	$supply_order = new SupplyOrder($id_supply_order);

	if (!Validate::isLoadedObject($supply_order))
		die(Tools::displayError('Cannot find this supply order in the database'));

	generatePDF($supply_order, PDF::TEMPLATE_SUPPLY_ORDER_FORM);
}

function generateInvoicePDF()
{
	if (Tools::isSubmit('id_order'))
		generateInvoicePDFByIdOrder(Tools::getValue('id_order'));
	elseif (Tools::isSubmit('id_order_invoice'))
		generateInvoicePDFByIdOrderInvoice(Tools::getValue('id_order_invoice'));
	else
		die (Tools::displayError('Missing order ID or invoice order ID'));
	exit;
}

function generateInvoicePDFByIdOrder($id_order)
{
	$order = new Order((int)$id_order);
	if (!Validate::isLoadedObject($order))
		die(Tools::displayError('Cannot find order in database'));

	$order_invoice_list = $order->getInvoicesCollection();
	Hook::exec('actionPDFInvoiceRender', array('order_invoice_list' => $order_invoice_list));
	generatePDF($order_invoice_list, PDF::TEMPLATE_INVOICE);
}

function generateInvoicePDFByIdOrderInvoice($id_order_invoice)
{
	$order_invoice = new OrderInvoice((int)$id_order_invoice);
	if (!Validate::isLoadedObject($order_invoice))
		die(Tools::displayError('Cannot find order invoice in database'));

	Hook::exec('actionPDFInvoiceRender', array('order_invoice_list' => array($order_invoice)));
	generatePDF($order_invoice, PDF::TEMPLATE_INVOICE);
}

function generateOrderSlipPDF()
{
	$orderSlip = new OrderSlip((int)Tools::getValue('id_order_slip'));
	$order = new Order((int)$orderSlip->id_order);
	if (!Validate::isLoadedObject($order))
		die(Tools::displayError('Cannot find order in database'));
	$order->products = OrderSlip::getOrdersSlipProducts($orderSlip->id, $order);

	generatePDF($orderSlip, PDF::TEMPLATE_ORDER_SLIP);
}

function generateDeliverySlipPDF()
{
	if (Tools::isSubmit('id_order'))
		generateDeliverySlipPDFByIdOrder((int)Tools::getValue('id_order'));
	elseif (Tools::isSubmit('id_order_invoice'))
		generateDeliverySlipPDFByIdOrderInvoice((int)Tools::getValue('id_order_invoice'));
	elseif (Tools::isSubmit('id_delivery'))
	{
		$order = Order::getByDelivery((int)Tools::getValue('id_delivery'));
		generateDeliverySlipPDFByIdOrder((int)$order->id);
	}
	else
		die (Tools::displayError('Missing order ID or invoice order ID'));
	exit;
}

function generateDeliverySlipPDFByIdOrder($id_order)
{
	$order = new Order((int)$id_order);
	if (!Validate::isLoadedObject($order))
		throw new PrestaShopException('Can\'t load Order object');

	$order_invoice_collection = $order->getInvoicesCollection();
	generatePDF($order_invoice_collection, PDF::TEMPLATE_DELIVERY_SLIP);
}

function generateDeliverySlipPDFByIdOrderInvoice($id_order_invoice)
{
	$order_invoice = new OrderInvoice((int)$id_order_invoice);
	if (!Validate::isLoadedObject($order_invoice))
		throw new PrestaShopException('Can\'t load Order Invoice object');

	generatePDF($order_invoice, PDF::TEMPLATE_DELIVERY_SLIP);
}

function generateInvoicesPDF()
{
	$order_invoice_collection = OrderInvoice::getByDateInterval(Tools::getValue('date_from'), Tools::getValue('date_to'));

	if (!count($order_invoice_collection))
		die(Tools::displayError('No invoices found'));

	generatePDF($order_invoice_collection, PDF::TEMPLATE_INVOICE);
}

function generateInvoicesPDF2()
{
	$order_invoice_collection = array();
	foreach (explode('-', Tools::getValue('id_order_state')) as $id_order_state)
		if (is_array($order_invoices = OrderInvoice::getByStatus((int)$id_order_state)))
			$order_invoice_collection = array_merge($order_invoices, $order_invoice_collection);

	if (!count($order_invoice_collection))
		die(Tools::displayError('No invoices found'));

	generatePDF($order_invoice_collection, PDF::TEMPLATE_INVOICE);
}

function generateOrderSlipsPDF()
{
	$id_order_slips_list = OrderSlip::getSlipsIdByDate(Tools::getValue('date_from'), Tools::getValue('date_to'));
	if (!count($id_order_slips_list))
		die (Tools::displayError('No order slips found'));

	$order_slips = array();
	foreach ($id_order_slips_list as $id_order_slips)
		$order_slips[] = new OrderSlip((int)$id_order_slips);

	generatePDF($order_slips, PDF::TEMPLATE_ORDER_SLIP);
}

function generateDeliverySlipsPDF()
{
	$order_invoice_collection = OrderInvoice::getByDeliveryDateInterval(Tools::getValue('date_from'), Tools::getValue('date_to'));

	if (!count($order_invoice_collection))
		die(Tools::displayError('No invoices found'));

	generatePDF($order_invoice_collection, PDF::TEMPLATE_DELIVERY_SLIP);
}

function generatePDF($object, $template)
{
	$pdf = new PDF($object, $template, Context::getContext()->smarty);
	$pdf->render();
}