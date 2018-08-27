SET SESSION sql_mode = '';
SET NAMES 'utf8';

/* PHP:add_supplier_manufacturer_routes(); */;

/* PHP:ps_1750_update_module_tabs(); */;

ALTER TABLE `PREFIX_product` CHANGE `location` `location` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `PREFIX_product_attribute` CHANGE `location` `location` VARCHAR(255) NULL DEFAULT NULL;
