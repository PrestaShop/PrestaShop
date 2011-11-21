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
*  @version  Release: $Revision: 7310 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(dirname(__FILE__).'/../../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../../init.php');

include_once(_PS_MODULE_DIR_.'paypal/paypal.php');
include_once(_PS_MODULE_DIR_.'paypal/express/paypalexpress.php');

$paypal = new Paypal();

if (!$paypal->active)
	exit;

$ppExpress = new PaypalExpress();
$errors = array();
// #####
// Functions

function getAuthorization()
{
	global $ppExpress;

	$result = $ppExpress->getAuthorisation();
	$logs = $ppExpress->getLogs();
	if (is_array($result) AND sizeof($result))
	{
		if (strtoupper($result['ACK']) == 'SUCCESS')
		{
			if (isset($result['TOKEN']))
			{
				Context::getContext()->cookie->paypal_token = strval($result['TOKEN']);
				Context::getContext()->cookie->paypal_token_date = time();
				header('Location: https://'.$ppExpress->getPayPalURL().'/webscr&cmd=_express-checkout&token='.urldecode(strval(Context::getContext()->cookie->paypal_token)));
				exit;
			}
			else
				$logs[] = '<b>'.$ppExpress->l('No token given by PayPal', 'submit').'</b>';
		}
		else
			$logs[] = '<b>'.$ppExpress->l('PayPal returned error', 'submit').'</b>';
	}
	$ppExpress->displayPayPalAPIError($ppExpress->l('Authorisation to PayPal failed', 'submit'), $logs);
}

function getInfos()
{
	global $ppExpress;

	$result = $ppExpress->getCustomerInfos();
	$logs = $ppExpress->getLogs();

	if (!is_array($result) OR !isset($result['ACK']) OR strtoupper($result['ACK']) != 'SUCCESS')
	{
		$logs[] = '<b>'.$ppExpress->l('Cannot retrieve PayPal account information', 'submit').'</b>';
		$ppExpress->displayPayPalAPIError($ppExpress->l('PayPal returned error', 'submit'), $logs);
	}
	elseif (!isset($result['TOKEN']) OR $result['TOKEN'] != Context::getContext()->cookie->paypal_token)
	{
		$logs[] = '<b>'.$ppExpress->l('Token given by PayPal is not the same as the cookie token', 'submit').'</b>';
		$ppExpress->displayPayPalAPIError($ppExpress->l('PayPal returned error', 'submit'), $logs);
	}
	return $result;
}

function displayProcess($payerID)
{
	Context::getContext()->cookie->paypal_token = strval(Context::getContext()->cookie->paypal_token);
	Context::getContext()->cookie->paypal_payer_id = $payerID;
	Tools::redirect('index.php?controller=order&step=1&back=paypal');
}

function displayConfirm()
{
	global $ppExpress, $payerID;

	if (!Context::getContext()->customer->isLogged(true))
		die('Not logged');
	if (!$payerID AND !$payerID = Tools::htmlentitiesUTF8(strval(Tools::getValue('payerID'))))
		die('No payer ID');

	// Display all and exit
	include(_PS_ROOT_DIR_.'/header.php');

	Context::getContext()->smarty->assign(array(
		'back' => 'paypal',
		'logo' => $ppExpress->getLogo(),
		'ppToken' => strval(Context::getContext()->cookie->paypal_token),
		'cust_currency' => Context::getContext()->cart->id_currency,
		'currencies' => $ppExpress->getCurrency((int)Context::getContext()->cart->id_currency),
		'total' => Context::getContext()->cart->getOrderTotal(true, PayPal::BOTH),
		'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'. $ppExpress->name.'/',
		'payerID' => $payerID,
		'mode' => 'express/'
	));

	echo $ppExpress->display('paypal.php', 'confirm.tpl');
	include(_PS_ROOT_DIR_.'/footer.php');
	die ;
}

function submitConfirm()
{
	global $ppExpress;

	if (!Context::getContext()->customer->isLogged(true))
		die('Not logged');
	elseif (!$currency = (int)(Tools::getValue('currency_payement')))
		die('No currency');
	elseif (!$payerID = Tools::htmlentitiesUTF8(strval(Tools::getValue('payerID'))))
		die('No payer ID');
	elseif (!Context::getContext()->cart->getOrderTotal(true, PayPal::BOTH))
		die('Empty cart');

	$ppExpress->makePayPalAPIValidation(Context::getContext()->cookie, Context::getContext()->cart, $currency, $payerID, 'express');
}

function submitAccount()
{
	global $errors;

	$context = Context::getContext();
	$email = Tools::getValue('email');
	if (empty($email) OR !Validate::isEmail($email))
		$errors[] = Tools::displayError('e-mail not valid');
	elseif (!Validate::isPasswd(Tools::getValue('passwd')))
		$errors[] = Tools::displayError('invalid password');
	elseif (Customer::customerExists($email))
		$errors[] = Tools::displayError('someone has already registered with this e-mail address');
	elseif (!@checkdate(Tools::getValue('months'), Tools::getValue('days'), Tools::getValue('years')) AND !(Tools::getValue('months') == '' AND Tools::getValue('days') == '' AND Tools::getValue('years') == ''))
		$errors[] = Tools::displayError('invalid birthday');
	else
	{
		$customer = new Customer();
		if (Tools::isSubmit('newsletter'))
		{
			$customer->ip_registration_newsletter = pSQL(Tools::getRemoteAddr());
			$customer->newsletter_date_add = pSQL(date('Y-m-d h:i:s'));
		}
		$customer->birthday = (empty($_POST['years']) ? '' : (int)($_POST['years']).'-'.(int)($_POST['months']).'-'.(int)($_POST['days']));
		/* Customer and address, same fields, caching data */
		$errors = $customer->validateControler();
		$address = new Address();
		$address->id_customer = 1;
		$errors = array_unique(array_merge($errors, $address->validateControler()));
		if (!sizeof($errors))
		{
			$customer->active = 1;
			if (!$customer->add())
				$errors[] = Tools::displayError('an error occurred while creating your account');
			else
			{
				$address->id_customer = (int)($customer->id);
				if (!$address->add())
					$errors[] = Tools::displayError('an error occurred while creating your address');
				else
				{
					if (Mail::Send($context->language->id, 'account', Mail::l('Welcome!'),
					array('{firstname}' => $customer->firstname, '{lastname}' => $customer->lastname, '{email}' => $customer->email, '{passwd}' => Tools::getValue('passwd')), $customer->email, $customer->firstname.' '.$customer->lastname))
						$context->smarty->assign('confirmation', 1);
					$context->cookie->id_customer = (int)($customer->id);
					$context->cookie->customer_lastname = $customer->lastname;
					$context->cookie->customer_firstname = $customer->firstname;
					$context->cookie->passwd = $customer->passwd;
					$context->cookie->logged = 1;
					$customer->logged = 1;
					$context->cookie->email = $customer->email;
					Hook::exec('createAccount', array(
						'_POST' => $_POST,
						'newCustomer' => $customer
					));

					// Next !
					$payerID = strval(Tools::getValue('payerID'));
					displayProcess($payerID);
				}
			}
		}
	}
}

function submitLogin()
{
	global $errors;

	$passwd = trim(Tools::getValue('passwd'));
	$email = trim(Tools::getValue('email'));
	if (empty($email))
		$errors[] = Tools::displayError('e-mail address is required');
	elseif (empty($email) OR !Validate::isEmail($email))
		$errors[] = Tools::displayError('invalid e-mail address');
	elseif (empty($passwd))
		$errors[] = Tools::displayError('password is required');
	elseif (Tools::strlen($passwd) > 32)
		$errors[] = Tools::displayError('password is too long');
	elseif (!Validate::isPasswd($passwd))
		$errors[] = Tools::displayError('invalid password');
	else
	{
		$customer = new Customer();
		$authentication = $customer->getByemail(trim($email), trim($passwd));
		/* Handle brute force attacks */
		sleep(1);
		if (!$authentication OR !$customer->id)
			$errors[] = Tools::displayError('authentication failed');
		else
		{
			$context->cookie->id_customer = (int)($customer->id);
			$context->cookie->customer_lastname = $customer->lastname;
			$context->cookie->customer_firstname = $customer->firstname;
			$context->cookie->logged = 1;
			$customer->logged = 1;
			$context->cookie->passwd = $customer->passwd;
			$context->cookie->email = $customer->email;
			if (Configuration::get('PS_CART_FOLLOWING') AND (empty($context->cookie->id_cart) OR Cart::getNbProducts($context->cookie->id_cart) == 0))
				$context->cookie->id_cart = Cart::lastNoneOrderedCart($customer->id);
			Hook::exec('authentication');

			// Next !
			$payerID = strval(Tools::getValue('payerID'));
			displayProcess($payerID);
		}
	}
}

function displayLogin()
{
	global $result, $email, $payerID, $errors, $ppExpress;

	// Customer exists, login form

	// If customer already logged, check if same mail than PayPal, and go through, or unlog
	if (Context::getContext()->customer->isLogged(true) AND isset($result['EMAIL']) AND Context::getContext()->customer->email == $result['EMAIL'])
		displayProcess($payerID);
	elseif (Context::getContext()->customer->isLogged(true))
	{
		Context::getContext()->cookie->makeNewLog();
		Context::getContext()->customer = new Customer();
	}
	// Smarty assigns
	Context::getContext()->smarty->assign(array(
		'email' => $email,
		'ppToken' => strval(Context::getContext()->cookie->paypal_token),
		'errors'=> $errors,
		'payerID' => $payerID
	));

	// Display all and exit
	include(_PS_ROOT_DIR_.'/header.php');
	echo $ppExpress->display('paypal.php', 'express/login.tpl');
	include(_PS_ROOT_DIR_.'/footer.php');
	die ;
}

function displayAccount()
{
	global $result, $email, $payerID, $errors, $ppExpress;

	// Customer does not exists, signup form

	// If customer already logged, unlog him
	if (Context::getContext()->customer->isLogged(true))
	{
		Context::getContext()->cookie->makeNewLog();
		Context::getContext()->customer = new Customer();
	}
	// Generate years, months and days
	if (isset($_POST['years']) AND is_numeric($_POST['years']))
		$selectedYears = (int)($_POST['years']);
	$years = Tools::dateYears();
	if (isset($_POST['months']) AND is_numeric($_POST['months']))
		$selectedMonths = (int)($_POST['months']);
	$months = Tools::dateMonths();
	if (isset($_POST['days']) AND is_numeric($_POST['days']))
		$selectedDays = (int)($_POST['days']);
	$days = Tools::dateDays();

	// Select the most appropriate country
	if (Tools::getValue('id_country'))
		$selectedCountry = (int)(Tools::getValue('id_country'));
	else if ((int)$result['COUNTRYCODE'])
	{
		$selectedCountry = Country::getByIso(strval($result['COUNTRYCODE']));
	}
	$countries = Country::getCountries((int)(Context::getContext()->cookie->id_lang), true);

	// Smarty assigns
	Context::getContext()->smarty->assign(array(
		'years' => $years,
		'sl_year' => (isset($selectedYears) ? $selectedYears : 0),
		'months' => $months,
		'sl_month' => (isset($selectedMonths) ? $selectedMonths : 0),
		'days' => $days,
		'sl_day' => (isset($selectedDays) ? $selectedDays : 0),
		'countries' => $countries,
		'sl_country' => (isset($selectedCountry) ? $selectedCountry : 0),
		'email' => $email,
		'firstname' => (Tools::getValue('customer_firstname') ? Tools::htmlentitiesUTF8(strval(Tools::getValue('customer_firstname'))) : $result['FIRSTNAME']),
		'lastname' => (Tools::getValue('customer_lastname') ? Tools::htmlentitiesUTF8(strval(Tools::getValue('customer_lastname'))) : $result['LASTNAME']),
		'street' => (Tools::getValue('address1') ? Tools::htmlentitiesUTF8(strval(Tools::getValue('address1'))) : (isset($result['SHIPTOSTREET']) ? $result['SHIPTOSTREET'] : '')),
		'city' => (Tools::getValue('city') ? Tools::htmlentitiesUTF8(strval(Tools::getValue('city'))) : (isset($result['SHIPTOCITY']) ? $result['SHIPTOCITY'] : '')),
		'zip' => (Tools::getValue('postcode') ? Tools::htmlentitiesUTF8(strval(Tools::getValue('postcode'))) : (isset($result['SHIPTOZIP']) ? $result['SHIPTOZIP'] : '')),
		'payerID' => $payerID,
		'ppToken' => strval(Context::getContext()->cookie->paypal_token),
		'errors'=> $errors,
		'genders' => Gender::getGenders(),
	));

	// Display all and exit
	include(_PS_ROOT_DIR_.'/header.php');
	echo $ppExpress->display('paypal.php', 'express/authentication.tpl');
	include(_PS_ROOT_DIR_.'/footer.php');
	die ;
}

// #####
// Process !!
/*if (!Context::getContext()->customer->isLogged(true))
{
	displayAccount();
	die('Not logged');
}*/

if (!Context::getContext()->cart->getOrderTotal(true, PayPal::BOTH))
	die('Empty cart');

// No token, we need to get one by making PayPal Authorisation
if (!isset(Context::getContext()->cookie->paypal_token) OR !Context::getContext()->cookie->paypal_token)
	getAuthorization();
else
{
	// We have token, we need to confirm user informations (login or signup)
	if ((int)(Tools::getValue('confirm')))
		displayConfirm();
	elseif (Tools::isSubmit('submitAccount'))
		submitAccount();
	elseif (Tools::isSubmit('submitLogin'))
		submitLogin();
	elseif (Tools::isSubmit('submitPayment'))
		submitConfirm();

	// We got an error or we still not submit form
	if ((!Tools::isSubmit('submitAccount') AND !Tools::isSubmit('submitLogin')) OR sizeof($errors))
	{
		if (isset(Context::getContext()->cookie->paypal_token) AND isset(Context::getContext()->cookie->paypal_token_date) AND (time() - 10800 > Context::getContext()->cookie->paypal_token_date))
		{
			// Token expired, unset it
			unset(Context::getContext()->cookie->paypal_token);
			Tools::redirect('modules/paypal/express/submit.php');
		}
		//  We didn't submit form, getting PayPal informations
		if (!Tools::isSubmit('submitAccount') AND !Tools::isSubmit('submitLogin'))
			$result = getInfos();

		if (Tools::getValue('email') AND Tools::getValue('payerID'))
		{
			// Form was submitted (errors)
			$email = Tools::htmlentitiesUTF8(strval(Tools::getValue('email')));
			$payerID = Tools::htmlentitiesUTF8(strval(Tools::getValue('payerID')));
		}
		elseif (isset($result['EMAIL']) AND isset($result['PAYERID']))
		{
			// Displaying form for the first time
			$email = $result['EMAIL'];
			$payerID = $result['PAYERID'];
		}
		else
		{
			// Error in token, we need to make authorization again
			unset(Context::getContext()->cookie->paypal_token);
			Tools::redirect('modules/paypal/express/submit.php');
		}
		if (Customer::customerExists($email) OR Tools::isSubmit('submitLogin'))
			displayLogin();
		displayAccount();
	}
}
