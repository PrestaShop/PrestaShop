<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (function_exists('date_default_timezone_set'))
{
	// date_default_timezone_get calls date_default_timezone_set, which can provide warning
	$timezone = @date_default_timezone_get();
	date_default_timezone_set($timezone);
}

if (!defined('_PS_MODULE_DIR_'))
	define('_PS_MODULE_DIR_', realpath(dirname(__FILE__).'/../../').'/modules/');

define('AUTOUPGRADE_MODULE_DIR', _PS_MODULE_DIR_.'autoupgrade/');
require_once(AUTOUPGRADE_MODULE_DIR.'functions.php');
//
// the following test confirm the directory exists
if (realpath(dirname(__FILE__).'/../../').DIRECTORY_SEPARATOR.$_POST['dir'] !== realpath(realpath(dirname(__FILE__).'/../../').DIRECTORY_SEPARATOR.$_POST['dir']))
	die('wrong directory :'.$_POST['dir']);

define('_PS_ADMIN_DIR_', realpath(dirname(__FILE__).'/../../').DIRECTORY_SEPARATOR.$_POST['dir']);

// defines.inc.php can not exists (1.3.0.1 for example)
// but we need _PS_ROOT_DIR_
if (!defined('_PS_ROOT_DIR_'))
	define('_PS_ROOT_DIR_', realpath(_PS_ADMIN_DIR_.'/../'));

// ajax-upgrade-tab is located in admin/autoupgrade directory
require_once(_PS_ROOT_DIR_.'/config/settings.inc.php');

//require(_PS_ADMIN_DIR_.'/functions.php');
include(AUTOUPGRADE_MODULE_DIR.'init.php');

// this is used to set this->ajax = true in the constructor
global $ajax;

$ajax = true;
$adminObj = new AdminSelfUpgrade();

if (is_object($adminObj))
{
	$adminObj->optionDisplayErrors();
	$adminObj->ajax = 1;
	if ($adminObj->checkToken())
	{
		// the differences with index.php is here
		$adminObj->ajaxPreProcess();
		$action = Tools14::getValue('action');

		// no need to use displayConf() here

		if (!empty($action) && method_exists($adminObj, 'ajaxProcess'.$action) )
			$adminObj->{'ajaxProcess'.$action}();
		else
			$adminObj->ajaxProcess();

		// @TODO We should use a displayAjaxError
		$adminObj->displayErrors();
		if (!empty($action) && method_exists($adminObj, 'displayAjax'.$action))
			$adminObj->{'displayAjax'.$action}();
		else
			$adminObj->displayAjax();
	}
	else
	{
		// If this is an XSS attempt, then we should only display a simple, secure page
		ob_clean();
		die('{wrong token}');
	}
}
