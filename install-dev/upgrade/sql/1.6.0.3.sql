SET NAMES 'utf8';

ALTER TABLE `PREFIX_theme` ADD `default_left_column` TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE `PREFIX_theme` ADD `default_right_column` TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE `PREFIX_theme` ADD `responsive` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `PREFIX_theme` ADD `product_per_page` INT UNSIGNED NOT NULL;

ALTER TABLE `PREFIX_tab_lang` CHANGE `name` `name` VARCHAR(64) DEFAULT NULL;  

ALTER TABLE `PREFIX_attachment` ADD `file_size` bigint(10) unsigned NOT NULL DEFAULT 0 AFTER `file_name`;
/* PHP:p1603_add_atachment_size(); */;

DROP TABLE `PREFIX_help_access`;

ALTER TABLE `PREFIX_theme_meta` CHANGE `left_column` `left_column` TINYINT( 1 ) NOT NULL DEFAULT '1',
CHANGE `right_column` `right_column` TINYINT( 1 ) NOT NULL DEFAULT '1';

ALTER TABLE `PREFIX_employee` ADD `bo_css` varchar(64) default 'admin-theme.css' AFTER `bo_theme`;
