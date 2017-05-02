
ALTER TABLE  `PREFIX_stock_available` ADD  `physical_quantity` INT NOT NULL DEFAULT  '0' AFTER  `quantity`;
ALTER TABLE  `PREFIX_stock_available` ADD  `reserved_quantity` INT NOT NULL DEFAULT  '0' AFTER  `physical_quantity`;
ALTER TABLE `ps_stock_mvt` CHANGE `id_stock` `id_stock` INT(11) UNSIGNED NOT NULL COMMENT 'since ps 1.7 corresponding to id_stock_available';

UPDATE `PREFIX_configuration` SET `value` = 0 WHERE `name` = "PS_ADVANCED_STOCK_MANAGEMENT";
/* PHP:add_new_status_stock(); */;
