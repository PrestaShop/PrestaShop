SET SESSION sql_mode = '';
SET NAMES 'utf8';

/* PHP:add_supplier_manufacturer_routes(); */;

/* PHP:ps_1750_update_module_tabs(); */;

ALTER TABLE `PREFIX_cms_lang`
	ADD `head_seo_title` varchar(255) DEFAULT NULL AFTER `meta_title`;

ALTER TABLE `PREFIX_stock_available`
  ADD `location` VARCHAR(255) NOT NULL DEFAULT '' AFTER `out_of_stock`;

ALTER TABLE `PREFIX_store` CHANGE `email` `email` VARCHAR(255) DEFAULT NULL;

ALTER TABLE `PREFIX_contact` CHANGE `email` `email` VARCHAR(255) NOT NULL;

ALTER TABLE `PREFIX_category_lang` CHANGE `meta_title` `meta_title` VARCHAR(255) DEFAULT NULL;

ALTER TABLE `PREFIX_cms_lang` CHANGE `meta_title` `meta_title` VARCHAR(255) NOT NULL;

ALTER TABLE `PREFIX_cms_category_lang` CHANGE `meta_title` `meta_title` VARCHAR(255) DEFAULT NULL;

ALTER TABLE `PREFIX_contact` CHANGE `email` `email` VARCHAR(255) NOT NULL;

ALTER TABLE `PREFIX_customer` CHANGE `company` `company` VARCHAR(255);

ALTER TABLE `PREFIX_customer` CHANGE `email` `email` VARCHAR(255) NOT NULL;

ALTER TABLE `PREFIX_customer` CHANGE `passwd` `passwd` VARCHAR(255) NOT NULL;

ALTER TABLE `PREFIX_manufacturer_lang` CHANGE `meta_title` `meta_title` VARCHAR(255) NOT NULL;

ALTER TABLE `PREFIX_supplier_lang` CHANGE `meta_title` `meta_title` VARCHAR(255) DEFAULT NULL;

ALTER TABLE `PREFIX_employee` CHANGE `lastname` `lastname` VARCHAR(255) NOT NULL;

ALTER TABLE `PREFIX_employee` CHANGE `firstname` `firstname` VARCHAR(255) NOT NULL;

ALTER TABLE `PREFIX_employee` CHANGE `email` `email` VARCHAR(255) NOT NULL;

ALTER TABLE `PREFIX_employee` CHANGE `passwd` `passwd` VARCHAR(255) NOT NULL;

ALTER TABLE `PREFIX_referrer` CHANGE `passwd` `passwd` VARCHAR(255) DEFAULT NULL;

ALTER TABLE `PREFIX_supply_order_history` CHANGE `employee_lastname` `employee_lastname` VARCHAR(255) DEFAULT '';

ALTER TABLE `PREFIX_supply_order_history` CHANGE `employee_firstname` `employee_firstname` VARCHAR(255) DEFAULT '';

ALTER TABLE `PREFIX_supply_order_receipt_history` CHANGE `employee_firstname` `employee_firstname` VARCHAR(255) DEFAULT '';

ALTER TABLE `PREFIX_supply_order_receipt_history` CHANGE `employee_lastname` `employee_firstname` VARCHAR(255) DEFAULT '';
