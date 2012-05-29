SET NAMES 'utf8';

/* PHP:add_missing_image_key(); */;
ALTER TABLE `PREFIX_cms_block_shop` ADD COLUMN `id_group_shop` int(10) unsigned DEFAULT 0 AFTER `id_cms_block`;