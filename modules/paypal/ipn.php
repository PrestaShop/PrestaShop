<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');

include_once(_PS_MODULE_DIR_.'paypal/paypal.php');
$pp = new Paypal();

if (!$transaction_id = Tools::getValue('txn_id'))
	die('No transaction id');
if (!$id_order = $pp->getOrder($transaction_id))
	die('No order');

$order = new Order((int)($id_order));
if (!Validate::isLoadedObject($order) OR !$order->id)
	die('Invalid order');
if (!$amount = (float)(Tools::getValue('mc_gross')) OR $amount != $order->total_paid)
	die('Incorrect amount');

if (!$status = strval(Tools::getValue('payment_status')))
	die('Incorrect order status');

// Getting params
$params = 'cmd=_notify-validate';
foreach ($_POST AS $key => $value)
	$params .= '&'.$key.'='.urlencode(stripslashes($value));

// Checking params by asking PayPal
include(_PS_MODULE_DIR_.'paypal/api/paypallib.php');
$ppAPI = new PaypalLib();
$result = $ppAPI->makeSimpleCall($pp->getAPIURL(), $pp->getAPIScript(), $params);
if (!$result OR (Tools::strlen($result) < 8) OR (!$statut = substr($result, -8)) OR $statut != 'VERIFIED')
	die('Incorrect PayPal verified');

// Getting order status
switch ($status)
{
	case 'Completed':
		$id_order_state = _PS_OS_PAYMENT_;
		break;
	case 'Pending':
		$id_order_state = _PS_OS_PAYPAL_;
		break;
	default:
		$id_order_state = _PS_OS_ERROR_;
}

if ($order->getCurrentState() == $id_order_state)
	die('Same status');

// Set order state in order history
$history = new OrderHistory();
$history->id_order = (int)($order->id);
$history->changeIdOrderState((int)($id_order_state), (int)($order->id));
$history->addWithemail(true, $extraVars);
