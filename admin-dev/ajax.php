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

@trigger_error('Using '.__FILE__.' to make an ajax call is deprecated. Use a controller instead.', E_USER_DEPRECATED);

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
 * Return the list of a pack of products
 * Not found
 *
 * -> Moved in legacy
 */
if (Tools::isSubmit('ajaxProductPackItems')) {
    $_GET['ajax'] = 1;
    $_GET['controller'] = 'AdminProducts';
    $_GET['action'] = 'productPackItems';
}

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
 * Search for a category
 *
 * -> moved in legacy
 */
if (Tools::isSubmit('searchCategory')) {
    $_GET['ajax'] = 1;
    $_GET['controller'] = 'AdminCategories';
    $_GET['action'] = 'searchCategory';
}

/**
 * Get all parents of a given category
 *
 * -> moved in legacy
 */
if (Tools::isSubmit('getParentCategoriesId') && Tools::isSubmit('id_category')) {
    $_GET['ajax'] = 1;
    $_GET['controller'] = 'AdminCategories';
    $_GET['action'] = 'parentCategories';
}

/**
 * Get all zones stored on the shop
 * Json content with an html attribute in it.
 *
 * -> Moved in legacy
 */
if (Tools::isSubmit('getZones')) {
    $_GET['ajax'] = 1;
    $_GET['controller'] = 'AdminZones';
    $_GET['action'] = 'zones';
}

if (Tools::isSubmit('getEmailHTML') && Tools::isSubmit('email')) {
    $_GET['ajax'] = 1;
    $_GET['controller'] = 'AdminTranslations';
    $_GET['action'] = 'emailHTML';
}

if (1 === $_GET['ajax']) {
    require_once dirname(__FILE__).'/index.php';
}

/**
 * From this line, the code could not be moved outside this file. It still requires the core to work.
 */

if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', getcwd());
}

if (!defined('PS_ADMIN_DIR')) {
    define('PS_ADMIN_DIR', _PS_ADMIN_DIR_);
}

require(_PS_ADMIN_DIR_.'/../config/config.inc.php');

$context = Context::getContext();

// Not used anymore, but kept just in case
if (Tools::getValue('page') == 'prestastore' && @fsockopen('addons.prestashop.com', 80, $errno, $errst, 3)) {
    readfile('https://addons.prestashop.com/adminmodules.php?lang='.$context->language->iso_code);
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

/**
 * Updates the last time a notification has been seen
 *
 * -> Duplicated (NOT MOVED) in Symfony
 */
if (Tools::isSubmit('updateElementEmployee') && Tools::getValue('updateElementEmployeeType')) {
    $notification = new Notification;
    die($notification->updateEmployeeLastElement(Tools::getValue('updateElementEmployeeType')));
}
