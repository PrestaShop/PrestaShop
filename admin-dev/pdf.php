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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

define('PS_ADMIN_DIR', getcwd());

include(PS_ADMIN_DIR.'/../config/config.inc.php');

/* Header can't be included, so cookie must be created here */
$cookie = new Cookie('psAdmin');
if (!$cookie->id_employee)
	Tools::redirectAdmin('login.php');
	
$functionArray = array(
	'pdf' => 'generateInvoicePDF',
	'id_order_slip' => 'generateOrderSlipPDF',
	'id_delivery' => 'generateDeliverySlipPDF',
	'invoices' => 'generateInvoicesPDF',
	'invoices2' => 'generateInvoicesPDF2',
	'slips' => 'generateOrderSlipsPDF',
	'deliveryslips' => 'generateDeliverySlipsPDF'
);

foreach ($functionArray as $var => $function)
	if (isset($_GET[$var]))
	{
		call_user_func($function);
		die;
	}

function generateInvoicePDF()
{
	if (!isset($_GET['id_order']))
		die (Tools::displayError('Missing order ID'));
	$order = new Order((int)($_GET['id_order']));
	if (!Validate::isLoadedObject($order))
		die(Tools::displayError('Cannot find order in database'));
	PDF::invoice($order);
}

function generateOrderSlipPDF()
{
	$orderSlip = new OrderSlip((int)($_GET['id_order_slip']));
	$order = new Order((int)($orderSlip->id_order));
	if (!Validate::isLoadedObject($order))
		die(Tools::displayError('Cannot find order in database'));
	$order->products = OrderSlip::getOrdersSlipProducts($orderSlip->id, $order);
	$tmp = NULL;
	PDF::invoice($order, 'D', false, $tmp, $orderSlip);
}

function generateDeliverySlipPDF()
{
	$order = Order::getByDelivery((int)($_GET['id_delivery']));
	if (!Validate::isLoadedObject($order))
		die(Tools::displayError('Cannot find order in database'));
	$tmp = NULL;
	PDF::invoice($order, 'D', false, $tmp, false, $order->delivery_number);
}

function generateInvoicesPDF()
{
	$orders = Order::getOrdersIdInvoiceByDate($_GET['date_from'], $_GET['date_to'], NULL, 'invoice');
	if (!is_array($orders))
		die (Tools::displayError('No invoices found'));
	PDF::multipleInvoices($orders);
}

function generateInvoicesPDF2()
{
	$allOrders = array();
	foreach (explode('-', Tools::getValue('id_order_state')) as $id_order_state)
		if (is_array($orders = Order::getOrderIdsByStatus((int)$id_order_state)))
			$allOrders = array_merge($allOrders, $orders);
	PDF::multipleInvoices($allOrders);
}

function generateOrderSlipsPDF()
{
	$orderSlips = OrderSlip::getSlipsIdByDate($_GET['date_from'], $_GET['date_to']);
	if (!count($orderSlips))
		die (Tools::displayError('No order slips found'));
	PDF::multipleOrderSlips($orderSlips);
}

function generateDeliverySlipsPDF()
{
	$slips = unserialize(urldecode($_GET['deliveryslips']));
	if (is_array($slips))
		PDF::multipleDelivery($slips);
}
