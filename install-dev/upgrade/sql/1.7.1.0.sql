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

INSERT INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES
  (NULL, 'filteredCmsContent', 'Filter the content page', 'This hook is called just before fetching content page.', '1'),
  (NULL, 'filteredCmsCategoryContent', 'Filter the content page category', 'This hook is called just before fetching content page category.', '1'),
  (NULL, 'filteredProductContent', 'Filter the content page product', 'This hook is called just before fetching content page product.', '1'),
  (NULL, 'filteredCategoryContent', 'Filter the content page category', 'This hook is called just before fetching content page category.', '1'),
  (NULL, 'filteredManufacturerContent', 'Filter the content page manufacturer', 'This hook is called just before fetching content page manufacturer.', '1'),
  (NULL, 'filteredSupplierContent', 'Filter the content page supplier', 'This hook is called just before fetching content page supplier.', '1'),
  (NULL, 'filteredHtmlContent', 'Filter HTML field before rending a page', 'This hook is called just before fetching a page on HTML field.', '1'),
  (NULL, 'dashboardTop', 'Dashboard Top', 'Displays the content in the dashboard''s top area.', '1'),
  (NULL, 'actionObjectProductInCartDeleteBefore', 'Cart product removal', 'This hook is called before a product is removed from a cart', '1'),
  (NULL, 'actionObjectProductInCartDeleteAfter', 'Cart product removal', 'This hook is called after a product is removed from a cart', '1'),
  (NULL, 'actionUpdateLangAfter', 'Update "lang" tables', 'Update "lang" tables after adding or updating a language.', '1');

DELETE FROM `PREFIX_configuration` WHERE `name` IN ('PS_META_KEYWORDS');
