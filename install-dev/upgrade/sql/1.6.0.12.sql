SET NAMES 'utf8';

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_CUSTOMER_NWSL', 1, NOW(), NOW()), ('PS_CUSTOMER_OPTIN', 1, NOW(), NOW());

INSERT INTO `PREFIX_order_state` (`invoice`, `send_email`, `module_name`, `color`, `unremovable`, `hidden`, `logable`, `delivery`, `shipped`, `paid`, `deleted`) VALUES ('0', '0', 'cashondelivery', '#4169E1', '1', '0', '0', '0', '0', '0', '0');
SET @id_order_state = LAST_INSERT_ID();
INSERT INTO `PREFIX_order_state_lang` (`id_order_state`, `id_lang`, `name`, `template`) (SELECT @id_order_state, id_lang, 'Waiting cod validation', '' FROM `PREFIX_lang`);

INSERT IGNORE INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_OS_COD_VALIDATION', @id_order_state, NOW(), NOW());

UPDATE `PREFIX_hook` set `position` = 1 where `name` in ('displayBanner', 'displayNav', 'displayTopColumn');

ALTER TABLE `PREFIX_product` ADD `pack_stock_type` int(11) UNSIGNED DEFAULT '3';
ALTER TABLE `PREFIX_product_shop` ADD `pack_stock_type` int(11) UNSIGNED DEFAULT '3';
ALTER TABLE `PREFIX_pack` ADD `id_product_attribute_item` int(10) UNSIGNED NOT NULL AFTER `id_product_item`;
ALTER TABLE `PREFIX_pack` DROP PRIMARY KEY;

/* PHP:p16012_pack_rework(); */;

ALTER TABLE `PREFIX_pack` ADD PRIMARY KEY (`id_product_pack`, `id_product_item`, `id_product_attribute_item`);

ALTER TABLE `PREFIX_order_state` ADD `pdf_delivery` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `paid`;
ALTER TABLE `PREFIX_order_state` ADD `pdf_invoice` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `pdf_delivery`;

ALTER TABLE `PREFIX_orders` CHANGE `shipping_number` `shipping_number` VARCHAR( 64 );
