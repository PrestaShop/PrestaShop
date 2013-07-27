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

define('_PS_ADMIN_DIR_', getcwd());
include(_PS_ADMIN_DIR_.'/../config/config.inc.php');
/* Getting cookie or logout */
require_once(_PS_ADMIN_DIR_.'/init.php');

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

	$fields = $import->getAvailableFields(true);
	foreach ($fields AS $field)
		$jsonArray[] = '{"field":"'.addslashes($field).'"}';
	die('['.implode(',', $jsonArray).']');
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

if (Tools::getValue('form_language_id'))
{
	if (!($context->cookie->employee_form_lang = (int)(Tools::getValue('form_language_id'))))
		die ('Error while updating cookie.');
	die ('Form language updated.');
}

if (Tools::isSubmit('submitTrackClickOnHelp'))
{
    $label = Tools::getValue('label');
    $version = Tools::getValue('version');

    if (!empty($label) && !empty($version))
        HelpAccess::trackClick($label, $version);
}

if (Tools::isSubmit('toggleScreencast'))
{
	if (Validate::isLoadedObject($context->employee))
	{
		$context->employee->bo_show_screencast = !$context->employee->bo_show_screencast;
		$context->employee->update();
	}
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

function displayJavascriptAlert($s)
{
	echo '<script type="text/javascript">alert(\''.addslashes($s).'\');</script>';
}
