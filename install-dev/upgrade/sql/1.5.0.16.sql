SET NAMES 'utf8';

/* PHP:p15016_add_missing_columns(); */;

ALTER TABLE `PREFIX_order_detail` ADD `id_shop` INT(11) UNSIGNED NOT NULL AFTER `id_warehouse`, ADD INDEX (`id_shop`);

UPDATE `PREFIX_order_detail` od SET `id_shop`=(SELECT `id_shop` FROM `PREFIX_orders` WHERE `id_order`=od.`id_order`);

DELETE FROM `PREFIX_tab` WHERE `class_name` = 'AdminAddonsMyAccount';
DELETE FROM `PREFIX_tab_lang` WHERE `id_tab` NOT IN (SELECT `id_tab` FROM `PREFIX_tab`);
DELETE FROM `PREFIX_access` WHERE `id_tab` NOT IN (SELECT `id_tab` FROM `PREFIX_tab`);

UPDATE `PREFIX_employee` SET bo_theme = 'default';
