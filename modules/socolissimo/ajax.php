<?php

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('../../modules/socolissimo/socolissimo.php');

if (!Tools::getValue('ajax'))
	die('');
	
$socolissimo = new Socolissimo();
	
global $cookie;

$result = $socolissimo->getDeliveryInfos($cookie->id_cart,$cookie->id_customer);
if (!$result)
	die('{"result" : false}');
else
	die('{"result" : true}');
?>