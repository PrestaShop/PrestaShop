SET NAMES 'utf8';

UPDATE `PREFIX_meta` SET `page` = 'supplier' WHERE `page` = 'supply';

ALTER TABLE  `PREFIX_image_type` CHANGE  `name`  `name` VARCHAR( 64 ) NOT NULL;

ALTER TABLE `PREFIX_customer` ADD `id_lang` INT UNSIGNED NULL AFTER `id_default_group`;
UPDATE `PREFIX_customer` SET id_lang = (SELECT `value` FROM `PREFIX_configuration` WHERE name = 'PS_LANG_DEFAULT' LIMIT 1);
UPDATE `PREFIX_customer` c, `PREFIX_orders` o SET c.id_lang = o.id_lang WHERE c.id_customer = o.id_customer;

UPDATE `PREFIX_quick_access` SET `link` = 'index.php?controller=AdminCartRules&addcart_rule' WHERE `link` = 'index.php?tab=AdminDiscounts&adddiscount';

ALTER TABLE `PREFIX_order_cart_rule` ADD `free_shipping` BOOLEAN NOT NULL DEFAULT FALSE AFTER `value_tax_excl`;

UPDATE `PREFIX_order_cart_rule` ocr, `PREFIX_cart_rule` cr SET ocr.free_shipping = 1 WHERE ocr.id_cart_rule = cr.id_cart_rule AND cr.free_shipping = 1;

UPDATE `PREFIX_orders` o, `PREFIX_order_cart_rule` ocr SET
	o.`total_discounts` = o.total_discounts + o.`total_shipping_tax_incl`,
	o.`total_discounts_tax_incl` = o.`total_discounts_tax_incl` + o.`total_shipping_tax_incl`,
	o.`total_discounts_tax_excl` = o.`total_discounts_tax_excl` + o.`total_shipping_tax_excl`
WHERE o.id_order = ocr.id_order AND ocr.free_shipping = 1;

