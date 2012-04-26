<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 14011 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include(dirname(__FILE__). '/../../config/config.inc.php');
/* will include backward file */
include(dirname(__FILE__). '/authorizeaim.php');

$authorizeaim = new authorizeAIM();

/* SSL Tricks to bypass the redirect for the FrontController in 1.5 + */
Configuration::updateValue('PS_SSL_ENABLED', 0);
include(dirname(__FILE__). '/../../init.php');
Configuration::updateValue('PS_SSL_ENABLED', 1);

/* Transform the POST from the template to a GET for the CURL */
if (isset($_POST['x_exp_date_m']) && isset($_POST['x_exp_date_y']))
{
	$_POST['x_exp_date'] = $_POST['x_exp_date_m'].$_POST['x_exp_date_y'];
	unset($_POST['x_exp_date_m']);
	unset($_POST['x_exp_date_y']);
}
$postString = '';
foreach ($_POST as $key => $value)
	$postString .= $key.'='.urlencode($value).'&';

$postString = trim($postString, '&');

$url = 'https://secure.authorize.net/gateway/transact.dll';
if (Configuration::get('AUTHORIZE_AIM_DEMO'))
{
	$postString .= '&x_test_request=TRUE';
	$url = 'https://test.authorize.net/gateway/transact.dll';
}

/* Do the CURL request ro Authorize.net */
$request = curl_init($url);
curl_setopt($request, CURLOPT_HEADER, 0);
curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($request, CURLOPT_POSTFIELDS, $postString);
curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
$postResponse = curl_exec($request);
curl_close($request);

$response = explode('|', $postResponse);
if (!isset($response[7]) || !isset($response[3]) || !isset($response[9]))
{
	$msg = 'Authorize.net returned a malformed response for cart';
	if (isset($response[7]))
		$msg .= ' '.(int)$response[7];
	Logger::addLog($msg, 4);
	die('Authorize.net returned a malformed response, aborted.');
}

/* Does the cart exist and is valid? */
$cart = new Cart((int)$response[7]);
if (!Validate::isLoadedObject($cart))
{
	Logger::addLog('Cart loading failed for cart '.(int)$response[7], 4);
	die('An unrecoverable error occured with the cart '.(int)$repsonse[7]);
}
$customer = new Customer((int)$cart->id_customer);
$message = $response[3];
$payment_method = 'Authorize.net AIM';

switch ($response[0]) // Response code
{
	case 1: // Payment accepted
		$authorizeaim->setTransactionDetail($response);
		$authorizeaim->validateOrder((int)$cart->id,
			Configuration::get('PS_OS_PAYMENT'), (float)$response[9],
			$payment_method, $message, NULL, NULL, false, $customer->secure_key);
		break ;

	case 4: // Hold for review
		$authorizeaim->validateOrder((int)$cart->id,
			Configuration::get('AUTHORIZE_AIM_HOLD_REVIEW_OS'), (float)$response[9],
			$authorizeaim->displayName, $response[3]);
		break ;

	default:
		$checkout_type = Configuration::get('PS_ORDER_PROCESS_TYPE') ?
			'order-opc' : 'order';
		$url = _PS_VERSION_ >= '1.5' ?
			'index.php?controller='.$checkout_type.'&' : $checkout_type.'.php?';
		$url .= 'step=3&cgv=1&aimerror=1';

		if (!isset($_SERVER['HTTP_REFERER']) ||
			strstr($_SERVER['HTTP_REFERER'], 'order'))
			Tools::redirect($url);
		elseif (strstr($_SERVER['HTTP_REFERER'], '?'))
			Tools::redirect($_SERVER['HTTP_REFERER'].'&aimerror=1', '');
		else
			Tools::redirect($_SERVER['HTTP_REFERER'].'?aimerror=1', '');

		exit;

		/// @todo check the reason code and determine weither we should retry are
		///				mark the order as failed.
		$authorizeaim->validateOrder((int)$cart->id,
			Configuration::get('PS_OS_ERROR'), (float)$response[9],
			$payment_method, $message, NULL, NULL, false, $customer->secure_key);
}

$url = 'index.php?controller=order-confirmation&';
if (_PS_VERSION_ < '1.5')
	$url = 'order-confirmation.php?';

Tools::redirect($url.'id_module='.(int)$authorizeaim->id.'&id_cart='.
	(int)$cart->id.'&key='.$customer->secure_key);
