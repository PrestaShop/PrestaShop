SET NAMES 'utf8';

INSERT IGNORE INTO `PREFIX_meta` (`id_meta`, `page`, `configurable`) VALUES (NULL, 'index', '0'), (NULL, 'product', '0'), (NULL, 'category', '0'), (NULL, 'cms', '0');

ALTER TABLE `PREFIX_employee` ADD `optin` tinyint(1) unsigned NOT NULL default '1' AFTER `active`;

ALTER IGNORE TABLE `PREFIX_meta` ADD UNIQUE (`page`);

UPDATE `PREFIX_orders` SET module = 'free_order' WHERE total_paid = 0 AND module LIKE '';