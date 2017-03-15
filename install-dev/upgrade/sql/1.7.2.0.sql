
ALTER TABLE  `PREFIX_stock_available` ADD  `physical_quantity` INT NOT NULL DEFAULT  '0' AFTER  `quantity`;
ALTER TABLE  `PREFIX_stock_available` ADD  `reserved_quantity` INT NOT NULL DEFAULT  '0' AFTER  `quantity`;
