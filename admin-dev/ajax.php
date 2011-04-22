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

define('PS_ADMIN_DIR', getcwd());
include(PS_ADMIN_DIR.'/../config/config.inc.php');
/* Getting cookie or logout */
require_once(dirname(__FILE__).'/init.php');

require_once(PS_ADMIN_DIR.'/tabs/AdminCounty.php');

if (isset($_GET['changeParentUrl']))
	echo '<script type="text/javascript">parent.parent.document.location.href = "'.addslashes(urldecode(Tools::getValue('changeParentUrl'))).'";</script>';
if (isset($_GET['installBoughtModule']))
{
	if (!class_exists('ZipArchive', false))
		die(displayJavascriptAlert('Host does not handle Zip files'));
	$zip = new ZipArchive();
	$file = false;
	while ($file === false OR file_exists(_PS_MODULE_DIR_.$file))
		$file = uniqid();
	$file = _PS_MODULE_DIR_.$file.'.zip';
	if (!copy('http://addons.prestashop.com/iframe/getboughtfile.php?id_order_detail='.Tools::getValue('id_order_detail').'&token='.Tools::getValue('token'), $file))
		die(displayJavascriptAlert('Cannot copy file'));
	$first6 = fread($fd = fopen($file, 'r'), 6);
	if (!strncmp($first6, 'Error:', 6))
	{
		fclose($fd);
		unlink($file);
		die(displayJavascriptAlert(fread($fd, 1024)));
	}
	fclose($fd);
	if ($zip->open($file) !== true OR !$zip->extractTo(_PS_MODULE_DIR_) OR !$zip->close())
	{
		unlink($file);
		die(displayJavascriptAlert('Cannot unzip file'));
	}
	unlink($file);
	die(displayJavascriptAlert('Module copied to disk'));
}

function displayJavascriptAlert($s){echo '<script type="text/javascript">alert(\''.addslashes($s).'\');</script>';}

if (isset($_GET['ajaxProductManufacturers']))
{
	$currentIndex = 'index.php?tab=AdminCatalog';
	$manufacturers = Manufacturer::getManufacturers();
	if ($manufacturers)
	{
		$jsonArray = array();
		foreach ($manufacturers AS $manufacturer)
			$jsonArray[] = '{"optionValue": "'.$manufacturer['id_manufacturer'].'", "optionDisplay": "'.htmlspecialchars($manufacturer['name']).'"}';
		die('['.implode(',', $jsonArray).']');
	}
}

if (isset($_GET['ajaxProductSuppliers']))
{
	$currentIndex = 'index.php?tab=AdminCatalog';
	$suppliers = Supplier::getSuppliers();
	if ($suppliers)
	{
		$jsonArray = array();
		foreach ($suppliers AS $supplier)
			$jsonArray[] = '{"optionValue": "'.$supplier['id_supplier'].'", "optionDisplay": "'.htmlspecialchars($supplier['name']).'"}';
		die('['.implode(',', $jsonArray).']');
	}
}

if (isset($_GET['ajaxProductAccessories']))
{
	$currentIndex = 'index.php?tab=AdminCatalog';
	$jsonArray = array();

	$products = Db::getInstance()->ExecuteS('
	SELECT p.`id_product`, pl.`name`
	FROM `'._DB_PREFIX_.'product` p
	NATURAL LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
	WHERE pl.`id_lang` = '.(int)(Tools::getValue('id_lang')).'
	AND p.`id_product` != '.(int)(Tools::getValue('id_product')).'
	AND p.`id_product` NOT IN (
		SELECT a.`id_product_2`
		FROM `'._DB_PREFIX_.'accessory` a
		WHERE a.`id_product_1` = '.(int)(Tools::getValue('id_product')).')
	ORDER BY pl.`name`');

	foreach ($products AS $accessory)
		$jsonArray[] = '{"value: "'.(int)($accessory['id_product']).'-'.addslashes($accessory['name']).'", "text":"'.(int)($accessory['id_product']).' - '.addslashes($accessory['name']).'"}';
	die('['.implode(',', $jsonArray).']');
}

if (isset($_GET['ajaxDiscountCustomers']))
{
	global $cookie;

	$currentIndex = 'index.php?tab=AdminDiscounts';
	$jsonArray = array();
	$filter = Tools::getValue('filter');

	if (Validate::isBool_Id($filter))
		$filterArray = explode('_', $filter);

	$customers = Db::getInstance()->ExecuteS('
	SELECT `id_customer`, `email`, CONCAT(`lastname`, \' \', `firstname`) as name
	FROM `'._DB_PREFIX_.'customer`
	WHERE `deleted` = 0 AND is_guest = 0
	AND '.(Validate::isUnsignedInt($filter) ? '`id_customer` = '.(int)($filter) : '(`email` LIKE "%'.pSQL($filter).'%"
	'.((Validate::isBool_Id($filter) AND $filterArray[0] == 0) ? 'OR `id_customer` = '.(int)($filterArray[1]) : '').'
	'.(Validate::isUnsignedInt($filter) ? '`id_customer` = '.(int)($filter) : '').'
	OR CONCAT(`firstname`, \' \', `lastname`) LIKE "%'.pSQL($filter).'%"
	OR CONCAT(`lastname`, \' \', `firstname`) LIKE "%'.pSQL($filter).'%")').'
	ORDER BY CONCAT(`lastname`, \' \', `firstname`) ASC
	LIMIT 50');

	$groups = Db::getInstance()->ExecuteS('
	SELECT g.`id_group`, gl.`name`
	FROM `'._DB_PREFIX_.'group` g
	LEFT JOIN `'._DB_PREFIX_.'group_lang` AS gl ON (g.`id_group` = gl.`id_group` AND gl.`id_lang` = '.(int)($cookie->id_lang).')
	WHERE '.(Validate::isUnsignedInt($filter) ? 'g.`id_group` = '.(int)($filter) : 'gl.`name` LIKE "%'.pSQL($filter).'%"
	'.((Validate::isBool_Id($filter) AND $filterArray[0] == 1) ? 'OR g.`id_group` = '.(int)($filterArray[1]) : '')).'
	ORDER BY gl.`name` ASC
	LIMIT 50');

	$json = '{"customers" : ';
	foreach ($customers AS $customer)
		$jsonArray[] = '{"value":"0_'.(int)($customer['id_customer']).'", "text":"'.addslashes($customer['name']).' ('.addslashes($customer['email']).')"}';
	$json .= '['.implode(',', $jsonArray).'],
		"groups" : ';
	$jsonArray = array();
	foreach ($groups AS $group)
		$jsonArray[] = '{"value":"1_'.(int)($group['id_group']).'", "text":"'.addslashes($group['name']).'"}';
	$json .= '['.implode(',', $jsonArray).']}';
	die($json);
}

if (Tools::getValue('page') == 'prestastore' AND @fsockopen('addons.prestashop.com', 80, $errno, $errst, 3))
	readfile('http://addons.prestashop.com/adminmodules.php?lang='.Language::getIsoById($cookie->id_lang));
if (Tools::getValue('page') == 'themes'  AND @fsockopen('addons.prestashop.com', 80, $errno, $errst, 3))
	readfile('http://addons.prestashop.com/adminthemes.php?lang='.Language::getIsoById($cookie->id_lang));

if ($step = (int)(Tools::getValue('ajaxProductTab')))
{
	require_once(dirname(__FILE__).'/tabs/AdminCatalog.php');
	$catalog = new AdminCatalog();
	$admin = new AdminProducts();

	$languages = Language::getLanguages(false);
	$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
	$product = new Product((int)(Tools::getValue('id_product')));
	if (!Validate::isLoadedObject($product))
		die (Tools::displayError('Product cannot be loaded'));

	$switchArray = array(3 => 'displayFormPrices', 4 => 'displayFormAttributes', 5 => 'displayFormFeatures', 6 => 'displayFormCustomization', 7 => 'displayFormAttachments');
	$currentIndex = 'index.php?tab=AdminCatalog';
	if (key_exists($step, $switchArray))
		$admin->{$switchArray[$step]}($product, $languages, $defaultLanguage);
}

if (isset($_GET['getAvailableFields']) and isset($_GET['entity']))
{
	$currentIndex = 'index.php?tab=AdminImport';
	$jsonArray = array();
	require_once(dirname(__FILE__).'/tabs/AdminImport.php');
	$import = new AdminImport();

	$languages = Language::getLanguages(false);
	$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
	$fields = $import->getAvailableFields(true);
	foreach ($fields AS $field)
		$jsonArray[] = '{"field":"'.addslashes($field).'"}';
	die('['.implode(',', $jsonArray).']');
}

if (array_key_exists('ajaxModulesPositions', $_POST))
{
	$id_module = (int)(Tools::getValue('id_module'));
	$id_hook = (int)(Tools::getValue('id_hook'));
	$way = (int)(Tools::getValue('way'));
	$positions = Tools::getValue(strval($id_hook));
	$position = (is_array($positions)) ? array_search($id_hook.'_'.$id_module, $positions) : null;
	$module = Module::getInstanceById($id_module);
	if (Validate::isLoadedObject($module))
		if ($module->updatePosition($id_hook, $way, $position))
			die(true);
		else
			die('{"hasError" : true, "errors" : "Can not update module position"}');
	else
		die('{"hasError" : true, "errors" : "This module can not be loaded"}');
}

if (array_key_exists('ajaxCategoriesPositions', $_POST))
{
	$id_category_to_move = (int)(Tools::getValue('id_category_to_move'));
	$id_category_parent = (int)(Tools::getValue('id_category_parent'));
	$way = (int)(Tools::getValue('way'));
	$positions = Tools::getValue('category');
	if (is_array($positions))
		foreach ($positions AS $key => $value)
		{
			$pos = explode('_', $value);
			if ((isset($pos[1]) AND isset($pos[2])) AND ($pos[1] == $id_category_parent AND $pos[2] == $id_category_to_move))
			{
				$position = $key;
				break;
			}
		}
	$category = new Category($id_category_to_move);
	if (Validate::isLoadedObject($category))
	{
		if (isset($position) && $category->updatePosition($way, $position))
		{
			Module::hookExec('categoryUpdate');
			die(true);
		}
		else
			die('{"hasError" : true, errors : "Can not update categories position"}');
	}
	else
		die('{"hasError" : true, "errors" : "This category can not be loaded"}');

}

if (array_key_exists('ajaxCMSCategoriesPositions', $_POST))
{
	$id_cms_category_to_move = (int)(Tools::getValue('id_cms_category_to_move'));
	$id_cms_category_parent = (int)(Tools::getValue('id_cms_category_parent'));
	$way = (int)(Tools::getValue('way'));
	$positions = Tools::getValue('cms_category');
	if (is_array($positions))
		foreach ($positions AS $key => $value)
		{
			$pos = explode('_', $value);
			if ((isset($pos[1]) AND isset($pos[2])) AND ($pos[1] == $id_cms_category_parent AND $pos[2] == $id_cms_category_to_move))
			{
				$position = $key;
				break;
			}
		}
	$cms_category = new CMSCategory($id_cms_category_to_move);
	if (Validate::isLoadedObject($cms_category))
	{
		if (isset($position) && $cms_category->updatePosition($way, $position))
			die(true);
		else
			die('{"hasError" : true, "errors" : "Can not update cms categories position"}');
	}
	else
		die('{"hasError" : true, "errors" : "This cms category can not be loaded"}');
}

if (array_key_exists('ajaxCMSPositions', $_POST))
{
	$id_cms = (int)(Tools::getValue('id_cms'));
	$id_category = (int)(Tools::getValue('id_cms_category'));
	$way = (int)(Tools::getValue('way'));
	$positions = Tools::getValue('cms');
	if (is_array($positions))
		foreach ($positions AS $key => $value)
		{
			$pos = explode('_', $value);
			if ((isset($pos[1]) AND isset($pos[2])) AND ($pos[1] == $id_category AND $pos[2] == $id_cms))
			{
				$position = $key;
				break;
			}
		}
	$cms = new CMS($id_cms);
	if (Validate::isLoadedObject($cms))
	{
		if (isset($position) && $cms->updatePosition($way, $position))
			die(true);
		else
			die('{"hasError" : true, "errors" : "Can not update cms position"}');
	}
	else
		die('{"hasError" : true, "errors" : "This cms can not be loaded"}');
}

/* Modify product position in catalog */
if (array_key_exists('ajaxProductsPositions', $_POST))
{
	$way = (int)(Tools::getValue('way'));
	$id_product = (int)(Tools::getValue('id_product'));
	$id_category = (int)(Tools::getValue('id_category'));
	$positions = Tools::getValue('product');

	if (is_array($positions))
		foreach ($positions AS $position => $value)
		{
			// pos[1] = id_categ, pos[2] = id_product, pos[3]=old position
			$pos = explode('_', $value);

			if ((isset($pos[1]) AND isset($pos[2])) AND ($pos[1] == $id_category AND (int)$pos[2] === $id_product))
			{
				if ($product = new Product((int)$pos[2]))
					if (isset($position) && $product->updatePosition($way, $position))
						echo "ok position $position for product $pos[2]\r\n";
					else
						echo '{"hasError" : true, "errors" : "Can not update product '. $id_product . ' to position '.$position.' "}';
				else
					echo '{"hasError" : true, "errors" : "This product ('.$id_product.') can t be loaded"}';

				break;
			}
		}
}

if (isset($_GET['ajaxProductPackItems']))
{
	$jsonArray = array();
	$products = Db::getInstance()->ExecuteS('
	SELECT p.`id_product`, pl.`name`
	FROM `'._DB_PREFIX_.'product` p
	NATURAL LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
	WHERE pl.`id_lang` = '.(int)(Tools::getValue('id_lang')).'
	AND p.`id_product` NOT IN (SELECT DISTINCT id_product_pack FROM `'._DB_PREFIX_.'pack`)
	AND p.`id_product` != '.(int)(Tools::getValue('id_product')));

	foreach ($products AS $packItem)
		$jsonArray[] = '{"value": "'.(int)($packItem['id_product']).'-'.addslashes($packItem['name']).'", "text":"'.(int)($packItem['id_product']).' - '.addslashes($packItem['name']).'"}';
	die('['.implode(',', $jsonArray).']');
}

if (isset($_GET['ajaxStates']) AND isset($_GET['id_country']))
{
	$states = Db::getInstance()->ExecuteS('
	SELECT s.id_state, s.name
	FROM '._DB_PREFIX_.'state s
	LEFT JOIN '._DB_PREFIX_.'country c ON (s.`id_country` = c.`id_country`)
	WHERE s.id_country = '.(int)(Tools::getValue('id_country')).' AND s.active = 1 AND c.`contains_states` = 1
	ORDER BY s.`name` ASC');
	
	if (is_array($states) AND !empty($states))
	{
		$list = '';
		if (Tools::getValue('no_empty') != true)
			$list = '<option value="0">-----------</option>'."\n";

		foreach ($states AS $state)
			$list .= '<option value="'.(int)($state['id_state']).'"'.((isset($_GET['id_state']) AND $_GET['id_state'] == $state['id_state']) ? ' selected="selected"' : '').'>'.$state['name'].'</option>'."\n";
	}
	else
		$list = 'false';

	die($list);
}

if (Tools::isSubmit('submitCustomerNote') AND $id_customer = (int)Tools::getValue('id_customer'))
{
	$note = html_entity_decode(Tools::getValue('note'));
	if (!empty($note) AND !Validate::isCleanHtml($note))
		die ('error:validation');
	if (!Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'customer SET `note` = "'.pSQL($note, true).'" WHERE id_customer = '.(int)$id_customer.' LIMIT 1'))
		die ('error:update');
	die('ok');
}

if (Tools::getValue('form_language_id'))
{
	if (!($cookie->employee_form_lang = (int)(Tools::getValue('form_language_id'))))
		die ('Error while updating cookie.');
	die ('Form language updated.');
}

if (Tools::getValue('submitPublishProduct'))
{
	global $cookie;

	if (Tools::getIsset('id_product'))
	{
		$id_product = (int)(Tools::getValue('id_product'));
		$id_tab_catalog = (int)(Tab::getIdFromClassName('AdminCatalog'));
		$token = Tools::getAdminToken('AdminCatalog'.(int)($id_tab_catalog).(int)($cookie->id_employee));
		$bo_product_url = dirname($_SERVER['PHP_SELF']).'/index.php?tab=AdminCatalog&id_product='.$id_product.'&updateproduct&token='.$token;

		if (Tools::getValue('redirect'))
			die($bo_product_url);

		$profileAccess = Profile::getProfileAccess((int)($cookie->profile), $id_tab_catalog);
		if($profileAccess['edit'])
		{
			$product = new Product((int)(Tools::getValue('id_product')));
			if (!Validate::isLoadedObject($product))
				die('error: invalid id');

			$product->active = 1;

			if ($product->save())
				die($bo_product_url);
			else
				die('error: saving');

		} else {
			die('error: permissions');
		}
	}
	else
		die ('error: parameters');
}

if (Tools::getValue('submitPublishCMS'))
{
	global $cookie;

	if (Tools::getIsset('id_cms'))
	{
		$id_cms = (int)(Tools::getValue('id_cms'));
		$id_tab_cms = (int)(Tab::getIdFromClassName('AdminCMSContent'));
		$token = Tools::getAdminToken('AdminCMSContent'.(int)($id_tab_cms).(int)($cookie->id_employee));
		$bo_cms_url = dirname($_SERVER['PHP_SELF']).'/index.php?tab=AdminCMSContent&id_cms='.(int)$id_cms.'&updatecms&token='.$token;

		if (Tools::getValue('redirect'))
			die($bo_cms_url);

		$profileAccess = Profile::getProfileAccess((int)($cookie->profile), $id_tab_cms);
		if($profileAccess['edit'])
		{
			$cms = new CMS((int)(Tools::getValue('id_cms')));
			if (!Validate::isLoadedObject($cms))
				die('error: invalid id');

			$cms->active = 1;

			if ($cms->save())
				die($bo_cms_url);
			else
				die('error: saving');

		} else {
			die('error: permissions');
		}
	}
	else
		die ('error: parameters');
}

if (Tools::isSubmit('submitTrackClickOnHelp'))
{
    $label = Tools::getValue('label');
    $version = Tools::getValue('version');

    if (!empty($label) && !empty($version))
        HelpAccess::trackClick($label, $version);
}

if (Tools::isSubmit('saveImportMatchs'))
{
   $match = implode('|', Tools::getValue('type_value'));
   Db::getInstance()->Execute('INSERT INTO  `'._DB_PREFIX_.'import_match` (
								`id_import_match` ,
								`name` ,
								`match`,
								`skip`
								)
								VALUES (
								NULL ,
								\''.pSQL(Tools::getValue('newImportMatchs')).'\',
								\''.pSQL($match).'\',
								\''.pSQL(Tools::getValue('skip')).'\'
								)');

	die('{"id" : "'.Db::getInstance()->Insert_ID().'"}');
}

if (Tools::isSubmit('deleteImportMatchs'))
{
   Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'import_match` WHERE id_import_match = '.pSQL(Tools::getValue('idImportMatchs')));
}

if (Tools::isSubmit('loadImportMatchs'))
{
   $return = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'import_match` WHERE id_import_match = '.pSQL(Tools::getValue('idImportMatchs')));
   die('{"id" : "'.$return[0]['id_import_match'].'", "matchs" : "'.$return[0]['match'].'", "skip" : "'.$return[0]['skip'].'"}');
}

if (Tools::isSubmit('toggleScreencast'))
{
	global $cookie;
	$cookie->show_screencast = (int)(!(bool)$cookie->show_screencast);
}

if (Tools::isSubmit('ajaxAddZipCode') OR Tools::isSubmit('ajaxRemoveZipCode'))
{
	$zipcodes = Tools::getValue('zipcodes');
	$id_county = (int)Tools::getValue('id_county');

	$county = new County($id_county);
	if (!Validate::isLoadedObject($county))
		die('error');

	if (Tools::isSubmit('ajaxAddZipCode'))
	{
		if ($county->isZipCodeRangePresent($zipcodes))
			die('error:'.Tools::displayError('This Zip Code is already in use.'));
		if ($county->addZipCodes($zipcodes))
			die(AdminCounty::renderZipCodeList($county->getZipCodes()));
	}
	else if (Tools::isSubmit('ajaxRemoveZipCode') AND $county->removeZipCodes($zipcodes))
			die(AdminCounty::renderZipCodeList($county->getZipCodes()));

	die('error');
}

if (Tools::isSubmit('helpAccess'))
{
	$item = Tools::getValue('item');
	$isoUser = Tools::getValue('isoUser');
	$country = Tools::getValue('country');
	$version = Tools::getValue('version');

	if (isset($item) AND isset($isoUser) AND isset($country))
		die(HelpAccess::displayHelp($item, $isoUser,  $country, $version));
	die();
}

if (Tools::isSubmit('getHookableList'))
{
	$modules_list = explode(',', Tools::getValue('modules_list'));
	$hooks_list = explode(',', Tools::getValue('hooks_list'));
	$hookableList = array();
	
	foreach ($modules_list as $module)
	{
		$moduleInstance = Module::getInstanceByName($module);
		foreach($hooks_list as $hook_name)
		{
			if (!array_key_exists($hook_name, $hookableList))
				$hookableList[$hook_name] = array();
			if ($moduleInstance->isHookableOn($hook_name))
				array_push($hookableList[$hook_name], $module);
		}
			
	}
	die(Tools::jsonEncode($hookableList));
}

if (Tools::isSubmit('getHookableModuleList'))
{
	
	include('../init.php');
	$hook_name = Tools::getValue('hook');
	$hookableModulesList = array();
	$modules = Db::getInstance()->ExecuteS('SELECT id_module, name FROM `'._DB_PREFIX_.'module` ');
	foreach ($modules as $module)
	{
		if (file_exists(_PS_MODULE_DIR_.$module['name'].'/'.$module['name'].'.php'))
		{
			include_once(_PS_MODULE_DIR_.$module['name'].'/'.$module['name'].'.php');
			$mod = new $module['name']();
			if ($mod->isHookableOn($hook_name))
				$hookableModulesList[] = array('id' => (int)$mod->id, 'name' => $mod->displayName, 'display' => Module::hookExec($hook_name, array(), (int)$mod->id));
		}		
	}
	die(Tools::jsonEncode($hookableModulesList));			
}

if (Tools::isSubmit('saveHook'))
{
	$hooks_list = explode(',', Tools::getValue('hooks_list'));
	$hookableList = array();
	foreach ($hooks_list as $hook)
	{
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'hook_module` WHERE `id_hook` = (SELECT id_hook FROM `'._DB_PREFIX_.'hook` WHERE `name` = \''.pSQL($hook).'\' LIMIT 0, 1)');
		$hookedModules = explode(',', Tools::getValue($hook));
		$i = 1;
		$value = '';
		foreach($hookedModules as $module)
		{
			$ids = explode('_', $module);
			$value .= '('.$ids[1].', (SELECT id_hook FROM `'._DB_PREFIX_.'hook` WHERE `name` = \''.pSQL($hook).'\' LIMIT 0, 1), '.$i.'),';
			$i ++;
		}
		$value = rtrim($value, ',');
		Db::getInstance()->Execute('INSERT INTO  `'._DB_PREFIX_.'hook_module` (`id_module`, `id_hook`, `position`) VALUES '.$value);
			
	}
	die('{"hasError" : false, "errors" : ""}');
}

if (Tools::isSubmit('getAdminHomeElement'))
{
	$result = array();
	
	// PREACTIVATION
	$protocol = (!empty($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) != 'off') ? 'https' : 'http';
	$isoUser = Language::getIsoById(intval($cookie->id_lang));
	$isoCountry = Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'));
	
	$context = stream_context_create(array('http' => array('method'=>"GET", 'timeout' => 5)));
	$content = @file_get_contents('https://www.prestashop.com/partner/preactivation/preactivation-block.php?version=1.0&shop='.urlencode(Configuration::get('PS_SHOP_NAME')).'&protocol='.$protocol.'&url='.urlencode($_SERVER['HTTP_HOST']).'&iso_country='.$isoCountry.'&iso_lang='.Tools::strtolower($isoUser).'&id_lang='.(int)$cookie->id_lang.'&email='.urlencode(Configuration::get('PS_SHOP_EMAIL')).'&date_creation='._PS_CREATION_DATE_.'&v='._PS_VERSION_.'&security='.md5(Configuration::get('PS_SHOP_EMAIL')._COOKIE_IV_), false, $context);
	if (!$content)
		die('NOK');
	$content = explode('|', $content);
	if ($content[0] == 'OK')
	{
		$result['partner_preactivation'] = $content[2];
		$content[1] = explode('#%#', $content[1]);
		foreach ($content[1] as $partnerPopUp)
			if ($partnerPopUp)
			{
				$partnerPopUp = explode('%%', $partnerPopUp);
				if (!Configuration::get('PS_PREACTIVATION_'.strtoupper($partnerPopUp[0])))
				{
					$result['partner_preactivation'] .= $partnerPopUp[1];
					Configuration::updateValue('PS_PREACTIVATION_'.strtoupper($partnerPopUp[0]), 'TRUE');
				}
			}
	}
	else
		$result['partner_preactivation'] = '';
	
	// DISCOVER PRESTASHOP
	$content = @file_get_contents('https://www.prestashop.com/partner/prestashop/prestashop-link.php?iso_country='.$isoCountry.'&iso_lang='.Tools::strtolower($isoUser).'&id_lang='.(int)$cookie->id_lang, false, $context);
	$content = explode('|', $content);
	if ($content[0] == 'OK')
		$result['discover_prestashop'] = $content[1];
	else
		$result['discover_prestashop'] = '';

	if (@fsockopen('www.prestashop.com', 80, $errno, $errst, 3))
		$result['discover_prestashop'] .= '<iframe frameborder="no" style="margin: 0px; padding: 0px; width: 315px; height: 290px;" src="'.$protocol.'://www.prestashop.com/rss/news2.php?v='._PS_VERSION_.'&lang='.$isoUser.'"></iframe>';

	$content = @file_get_contents('https://www.prestashop.com/partner/paypal/paypal-tips.php?protocol='.$protocol.'&iso_country='.$isoCountry.'&iso_lang='.Tools::strtolower($isoUser).'&id_lang='.(int)$cookie->id_lang, false, $context);
	$content = explode('|', $content);
	if ($content[0] == 'OK')
		$result['discover_prestashop'] .= $content[1];
	
	
	die(Tools::jsonEncode($result));
}
