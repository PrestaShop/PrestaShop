<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7389 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function deactivate_custom_modules()
{
	$db = Db::getInstance();
	$modulesDirOnDisk = array();
	$modules = scandir(_PS_MODULE_DIR_);
	foreach ($modules AS $name)
	{
		if (is_dir(_PS_MODULE_DIR_.$name) && file_exists(_PS_MODULE_DIR_.$name.'/'.$name.'.php'))
		{
			if (!preg_match('/^[a-zA-Z0-9_-]+$/', $name))
				die(Tools::displayError().' (Module '.$name.')');
			$modulesDirOnDisk[] = $name;
		}
	}

	$module_list_xml = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'xml'.DIRECTORY_SEPARATOR.'modules_list.xml';

	if (!file_exists($module_list_xml))
	{
		$module_list_xml = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'modules_list.xml';
		if (!file_exists($module_list_xml))
		return false;
	}

	$nativeModules = simplexml_load_file($module_list_xml);
	$nativeModules = $nativeModules->modules;
	$arrNativeModules = array();
	foreach ($nativeModules as $nativeModulesType)
		if (in_array($nativeModulesType['type'],array('native','partner')))
		{
			$arrNativeModules[] = '""';
			foreach ($nativeModulesType->module as $module)
				$arrNativeModules[] = '"'.pSQL($module['name']).'"';
		}

	$arrNonNative = $db->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'module` m
		WHERE name NOT IN ('.implode(',',$arrNativeModules).') ');

	$uninstallMe = array("undefined-modules");
	if (is_array($arrNonNative))
		foreach($arrNonNative as $aModule)
			$uninstallMe[] = $aModule['name'];

	if (!is_array($uninstallMe))
		$uninstallMe = array($uninstallMe);

	foreach ($uninstallMe as $k=>$v)
		$uninstallMe[$k] = '"'.pSQL($v).'"';

	return Db::getInstance()->execute('
	UPDATE `'._DB_PREFIX_.'module`
	SET `active`= 0
	WHERE `name` IN ('.implode(',',$uninstallMe).')');
}

