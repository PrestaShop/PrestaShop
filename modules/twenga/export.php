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

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once(dirname(__FILE__).'/twenga.php');

if ((sha1(Configuration::get('TWENGA_TOKEN')._COOKIE_KEY_)) != Tools::getValue('twenga_token'))
	die('FATAL ERROR : INVALID TOKEN');

@ini_set('memory_limit', '300M');
if (!ini_get('safe_mode'))
	@set_time_limit(300);

header("Content-type: text/xml; charset=utf-8");
$export = new Twenga();
$export->buildXML();
