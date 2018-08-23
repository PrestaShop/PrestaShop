<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * This file seems to be existing since a long time. Although ajax requests can be handled by our controller,
 * some methods were forgotten here.
 *
 * They've been moved to the proper controller, but we keep this file for compatibility purpose.
 * The content your see here is a proxy to the controller.
 */

use ToolsCore as Tools;

@trigger_error('Using ajax.php to make an ajax call is deprecated. Use a controller instead.', E_USER_DEPRECATED);

require dirname(__FILE__).'/../classes/Tools.php';

/**
 * Ajax calls to the controller AdminReferrers
 * -> Moved in legacy
 */
if (Tools::isSubmit('ajaxReferrers')) {
    $_GET['ajax'] = 1;
    $_GET['controller'] = 'AdminReferrers';
    if (Tools::isSubmit('ajaxProductFilter')) {
        $_GET['action'] = 'productFilter';
    }
    if (Tools::isSubmit('ajaxFillProducts')) {
        $_GET['action'] = 'fillProducts';
    }
}

// Not used anymore, but kept just in case
if (Tools::getValue('page') == 'prestastore' && @fsockopen('addons.prestashop.com', 80, $errno, $errst, 3)) {
    readfile('https://addons.prestashop.com/adminmodules.php?lang='.$context->language->iso_code);
}

/**
 * Import controller: Fields available for a given entity
 * -> Moved in legacy (although called from symfony, the import content is not migrated yet)
 */
if (Tools::isSubmit('getAvailableFields') && Tools::isSubmit('entity')) {
    $_GET['ajax'] = 1;
    $_GET['controller'] = 'AdminImport';
    $_GET['action'] = 'availableFields';
}

/**
 * Not found
 */
//if (Tools::isSubmit('ajaxProductPackItems')) {
//    $jsonArray = array();
//    $products = Db::getInstance()->executeS('
//	SELECT p.`id_product`, pl.`name`
//	FROM `'._DB_PREFIX_.'product` p
//	NATURAL LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
//	WHERE pl.`id_lang` = '.(int)(Tools::getValue('id_lang')).'
//	'.Shop::addSqlRestrictionOnLang('pl').'
//	AND NOT EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'pack` WHERE `id_product_pack` = p.`id_product`)
//	AND p.`id_product` != '.(int)(Tools::getValue('id_product')));
//
//    foreach ($products as $packItem) {
//        $jsonArray[] = '{"value": "'.(int)($packItem['id_product']).'-'.addslashes($packItem['name']).'", "text":"'.(int)($packItem['id_product']).' - '.addslashes($packItem['name']).'"}';
//    }
//    die('['.implode(',', $jsonArray).']');
//}

/**
 * Used to display children of a given category, but flagged as deprecated since 1.6.0.4
 * -> Moved in legacy, in AdminCategories
 */
if (Tools::isSubmit('getChildrenCategories') && Tools::isSubmit('id_category_parent')) {
    $_GET['ajax'] = 1;
    $_GET['controller'] = 'AdminCategories';
    $_GET['action'] = 'childrenCategories';
}

/**
 * List notifications for an employee
 * i.e: recent orders, new customers...
 *
 * -> Duplicated (NOT MOVED) in Symfony
 */
if (Tools::isSubmit('getNotifications')) {
    $notification = new Notification;
    die(json_encode($notification->getLastElements()));
}

//if (Tools::isSubmit('updateElementEmployee') && Tools::getValue('updateElementEmployeeType')) {
//    $notification = new Notification;
//    die($notification->updateEmployeeLastElement(Tools::getValue('updateElementEmployeeType')));
//}
//
//if (Tools::isSubmit('searchCategory')) {
//    $q = Tools::getValue('q');
//    $limit = Tools::getValue('limit');
//    $results = Db::getInstance()->executeS('SELECT c.`id_category`, cl.`name`
//		FROM `'._DB_PREFIX_.'category` c
//		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').')
//		WHERE cl.`id_lang` = '.(int)$context->language->id.' AND c.`level_depth` <> 0
//		AND cl.`name` LIKE \'%'.pSQL($q).'%\'
//		GROUP BY c.id_category
//		ORDER BY c.`position`
//		LIMIT '.(int)$limit
//    );
//    if ($results) {
//        foreach ($results as $result) {
//            echo trim($result['name']).'|'.(int)$result['id_category']."\n";
//        }
//    }
//}
//
//if (Tools::isSubmit('getParentCategoriesId') && $id_category = Tools::getValue('id_category')) {
//    $category = new Category((int)$id_category);
//    $results = Db::getInstance()->executeS('SELECT `id_category` FROM `'._DB_PREFIX_.'category` c WHERE c.`nleft` < '.(int)$category->nleft.' AND c.`nright` > '.(int)$category->nright.'');
//    $output = array();
//    foreach ($results as $result) {
//        $output[] = $result;
//    }
//
//    die(json_encode($output));
//}
//
//if (Tools::isSubmit('getZones')) {
//    $html = '<select id="zone_to_affect" name="zone_to_affect">';
//    foreach (Zone::getZones() as $z) {
//        $html .= '<option value="'.$z['id_zone'].'">'.$z['name'].'</option>';
//    }
//    $html .= '</select>';
//    $array = array('hasError' => false, 'errors' => '', 'data' => $html);
//    die(json_encode($array));
//}
//
//if (Tools::isSubmit('getEmailHTML') && $email = Tools::getValue('email')) {
//    $email_html = AdminTranslationsController::getEmailHTML($email);
//    die($email_html);
//}

require_once dirname(__FILE__).'/index.php';
