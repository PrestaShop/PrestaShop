SET NAMES 'utf8';

ALTER TABLE `PREFIX_state` CHANGE `iso_code` `iso_code` varchar(7) NOT NULL;

DROP TABLE `PREFIX_accounting_export`;
DROP TABLE `PREFIX_accounting_zone_shop`;
DROP TABLE `PREFIX_accounting_product_zone_shop`;
ALTER TABLE `PREFIX_tax` DROP `account_number`;
ALTER TABLE `PREFIX_customer` DROP `account_number`;

/* PHP:move_translations_module_file(); */;

ALTER TABLE `PREFIX_tax_rule` CHANGE `zipcode_from` `zipcode_from` VARCHAR(12) NOT NULL, CHANGE `zipcode_to` `zipcode_to` VARCHAR(12) NOT NULL;

