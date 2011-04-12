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

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/bankwire.php');

$bankwire = new BankWire();

if ($cart->id_customer == 0 OR $cart->id_address_delivery == 0 OR $cart->id_address_invoice == 0 OR !$bankwire->active)
	Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

$customer = new Customer((int)$cart->id_customer);

if (!Validate::isLoadedObject($customer))
	Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

$currency = new Currency((int)(isset($_POST['currency_payement']) ? $_POST['currency_payement'] : $cookie->id_currency));
$total = (float)($cart->getOrderTotal(true, Cart::BOTH));
$mailVars = array(
	'{bankwire_owner}' => Configuration::get('BANK_WIRE_OWNER'),
	'{bankwire_details}' => nl2br(Configuration::get('BANK_WIRE_DETAILS')),
	'{bankwire_address}' => nl2br(Configuration::get('BANK_WIRE_ADDRESS'))
);

$bankwire->validateOrder($cart->id, _PS_OS_BANKWIRE_, $total, $bankwire->displayName, NULL, $mailVars, (int)$currency->id, false, $customer->secure_key);
$order = new Order($bankwire->currentOrder);
Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?id_cart='.$cart->id.'&id_module='.$bankwire->id.'&id_order='.$bankwire->currentOrder.'&key='.$customer->secure_key);
