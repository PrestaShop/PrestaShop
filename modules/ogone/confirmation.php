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

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/ogone.php');

$ogone = new Ogone();
$id_module = $ogone->id;
$id_cart = Tools::getValue('orderID');
$key = Db::getInstance()->getValue('SELECT secure_key FROM '._DB_PREFIX_.'customer WHERE id_customer = '.(int)$cookie->id_customer);
$link = new Link();

$smarty->assign(array(
	'id_module' => $id_module,
	'id_cart' => $id_cart,
	'key' => $key,
	'ogone_link' => (method_exists($link, 'getPageLink') ? $link->getPageLink('my-account.php') : _PS_BASE_URL_.'my-account.php')
	));
echo $ogone->display(__FILE__, 'waiting.tpl');

include(dirname(__FILE__).'/../../footer.php');


