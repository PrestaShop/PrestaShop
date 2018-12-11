<?php
/**
 * $relations = array(
 *   TABLENAME => array(
 *      'field' => array(
 *          'foreign table' => 'foreign field',
 *       ),
 *    ),
 * );.
 */
$relations = [
    'access' => [
        'id_profile' => [
            'profile' => 'id_profile',
        ],
    ],

    'accessory' => [
        'id_product_1' => [
            'product' => 'id_product',
        ],
        'id_product_2' => [
            'product' => 'id_product',
        ],
    ],

    'address' => [
        'id_country' => [
            'country' => 'id_country',
        ],
        'id_state' => [
            'state' => 'id_state',
        ],
        'id_manufacturer' => [
            'manufacturer' => 'id_manufacturer',
        ],
        'id_supplier' => [
            'supplier' => 'id_supplier',
        ],
        'id_customer' => [
            'customer' => 'id_customer',
        ],
    ],

    'alias' => [
    ],

    'attachment' => [
    ],

    'attachment_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_attachment' => [
            'attachment' => 'id_attachment',
        ],
    ],

    'product_attachment' => [
        'id_product' => [
            'product' => 'id_product',
        ],
        'id_attachment' => [
            'attachment' => 'id_attachment',
        ],
    ],

    'attribute' => [
        'id_attribute_group' => [
            'attribute_group' => 'id_attribute_group',
        ],
    ],

    'attribute_group' => [
    ],

    'attribute_group_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_attribute_group' => [
            'attribute_group' => 'id_attribute_group',
        ],
    ],

    'attribute_impact' => [
        'id_product' => [
            'product' => 'id_product',
        ],
        'id_attribute' => [
            'attribute' => 'id_attribute',
        ],
    ],

    'attribute_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_attribute' => [
            'attribute' => 'id_attribute',
        ],
    ],

    'carrier' => [
        'id_tax_rules_group' => [
            'tax_rules_group' => 'id_tax_rules_group',
        ],
    ],

    'carrier_lang' => [
        'id_carrier' => [
            'carrier' => 'id_carrier',
        ],
        'id_lang' => [
            'lang' => 'id_lang',
        ],
    ],

    'carrier_zone' => [
        'id_carrier' => [
            'carrier' => 'id_carrier',
        ],
        'id_zone' => [
            'zone' => 'id_zone',
        ],
    ],

    'cart' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_customer' => [
            'customer' => 'id_customer',
        ],
        'id_address_delivery' => [
            'address' => 'id_address',
        ],
        'id_address_invoice' => [
            'address' => 'id_address',
        ],
        'id_carrier' => [
            'carrier' => 'id_carrier',
        ],
        'id_currency' => [
            'currency' => 'id_currency',
        ],
        'id_guest' => [
            'guest' => 'id_guest',
        ],
    ],

    'cart_discount' => [
        'id_cart' => [
            'cart' => 'id_cart',
        ],
        'id_discount' => [
            'discount' => 'id_discount',
        ],
    ],

    'cart_product' => [
        'id_cart' => [
            'cart' => 'id_cart',
        ],
        'id_product' => [
            'product' => 'id_product',
        ],
        'id_product_attribute' => [
            'product_attribute' => 'id_product_attribute',
        ],
    ],

    'category' => [
        'id_parent' => [
            'category' => 'id_category',
        ],
    ],

    'category_group' => [
        'id_category' => [
            'category' => 'id_category',
        ],
        'id_group' => [
            'group' => 'id_group',
        ],
    ],

    'category_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_category' => [
            'category' => 'id_category',
        ],
    ],

    'category_product' => [
        'id_category' => [
            'category' => 'id_category',
        ],
        'id_product' => [
            'product' => 'id_product',
        ],
    ],

    'cms' => [
        'id_cms_category' => [
            'cms_category' => 'id_cms_category',
        ],
    ],

    'cms_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_cms' => [
            'cms' => 'id_cms',
        ],
    ],

    'cms_category' => [
        'id_parent' => [
            'cms_category' => 'id_cms_category',
        ],
    ],

    'cms_category_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_cms_category' => [
            'cms_category' => 'id_cms_category',
        ],
    ],

    'compare' => [
        'id_customer' => [
            'customer' => 'id_customer',
        ],
    ],

    'compare_product' => [
        'id_compare' => [
            'compare' => 'id_compare',
        ],
        'id_product' => [
            'product' => 'id_product',
        ],
    ],

    'configuration' => [
    ],

    'configuration_lang' => [
        'id_configuration' => [
            'configuration' => 'id_configuration',
        ],
        'id_lang' => [
            'lang' => 'id_lang',
        ],
    ],

    'connections' => [
        'id_guest' => [
            'guest' => 'id_guest',
        ],
        'id_page' => [
            'page' => 'id_page',
        ],
    ],

    'connections_page' => [
        'id_connections' => [
            'connections' => 'id_connections',
        ],
        'id_page' => [
            'page' => 'id_page',
        ],
    ],

    'connections_source' => [
        'id_connections' => [
            'connections' => 'id_connections',
        ],
    ],

    'contact' => [
    ],

    'contact_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_contact' => [
            'contact' => 'id_contact',
        ],
    ],

    'country' => [
        'id_zone' => [
            'zone' => 'id_zone',
        ],
        'id_currency' => [
            'currency' => 'id_currency',
        ],
    ],

    'country_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_country' => [
            'country' => 'id_country',
        ],
    ],

    'currency' => [
    ],

    'customer' => [
    ],

    'customer_group' => [
        'id_customer' => [
            'customer' => 'id_customer',
        ],
        'id_group' => [
            'group' => 'id_group',
        ],
    ],

    'customer_message' => [
        'id_customer_thread' => [
            'customer_thread' => 'id_customer_thread',
        ],
        'id_employee' => [
            'employee' => 'id_employee',
        ],
    ],

    'customer_thread' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_contact' => [
            'contact' => 'id_contact',
        ],
        'id_customer' => [
            'customer' => 'id_customer',
        ],
        'id_order' => [
            'orders' => 'id_order',
        ],
        'id_product' => [
            'product' => 'id_product',
        ],
    ],

    'customization' => [
        'id_product_attribute' => [
            'product_attribute' => 'id_product_attribute',
        ],
        'id_cart' => [
            'cart' => 'id_cart',
        ],
        'id_product' => [
            'product' => 'id_product',
        ],
    ],

    'customization_field' => [
        'id_product' => [
            'product' => 'id_product',
        ],
    ],

    'customization_field_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_customization_field' => [
            'customization_field' => 'id_customization_field',
        ],
    ],

    'customized_data' => [
        'id_customization' => [
            'customization' => 'id_customization',
        ],
    ],

    'date_range' => [
    ],

    'delivery' => [
        'id_carrier' => [
            'carrier' => 'id_carrier',
        ],
        'id_zone' => [
            'zone' => 'id_zone',
        ],
    ],

    'discount' => [
        'id_discount_type' => [
            'discount_type' => 'id_discount_type',
        ],
        'id_customer' => [
            'customer' => 'id_customer',
        ],
    ],

    'discount_category' => [
        'id_discount' => [
            'discount' => 'id_discount',
        ],
        'id_category' => [
            'category' => 'id_category',
        ],
    ],

    'discount_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_discount' => [
            'discount' => 'id_discount',
        ],
    ],

    'discount_type' => [
    ],

    'discount_type_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_discount_type' => [
            'discount_type' => 'id_discount_type',
        ],
    ],

    'employee' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_profile' => [
            'profile' => 'id_profile',
        ],
    ],

    'feature' => [
    ],

    'feature_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_feature' => [
            'feature' => 'id_feature',
        ],
    ],

    'feature_product' => [
        'id_feature' => [
            'feature' => 'id_feature',
        ],
        'id_feature_value' => [
            'feature_value' => 'id_feature_value',
        ],
        'id_product' => [
            'product' => 'id_product',
        ],
    ],

    'feature_value' => [
        'id_feature' => [
            'feature' => 'id_feature',
        ],
    ],

    'feature_value_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_feature_value' => [
            'feature_value' => 'id_feature_value',
        ],
    ],

    'group' => [
    ],

    'group_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_group' => [
            'group' => 'id_group',
        ],
    ],

    'group_reduction' => [
        'id_group' => [
            'group' => 'id_group',
        ],
        'id_category' => [
            'category' => 'id_category',
        ],
    ],

    'product_group_reduction_cache' => [
        'id_product' => [
            'product' => 'id_product',
        ],
        'id_group' => [
            'group' => 'id_group',
        ],
    ],

    'guest' => [
        'id_customer' => [
            'customer' => 'id_customer',
        ],
        'id_operating_system' => [
            'operating_system' => 'id_operating_system',
        ],
        'id_web_browser' => [
            'web_browser' => 'id_web_browser',
        ],
    ],

    'hook' => [
    ],

    'hook_module' => [
        'id_module' => [
            'module' => 'id_module',
        ],
        'id_hook' => [
            'hook' => 'id_hook',
        ],
    ],

    'hook_module_exceptions' => [
        'id_module' => [
            'module' => 'id_module',
        ],
        'id_hook' => [
            'hook' => 'id_hook',
        ],
    ],

    'image' => [
        'id_product' => [
            'product' => 'id_product',
        ],
    ],

    'image_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_image' => [
            'image' => 'id_image',
        ],
    ],

    'image_type' => [
    ],

    'lang' => [
    ],

    'manufacturer' => [
    ],

    'manufacturer_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_manufacturer' => [
            'manufacturer' => 'id_manufacturer',
        ],
    ],

    'message' => [
        'id_cart' => [
            'cart' => 'id_cart',
        ],
        'id_customer' => [
            'customer' => 'id_customer',
        ],
        'id_employee' => [
            'employee' => 'id_employee',
        ],
        'id_order' => [
            'orders' => 'id_order',
        ],
    ],

    'message_readed' => [
        'id_message' => [
            'message' => 'id_message',
        ],
        'id_employee' => [
            'employee' => 'id_employee',
        ],
    ],

    'meta' => [
    ],

    'meta_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_meta' => [
            'meta' => 'id_meta',
        ],
    ],

    'module' => [
    ],

    'module_country' => [
        'id_module' => [
            'module' => 'id_module',
        ],
        'id_country' => [
            'country' => 'id_country',
        ],
    ],

    'module_currency' => [
        'id_module' => [
            'module' => 'id_module',
        ],
        'id_currency' => [
            'currency' => 'id_currency',
        ],
    ],

    'module_group' => [
        'id_module' => [
            'module' => 'id_module',
        ],
        'id_group' => [
            'group' => 'id_group',
        ],
    ],

    'operating_system' => [
    ],

    'orders' => [
        'id_customer' => [
            'customer' => 'id_customer',
        ],
        'id_carrier' => [
            'carrier' => 'id_carrier',
        ],
        'id_cart' => [
            'cart' => 'id_cart',
        ],
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_currency' => [
            'currency' => 'id_currency',
        ],
        'id_address_delivery' => [
            'address' => 'id_address',
        ],
        'id_address_invoice' => [
            'address' => 'id_address',
        ],
    ],

    'order_detail' => [
        'id_order' => [
            'orders' => 'id_order',
        ],
        'product_id' => [
            'product' => 'id_product',
        ],
        'product_attribute_id' => [
            'product_attribute' => 'id_product_attribute',
        ],
    ],

    'order_tax' => [
        'id_order' => [
            'orders' => 'id_order',
        ],
    ],

    'order_discount' => [
        'id_order' => [
            'orders' => 'id_order',
        ],
        'id_discount' => [
            'discount' => 'id_discount',
        ],
    ],

    'order_history' => [
        'id_employee' => [
            'employee' => 'id_employee',
        ],
        'id_order' => [
            'orders' => 'id_order',
        ],
        'id_order_state' => [
            'order_state' => 'id_order_state',
        ],
    ],

    'order_message' => [
    ],

    'order_message_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_order_message' => [
            'order_message' => 'id_order_message',
        ],
    ],

    'order_return' => [
        'id_customer' => [
            'customer' => 'id_customer',
        ],
        'id_order' => [
            'orders' => 'id_order',
        ],
    ],

    'order_return_detail' => [
        'id_order_return' => [
            'order_return' => 'id_order_return',
        ],
        'id_order_detail' => [
            'order_detail' => 'id_order_detail',
        ],
        'id_customization' => [
            'customization' => 'id_customization',
        ],
    ],

    'order_return_state' => [
    ],

    'order_return_state_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_order_return_state' => [
            'order_return_state' => 'id_order_return_state',
        ],
    ],

    'order_slip' => [
        'id_customer' => [
            'customer' => 'id_customer',
        ],
        'id_order' => [
            'orders' => 'id_order',
        ],
    ],

    'order_slip_detail' => [
        'id_order_slip' => [
            'order_slip' => 'id_order_slip',
        ],
        'id_order_detail' => [
            'order_detail' => 'id_order_detail',
        ],
    ],

    'order_state' => [
    ],

    'order_state_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_order_state' => [
            'order_state' => 'id_order_state',
        ],
    ],

    'pack' => [
        'id_product_item' => [
            'product' => 'id_product',
        ],
    ],

    'page' => [
        'id_page_type' => [
            'page_type' => 'id_page_type',
        ],
    ],

    'page_type' => [
    ],

    'page_viewed' => [
        'id_page' => [
            'page' => 'id_page',
        ],
        'id_date_range' => [
            'date_range' => 'id_date_range',
        ],
    ],

    'payment_cc' => [
        'id_order' => [
            'orders' => 'id_order',
        ],
        'id_currency' => [
            'currency' => 'id_currency',
        ],
    ],

    'product' => [
        'id_supplier' => [
            'supplier' => 'id_supplier',
        ],
        'id_manufacturer' => [
            'manufacturer' => 'id_manufacturer',
        ],
        'id_tax_rules_group' => [
            'tax_rules_group' => 'id_tax_rules_group',
        ],
        'id_category_default' => [
            'category' => 'id_category',
        ],
    ],

    'product_attribute' => [
        'id_product' => [
            'product' => 'id_product',
        ],
    ],

    'product_attribute_combination' => [
        'id_product_attribute' => [
            'product_attribute' => 'id_product_attribute',
        ],
    ],

    'product_attribute_image' => [
        'id_image' => [
            'image' => 'id_image',
        ],
        'id_product_attribute' => [
            'product_attribute' => 'id_product_attribute',
        ],
    ],

    'product_download' => [
        'id_product' => [
            'product' => 'id_product',
        ],
    ],

    'product_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_product' => [
            'product' => 'id_product',
        ],
    ],

    'product_sale' => [
        'id_product' => [
            'product' => 'id_product',
        ],
    ],

    'product_tag' => [
        'id_product' => [
            'product' => 'id_product',
        ],
        'id_tag' => [
            'tag' => 'id_tag',
        ],
    ],

    'profile' => [
    ],

    'profile_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_profile' => [
            'profile' => 'id_profile',
        ],
    ],

    'quick_access' => [
    ],

    'quick_access_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_quick_access' => [
            'quick_access' => 'id_quick_access',
        ],
    ],

    'range_price' => [
        'id_carrier' => [
            'carrier' => 'id_carrier',
        ],
    ],

    'range_weight' => [
        'id_carrier' => [
            'carrier' => 'id_carrier',
        ],
    ],

    'referrer' => [
    ],

    'referrer_cache' => [
        'id_connections_source' => [
            'connections_source' => 'id_connections_source',
        ],
        'id_referrer' => [
            'referrer' => 'id_referrer',
        ],
    ],

    'scene' => [
    ],

    'scene_category' => [
        'id_scene' => [
            'scene' => 'id_scene',
        ],
        'id_category' => [
            'category' => 'id_category',
        ],
    ],

    'scene_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_scene' => [
            'scene' => 'id_scene',
        ],
    ],

    'scene_products' => [
        'id_scene' => [
            'scene' => 'id_scene',
        ],
        'id_product' => [
            'product' => 'id_product',
        ],
    ],

    'search_engine' => [
    ],

    'search_index' => [
        'id_product' => [
            'product' => 'id_product',
        ],
        'id_word' => [
            'search_word' => 'id_word',
        ],
    ],

    'search_word' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
    ],

    'specific_price' => [
        'id_product' => [
            'product' => 'id_product',
        ],
        'id_country' => [
            'country' => 'id_country',
        ],
        'id_group' => [
            'group' => 'id_group',
        ],
    ],

    'state' => [
        'id_country' => [
            'country' => 'id_country',
        ],
        'id_zone' => [
            'zone' => 'id_zone',
        ],
    ],

    'subdomain' => [
    ],

    'supplier' => [
    ],

    'supplier_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_supplier' => [
            'supplier' => 'id_supplier',
        ],
    ],

    'tab' => [
        'id_parent' => [
            'tab' => 'id_tab',
        ],
    ],

    'tab_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_tab' => [
            'tab' => 'id_tab',
        ],
    ],

    'tag' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
    ],

    'tax' => [
    ],

    'tax_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_tax' => [
            'tax' => 'id_tax',
        ],
    ],

    'timezone' => [
    ],

    'web_browser' => [
    ],

    'zone' => [
    ],

    'carrier_group' => [
        'id_carrier' => [
            'carrier' => 'id_carrier',
        ],
        'id_group' => [
            'group' => 'id_group',
        ],
    ],

    'stock_mvt' => [
        'id_product' => [
            'product' => 'id_product',
        ],
        'id_product_attribute' => [
            'product_attribute' => 'id_product_attribute',
        ],
        'id_order' => [
            'orders' => 'id_order',
        ],
        'id_stock_mvt_reason' => [
            'stock_mvt_reason' => 'id_stock_mvt_reason',
        ],
        'id_employee' => [
            'employee' => 'id_employee',
        ],
    ],

    'stock_mvt_reason' => [
    ],

    'stock_mvt_reason_lang' => [
        'id_lang' => [
            'lang' => 'id_lang',
        ],
        'id_stock_mvt_reason' => [
            'stock_mvt_reason' => 'id_stock_mvt_reason',
        ],
    ],

    'store' => [
        'id_country' => [
            'country' => 'id_country',
        ],
        'id_state' => [
            'state' => 'id_state',
        ],
    ],

    'webservice_account' => [
    ],

    'webservice_permission' => [
        'id_webservice_account' => [
            'webservice_account' => 'id_webservice_account',
        ],
    ],

    'required_field' => [
    ],

    'memcached_servers' => [
    ],

    'product_country_tax' => [
        'id_product' => [
            'product' => 'id_product',
        ],
        'id_country' => [
            'country' => 'id_country',
        ],
        'id_tax' => [
            'tax' => 'id_tax',
        ],
    ],

    'tax_rule' => [
        'id_tax_rules_group' => [
            'tax_rules_group' => 'id_tax_rules_group',
        ],
        'id_country' => [
            'country' => 'id_country',
        ],
        'id_state' => [
            'state' => 'id_state',
        ],
        'id_tax' => [
            'tax' => 'id_tax',
        ],
    ],

    'tax_rules_group' => [
    ],

    'specific_price_priority' => [
        'id_product' => [
            'product' => 'id_product',
        ],
    ],

    'log' => [
    ],

    'import_match' => [
    ],

    'address_format' => [
        'id_country' => [
            'country' => 'id_country',
        ],
    ],
];
