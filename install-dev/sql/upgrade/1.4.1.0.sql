SET NAMES 'utf8';

INSERT INTO `PREFIX_hook` (`name`, `title`, `description`, `position`, `live_edit`) VALUES ('afterSaveAdminMeta', 'After save configuration in AdminMeta', 'After save configuration in AdminMeta', 0, 0);

INSERT INTO `PREFIX_hook_module` (`id_module`, `id_hook`, `position`) VALUES 
(
(SELECT `id_module` FROM `PREFIX_module` WHERE `name` = 'blockcategories'), 
(SELECT `id_hook` FROM `PREFIX_hook` WHERE `name` = 'afterSaveAdminMeta'), 1
);


ALTER TABLE `PREFIX_webservice_account` ADD `is_module` TINYINT( 2 ) NOT NULL DEFAULT '0' AFTER `class_name` ,
ADD `module_name` VARCHAR( 50 ) NULL DEFAULT NULL AFTER `is_module`;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_IMG_UPDATE_TIME', UNIX_TIMESTAMP(), NOW(), NOW());

UPDATE `PREFIX_cms_lang` set link_rewrite = "uber-uns" where link_rewrite like "%ber-uns";

ALTER TABLE `PREFIX_connections` CHANGE `ip_address` `ip_address` BIGINT NULL DEFAULT NULL;


UPDATE `PREFIX_meta_lang`
SET `title` = 'Angebote', `keywords` = 'besonders, Angebote', `url_rewrite` = 'angebote' WHERE url_rewrite = 'preise-fallen';

ALTER TABLE `PREFIX_order_detail` 
CHANGE `product_quantity_in_stock` `product_quantity_in_stock` INT(10) NOT NULL DEFAULT '0';

/* PHP:alter_cms_block(); */;
