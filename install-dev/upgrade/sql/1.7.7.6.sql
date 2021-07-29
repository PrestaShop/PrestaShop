SET SESSION sql_mode='';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_currency` MODIFY COLUMN `numeric_iso_code` varchar(3) DEFAULT NULL NULL;
