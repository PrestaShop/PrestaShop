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
include(dirname(__FILE__).'/paypal.php');

$errors = '';
$result = false;
$paypal = new Paypal();

// Fill params
$params = 'cmd=_notify-validate';
foreach ($_POST AS $key => $value)
	$params .= '&'.$key.'='.urlencode(stripslashes($value));

// PayPal Server
$paypalServer = 'www.'.(Configuration::get('PAYPAL_SANDBOX') ? 'sandbox.' : '').'paypal.com';

// Getting PayPal data...
if (function_exists('curl_exec'))
{
	// curl ready
	$ch = curl_init('https://' . $paypalServer . '/cgi-bin/webscr');
    
	// If the above fails, then try the url with a trailing slash (fixes problems on some servers)
 	if (!$ch)
		$ch = curl_init('https://' . $paypalServer . '/cgi-bin/webscr/');
	
	if (!$ch)
		$errors .= $paypal->getL('connect').' '.$paypal->getL('curlmethodfailed');
	else
	{
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$result = curl_exec($ch);

		if (strtoupper($result) != 'VERIFIED')
			$errors .= $paypal->getL('curlmethod').$result.' cURL error:'.curl_error($ch);
		curl_close($ch);
	}
}
elseif (($fp = @fsockopen('ssl://' . $paypalServer, 443, $errno, $errstr, 30)) || ($fp = @fsockopen($paypalServer, 80, $errno, $errstr, 30)))
{
	// fsockopen ready
	$header = 'POST /cgi-bin/webscr HTTP/1.0'."\r\n" .
          'Host: '.$paypalServer."\r\n".
          'Content-Type: application/x-www-form-urlencoded'."\r\n".
          'Content-Length: '.Tools::strlen($params)."\r\n".
          'Connection: close'."\r\n\r\n";
	fputs($fp, $header.$params);
 	
 	$read = '';
 	while (!feof($fp))
	{
		$reading = trim(fgets($fp, 1024));
		$read .= $reading;
		if (strtoupper($reading) == 'VERIFIED' OR strtoupper($reading) == 'INVALID')
		{
		 	$result = $reading;
			break;
		}
 	}
	if (strtoupper($result) != 'VERIFIED')
		$errors .= $paypal->getL('socketmethod').$result;
	fclose($fp);
}
else
	$errors = $paypal->getL('connect').$paypal->getL('nomethod');

if (isset($_POST['custom']))
	$cart_secure = explode('_', $_POST['custom']);
else
	$cart_secure = array();

// Printing errors...
if (strtoupper($result) == 'VERIFIED')
{
	if (!isset($_POST['mc_gross']))
		$errors .= $paypal->getL('mc_gross').'<br />';
	if (!isset($_POST['payment_status']))
		$errors .= $paypal->getL('payment_status').'<br />';
	elseif (strtoupper($_POST['payment_status']) != 'COMPLETED')
		$errors .= $paypal->getL('payment').$_POST['payment_status'].'<br />';
	if (!isset($_POST['custom']))
		$errors .= $paypal->getL('custom').'<br />';
	if (!isset($_POST['txn_id']))
		$errors .= $paypal->getL('txn_id').'<br />';
	if (!isset($_POST['mc_currency']))
		$errors .= $paypal->getL('mc_currency').'<br />';
	if (empty($errors))
	{
		$cart = new Cart((int)($cart_secure[0]));
		if (!$cart->id)
			$errors = $paypal->getL('cart').'<br />';
		elseif (Order::getOrderByCartId((int)($cart_secure[0])))
			$errors = $paypal->getL('order').'<br />';
		else
			$paypal->validateOrder((int)$cart_secure[0], _PS_OS_PAYMENT_, (float)($_POST['mc_gross']), $paypal->displayName, $paypal->getL('transaction').$_POST['txn_id'], array('transaction_id' => $_POST['txn_id'], 'payment_status' => $_POST['payment_status']), NULL, false, $cart_secure[1]);
	}
}
else
	$errors .= $paypal->getL('verified');

if (!empty($errors) AND isset($_POST['custom']))
{
	if (strtoupper($_POST['payment_status']) == 'PENDING')
		$paypal->validateOrder((int)$cart_secure[0], _PS_OS_PAYPAL_, (float)($_POST['mc_gross']), $paypal->displayName, $paypal->getL('transaction').$_POST['txn_id'].'<br />'.$errors, array('transaction_id' => $_POST['txn_id'], 'payment_status' => $_POST['payment_status']), NULL, false, $cart_secure[1]);
	else
		$paypal->validateOrder((int)$cart_secure[0], _PS_OS_ERROR_, 0, $paypal->displayName, $errors.'<br />', array(), NULL, false, $cart_secure[1]);
}
