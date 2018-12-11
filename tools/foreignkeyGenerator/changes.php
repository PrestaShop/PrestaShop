<?php
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
$changes = [
    'module_currency' => [
        'id_currency' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
    ],
    'tab' => [
        'id_parent' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
    ],
    'tax_rules_group' => [
        'id_tax_rules_group' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
    ],
    'customer_message' => [
        'id_customer_thread' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
    ],
    'customization' => [
        'id_product' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
    ],
    'stock_mvt_reason' => [
        'id_stock_mvt_reason' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
    ],
    'stock_mvt_reason_lang' => [
        'id_stock_mvt_reason' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
        'id_lang' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
    ],
    'webservice_account' => [
        'id_webservice_account' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
    ],
    'webservice_permission' => [
        'id_webservice_permission' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
        'id_webservice_account' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
    ],
    'required_field' => [
        'id_required_field' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
    ],
    'memcached_servers' => [
        'id_memcached_server' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
    ],
    'product_country_tax' => [
        'id_product' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
        'id_country' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
        'id_tax' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
    ],
    'tax_rule' => [
        'id_tax_rule' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
        'id_tax_rules_group' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
        'id_country' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
        'id_state' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
        'id_tax' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
    ],
    'tax_rules_group' => [
        'id_tax_rules_group' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
    ],
    'specific_price_priority' => [
        'id_specific_price_priority' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
        'id_product' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
    ],
    'import_match' => [
        'id_import_match' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
    ],
    'address_format' => [
        'id_country' => [
            '#type' => 'INT(11)',
            '#unsigned' => true,
        ],
    ],
    'country' => [
        'display_tax_label' => [
            '#type' => 'tinyint(1)', // Was boolean
            '#unsigned' => false,
            '#null' => false,
        ],
    ],
];
