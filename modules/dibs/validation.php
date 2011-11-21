<?php

include(dirname(__FILE__). '/../../config/config.inc.php');
include(dirname(__FILE__).'/dibs.php');

$posted_values = array();
$errors = array();
$obj_dibs = new dibs();
$required_fields = array('orderid', 'paytype', 'transact', 'merchant', 'uniqueoid', 'amount', 'currency', 'authkey');
$valid_order = true;
if (count($_POST))
{
	$posted_values = $_POST;

	foreach ($required_fields AS $field)
		if (!isset($posted_values[$field]))
			$errors[] = 'Missing field '.$field;

	$secure_cart = explode('_', $posted_values['uniqueoid']);
	$arr_order_id = explode('_',$posted_values['orderid']);
	$posted_values['orderid'] = $arr_order_id[0];

	if ((string)$posted_values['merchant'] !== (string)dibs::$ID_MERCHANT)
		$errors[] = Tools::displayError('You did not use the correct merchant ID.');

	$md5_key = md5(dibs::$MORE_SETTINGS['k2'].md5(dibs::$MORE_SETTINGS['k1'].'transact='.$posted_values['transact'].'&amount='.$posted_values['amount'].'&currency='.$posted_values['currency']));
	if((string)$posted_values['authkey'] !== $md5_key)
		$errors[] = Tools::displayError('Your are not allowed to validate the command for security reasons.');

	$message = '';
	foreach ($posted_values AS $key => $value)
		if (is_string($value) AND in_array($key, $required_fields) AND $key !== 'HTTP_COOKIE')
			$message .= $key.': '.$value."\n";
	if (sizeof($errors))
	{
		$message .= sizeof($errors).' error(s):'."\n";
		$valid_order = false;
	}

	foreach ($errors AS $error)
		$message .= $error."\n";
	$message = nl2br(strip_tags($message));
	if ($valid_order === true)
	{
    $obj_dibs->setTransactionDetail($posted_values);
		$obj_dibs->validateOrder((int)$posted_values['orderid'], Configuration::get('PS_OS_PAYMENT'),
			(float)((int)$posted_values['amount'] / 100), $obj_dibs->displayName, $message, array(), NULL, false, $secure_cart[2]);
	}
	else if ($valid_order === false)
		$obj_dibs->validateOrder((int)$posted_values['orderid'], Configuration::get('PS_OS_ERROR'), 0, $obj_dibs->displayName, $message, array(), NULL, false, $secure_cart[2]);
}
