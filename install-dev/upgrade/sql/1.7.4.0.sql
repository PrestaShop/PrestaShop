SET SESSION sql_mode = '';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_order_detail` DROP KEY product_id, ADD KEY product_id (product_id, product_attribute_id);
ALTER TABLE `PREFIX_currency` ADD `numeric_iso_code` varchar(3) NOT NULL DEFAULT '0' AFTER `iso_code`;
ALTER TABLE `PREFIX_currency` ADD `precision` int(2) NOT NULL DEFAULT 6 AFTER `numeric_iso_code`;

/* Localized currency information */
CREATE TABLE `PREFIX_currency_lang` (
  `id_currency` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `symbol` varchar(255) NOT NULL,
  PRIMARY KEY (`id_currency`,`id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

/* PHP:ps_1740_copy_data_from_currency_to_currency_lang(); */;

ALTER TABLE `PREFIX_currency` DROP `name`;

INSERT IGNORE INTO `PREFIX_hook` (`id_hook`, `name`, `title`, `description`, `position`) VALUES
  (NULL, 'displayAdministrationPageForm', 'Manage Administration Page form fields', 'This hook adds, update or remove fields of the Administration Page form', '1'),
  (NULL, 'actionAdministrationPageFormSave', 'Processing Administration page form', 'This hook is called when the Administration Page form is processed', '1'),
  (NULL, 'displayPerformancePageForm', 'Manage Performance Page form fields', 'This hook adds, update or remove fields of the Performance Page form', '1'),
  (NULL, 'actionPerformancePageFormSave', 'Processing Performance page form', 'This hook is called when the Performance Page form is processed', '1'),
  (NULL, 'displayMaintenancePageForm', 'Manage Maintenance Page form fields', 'This hook adds, update or remove fields of the Maintenance Page form', '1'),
  (NULL, 'actionMaintenancePageFormSave', 'Processing Maintenance page form', 'This hook is called when the Maintenance Page form is processed', '1');
