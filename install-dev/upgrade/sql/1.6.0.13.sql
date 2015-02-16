SET NAMES 'utf8';

UPDATE `PREFIX_configuration` SET `value` = CONCAT('#', `value`) WHERE `name` LIKE 'PS_%_PREFIX';

UPDATE `PREFIX_configuration_lang` SET `value` = CONCAT('#', `value`) WHERE `id_configuration` IN (SELECT `id_configuration` FROM `PREFIX_configuration` WHERE `name` LIKE 'PS_%_PREFIX');

ALTER TABLE `PREFIX_orders` CHANGE `invoice_number` `invoice_number` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0',
CHANGE `delivery_number` `delivery_number` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0';
