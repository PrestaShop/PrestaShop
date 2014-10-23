SET NAMES 'utf8';

INSERT INTO `PREFIX_order_state` (`invoice`, `send_email`, `module_name`, `color`, `unremovable`, `hidden`, `logable`, `delivery`, `shipped`, `paid`, `deleted`) VALUES ('0', '1', NULL, '#FF69B4', '1', '0', '0', '0', '0', '0', '0');
SET @id_order_state = LAST_INSERT_ID();
SET @id_order_state_oos = (SELECT `value` FROM `PREFIX_configuration` WHERE `name` = 'PS_OS_OUTOFSTOCK' LIMIT 1);
INSERT INTO `PREFIX_order_state_lang` (`id_order_state`, `id_lang`, `name`, `template`) (SELECT @id_order_state, id_lang, name, template FROM `PREFIX_order_state_lang` WHERE id_order_state = @id_order_state_oos);

INSERT IGNORE INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_OS_OUTOFSTOCK_PAID', @id_order_state_oos, NOW(), NOW()),
('PS_OS_OUTOFSTOCK_UNPAID', @id_order_state, NOW(), NOW());
SET NAMES 'utf8';

ALTER TABLE  `PREFIX_module_access` ADD  `uninstall` TINYINT( 1 ) NOT NULL AFTER  `configure`;

ALTER TABLE  `PREFIX_specific_price` ADD  `reduction_tax` TINYINT( 1 ) NOT NULL DEFAULT 1 AFTER  `reduction`;
ALTER TABLE  `PREFIX_specific_price_rule` ADD  `reduction_tax` TINYINT( 1 ) NOT NULL DEFAULT 1 AFTER  `reduction`;

INSERT INTO `PREFIX_hook` (`id_hook` , `name` , `title` , `description` , `position` , `live_edit`)
VALUES (NULL , 'displayCustomerIdentityForm', 'Customer identity form displayed in Front Office', 'This hook displays new elements on the form to update a customer identity', '1', '0');

ALTER TABLE `PREFIX_orders` ADD `round_mode` TINYINT(1) NOT NULL DEFAULT '2' AFTER `total_wrapping_tax_excl`;

ALTER TABLE `PREFIX_product_attribute` MODIFY `unit_price_impact` DECIMAL(20,6);
ALTER TABLE `PREFIX_product_attribute_shop` MODIFY `unit_price_impact` DECIMAL(20,6);

/* PHP:p16011_media_server(); */;
