
ALTER TABLE  `PREFIX_stock_available` ADD  `physical_quantity` INT NOT NULL DEFAULT  '0' AFTER  `quantity`;
ALTER TABLE  `PREFIX_stock_available` ADD  `reserved_quantity` INT NOT NULL DEFAULT  '0' AFTER  `physical_quantity`;

INSERT INTO `PREFIX_tab` (`id_tab`, `id_parent`, `position`, `module`, `class_name`, `active`, `hide_host_mode`, `icon`) VALUES
(null, 9, 7, NULL, 'AdminStockManagement', 1, 0, '');
