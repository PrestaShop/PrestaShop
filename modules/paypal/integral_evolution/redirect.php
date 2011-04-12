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

include(dirname(__FILE__).'/../../../config/config.inc.php');
include(dirname(__FILE__).'/../../../init.php');
include(dirname(__FILE__).'/../paypal.php');

$paypal = new Paypal();
$cart = new Cart((int)($cookie->id_cart));

// Billing address
$billingAddress = new Address((int)($cart->id_address_invoice));
$billingCountry = new Country((int)($billingAddress->id_country));
$billingState = NULL;
if ($billingAddress->id_state)
	$billingState = new State((int)($billingAddress->id_state));

// Shipping address
if ($cart->id_address_delivery == $cart->id_address_invoice)
{
	$shippingAddress = $billingAddress;
	$shippingCountry = $billingCountry;
	$shippingState = $billingState;
}
else
{
	$shippingAddress = new Address((int)($cart->id_address_delivery));
	$shippingCountry = new Country((int)($shippingAddress->id_country));
	$shippingState = NULL;
	if ($shippingAddress->id_state)
		$shippingState = new State((int)($shippingAddress->id_state));
}

$customer = new Customer((int)($cart->id_customer));
$business = Configuration::get('PAYPAL_BUSINESS');
$header = Configuration::get('PAYPAL_HEADER');
$currency_order = new Currency((int)($cart->id_currency));
$currency_module = $paypal->getCurrency((int)($cart->id_currency));

if (empty($business) OR !Validate::isEmail($business))
	die($paypal->getL('Paypal error: (invalid or undefined business account email)'));

if (!Validate::isLoadedObject($billingAddress) OR !Validate::isLoadedObject($shippingAddress) OR !Validate::isLoadedObject($customer) OR !Validate::isLoadedObject($currency_module))
	die($paypal->getL('Paypal error: (invalid address or customer)'));

// check currency of payment
if ($currency_order->id != $currency_module->id)
{
	$cookie->id_currency = $currency_module->id;
	$cart->id_currency = $currency_module->id;
	$cart->update();
}

$smarty->assign(array(
	'redirect_text' => $paypal->getL('Please wait, redirecting to Paypal... Thanks.'),
	'cancel_text' => $paypal->getL('Cancel'),
	'cart_text' => $paypal->getL('My cart'),
	'return_text' => $paypal->getL('Return to shop'),
	'paypal_url' => $paypal->getPaypalIntegralEvolutionUrl(),
	'billing_address' => $billingAddress,
	'billing_country' => $billingCountry,
	'billing_state' => $billingState,
	'shipping_address' => $shippingAddress,
	'shipping_country' => $shippingCountry,
	'shipping_state' => $shippingState,
	'amount' => (float)($cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING)),
	'customer' => $customer,
	'total' => (float)($cart->getOrderTotal(true, Cart::BOTH)),
	'shipping' => Tools::ps_round((float)($cart->getOrderShippingCost()) + (float)($cart->getOrderTotal(true, Cart::ONLY_WRAPPING)), 2),
	'discount' => $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS),
	'business' => $business,
	'currency_module' => $currency_module,
	'cart_id' => (int)($cart->id).'_'.pSQL($cart->secure_key),
	'products' => $cart->getProducts(),
	'paypal_id' => (int)($paypal->id),
	'header' => $header,
	'template' => 'Template'.Configuration::get('PAYPAL_TEMPLATE'),
	'url' => Tools::getShopDomain(true, true).__PS_BASE_URI__,
	'paymentaction' => (Configuration::get('PAYPAL_CAPTURE') ? 'authorization' : 'sale')
));


if (is_file(_PS_THEME_DIR_.'modules/paypal/integral_evolution/redirect.tpl'))
	$smarty->display(_PS_THEME_DIR_.'modules/'.$paypal->name.'/integral_evolution/redirect.tpl');
else
	$smarty->display(_PS_MODULE_DIR_.$paypal->name.'/integral_evolution/redirect.tpl');

