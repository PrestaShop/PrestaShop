SET NAMES 'utf8';

UPDATE `PREFIX_meta` SET `page` = 'supplier' WHERE `page` = 'supply';

ALTER TABLE  `PREFIX_image_type` CHANGE  `name`  `name` VARCHAR( 64 ) NOT NULL;

ALTER TABLE `PREFIX_customer` ADD `id_lang` INT UNSIGNED NULL AFTER `id_default_group`;
UPDATE `PREFIX_customer` SET id_lang = (SELECT `value` FROM `PREFIX_configuration` WHERE name = 'PS_LANG_DEFAULT' LIMIT 1);
UPDATE `PREFIX_customer` c, `PREFIX_orders` o SET c.id_lang = o.id_lang WHERE c.id_customer = o.id_customer;

UPDATE `PREFIX_quick_access` SET `link` = 'index.php?controller=AdminCartRules&addcart_rule' WHERE `link` = 'index.php?tab=AdminDiscounts&adddiscount';

