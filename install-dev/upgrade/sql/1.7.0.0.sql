SET NAMES 'utf8';

INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES ('actionCartUpdateQuantityBefore', 'actionBeforeCartUpdateQty');
INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES ('actionAjaxDieBefore', 'actionBeforeAjaxDie');
INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES ('actionAuthenticationBefore', 'actionBeforeAuthentication');
INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES ('actionSubmitAccountBefore', 'actionBeforeSubmitAccount');
INSERT INTO `PREFIX_hook_alias` (`name`, `alias`) VALUES ('actionDeleteProductInCartAfter', 'actionAfterDeleteProductInCart');

ALTER TABLE  `PREFIX_currency` DROP  `iso_code_num` ,DROP  `sign` ,DROP  `blank` ,DROP  `format` ,DROP  `decimals` ;