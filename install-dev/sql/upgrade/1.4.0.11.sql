INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`)
VALUES
(
'PREFIX_INVOICE_START_NUMBER',
( SELECT `invoice_number` FROM (SELECT GREATEST(`value`, (SELECT CAST(MAX(`invoice_number`) AS CHAR) FROM `PREFIX_orders`)) AS `invoice_number`  FROM `PREFIX_configuration`  WHERE `name` = 'PREFIX_INVOICE_NUMBER' ) as tmp),
NOW(),
NOW()
);

DELETE FROM `PREFIX_configuration` WHERE `name` = 'PS_INVOICE_NUMBER';

CREATE TABLE `PREFIX_log` (
	`id_log` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`severity` tinyint(1) NOT NULL,
	`error_code` int(11) DEFAULT NULL,
	`message` text NOT NULL,
	`object_type` varchar(32) DEFAULT NULL,
	`object_id` int(10) unsigned DEFAULT NULL,
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id_log`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_LOGS_BY_EMAIL', '5', NOW(), NOW());

ALTER TABLE `PREFIX_tax_rules_group` CHANGE `name` `name` VARCHAR( 50 ) NOT NULL;

CREATE TABLE `PREFIX_import_match` (
  `id_import_match` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `match` text NOT NULL,
  `skip` int(2) NOT NULL,
  PRIMARY KEY (`id_import_match`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

/* PHP:add_new_tab(AdminLogs, en:Log|fr:Log|es:Log|de:Log|it:Log,  9); */;
