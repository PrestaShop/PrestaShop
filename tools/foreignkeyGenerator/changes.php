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
 * $changes = array(
 *   'tablename' => array(
 *     'field' => array(
 *       '#type' => 'type(size)',   // Mandatory, all other are optionals
 *       '#name' => 'newfieldname',
 *       '#unsigned' => true,
 *       '#null' => true,
 *     ),
 *   ),
 * );.
 */
$changes = array(
    'module_currency' => array(
        'id_currency' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
    ),
    'tab' => array(
        'id_parent' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
    ),
    'tax_rules_group' => array(
        'id_tax_rules_group' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
    ),
    'customer_message' => array(
        'id_customer_thread' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
    ),
    'customization' => array(
        'id_product' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
    ),
    'stock_mvt_reason' => array(
        'id_stock_mvt_reason' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
    ),
    'stock_mvt_reason_lang' => array(
        'id_stock_mvt_reason' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
        'id_lang' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
    ),
    'webservice_account' => array(
        'id_webservice_account' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
    ),
    'webservice_permission' => array(
        'id_webservice_permission' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
        'id_webservice_account' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
    ),
    'required_field' => array(
        'id_required_field' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
    ),
    'memcached_servers' => array(
        'id_memcached_server' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
    ),
    'product_country_tax' => array(
        'id_product' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
        'id_country' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
        'id_tax' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
    ),
    'tax_rule' => array(
        'id_tax_rule' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
        'id_tax_rules_group' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
        'id_country' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
        'id_state' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
        'id_tax' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
    ),
    'specific_price_priority' => array(
        'id_specific_price_priority' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
        'id_product' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
    ),
    'import_match' => array(
        'id_import_match' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
    ),
    'address_format' => array(
        'id_country' => array(
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ),
    ),
    'country' => array(
        'display_tax_label' => array(
            '#type' => 'tinyint(1)', // Was boolean
            '#unsigned' => false,
            '#null' => false,
        ),
    ),
);
