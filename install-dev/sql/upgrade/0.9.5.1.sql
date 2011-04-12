/* STRUCTURE */
ALTER TABLE `PREFIX_order_state`
	ADD `logable` TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE `PREFIX_product_sale`
	CHANGE `nb_vente` `sale_nbr` INT(10) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `PREFIX_carrier`
	CHANGE `tax` `id_tax` INT(10) UNSIGNED NULL DEFAULT 0 AFTER `id_carrier`;
ALTER TABLE `PREFIX_carrier`
	ADD `shipping_handling` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 AFTER `deleted`;
ALTER TABLE `PREFIX_address`
	CHANGE `id_country` `id_country` INT(10) UNSIGNED NOT NULL DEFAULT 0,
	CHANGE `id_customer` `id_customer` INT(10) UNSIGNED NOT NULL DEFAULT 0,
	CHANGE `id_manufacturer` `id_manufacturer` INT(10) UNSIGNED NOT NULL DEFAULT 0;
RENAME TABLE `PREFIX_product_attribute_combinaison` TO `PREFIX_product_attribute_combination`;
ALTER TABLE `PREFIX_product_attribute_combination`
	DROP INDEX `product_attribute_combinaison_index`,
	ADD PRIMARY KEY (`id_attribute`, `id_product_attribute`);

CREATE TABLE `PREFIX_carrier_zone` (
  id_carrier int(10) unsigned NOT NULL,
  id_zone int(10) unsigned NOT NULL,
  INDEX carrier_zone_index(id_carrier, id_zone)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_tax_zone` (
  id_tax int(10) unsigned NOT NULL,
  id_zone int(10) unsigned NOT NULL,
   INDEX tax_zone_index(id_tax, id_zone)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


/*  CONTENTS */

/* Adding tab Contact */
INSERT INTO `PREFIX_tab` (`id_parent`, `class_name`, `position`) VALUES ((SELECT tmp.`id_tab` FROM (SELECT `id_tab` FROM`PREFIX_tab` t WHERE t.class_name = 'AdminPreferences' LIMIT 1) AS tmp), 'AdminContact', 6);
INSERT INTO `PREFIX_tab_lang` (id_lang, id_tab, name) (
	SELECT id_lang,
	(SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminContact' LIMIT 1),
	'Contact' FROM `PREFIX_lang`);
UPDATE `PREFIX_tab_lang` SET `name` = 'Coordonnées'
	WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminContact' LIMIT 1)
	AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` l WHERE l.iso_code = 'fr' LIMIT 1);
INSERT INTO `PREFIX_access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`)
	VALUES ('1', (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminContact' LIMIT 1), '1', '1', '1', '1');

/* Rename old tab */
UPDATE `PREFIX_tab_lang` SET `name` = 'Produits'
	WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminPPreferences')
	AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` l WHERE l.iso_code = 'fr');
UPDATE `PREFIX_tab_lang` SET `name` = 'Emails'
	WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminEmails')
	AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` l WHERE l.iso_code = 'fr');
UPDATE `PREFIX_tab_lang` SET `name` = 'Images'
	WHERE `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` t WHERE t.class_name = 'AdminImages')
	AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` l WHERE l.iso_code = 'fr');

/* New BankWire state */
UPDATE `PREFIX_order_state` SET `logable` = 1 WHERE `id_order_state` < 6 AND `id_order_state` > 1;
INSERT INTO `PREFIX_order_state` (`id_order_state`, `invoice`, `send_email`, `color`, `unremovable`, `logable`) VALUES (10, 0, 1, 'lightblue', 1, 0);
INSERT INTO `PREFIX_order_state_lang` (`id_order_state`, `id_lang`, `name`, `template`) VALUES
(10, 1, 'Awaiting bank wire payment', 'bankwire'),
(10, 2, 'En attente du paiement par virement bancaire', 'bankwire');

/* New hook */
INSERT INTO `PREFIX_hook` (`name`, `title`, `description`, `position`) VALUES ('updateOrderStatus', 'Order''s status update event', 'Launch modules when the order''s status of an order change.', 0);

/* Adding zones for tax/carrier */
INSERT INTO `PREFIX_tax_zone` (id_tax, id_zone) (SELECT id_tax, id_zone FROM `PREFIX_tax` CROSS JOIN `PREFIX_zone`);
INSERT INTO `PREFIX_carrier_zone` (id_carrier, id_zone) (SELECT id_carrier, id_zone FROM `PREFIX_carrier` CROSS JOIN `PREFIX_zone`);

/* CONFIGURATION VARIABLE */

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PREFIX_PURCHASE_MINIMUM', '0', NOW(), NOW()),
('PREFIX_SHOP_ENABLE', '1', NOW(), NOW());