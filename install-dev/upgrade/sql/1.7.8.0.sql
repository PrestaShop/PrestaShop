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
  (NULL, 'displayCartModalFooter', 'Cart Presenter', 'This hook displays content in the bottom of window that appears after adding product to cart', '1')
  (NULL, 'actionCheckoutRender', 'Checkout process render', 'This hook is called when checkout process is constructed', '1'),
  (NULL, 'actionPresentProductListing', 'Product Listing Presenter', 'This hook is called before a product listing is presented', '1'),
  (NULL, 'actionGetProductPropertiesAfterUnitPrice', 'Product Properties', 'This hook is called after defining the properties of a product', '1'),
  (NULL, 'actionProductSearchProviderRunQueryBefore', 'Runs an action before ProductSearchProviderInterface::RunQuery()', 'Required to modify an SQL query before executing it', '1'),
  (NULL, 'actionProductSearchProviderRunQueryAfter', 'Runs an action after ProductSearchProviderInterface::RunQuery()', 'Required to return a previous state of an SQL query or/and to change a result of the SQL query after executing it', '1'),
  (NULL, 'actionOverrideEmployeeImage', 'Override Employee Image', 'This hook is used to override the employee image', '1')
;

ALTER TABLE `PREFIX_employee` ADD `has_enabled_gravatar` TINYINT UNSIGNED DEFAULT 0 NOT NULL;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
    ('PS_COOKIE_SAMESITE', 'Lax', NOW(), NOW())
;

ALTER TABLE `PREFIX_hook` ADD `active` TINYINT(1) UNSIGNED DEFAULT 1 NOT NULL AFTER `description`;

ALTER TABLE `PREFIX_orders` ADD COLUMN `note` TEXT AFTER `date_upd`;

ALTER TABLE `PREFIX_currency` CHANGE `numeric_iso_code` `numeric_iso_code` varchar(3) NULL DEFAULT NULL;

ALTER TABLE `PREFIX_product_attribute` ADD `date_add` DATETIME NOT NULL DEFAULT NOW() AFTER `available_date`;
ALTER TABLE `PREFIX_product_attribute` ADD `date_upd` DATETIME NOT NULL DEFAULT NOW() AFTER `date_add`;

ALTER TABLE `PREFIX_product_attribute_shop` ADD `date_add` DATETIME NOT NULL DEFAULT NOW() AFTER `available_date`;
ALTER TABLE `PREFIX_product_attribute_shop` ADD `date_upd` DATETIME NOT NULL DEFAULT NOW() AFTER `date_add`;
