SET NAMES 'utf8';

ALTER TABLE `PREFIX_state` CHANGE `iso_code` `iso_code` varchar(7) NOT NULL;

DROP TABLE `PREFIX_accounting_export`;
DROP TABLE `PREFIX_accounting_zone_shop`;
DROP TABLE `PREFIX_accounting_product_zone_shop`;
ALTER TABLE `PREFIX_tax` DROP `account_number`;
ALTER TABLE `PREFIX_customer` DROP `account_number`;

/* PHP:move_translations_module_file(); */;

ALTER TABLE `PREFIX_tax_rule` CHANGE `zipcode_from` `zipcode_from` VARCHAR(12) NOT NULL, CHANGE `zipcode_to` `zipcode_to` VARCHAR(12) NOT NULL;

UPDATE PREFIX_order_detail_tax odt
LEFT JOIN PREFIX_tax t ON (t.id_tax = odt.id_tax)
SET unit_amount = ROUND((t.rate / 100) * (
		SELECT od.unit_price_tax_excl - ( o.total_discounts_tax_excl * ( od.unit_price_tax_excl / o.total_products ))
		FROM PREFIX_order_detail od
		LEFT JOIN PREFIX_orders o ON ( o.id_order = od.id_order)
		WHERE odt.id_order_detail = od.id_order_detail
), 2);


UPDATE PREFIX_order_detail_tax odt
LEFT JOIN PREFIX_order_detail od ON (od.id_order_detail = odt.id_order_detail)
SET total_amount = odt.unit_amount * od.product_quantity;

ALTER TABLE `PREFIX_pagenotfound` ADD `id_shop_group` INT(10) AFTER `id_pagenotfound`;
ALTER TABLE `PREFIX_pagenotfound` ADD `id_shop` INT(10) AFTER `id_pagenotfound`;

/* PHP:add_new_groups('Non identifié', 'Unidentified'); */;