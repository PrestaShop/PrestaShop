SET SESSION sql_mode = '';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_admin_filter`
	ADD `unique_key` VARCHAR (255) DEFAULT '' NOT NULL AFTER `shop`,
	ADD INDEX `search_idx` (`unique_key`, `shop`, `employee`),
	DROP INDEX `admin_filter_search_idx`;
