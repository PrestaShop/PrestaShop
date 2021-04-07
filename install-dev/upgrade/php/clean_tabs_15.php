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
function clean_tabs_15()
{
    include_once _PS_INSTALL_PATH_ . 'upgrade/php/migrate_tabs_15.php';

    $clean_tabs_15 = [
        9 => [
            'class_name' => 'AdminCatalog',
            'position' => 0,
            'active' => 1,
            'children' => [
                21 => ['class_name' => 'AdminProducts', 'position' => 0, 'active' => 1,
                ],
                22 => ['class_name' => 'AdminCategories', 'position' => 1, 'active' => 1,
                ],
                23 => ['class_name' => 'AdminTracking', 'position' => 2, 'active' => 1,
                ],
                24 => ['class_name' => 'AdminAttributesGroups', 'position' => 3, 'active' => 1,
                ],
                25 => ['class_name' => 'AdminFeatures', 'position' => 4, 'active' => 1,
                ],
                26 => ['class_name' => 'AdminManufacturers', 'position' => 5, 'active' => 1,
                ],
                27 => ['class_name' => 'AdminSuppliers', 'position' => 6, 'active' => 1,
                ],
                28 => ['class_name' => 'AdminScenes', 'position' => 7, 'active' => 1,
                ],
                29 => ['class_name' => 'AdminTags', 'position' => 8, 'active' => 1,
                ],
                30 => ['class_name' => 'AdminAttachments', 'position' => 9, 'active' => 1,
                ],
            ],
        ],
        10 => [
            'class_name' => 'AdminParentOrders',
            'position' => 1,
            'active' => 1,
            'children' => [
                31 => ['class_name' => 'AdminOrders', 'position' => 0, 'active' => 1,
                ],
                32 => ['class_name' => 'AdminInvoices', 'position' => 1, 'active' => 1,
                ],
                33 => ['class_name' => 'AdminReturn', 'position' => 2, 'active' => 1,
                ],
                34 => ['class_name' => 'AdminDeliverySlip', 'position' => 3, 'active' => 1,
                ],
                35 => ['class_name' => 'AdminSlip', 'position' => 4, 'active' => 1,
                ],
                36 => ['class_name' => 'AdminStatuses', 'position' => 5, 'active' => 1,
                ],
                37 => ['class_name' => 'AdminOrderMessage', 'position' => 6, 'active' => 1,
                ],
            ],
        ],
        11 => [
            'class_name' => 'AdminParentCustomer',
            'position' => 2,
            'active' => 1,
            'children' => [
                38 => ['class_name' => 'AdminCustomers', 'position' => 0, 'active' => 1,
                ],
                39 => ['class_name' => 'AdminAddresses', 'position' => 1, 'active' => 1,
                ],
                40 => ['class_name' => 'AdminGroups', 'position' => 2, 'active' => 1,
                ],
                41 => ['class_name' => 'AdminCarts', 'position' => 3, 'active' => 1,
                ],
                42 => ['class_name' => 'AdminCustomerThreads', 'position' => 4, 'active' => 1,
                ],
                43 => ['class_name' => 'AdminContacts', 'position' => 5, 'active' => 1,
                ],
                44 => ['class_name' => 'AdminGenders', 'position' => 6, 'active' => 1,
                ],
                45 => ['class_name' => 'AdminOutstanding', 'position' => 7, 'active' => 0,
                ],
            ],
        ],
        12 => [
            'class_name' => 'AdminPriceRule',
            'position' => 3,
            'active' => 1,
            'children' => [
                46 => ['class_name' => 'AdminCartRules', 'position' => 0, 'active' => 1,
                ],
                47 => ['class_name' => 'AdminSpecificPriceRule', 'position' => 1, 'active' => 1,
                ],
            ],
        ],
        13 => [
            'class_name' => 'AdminParentShipping',
            'position' => 4,
            'active' => 1,
            'children' => [
                48 => ['class_name' => 'AdminShipping', 'position' => 0, 'active' => 1,
                ],
                49 => ['class_name' => 'AdminCarriers', 'position' => 1, 'active' => 1,
                ],
                50 => ['class_name' => 'AdminRangePrice', 'position' => 2, 'active' => 1,
                ],
                51 => ['class_name' => 'AdminRangeWeight', 'position' => 3, 'active' => 1,
                ],
            ],
        ],
        14 => [
            'class_name' => 'AdminParentLocalization',
            'position' => 5,
            'active' => 1,
            'children' => [
                52 => ['class_name' => 'AdminLocalization', 'position' => 0, 'active' => 1,
                ],
                53 => ['class_name' => 'AdminLanguages', 'position' => 1, 'active' => 1,
                ],
                54 => ['class_name' => 'AdminZones', 'position' => 2, 'active' => 1,
                ],
                55 => ['class_name' => 'AdminCountries', 'position' => 3, 'active' => 1,
                ],
                56 => ['class_name' => 'AdminStates', 'position' => 4, 'active' => 1,
                ],
                57 => ['class_name' => 'AdminCurrencies', 'position' => 5, 'active' => 1,
                ],
                58 => ['class_name' => 'AdminTaxes', 'position' => 6, 'active' => 1,
                ],
                59 => ['class_name' => 'AdminTaxRulesGroup', 'position' => 7, 'active' => 1,
                ],
                60 => ['class_name' => 'AdminTranslations', 'position' => 8, 'active' => 1,
                ],
            ],
        ],
        15 => [
            'class_name' => 'AdminParentModules',
            'position' => 6,
            'active' => 1,
            'children' => [
                61 => ['class_name' => 'AdminModules', 'position' => 0, 'active' => 1,
                ],
                62 => ['class_name' => 'AdminAddonsCatalog', 'position' => 1, 'active' => 1,
                ],
                63 => ['class_name' => 'AdminModulesPositions', 'position' => 2, 'active' => 1,
                ],
                64 => ['class_name' => 'AdminPayment', 'position' => 3, 'active' => 1,
                ],
            ],
        ],
        16 => [
            'class_name' => 'AdminParentPreferences',
            'position' => 7,
            'active' => 1,
            'children' => [
                65 => ['class_name' => 'AdminPreferences', 'position' => 0, 'active' => 1,
                ],
                66 => ['class_name' => 'AdminOrderPreferences', 'position' => 1, 'active' => 1,
                ],
                67 => ['class_name' => 'AdminPPreferences', 'position' => 2, 'active' => 1,
                ],
                68 => ['class_name' => 'AdminCustomerPreferences', 'position' => 3, 'active' => 1,
                ],
                69 => ['class_name' => 'AdminThemes', 'position' => 4, 'active' => 1,
                ],
                70 => ['class_name' => 'AdminMeta', 'position' => 5, 'active' => 1,
                ],
                71 => ['class_name' => 'AdminCmsContent', 'position' => 6, 'active' => 1,
                ],
                72 => ['class_name' => 'AdminImages', 'position' => 7, 'active' => 1,
                ],
                73 => ['class_name' => 'AdminStores', 'position' => 8, 'active' => 1,
                ],
                74 => ['class_name' => 'AdminSearchConf', 'position' => 9, 'active' => 1,
                ],
                75 => ['class_name' => 'AdminMaintenance', 'position' => 10, 'active' => 1,
                ],
                76 => ['class_name' => 'AdminGeolocation', 'position' => 11, 'active' => 1,
                ],
            ],
        ],
        17 => [
            'class_name' => 'AdminTools',
            'position' => 8,
            'active' => 1,
            'children' => [
                77 => ['class_name' => 'AdminInformation', 'position' => 0, 'active' => 1,
                ],
                78 => ['class_name' => 'AdminPerformance', 'position' => 1, 'active' => 1,
                ],
                79 => ['class_name' => 'AdminEmails', 'position' => 2, 'active' => 1,
                ],
                80 => ['class_name' => 'AdminShopGroup', 'position' => 3, 'active' => 0,
                ],
                81 => ['class_name' => 'AdminImport', 'position' => 4, 'active' => 1,
                ],
                82 => ['class_name' => 'AdminBackup', 'position' => 5, 'active' => 1,
                ],
                83 => ['class_name' => 'AdminRequestSql', 'position' => 6, 'active' => 1,
                ],
                84 => ['class_name' => 'AdminLogs', 'position' => 7, 'active' => 1,
                ],
                85 => ['class_name' => 'AdminWebservice', 'position' => 8, 'active' => 1,
                ],
            ],
        ],
        18 => [
            'class_name' => 'AdminAdmin',
            'position' => 9,
            'active' => 1,
            'children' => [
                86 => ['class_name' => 'AdminAdminPreferences', 'position' => 0, 'active' => 1,
                ],
                87 => ['class_name' => 'AdminQuickAccesses', 'position' => 1, 'active' => 1,
                ],
                88 => ['class_name' => 'AdminEmployees', 'position' => 2, 'active' => 1,
                ],
                89 => ['class_name' => 'AdminProfiles', 'position' => 3, 'active' => 1,
                ],
                90 => ['class_name' => 'AdminAccess', 'position' => 4, 'active' => 1,
                ],
                91 => ['class_name' => 'AdminTabs', 'position' => 5, 'active' => 1,
                ],
            ],
        ],
        19 => [
            'class_name' => 'AdminParentStats',
            'position' => 10,
            'active' => 1,
            'children' => [
                92 => ['class_name' => 'AdminStats', 'position' => 0, 'active' => 1,
                ],
                93 => ['class_name' => 'AdminSearchEngines', 'position' => 1, 'active' => 1,
                ],
                94 => ['class_name' => 'AdminReferrers', 'position' => 2, 'active' => 1,
                ],
            ],
        ],
        20 => [
            'class_name' => 'AdminStock',
            'position' => 11,
            'active' => 1,
            'children' => [
                95 => ['class_name' => 'AdminWarehouses', 'position' => 0, 'active' => 1,
                ],
                96 => ['class_name' => 'AdminStockManagement', 'position' => 1, 'active' => 1,
                ],
                97 => ['class_name' => 'AdminStockMvt', 'position' => 2, 'active' => 1,
                ],
                98 => ['class_name' => 'AdminStockInstantState', 'position' => 3, 'active' => 1,
                ],
                99 => ['class_name' => 'AdminStockCover', 'position' => 4, 'active' => 1,
                ],
                100 => ['class_name' => 'AdminSupplyOrders', 'position' => 5, 'active' => 1,
                ],
                101 => ['class_name' => 'AdminStockConfiguration', 'position' => 6, 'active' => 1,
                ],
            ],
        ],
    ];

    //===== step 1 disabled all useless native tabs in 1.5 =====/

    $remove_tabs = [
        2 => 'AdminAddonsMyAccount', 4 => 'AdminAliases', 5 => 'AdminAppearance', 12 => 'AdminCMSContent',
        13 => 'AdminContact', 16 => 'AdminCounty', 20 => 'AdminDb', 22 => 'AdminDiscounts', 26 => 'AdminGenerator',
        38 => 'AdminMessages', 45 => 'AdminPDF', 63 => 'AdminStatsConf', 67 => 'AdminSubDomains',
    ];
    $ids = [];
    foreach ($remove_tabs as $tab) {
        if ($id = get_tab_id($tab)) {
            $ids[] = $id;
        }
    }

    if ($ids) {
        Db::getInstance()->update('tab', ['active' => 0], 'id_tab IN (' . implode(', ', $ids) . ')');
    }

    //=====================================/

    //===== step 2 move all no native tabs in AdminTools  =====/

    $id_admin_tools = get_tab_id('AdminTools');

    $tab_to_move = get_simple_clean_tab15($clean_tabs_15);

    $ids = [];
    foreach ($tab_to_move as $tab) {
        if ($id = get_tab_id($tab)) {
            $ids[] = $id;
        }
    }

    if ($ids) {
        Db::getInstance()->update('tab', ['id_parent' => $id_admin_tools], 'id_tab NOT IN (' . implode(', ', $ids) . ') AND `id_parent` <> -1');
    }

    //=====================================/

    //===== step 3 sort all 1.5 tabs  =====/

    updatePositionAndActive15($clean_tabs_15);

    //=====================================/

    //specific case for AdminStockMvt in AdminStock

    $id_AdminStockMvt = get_tab_id('AdminStockMvt');
    $id_AdminStock = get_tab_id('AdminStock');
    Db::getInstance()->update('tab', ['id_parent' => $id_AdminStock], 'id_tab =' . $id_AdminStockMvt);

    //rename some tabs
    renameTab(get_tab_id('AdminCartRules'), ['fr' => 'Règles paniers', 'es' => 'Reglas de cesta', 'en' => 'Cart Rules', 'de' => 'Warenkorb Preisregein', 'it' => 'Regole Carrello']);

    renameTab(get_tab_id('AdminPreferences'), ['fr' => 'Générales', 'es' => 'General', 'en' => 'General', 'de' => 'Allgemein', 'it' => 'Generale']);

    renameTab(get_tab_id('AdminThemes'), ['fr' => 'Thèmes', 'es' => 'Temas', 'en' => 'Themes', 'de' => 'Themen', 'it' => 'Temi']);

    renameTab(get_tab_id('AdminStores'), ['fr' => 'Coordonnées & magasins', 'es' => 'Contacto y tiendas', 'en' => 'Store Contacts', 'de' => 'Shopadressen', 'it' => 'Contatti e Negozi']);

    renameTab(get_tab_id('AdminTools'), ['fr' => 'Paramètres avancés', 'es' => 'Parametros avanzados', 'en' => 'Advanced Parameters', 'de' => 'Erweiterte Parameter', 'it' => 'Parametri Avanzati']);

    renameTab(get_tab_id('AdminTools'), ['fr' => 'Paramètres avancés', 'es' => 'Parametros avanzados', 'en' => 'Advanced Parameters', 'de' => 'Erweiterte Parameter', 'it' => 'Parametri Avanzati']);

    renameTab(get_tab_id('AdminTabs'), ['fr' => 'Menus', 'es' => 'Pestañas', 'en' => 'Menus', 'de' => 'Tabs', 'it' => 'Tabs']);
}

//==== functions =====/

function get_simple_clean_tab15($clean_tabs_15)
{
    $light_tab = [];
    foreach ($clean_tabs_15 as $tab) {
        $light_tab[] = $tab['class_name'];
        if (isset($tab['children'])) {
            $light_tab = array_merge($light_tab, get_simple_clean_tab15($tab['children']));
        }
    }

    return $light_tab;
}

function updatePositionAndActive15($clean_tabs_15)
{
    foreach ($clean_tabs_15 as $id => $tab) {
        Db::getInstance()->update('tab', ['position' => $tab['position'], 'active' => $tab['active']], '`id_tab`= ' . get_tab_id($tab['class_name']));
        if (isset($tab['children'])) {
            updatePositionAndActive15($tab['children']);
        }
    }
}

function renameTab($id_tab, $names)
{
    if (!$id_tab) {
        return;
    }
    $langues = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'lang');

    foreach ($langues as $lang) {
        if (array_key_exists($lang['iso_code'], $names)) {
            Db::getInstance()->update('tab_lang', ['name' => $names[$lang['iso_code']]], '`id_tab`= ' . $id_tab . ' AND `id_lang` =' . $lang['id_lang']);
        }
    }
}
