<?php

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('../../modules/socolissimo/socolissimo.php');

global $cookie;

if (Tools::getValue('token') == sha1('socolissimo'._COOKIE_KEY_.$cookie->id_cart))
	die('INVALID TOKEN');

$socolissimo = new Socolissimo();
$result = $socolissimo->getDeliveryInfos(Context::getContext()->cart->id, Context::getContext()->customer->id);
if (!$result)
	die('{"result" : false}');
else
	die('{"result" : true}');

?>