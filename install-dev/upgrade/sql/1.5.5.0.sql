SET NAMES 'utf8';

ALTER TABLE  `PREFIX_store` CHANGE  `latitude`  `latitude` DECIMAL( 13, 8 ) NULL DEFAULT NULL , CHANGE  `longitude`  `longitude` DECIMAL( 13, 8 ) NULL DEFAULT NULL ;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES('PS_CUSTOMER_CREATION_EMAIL', 1, NOW(), NOW());

ALTER TABLE `PREFIX_webservice_account` CHANGE `class_name` `class_name` VARCHAR(64) NOT NULL DEFAULT 'WebserviceRequest',
CHANGE `module_name` `module_name` VARCHAR(64) NULL DEFAULT NULL;

/* PHP:add_module_to_hook(blockcart, actionCartListOverride); */;
/* PHP:add_module_to_hook(blockmanufacturer, actionObjectManufacturerDeleteAfter); */;
/* PHP:add_module_to_hook(blockmanufacturer, actionObjectManufacturerAddAfter); */;
/* PHP:add_module_to_hook(blockmanufacturer, actionObjectManufacturerUpdateAfter); */;
/* PHP:add_module_to_hook(blocksupplier, actionObjectSupplierDeleteAfter); */;
/* PHP:add_module_to_hook(blocksupplier, actionObjectSupplierAddAfter); */;
/* PHP:add_module_to_hook(blocksupplier, actionObjectSupplierUpdateAfter); */;
/* PHP:fix_download_product_feature_active(); */;
/* PHP:add_module_to_hook(blockmyaccount, actionModuleRegisterHookAfter); */;
/* PHP:add_module_to_hook(blockmyaccountfooter, actionModuleRegisterHookAfter); */;
/* PHP:add_module_to_hook(blockmyaccount, actionModuleUnRegisterHookAfter); */;
/* PHP:add_module_to_hook(blockmyaccountfooter, actionModuleUnRegisterHookAfter); */;
/* PHP:remove_tab(AdminRangePrice); */;
/* PHP:remove_tab(AdminRangeWeight); */;

/* PHP:remove_tab(AdminShipping); */;
/* PHP:remove_tab(AdminParentShipping); */;
/* PHP:remove_tab(AdminCarriers); */;

/* PHP:add_new_tab(AdminShipping, es:Shipping|it:Shipping|en:Shipping|de:Shipping|fr:Transport, 0, false, AdminParentPreferences); */;
/* PHP:add_new_tab(AdminCarriers, es:Carriers|it:Carriers|en:Carriers|de:Carriers|fr:Tarsporteurs, 0, false, AdminParentModules); */;


ALTER TABLE `PREFIX_log` ADD `id_employee` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `object_id`;

SET @id_parent = (SELECT IFNULL(id_tab, 1) FROM `PREFIX_tab` WHERE `class_name` = 'AdminPriceRule' LIMIT 1);
UPDATE `PREFIX_tab` SET id_parent = @id_parent WHERE `id_parent` = 1 AND `class_name` = 'AdminMarketing' LIMIT 1;

UPDATE `PREFIX_hook` SET `description` = 'This hook is called when a new credit slip is added regarding client order' WHERE `name` = 'actionOrderSlipAdd';

ALTER TABLE `PREFIX_product_shop` DROP INDEX `date_add`, ADD INDEX `date_add` (`date_add` , `active` , `visibility`);

UPDATE `PREFIX_hook` SET `live_edit` = '1' WHERE `name` LIKE 'leftcolumn';

UPDATE `PREFIX_configuration` SET `name` = '0' WHERE `name` LIKE 'PS_LEGACY_IMAGES' AND `value` LIKE '1';

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES('PS_SMARTY_CONSOLE_KEY', 'SMARTY_DEBUG', NOW(), NOW());
