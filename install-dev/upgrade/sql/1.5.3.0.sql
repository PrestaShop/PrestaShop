SET NAMES 'utf8';

ALTER TABLE `PREFIX_address` CHANGE  `outstanding_allow_amount` `outstanding_allow_amount` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000';

/* PHP:blocknewsletter1530(); */;

/* PHP:block_category_1521(); */;

UPDATE `PREFIX_order_state` SET `delivery` = 0 WHERE `id_order_state` = 3;

ALTER TABLE  `PREFIX_product_shop` ADD `id_product_redirected` int(10) unsigned NOT NULL default '0' AFTER `active` ;

ALTER TABLE  `PREFIX_product` ADD `id_product_redirected` int(10) unsigned NOT NULL default '0' AFTER `active` ;

UPDATE `PREFIX_order_state` SET `send_email` = 1 WHERE `id_order_state` = (SELECT `value` FROM `PREFIX_configuration` WHERE `name` = 'PS_OS_WS_PAYMENT' LIMIT 1);

UPDATE `PREFIX_order_state_lang` SET `template` = 'payment' WHERE `id_order_state` = (SELECT `value` FROM `PREFIX_configuration` WHERE `name` = 'PS_OS_WS_PAYMENT' LIMIT 1);

DELETE FROM `PREFIX_configuration` WHERE `name`= 'PS_HIGH_HTML_THEME_COMPRESSION';

INSERT INTO `PREFIX_configuration`(`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_MAIL_COLOR', '#db3484', NOW(), NOW());

ALTER TABLE `PREFIX_order_cart_rule` CHANGE `name` `name` VARCHAR(254);

ALTER TABLE `PREFIX_cart` CHANGE `delivery_option` `delivery_option` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
