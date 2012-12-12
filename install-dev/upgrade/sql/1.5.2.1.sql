SET NAMES 'utf8';

ALTER TABLE `PREFIX_address` CHANGE  `outstanding_allow_amount` `outstanding_allow_amount` DECIMAL(20, 6) NOT NULL DEFAULT '0.000000';
ALTER TABLE `PREFIX_product_download` ADD `id_product_attribute` INT( 10 ) NOT NULL AFTER `id_product`;
/* PHP:blocknewsletter1530(); */;

/* PHP:block_category_1521(); */;

UPDATE `PREFIX_order_state` SET `delivery` = 0 WHERE `id_order_state` = 3;

ALTER TABLE  `PREFIX_product_shop` ADD `id_product_redirected` int(10) unsigned NOT NULL default '0' AFTER `active` , ADD `available_for_order` tinyint(1) NOT NULL default '1' AFTER `id_product_redirected`;

ALTER TABLE  `PREFIX_product` ADD `id_product_redirected` int(10) unsigned NOT NULL default '0' AFTER `active` , ADD `available_for_order` tinyint(1) NOT NULL default '1' AFTER `id_product_redirected`;

UPDATE `PREFIX_order_state` SET `send_email` = 1 WHERE `id_order_state` = (SELECT `value` FROM `PREFIX_configuration` WHERE `name` = 'PS_OS_WS_PAYMENT' LIMIT 1);

UPDATE `PREFIX_order_state_lang` SET `template` = 'payment' WHERE `id_order_state` = (SELECT `value` FROM `PREFIX_configuration` WHERE `name` = 'PS_OS_WS_PAYMENT' LIMIT 1);

DELETE FROM `PREFIX_configuration` WHERE `name`= 'PS_HIGH_HTML_THEME_COMPRESSION';

INSERT INTO `PREFIX_configuration`(`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_MAIL_COLOR', '#db3484', NOW(), NOW());
