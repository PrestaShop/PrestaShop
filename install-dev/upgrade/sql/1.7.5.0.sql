SET SESSION sql_mode = '';
SET NAMES 'utf8';

/* PHP:add_supplier_manufacturer_routes(); */;

/* PHP:ps_1750_update_module_tabs(); */;

ALTER TABLE `PREFIX_cms_lang`
	ADD `head_seo_title` varchar(255) DEFAULT NULL AFTER `meta_title`;

ALTER TABLE `PREFIX_product` MODIFY `location` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `PREFIX_product_attribute` MODIFY `location` VARCHAR(255) NULL DEFAULT NULL;
