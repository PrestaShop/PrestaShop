<?php

$configPath = '../../../config/config.inc.php';
if (file_exists($configPath))
{
	include('../../../config/config.inc.php');
	if (!Tools::getValue('token') || Tools::getValue('token') != Configuration::get('EBAY_SECURITY_TOKEN'))
		die('ERROR :X');

	if (file_exists(dirname(__FILE__).'/../eBayRequest.php'))
	{
		include(dirname(__FILE__).'/../eBayRequest.php');

		$ebay = new eBayRequest();
		$ebay->session = Configuration::get('EBAY_API_SESSION');
		$ebay->username = Configuration::get('EBAY_API_USERNAME');
		$ebay->fetchToken();
		if (!empty($ebay->token))
		{
			Configuration::updateValue('EBAY_API_TOKEN', $ebay->token);
			echo 'OK';
		}
		else
			echo 'KO';
	}
	else
		echo 'ERROR02';
}
else
	echo 'ERROR01';

