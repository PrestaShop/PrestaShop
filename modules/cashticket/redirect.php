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
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/cashticket.php');

$module = new CashTicket();

if (!$cart->id OR $cart->id_customer == 0 OR $cart->id_address_delivery == 0 OR $cart->id_address_invoice == 0 OR !$module->active)
	Tools::redirect('order.php?step=3');
	
$currency = new Currency($cart->id_currency);
if (!$module->isCurrencyActive($currency->iso_code))
	Tools::redirect('order.php?step=3');

$result = $module->createDisposition($cart);

if ($result['return_code'] != 0)
{
	include(dirname(__FILE__).'/../../header.php');
	echo $module->getL('cant_create_dispo');
	include(dirname(__FILE__).'/../../footer.php');
}
else
	Tools::redirectLink($result['message']);

