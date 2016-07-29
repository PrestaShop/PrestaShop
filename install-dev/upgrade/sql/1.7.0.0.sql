SET NAMES 'utf8';

INSERT INTO `PREFIX_configuration` (`name` , `value` , `date_add` , `date_upd`) VALUES ('PS_SMARTY_LOCAL', '0', NOW(), NOW());
DELETE FROM `PREFIX_configuration` WHERE `name` = 'PS_ORDER_PROCESS_TYPE';
DELETE FROM `PREFIX_configuration` WHERE `name` = 'PS_ADVANCED_PAYMENT_API';
DELETE FROM `PREFIX_configuration` WHERE `name` = 'PS_ONE_PHONE_AT_LEAST';

INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES ('actionCartUpdateQuantityBefore', 'actionBeforeCartUpdateQty');
INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES ('actionAjaxDieBefore', 'actionBeforeAjaxDie');
INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES ('actionAuthenticationBefore', 'actionBeforeAuthentication');
INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES ('actionSubmitAccountBefore', 'actionBeforeSubmitAccount');
INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES ('actionDeleteProductInCartAfter', 'actionAfterDeleteProductInCart');

ALTER TABLE `PREFIX_currency` DROP `iso_code_num` ,DROP `sign` ,DROP `blank` ,DROP `format` ,DROP `decimals` ;

/* Password reset token for new "Forgot my password screen */
ALTER TABLE PREFIX_customer ADD `reset_password_token` varchar(40) DEFAULT NULL;
ALTER TABLE PREFIX_customer ADD `reset_password_validity` datetime DEFAULT NULL;
ALTER TABLE PREFIX_employee ADD `reset_password_token` varchar(40) DEFAULT NULL;
ALTER TABLE PREFIX_employee ADD `reset_password_validity` datetime DEFAULT NULL;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_PASSWD_RESET_VALIDITY', '1440', NOW(), NOW());

ALTER TABLE `PREFIX_customer` CHANGE COLUMN `passwd` `passwd` varchar(255) NOT NULL;

INSERT INTO `PREFIX_configuration` (`id_configuration` ,`id_shop_group` ,`id_shop` ,`name` ,`value` ,`date_add` ,`date_upd`) VALUES (NULL , NULL , NULL , 'PS_ACTIVE_CRONJOB_EXCHANGE_RATE', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_CUSTOMER_BIRTHDATE', 1, NOW(), NOW());

ALTER TABLE `PREFIX_customer` CHANGE COLUMN `firstname` `firstname` varchar(255) NOT NULL;
ALTER TABLE `PREFIX_customer` CHANGE COLUMN `lastname` `lastname` varchar(255) NOT NULL;

/* Changes regarding modules */
ALTER TABLE `PREFIX_module` ADD UNIQUE INDEX `name_UNIQUE` (`name` ASC);

DROP TABLE `PREFIX_modules_perfs`;

CREATE TABLE `PREFIX_module_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_employee` int(10) unsigned NOT NULL,
  `id_module` int(10) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_employee` (`id_employee`,`id_module`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;


ALTER TABLE `PREFIX_product` ADD `show_condition` TINYINT(1) NOT NULL DEFAULT '0' AFTER `available_date`;
ALTER TABLE `PREFIX_product_shop` ADD `show_condition` TINYINT(1) NOT NULL DEFAULT '0' AFTER `available_date`;

/* Add Payment Preferences tab. SuperAdmin profile is the only one to access it. */
/* PHP:ps_1701_add_payment_preferences_tab(); */;
UPDATE `PREFIX_access` SET `view` = '0', `add` = '0', `edit` = '0', `delete` = '0' WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.`class_name` = 'AdminPaymentPreferences' LIMIT 1) AND `id_profile` > 1;

UPDATE `PREFIX_quick_access` SET `link` = "product/new" WHERE `link` = "index.php?controller=AdminProducts&addproduct";

ALTER TABLE `PREFIX_product` ADD `isbn` VARCHAR( 13 ) NULL DEFAULT NULL;
ALTER TABLE `PREFIX_order_detail` ADD `product_isbn` VARCHAR( 13 ) NULL DEFAULT NULL;
ALTER TABLE `PREFIX_product_attribute` ADD `isbn` VARCHAR( 13 ) NULL DEFAULT NULL;
ALTER TABLE `PREFIX_stock` ADD `isbn` VARCHAR( 13 ) NULL DEFAULT NULL;
ALTER TABLE `PREFIX_supply_order_detail` ADD `isbn` VARCHAR( 13 ) NULL DEFAULT NULL;

ALTER TABLE `PREFIX_order_invoice` DROP COLUMN `invoice_address` DROP COLUMN `delivery_address`;

ALTER TABLE `PREFIX_cart_product` CHANGE `id_product_attribute` `id_product_attribute` int(10) unsigned DEFAULT '0';

ALTER TABLE  `PREFIX_product_lang` ADD  `social_sharing_title` VARCHAR( 255 ) NOT NULL;
ALTER TABLE  `PREFIX_product_lang` ADD  `social_sharing_description` VARCHAR( 255 ) NOT NULL;

/* PHP:ps1700_stores(); */;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_PASSWD_RESET_VALIDITY', '1440', NOW(), NOW());

ALTER TABLE `PREFIX_hook` DROP `live_edit`;

/* Remove comparator feature */
DELETE FROM `PREFIX_hook_alias` WHERE `name` = 'displayProductComparison';
DELETE FROM `PREFIX_hook` WHERE `name` = 'displayProductComparison';
DELETE FROM `PREFIX_configuration` WHERE `name` = 'PS_COMPARATOR_MAX_ITEM';
DELETE FROM `PREFIX_meta` WHERE `page` = 'products-comparison';
DROP TABLE IF EXISTS PREFIX_compare;
DROP TABLE IF EXISTS PREFIX_compare_product;

ALTER TABLE `PREFIX_cart` ADD `checkout_session_data` MEDIUMTEXT NULL;

DROP TABLE `PREFIX_theme`;
DROP TABLE `PREFIX_theme_meta`;
DROP TABLE `PREFIX_theme_specific`;

ALTER TABLE `PREFIX_shop` DROP COLUMN `id_theme`;
ALTER TABLE `PREFIX_shop` ADD COLUMN `theme_name` VARCHAR(255) AFTER `id_category`;
UPDATE `PREFIX_shop` SET `theme_name` = 'classic';

DROP TABLE `PREFIX_scene`;
DROP TABLE `PREFIX_scene_category`;
DROP TABLE `PREFIX_scene_lang`;
DROP TABLE `PREFIX_scene_products`;
DROP TABLE `PREFIX_scene_shop`;
ALTER TABLE `PREFIX_image_type` DROP `scenes`;
DELETE FROM `PREFIX_configuration` WHERE `name` = 'PS_SCENE_FEATURE_ACTIVE';

UPDATE `PREFIX_configuration` SET value=0 WHERE name='PS_TAX_DISPLAY';

DELETE FROM `PREFIX_configuration` WHERE `name` = 'PS_ADMINREFRESH_NOTIFICATION';

ALTER TABLE `PREFIX_cart_product` ADD `id_customization` INT(10) NOT NULL DEFAULT '0' AFTER `id_product_attribute`;
ALTER TABLE `PREFIX_cart_product` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_cart`, `id_product`, `id_product_attribute`, `id_customization`, `id_address_delivery`);
ALTER TABLE `PREFIX_order_detail` ADD `id_customization` INT(10) NULL DEFAULT '0' AFTER `product_attribute_id`;
ALTER TABLE `PREFIX_customized_data` ADD `id_module` INT(10) NOT NULL DEFAULT '0', ADD `price` DECIMAL(20,6) NOT NULL DEFAULT '0', ADD `weight` DECIMAL(20,6) NOT NULL DEFAULT '0';
ALTER TABLE `PREFIX_customization_field` ADD `is_module` TINYINT(1) NOT NULL DEFAULT '0' ;

INSERT INTO `PREFIX_configuration` (name, value, date_add, date_upd) VALUES ('PS_MAINTENANCE_TEXT', 'We are currently updating our shop and will be back really soon.&lt;br&gt;Thanks for your patience.', NOW(), NOW());
INSERT INTO `PREFIX_configuration_lang` (`id_configuration`, `id_lang`, `value`, `date_upd`) SELECT c.`id_configuration`, l.`id_lang`, c.`value`, NOW() FROM `PREFIX_configuration` c, `PREFIX_lang` l WHERE c.`name` = 'PS_MAINTENANCE_TEXT';

/* Right management */
CREATE TABLE `PREFIX_authorization_role` (
  `id_authorization_role` int(10) unsigned NOT NULL auto_increment,
  `slug` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_authorization_role`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

RENAME TABLE `PREFIX_access` TO `PREFIX_access_old`;
RENAME TABLE `PREFIX_module_access` TO `PREFIX_module_access_old`;

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

/* Add Payment Preferences tab. SuperAdmin profile is the only one to access it. */
/* PHP:ps_1702_right_management(); */;

DROP TABLE `PREFIX_access_old`;
DROP TABLE `PREFIX_module_access_old`;

DELETE FROM `PREFIX_configuration` WHERE `name` = 'PS_CUSTOMER_NWSL';

ALTER TABLE `PREFIX_cart_rule` ADD  `reduction_exclude_special` TINYINT(1) UNSIGNED NOT NULL DEFAULT  '0' AFTER  `reduction_percent`;
ALTER TABLE `PREFIX_product` ADD state INT UNSIGNED NOT NULL DEFAULT '1';
ALTER TABLE `PREFIX_product` ADD KEY state (state, date_upd);