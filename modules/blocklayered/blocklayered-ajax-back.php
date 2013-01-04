<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registred Trademark & Property of PrestaShop SA
*/

/* Getting cookie or logout */
include(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');

if (substr(Tools::encrypt('blocklayered/index'),0,10) != Tools::getValue('layered_token') || !Module::isInstalled('blocklayered'))
	die('Bad token');

include(dirname(__FILE__).'/blocklayered.php');

$category_box = Tools::getValue('categoryBox');

/* Clean categoryBox before use */
if (is_array($category_box))
	foreach ($category_box AS &$value)
		$value = (int)$value;

$blockLayered = new BlockLayered();
echo $blockLayered->ajaxCallBackOffice($category_box, Tools::getValue('id_layered_filter'));
