SET NAMES 'utf8';

ALTER TABLE `PREFIX_order_detail` ADD `id_shop` INT(11) UNSIGNED NOT NULL AFTER `id_warehouse`, ADD INDEX (`id_shop`);

UPDATE `PREFIX_order_detail` od SET `id_shop`=(SELECT `id_shop` FROM `PREFIX_orders` WHERE `id_order`=od.`id_order`);
