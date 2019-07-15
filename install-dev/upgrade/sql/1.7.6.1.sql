SET SESSION sql_mode = '';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_admin_filter`
	ADD `filter_id` VARCHAR (255) DEFAULT '' NOT NULL AFTER `shop`;