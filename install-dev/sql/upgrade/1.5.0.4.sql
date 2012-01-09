SET NAMES 'utf8';


ALTER TABLE `PREFIX_order_state` ADD COLUMN `deleted` tinyint(1) UNSIGNED NOT NULL default '0' AFTER `paid`;

ALTER TABLE `PREFIX_category` ADD COLUMN `is_root_category` tinyint(1) NOT NULL default '0' AFTER `position`;

UPDATE `PREFIX_category` SET `is_root_category` = 1 WHERE `id_category` = 1;
