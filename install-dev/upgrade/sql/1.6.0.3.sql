SET NAMES 'utf8';

ALTER TABLE `PREFIX_theme` ADD `default_left_column` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `PREFIX_theme` ADD `default_right_column` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `PREFIX_theme` ADD `responsive` TINYINT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `PREFIX_theme` ADD `product_per_page` INT UNSIGNED NOT NULL;

ALTER TABLE `PREFIX_tab_lang` CHANGE `name` `name` VARCHAR(64) DEFAULT NULL;  

ALTER TABLE `PREFIX_attachment` ADD `file_size` bigint(10) unsigned NOT NULL DEFAULT 0 AFTER `file_name`;
/* PHP:p1603_add_atachment_size(); */;

DROP TABLE `PREFIX_help_access`;

UPDATE `PREFIX_configuration` SET value = 0 WHERE name = 'PS_ALLOW_MULTISHIPPING';