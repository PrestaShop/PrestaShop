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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
global $smarty;
$smarty->debugging = false;
$smarty->debugging_ctrl = 'NONE';

function smartyTranslate($params, &$smarty)
{
	$htmlentities = !isset($params['js']);
    $pdf = isset($params['pdf']);
	$addslashes = isset($params['slashes']);

    if ($pdf)
    {
		global $_LANGPDF;
		$iso = Context::getContext()->language->iso_code;
		if (!Validate::isLanguageIsoCode($iso))
			throw PrestaShopException('Invalid iso lang!');

        $translationsFile = _PS_THEME_DIR_.'pdf/lang/'.$iso.'.php';

        if (Tools::file_exists_cache($translationsFile))
            @include_once($translationsFile);

        $key = 'PDF'.md5($params['s']);
        $lang_array = $_LANGPDF;

	    $msg = $params['s'];
	    if (is_array($lang_array) AND key_exists($key, $lang_array))
		    $msg = $lang_array[$key];
	    elseif (is_array($lang_array) && key_exists(Tools::strtolower($key), $lang_array))
		    $msg = $lang_array[Tools::strtolower($key)];

        return $msg;
    }

	$filename = ((!isset($smarty->compiler_object) OR !is_object($smarty->compiler_object->template)) ? $smarty->template_resource : $smarty->compiler_object->template->getTemplateFilepath());

	// If the template is part of a module
	if (!empty($params['mod']))
	{
		global $_MODULES, $_MODULE;

		$key = Tools::substr(basename($filename), 0, -4).'_'.md5($params['s']);
		$iso = Language::getIsoById(Context::getContext()->language->id);

		if (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/'.$params['mod'].'/'.$iso.'.php'))
		{
			$translationsFile = _PS_THEME_DIR_.'modules/'.$params['mod'].'/'.$iso.'.php';
			$key = '<{'.$params['mod'].'}'._THEME_NAME_.'>'.$key;
		}
		else
		{
			// @retrocompatibility with translations files in module root
			if (Tools::file_exists_cache(_PS_MODULE_DIR_.$params['mod'].'/translations'))
				$translationsFile = _PS_MODULE_DIR_.$params['mod'].'/translations/'.$iso.'.php';
			else
				$translationsFile = _PS_MODULE_DIR_.$params['mod'].'/'.$iso.'.php';
			$key = '<{'.$params['mod'].'}prestashop>'.$key;
		}

		if (!is_array($_MODULES))
			$_MODULES = array();
		if (@include_once($translationsFile))
			if (is_array($_MODULE))
				$_MODULES = array_merge($_MODULES, $_MODULE);
		$lang_array = $_MODULES;
		if (is_array($lang_array) && key_exists($key, $lang_array))
			$msg = $lang_array[$key];
		elseif (is_array($lang_array) && key_exists(Tools::strtolower($key), $lang_array))
			$msg = $lang_array[Tools::strtolower($key)];
		else
			$msg = $params['s'];
		return $msg;
	}

	// If the tpl is at the root of the template folder
	if (dirname($filename) == '.')
		$class = 'index';
	// If the tpl is used by a Helper
	elseif (strpos($filename, 'helpers') === 0)
		$class = 'Helper';
	// If the tpl is used by a Controller
	else
	{
		// Split by \ and / to get the folder tree for the file
		$folder_tree = preg_split('#[/\\\]#', $filename);
		$key = array_search('controllers', $folder_tree);

		// If there was a match, construct the class name using the child folder name
		// Eg. xxx/controllers/customers/xxx => AdminCustomers
		if ($key !== false)
			$class = 'Admin'.Tools::toCamelCase($folder_tree[$key + 1], true);
		else
			$class = null;
	}

	return AdminController::translate($params['s'], $class, $addslashes, $htmlentities);
}
