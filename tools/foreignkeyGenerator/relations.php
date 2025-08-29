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
 * $relations = array(
 *   TABLENAME => array(
 *      'field' => array(
 *          'foreign table' => 'foreign field',
 *       ),
 *    ),
 * );.
 */
$relations = array(
    'access' => array(
        'id_profile' => array(
            'profile' => 'id_profile',
        ),
    ),

    'accessory' => array(
        'id_product_1' => array(
            'product' => 'id_product',
        ),
        'id_product_2' => array(
            'product' => 'id_product',
        ),
    ),

    'address' => array(
        'id_country' => array(
            'country' => 'id_country',
        ),
        'id_state' => array(
            'state' => 'id_state',
        ),
        'id_manufacturer' => array(
            'manufacturer' => 'id_manufacturer',
        ),
        'id_supplier' => array(
            'supplier' => 'id_supplier',
        ),
        'id_customer' => array(
            'customer' => 'id_customer',
        ),
    ),

    'alias' => array(
    ),

    'attachment' => array(
    ),

    'attachment_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_attachment' => array(
            'attachment' => 'id_attachment',
        ),
    ),

    'product_attachment' => array(
        'id_product' => array(
            'product' => 'id_product',
        ),
        'id_attachment' => array(
            'attachment' => 'id_attachment',
        ),
    ),

    'attribute' => array(
        'id_attribute_group' => array(
            'attribute_group' => 'id_attribute_group',
        ),
    ),

    'attribute_group' => array(
    ),

    'attribute_group_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_attribute_group' => array(
            'attribute_group' => 'id_attribute_group',
        ),
    ),

    'attribute_impact' => array(
        'id_product' => array(
            'product' => 'id_product',
        ),
        'id_attribute' => array(
            'attribute' => 'id_attribute',
        ),
    ),

    'attribute_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_attribute' => array(
            'attribute' => 'id_attribute',
        ),
    ),

    'carrier' => array(
        'id_tax_rules_group' => array(
            'tax_rules_group' => 'id_tax_rules_group',
        ),
    ),

    'carrier_lang' => array(
        'id_carrier' => array(
            'carrier' => 'id_carrier',
        ),
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
    ),

    'carrier_zone' => array(
        'id_carrier' => array(
            'carrier' => 'id_carrier',
        ),
        'id_zone' => array(
            'zone' => 'id_zone',
        ),
    ),

    'cart' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_customer' => array(
            'customer' => 'id_customer',
        ),
        'id_address_delivery' => array(
            'address' => 'id_address',
        ),
        'id_address_invoice' => array(
            'address' => 'id_address',
        ),
        'id_carrier' => array(
            'carrier' => 'id_carrier',
        ),
        'id_currency' => array(
            'currency' => 'id_currency',
        ),
        'id_guest' => array(
            'guest' => 'id_guest',
        ),
    ),

    'cart_discount' => array(
        'id_cart' => array(
            'cart' => 'id_cart',
        ),
        'id_discount' => array(
            'discount' => 'id_discount',
        ),
    ),

    'cart_product' => array(
        'id_cart' => array(
            'cart' => 'id_cart',
        ),
        'id_product' => array(
            'product' => 'id_product',
        ),
        'id_product_attribute' => array(
            'product_attribute' => 'id_product_attribute',
        ),
    ),

    'category' => array(
        'id_parent' => array(
            'category' => 'id_category',
        ),
    ),

    'category_group' => array(
        'id_category' => array(
            'category' => 'id_category',
        ),
        'id_group' => array(
            'group' => 'id_group',
        ),
    ),

    'category_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_category' => array(
            'category' => 'id_category',
        ),
    ),

    'category_product' => array(
        'id_category' => array(
            'category' => 'id_category',
        ),
        'id_product' => array(
            'product' => 'id_product',
        ),
    ),

    'cms' => array(
        'id_cms_category' => array(
            'cms_category' => 'id_cms_category',
        ),
    ),

    'cms_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_cms' => array(
            'cms' => 'id_cms',
        ),
    ),

    'cms_category' => array(
        'id_parent' => array(
            'cms_category' => 'id_cms_category',
        ),
    ),

    'cms_category_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_cms_category' => array(
            'cms_category' => 'id_cms_category',
        ),
    ),

    'compare' => array(
        'id_customer' => array(
            'customer' => 'id_customer',
        ),
    ),

    'compare_product' => array(
        'id_compare' => array(
            'compare' => 'id_compare',
        ),
        'id_product' => array(
            'product' => 'id_product',
        ),
    ),

    'configuration' => array(
    ),

    'configuration_lang' => array(
        'id_configuration' => array(
            'configuration' => 'id_configuration',
        ),
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
    ),

    'connections' => array(
        'id_guest' => array(
            'guest' => 'id_guest',
        ),
        'id_page' => array(
            'page' => 'id_page',
        ),
    ),

    'connections_page' => array(
        'id_connections' => array(
            'connections' => 'id_connections',
        ),
        'id_page' => array(
            'page' => 'id_page',
        ),
    ),

    'connections_source' => array(
        'id_connections' => array(
            'connections' => 'id_connections',
        ),
    ),

    'contact' => array(
    ),

    'contact_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_contact' => array(
            'contact' => 'id_contact',
        ),
    ),

    'country' => array(
        'id_zone' => array(
            'zone' => 'id_zone',
        ),
        'id_currency' => array(
            'currency' => 'id_currency',
        ),
    ),

    'country_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_country' => array(
            'country' => 'id_country',
        ),
    ),

    'currency' => array(
    ),

    'customer' => array(
    ),

    'customer_group' => array(
        'id_customer' => array(
            'customer' => 'id_customer',
        ),
        'id_group' => array(
            'group' => 'id_group',
        ),
    ),

    'customer_message' => array(
        'id_customer_thread' => array(
            'customer_thread' => 'id_customer_thread',
        ),
        'id_employee' => array(
            'employee' => 'id_employee',
        ),
    ),

    'customer_thread' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_contact' => array(
            'contact' => 'id_contact',
        ),
        'id_customer' => array(
            'customer' => 'id_customer',
        ),
        'id_order' => array(
            'orders' => 'id_order',
        ),
        'id_product' => array(
            'product' => 'id_product',
        ),
    ),

    'customization' => array(
        'id_product_attribute' => array(
            'product_attribute' => 'id_product_attribute',
        ),
        'id_cart' => array(
            'cart' => 'id_cart',
        ),
        'id_product' => array(
            'product' => 'id_product',
        ),
    ),

    'customization_field' => array(
        'id_product' => array(
            'product' => 'id_product',
        ),
    ),

    'customization_field_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_customization_field' => array(
            'customization_field' => 'id_customization_field',
        ),
    ),

    'customized_data' => array(
        'id_customization' => array(
            'customization' => 'id_customization',
        ),
    ),

    'date_range' => array(
    ),

    'delivery' => array(
        'id_carrier' => array(
            'carrier' => 'id_carrier',
        ),
        'id_zone' => array(
            'zone' => 'id_zone',
        ),
    ),

    'discount' => array(
        'id_discount_type' => array(
            'discount_type' => 'id_discount_type',
        ),
        'id_customer' => array(
            'customer' => 'id_customer',
        ),
    ),

    'discount_category' => array(
        'id_discount' => array(
            'discount' => 'id_discount',
        ),
        'id_category' => array(
            'category' => 'id_category',
        ),
    ),

    'discount_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_discount' => array(
            'discount' => 'id_discount',
        ),
    ),

    'discount_type' => array(
    ),

    'discount_type_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_discount_type' => array(
            'discount_type' => 'id_discount_type',
        ),
    ),

    'employee' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_profile' => array(
            'profile' => 'id_profile',
        ),
    ),

    'feature' => array(
    ),

    'feature_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_feature' => array(
            'feature' => 'id_feature',
        ),
    ),

    'feature_product' => array(
        'id_feature' => array(
            'feature' => 'id_feature',
        ),
        'id_feature_value' => array(
            'feature_value' => 'id_feature_value',
        ),
        'id_product' => array(
            'product' => 'id_product',
        ),
    ),

    'feature_value' => array(
        'id_feature' => array(
            'feature' => 'id_feature',
        ),
    ),

    'feature_value_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_feature_value' => array(
            'feature_value' => 'id_feature_value',
        ),
    ),

    'group' => array(
    ),

    'group_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_group' => array(
            'group' => 'id_group',
        ),
    ),

    'group_reduction' => array(
        'id_group' => array(
            'group' => 'id_group',
        ),
        'id_category' => array(
            'category' => 'id_category',
        ),
    ),

    'product_group_reduction_cache' => array(
        'id_product' => array(
            'product' => 'id_product',
        ),
        'id_group' => array(
            'group' => 'id_group',
        ),
    ),

    'guest' => array(
        'id_customer' => array(
            'customer' => 'id_customer',
        ),
        'id_operating_system' => array(
            'operating_system' => 'id_operating_system',
        ),
        'id_web_browser' => array(
            'web_browser' => 'id_web_browser',
        ),
    ),

    'hook' => array(
    ),

    'hook_module' => array(
        'id_module' => array(
            'module' => 'id_module',
        ),
        'id_hook' => array(
            'hook' => 'id_hook',
        ),
    ),

    'hook_module_exceptions' => array(
        'id_module' => array(
            'module' => 'id_module',
        ),
        'id_hook' => array(
            'hook' => 'id_hook',
        ),
    ),

    'image' => array(
        'id_product' => array(
            'product' => 'id_product',
        ),
    ),

    'image_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_image' => array(
            'image' => 'id_image',
        ),
    ),

    'image_type' => array(
    ),

    'lang' => array(
    ),

    'manufacturer' => array(
    ),

    'manufacturer_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_manufacturer' => array(
            'manufacturer' => 'id_manufacturer',
        ),
    ),

    'message' => array(
        'id_cart' => array(
            'cart' => 'id_cart',
        ),
        'id_customer' => array(
            'customer' => 'id_customer',
        ),
        'id_employee' => array(
            'employee' => 'id_employee',
        ),
        'id_order' => array(
            'orders' => 'id_order',
        ),
    ),

    'message_readed' => array(
        'id_message' => array(
            'message' => 'id_message',
        ),
        'id_employee' => array(
            'employee' => 'id_employee',
        ),
    ),

    'meta' => array(
    ),

    'meta_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_meta' => array(
            'meta' => 'id_meta',
        ),
    ),

    'module' => array(
    ),

    'module_country' => array(
        'id_module' => array(
            'module' => 'id_module',
        ),
        'id_country' => array(
            'country' => 'id_country',
        ),
    ),

    'module_currency' => array(
        'id_module' => array(
            'module' => 'id_module',
        ),
        'id_currency' => array(
            'currency' => 'id_currency',
        ),
    ),

    'module_group' => array(
        'id_module' => array(
            'module' => 'id_module',
        ),
        'id_group' => array(
            'group' => 'id_group',
        ),
    ),

    'operating_system' => array(
    ),

    'orders' => array(
        'id_customer' => array(
            'customer' => 'id_customer',
        ),
        'id_carrier' => array(
            'carrier' => 'id_carrier',
        ),
        'id_cart' => array(
            'cart' => 'id_cart',
        ),
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_currency' => array(
            'currency' => 'id_currency',
        ),
        'id_address_delivery' => array(
            'address' => 'id_address',
        ),
        'id_address_invoice' => array(
            'address' => 'id_address',
        ),
    ),

    'order_detail' => array(
        'id_order' => array(
            'orders' => 'id_order',
        ),
        'product_id' => array(
            'product' => 'id_product',
        ),
        'product_attribute_id' => array(
            'product_attribute' => 'id_product_attribute',
        ),
    ),

    'order_tax' => array(
        'id_order' => array(
            'orders' => 'id_order',
        ),
    ),

    'order_discount' => array(
        'id_order' => array(
            'orders' => 'id_order',
        ),
        'id_discount' => array(
            'discount' => 'id_discount',
        ),
    ),

    'order_history' => array(
        'id_employee' => array(
            'employee' => 'id_employee',
        ),
        'id_order' => array(
            'orders' => 'id_order',
        ),
        'id_order_state' => array(
            'order_state' => 'id_order_state',
        ),
    ),

    'order_message' => array(
    ),

    'order_message_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_order_message' => array(
            'order_message' => 'id_order_message',
        ),
    ),

    'order_return' => array(
        'id_customer' => array(
            'customer' => 'id_customer',
        ),
        'id_order' => array(
            'orders' => 'id_order',
        ),
    ),

    'order_return_detail' => array(
        'id_order_return' => array(
            'order_return' => 'id_order_return',
        ),
        'id_order_detail' => array(
            'order_detail' => 'id_order_detail',
        ),
        'id_customization' => array(
            'customization' => 'id_customization',
        ),
    ),

    'order_return_state' => array(
    ),

    'order_return_state_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_order_return_state' => array(
            'order_return_state' => 'id_order_return_state',
        ),
    ),

    'order_slip' => array(
        'id_customer' => array(
            'customer' => 'id_customer',
        ),
        'id_order' => array(
            'orders' => 'id_order',
        ),
    ),

    'order_slip_detail' => array(
        'id_order_slip' => array(
            'order_slip' => 'id_order_slip',
        ),
        'id_order_detail' => array(
            'order_detail' => 'id_order_detail',
        ),
    ),

    'order_state' => array(
    ),

    'order_state_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_order_state' => array(
            'order_state' => 'id_order_state',
        ),
    ),

    'pack' => array(
        'id_product_item' => array(
            'product' => 'id_product',
        ),
    ),

    'page' => array(
        'id_page_type' => array(
            'page_type' => 'id_page_type',
        ),
    ),

    'page_type' => array(
    ),

    'page_viewed' => array(
        'id_page' => array(
            'page' => 'id_page',
        ),
        'id_date_range' => array(
            'date_range' => 'id_date_range',
        ),
    ),

    'payment_cc' => array(
        'id_order' => array(
            'orders' => 'id_order',
        ),
        'id_currency' => array(
            'currency' => 'id_currency',
        ),
    ),

    'product' => array(
        'id_supplier' => array(
            'supplier' => 'id_supplier',
        ),
        'id_manufacturer' => array(
            'manufacturer' => 'id_manufacturer',
        ),
        'id_tax_rules_group' => array(
            'tax_rules_group' => 'id_tax_rules_group',
        ),
        'id_category_default' => array(
            'category' => 'id_category',
        ),
    ),

    'product_attribute' => array(
        'id_product' => array(
            'product' => 'id_product',
        ),
    ),

    'product_attribute_combination' => array(
        'id_product_attribute' => array(
            'product_attribute' => 'id_product_attribute',
        ),
    ),

    'product_attribute_image' => array(
        'id_image' => array(
            'image' => 'id_image',
        ),
        'id_product_attribute' => array(
            'product_attribute' => 'id_product_attribute',
        ),
    ),

    'product_attribute_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_product_attribute' => array(
            'product_attribute' => 'id_product_attribute',
        ),
    ),

    'product_download' => array(
        'id_product' => array(
            'product' => 'id_product',
        ),
    ),

    'product_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_product' => array(
            'product' => 'id_product',
        ),
    ),

    'product_sale' => array(
        'id_product' => array(
            'product' => 'id_product',
        ),
    ),

    'product_tag' => array(
        'id_product' => array(
            'product' => 'id_product',
        ),
        'id_tag' => array(
            'tag' => 'id_tag',
        ),
    ),

    'profile' => array(
    ),

    'profile_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_profile' => array(
            'profile' => 'id_profile',
        ),
    ),

    'quick_access' => array(
    ),

    'quick_access_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_quick_access' => array(
            'quick_access' => 'id_quick_access',
        ),
    ),

    'range_price' => array(
        'id_carrier' => array(
            'carrier' => 'id_carrier',
        ),
    ),

    'range_weight' => array(
        'id_carrier' => array(
            'carrier' => 'id_carrier',
        ),
    ),

    'scene' => array(
    ),

    'scene_category' => array(
        'id_scene' => array(
            'scene' => 'id_scene',
        ),
        'id_category' => array(
            'category' => 'id_category',
        ),
    ),

    'scene_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_scene' => array(
            'scene' => 'id_scene',
        ),
    ),

    'scene_products' => array(
        'id_scene' => array(
            'scene' => 'id_scene',
        ),
        'id_product' => array(
            'product' => 'id_product',
        ),
    ),

    'search_engine' => array(
    ),

    'search_index' => array(
        'id_product' => array(
            'product' => 'id_product',
        ),
        'id_word' => array(
            'search_word' => 'id_word',
        ),
    ),

    'search_word' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
    ),

    'specific_price' => array(
        'id_product' => array(
            'product' => 'id_product',
        ),
        'id_country' => array(
            'country' => 'id_country',
        ),
        'id_group' => array(
            'group' => 'id_group',
        ),
    ),

    'state' => array(
        'id_country' => array(
            'country' => 'id_country',
        ),
        'id_zone' => array(
            'zone' => 'id_zone',
        ),
    ),

    'subdomain' => array(
    ),

    'supplier' => array(
    ),

    'supplier_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_supplier' => array(
            'supplier' => 'id_supplier',
        ),
    ),

    'tab' => array(
        'id_parent' => array(
            'tab' => 'id_tab',
        ),
    ),

    'tab_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_tab' => array(
            'tab' => 'id_tab',
        ),
    ),

    'tag' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
    ),

    'tax' => array(
    ),

    'tax_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_tax' => array(
            'tax' => 'id_tax',
        ),
    ),

    'timezone' => array(
    ),

    'web_browser' => array(
    ),

    'zone' => array(
    ),

    'carrier_group' => array(
        'id_carrier' => array(
            'carrier' => 'id_carrier',
        ),
        'id_group' => array(
            'group' => 'id_group',
        ),
    ),

    'stock_mvt' => array(
        'id_product' => array(
            'product' => 'id_product',
        ),
        'id_product_attribute' => array(
            'product_attribute' => 'id_product_attribute',
        ),
        'id_order' => array(
            'orders' => 'id_order',
        ),
        'id_stock_mvt_reason' => array(
            'stock_mvt_reason' => 'id_stock_mvt_reason',
        ),
        'id_employee' => array(
            'employee' => 'id_employee',
        ),
    ),

    'stock_mvt_reason' => array(
    ),

    'stock_mvt_reason_lang' => array(
        'id_lang' => array(
            'lang' => 'id_lang',
        ),
        'id_stock_mvt_reason' => array(
            'stock_mvt_reason' => 'id_stock_mvt_reason',
        ),
    ),

    'store' => array(
        'id_country' => array(
            'country' => 'id_country',
        ),
        'id_state' => array(
            'state' => 'id_state',
        ),
    ),

    'webservice_account' => array(
    ),

    'webservice_permission' => array(
        'id_webservice_account' => array(
            'webservice_account' => 'id_webservice_account',
        ),
    ),

    'required_field' => array(
    ),

    'memcached_servers' => array(
    ),

    'product_country_tax' => array(
        'id_product' => array(
            'product' => 'id_product',
        ),
        'id_country' => array(
            'country' => 'id_country',
        ),
        'id_tax' => array(
            'tax' => 'id_tax',
        ),
    ),

    'tax_rule' => array(
        'id_tax_rules_group' => array(
            'tax_rules_group' => 'id_tax_rules_group',
        ),
        'id_country' => array(
            'country' => 'id_country',
        ),
        'id_state' => array(
            'state' => 'id_state',
        ),
        'id_tax' => array(
            'tax' => 'id_tax',
        ),
    ),

    'tax_rules_group' => array(
    ),

    'specific_price_priority' => array(
        'id_product' => array(
            'product' => 'id_product',
        ),
    ),

    'log' => array(
    ),

    'import_match' => array(
    ),

    'address_format' => array(
        'id_country' => array(
            'country' => 'id_country',
        ),
    ),
);
