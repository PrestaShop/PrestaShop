SET SESSION sql_mode = '';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_currency` ADD `numeric_iso_code` varchar(3) NOT NULL DEFAULT '0' AFTER `iso_code`;
ALTER TABLE `PREFIX_currency` ADD `precision` int(2) NOT NULL DEFAULT 6 AFTER `numeric_iso_code`;
ALTER TABLE `PREFIX_currency` ADD KEY `currency_iso_code` (`iso_code`);

/* Localized currency information */
CREATE TABLE `PREFIX_currency_lang` (
    `id_currency` int(10) unsigned NOT NULL,
    `id_lang` int(10) unsigned NOT NULL,
    `name` varchar(255) NOT NULL,
    `symbol` varchar(255) NOT NULL,
    PRIMARY KEY (`id_currency`,`id_lang`)
  ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

/* PHP:ps_1760_copy_data_from_currency_to_currency_lang(); */;

/* Module Manager tab should be the first tab in Modules Tab */
UPDATE `PREFIX__tab` SET `position` = 0 WHERE `class_name` = 'AdminModulesSf' AND `position`= 1;
UPDATE `PREFIX__tab` SET `position` = 1 WHERE `class_name` = 'AdminParentModulesCatalog' AND `position`= 0;
