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
UPDATE `PREFIX_tab` SET `position` = 0 WHERE `class_name` = 'AdminModulesSf' AND `position`= 1;
UPDATE `PREFIX_tab` SET `position` = 1 WHERE `class_name` = 'AdminParentModulesCatalog' AND `position`= 0;

/* Fix Problem with missing lang entries in Configuration */
INSERT INTO `PREFIX_configuration_lang` (`id_configuration`, `id_lang`, `value`)
SELECT `id_configuration`, l.`id_lang`, `value`
  FROM `PREFIX_configuration` c
  JOIN `PREFIX_lang_shop` l on l.`id_shop` = COALESCE(c.`id_shop`, 1)
  WHERE `name` IN (
      'PS_DELIVERY_PREFIX',
      'PS_INVOICE_PREFIX',
      'PS_INVOICE_LEGAL_FREE_TEXT',
      'PS_INVOICE_FREE_TEXT',
      'PS_RETURN_PREFIX',
      'PS_SEARCH_BLACKLIST',
      'PS_CUSTOMER_SERVICE_SIGNATURE',
      'PS_MAINTENANCE_TEXT',
      'PS_LABEL_IN_STOCK_PRODUCTS',
      'PS_LABEL_OOS_PRODUCTS_BOA',
      'PS_LABEL_OOS_PRODUCTS_BOD'
      )
  AND NOT EXISTS (SELECT 1 FROM `PREFIX_configuration_lang` WHERE `id_configuration` = c.`id_configuration`);

/* PHP:ps_1760_update_configuration(); */;
/* PHP:ps_1760_update_tabs(); */;

/* Insert new hooks */
INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES
  (NULL, 'actionListMailThemes', 'List the available email themes and layouts', 'This hook allows to add/remove available email themes (ThemeInterface) and/or to add/remove their layouts (LayoutInterface)', '1'),
  (NULL, 'actionGetMailThemeFolder', 'Define the folder of an email theme', 'This hook allows to change the folder of an email theme (useful if you theme is in a module for example)', '1'),
  (NULL, 'actionBuildMailLayoutVariables', 'Build the variables used in email layout rendering', 'This hook allows to change the variables used when an email layout is rendered', '1'),
  (NULL, 'actionGetMailLayoutTransformations', 'Define the transformation to apply on layout', 'This hook allows to add/remove TransformationInterface used to generate an email layout', '1')
;
