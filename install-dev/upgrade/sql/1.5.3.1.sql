ALTER TABLE  `PREFIX_product` ADD `redirect_type` ENUM('', '404', '301', '302') NOT NULL DEFAULT '404' AFTER `active`;
ALTER TABLE  `PREFIX_product_shop` ADD `redirect_type` ENUM('', '404', '301', '302') NOT NULL DEFAULT '404' AFTER `active`;

UPDATE `PREFIX_order_state` SET `delivery` = '1' WHERE `PREFIX_order_state`.`id_order_state` = 3;
