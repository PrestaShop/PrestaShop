<?php
require_once(realpath(dirname(__FILE__).'/../../config/config.inc.php'));

require_once(realpath(dirname(__FILE__).'/../../init.php'));
require_once(realpath(dirname(__FILE__).'/dejala.php'));

$params['cart'] = new Cart($cookie->id_cart);
$djl = new Dejala() ;

$result = array() ;

$djl->hookProcessCarrier($params) ;
echo json_encode($result);
