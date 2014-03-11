SET NAMES 'utf8';

ALTER TABLE `PREFIX_image_shop` CHANGE `cover` `cover` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE  `PREFIX_order_detail_tax` ADD PRIMARY KEY (`id_order_detail`);
ALTER TABLE  `PREFIX_order_detail_tax` ADD INDEX `id_tax` ( `id_tax`);
