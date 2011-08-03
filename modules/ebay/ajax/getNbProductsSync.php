<?php

$configPath = '../../../config/config.inc.php';
if (file_exists($configPath))
{
	include('../../../config/config.inc.php');
	if (!Tools::getValue('token') || Tools::getValue('token') != Configuration::get('EBAY_SECURITY_TOKEN'))
		die('ERROR :X');

	Db::getInstance()->autoExecute(_DB_PREFIX_.'ebay_category_configuration', array('sync' => (int)($_GET['action'])), 'UPDATE', '`id_category` = '.(int)$_GET['id_category']);

	$nbProducts = Db::getInstance()->getValue('
	SELECT COUNT(`id_product`) as nb
	FROM `'._DB_PREFIX_.'product`
	WHERE `quantity` > 0 AND `active` = 1
	AND `id_category_default` IN (SELECT `id_category` FROM `'._DB_PREFIX_.'ebay_category_configuration` WHERE `id_ebay_category` > 0 AND `sync` = 1)');

	echo $nbProducts;
}
else
	echo 'ERROR';

