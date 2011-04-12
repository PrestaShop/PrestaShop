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

if (Configuration::get('PS_FORCE_SMARTY_2'))
	define('_PS_SMARTY_DIR_', _PS_TOOL_DIR_.'smarty_v2/');
else
	define('_PS_SMARTY_DIR_', _PS_TOOL_DIR_.'smarty/');

require_once(_PS_SMARTY_DIR_.'Smarty.class.php');

global $smarty;
$smarty = new Smarty();
$smarty->template_dir = _PS_THEME_DIR_.'tpl';
$smarty->compile_dir = _PS_SMARTY_DIR_.'compile';
$smarty->cache_dir = _PS_SMARTY_DIR_.'cache';
$smarty->config_dir = _PS_SMARTY_DIR_.'configs';
$smarty->caching = false;
$smarty->force_compile = (bool)Configuration::get('PS_SMARTY_FORCE_COMPILE');
$smarty->compile_check = false;

if (Configuration::get('PS_FORCE_SMARTY_2')) 
{	
	$smarty->debug_tpl = _PS_ALL_THEMES_DIR_.'debug.tpl';
	
	if (Configuration::get('PS_HTML_THEME_COMPRESSION'))
		$smarty->register_outputfilter('smartyMinifyHTML');
	if (Configuration::get('PS_JS_HTML_THEME_COMPRESSION'))
		$smarty->register_outputfilter('smartyPackJSinHTML');
}
else
{
	if (Configuration::get('PS_HTML_THEME_COMPRESSION'))
		$smarty->registerFilter('output', 'smartyMinifyHTML');
	if (Configuration::get('PS_JS_HTML_THEME_COMPRESSION'))
		$smarty->registerFilter('output', 'smartyPackJSinHTML');
}

smartyRegisterFunction($smarty, 'modifier', 'truncate', 'smarty_modifier_truncate');
smartyRegisterFunction($smarty, 'modifier', 'secureReferrer', array('Tools', 'secureReferrer'));

smartyRegisterFunction($smarty, 'function', 't', 'smartyTruncate');
smartyRegisterFunction($smarty, 'function', 'm', 'smartyMaxWords');
smartyRegisterFunction($smarty, 'function', 'p', 'smartyShowObject');
smartyRegisterFunction($smarty, 'function', 'd', 'smartyDieObject');
smartyRegisterFunction($smarty, 'function', 'l', 'smartyTranslate');

smartyRegisterFunction($smarty, 'function', 'dateFormat', array('Tools', 'dateFormat'));
smartyRegisterFunction($smarty, 'function', 'productPrice', array('Product', 'productPrice'));
smartyRegisterFunction($smarty, 'function', 'convertPrice', array('Product', 'convertPrice'));
smartyRegisterFunction($smarty, 'function', 'convertPriceWithoutDisplay', array('Product', 'productPriceWithoutDisplay'));
smartyRegisterFunction($smarty, 'function', 'convertPriceWithCurrency', array('Product', 'convertPriceWithCurrency'));
smartyRegisterFunction($smarty, 'function', 'displayWtPrice', array('Product', 'displayWtPrice'));
smartyRegisterFunction($smarty, 'function', 'displayWtPriceWithCurrency', array('Product', 'displayWtPriceWithCurrency'));
smartyRegisterFunction($smarty, 'function', 'displayPrice', array('Tools', 'displayPriceSmarty'));
smartyRegisterFunction($smarty, 'modifier', 'convertAndFormatPrice', array('Product', 'convertAndFormatPrice'));

function smartyTranslate($params, &$smarty)
{
	/*
	 * Warning in Smarty-v2 : 2 lines have been added to the Smarty class.
	 * "public $currentTemplate = null;" into the class itself
	 * "$this->currentTemplate = Tools::substr(basename($resource_name), 0, -4);" into the "display" method
	 *
	 * In Smarty-v3 : No modifications, using the existing var $this->smarty->_current_file instead
	 */
	global $_LANG, $_MODULES, $cookie, $_MODULE;
	if (!isset($params['js'])) $params['js'] = 0;
	if (!isset($params['mod'])) $params['mod'] = false;
	
	$string = str_replace('\'', '\\\'', $params['s']);
	$key = '';
	if (Configuration::get('PS_FORCE_SMARTY_2')) /* Keep a backward compatibility for Smarty v2 */
		$key = $smarty->currentTemplate.'_'.md5($string);
	else
	{
		$filename = ((!isset($smarty->compiler_object) OR !is_object($smarty->compiler_object->template)) ? $smarty->template_filepath : $smarty->compiler_object->template->getTemplateFilepath());
		$key = Tools::substr(basename($filename), 0, -4).'_'.md5($string);
	}
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

function smartyDieObject($params, &$smarty)
{
	return Tools::d($params['var']);
}

function smartyShowObject($params, &$smarty)
{
	return Tools::p($params['var']);
}

function smartyMaxWords($params, &$smarty)
{
	$params['s'] = str_replace('...', ' ...', html_entity_decode($params['s'], ENT_QUOTES, 'UTF-8'));
	$words = explode(' ', $params['s']);
	
	foreach($words AS &$word)
		if(Tools::strlen($word) > $params['n'])
			$word = Tools::substr(trim(chunk_split($word, $params['n']-1, '- ')), 0, -1);

	return implode(' ',  Tools::htmlentitiesUTF8($words));
}

function smartyTruncate($params, &$smarty)
{
	$text = isset($params['strip']) ? strip_tags($params['text']) : $params['text'];
	$length = $params['length'];
	$sep = isset($params['sep']) ? $params['sep'] : '...';

	if (Tools::strlen($text) > $length + Tools::strlen($sep))
		$text = Tools::substr($text, 0, $length).$sep;

	return (isset($params['encode']) ? Tools::htmlentitiesUTF8($text, ENT_NOQUOTES) : $text);
}

function smarty_modifier_truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false, $charset = 'UTF-8')
{
	if (!$length)
		return '';
 
	if (Tools::strlen($string) > $length)
	{
		$length -= min($length, Tools::strlen($etc));
		if (!$break_words && !$middle)
			$string = preg_replace('/\s+?(\S+)?$/u', '', Tools::substr($string, 0, $length+1, $charset));
		return !$middle ? Tools::substr($string, 0, $length, $charset).$etc : Tools::substr($string, 0, $length/2, $charset).$etc.Tools::substr($string, -$length/2, $charset);
	}
	else
		return $string;
}

function smartyMinifyHTML($tpl_output, &$smarty)
{
    $tpl_output = Tools::minifyHTML($tpl_output);
    return $tpl_output;
}

function smartyPackJSinHTML($tpl_output, &$smarty)
{
    $tpl_output = Tools::packJSinHTML($tpl_output);
    return $tpl_output;
}

function smartyRegisterFunction($smarty, $type, $function, $params)
{
	if (!in_array($type, array('function', 'modifier')))
		return false;
	if (!Configuration::get('PS_FORCE_SMARTY_2'))
		$smarty->registerPlugin($type, $function, $params); // Use Smarty 3 API calls, only if PHP version > 5.1.2
	else
		$smarty->{'register_'.$type}($function, $params); // or keep a backward compatibility if PHP version < 5.1.2
}
