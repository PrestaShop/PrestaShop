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

/* Redefine REQUEST_URI */
$_SERVER['REQUEST_URI'] = '/install/index_cli.php';
require_once dirname(__FILE__).'/init.php';
ini_set('memory_limit', '128M');

try
{
	require_once _PS_INSTALL_PATH_.'classes/datas.php';
	if (!($argc-1))
	{
		$available_arguments = Datas::getInstance()->getArgs();
		echo 'Arguments available:'."\n";
		foreach ($available_arguments as $key => $arg)
		{
			$name = isset($arg['name']) ? $arg['name'] : $key;
			echo '--'.$name."\t".(isset($arg['help']) ? $arg['help'] : '').(isset($arg['default']) ? "\t".'(Default: '.$arg['default'].')' : '')."\n";
		}
		exit;
	}
	if (($errors = Datas::getInstance()->getAndCheckArgs($argv)) !== true)
	{
		if (count($errors))
			foreach ($errors as $error)
				echo $error."\n";
		exit;
	}
	require_once _PS_INSTALL_PATH_.'classes/controllerConsole.php';
	InstallControllerConsole::execute();
	echo '-- Installation successfull! --'."\n";
}
catch (PrestashopInstallerException $e)
{
	$e->displayMessage();
}
