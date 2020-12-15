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
  (NULL, 'actionCheckoutRender', 'Checkout process render', 'This hook is called when checkout process is constructed', '1'),
  (NULL, 'actionPresentProductListing', 'Product Listing Presenter', 'This hook is called before a product listing is presented', '1'),
  (NULL, 'actionGetProductPropertiesAfterUnitPrice', 'Product Properties', 'This hook is called after defining the properties of a product', '1'),
  (NULL, 'actionProductSearchProviderRunQueryBefore', 'Runs an action before ProductSearchProviderInterface::RunQuery()', 'Required to modify an SQL query before executing it', '1'),
  (NULL, 'actionProductSearchProviderRunQueryAfter', 'Runs an action after ProductSearchProviderInterface::RunQuery()', 'Required to return a previous state of an SQL query or/and to change a result of the SQL query after executing it', '1'),
  (NULL, 'actionOverrideEmployeeImage', 'Override Employee Image', 'This hook is used to override the employee image', '1'),
  (NULL, 'actionFrontControllerSetVariables', 'Add variables in JavaScript object and Smarty templates', 'Add variables to javascript object that is available in Front Office. These are also available in smarty templates in modules.your_module_name.', '1')
;

ALTER TABLE `PREFIX_employee` ADD `has_enabled_gravatar` TINYINT UNSIGNED DEFAULT 0 NOT NULL;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
    ('PS_COOKIE_SAMESITE', 'Lax', NOW(), NOW())
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
