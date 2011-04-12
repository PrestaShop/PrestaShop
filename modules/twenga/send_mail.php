<?php

$configPath = '../../config/config.inc.php';

if (file_exists($configPath))
{
	include('../../config/config.inc.php');
	include(dirname(__FILE__).'/twenga.php');

	$controller = new FrontController();
	$controller->init();

	$country = Twenga::getCurrentCountryName();

	$to = 'rts_support@twenga.com';
	$subject = 'Site prestashop '.$country.' ayant supprimé le module';
	
	$template = 'mail';

	$template_vars = array(
		'{shop_url}' 		=> Tools::getShopDomain(true),
		'{trader_email}' 	=> Configuration::get('PS_SHOP_EMAIL'),
		'{shop_country}'	=> $country);

	 Mail::Send($_POST['id_lang'], $template, $subject, $template_vars,
		 $to, NULL, NULL, NULL, NULL, NULL, dirname(__FILE__).'/mails/');
}

?>
