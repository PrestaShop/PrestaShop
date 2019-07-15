SET SESSION sql_mode = '';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_tab` ADD `route_name` varchar(256) DEFAULT NULL AFTER `class_name`;
