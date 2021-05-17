SET SESSION sql_mode='';
SET NAMES 'utf8mb4';

INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES
  (NULL, 'actionPresentCart', 'Cart Presenter', 'This hook is called before a cart is presented', '1'),
  (NULL, 'actionPresentOrder', 'Order footer', 'This hook is called before an order is presented', '1'),
  (NULL, 'actionPresentOrderReturn', 'Order Return Presenter', 'This hook is called before an order return is presented', '1'),
  (NULL, 'actionPresentProduct', 'Product Presenter', 'This hook is called before a product is presented', '1'),
  (NULL, 'displayBanner', 'Display Banner', 'Use this hook for banners on top of every pages', '1'),
  (NULL, 'actionModuleUninstallBefore', 'Module uninstall before', 'This hook is called before module uninstall process', '1'),
  (NULL, 'actionModuleUninstallAfter', 'Module uninstall after', 'This hook is called at the end of module uninstall process', '1'),
  (NULL, 'displayCartModalContent', 'Cart Presenter', 'This hook displays content in the middle of the window that appears after adding product to cart', '1'),
  (NULL, 'displayCartModalFooter', 'Cart Presenter', 'This hook displays content in the bottom of window that appears after adding product to cart', '1'),
  (NULL, 'displayHeaderCategory', 'Category header', 'This hook adds new blocks above the products listing in a category/search', '1'),
  (NULL, 'actionCheckoutRender', 'Checkout process render', 'This hook is called when checkout process is constructed', '1'),
  (NULL, 'actionPresentProductListing', 'Product Listing Presenter', 'This hook is called before a product listing is presented', '1'),
  (NULL, 'actionGetProductPropertiesAfterUnitPrice', 'Product Properties', 'This hook is called after defining the properties of a product', '1'),
  (NULL, 'actionProductSearchProviderRunQueryBefore', 'Runs an action before ProductSearchProviderInterface::RunQuery()', 'Required to modify an SQL query before executing it', '1'),
  (NULL, 'actionProductSearchProviderRunQueryAfter', 'Runs an action after ProductSearchProviderInterface::RunQuery()', 'Required to return a previous state of an SQL query or/and to change a result of the SQL query after executing it', '1'),
  (NULL, 'actionOverrideEmployeeImage', 'Override Employee Image', 'This hook is used to override the employee image', '1'),
  (NULL, 'actionFrontControllerSetVariables', 'Add variables in JavaScript object and Smarty templates', 'Add variables to javascript object that is available in Front Office. These are also available in smarty templates in modules.your_module_name.', '1'),
  (NULL, 'displayAdminGridTableBefore', 'Display before Grid table', 'This hook adds new blocks before Grid component table.', '1'),
  (NULL, 'displayAdminGridTableAfter', 'Display after Grid table', 'This hook adds new blocks after Grid component table.', '1'),
  (NULL, 'displayAdminOrderCreateExtraButtons', 'Add buttons on the create order page dropdown', 'Add buttons on the create order page dropdown', '1'),
  (NULL, 'actionProductFormBuilderModifier', 'Modify product identifiable object form', 'This hook allows to modify product identifiable object form content by modifying form builder data or FormBuilder itself', '1'),
  (NULL, 'actionBeforeUpdateProductFormHandler', 'Modify product identifiable object data before updating it', 'This hook allows to modify product identifiable object form data before it was updated', '1'),
  (NULL, 'actionAfterUpdateProductFormHandler', 'Modify product identifiable object data after updating it', 'This hook allows to modify product identifiable object form data before it was updated', '1'),
  (NULL, 'actionBeforeCreateProductFormHandler', 'Modify product identifiable object data before creating it', 'This hook allows to modify product identifiable object form data before it was created', '1'),
  (NULL, 'actionAfterCreateProductFormHandler', 'Modify product identifiable object data after creating it', 'This hook allows to modify product identifiable object form data after it was created', '1')
;

ALTER TABLE `PREFIX_employee` ADD `has_enabled_gravatar` TINYINT UNSIGNED DEFAULT 0 NOT NULL;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
    ('PS_COOKIE_SAMESITE', 'Lax', NOW(), NOW()),
    ('PS_SHOW_LABEL_OOS_LISTING_PAGES', '1', NOW(), NOW()),
    ('ADDONS_API_MODULE_CHANNEL', 'stable', NOW(), NOW())
;

ALTER TABLE `PREFIX_hook` ADD `active` TINYINT(1) UNSIGNED DEFAULT 1 NOT NULL AFTER `description`;

ALTER TABLE `PREFIX_orders` ADD COLUMN `note` TEXT AFTER `date_upd`;

ALTER TABLE `PREFIX_currency` CHANGE `numeric_iso_code` `numeric_iso_code` varchar(3) NULL DEFAULT NULL;

UPDATE `PREFIX_configuration` SET `value` = '4' WHERE `name` = 'PS_LOGS_BY_EMAIL' AND `value` = '5';
ALTER TABLE `PREFIX_log`
  ADD `id_shop` INT(10) NULL DEFAULT NULL after `object_id`,
  ADD `id_shop_group` INT(10) NULL DEFAULT NULL after `id_shop`,
  ADD `id_lang` INT(10) NULL DEFAULT NULL after `id_shop_group`,
  ADD `in_all_shops` TINYINT(1) unsigned NOT NULL DEFAULT '0'
;

ALTER TABLE `PREFIX_tab` ADD `wording` VARCHAR(255) DEFAULT NULL AFTER `icon`;
ALTER TABLE `PREFIX_tab` ADD `wording_domain` VARCHAR(255) DEFAULT NULL AFTER `wording`;

UPDATE `PREFIX_product` SET `location` = '' WHERE `location` IS NULL;
ALTER TABLE `PREFIX_product` MODIFY COLUMN `location` VARCHAR(255) NOT NULL DEFAULT '';
UPDATE `PREFIX_product_attribute` SET `location` = '' WHERE `location` IS NULL;
ALTER TABLE `PREFIX_product_attribute` MODIFY COLUMN `location` VARCHAR(255) NOT NULL DEFAULT '';

UPDATE `PREFIX_product` SET `redirect_type` = '404' WHERE `redirect_type` = '';
ALTER TABLE `PREFIX_product` MODIFY COLUMN `redirect_type` ENUM(
    '404', '301-product', '302-product', '301-category', '302-category'
) NOT NULL DEFAULT '404';

ALTER TABLE  `PREFIX_product` ADD `product_type` ENUM(
    'standard', 'pack', 'virtual', 'combinations'
) NOT NULL DEFAULT 'standard';

/* First set all products to standard type, then update them based on cached columns that identify the type */
UPDATE `PREFIX_product` SET `product_type` = "standard";
UPDATE `PREFIX_product` SET `product_type` = "combinations" WHERE `cache_default_attribute` != 0;
UPDATE `PREFIX_product` SET `product_type` = "pack" WHERE `cache_is_pack` = 1;
UPDATE `PREFIX_product` SET `product_type` = "virtual" WHERE `is_virtual` = 1;

/* PHP:ps_1780_add_feature_flag_tab(); */;

/* this table should be created by Doctrine but we need to perform INSERT and the 1.7.8.0.sql script is called
before Doctrine schema update */
/* consequently we create the table manually */
CREATE TABLE IF NOT EXISTS `PREFIX_feature_flag` (
  `id_feature_flag` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(191) COLLATE utf8mb4_general_ci NOT NULL,
  `state` TINYINT(1) NOT NULL DEFAULT '0',
  `label_wording` VARCHAR(191) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `label_domain` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `description_wording` VARCHAR(191) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `description_domain` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id_feature_flag`),
  UNIQUE KEY `UNIQ_91700F175E237E06` (`name`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `PREFIX_feature_flag` (`name`, `state`, `label_wording`, `label_domain`, `description_wording`, `description_domain`)
VALUES
	('product_page_v2', 0, 'Experimental product page', 'Admin.Advparameters.Feature', 'This page benefits from increased performance and includes new features such as a new combination management system. Please note this is a work in progress and some features are not available yet.', 'Admin.Advparameters.Help');