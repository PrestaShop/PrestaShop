SET SESSION sql_mode = '';
SET NAMES 'utf8';

ALTER TABLE  `PREFIX_available` ADD  `physical_quantity` INT NOT NULL DEFAULT  '0' AFTER  `quantity`;
ALTER TABLE  `PREFIX_available` ADD  `reserved_quantity` INT NOT NULL DEFAULT  '0' AFTER  `quantity`;
