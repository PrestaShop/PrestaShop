<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include(dirname(__FILE__).'/config/config.inc.php');
include(dirname(__FILE__).'/init.php');

$cookie = new Cookie('ps');
if (!$cookie->isLogged())
	Tools::redirect('authentication.php?back=order-follow.php');

if (isset($_GET['id_order_slip']) AND Validate::isUnsignedId($_GET['id_order_slip']))
	$orderSlip = new OrderSlip((int)($_GET['id_order_slip']));
if (!isset($orderSlip) OR !Validate::isLoadedObject($orderSlip))
    die(Tools::displayError('Order return not found'));
elseif ($orderSlip->id_customer != $cookie->id_customer)
    die(Tools::displayError('Order return not found'));
$order = new Order((int)($orderSlip->id_order));
if (!Validate::isLoadedObject($order))
    die(Tools::displayError('Order not found'));
$order->products = OrderSlip::getOrdersSlipProducts((int)($orderSlip->id), $order);
$ref = NULL;
PDF::invoice($order, 'D', false, $ref, $orderSlip);

