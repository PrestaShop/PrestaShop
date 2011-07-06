SET NAMES 'utf8';

ALTER TABLE `PREFIX_tab_lang` MODIFY `id_lang` int(10) unsigned NOT NULL AFTER `id_tab`;
ALTER TABLE `PREFIX_carrier` ADD `is_free` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `is_module`;

UPDATE `PREFIX_address_format` SET `format`=REPLACE(REPLACE(`format`, 'state_iso', 'State:name'), 'country', 'Country:name');

ALTER TABLE `PREFIX_orders` ADD INDEX `date_add`(`date_add`);

/* PHP:update_module_followup(); */;

INSERT IGNORE INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_STOCK_MVT_REASON_DEFAULT', 3, NOW(), NOW());

INSERT IGNORE INTO `PREFIX_order_state` (`id_order_state`, `invoice`, `send_email`, `color`, `unremovable`, `logable`, `delivery`) VALUES (12, 0, 0, 'lightblue', 1, 0, 0);

INSERT IGNORE INTO `PREFIX_order_state_lang` (`id_order_state`, `id_lang`, `name`, `template`) VALUES
(12, 1, 'Payment remotely accepted', ''),
(12, 2, 'Paiement à distance accepté', ''),
(12, 3, 'Payment remotely accepted', ''),
(12, 4, 'Payment remotely accepted', ''),
(12, 5, 'Payment remotely accepted', '');

/* PHP:alter_blocklink(); */;
/* PHP:update_module_loyalty(); */;
/* PHP:remove_module_from_hook(blockcategories, afterCreateHtaccess); */;
/* PHP:updatetabicon_from_11version(); */;

