SET SESSION sql_mode = '';
SET NAMES 'utf8';

UPDATE `PREFIX_address_format` SET `format` = 'firstname lastname
company
vat_number
address1
address2
city
postcode
State:name
Country:name
phone' WHERE `id_country` = (SELECT `id_country` FROM `PREFIX_country` WHERE `iso_code` = 'IN');

UPDATE `PREFIX_hook` SET `name` = 'displayProductAdditionalInfo' WHERE `name` = 'displayProductButtons';
INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES ('displayProductAdditionalInfo', 'displayProductButtons');

-- Need old value before updating
ALTER TABLE `PREFIX_product` CHANGE `redirect_type` `redirect_type`
  ENUM('','404',
  '301', '302',
  '301-product','302-product','301-category','302-category')
  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

ALTER TABLE `PREFIX_product_shop` CHANGE `redirect_type` `redirect_type`
  ENUM('','404',
  '301', '302',
  '301-product','302-product','301-category','302-category')
  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

UPDATE `PREFIX_product` SET redirect_type = '301-product' WHERE redirect_type = '301';
UPDATE `PREFIX_product` SET redirect_type = '302-product' WHERE redirect_type = '302';

UPDATE `PREFIX_product_shop` SET redirect_type = '301-product' WHERE redirect_type = '301';
UPDATE `PREFIX_product_shop` SET redirect_type = '302-product' WHERE redirect_type = '302';

-- Can now remove old value
ALTER TABLE `PREFIX_product` CHANGE `redirect_type` `redirect_type`
  ENUM('','404','301-product','302-product','301-category','302-category')
  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

ALTER TABLE `PREFIX_product_shop` CHANGE `redirect_type` `redirect_type`
  ENUM('','404','301-product','302-product','301-category','302-category')
  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

ALTER TABLE `PREFIX_product` CHANGE `id_product_redirected` `id_type_redirected` INT(10) NOT NULL DEFAULT '0';
ALTER TABLE `PREFIX_product_shop` CHANGE `id_product_redirected` `id_type_redirected` INT(10) NOT NULL DEFAULT '0';

INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES
  (NULL, 'filterCmsContent', 'Filter the content page', 'This hook is called just before fetching content page', '1'),
  (NULL, 'filterCmsCategoryContent', 'Filter the content page category', 'This hook is called just before fetching content page category', '1'),
  (NULL, 'filterProductContent', 'Filter the content page product', 'This hook is called just before fetching content page product', '1'),
  (NULL, 'filterCategoryContent', 'Filter the content page category', 'This hook is called just before fetching content page category', '1'),
  (NULL, 'filterManufacturerContent', 'Filter the content page manufacturer', 'This hook is called just before fetching content page manufacturer', '1'),
  (NULL, 'filterSupplierContent', 'Filter the content page supplier', 'This hook is called just before fetching content page supplier', '1'),
  (NULL, 'filterHtmlContent', 'Filter HTML field before rending a page', 'This hook is called just before fetching a page on HTML field', '1'),
  (NULL, 'displayDashboardTop', 'Dashboard Top', 'Displays the content in the dashboard''s top area', '1'),
  (NULL, 'actionObjectProductInCartDeleteBefore', 'Cart product removal', 'This hook is called before a product is removed from a cart', '1'),
  (NULL, 'actionObjectProductInCartDeleteAfter', 'Cart product removal', 'This hook is called after a product is removed from a cart', '1'),
  (NULL, 'actionUpdateLangAfter', 'Update "lang" tables', 'Update "lang" tables after adding or updating a language', '1'),
  (NULL, 'actionOutputHTMLBefore', 'Filter the whole HTML page', 'This hook is used to filter the whole HTML page before it is rendered (only front)', '1'),
  (NULL, 'displayAfterProductThumbs', 'Display extra content below product thumbs', 'This hook displays new elements below product images ex. additional media', '1'),
  (NULL, 'actionDispatcherBefore', 'Before dispatch', 'This hook is called at the beginning of the dispatch method of the Dispatcher', '1'),
  (NULL, 'actionDispatcherAfter', 'After dispatch', 'This hook is called at the end of the dispatch method of the Dispatcher', '1'),
  (NULL, 'actionClearCache', 'Clear smarty cache', 'This hook is called when the cache of the theme is cleared', '1'),
  (NULL, 'actionClearCompileCache', 'Clear smarty compile cache', 'This hook is called when smarty''s compile cache is cleared', '1'),
  (NULL, 'actionClearSf2Cache', 'Clear Sf2 cache', 'This hook is called when the Symfony cache is cleared', '1'),
  (NULL, 'filterProductSearch', 'Filter search products result', 'This hook is called in order to allow to modify search product result', '1'),
  (NULL, 'actionProductSearchAfter', 'Event triggered after search product completed', 'This hook is called after the product search. Parameters are already filtered', '1'),
  (NULL, 'actionEmailSendBefore', 'Before sending an email', 'This hook is used to filter the content or the metadata of an email before sending it or even prevent its sending', '1'),
  (NULL, 'displayProductPageDrawer', 'Product Page Drawer', 'This hook displays content in the right sidebar of the product page', '1');

DELETE FROM `PREFIX_configuration` WHERE `name` IN ('PS_META_KEYWORDS');

INSERT INTO `PREFIX_operating_system` (`name`) VALUES ('Windows 8.1'), ('Windows 10');

/* UPDATE TO DOCTRINE */
ALTER TABLE `PREFIX_attribute` CHANGE `id_attribute` `id_attribute` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `PREFIX_attribute` CHANGE `id_attribute_group` `id_attribute_group` INT(11) NOT NULL;

ALTER TABLE `PREFIX_attribute_group` CHANGE `id_attribute_group` `id_attribute_group` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `PREFIX_attribute_group_lang` CHANGE `id_attribute_group` `id_attribute_group` INT(11) NOT NULL;
ALTER TABLE `PREFIX_attribute_group_lang` CHANGE `id_lang` `id_lang` INT(11) NOT NULL;

ALTER TABLE `PREFIX_attribute_group_shop` CHANGE `id_attribute_group` `id_attribute_group` INT(11) NOT NULL;
ALTER TABLE `PREFIX_attribute_group_shop` CHANGE `id_shop` `id_shop` INT(11) NOT NULL;

ALTER TABLE `PREFIX_attribute_lang` CHANGE `id_attribute` `id_attribute` INT(11) NOT NULL;
ALTER TABLE `PREFIX_attribute_lang` CHANGE `id_lang` `id_lang` INT(11) NOT NULL;

ALTER TABLE `PREFIX_attribute_shop` CHANGE `id_attribute` `id_attribute` INT(11) NOT NULL;
ALTER TABLE `PREFIX_attribute_shop` CHANGE `id_shop` `id_shop` INT(11) NOT NULL;

ALTER TABLE `PREFIX_lang` CHANGE `id_lang` `id_lang` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `PREFIX_lang_shop` CHANGE `id_lang` `id_lang` INT(11) NOT NULL;
ALTER TABLE `PREFIX_lang_shop` CHANGE `id_shop` `id_shop` INT(11) NOT NULL;

ALTER TABLE `PREFIX_shop` CHANGE `id_shop` `id_shop` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `PREFIX_shop` CHANGE `id_shop_group` `id_shop_group` INT(11) NOT NULL;

ALTER TABLE `PREFIX_shop_group` CHANGE `id_shop_group` `id_shop_group` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `PREFIX_tab` CHANGE `id_tab` `id_tab` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `PREFIX_tab_lang` CHANGE `id_tab` `id_tab` INT(11) NOT NULL;
ALTER TABLE `PREFIX_tab_lang` CHANGE `id_lang` `id_lang` INT(11) NOT NULL;
