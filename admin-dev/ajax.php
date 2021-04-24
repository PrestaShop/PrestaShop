<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * This file seems to be existing since a long time. Although ajax requests can be handled by our controller,
 * some methods were forgotten here.
 *
 * They've been moved to the proper controller, but we keep this file for compatibility purpose.
 * The content your see here is a proxy to the controller.
 */

use ToolsCore as Tools;

@trigger_error('Using '.__FILE__.' to make an ajax call is deprecated since 1.7.6.0 and will be removed in the next major version. Use a controller instead.', E_USER_DEPRECATED);

require_once dirname(__FILE__) . '/../classes/Tools.php';

/**
 * Ajax calls to the controller AdminReferrers
 * -> Moved to legacy
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
 * Return the list of a pack of products
 * Not found
 *
 * -> Moved to legacy
 */
elseif (Tools::isSubmit('ajaxProductPackItems')) {
    $_GET['ajax'] = 1;
    $_GET['controller'] = 'AdminProducts';
    $_GET['action'] = 'productPackItems';
}

/**
 * Get all zones stored on the shop
 * Json content with an html attribute in it.
 *
 * -> Moved in legacy
 */
elseif (Tools::isSubmit('getZones')) {
    $_GET['ajax'] = 1;
    $_GET['controller'] = 'AdminZones';
    $_GET['action'] = 'zones';
}

elseif (Tools::isSubmit('getEmailHTML') && Tools::isSubmit('email')) {
    $_GET['ajax'] = 1;
    $_GET['controller'] = 'AdminTranslations';
    $_GET['action'] = 'emailHTML';
}

if (1 === Tools::getValue('ajax')) {
    require_once __DIR__ . '/index.php';
    return;
}

/**
 * From this line, the code could not be moved outside this file. It still requires the core to work.
 */

require_once __DIR__ . '/bootstrap.php';

$context = Context::getContext();

/**
 * Import controller: Fields available for a given entity
 * -> Duplicated in Symfony
 */
if (Tools::isSubmit('getAvailableFields') && Tools::isSubmit('entity')) {
    $import = new AdminImportController();

    $fields = array_map(function ($elem) {
        return ['field' => $elem];
    }, $import->getAvailableFields(true));
    echo json_encode($fields);
}

/**
 * List notifications for an employee
 * i.e: recent orders, new customers...
 *
 * -> Duplicated in Symfony
 */
elseif (Tools::isSubmit('getNotifications')) {
    $notification = new Notification();
    echo json_encode($notification->getLastElements());
}

/**
 * Updates the last time a notification has been seen
 *
 * -> Duplicated in Symfony
 */
elseif (Tools::isSubmit('updateElementEmployee') && Tools::getValue('updateElementEmployeeType')) {
    $notification = new Notification();
    echo $notification->updateEmployeeLastElement(Tools::getValue('updateElementEmployeeType'));
}

/**
 * Search for a category
 *
 * -> TODO in Symfony stack
 */
elseif (Tools::isSubmit('searchCategory')) {
    $q = Tools::getValue('q');
    $limit = Tools::getValue('limit');
    $results = Db::getInstance()->executeS('SELECT c.`id_category`, cl.`name`
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').')
		WHERE cl.`id_lang` = '.(int)$context->language->id.' AND c.`level_depth` <> 0
		AND cl.`name` LIKE \'%'.pSQL($q).'%\'
		GROUP BY c.id_category
		ORDER BY c.`position`
		LIMIT '.(int)$limit
    );
    if ($results) {
        foreach ($results as $result) {
            echo trim($result['name']).'|'.(int)$result['id_category']."\n";
        }
    }
}

/**
 * Used to display children of a given category, but flagged as deprecated since 1.6.0.4
 * Not moved / Not duplicated
 */
elseif (Tools::isSubmit('getChildrenCategories') && Tools::isSubmit('id_category_parent')) {
    $children_categories = Category::getChildrenWithNbSelectedSubCat(Tools::getValue('id_category_parent'), Tools::getValue('selectedCat'), Context::getContext()->language->id, null, Tools::getValue('use_shop_context'));
    echo json_encode($children_categories);
}

/**
 * Get all parents of a given category
 *
 * -> TODO in Symfony stack
 */
elseif (Tools::isSubmit('getParentCategoriesId') && $id_category = Tools::getValue('id_category')) {
    $category = new Category((int)$id_category);
    $results = Db::getInstance()->executeS('SELECT `id_category` FROM `'._DB_PREFIX_.'category` c WHERE c.`nleft` < '.(int)$category->nleft.' AND c.`nright` > '.(int)$category->nright.'');
    $output = array();
    foreach ($results as $result) {
        $output[] = $result;
    }

    echo json_encode($output);
}
