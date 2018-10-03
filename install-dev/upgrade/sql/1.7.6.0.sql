SET SESSION sql_mode = '';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_admin_filter`
	ADD `class_name` VARCHAR (255) DEFAULT '' NOT NULL AFTER `shop`,
	ADD INDEX `search_idx` (`class_name`, `shop`, `employee`);
