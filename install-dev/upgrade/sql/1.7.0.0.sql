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
