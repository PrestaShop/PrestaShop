<?php

include(dirname(__FILE__).'/../../../config/config.inc.php');
include(dirname(__FILE__).'/../../../init.php');
include(dirname(__FILE__).'/../mondialrelay.php');

global $cookie;

$cart = new Cart($cookie->id_cart);
$result_MR = Db::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."mr_method WHERE id_carrier='".(int)($cart->id_carrier)."' ;");

Db::getInstance()->delete(_DB_PREFIX_.'mr_selected','id_cart = "'.(int)($cart->id).'"');
$mrselected = new MondialRelayClass();
$mrselected->id_customer = $cart->id_customer;
$mrselected->id_method = $result_MR[0]['id_mr_method'];
$mrselected->id_cart = $cart->id;
$mrselected->MR_Selected_Num = $_POST['Num'];
$mrselected->MR_Selected_LgAdr1 = $_POST['LgAdr1'];
$mrselected->MR_Selected_LgAdr2 = $_POST['LgAdr2'];
$mrselected->MR_Selected_LgAdr3 = $_POST['LgAdr3'];
$mrselected->MR_Selected_LgAdr4 = $_POST['LgAdr4'];
$mrselected->MR_Selected_CP = $_POST['CP'];
$mrselected->MR_Selected_Ville = $_POST['Ville'];
$mrselected->MR_Selected_Pays = $_POST['Pays'];
$mrselected->save();


