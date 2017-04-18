
ALTER TABLE  `PREFIX_stock_available` ADD  `physical_quantity` INT NOT NULL DEFAULT  '0' AFTER  `quantity`;
ALTER TABLE  `PREFIX_stock_available` ADD  `reserved_quantity` INT NOT NULL DEFAULT  '0' AFTER  `physical_quantity`;

UPDATE `PREFIX_configuration` SET `value` = 0 WHERE `name` = "PS_ADVANCED_STOCK_MANAGEMENT";
/* PHP:add_new_status_stock(); */;

INSERT INTO `PREFIX_tab` (`id_tab`, `id_parent`, `position`, `module`, `class_name`, `active`, `hide_host_mode`, `icon`) VALUES
(null, 9, 7, NULL, 'AdminStockManagement', 1, 0, '');
