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

$useSSL = true;

include_once(dirname(__FILE__).'/../../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../../init.php');

include_once(_PS_MODULE_DIR_.'paypal/paypal.php');
include_once(_PS_MODULE_DIR_.'paypal/payment/paypalpayment.php');

$ppPayment = new PaypalPayment();
$errors = array();

// #####
// Functions

function getAuthorization()
{
	global $ppPayment, $cookie, $cart;

	$result = $ppPayment->getAuthorisation();
	$logs = $ppPayment->getLogs();
	if (is_array($result) AND sizeof($result))
	{
		if (strtoupper($result['ACK']) == 'SUCCESS')
		{
			if (isset($result['TOKEN']))
			{
				$cookie->paypal_token = strval($result['TOKEN']);
				$cookie->paypal_token_date = time();
				header('Location: https://'.$ppPayment->getPayPalURL().'/webscr&cmd=_express-checkout&token='.urldecode(strval($cookie->paypal_token)).'&useraction=commit');
        exit;
			}
			else
				$logs[] = '<b>'.$ppPayment->l('No token given by PayPal', 'submit').'</b>';
		} else
			$logs[] = '<b>'.$ppPayment->l('PayPal returned error', 'submit').'</b>';
	}
	$ppPayment->displayPayPalAPIError($ppPayment->l('Authorisation to PayPal failed', 'submit'), $logs);
}

function displayConfirm()
{
	global $cookie, $smarty, $ppPayment, $cart;

	if (!$cookie->isLogged(true))
		die('Not logged');
	unset($cookie->paypal_token);

	if ($cart->id_currency != $ppPayment->getCurrency((int)$cart->id_currency)->id)
	{
		$cart->id_currency = (int)($ppPayment->getCurrency((int)$cart->id_currency)->id);
		$cookie->id_currency = (int)($cart->id_currency);
		$cart->update();
		Tools::redirect('modules/'.$ppPayment->name.'/payment/submit.php');
	}

	// Display all and exit
	include(_PS_ROOT_DIR_.'/header.php');

	$smarty->assign(array(
		'logo' => $ppPayment->getLogo(),
		'cust_currency' => $cart->id_currency,
		'currency' => $ppPayment->getCurrency((int)$cart->id_currency),
		'total' => $cart->getOrderTotal(true, Cart::BOTH),
		'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'. $ppPayment->name.'/',
		'mode' => 'payment/'
	));

	echo $ppPayment->display('paypal.php', 'confirm.tpl');
	include(_PS_ROOT_DIR_.'/footer.php');
	die ;
}

function submitConfirm()
{
	global $cookie, $smarty, $ppPayment, $cart;

	if (!$cookie->isLogged(true))
		die('Not logged');
	elseif (!$id_currency = (int)(Tools::getValue('currency_payement')))
		die('No currency');
	elseif (!$cart->getOrderTotal(true, Cart::BOTH))
		die('Empty cart');
	$currency = new Currency((int)($id_currency));
	if (!Validate::isLoadedObject($currency))
		die('Invalid currency');
	$cookie->id_currency = (int)($id_currency);
	getAuthorization();
}

function validOrder()
{
	global $cookie, $cart, $ppPayment;
	if (!$cookie->isLogged(true))
		die('Not logged');
	elseif (!$cart->getOrderTotal(true, Cart::BOTH))
		die('Empty cart');
	if (!$token = Tools::htmlentitiesUTF8(strval(Tools::getValue('token'))))
		die('Invalid token');
	if ($token != strval($cookie->paypal_token))
		die('Invalid cookie token');
	if (!$payerID = Tools::htmlentitiesUTF8(strval(Tools::getValue('PayerID'))))
		die('Invalid payerID');
	$ppPayment->makePayPalAPIValidation($cookie, $cart, $cart->id_currency, $payerID, 'payment');
}

// #####
// Process !!

// No submit, confirmation page
if (!Tools::isSubmit('submitPayment') AND !Tools::getValue('fromPayPal'))
	displayConfirm();
else
{
	if (!isset($cookie->paypal_token) OR !$cookie->paypal_token)
		submitConfirm();
	validOrder();
}
