SET NAMES 'utf8';

ALTER TABLE `PREFIX_image_shop` ADD `cover` TINYINT(1) UNSIGNED NOT NULL AFTER `id_shop`;
ALTER TABLE `PREFIX_image_shop` DROP PRIMARY KEY;
ALTER TABLE `PREFIX_image_shop` ADD  ADD INDEX (`id_image`, `id_shop`, `cover`);
