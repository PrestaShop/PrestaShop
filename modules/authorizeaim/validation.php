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
include(dirname(__FILE__). '/../../config/config.inc.php');
include(dirname(__FILE__). '/../../init.php');
include(dirname(__FILE__). '/authorizeaim.php');

/* Transform the POST from the template to a GET for the CURL */
if (isset($_POST['x_exp_date']) && isset($_POST['x_exp_date_m']) && isset($_POST['x_exp_date_y']) && isset($_POST['x_exp_date_y']) && isset($_POST['name']))
{
	$_POST['x_exp_date'] = $_POST['x_exp_date_m'].$_POST['x_exp_date_y'];
	unset($_POST['x_exp_date_m']);
	unset($_POST['x_exp_date_y']);
	unset($_POST['name']);
}
$postString = '';
foreach ($_POST AS $key => $value)
	if ($key != "x_exp_date_m" OR $key != "x_exp_date_m")
		$postString .= $key.'='.urlencode($value).'&';
$postString .= 'x_exp_date='.str_pad($_POST["x_exp_date_m"], 2, "0",STR_PAD_LEFT).$_POST["x_exp_date_y"];

/* Do the CURL request ro Authorize.net */
$request = curl_init(
Tools::getValue('x_test_request') ? 'https://test.authorize.net/gateway/transact.dll' : 'https://secure.authorize.net/gateway/transact.dll');
curl_setopt($request, CURLOPT_HEADER, 0);
curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($request, CURLOPT_POSTFIELDS, $postString);
curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
$postResponse = curl_exec($request); 
curl_close($request);

$response = explode('|', $postResponse);
if (!isset($response[7]) OR !isset($response[3]) OR !isset($response[9]))
{
		Logger::addLog('Authorize.net returned a malformed response for cart '.$response[7], 4);
		die('Authorize.net returned a malformed response, aborted.');
}

if ($response[0] == 3)
	Tools::redirect('order.php?step=3&aimerror=1');
else 
{
	/* Does the cart exist and is valid? */
	$cart = new Cart((int)$response[7]);
	if (!Validate::isLoadedObject($cart))
	{
		Logger::addLog('Cart loading failed for cart '.$response[7], 4);
		exit;
	}

	$customer = new Customer((int)$cart->id_customer);

	/* Loading the object */	
	$authorizeaim = new authorizeaim();
	$message = $response[3];

	if ($response[0] == 1)
		$authorizeaim->validateOrder((int)$cart->id, _PS_OS_PAYMENT_, (float)$response[9], $authorizeaim->displayName, $message);
	else
		$authorizeaim->validateOrder((int)$cart->id, _PS_OS_ERROR_, (float)$response[9], $authorizeaim->displayName, $message);

	Tools::redirect('order-confirmation.php?id_module='.(int)$authorizeaim->id.'&id_cart='.(int)$cart->id.'&key='.$customer->secure_key);
}

