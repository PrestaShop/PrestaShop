SET NAMES 'utf8';

ALTER TABLE `PREFIX_order_invoice_tax` ADD INDEX (`id_tax`);

INSERT IGNORE INTO `PREFIX_meta` (`id_meta`, `page`, `configurable`) VALUES
  (NULL, 'products-comparison', '1'),
  (NULL, 'cms', '0'),
  (NULL, 'category', '0'),
  (NULL, 'product', '0'),
  (NULL, 'module-bankwire-payment', '0'),
  (NULL, 'module-bankwire-validation', '0'),
  (NULL, 'module-cheque-validation', '0'),
  (NULL, 'module-cheque-payment', '0');

INSERT IGNORE INTO `PREFIX_theme_meta` ( `id_theme` , `id_meta` , `left_column` , `right_column` )
  SELECT `PREFIX_theme`.`id_theme` , `PREFIX_meta`.`id_meta` , `default_left_column` , `default_right_column`
  FROM `PREFIX_theme` , `PREFIX_meta`;

ALTER TABLE `PREFIX_tab` ADD `hide_host_mode` tinyint(1) NOT NULL DEFAULT '0' AFTER  `active`;

UPDATE `PREFIX_employee` SET `bo_theme` = 'default';

DELETE FROM `PREFIX_image_type` WHERE `name` = 'cart_default';

INSERT INTO `PREFIX_image_type` (`id_image_type`,`name`,`width`,`height`,`products`,`categories`,`manufacturers`,`suppliers`,`scenes`,`stores`)
VALUES (NULL, 'cart_default', '80', '80', '1', '0', '0', '0', '0', '0');

ALTER TABLE `PREFIX_cart_rule_combination` ADD INDEX `id_cart_rule_1` (`id_cart_rule_1`);
ALTER TABLE `PREFIX_cart_rule_combination` ADD INDEX `id_cart_rule_2` (`id_cart_rule_2`);

/* PHP:p1606module_exceptions(); */;
