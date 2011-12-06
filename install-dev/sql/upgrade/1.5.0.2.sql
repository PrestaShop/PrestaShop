SET NAMES 'utf8';

INSERT INTO `PREFIX_access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`) VALUES ('1', '108', '1', '1', '1', '1');
INSERT INTO `PREFIX_access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`) VALUES ('2', '108', '1', '1', '1', '1');
INSERT INTO `PREFIX_access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`) VALUES ('3', '108', '1', '1', '1', '1');
INSERT INTO `PREFIX_access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`) VALUES ('4', '108', '0', '0', '0', '0');
INSERT INTO `PREFIX_access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`) VALUES ('5', '108', '0', '0', '0', '0');



ALTER TABLE `PREFIX_orders` DROP COLUMN `id_warehouse`;
ALTER TABLE `PREFIX_order_detail` ADD COLUMN `id_warehouse` int(10) unsigned DEFAULT 0 AFTER `id_order_invoice`;
ALTER TABLE `PREFIX_suplier` ADD COLUMN `id_address` int(10) unsigned NOT NULL AFTER `id_supplier`;
ALTER TABLE `PREFIX_address` ADD COLUMN `id_warehouse` int(10) unsigned NOT NULL DEFAULT 0 AFTER `id_supplier`;
