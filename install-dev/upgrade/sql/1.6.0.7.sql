SET NAMES 'utf8';

ALTER TABLE `PREFIX_customer_message` CHANGE `ip_address` `ip_address` VARCHAR( 16 ) NULL DEFAULT NULL;

UPDATE `PREFIX_theme` SET product_per_page = '12' WHERE `product_per_page` = 0;

UPDATE `PREFIX_hook` SET  live_edit = '1' WHERE `name` IN('displayTop','displayAttributeForm','displayAttributeGroupForm','displayBeforeCarrier',
'displayBeforePayment','displayCarrierList','displayCustomerAccount','displayCustomerAccountForm','displayCustomerAccountFormTop','displayFeatureForm',
'displayFeatureValueForm','displayFooter','displayLeftColumnProduct','displayMyAccountBlock','displayMyAccountBlockfooter','displayOrderConfirmation',
'displayOrderDetail','displayPaymentReturn','displayPaymentTop','displayProductButtons','displayProductComparison','displayProductListFunctionalButtons',
'displayProductTab','displayProductTabContent','displayRightColumnProduct','displayShoppingCart','displayShoppingCartFooter');

ALTER TABLE `PREFIX_order_detail_tax` DROP PRIMARY KEY, ADD INDEX id_order_detail (`id_order_detail`);

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES('PS_DISPLAY_BEST_SELLERS', '1', NOW(), NOW());

INSERT INTO `PREFIX_hook` (`id_hook` , `name` , `title` , `description` , `position` , `live_edit`)
VALUES (NULL , 'displayAdminOrderTabOrder', 'Display new elements in the Back Office, tab AdminOrder, panel Order', 'This hook launches modules when the AdminOrder tab is displayed in the Back Office and extends / override Order panel tabs', '1', '0'),
(NULL , 'displayAdminOrderContentOrder', 'Display new elements in the Back Office, tab AdminOrder, panel Order', 'This hook launches modules when the AdminOrder tab is displayed in the Back Office and extends / override Order panel content', '1', '0'),
(NULL , 'displayAdminOrderTabShip', 'Display new elements in the Back Office, tab AdminOrder, panel Shipping', 'This hook launches modules when the AdminOrder tab is displayed in the Back Office and extends / override Shipping panel tabs', '1', '0'),
(NULL , 'displayAdminOrderContentShip', 'Display new elements in the Back Office, tab AdminOrder, panel Shipping', 'This hook launches modules when the AdminOrder tab is displayed in the Back Office and extends / override Shipping panel content', '1', '0');
