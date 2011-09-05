<?php

$configPath = '../../../config/config.inc.php';
if (file_exists($configPath))
{
	include('../../../config/config.inc.php');
	include('../../../init.php');
	include('../../../modules/ebay/ebay.php');
	if (!Tools::getValue('token') || Tools::getValue('token') != Configuration::get('EBAY_SECURITY_TOKEN'))
		die('ERROR : Invalid Token');

	global $cookie;
	$cookie = new Cookie('psEbay', '', 3600);

	$ebay = new eBay();
	$ebay->ajaxProductSync();

	unset($cookie);
}
else
	echo 'ERROR';

