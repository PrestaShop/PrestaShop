SET NAMES 'utf8';

UPDATE `PREFIX_meta` SET `page` = 'supplier' WHERE `page` = 'supply';

ALTER TABLE  `PREFIX_image_type` CHANGE  `name`  `name` VARCHAR( 64 ) NOT NULL;
