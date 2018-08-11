SET NAMES 'utf8';

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
  ('PS_SMARTY_LOCAL', '0', NOW(), NOW()),
  ('PS_PASSWD_RESET_VALIDITY', '1440', NOW(), NOW()),
  ('PS_CUSTOMER_BIRTHDATE', '1', NOW(), NOW()),
  ('PS_ACTIVE_CRONJOB_EXCHANGE_RATE', '0', NOW(), NOW()),
  ('PS_ORDER_RECALCULATE_SHIPPING', '1', NOW(), NOW()),
  ('PS_MAINTENANCE_TEXT', 'We are currently updating our shop and will be back really soon.&lt;br&gt;Thanks for your patience.', NOW(), NOW());

INSERT INTO `PREFIX_configuration_lang` (`id_configuration`, `id_lang`, `value`, `date_upd`) SELECT c.`id_configuration`, l.`id_lang`, c.`value`, NOW() FROM `PREFIX_configuration` c, `PREFIX_lang` l WHERE c.`name` = 'PS_MAINTENANCE_TEXT';

INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES
  ('actionCartUpdateQuantityBefore', 'actionBeforeCartUpdateQty'),
  ('actionAjaxDieBefore', 'actionBeforeAjaxDie'),
  ('actionAuthenticationBefore', 'actionBeforeAuthentication'),
  ('actionSubmitAccountBefore', 'actionBeforeSubmitAccount'),
  ('actionDeleteProductInCartAfter', 'actionAfterDeleteProductInCart');

ALTER TABLE `PREFIX_currency` DROP `iso_code_num` , DROP `sign` , DROP `blank` , DROP `format` , DROP `decimals` ;

/* Password reset token for new "Forgot my password screen */
ALTER TABLE `PREFIX_customer` ADD `reset_password_token` varchar(40) DEFAULT NULL;
ALTER TABLE `PREFIX_customer` ADD `reset_password_validity` datetime DEFAULT NULL;
ALTER TABLE `PREFIX_employee` CHANGE `last_connection_date` `last_connection_date` DATE NULL DEFAULT NULL;
ALTER TABLE `PREFIX_employee` ADD `reset_password_token` varchar(40) DEFAULT NULL;
ALTER TABLE `PREFIX_employee` ADD `reset_password_validity` datetime DEFAULT NULL;

/*  Need to set the date to null before manipulate the table if the strict mode is enabled on MySQL */
UPDATE `PREFIX_customer` SET `newsletter_date_add` = NULL WHERE YEAR(newsletter_date_add) = "0000";
ALTER TABLE `PREFIX_customer` CHANGE COLUMN `passwd` `passwd` varchar(60) NOT NULL;
ALTER TABLE `PREFIX_employee` CHANGE COLUMN `passwd` `passwd` varchar(60) NOT NULL;

ALTER TABLE `PREFIX_customer` CHANGE COLUMN `firstname` `firstname` varchar(255) NOT NULL;
ALTER TABLE `PREFIX_customer` CHANGE COLUMN `lastname` `lastname` varchar(255) NOT NULL;

/* Changes regarding modules */
ALTER TABLE `PREFIX_module` ADD UNIQUE INDEX `name_UNIQUE` (`name` ASC);

DROP TABLE IF EXISTS `PREFIX_modules_perfs`;

CREATE TABLE `PREFIX_module_carrier` (
  `id_module`INT(10) unsigned NOT NULL,
  `id_shop`INT(11) unsigned NOT NULL DEFAULT '1',
  `id_reference` INT(11) NOT NULL,
  PRIMARY KEY (`id_module`,`id_shop`, `id_reference`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
/* PHP:select_current_payment_modules(); */;


/* Add Payment Preferences tab. SuperAdmin profile is the only one to access it. */
/* PHP:ps_1700_add_payment_preferences_tab(); */;
UPDATE `PREFIX_access` SET `view` = '0', `add` = '0', `edit` = '0', `delete` = '0' WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.`class_name` = 'AdminPaymentPreferences' LIMIT 1) AND `id_profile` > 1;

UPDATE `PREFIX_quick_access` SET `link` = "index.php/product/new" WHERE `link` = "index.php?controller=AdminProducts&addproduct";

ALTER TABLE `PREFIX_product` CHANGE `available_date` `available_date` DATE NULL DEFAULT NULL;
ALTER TABLE `PREFIX_product` ADD `show_condition` TINYINT(1) NOT NULL DEFAULT '0' AFTER `available_date`;
ALTER TABLE `PREFIX_product` ADD `isbn` VARCHAR( 13 ) NULL DEFAULT NULL;
ALTER TABLE `PREFIX_product_shop` CHANGE `available_date` `available_date` DATE NULL DEFAULT NULL;
ALTER TABLE `PREFIX_product_shop` ADD `show_condition` TINYINT(1) NOT NULL DEFAULT '0' AFTER `available_date`;
ALTER TABLE `PREFIX_order_detail` ADD `product_isbn` VARCHAR( 13 ) NULL DEFAULT NULL;
ALTER TABLE `PREFIX_product_attribute` CHANGE `available_date` `available_date` DATE NULL DEFAULT NULL;
ALTER TABLE `PREFIX_product_attribute` ADD `isbn` VARCHAR( 13 ) NULL DEFAULT NULL;
ALTER TABLE `PREFIX_product_attribute_shop` CHANGE `available_date` `available_date` DATE NULL DEFAULT NULL;
ALTER TABLE `PREFIX_stock` ADD `isbn` VARCHAR( 13 ) NULL DEFAULT NULL;
ALTER TABLE `PREFIX_supply_order_detail` ADD `isbn` VARCHAR( 13 ) NULL DEFAULT NULL;

ALTER TABLE `PREFIX_order_invoice` DROP COLUMN `invoice_address`, DROP COLUMN `delivery_address`;

ALTER TABLE `PREFIX_cart_product` CHANGE `id_product_attribute` `id_product_attribute` int(10) unsigned DEFAULT '0';

ALTER TABLE  `PREFIX_product_lang` ADD  `social_sharing_title` VARCHAR( 255 ) NOT NULL;
ALTER TABLE  `PREFIX_product_lang` ADD  `social_sharing_description` VARCHAR( 255 ) NOT NULL;

/* PHP:ps1700_stores(); */;

ALTER TABLE `PREFIX_hook` DROP `live_edit`;

/* Remove comparator feature */
DELETE FROM `PREFIX_hook_alias` WHERE `name` = 'displayProductComparison';
DELETE FROM `PREFIX_hook` WHERE `name` = 'displayProductComparison';
DELETE FROM `PREFIX_meta` WHERE `page` = 'products-comparison';
DROP TABLE IF EXISTS `PREFIX_compare`;
DROP TABLE IF EXISTS `PREFIX_compare_product`;
DROP TABLE IF EXISTS `PREFIX_theme`;
DROP TABLE IF EXISTS `PREFIX_theme_meta`;
DROP TABLE IF EXISTS `PREFIX_theme_specific`;
DROP TABLE IF EXISTS `PREFIX_scene`;
DROP TABLE IF EXISTS `PREFIX_scene_category`;
DROP TABLE IF EXISTS `PREFIX_scene_lang`;
DROP TABLE IF EXISTS `PREFIX_scene_products`;
DROP TABLE IF EXISTS `PREFIX_scene_shop`;

ALTER TABLE `PREFIX_shop` ADD COLUMN `theme_name` VARCHAR(255) AFTER `id_category`;
UPDATE `PREFIX_shop` SET `theme_name` = 'classic';
UPDATE `PREFIX_configuration` SET value=0 WHERE name='PS_TAX_DISPLAY';

ALTER TABLE `PREFIX_image_type` DROP `scenes`;
ALTER TABLE `PREFIX_cart` ADD `checkout_session_data` MEDIUMTEXT NULL;
ALTER TABLE `PREFIX_shop` DROP COLUMN `id_theme`;
ALTER TABLE `PREFIX_cart_product` ADD `id_customization` INT(10) NOT NULL DEFAULT '0' AFTER `id_product_attribute`;
ALTER TABLE `PREFIX_cart_product` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_cart`, `id_product`, `id_product_attribute`, `id_customization`, `id_address_delivery`);
ALTER TABLE `PREFIX_order_detail` ADD `id_customization` INT(10) NULL DEFAULT '0' AFTER `product_attribute_id`;
ALTER TABLE `PREFIX_customized_data` ADD `id_module` INT(10) NOT NULL DEFAULT '0', ADD `price` DECIMAL(20,6) NOT NULL DEFAULT '0', ADD `weight` DECIMAL(20,6) NOT NULL DEFAULT '0';
ALTER TABLE `PREFIX_customization_field` ADD `is_module` TINYINT(1) NOT NULL DEFAULT '0' ;
ALTER TABLE `PREFIX_cart_rule` ADD  `reduction_exclude_special` TINYINT(1) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `reduction_percent`;
ALTER TABLE `PREFIX_product` ADD `state` INT UNSIGNED NOT NULL DEFAULT '1';
ALTER TABLE `PREFIX_product` ADD KEY `state` (`state`, `date_upd`);

ALTER TABLE `PREFIX_lang` ADD `locale` varchar(5) COLLATE utf8_unicode_ci NOT NULL;
/* PHP:ps_1700_add_locale(); */;

/* Right management */
CREATE TABLE `PREFIX_authorization_role` (
  `id_authorization_role` int(10) unsigned NOT NULL auto_increment,
  `slug` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_authorization_role`),
  UNIQUE KEY (`slug`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

/* Create a copy without indexes to make ID updates without conflict. */
CREATE TABLE `PREFIX_access_old` AS SELECT * FROM `PREFIX_access`;
DROP TABLE `PREFIX_access`;
RENAME TABLE `PREFIX_module_access` TO `PREFIX_module_access_old`;

CREATE TABLE `PREFIX_tab_transit` (
  `id_old_tab` int(11),
  `id_new_tab` int(11),
  `key` VARCHAR(128) /* class_name and module concatenation */
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
/* Save the old IDs */
INSERT INTO `PREFIX_tab_transit` (`id_old_tab`, `key`) SELECT `id_tab`, CONCAT(`class_name`, COALESCE(`module`, '')) FROM `PREFIX_tab`;

CREATE TABLE `PREFIX_access` (
  `id_profile` int(10) unsigned NOT NULL,
  `id_authorization_role` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_profile`,`id_authorization_role`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_module_access` (
  `id_profile` int(10) unsigned NOT NULL,
  `id_authorization_role` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_profile`,`id_authorization_role`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

/* PHP:add_quick_access_tab(); */;

INSERT INTO `PREFIX_hook` (`name`, `title`, `description`, `position`) VALUES
  ('actionValidateCustomerAddressForm', 'Customer address form validation', 'This hook is called when a customer submit its address form', '1'),
  ('displayAfterCarrier', 'After carriers list', 'This hook is displayed after the carrier list in Front Office', '1'),
  ('displayCarrierExtraContent', 'Display additional content for a carrier (e.g pickup points)', 'This hook calls only the module related to the carrier, in order to add options when needed.', '1'),
  ('validateCustomerFormFields', 'Customer registration form validation', 'This hook is called to a module when it has sent additional fields with additionalCustomerFormFields', '1'),
  ('displayProductExtraContent', 'Display extra content on the product page', 'This hook expects ProductExtraContent instances, which will be properly displayed by the template on the product page.', '1'),
  ('displayNavFullWidth', 'Navigation', 'This hook displays full width navigation menu at the top of your pages', '1'),
  ('displayAfterBodyOpeningTag', 'Very top of pages', 'Use this hook for advertisement or modals you want to load first.', '1'),
  ('displayBeforeBodyClosingTag', 'Very bottom of pages', 'Use this hook for your modals or any content you want to load at the very end.', '1');


DELETE FROM `PREFIX_hook` WHERE `name` IN (
  'displayProductTab',
  'displayProductTabContent',
  'displayBeforePayment',
  'actionBeforeAuthentication',
  'actionOrderDetail',
  'actionProductListOverride',
  'actionSearch',
  'displayCustomerIdentityForm',
  'displayHomeTab',
  'displayHomeTabContent',
  'displayPayment');

DELETE FROM `PREFIX_hook_alias` WHERE `name` IN (
  'beforeAuthentication',
  'displayProductTab',
  'displayProductTabContent',
  'displayBeforePayment',
  'orderDetail',
  'payment',
  'productListAssign',
  'search');

DELETE FROM `PREFIX_configuration` WHERE `name` IN (
  '_MEDIA_SERVER_2_',
  '_MEDIA_SERVER_3_',
  'PS_ORDER_PROCESS_TYPE',
  'PS_ADVANCED_PAYMENT_API',
  'PS_ONE_PHONE_AT_LEAST',
  'PS_SCENE_FEATURE_ACTIVE',
  'PS_ADMINREFRESH_NOTIFICATION',
  'PS_CUSTOMER_NWSL',
  'PS_CACHEFS_DIRECTORY_DEPTH',
  'PS_CART_REDIRECT',
  'PS_COMPARATOR_MAX_ITEM',
  'PS_STORES_DISPLAY_FOOTER',
  'PS_STORES_SIMPLIFIED',
  'PS_STORES_CENTER_LAT',
  'PS_STORES_CENTER_LONG',
  'PS_STORES_DISPLAY_SITEMAP',
  'PS_CIPHER_ALGORITHM',
  'PS_HTML_THEME_COMPRESSION',
  'PS_JS_HTML_THEME_COMPRESSION',
  'PS_LOGO_MOBILE',
  'SHOP_LOGO_MOBILE_HEIGHT',
  'SHOP_LOGO_MOBILE_WIDTH');

ALTER TABLE `PREFIX_tab` ADD `icon` varchar(32) DEFAULT '';

/* PHP:migrate_tabs_17(); */;

/* Save the new IDs */
UPDATE `PREFIX_tab_transit` tt SET `id_new_tab` = (
  SELECT `id_tab` FROM `PREFIX_tab` WHERE CONCAT(`class_name`, COALESCE(`module`, '')) = tt.`key` LIMIT 1
);
/* Update default tab IDs for employees */
UPDATE `PREFIX_employee` e SET `default_tab` = (
  SELECT IFNULL(`id_new_tab`,
    /* If the tab does not exist anymore, fallback to the dashboard. */
    (SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = 'AdminDashboard' AND `module` IS NULL)
  ) FROM `PREFIX_tab_transit` WHERE `id_old_tab` = e.`default_tab`
);

/* Update access tab IDs */
UPDATE `PREFIX_access_old` ao SET `id_tab` = (
  /* Update tab ID if possible, leave as is if the tab does not exist anymore */
  SELECT IFNULL(`id_new_tab`, ao.`id_tab`) FROM `PREFIX_tab_transit` WHERE `id_old_tab` = ao.`id_tab`
);

/* Properly migrate the rights associated with each tabs */
/* PHP:ps_1700_right_management(); */;

DROP TABLE IF EXISTS `PREFIX_access_old`;
DROP TABLE IF EXISTS `PREFIX_module_access_old`;
DROP TABLE IF EXISTS `PREFIX_tab_transit`;
