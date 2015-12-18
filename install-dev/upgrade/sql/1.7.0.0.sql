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

ALTER TABLE  `PREFIX_currency` DROP  `iso_code_num` ,DROP  `sign` ,DROP  `blank` ,DROP  `format` ,DROP  `decimals` ;

/* Password reset token for new "Forgot my password screen */
ALTER TABLE PREFIX_customer ADD `reset_password_token` varchar(40) DEFAULT NULL;
ALTER TABLE PREFIX_customer ADD `reset_password_validity` datetime DEFAULT NULL;
ALTER TABLE PREFIX_employee ADD `reset_password_token` varchar(40) DEFAULT NULL;
ALTER TABLE PREFIX_employee ADD `reset_password_validity` datetime DEFAULT NULL;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_PASSWD_RESET_VALIDITY', '1440', NOW(), NOW());

ALTER TABLE `PREFIX_customer` CHANGE COLUMN `passwd` `passwd` varchar(255) NOT NULL;

INSERT INTO  `PREFIX_configuration` (`id_configuration` ,`id_shop_group` ,`id_shop` ,`name` ,`value` ,`date_add` ,`date_upd`) VALUES (NULL , NULL , NULL ,  'PS_ACTIVE_CRONJOB_EXCHANGE_RATE',  '0',  '0000-00-00 00:00:00',  '0000-00-00 00:00:00');

ALTER TABLE `PREFIX_customer` CHANGE COLUMN `firstname` `firstname` varchar(255) NOT NULL;
ALTER TABLE `PREFIX_customer` CHANGE COLUMN `lastname` `lastname` varchar(255) NOT NULL;

ALTER TABLE  `PREFIX_product` ADD  `isbn` VARCHAR( 13 ) NULL DEFAULT NULL;
ALTER TABLE  `PREFIX_order_detail` ADD  `product_isbn` VARCHAR( 13 ) NULL DEFAULT NULL;
ALTER TABLE  `PREFIX_product_attribute` ADD  `isbn` VARCHAR( 13 ) NULL DEFAULT NULL;
ALTER TABLE  `PREFIX_stock` ADD  `isbn` VARCHAR( 13 ) NULL DEFAULT NULL;
ALTER TABLE  `PREFIX_supply_order_detail` ADD  `isbn` VARCHAR( 13 ) NULL DEFAULT NULL;

ALTER TABLE  `PREFIX_product_lang` ADD  `social_sharing_title` VARCHAR( 255 ) NOT NULL;
ALTER TABLE  `PREFIX_product_lang` ADD  `social_sharing_description` VARCHAR( 255 ) NOT NULL;

/* PHP:ps1700_stores(); */

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_PASSWD_RESET_VALIDITY', '1440', NOW(), NOW());

ALTER TABLE `PREFIX_hook` DROP `live_edit`;

/* Remove comparator feature */
DELETE FROM `PREFIX_hook_alias` WHERE `name` = 'displayProductComparison';
DELETE FROM `PREFIX_hook` WHERE `name` = 'displayProductComparison';
DELETE FROM `PREFIX_configuration` WHERE `name` = 'PS_COMPARATOR_MAX_ITEM';
DELETE FROM `PREFIX_meta` WHERE `page` = 'products-comparison';
DROP TABLE IF EXISTS PREFIX_compare;
DROP TABLE IF EXISTS PREFIX_compare_product;

ALTER TABLE `PREFIX_cart` ADD `checkout_session_data` MEDIUMTEXT NULL
