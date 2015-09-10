SET NAMES 'utf8';

INSERT INTO `PREFIX_configuration` (`name` , `value` , `date_add` , `date_upd`) VALUES ('PS_SMARTY_LOCAL', '0', NOW(), NOW());

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

ALTER TABLE `PREFIX_product` ADD `show_condition` TINYINT(1) NOT NULL DEFAULT '1' AFTER `available_date`;
ALTER TABLE `PREFIX_product_shop` ADD `show_condition` TINYINT(1) NOT NULL DEFAULT '1' AFTER `available_date`;


/* Add Payment Preferences tab. SuperAdmin profile is the only one to access it. */
/* PHP:ps_1701_add_payment_preferences_tab(); */;
UPDATE `PREFIX_access` SET `view` = '0', `add` = '0', `edit` = '0', `delete` = '0' WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.`class_name` = 'AdminPaymentPreferences' LIMIT 1) AND `id_profile` > 1;

UPDATE `PREFIX_quick_access` SET `link` = "product/new" WHERE `link` = "index.php?controller=AdminProducts&addproduct";

ALTER TABLE  `PREFIX_product_lang` ADD  `social_sharing_title` VARCHAR( 255 ) NOT NULL;
ALTER TABLE  `PREFIX_product_lang` ADD  `social_sharing_description` VARCHAR( 255 ) NOT NULL;

/* PHP:ps1700_stores(); */
