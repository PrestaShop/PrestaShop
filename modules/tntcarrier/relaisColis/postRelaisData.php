<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/


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
	Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'tnt_carrier_drop_off` SET `code` = "'.pSQL($code).'", `name` = "'.pSQL($name).'",
								`address` = "'.pSQL($address).'", `zipcode` = "'.pSQL($zipcode).'", `city` = "'.pSQL($city).'" WHERE `id_cart` = "'.(int)($id_cart).'"');
}
else
	Db::getInstance()->ExecuteS('INSERT INTO `'._DB_PREFIX_.'tnt_carrier_drop_off` (`id_cart`, `code`, `name`, `address`, `zipcode`, `city`) 
							VALUES ("'.(int)($id_cart).'", "'.pSQL($code).'", "'.pSQL($name).'", "'.pSQL($address).'", "'.pSQL($zipcode).'", "'.pSQL($city).'")');
