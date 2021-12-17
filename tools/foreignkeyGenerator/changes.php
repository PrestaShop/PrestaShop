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
