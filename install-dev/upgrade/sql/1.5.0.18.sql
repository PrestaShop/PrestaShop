SET NAMES 'utf8';

ALTER TABLE `PREFIX_image_shop` ADD `cover` TINYINT(1) UNSIGNED NOT NULL AFTER `id_shop`;
ALTER TABLE `PREFIX_image_shop` DROP PRIMARY KEY;
ALTER TABLE `PREFIX_image_shop` ADD INDEX (`id_image`, `id_shop`, `cover`);
UPDATE `PREFIX_image_shop` image_shop SET image_shop.`cover`=1 WHERE `id_image` IN (SELECT `id_image` FROM `PREFIX_image` i WHERE i.`cover`=1);

INSERT INTO `PREFIX_configuration`(`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_ONE_PHONE_AT_LEAST', '1', NOW(), NOW());

/* PHP:p15018_change_image_types(); */;
