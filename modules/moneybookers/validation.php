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
include(dirname(__FILE__).'/moneybookers.php');

$moneyBookers = new MoneyBookers();

$errors = array();

/* Check for mandatory fields */
$requiredFields = array('status', 'md5sig', 'merchant_id', 'pay_to_email', 'mb_amount', 
'mb_transaction_id', 'currency', 'amount', 'transaction_id', 'pay_from_email', 'mb_currency');

foreach ($requiredFields AS $field)
	if (!isset($_POST[$field]))
		$errors[] = 'Missing field '.$field;

/* Check for MD5 signature */
$md5 = strtoupper(md5($_POST['merchant_id'].$_POST['transaction_id'].strtoupper(md5(Configuration::get('MB_SECRET_WORD'))).$_POST['mb_amount'].$_POST['mb_currency'].$_POST['status']));
if ($md5 != $_POST['md5sig'])
	$errors[] = 'Please double-check your Moneybookers account to make sure you have received the payment (Yours / MB) ['.$md5.'] ['.$_POST['md5sig'].']';

$message = '';
foreach ($_POST AS $key => $value)
	$message .= $key.': '.$value."\n";
if (sizeof($errors))
{
	$message .= sizeof($errors).' error(s):'."\n";
	
	/* Force status to 1 - ERROR ! */
	$_POST['status'] = 1;
}
foreach ($errors AS $error)
	$message .= $error."\n";
$message = nl2br(strip_tags($message));

$id_cart = (int)(substr($_POST['transaction_id'], 0, strpos($_POST['transaction_id'], '_')));
$secure_cart = explode('_', $_POST['transaction_id']);
$status = (int)($_POST['status']);
switch ($status)
{	
	/* Bankwire */
	case 0:
		$moneyBookers->validateOrder((int)($secure_cart[0]), _PS_OS_BANKWIRE_, (float)($_POST['amount']), $moneyBookers->displayName, $message, array(), NULL, false, $secure_cart[2]);
		break;

	/* Payment OK */
	case 2:
		$moneyBookers->validateOrder((int)($secure_cart[0]), _PS_OS_PAYMENT_, (float)($_POST['amount']), $moneyBookers->displayName, $message, array(), NULL, false, $secure_cart[2]);
		break;

	/* Unknown or error */
	default:
		$moneyBookers->validateOrder((int)($secure_cart[0]), _PS_OS_ERROR_, 0, $moneyBookers->displayName, $message, array(), NULL, false, $secure_cart[2]);
		break;
}


