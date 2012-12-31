ALTER TABLE  `PREFIX_product` ADD `redirect_type` ENUM('', '404', '301', '302') NOT NULL DEFAULT '404' AFTER `active`;
ALTER TABLE  `PREFIX_product_shop` ADD `redirect_type` ENUM('', '404', '301', '302') NOT NULL DEFAULT '404' AFTER `active`;

ALTER TABLE `PREFIX_cart` CHANGE `delivery_option` `delivery_option` TEXT NOT NULL;
