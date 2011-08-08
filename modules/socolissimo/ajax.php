<?php

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('../../modules/socolissimo/socolissimo.php');

if (!Tools::getValue('ajax'))
	die('');
	
$socolissimo = new Socolissimo();
	
$result = $socolissimo->getDeliveryInfos(Context::getContext()->cart->id, Context::getContext()->customer->id);
if (!$result)
	die('{"result" : false}');
else
	die('{"result" : true}');
?>