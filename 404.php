<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/* Send the proper status code in HTTP headers */
header('HTTP/1.1 404 Not Found');
header('Status: 404 Not Found');

if (in_array(substr($_SERVER['REQUEST_URI'], -3), array('png', 'jpg', 'gif')))
{
	require_once(dirname(__FILE__).'/config/settings.inc.php');
	header('Location: '.__PS_BASE_URI__.'img/404.gif');
	exit;
}
elseif (in_array(substr($_SERVER['REQUEST_URI'], -3), array('.js', 'css')))
	die('');

require_once(dirname(__FILE__).'/config/config.inc.php');
Controller::getController('PageNotFoundController')->run();
