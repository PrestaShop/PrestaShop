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
include(dirname(__FILE__).'/ogone.php');

$ogone = new Ogone();

/* First we need to check var presence */
$neededVars = array('orderID', 'amount', 'currency', 'PM', 'ACCEPTANCE', 'STATUS', 'CARDNO', 'PAYID', 'NCERROR', 'BRAND', 'SHASIGN');
$params = '<br /><br />'.$ogone->l('Received parameters:').'<br /><br />';

foreach ($neededVars AS $k)
	if (!isset($_GET[$k]))
		die($ogone->l('Missing parameter:').' '.$k);
	else
		$params .= $k.' : '.$_GET[$k].'<br />';

/* Then, load the customer cart and perform some checks */
$cart = new Cart((int)($_GET['orderID']));
if (Validate::isLoadedObject($cart))
{
	/* Fist, check for a valid SHA-1 signature */
	$ogoneParams = array();
	foreach ($_GET as $key => $value)
	if (strtoupper($key) != 'SHASIGN' AND $value != '')
		$ogoneParams[strtoupper($key)] = $value;

	ksort($ogoneParams);
	$shasign = '';
	foreach ($ogoneParams as $key => $value)
		$shasign .= strtoupper($key).'='.$value.Configuration::get('OGONE_SHA_OUT');
	$sha1 = strtoupper(sha1($shasign));	

	if ($sha1 == $_GET['SHASIGN'])
	{
		switch ($_GET['STATUS'])
		{
			case 1:
				/* Real error or payment canceled */
				$ogone->validate((int)$_GET['orderID'], _PS_OS_ERROR_, 0, $_GET['NCERROR'].$params, $_GET['secure_key']);
				break;
			case 2:
				/* Real error - authorization refused */
				$ogone->validate((int)$_GET['orderID'], _PS_OS_ERROR_, 0, $ogone->l('Error (auth. refused)').'<br />'.$_GET['NCERROR'].$params, $_GET['secure_key']);
				break;
			case 5:
			case 9:
				/* Payment OK */
				$ogone->validate((int)$_GET['orderID'], _PS_OS_PAYMENT_, (float)($_GET['amount']), $ogone->l('Payment authorized / OK').$params, $_GET['secure_key']);
				break;
			case 6:
			case 7:
			case 8:
				// Payment canceled later
				if ($id_order = (int)(Order::getOrderByCartId((int)($_GET['orderID']))))
				{
					// Update the amount really paid
					$order = new Order($id_order);
					$order->total_paid_real = 0;
					$order->update();
					
					// Send a new message and change the state
					$history = new OrderHistory();
					$history->id_order = $id_order;
					$history->changeIdOrderState(_PS_OS_ERROR_, $id_order);
					$history->addWithemail(true, array());
				}
				break;
			default:
				$ogone->validate((int)$_GET['orderID'], _PS_OS_ERROR_, (float)($_GET['amount']), $ogone->l('Unknown status:').' '.$_GET['STATUS'].$params, $_GET['secure_key']);
		}
		exit;
	}
	else
	{
		$message = $ogone->l('Invalid SHA-1 signature').'<br />'.$ogone->l('SHA-1 given:').' '.$_GET['SHASIGN'].'<br />'.$ogone->l('SHA-1 calculated:').' '.$sha1.'<br />'.$ogone->l('Plain key:').' '.$shasign;
		$ogone->validate((int)$_GET['orderID'], _PS_OS_ERROR_, 0, $message.'<br />'.$params, $_GET['secure_key']);
	}
}
	
