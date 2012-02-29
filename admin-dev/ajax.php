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
*  @version  Release: $Revision: 7234 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

define('_PS_ADMIN_DIR_', getcwd());
include(_PS_ADMIN_DIR_.'/../config/config.inc.php');
/* Getting cookie or logout */
require_once(dirname(__FILE__).'/init.php');

$context = Context::getContext();

if (Tools::isSubmit('changeParentUrl'))
	echo '<script type="text/javascript">parent.parent.document.location.href = "'.addslashes(urldecode(Tools::getValue('changeParentUrl'))).'";</script>';
if (Tools::isSubmit('installBoughtModule'))
{
	$file = false;
	while ($file === false OR file_exists(_PS_MODULE_DIR_.$file))
		$file = uniqid();
	$file = _PS_MODULE_DIR_.$file.'.zip';
	$sourceFile = 'http://addons.prestashop.com/iframe/getboughtfile.php?id_order_detail='.Tools::getValue('id_order_detail').'&token='.Tools::getValue('token');
	if (!copy($sourceFile, $file))
	{
		if (!($content = file_get_contents($sourceFile)))
			die(displayJavascriptAlert('Access denied: Please download your module directly from PrestaShop Addons website'));
		elseif (!file_put_contents($file, $content))
			die(displayJavascriptAlert('Local error: your module directory is not writable'));
	}
	$first6 = fread($fd = fopen($file, 'r'), 6);
	if (!strncmp($first6, 'Error:', 6))
	{
		$displayJavascriptAlert = displayJavascriptAlert(fread($fd, 1024));
		fclose($fd);
		unlink($file);
		die($displayJavascriptAlert);
	}
	fclose($fd);
	if (!Tools::ZipExtract($file, _PS_MODULE_DIR_))
	{
		unlink($file);
		die(displayJavascriptAlert('Cannot unzip file'));
	}
	unlink($file);
	die(displayJavascriptAlert('Module copied to disk'));
}

function displayJavascriptAlert($s)
{
	echo '<script type="text/javascript">alert(\''.addslashes($s).'\');</script>';
}

if (Tools::isSubmit('ajaxReferrers'))
{
	require(_PS_CONTROLLER_DIR_.'admin/AdminReferrersController.php');
}

if (Tools::getValue('page') == 'prestastore' AND @fsockopen('addons.prestashop.com', 80, $errno, $errst, 3))
	readfile('http://addons.prestashop.com/adminmodules.php?lang='.$context->language->iso_code);

if (Tools::isSubmit('getAvailableFields') AND Tools::isSubmit('entity'))
{
	$jsonArray = array();
	$import = new AdminImportController();

	$languages = Language::getLanguages(false);
	$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
	$fields = $import->getAvailableFields(true);
	foreach ($fields AS $field)
		$jsonArray[] = '{"field":"'.addslashes($field).'"}';
	die('['.implode(',', $jsonArray).']');
}

if (Tools::isSubmit('ajaxModulesPositions'))
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

if (Tools::isSubmit('ajaxCategoriesPositions'))
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
			Hook::exec('actionCategoryUpdate');
			die(true);
		}
		else
			die('{"hasError" : true, errors : "Can not update categories position"}');
	}
	else
		die('{"hasError" : true, "errors" : "This category can not be loaded"}');

}

if (Tools::isSubmit('ajaxCMSCategoriesPositions'))
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

if (Tools::isSubmit('ajaxCMSPositions'))
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
if (Tools::isSubmit('ajaxProductsPositions'))
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
						echo "ok position ".(int)$position." for product ".(int)$pos[2]."\r\n";
					else
						echo '{"hasError" : true, "errors" : "Can not update product '. (int)$id_product . ' to position '.(int)$position.' "}';
				else
					echo '{"hasError" : true, "errors" : "This product ('.(int)$id_product.') can t be loaded"}';

				break;
			}
		}
}

if (Tools::isSubmit('ajaxProductImagesPositions'))
{
	$id_image = (int)(Tools::getValue('id_image'));
	$way = (int)(Tools::getValue('way'));
	$positions = Tools::getValue('imageTable');

	if (is_array($positions))
		foreach ($positions AS $key => $value)
		{
			$pos = explode('_', $value);
			if ((isset($pos[1])) AND ($pos[1] == $id_image))
			{
				// +1 is added because images position range starts from 1 instead of 0 for other objects (products, categories...)
				$position = ($key + 1);
				break;
			}
		}
	$image = new Image($id_image);
	if (Validate::isLoadedObject($image))
	{
		if (isset($position) && $image->updatePosition($way, $position))
			die(true);
		else
			die('{"hasError" : true, "errors" : "Cannot update image position"}');
	}
	else
		die('{"hasError" : true, "errors" : "This image cannot be loaded"}');
}


if (Tools::isSubmit('ajaxProductPackItems'))
{
	$jsonArray = array();
	$products = Db::getInstance()->executeS('
	SELECT p.`id_product`, pl.`name`
	FROM `'._DB_PREFIX_.'product` p
	NATURAL LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
	WHERE pl.`id_lang` = '.(int)(Tools::getValue('id_lang')).'
	'.Shop::addSqlRestrictionOnLang('pl').'
	AND p.`id_product` NOT IN (SELECT DISTINCT id_product_pack FROM `'._DB_PREFIX_.'pack`)
	AND p.`id_product` != '.(int)(Tools::getValue('id_product')));

	foreach ($products AS $packItem)
		$jsonArray[] = '{"value": "'.(int)($packItem['id_product']).'-'.addslashes($packItem['name']).'", "text":"'.(int)($packItem['id_product']).' - '.addslashes($packItem['name']).'"}';
	die('['.implode(',', $jsonArray).']');
}

if (Tools::isSubmit('ajaxStates') AND Tools::isSubmit('id_country'))
{
	$states = Db::getInstance()->executeS('
	SELECT s.id_state, s.name
	FROM '._DB_PREFIX_.'state s
	LEFT JOIN '._DB_PREFIX_.'country c ON (s.`id_country` = c.`id_country`)
	WHERE s.id_country = '.(int)(Tools::getValue('id_country')).' AND s.active = 1 AND c.`contains_states` = 1
	ORDER BY s.`name` ASC');

	if (is_array($states) AND !empty($states))
	{
		$list = '';
		if (Tools::getValue('no_empty') != true)
		{
			$empty_value = (Tools::isSubmit('empty_value')) ? Tools::getValue('empty_value') : '----------';
			$list = '<option value="0">'.Tools::htmlentitiesUTF8($empty_value).'</option>'."\n";
		}

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
	if (!Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'customer SET `note` = "'.pSQL($note, true).'" WHERE id_customer = '.(int)$id_customer.' LIMIT 1'))
		die ('error:update');
	die('ok');
}

if (Tools::getValue('form_language_id'))
{
	if (!($context->cookie->employee_form_lang = (int)(Tools::getValue('form_language_id'))))
		die ('Error while updating cookie.');
	die ('Form language updated.');
}

if (Tools::getValue('submitPublishProduct'))
{
	if (Tools::getIsset('id_product'))
	{
		$id_product = (int)(Tools::getValue('id_product'));
		$id_tab_catalog = (int)(Tab::getIdFromClassName('AdminProducts'));
		$token = Tools::getAdminToken('AdminProducts'.(int)($id_tab_catalog).(int)$context->employee->id);
		$bo_product_url = dirname($_SERVER['PHP_SELF']).'/index.php?tab=AdminProducts&id_product='.$id_product.'&updateproduct&token='.$token;

		if (Tools::getValue('redirect'))
			die($bo_product_url);

		$profileAccess = Profile::getProfileAccess($context->employee->id_profile, $id_tab_catalog);
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
	if (Tools::getIsset('id_cms'))
	{
		$id_cms = (int)(Tools::getValue('id_cms'));
		$id_tab_cms = (int)(Tab::getIdFromClassName('AdminCmsContent'));
		$token = Tools::getAdminToken('AdminCmsContent'.(int)($id_tab_cms).(int)$context->employee->id);
		$bo_cms_url = dirname($_SERVER['PHP_SELF']).'/index.php?tab=AdminCmsContent&id_cms='.(int)$id_cms.'&updatecms&token='.$token;

		if (Tools::getValue('redirect'))
			die($bo_cms_url);

		$profileAccess = Profile::getProfileAccess($context->employee->id_profile, $id_tab_cms);
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
   Db::getInstance()->execute('INSERT INTO  `'._DB_PREFIX_.'import_match` (
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
   Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'import_match` WHERE `id_import_match` = '.(int)Tools::getValue('idImportMatchs'));
}

if (Tools::isSubmit('loadImportMatchs'))
{
   $return = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'import_match` WHERE `id_import_match` = '.(int)Tools::getValue('idImportMatchs'));
   die('{"id" : "'.$return[0]['id_import_match'].'", "matchs" : "'.$return[0]['match'].'", "skip" : "'.$return[0]['skip'].'"}');
}

if (Tools::isSubmit('toggleScreencast'))
{
	if (Validate::isLoadedObject($context->employee))
	{
		$context->employee->bo_show_screencast = !$context->employee->bo_show_screencast;
		$context->employee->update();
	}
}

if (Tools::isSubmit('getHookableList'))
{
	/* PrestaShop demo mode */
	if (_PS_MODE_DEMO_)
		die('{"hasError" : true, "errors" : ["Live Edit : This functionnality has been disabled"]}');
	/* PrestaShop demo mode*/

	if (!count(Tools::getValue('hooks_list')))
		die('{"hasError" : true, "errors" : ["Live Edit : no module on this page"]}');

	$modules_list = Tools::getValue('modules_list');
	$hooks_list = Tools::getValue('hooks_list');
	$hookableList = array();

	foreach ($modules_list as $module)
	{
		$module = trim($module);
		if (!$module)
			continue;

		$moduleInstance = Module::getInstanceByName($module);
		foreach ($hooks_list as $hook_name)
		{
			$hook_name = trim($hook_name);
			if (!$hook_name)
				continue;
			if (!array_key_exists($hook_name, $hookableList))
				$hookableList[$hook_name] = array();
			if ($moduleInstance->isHookableOn($hook_name))
				array_push($hookableList[$hook_name], $module);
		}

	}
	$hookableList['hasError'] = false;
	die(Tools::jsonEncode($hookableList));
}

if (Tools::isSubmit('getHookableModuleList'))
{
	/* PrestaShop demo mode */
	if (_PS_MODE_DEMO_)
		die('{"hasError" : true, "errors" : ["Live Edit : This functionnality has been disabled"]}');
	/* PrestaShop demo mode*/

	include('../init.php');
	$hook_name = Tools::getValue('hook');
	$hookableModulesList = array();
	$modules = Db::getInstance()->executeS('SELECT id_module, name FROM `'._DB_PREFIX_.'module` ');
	foreach ($modules as $module)
	{
		if (file_exists(_PS_MODULE_DIR_.$module['name'].'/'.$module['name'].'.php'))
		{
			include_once(_PS_MODULE_DIR_.$module['name'].'/'.$module['name'].'.php');
			$mod = new $module['name']();
			if ($mod->isHookableOn($hook_name))
				$hookableModulesList[] = array('id' => (int)$mod->id, 'name' => $mod->displayName, 'display' => Hook::exec($hook_name, array(), (int)$mod->id));
		}
	}
	die(Tools::jsonEncode($hookableModulesList));
}

if (Tools::isSubmit('saveHook'))
{
	/* PrestaShop demo mode */
	if (_PS_MODE_DEMO_)
		die('{"hasError" : true, "errors" : ["Live Edit : This functionnality has been disabled"]}');

	$hooks_list = explode(',', Tools::getValue('hooks_list'));
	$id_shop = (int)Tools::getValue('id_shop');
	if (!$id_shop)
		$id_shop = Context::getContext()->shop->id;

	$res = true;
	$hookableList = array();
	// $_POST['hook'] is an array of id_module
	$hooks_list = Tools::getValue('hook');
	foreach ($hooks_list as $id_hook => $modules)
	{
		// 1st, drop all previous hooked modules
		$sql = 'DELETE FROM `'._DB_PREFIX_.'hook_module`
			WHERE `id_hook` =  '.(int)$id_hook.'
			AND id_shop = '.$id_shop;
		$res &= Db::getInstance()->execute($sql);

		$i = 1;
		$value = '';
		$ids = array();
		// then prepare sql query to rehook all chosen modules(id_module, id_shop, id_hook, position)
		// position is i (autoincremented)
		foreach ($modules as $id_module)
		{
			if (!in_array($id_module, $ids))
			{
				$ids[] = $id_module;
				$value .= '('.(int)$id_module.', '.$id_shop.', '.(int)$id_hook.', '.$i.'),';
			}
			$i++;
		}
		$value = rtrim($value, ',');
		$res &= Db::getInstance()->execute('INSERT INTO  `'._DB_PREFIX_.'hook_module`
			(id_module, id_shop, id_hook, position)
			VALUES '.$value);

	}
	if ($res)
		$hasError = true;
	else
		$hasError = false;
	die('{"hasError" : false, "errors" : ""}');
}

if (Tools::isSubmit('getAdminHomeElement'))
{
	$result = array();

	$protocol = Tools::usingSecureMode() ? 'https' : 'http';
	$isoUser = Context::getContext()->language->iso_code;
	$isoCountry = Context::getContext()->country->iso_code;
	$stream_context = @stream_context_create(array('http' => array('method'=> 'GET', 'timeout' => 5)));

	// SCREENCAST
	if (@fsockopen('api.prestashop.com', 80, $errno, $errst, 3))
		$result['screencast'] = 'OK';
	else
		$result['screencast'] = 'NOK';

	// PREACTIVATION
	$content = @file_get_contents($protocol.'://api.prestashop.com/partner/preactivation/preactivation-block.php?version=1.0&shop='.urlencode(Configuration::get('PS_SHOP_NAME')).'&protocol='.$protocol.'&url='.urlencode($_SERVER['HTTP_HOST']).'&iso_country='.$isoCountry.'&iso_lang='.Tools::strtolower($isoUser).'&id_lang='.(int)Context::getContext()->language->id.'&email='.urlencode(Configuration::get('PS_SHOP_EMAIL')).'&date_creation='._PS_CREATION_DATE_.'&v='._PS_VERSION_.'&security='.md5(Configuration::get('PS_SHOP_EMAIL')._COOKIE_IV_), false, $stream_context);
	if (!$content)
		$result['partner_preactivation'] = 'NOK';
	else
	{
		$content = explode('|', $content);
		if ($content[0] == 'OK' && Validate::isCleanHtml($content[2]) && Validate::isCleanHtml($content[1]))
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
			$result['partner_preactivation'] = 'NOK';
	}

	// PREACTIVATION PAYPAL WARNING
	$content = @file_get_contents('https://api.prestashop.com/partner/preactivation/preactivation-warnings.php?version=1.0&partner=paypal&iso_country='.Tools::strtolower(Context::getContext()->country->iso_code).'&iso_lang='.Tools::strtolower(Context::getContext()->language->iso_code).'&id_lang='.(int)Context::getContext()->language->id.'&email='.urlencode(Configuration::get('PS_SHOP_EMAIL')).'&security='.md5(Configuration::get('PS_SHOP_EMAIL')._COOKIE_IV_), false, $stream_context);
	$content = explode('|', $content);
	if ($content[0] == 'OK' && Validate::isCleanHtml($content[1]))
		Configuration::updateValue('PS_PREACTIVATION_PAYPAL_WARNING', $content[1]);
	else
		Configuration::updateValue('PS_PREACTIVATION_PAYPAL_WARNING', '');

	// DISCOVER PRESTASHOP
	$content = @file_get_contents($protocol.'://api.prestashop.com/partner/prestashop/prestashop-link.php?iso_country='.$isoCountry.'&iso_lang='.Tools::strtolower($isoUser).'&id_lang='.(int)Context::getContext()->language->id, false, $stream_context);
	if (!$content)
		$result['discover_prestashop'] = 'NOK';
	else
	{
		$content = explode('|', $content);
		if ($content[0] == 'OK' && Validate::isCleanHtml($content[1]))
			$result['discover_prestashop'] = $content[1];
		else
			$result['discover_prestashop'] = 'NOK';

		if (@fsockopen('api.prestashop.com', 80, $errno, $errst, 3))
			$result['discover_prestashop'] .= '<iframe frameborder="no" style="margin: 0px; padding: 0px; width: 315px; height: 290px;" src="'.$protocol.'://api.prestashop.com/rss/news2.php?v='._PS_VERSION_.'&lang='.$isoUser.'"></iframe>';

		$content = @file_get_contents($protocol.'://api.prestashop.com/partner/paypal/paypal-tips.php?protocol='.$protocol.'&iso_country='.$isoCountry.'&iso_lang='.Tools::strtolower($isoUser).'&id_lang='.(int)Context::getContext()->language->id, false, $stream_context);
		$content = explode('|', $content);
		if ($content[0] == 'OK' && Validate::isCleanHtml($content[1]))
			$result['discover_prestashop'] .= $content[1];
	}

	die(Tools::jsonEncode($result));
}

if (Tools::isSubmit('getChildrenCategories') && Tools::isSubmit('id_category_parent'))
{
	$children_categories = Category::getChildrenWithNbSelectedSubCat(Tools::getValue('id_category_parent'), Tools::getValue('selectedCat'), Context::getContext()->language->id, null, Tools::getValue('use_shop_context'));
	die(Tools::jsonEncode($children_categories));
}

if (Tools::isSubmit('getNotifications'))
{
	$notification = new Notification;
	die(Tools::jsonEncode($notification->getLastElements()));
}

if (Tools::isSubmit('updateElementEmployee') && Tools::getValue('updateElementEmployeeType'))
{
	$notification = new Notification;
	die($notification->updateEmployeeLastElement(Tools::getValue('updateElementEmployeeType')));
}

if (Tools::isSubmit('syncImapMail'))
{
	if (!($url = Configuration::get('PS_SAV_IMAP_URL'))
	|| !($port = Configuration::get('PS_SAV_IMAP_PORT'))
	|| !($user = Configuration::get('PS_SAV_IMAP_USER'))
	|| !($password = Configuration::get('PS_SAV_IMAP_PWD')))
	die('{"hasError" : true, "errors" : ["Configuration is not correct"]}');

	$conf = Configuration::getMultiple(array(
		'PS_SAV_IMAP_OPT_NORSH', 'PS_SAV_IMAP_OPT_SSL',
		'PS_SAV_IMAP_OPT_VALIDATE-CERT', 'PS_SAV_IMAP_OPT_NOVALIDATE-CERT',
		'PS_SAV_IMAP_OPT_TLS', 'PS_SAV_IMAP_OPT_NOTLS'));
	
	$conf_str = '';
	if ($conf['PS_SAV_IMAP_OPT_NORSH'])
		$conf_str .= '/norsh';
	if ($conf['PS_SAV_IMAP_OPT_SSL'])
		$conf_str .= '/ssl';
	if ($conf['PS_SAV_IMAP_OPT_VALIDATE-CERT'])
		$conf_str .= '/validate-cert';
	if ($conf['PS_SAV_IMAP_OPT_NOVALIDATE-CERT'])
		$conf_str .= '/novalidate-cert';
	if ($conf['PS_SAV_IMAP_OPT_TLS'])
		$conf_str .= '/tls';
	if ($conf['PS_SAV_IMAP_OPT_NOTLS'])
		$conf_str .= '/notls';

	if (!function_exists('imap_open'))
		die('{"hasError" : true, "errors" : ["imap is not installed on this server"]}');

	$mbox = @imap_open('{'.$url.':'.$port.$conf_str.'}', $user, $password);

	//checks if there is no error when connecting imap server
	$errors = imap_errors();
	$str_errors = '';
	$str_error_delete = '';
	if (sizeof($errors) && is_array($errors))
	{
		var_dump($errors);
		$str_errors = '';
		foreach($errors as $error)
			$str_errors .= '"'.$error.'",';
		$str_errors = rtrim($str_errors, ',').'';
	}
	//checks if imap connexion is active
	if (!$mbox)
		die('{"hasError" : true, "errors" : ["Cannot connect to the mailbox"]}');

	//Returns information about the current mailbox. Returns FALSE on failure.
	$check = imap_check($mbox);
	if (!$check)
		die('{"hasError" : true, "errors" : ["Fail to get information about the current mailbox"]}');

	if ($check->Nmsgs == 0)
		die('{"hasError" : true, "errors" : ["NO message to sync"]}');

	$result = imap_fetch_overview($mbox,"1:{$check->Nmsgs}",0);
	foreach ($result as $overview)
	{
	    //check if message exist in database
	    if (isset($overview->subject))
	   		$subject = $overview->subject;
	   	else
	   		$subject = '';

		//Creating an md5 to check if message has been allready processed
	    $md5 = md5($overview->date.$overview->from.$subject.$overview->msgno);
	    $exist = Db::getInstance()->getValue(
			    'SELECT `md5_header`
			    FROM `'._DB_PREFIX_.'customer_message_sync_imap`
			    WHERE `md5_header` = \''.pSQL($md5).'\'');
	    if ($exist)
	    {
			if (Configuration::get('PS_SAV_IMAP_DELETE_MSG'))
				if (!imap_delete($mbox, $overview->msgno))
					$str_error_delete = ', "Fail to delete message"';
	    }
	    else
	    {
	    	//check if subject has id_order
	    	preg_match('/\#ct([0-9]*)/', $subject, $matches1);
	    	preg_match('/\#tc([0-9-a-z-A-Z]*)/', $subject, $matches2);

			if (isset($matches1[1]) && isset($matches2[1]))
			{
				//check if order exist in database
				$ct = new CustomerThread((int)$matches1[1]);

				if (Validate::isLoadedObject($ct) && $ct->token == $matches2[1])
				{
					$cm = new CustomerMessage();
					$cm->id_customer_thread = $ct->id;
					$cm->message = imap_fetchbody($mbox, $overview->msgno, 1);
					$cm->add();
				}
			}
			Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'customer_message_sync_imap` (`md5_header`) VALUES (\''.pSQL($md5).'\')');
	    }
	}
	imap_expunge($mbox);
	imap_close($mbox);
	die('{"hasError" : false, "errors" : ["'.$str_errors.$str_error_delete.'"]}');
}

/* Modify attribute position */
if (Tools::isSubmit('ajaxAttributesPositions'))
{
	$way = (int)Tools::getValue('way');
	$id_attribute = (int)Tools::getValue('id_attribute');
	$id_attribute_group = (int)Tools::getValue('id_attribute_group');
	$positions = Tools::getValue('attribute');

	if (is_array($positions))
		foreach ($positions as $position => $value)
		{
			// pos[1] = id_attribute_group, pos[2] = id_attribute, pos[3]=old position
			$pos = explode('_', $value);

			if ((isset($pos[1]) && isset($pos[2])) && ($pos[1] == $id_attribute_group && (int)$pos[2] === $id_attribute))
			{
				if ($attribute = new Attribute((int)$pos[2]))
					if (isset($position) && $attribute->updatePosition($way, $position))
						echo "ok position ".(int)$position." for attribute ".(int)$pos[2]."\r\n";
					else
						echo '{"hasError" : true, "errors" : "Can not update attribute '. (int)$id_attribute . ' to position '.(int)$position.' "}';
				else
					echo '{"hasError" : true, "errors" : "This attribute ('.(int)$id_attribute.') can t be loaded"}';

				break;
			}
		}
}

/* Modify group attribute position */
if (Tools::isSubmit('ajaxGroupsAttributesPositions'))
{
	$way = (int)Tools::getValue('way');
	$id_attribute_group = (int)Tools::getValue('id_attribute_group');
	$positions = Tools::getValue('attribute_group');

	$new_positions = array();
	foreach($positions as $k => $v)
		if (count(explode('_', $v)) == 3)
			$new_positions[] = $v;

	foreach ($new_positions as $position => $value)
	{
		// pos[1] = id_attribute_group, pos[2] = old position
		$pos = explode('_', $value);

		if (isset($pos[1]) && (int)$pos[1] === $id_attribute_group)
		{
			if ($group_attribute = new AttributeGroup((int)$pos[1]))
				if (isset($position) && $group_attribute->updatePosition($way, $position))
					echo "ok position ".(int)$position." for group attribute ".(int)$pos[1]."\r\n";
				else
					echo '{"hasError" : true, "errors" : "Can not update group attribute '. (int)$id_attribute_group . ' to position '.(int)$position.' "}';
			else
				echo '{"hasError" : true, "errors" : "This group attribute ('.(int)$id_attribute_group.') can t be loaded"}';

			break;
		}
	}
}

/* Modify feature position */
if (Tools::isSubmit('ajaxFeaturesPositions'))
{
	$way = (int)Tools::getValue('way');
	$id_feature = (int)Tools::getValue('id_feature');
	$positions = Tools::getValue('feature');

	$new_positions = array();
	foreach($positions as $k => $v)
		if (!empty($v))
			$new_positions[] = $v;

	foreach ($new_positions as $position => $value)
	{
		// pos[2] = id_feature, pos[3] = old position
		$pos = explode('_', $value);

		if (isset($pos[2]) && (int)$pos[2] === $id_feature)
		{
			if ($feature = new Feature((int)$pos[2]))
				if (isset($position) && $feature->updatePosition($way, $position))
					echo "ok position ".(int)$position." for feature ".(int)$pos[1]."\r\n";
				else
					echo '{"hasError" : true, "errors" : "Can not update feature '. (int)$id_feature . ' to position '.(int)$position.' "}';
			else
				echo '{"hasError" : true, "errors" : "This feature ('.(int)$id_feature.') can t be loaded"}';

			break;
		}
	}
}

/* Modify carrier position */
if (Tools::isSubmit('ajaxCarriersPositions'))
{
	$way = (int)(Tools::getValue('way'));
	$id_carrier = (int)(Tools::getValue('id_carrier'));
	$positions = Tools::getValue('carrier');


	foreach ($positions as $position => $value)
	{
		$pos = explode('_', $value);

		if (isset($pos[2]) && (int)$pos[2] === $id_carrier)
		{
			if ($carrier = new Carrier((int)$pos[2]))
				if (isset($position) && $carrier->updatePosition($way, $position))
					echo "ok position ".(int)$position." for carrier ".(int)$pos[1]."\r\n";
				else
					echo '{"hasError" : true, "errors" : "Can not update carrier '. (int)$id_carrier . ' to position '.(int)$position.' "}';
			else
				echo '{"hasError" : true, "errors" : "This carrier ('.(int)$id_carrier.') can t be loaded"}';

			break;
		}
	}
}

if (Tools::isSubmit('searchCategory'))
{
	$q = Tools::getValue('q');
	$limit = Tools::getValue('limit');
	$results = Db::getInstance()->executeS(
		'SELECT c.`id_category`, cl.`name`
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').')
		WHERE cl.`id_lang` = '.(int)$context->language->id.' AND c.`level_depth` <> 0
		AND cl.`name` LIKE \'%'.pSQL($q).'%\'
		GROUP BY c.id_category
		ORDER BY c.`position`
		LIMIT '.(int)$limit);
	if ($results)
	foreach ($results as $result)
		echo trim($result['name']).'|'.(int)$result['id_category']."\n";
}

if (Tools::isSubmit('getParentCategoriesId') && $id_category = Tools::getValue('id_category'))
{
	$category = new Category((int)$id_category);
	$results = Db::getInstance()->executeS('SELECT `id_category` FROM `'._DB_PREFIX_.'category` c WHERE c.`nleft` < '.(int)$category->nleft.' AND c.`nright` > '.(int)$category->nright.'');
	$output = array();
	foreach ($results as $result)
		$output[] = $result;

	die(Tools::jsonEncode($output));
}

/* Update attribute */
if (Tools::isSubmit('ajaxUpdateTaxRule'))
{
	$id_tax_rule = Tools::getValue('id_tax_rule');
	$tax_rules = new TaxRule((int)$id_tax_rule);
	$output = array();
	foreach ($tax_rules as $key => $result)
		$output[$key] = $result;
	die(Tools::jsonEncode($output));
}

if (Tools::isSubmit('getZones'))
{
	$zones = Zone::getZones();
	$html = '<select id="zone_to_affect" name="zone_to_affect">';
	foreach ($zones as $z)
	{
		$html .= '<option value="'.$z['id_zone'].'">'.$z['name'].'</option>';
	}
	$html .= '</select>';
	$array = array('hasError' => false, 'errors' => '', 'data' => $html);
	die(Tools::jsonEncode($html));
}

/* Modify carrier position */
if (Tools::isSubmit('ajaxTabsPositions'))
{
	$way = (int)(Tools::getValue('way'));
	$id_tab = (int)(Tools::getValue('id_tab'));
	$positions = Tools::getValue('tab');

	foreach ($positions as $position => $value)
	{
		$pos = explode('_', $value);

		if (isset($pos[2]) && (int)$pos[2] === $id_tab)
		{
			if ($tab = new Tab((int)$pos[2]))
				if (isset($position) && $tab->updatePosition($way, $position))
					echo "ok position ".(int)$position." for tab ".(int)$pos[1]."\r\n";
				else
					echo '{"hasError" : true, "errors" : "Can not update tab '. (int)$id_tab . ' to position '.(int)$position.' "}';
			else
				echo '{"hasError" : true, "errors" : "This tab ('.(int)$id_tab.') can t be loaded"}';

			break;
		}
	}
}
