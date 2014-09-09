SET NAMES 'utf8';

INSERT INTO `PREFIX_order_state` (`invoice`, `send_email`, `module_name`, `color`, `unremovable`, `hidden`, `logable`, `delivery`, `shipped`, `paid`, `deleted`) VALUES ('0', '1', NULL, '#FF69B4', '1', '0', '0', '0', '0', '0', '0');
SET @id_order_state = LAST_INSERT_ID();
SET @id_order_state_oos = (SELECT `value` FROM `PREFIX_configuration` WHERE `name` = 'PS_OS_OUTOFSTOCK' LIMIT 1);
INSERT INTO `PREFIX_order_state_lang` (`id_order_state`, `id_lang`, `name`, `template`) (SELECT @id_order_state, id_lang, name, template FROM `PREFIX_order_state_lang` WHERE id_order_state = @id_order_state_oos);

INSERT IGNORE INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_OS_OUTOFSTOCK_PAID', @id_order_state_oos, NOW(), NOW()),
('PS_OS_OUTOFSTOCK_UNPAID', @id_order_state, NOW(), NOW());
