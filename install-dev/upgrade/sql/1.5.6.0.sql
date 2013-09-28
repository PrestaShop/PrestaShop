ALTER TABLE `PREFIX_manufacturer_lang` CHANGE `short_description` `short_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

/* PHP:add_module_to_hook(blockcart, actionCartListOverride); */;
/* PHP:add_module_to_hook(blockmanufacturer, actionObjectManufacturerDeleteAfter); */;
/* PHP:add_module_to_hook(blockmanufacturer, actionObjectManufacturerAddAfter); */;
/* PHP:add_module_to_hook(blockmanufacturer, actionObjectManufacturerUpdateAfter); */;
/* PHP:add_module_to_hook(blocksupplier, actionObjectSupplierDeleteAfter); */;
/* PHP:add_module_to_hook(blocksupplier, actionObjectSupplierAddAfter); */;
/* PHP:add_module_to_hook(blocksupplier, actionObjectSupplierUpdateAfter); */;
/* PHP:add_module_to_hook(blockmyaccount, actionModuleRegisterHookAfter); */;
/* PHP:add_module_to_hook(blockmyaccountfooter, actionModuleRegisterHookAfter); */;
/* PHP:add_module_to_hook(blockmyaccount, actionModuleUnRegisterHookAfter); */;
/* PHP:add_module_to_hook(blockmyaccountfooter, actionModuleUnRegisterHookAfter); */;
/* PHP:remove_tab(AdminRangePrice); */;
/* PHP:remove_tab(AdminRangeWeight); */;


/* Add support for delivery system */;
ALTER TABLE `PREFIX_order_state` ADD `package` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `PREFIX_order_invoice` ADD `package` INT( 11 ) NOT NULL;
