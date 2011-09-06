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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/


// autoloader 1.3 / 1.4 
/*
if(!function_exists('__autoload'))
{
	die('fct already exists -_-');	
	function __autoload($className)
	{
		info("try $className");
		if(!class_exists($className,false) AND class_exists($className.'Core',false))
		{
			echo 'pouet';
			eval('class '.$className.' extends '.$className.'Core {}');
		}
		
	}
}
else
{
	die('fct already exists -_-');	
}
*/
ob_start();
$timerStart = microtime(true);

$currentFileName = array_reverse(explode("/", $_SERVER['SCRIPT_NAME']));
// $cookieLifetime = (time() + (((int)Configuration::get('PS_COOKIE_LIFETIME_BO') > 0 ? (int)Configuration::get('PS_COOKIE_LIFETIME_BO') : 1)* 3600));
$cookieLifetime = time() + 84600;
$adminFilename = trim($_POST['dir'],'/').'/';
// die(info($adminFilename));
require_once(AUTOUPGRADE_MODULE_DIR.'Tools14.php');
require_once(AUTOUPGRADE_MODULE_DIR.'AdminSelfTab.php');
require_once(AUTOUPGRADE_MODULE_DIR.'AdminSelfUpgrade.php');
// $needClass = array('Cookie'); //, 'ObjectModel', 'Db', 'MySQL', 'SubDomain', 'Tools');
$needClass = array();
foreach ($needClass as $class)
{
	if (!class_exists($class,false))
	{
		if(file_exists(_PS_ADMIN_DIR_.'/autoupgrade/'.$class.'.php'))
		{
			require_once(_PS_ADMIN_DIR_.'/autoupgrade/'.$class.'.php');
			info($class,'from autoupgrade');
		}
		else
			require_once(_PS_ROOT_DIR_.'/classes/'.$class.'.php');
		if (version_compare(_PS_VERSION_, '1.4','<'))
			if (!class_exists($class,false) AND class_exists($class.'Core',false))
				eval ('class '.$class.' extends '.$class.'Core{}');
	}
}


	$currentIndex = $_SERVER['SCRIPT_NAME'].(($tab = Tools14::getValue('tab')) ? '?tab='.$tab : '');



