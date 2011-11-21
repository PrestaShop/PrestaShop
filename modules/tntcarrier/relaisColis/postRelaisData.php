<?php

require('../../../config/config.inc.php');

$code = $_POST['tntRCSelectedCode'];
$name = $_POST['tntRCSelectedNom'];
$address = $_POST['tntRCSelectedAdresse'];
$zipcode = $_POST['tntRCSelectedCodePostal'];
$city = $_POST['tntRCSelectedCommune'];
$id_cart = $_POST['id_cart'];

$data = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'tnt_carrier_drop_off` WHERE `id_cart` = "'.(int)($id_cart).'"');
if (count($data) > 0)
{
	echo "ok";
	Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'tnt_carrier_drop_off` SET `code` = "'.$code.'", `name` = "'.$name.'",
								`address` = "'.$address.'", `zipcode` = "'.$zipcode.'", `city` = "'.$city.'" WHERE `id_cart` = "'.(int)($id_cart).'"');
}
else
	Db::getInstance()->ExecuteS('INSERT INTO `'._DB_PREFIX_.'tnt_carrier_drop_off` (`id_cart`, `code`, `name`, `address`, `zipcode`, `city`) 
							VALUES ("'.(int)($id_cart).'", "'.$code.'", "'.$name.'", "'.$address.'", "'.$zipcode.'", "'.$city.'")');
?>