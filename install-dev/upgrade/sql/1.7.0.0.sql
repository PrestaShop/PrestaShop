SET NAMES 'utf8';

INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES ('actionCartUpdateQuantityBefore', 'actionBeforeCartUpdateQty');
INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES ('actionAjaxDieBefore', 'actionBeforeAjaxDie');
INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES ('actionAuthenticationBefore', 'actionBeforeAuthentication');
INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES ('actionSubmitAccountBefore', 'actionBeforeSubmitAccount');
INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES ('actionDeleteProductInCartAfter', 'actionAfterDeleteProductInCart');

ALTER TABLE  `PREFIX_currency` DROP  `iso_code_num` ,DROP  `sign` ,DROP  `blank` ,DROP  `format` ,DROP  `decimals` ;

/* Password reset token for new "Forgot my password" screen */
CREATE TABLE IF NOT EXISTS `PREFIX_reset_token` (
  `id_reset_token` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_customer` int(10) unsigned NULL DEFAULT 0,
  `secure_key` varchar(32) DEFAULT NULL,
  `id_employee` int(10) unsigned NULL DEFAULT 0,
  `unique_token` varchar(40) DEFAULT NULL,
  `last_token_gen` datetime NOT NULL,
  `validity_date` datetime NOT NULL,
  PRIMARY KEY (`id_reset_token`),
  KEY (`id_customer`),
  KEY (`id_customer`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
