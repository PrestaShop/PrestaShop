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
$smarty->template_dir = _PS_THEME_DIR_.'tpl';

function smartyTranslate($params, &$smarty)
{
	global $_LANG, $_MODULES, $cookie, $_MODULE, $_LANGPDF;
	if (!isset($params['js'])) $params['js'] = 0;
	if (!isset($params['pdf'])) $params['pdf'] = false;
	if (!isset($params['mod'])) $params['mod'] = false;

	$string = str_replace('\'', '\\\'', $params['s']);
	$filename = ((!isset($smarty->compiler_object) OR !is_object($smarty->compiler_object->template)) ? $smarty->template_resource : $smarty->compiler_object->template->getTemplateFilepath());
	$key = Tools::substr(basename($filename), 0, -4).'_'.md5($string);
	$lang_array = $_LANG;
	if ($params['mod'])
	{
		$iso = Language::getIsoById($cookie->id_lang);

		if (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/'.$params['mod'].'/'.$iso.'.php'))
		{
			$translationsFile = _PS_THEME_DIR_.'modules/'.$params['mod'].'/'.$iso.'.php';
			$key = '<{'.$params['mod'].'}'._THEME_NAME_.'>'.$key;
		}
		else
		{
			$translationsFile = _PS_MODULE_DIR_.$params['mod'].'/'.$iso.'.php';
			$key = '<{'.$params['mod'].'}prestashop>'.$key;
		}

		if(!is_array($_MODULES))
			$_MODULES = array();
		if (@include_once($translationsFile))
			if(is_array($_MODULE))
				$_MODULES = array_merge($_MODULES, $_MODULE);
		$lang_array = $_MODULES;
	}
	else if ($params['pdf']) 
	{
		$iso = Language::getIsoById($cookie->id_lang);
		$translationsFile = _PS_THEME_DIR_.'pdf/lang/'.$iso.'.php';

		if (Tools::file_exists_cache($translationsFile))
			@include_once($translationsFile);
		
		$key = 'PDF'.md5($string);
		$lang_array = $_LANGPDF;
	}

	if (is_array($lang_array) AND key_exists($key, $lang_array))
		$msg = $lang_array[$key];
	elseif (is_array($lang_array) AND key_exists(Tools::strtolower($key), $lang_array))
		$msg = $lang_array[Tools::strtolower($key)];
	else
		$msg = $params['s'];

	if ($msg != $params['s'])
		$msg = $params['js'] ? addslashes($msg) : stripslashes($msg);

	return $params['js'] ? $msg : Tools::htmlentitiesUTF8($msg);
}
