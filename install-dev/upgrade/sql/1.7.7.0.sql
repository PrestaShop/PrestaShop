SET SESSION sql_mode='';
SET NAMES 'utf8';

ALTER DATABASE `DB_NAME` CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
    ('PS_DISPLAY_MANUFACTURERS', '1', NOW(), NOW()),
    ('PS_ORDER_PRODUCTS_NB_PER_PAGE', '8', NOW(), NOW())
;

/* Add field MPN to tables */
ALTER TABLE `PREFIX_order_detail` ADD `product_mpn` VARCHAR(40) NULL AFTER `product_upc`;
ALTER TABLE `PREFIX_supply_order_detail` ADD `mpn` VARCHAR(40) NULL AFTER `upc`;
ALTER TABLE `PREFIX_stock` ADD `mpn` VARCHAR(40) NULL AFTER `upc`;
ALTER TABLE `PREFIX_product_attribute` ADD `mpn` VARCHAR(40) NULL AFTER `upc`;
ALTER TABLE `PREFIX_product` ADD `mpn` VARCHAR(40) NULL AFTER `upc`;

/* Delete price display precision configuration */
DELETE FROM `PREFIX_configuration` WHERE `name` = 'PS_PRICE_DISPLAY_PRECISION';

/* Set optin field value to 0 in employee table */
ALTER TABLE `PREFIX_employee` MODIFY COLUMN `optin` tinyint(1) unsigned DEFAULT NULL;

/* Increase column size */
UPDATE `PREFIX_hook` SET `name` = SUBSTRING(`name`, 1, 191);
ALTER TABLE `PREFIX_hook` CHANGE `name` `name` VARCHAR(191) NOT NULL;
ALTER TABLE `PREFIX_hook` CHANGE `title` `title` VARCHAR(255) NOT NULL;

UPDATE `PREFIX_hook_alias` SET `name` = SUBSTRING(`name`, 1, 191), `alias` = SUBSTRING(`alias`, 1, 191);
ALTER TABLE `PREFIX_hook_alias` CHANGE `name` `name` VARCHAR(191) NOT NULL;
ALTER TABLE `PREFIX_hook_alias` CHANGE `alias` `alias` VARCHAR(191) NOT NULL;

/* php:ps_1770_update_charset */

UPDATE `PREFIX_alias` SET `alias` = SUBSTRING(`alias`, 1, 191);
ALTER TABLE `PREFIX_alias` CHANGE `alias` `alias` VARCHAR(191) NOT NULL;

UPDATE `PREFIX_authorization_role` SET `slug` = SUBSTRING(`slug`, 1, 191);
ALTER TABLE `PREFIX_authorization_role` CHANGE `slug` `slug` VARCHAR(191) NOT NULL;

UPDATE `PREFIX_module_preference` SET `module` = SUBSTRING(`module`, 1, 191);
ALTER TABLE `PREFIX_module_preference` CHANGE `module` `module` VARCHAR(191) NOT NULL;

UPDATE `PREFIX_tab_module_preference` SET `module` = SUBSTRING(`module`, 1, 191);
ALTER TABLE `PREFIX_tab_module_preference` CHANGE `module` `module` VARCHAR(191) NOT NULL;

UPDATE `PREFIX_smarty_lazy_cache` SET `cache_id` = SUBSTRING(`cache_id`, 1, 191);
ALTER TABLE `PREFIX_smarty_lazy_cache` CHANGE `cache_id` `cache_id` VARCHAR(191) NOT NULL;

/* improve performance of lookup by product reference/product_supplier avoiding full table scan */
ALTER TABLE PREFIX_product
    ADD INDEX reference_idx(reference),
    ADD INDEX supplier_reference_idx(supplier_reference);

/* Add fields for currencies */
ALTER TABLE `PREFIX_currency` ADD `unofficial` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `active`;
ALTER TABLE `PREFIX_currency` ADD `modified` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `unofficial`;
ALTER TABLE `PREFIX_currency_lang` ADD `pattern` varchar(255) DEFAULT NULL AFTER `symbol`;

/* Utf8mb4 conversion */
ALTER TABLE `PREFIX_access` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_accessory` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_address` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_address_format` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_alias` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_attachment` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_attachment_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_attribute` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_attribute_group` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_attribute_group_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_attribute_group_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_attribute_impact` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_attribute_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_attribute_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_authorization_role` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_carrier` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_carrier_group` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_carrier_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_carrier_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_carrier_tax_rules_group_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_carrier_zone` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cart` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cart_cart_rule` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cart_product` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cart_rule` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cart_rule_carrier` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cart_rule_combination` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cart_rule_country` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cart_rule_group` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cart_rule_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cart_rule_product_rule` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cart_rule_product_rule_group` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cart_rule_product_rule_value` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cart_rule_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_category` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_category_group` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_category_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_category_product` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_category_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cms` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cms_category` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cms_category_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cms_category_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cms_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cms_role` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cms_role_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_cms_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_configuration` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_configuration_kpi` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_configuration_kpi_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_configuration_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_connections` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_connections_page` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_connections_source` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_contact` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_contact_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_contact_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_country` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_country_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_country_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_currency` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_currency_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_currency_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_customer` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_customer_group` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_customer_message` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_customer_message_sync_imap` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_customer_session` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_customer_thread` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_customization` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_customization_field` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_customization_field_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_customized_data` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_date_range` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_delivery` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_employee` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_employee_session` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_employee_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_feature` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_feature_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_feature_product` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_feature_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_feature_value` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_feature_value_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_gender` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_gender_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_group` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_group_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_group_reduction` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_group_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_guest` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_hook` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_hook_alias` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_hook_module` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_hook_module_exceptions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_image` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_image_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_image_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_image_type` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_import_match` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_lang_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_log` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_mail` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_manufacturer` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_manufacturer_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_manufacturer_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_memcached_servers` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_message` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_message_readed` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_meta` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_meta_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_module` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_module_access` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_module_carrier` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_module_country` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_module_currency` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_module_group` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_module_preference` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_module_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_operating_system` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_orders` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_carrier` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_cart_rule` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_detail` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_detail_tax` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_history` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_invoice` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_invoice_payment` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_invoice_tax` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_message` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_message_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_payment` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_return` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_return_detail` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_return_state` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_return_state_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_slip` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_slip_detail` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_slip_detail_tax` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_state` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_order_state_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_pack` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_page` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_pagenotfound` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_page_type` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_page_viewed` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_product` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_product_attachment` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_product_attribute` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_product_attribute_combination` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_product_attribute_image` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_product_attribute_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_product_carrier` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_product_country_tax` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_product_download` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_product_group_reduction_cache` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_product_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_product_sale` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_product_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_product_supplier` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_product_tag` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_profile` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_profile_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_quick_access` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_quick_access_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_range_price` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_range_weight` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_referrer` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_referrer_cache` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_referrer_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_request_sql` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_required_field` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_risk` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_risk_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_search_engine` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_search_index` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_search_word` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

ALTER TABLE `PREFIX_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_shop_group` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_shop_url` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_smarty_cache` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_smarty_last_flush` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_smarty_lazy_cache` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_specific_price` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_specific_price_priority` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_specific_price_rule` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_specific_price_rule_condition` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_specific_price_rule_condition_group` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_state` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_stock` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_stock_available` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_stock_mvt` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_stock_mvt_reason` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_stock_mvt_reason_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_store` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_store_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_store_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_supplier` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_supplier_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_supplier_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_supply_order` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_supply_order_detail` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_supply_order_history` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_supply_order_receipt_history` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_supply_order_state` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_supply_order_state_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_tab` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_tab_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_tab_module_preference` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_tag` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_tag_count` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_tax` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_tax_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_tax_rule` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_tax_rules_group` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_tax_rules_group_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_timezone` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_warehouse` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_warehouse_carrier` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_warehouse_product_location` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_warehouse_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_webservice_account` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_webservice_account_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_webservice_permission` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_web_browser` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_zone` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_zone_shop` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE `PREFIX_gender_lang` CHANGE `name` `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `PREFIX_stock_mvt` CHANGE `employee_lastname` `employee_lastname` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
ALTER TABLE `PREFIX_stock_mvt` CHANGE `employee_firstname` `employee_firstname` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
ALTER TABLE `PREFIX_timezone` CHANGE `name` `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `PREFIX_attribute_group` CHANGE `group_type` `group_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `PREFIX_search_word` CHANGE `word` `word` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `PREFIX_meta` CHANGE `page` `page` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `PREFIX_statssearch` CHANGE `keywords` `keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `PREFIX_stock` CHANGE `reference` `reference` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `PREFIX_stock` CHANGE `ean13` `ean13` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
ALTER TABLE `PREFIX_stock` CHANGE `isbn` `isbn` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
ALTER TABLE `PREFIX_stock` CHANGE `upc` `upc` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
ALTER TABLE `PREFIX_attribute_lang` CHANGE `name` `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `PREFIX_connections` CHANGE `http_referer` `http_referer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
ALTER TABLE `PREFIX_product_download` CHANGE `display_filename` `display_filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;

/* Doctrine update happens too late to update the new enabled field, so we preset everything here */
ALTER TABLE `PREFIX_tab` ADD enabled TINYINT(1) NOT NULL;

/* PHP:ps_1770_preset_tab_enabled(); */;
/* PHP:ps_1770_update_order_status_colors(); */;

INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`) VALUES
  (NULL, 'displayAdminOrderTop', 'Admin Order Top', 'This hook displays content at the top of the order view page'),
  (NULL, 'displayAdminOrderSide', 'Admin Order Side Column', 'This hook displays content in the order view page in the side column under the customer view'),
  (NULL, 'displayAdminOrderSideBottom', 'Admin Order Side Column Bottom', 'This hook displays content in the order view page at the bottom of the side column'),
  (NULL, 'displayAdminOrderMain', 'Admin Order Main Column', 'This hook displays content in the order view page in the main column under the details view'),
  (NULL, 'displayAdminOrderMainBottom', 'Admin Order Main Column Bottom', 'This hook displays content in the order view page at the bottom of the main column'),
  (NULL, 'displayAdminOrderTabLink', 'Admin Order Tab Link', 'This hook displays new tab links on the order view page'),
  (NULL, 'displayAdminOrderTabContent', 'Admin Order Tab Content', 'This hook displays new tab contents on the order view page'),
  (NULL, 'actionGetAdminOrderButtons', 'Admin Order Buttons', 'This hook is used to generate the buttons collection on the order view page (see ActionsBarButtonsCollection)'),
  (NULL, 'displayFooterCategory', 'Category footer', 'This hook adds new blocks under the products listing in a category/search'),
  (NULL, 'displayBackOfficeOrderActions', 'Admin Order Actions', 'This hook displays content in the order view page after action buttons (or aliased to side column in migrated page)'),
  (NULL, 'actionAdminAdminPreferencesControllerPostProcessBefore', 'On post-process in Admin Preferences', 'This hook is called on Admin Preferences post-process before processing the form'),
  (NULL, 'displayAdditionalCustomerAddressFields', 'Display additional customer address fields', 'This hook allows to display extra field values added in an address form using hook ''additionalCustomerAddressFields'''),
  (NULL, 'displayAdminProductsExtra', 'Admin Product Extra Module Tab', 'This hook displays extra content in the Module tab on the product edit page'),
  (NULL, 'actionFrontControllerInitBefore', 'Perform actions before front office controller initialization', 'This hook is launched before the initialization of all front office controllers'),
  (NULL, 'actionFrontControllerInitAfter', 'Perform actions after front office controller initialization', 'This hook is launched after the initialization of all front office controllers'),
  (NULL, 'actionAdminControllerInitAfter', 'Perform actions after admin controller initialization', 'This hook is launched after the initialization of all admin controllers'),
  (NULL, 'actionAdminControllerInitBefore', 'Perform actions before admin controller initialization', 'This hook is launched before the initialization of all admin controllers'),
  (NULL, 'actionControllerInitAfter', 'Perform actions after controller initialization', 'This hook is launched after the initialization of all controllers'),
  (NULL, 'actionControllerInitBefore', 'Perform actions before controller initialization', 'This hook is launched before the initialization of all controllers'),
  (NULL, 'actionAdminLoginControllerBefore', 'Perform actions before admin login controller initialization', 'This hook is launched before the initialization of the login controller'),
  (NULL, 'actionAdminLoginControllerLoginBefore', 'Perform actions before admin login controller login action initialization', 'This hook is launched before the initialization of the login action in login controller'),
  (NULL, 'actionAdminLoginControllerLoginAfter', 'Perform actions after admin login controller login action initialization', 'This hook is launched after the initialization of the login action in login controller'),
  (NULL, 'actionAdminLoginControllerForgotBefore', 'Perform actions before admin login controller forgot action initialization', 'This hook is launched before the initialization of the forgot action in login controller'),
  (NULL, 'actionAdminLoginControllerForgotAfter', 'Perform actions after admin login controller forgot action initialization', 'This hook is launched after the initialization of the forgot action in login controller'),
  (NULL, 'actionAdminLoginControllerResetBefore', 'Perform actions before admin login controller reset action initialization', 'This hook is launched before the initialization of the reset action in login controller'),
  (NULL, 'actionAdminLoginControllerResetAfter', 'Perform actions after admin login controller reset action initialization', 'This hook is launched after the initialization of the reset action in login controller'),
  (NULL, 'displayHeader', 'Pages html head section', 'This hook adds additional elements in the head section of your pages (head section of html)')
;

INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES
  ('displayAdminOrderTop', 'displayInvoice'),
  ('displayAdminOrderSide', 'displayBackOfficeOrderActions'),
  ('actionFrontControllerInitAfter', 'actionFrontControllerAfterInit')
;

/* Add refund amount on order detail, and fill new columns via data in order_slip_detail table */
ALTER TABLE `PREFIX_order_detail` ADD `total_refunded_tax_excl` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000' AFTER `original_wholesale_price`;
ALTER TABLE `PREFIX_order_detail` ADD `total_refunded_tax_incl` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000' AFTER `total_refunded_tax_excl`;

ALTER TABLE `PREFIX_group_reduction` CHANGE `reduction` `reduction` DECIMAL(5, 4) NOT NULL DEFAULT '0.0000';
ALTER TABLE `PREFIX_product_group_reduction_cache` CHANGE `reduction` `reduction` DECIMAL(5, 4) NOT NULL DEFAULT '0.0000';
ALTER TABLE `PREFIX_order_slip` CHANGE `amount` `amount` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000';
ALTER TABLE `PREFIX_order_slip` CHANGE `shipping_cost_amount` `shipping_cost_amount` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000';
ALTER TABLE `PREFIX_order_payment` CHANGE `amount` `amount` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000';

/* attribute_impact price */
UPDATE `PREFIX_attribute_impact` SET `price` = RIGHT(`price`, 17) WHERE LENGTH(`price`) > 17;
ALTER TABLE `PREFIX_attribute_impact` CHANGE `price` `price` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000';

/* cart_rule minimum_amount & reduction_amount */
UPDATE `PREFIX_cart_rule` SET `minimum_amount` = RIGHT(`minimum_amount`, 17) WHERE LENGTH(`minimum_amount`) > 17;
UPDATE `PREFIX_cart_rule` SET `reduction_amount` = RIGHT(`reduction_amount`, 17) WHERE LENGTH(`reduction_amount`) > 17;
ALTER TABLE `PREFIX_cart_rule` CHANGE `minimum_amount` `minimum_amount` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000';
ALTER TABLE `PREFIX_cart_rule` CHANGE `reduction_amount` `reduction_amount` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000';

/* group reduction */
UPDATE `PREFIX_group` SET `reduction` = RIGHT(`reduction`, 6) WHERE LENGTH(`reduction`) > 6;
ALTER TABLE `PREFIX_group` CHANGE `reduction` `reduction` DECIMAL(5, 2) NOT NULL DEFAULT '0.00';

/* order_detail reduction_percent, group_reduction & ecotax */
UPDATE `PREFIX_order_detail` SET `reduction_percent` = RIGHT(`reduction_percent`, 6) WHERE LENGTH(`reduction_percent`) > 6;
UPDATE `PREFIX_order_detail` SET `group_reduction` = RIGHT(`group_reduction`, 6) WHERE LENGTH(`group_reduction`) > 6;
UPDATE `PREFIX_order_detail` SET `ecotax` = RIGHT(`ecotax`, 18) WHERE LENGTH(`ecotax`) > 18;
ALTER TABLE `PREFIX_order_detail` CHANGE `reduction_percent` `reduction_percent` DECIMAL(5, 2) NOT NULL DEFAULT '0.00';
ALTER TABLE `PREFIX_order_detail` CHANGE `group_reduction` `group_reduction` DECIMAL(5, 2) NOT NULL DEFAULT '0.00';
ALTER TABLE `PREFIX_order_detail` CHANGE `ecotax` `ecotax` DECIMAL(17, 6) NOT NULL DEFAULT '0.000000';

/* product additional_shipping_cost */
UPDATE `PREFIX_product` SET `additional_shipping_cost` = RIGHT(`additional_shipping_cost`, 17) WHERE LENGTH(`additional_shipping_cost`) > 17;
ALTER TABLE `PREFIX_product` CHANGE `additional_shipping_cost` `additional_shipping_cost` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000';

/* product_shop additional_shipping_cost */
UPDATE `PREFIX_product_shop` SET `additional_shipping_cost` = RIGHT(`additional_shipping_cost`, 17) WHERE LENGTH(`additional_shipping_cost`) > 17;
ALTER TABLE `PREFIX_product_shop` CHANGE `additional_shipping_cost` `additional_shipping_cost` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000';

/* order_cart_rule value & value_tax_excl */
UPDATE `PREFIX_order_cart_rule` SET `value` = RIGHT(`value`, 17) WHERE LENGTH(`value`) > 17;
UPDATE `PREFIX_order_cart_rule` SET `value_tax_excl` = RIGHT(`value_tax_excl`, 17) WHERE LENGTH(`value_tax_excl`) > 17;
ALTER TABLE `PREFIX_order_cart_rule` CHANGE `value` `value` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000';
ALTER TABLE `PREFIX_order_cart_rule` CHANGE `value_tax_excl` `value_tax_excl` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000';

/* add deleted field */
ALTER TABLE `PREFIX_order_cart_rule` ADD `deleted` TINYINT(1) UNSIGNED NOT NULL;

UPDATE
    `PREFIX_order_detail` `od`
SET
    `od`.`total_refunded_tax_excl` = IFNULL((
        SELECT SUM(`osd`.`amount_tax_excl`)
        FROM `PREFIX_order_slip_detail` `osd`
        WHERE `osd`.`id_order_detail` = `od`.`id_order_detail`
    ), 0),
    `od`.`total_refunded_tax_incl` = IFNULL((
        SELECT SUM(`osd`.`amount_tax_incl`)
        FROM `PREFIX_order_slip_detail` `osd`
        WHERE `osd`.`id_order_detail` = `od`.`id_order_detail`
    ), 0)
;
INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`)
VALUES (NULL, 'actionOrderMessageFormBuilderModifier', 'Modify order message identifiable object form',
        'This hook allows to modify order message identifiable object forms content by modifying form builder data or FormBuilder itself',
        '1'),
       (NULL, 'actionCatalogPriceRuleFormBuilderModifier', 'Modify catalog price rule identifiable object form',
        'This hook allows to modify catalog price rule identifiable object forms content by modifying form builder data or FormBuilder itself',
        '1'),
       (NULL, 'actionAttachmentFormBuilderModifier', 'Modify attachment identifiable object form',
        'This hook allows to modify attachment identifiable object forms content by modifying form builder data or FormBuilder itself',
        '1'),
       (NULL, 'actionBeforeUpdateFeatureFormHandler', 'Modify feature identifiable object data before updating it',
        'This hook allows to modify feature identifiable object forms data before it was updated', '1'),
       (NULL, 'actionBeforeUpdateOrderMessageFormHandler',
        'Modify order message identifiable object data before updating it',
        'This hook allows to modify order message identifiable object forms data before it was updated', '1'),
       (NULL, 'actionBeforeUpdateCatalogPriceRuleFormHandler',
        'Modify catalog price rule identifiable object data before updating it',
        'This hook allows to modify catalog price rule identifiable object forms data before it was updated', '1'),
       (NULL, 'actionBeforeUpdateAttachmentFormHandler',
        'Modify attachment identifiable object data before updating it',
        'This hook allows to modify attachment identifiable object forms data before it was updated', '1'),
       (NULL, 'actionAfterUpdateOrderMessageFormHandler',
        'Modify order message identifiable object data after updating it',
        'This hook allows to modify order message identifiable object forms data after it was updated', '1'),
       (NULL, 'actionAfterUpdateCatalogPriceRuleFormHandler',
        'Modify catalog price rule identifiable object data after updating it',
        'This hook allows to modify catalog price rule identifiable object forms data after it was updated', '1'),
       (NULL, 'actionAfterUpdateAttachmentFormHandler', 'Modify attachment identifiable object data after updating it',
        'This hook allows to modify attachment identifiable object forms data after it was updated', '1'),
       (NULL, 'actionBeforeCreateFeatureFormHandler', 'Modify feature identifiable object data before creating it',
        'This hook allows to modify feature identifiable object forms data before it was created', '1'),
       (NULL, 'actionBeforeCreateOrderMessageFormHandler',
        'Modify order message identifiable object data before creating it',
        'This hook allows to modify order message identifiable object forms data before it was created', '1'),
       (NULL, 'actionBeforeCreateCatalogPriceRuleFormHandler',
        'Modify catalog price rule identifiable object data before creating it',
        'This hook allows to modify catalog price rule identifiable object forms data before it was created', '1'),
       (NULL, 'actionBeforeCreateAttachmentFormHandler',
        'Modify attachment identifiable object data before creating it',
        'This hook allows to modify attachment identifiable object forms data before it was created', '1'),
       (NULL, 'actionAfterCreateOrderMessageFormHandler',
        'Modify order message identifiable object data after creating it',
        'This hook allows to modify order message identifiable object forms data after it was created', '1'),
       (NULL, 'actionAfterCreateCatalogPriceRuleFormHandler',
        'Modify catalog price rule identifiable object data after creating it',
        'This hook allows to modify catalog price rule identifiable object forms data after it was created', '1'),
       (NULL, 'actionAfterCreateAttachmentFormHandler', 'Modify attachment identifiable object data after creating it',
        'This hook allows to modify attachment identifiable object forms data after it was created', '1'),
       (NULL, 'actionMerchandiseReturnForm', 'Modify merchandise return options form content',
        'This hook allows to modify merchandise return options form FormBuilder', '1'),
       (NULL, 'actionCreditSlipForm', 'Modify credit slip options form content',
        'This hook allows to modify credit slip options form FormBuilder', '1'),
       (NULL, 'actionMerchandiseReturnSave', 'Modify merchandise return options form saved data',
        'This hook allows to modify data of merchandise return options form after it was saved', '1'),
       (NULL, 'actionCreditSlipSave', 'Modify credit slip options form saved data',
        'This hook allows to modify data of credit slip options form after it was saved', '1'),
       (NULL, 'actionEmptyCategoryGridDefinitionModifier', 'Modify empty category grid definition',
        'This hook allows to alter empty category grid columns, actions and filters', '1'),
       (NULL, 'actionNoQtyProductWithCombinationGridDefinitionModifier',
        'Modify no qty product with combination grid definition',
        'This hook allows to alter no qty product with combination grid columns, actions and filters', '1'),
       (NULL, 'actionNoQtyProductWithoutCombinationGridDefinitionModifier',
        'Modify no qty product without combination grid definition',
        'This hook allows to alter no qty product without combination grid columns, actions and filters', '1'),
       (NULL, 'actionDisabledProductGridDefinitionModifier', 'Modify disabled product grid definition',
        'This hook allows to alter disabled product grid columns, actions and filters', '1'),
       (NULL, 'actionProductWithoutImageGridDefinitionModifier', 'Modify product without image grid definition',
        'This hook allows to alter product without image grid columns, actions and filters', '1'),
       (NULL, 'actionProductWithoutDescriptionGridDefinitionModifier',
        'Modify product without description grid definition',
        'This hook allows to alter product without description grid columns, actions and filters', '1'),
       (NULL, 'actionProductWithoutPriceGridDefinitionModifier', 'Modify product without price grid definition',
        'This hook allows to alter product without price grid columns, actions and filters', '1'),
       (NULL, 'actionOrderGridDefinitionModifier', 'Modify order grid definition',
        'This hook allows to alter order grid columns, actions and filters', '1'),
       (NULL, 'actionCatalogPriceRuleGridDefinitionModifier', 'Modify catalog price rule grid definition',
        'This hook allows to alter catalog price rule grid columns, actions and filters', '1'),
       (NULL, 'actionOrderMessageGridDefinitionModifier', 'Modify order message grid definition',
        'This hook allows to alter order message grid columns, actions and filters', '1'),
       (NULL, 'actionAttachmentGridDefinitionModifier', 'Modify attachment grid definition',
        'This hook allows to alter attachment grid columns, actions and filters', '1'),
       (NULL, 'actionAttributeGroupGridDefinitionModifier', 'Modify attribute group grid definition',
        'This hook allows to alter attribute group grid columns, actions and filters', '1'),
       (NULL, 'actionMerchandiseReturnGridDefinitionModifier', 'Modify merchandise return grid definition',
        'This hook allows to alter merchandise return grid columns, actions and filters', '1'),
       (NULL, 'actionTaxRulesGroupGridDefinitionModifier', 'Modify tax rules group grid definition',
        'This hook allows to alter tax rules group grid columns, actions and filters', '1'),
       (NULL, 'actionAddressGridDefinitionModifier', 'Modify address grid definition',
        'This hook allows to alter address grid columns, actions and filters', '1'),
       (NULL, 'actionCreditSlipGridDefinitionModifier', 'Modify credit slip grid definition',
        'This hook allows to alter credit slip grid columns, actions and filters', '1'),
       (NULL, 'actionEmptyCategoryGridQueryBuilderModifier', 'Modify empty category grid query builder',
        'This hook allows to alter Doctrine query builder for empty category grid', '1'),
       (NULL, 'actionNoQtyProductWithCombinationGridQueryBuilderModifier',
        'Modify no qty product with combination grid query builder',
        'This hook allows to alter Doctrine query builder for no qty product with combination grid', '1'),
       (NULL, 'actionNoQtyProductWithoutCombinationGridQueryBuilderModifier',
        'Modify no qty product without combination grid query builder',
        'This hook allows to alter Doctrine query builder for no qty product without combination grid', '1'),
       (NULL, 'actionDisabledProductGridQueryBuilderModifier', 'Modify disabled product grid query builder',
        'This hook allows to alter Doctrine query builder for disabled product grid', '1'),
       (NULL, 'actionProductWithoutImageGridQueryBuilderModifier', 'Modify product without image grid query builder',
        'This hook allows to alter Doctrine query builder for product without image grid', '1'),
       (NULL, 'actionProductWithoutDescriptionGridQueryBuilderModifier',
        'Modify product without description grid query builder',
        'This hook allows to alter Doctrine query builder for product without description grid', '1'),
       (NULL, 'actionProductWithoutPriceGridQueryBuilderModifier', 'Modify product without price grid query builder',
        'This hook allows to alter Doctrine query builder for product without price grid', '1'),
       (NULL, 'actionOrderGridQueryBuilderModifier', 'Modify order grid query builder',
        'This hook allows to alter Doctrine query builder for order grid', '1'),
       (NULL, 'actionCatalogPriceRuleGridQueryBuilderModifier', 'Modify catalog price rule grid query builder',
        'This hook allows to alter Doctrine query builder for catalog price rule grid', '1'),
       (NULL, 'actionOrderMessageGridQueryBuilderModifier', 'Modify order message grid query builder',
        'This hook allows to alter Doctrine query builder for order message grid', '1'),
       (NULL, 'actionAttachmentGridQueryBuilderModifier', 'Modify attachment grid query builder',
        'This hook allows to alter Doctrine query builder for attachment grid', '1'),
       (NULL, 'actionAttributeGroupGridQueryBuilderModifier', 'Modify attribute group grid query builder',
        'This hook allows to alter Doctrine query builder for attribute group grid', '1'),
       (NULL, 'actionMerchandiseReturnGridQueryBuilderModifier', 'Modify merchandise return grid query builder',
        'This hook allows to alter Doctrine query builder for merchandise return grid', '1'),
       (NULL, 'actionTaxRulesGroupGridQueryBuilderModifier', 'Modify tax rules group grid query builder',
        'This hook allows to alter Doctrine query builder for tax rules group grid', '1'),
       (NULL, 'actionAddressGridQueryBuilderModifier', 'Modify address grid query builder',
        'This hook allows to alter Doctrine query builder for address grid', '1'),
       (NULL, 'actionCreditSlipGridQueryBuilderModifier', 'Modify credit slip grid query builder',
        'This hook allows to alter Doctrine query builder for credit slip grid', '1'),
       (NULL, 'actionEmptyCategoryGridDataModifier', 'Modify empty category grid data',
        'This hook allows to modify empty category grid data', '1'),
       (NULL, 'actionNoQtyProductWithCombinationGridDataModifier', 'Modify no qty product with combination grid data',
        'This hook allows to modify no qty product with combination grid data', '1'),
       (NULL, 'actionNoQtyProductWithoutCombinationGridDataModifier',
        'Modify no qty product without combination grid data',
        'This hook allows to modify no qty product without combination grid data', '1'),
       (NULL, 'actionDisabledProductGridDataModifier', 'Modify disabled product grid data',
        'This hook allows to modify disabled product grid data', '1'),
       (NULL, 'actionProductWithoutImageGridDataModifier', 'Modify product without image grid data',
        'This hook allows to modify product without image grid data', '1'),
       (NULL, 'actionProductWithoutDescriptionGridDataModifier', 'Modify product without description grid data',
        'This hook allows to modify product without description grid data', '1'),
       (NULL, 'actionProductWithoutPriceGridDataModifier', 'Modify product without price grid data',
        'This hook allows to modify product without price grid data', '1'),
       (NULL, 'actionOrderGridDataModifier', 'Modify order grid data', 'This hook allows to modify order grid data',
        '1'),
       (NULL, 'actionCatalogPriceRuleGridDataModifier', 'Modify catalog price rule grid data',
        'This hook allows to modify catalog price rule grid data', '1'),
       (NULL, 'actionOrderMessageGridDataModifier', 'Modify order message grid data',
        'This hook allows to modify order message grid data', '1'),
       (NULL, 'actionAttachmentGridDataModifier', 'Modify attachment grid data',
        'This hook allows to modify attachment grid data', '1'),
       (NULL, 'actionAttributeGroupGridDataModifier', 'Modify attribute group grid data',
        'This hook allows to modify attribute group grid data', '1'),
       (NULL, 'actionMerchandiseReturnGridDataModifier', 'Modify merchandise return grid data',
        'This hook allows to modify merchandise return grid data', '1'),
       (NULL, 'actionTaxRulesGroupGridDataModifier', 'Modify tax rules group grid data',
        'This hook allows to modify tax rules group grid data', '1'),
       (NULL, 'actionAddressGridDataModifier', 'Modify address grid data',
        'This hook allows to modify address grid data', '1'),
       (NULL, 'actionCreditSlipGridDataModifier', 'Modify credit slip grid data',
        'This hook allows to modify credit slip grid data', '1'),
       (NULL, 'actionEmptyCategoryGridFilterFormModifier', 'Modify empty category grid filters',
        'This hook allows to modify filters for empty category grid', '1'),
       (NULL, 'actionNoQtyProductWithCombinationGridFilterFormModifier',
        'Modify no qty product with combination grid filters',
        'This hook allows to modify filters for no qty product with combination grid', '1'),
       (NULL, 'actionNoQtyProductWithoutCombinationGridFilterFormModifier',
        'Modify no qty product without combination grid filters',
        'This hook allows to modify filters for no qty product without combination grid', '1'),
       (NULL, 'actionDisabledProductGridFilterFormModifier', 'Modify disabled product grid filters',
        'This hook allows to modify filters for disabled product grid', '1'),
       (NULL, 'actionProductWithoutImageGridFilterFormModifier', 'Modify product without image grid filters',
        'This hook allows to modify filters for product without image grid', '1'),
       (NULL, 'actionProductWithoutDescriptionGridFilterFormModifier',
        'Modify product without description grid filters',
        'This hook allows to modify filters for product without description grid', '1'),
       (NULL, 'actionProductWithoutPriceGridFilterFormModifier', 'Modify product without price grid filters',
        'This hook allows to modify filters for product without price grid', '1'),
       (NULL, 'actionOrderGridFilterFormModifier', 'Modify order grid filters',
        'This hook allows to modify filters for order grid', '1'),
       (NULL, 'actionCatalogPriceRuleGridFilterFormModifier', 'Modify catalog price rule grid filters',
        'This hook allows to modify filters for catalog price rule grid', '1'),
       (NULL, 'actionOrderMessageGridFilterFormModifier', 'Modify order message grid filters',
        'This hook allows to modify filters for order message grid', '1'),
       (NULL, 'actionAttachmentGridFilterFormModifier', 'Modify attachment grid filters',
        'This hook allows to modify filters for attachment grid', '1'),
       (NULL, 'actionAttributeGroupGridFilterFormModifier', 'Modify attribute group grid filters',
        'This hook allows to modify filters for attribute group grid', '1'),
       (NULL, 'actionMerchandiseReturnGridFilterFormModifier', 'Modify merchandise return grid filters',
        'This hook allows to modify filters for merchandise return grid', '1'),
       (NULL, 'actionTaxRulesGroupGridFilterFormModifier', 'Modify tax rules group grid filters',
        'This hook allows to modify filters for tax rules group grid', '1'),
       (NULL, 'actionAddressGridFilterFormModifier', 'Modify address grid filters',
        'This hook allows to modify filters for address grid', '1'),
       (NULL, 'actionCreditSlipGridFilterFormModifier', 'Modify credit slip grid filters',
        'This hook allows to modify filters for credit slip grid', '1'),
       (NULL, 'actionEmptyCategoryGridPresenterModifier', 'Modify empty category grid template data',
        'This hook allows to modify data which is about to be used in template for empty category grid', '1'),
       (NULL, 'actionNoQtyProductWithCombinationGridPresenterModifier',
        'Modify no qty product with combination grid template data',
        'This hook allows to modify data which is about to be used in template for no qty product with combination grid',
        '1'),
       (NULL, 'actionNoQtyProductWithoutCombinationGridPresenterModifier',
        'Modify no qty product without combination grid template data',
        'This hook allows to modify data which is about to be used in template for no qty product without combination grid',
        '1'),
       (NULL, 'actionDisabledProductGridPresenterModifier', 'Modify disabled product grid template data',
        'This hook allows to modify data which is about to be used in template for disabled product grid', '1'),
       (NULL, 'actionProductWithoutImageGridPresenterModifier', 'Modify product without image grid template data',
        'This hook allows to modify data which is about to be used in template for product without image grid', '1'),
       (NULL, 'actionProductWithoutDescriptionGridPresenterModifier',
        'Modify product without description grid template data',
        'This hook allows to modify data which is about to be used in template for product without description grid',
        '1'),
       (NULL, 'actionProductWithoutPriceGridPresenterModifier', 'Modify product without price grid template data',
        'This hook allows to modify data which is about to be used in template for product without price grid', '1'),
       (NULL, 'actionOrderGridPresenterModifier', 'Modify order grid template data',
        'This hook allows to modify data which is about to be used in template for order grid', '1'),
       (NULL, 'actionCatalogPriceRuleGridPresenterModifier', 'Modify catalog price rule grid template data',
        'This hook allows to modify data which is about to be used in template for catalog price rule grid', '1'),
       (NULL, 'actionOrderMessageGridPresenterModifier', 'Modify order message grid template data',
        'This hook allows to modify data which is about to be used in template for order message grid', '1'),
       (NULL, 'actionAttachmentGridPresenterModifier', 'Modify attachment grid template data',
        'This hook allows to modify data which is about to be used in template for attachment grid', '1'),
       (NULL, 'actionAttributeGroupGridPresenterModifier', 'Modify attribute group grid template data',
        'This hook allows to modify data which is about to be used in template for attribute group grid', '1'),
       (NULL, 'actionMerchandiseReturnGridPresenterModifier', 'Modify merchandise return grid template data',
        'This hook allows to modify data which is about to be used in template for merchandise return grid', '1'),
       (NULL, 'actionTaxRulesGroupGridPresenterModifier', 'Modify tax rules group grid template data',
        'This hook allows to modify data which is about to be used in template for tax rules group grid', '1'),
       (NULL, 'actionAddressGridPresenterModifier', 'Modify address grid template data',
        'This hook allows to modify data which is about to be used in template for address grid', '1'),
       (NULL, 'actionCreditSlipGridPresenterModifier', 'Modify credit slip grid template data',
        'This hook allows to modify data which is about to be used in template for credit slip grid', '1'),
       (NULL, 'displayAfterTitleTag', 'After title tag', 'Use this hook to add content after title tag', '1')
;

/* Update wrong hook names */
UPDATE `PREFIX_hook_module` AS hm
INNER JOIN `PREFIX_hook` AS hfrom ON hm.id_hook = hfrom.id_hook AND hfrom.name = 'actionAdministrationPageFormSave'
INNER JOIN `PREFIX_hook` AS hto ON hto.name = 'actionAdministrationPageSave'
SET hm.id_hook = hto.id_hook;
DELETE FROM `PREFIX_hook` WHERE name = 'actionAdministrationPageFormSave';

UPDATE `PREFIX_hook_module` AS hm
INNER JOIN `PREFIX_hook` AS hfrom ON hm.id_hook = hfrom.id_hook AND hfrom.name = 'actionMaintenancePageFormSave'
INNER JOIN `PREFIX_hook` AS hto ON hto.name = 'actionMaintenancePageSave'
SET hm.id_hook = hto.id_hook;
DELETE FROM `PREFIX_hook` WHERE name = 'actionMaintenancePageFormSave';

UPDATE `PREFIX_hook_module` AS hm
INNER JOIN `PREFIX_hook` AS hfrom ON hm.id_hook = hfrom.id_hook AND hfrom.name = 'actionPerformancePageFormSave'
INNER JOIN `PREFIX_hook` AS hto ON hto.name = 'actionPerformancePageSave'
SET hm.id_hook = hto.id_hook;
DELETE FROM `PREFIX_hook` WHERE name = 'actionPerformancePageFormSave';

UPDATE `PREFIX_hook_module` AS hm
INNER JOIN `PREFIX_hook` AS hfrom ON hm.id_hook = hfrom.id_hook AND hfrom.name = 'actionFrontControllerAfterInit'
INNER JOIN `PREFIX_hook` AS hto ON hto.name = 'actionFrontControllerInitAfter'
SET hm.id_hook = hto.id_hook;
DELETE FROM `PREFIX_hook` WHERE name = 'actionFrontControllerAfterInit';

/* Update wrong hook alias */
UPDATE `PREFIX_hook_alias` SET name = 'displayHeader', alias = 'Header' WHERE name = 'Header' AND alias = 'displayHeader';
