SET NAMES 'utf8';

/* Carrier */
INSERT INTO `PREFIX_carrier` (`id_carrier`, `id_tax_rules_group`, `name`, `active`, `deleted`, `shipping_handling`) VALUES (2, 1, 'My carrier', 1, 0, 1);
INSERT INTO `PREFIX_carrier_group` (`id_carrier`, `id_group`) VALUES (2, 1);
INSERT INTO `PREFIX_carrier_lang` (`id_carrier`, `id_lang`, `delay`) VALUES (2, 1, 'Delivery next day!'),(2, 2, 'Livraison le lendemain !'),(2, 3, '¡Entrega día siguiente!'),(2, 4, 'Zustellung am nächsten Tag!'),(2, 5, 'Consegna il giorno dopo!');
INSERT INTO `PREFIX_carrier_zone` (`id_carrier`, `id_zone`) VALUES (2, 1),(2, 2);

UPDATE `PREFIX_configuration` SET `value` = '2' WHERE `name` = 'PS_CARRIER_DEFAULT';

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
	('MB_PAY_TO_EMAIL', 'testmerchant@moneybookers.com', NOW(), NOW()),
	('MB_SECRET_WORD', 'mbtest', NOW(), NOW()),
	('MB_HIDE_LOGIN', 1, NOW(), NOW()),
	('MB_ID_LOGO', 1, NOW(), NOW()),
	('MB_ID_LOGO_WALLET', 1, NOW(), NOW()),
	('MB_PARAMETERS', 1, NOW(), NOW()),
	('MB_PARAMETERS_2', 1, NOW(), NOW()),
	('MB_DISPLAY_MODE', 0, NOW(), NOW()),
	('MB_CANCEL_URL', 'http://www.yoursite.com', NOW(), NOW()),
	('MB_LOCAL_METHODS', '2', NOW(), NOW()),
	('MB_INTER_METHODS', '5', NOW(), NOW()),
	('BANK_WIRE_CURRENCIES', '2,1', NOW(), NOW()),
	('CHEQUE_CURRENCIES', '2,1', NOW(), NOW()),
	('PRODUCTS_VIEWED_NBR', '2', NOW(), NOW()),
	('BLOCK_CATEG_DHTML', '1', NOW(), NOW()),
	('BLOCK_CATEG_MAX_DEPTH', '3', NOW(), NOW()),
	('MANUFACTURER_DISPLAY_FORM', '1', NOW(), NOW()),
	('MANUFACTURER_DISPLAY_TEXT', '1', NOW(), NOW()),
	('MANUFACTURER_DISPLAY_TEXT_NB', '5', NOW(), NOW()),
	('NEW_PRODUCTS_NBR', '5', NOW(), NOW()),
	('PS_TOKEN_ENABLE', '1', NOW(), NOW()),
	('PS_STATS_RENDER', 'graphxmlswfcharts', NOW(), NOW()),
	('PS_STATS_OLD_CONNECT_AUTO_CLEAN', 'never', NOW(), NOW()),
	('PS_STATS_GRID_RENDER', 'gridhtml', NOW(), NOW()),
	('BLOCKTAGS_NBR', '10', NOW(), NOW()),
	('CHECKUP_DESCRIPTIONS_LT', '100', NOW(), NOW()),
	('CHECKUP_DESCRIPTIONS_GT', '400', NOW(), NOW()),
	('CHECKUP_IMAGES_LT', '1', NOW(), NOW()),
	('CHECKUP_IMAGES_GT', '2', NOW(), NOW()),
	('CHECKUP_SALES_LT', '1', NOW(), NOW()),
	('CHECKUP_SALES_GT', '2', NOW(), NOW()),
	('CHECKUP_STOCK_LT', '1', NOW(), NOW()),
	('CHECKUP_STOCK_GT', '3', NOW(), NOW()),
	('FOOTER_CMS', '0_3|0_4', NOW(), NOW()),
	('FOOTER_BLOCK_ACTIVATION', '0_3|0_4', NOW(), NOW()),
	('BLOCKADVERT_LINK', 0, NOW(), NOW()),
	('BLOCKSTORE_IMG', 'store.jpg', NOW(), NOW());

INSERT INTO `PREFIX_module` (`id_module`, `name`, `active`) VALUES (1, 'homefeatured', 1),(2, 'gsitemap', 1),(3, 'cheque', 1),(4, 'moneybookers', 1),(5, 'editorial', 1),
(6, 'bankwire', 1),(7, 'blockadvertising', 1),(8, 'blockbestsellers', 1),(9, 'blockcart', 1),(10, 'blockcategories', 1),(11, 'blockcurrencies', 1),(12, 'blockcms', 1),
(13, 'blocklanguages', 1),(14, 'blockmanufacturer', 1),(15, 'blockmyaccount', 1),(16, 'blocknewproducts', 1),(17, 'blockpaymentlogo', 1),(18, 'blockpermanentlinks', 1),
(19, 'blocksearch', 1),(20, 'blockspecials', 1),(21, 'blocktags', 1),(22, 'blockuserinfo', 1),(24, 'blockviewed', 1),(25, 'statsdata', 1),
(26, 'statsvisits', 1),(27, 'statssales', 1),(28, 'statsregistrations', 1),(30, 'statspersonalinfos', 1),(31, 'statslive', 1),(32, 'statsequipment', 1),(33, 'statscatalog', 1),
(34, 'graphvisifire', 1),(35, 'graphxmlswfcharts', 1),(36, 'graphgooglechart', 1),(37, 'graphartichow', 1),(39, 'gridhtml', 1),(40, 'statsbestcustomers', 1),
(41, 'statsorigin', 1),(42, 'pagesnotfound', 1),(43, 'sekeywords', 1),(44, 'statsproduct', 1),(45, 'statsbestproducts', 1),(46, 'statsbestcategories', 1),
(47, 'statsbestvouchers', 1),(48, 'statsbestsuppliers', 1),(49, 'statscarrier', 1),(50, 'statsnewsletter', 1),(51, 'statssearch', 1),(52, 'statscheckup', 1),(53, 'statsstock', 1),
(54, 'blockstore', 1),(55, 'statsforecast', 1);

INSERT INTO `PREFIX_hook` (`name`, `title`, `description`, `position`) VALUES
	('myAccountBlock', 'My account block', 'Display extra informations inside the "my account" block', 1);

INSERT INTO `PREFIX_hook_module` (`id_module`, `id_hook`, `position`) VALUES (3, 1, 1),(6, 1, 2),(4, 1, 3),(4, 4, 3),(8, 2, 1),(3, 4, 1),(6, 4, 2),(9, 6, 1),(16, 6, 2),(8, 6, 3),
(20, 6, 4),(12, 6, 5),(54, 6, 6),(15, 7, 1),(21, 7, 2),(10, 7, 3),(24, 7, 4),(14, 7, 5),(12, 7, 6),(7, 7, 7),(17, 7, 8),(5, 8, 1),(1, 8, 2),(11, 14, 1),(13, 14, 2),(18, 14, 3),
(19, 14, 4),(22, 14, 5),(8, 19, 1),(12, 21, 1),(25, 11, 1),(25, 21, 2),(26, 32, 1),(27, 32, 2),(28, 32, 3),(30, 32, 4),(31, 32, 5),(32, 32, 6),(33, 32, 7),(34, 33, 1),
(35, 33, 2),(36, 33, 3),(37, 33, 4),(39, 37, 1),(40, 32, 8),(41, 32, 9),(42, 32, 10),(43, 32, 11),(42, 14, 6),(43, 14, 7),(44, 32, 12),(45, 32, 13),(46, 32, 15),
(47, 32, 14),(48, 32, 16),(49, 32, 17),(55, 32, 22),(50, 32, 18),(51, 32, 19),(51, 45, 1),(25, 25, 1),(41, 20, 2),(52, 32, 20),(53, 32, 21),(17, 9, 2),(18, 9, 3),(24, 9, 4),(9, 9, 5),
(15, 9, 6),(5, 9, 7),(8, 9, 8),(10, 9, 9),(20, 9, 10),(11, 9, 11),(16, 9, 12),(22, 9, 13),(13, 9, 14),(14, 9, 15),(12, 9, 16),(7, 9, 17),(21, 9, 18),(10, 60, 1),(10, 61, 1),(10, 62, 1),(54, 9, 19)
,(10,65,1), (10,66,1);

CREATE TABLE `PREFIX_pagenotfound` (
  `id_pagenotfound` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `request_uri` VARCHAR(256) NOT NULL,
  `http_referer` VARCHAR(256) NOT NULL,
  `date_add` DATETIME NOT NULL,
  PRIMARY KEY(`id_pagenotfound`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_statssearch` (
	`id_statssearch` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`keywords` VARCHAR(255) NOT NULL,
	`results` INT(6) NOT NULL DEFAULT 0,
	`date_add` DATETIME NOT NULL,
	PRIMARY KEY(`id_statssearch`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_sekeyword` (
	`id_sekeyword` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`keyword` VARCHAR(256) NOT NULL,
	`date_add` DATETIME NOT NULL,
	PRIMARY KEY(`id_sekeyword`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;


CREATE TABLE `PREFIX_cms_block` (
	`id_cms_block` int(10) unsigned NOT NULL auto_increment,
	`id_cms_category` int(10) unsigned NOT NULL,
	`name` varchar(40) NOT NULL,
	`location` tinyint(1) unsigned NOT NULL,
	`position` int(10) unsigned NOT NULL default '0',
	`display_store` tinyint(1) NOT NULL DEFAULT '1',
	PRIMARY KEY (`id_cms_block`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_cms_block_page` (
	`id_cms_block_page` int(10) unsigned NOT NULL auto_increment,
	`id_cms_block` int(10) unsigned NOT NULL,
	`id_cms` int(10) unsigned NOT NULL,
	`is_category` tinyint(1) unsigned NOT NULL,
	PRIMARY KEY (`id_cms_block_page`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_cms_block_lang` (
	`id_cms_block` int(10) unsigned NOT NULL,
	`id_lang` int(10) unsigned NOT NULL,
	`name` varchar(40) NOT NULL default '',
	PRIMARY KEY (`id_cms_block`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_editorial` (
	`id_editorial` int(10) unsigned NOT NULL auto_increment,
	`body_home_logo_link` varchar(255) NOT NULL,
	PRIMARY KEY (`id_editorial`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_editorial_lang` (
	`id_editorial` int(10) unsigned NOT NULL,
	`id_lang` int(10) unsigned NOT NULL,
	`body_title` varchar(255) NOT NULL,
	`body_subheading` varchar(255) NOT NULL,
	`body_paragraph` text NOT NULL,
	`body_logo_subheading` varchar(255) NOT NULL,
	PRIMARY KEY (`id_editorial`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_editorial` (`id_editorial`, `body_home_logo_link`) VALUES (1, 'http://www.prestashop.com');
INSERT INTO `PREFIX_editorial_lang` (`id_editorial`, `id_lang`, `body_title`, `body_subheading`, `body_paragraph`, `body_logo_subheading`) VALUES
(1, 1, 'Lorem ipsum dolor sit amet', 'Consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>', 'Excepteur sint prestashop cupidatat non proident'),
(1, 2, 'Lorem ipsum dolor sit amet', 'Excepteur sint occaecat cupidatat non proident', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>', 'Lorem ipsum presta shop amet'),
(1, 3, 'Lorem ipsum dolor sit amet', 'Consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>', 'Excepteur sint prestashop cupidatat non proident'),
(1, 4, 'Lorem ipsum dolor sit amet', 'Consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>', 'Excepteur sint prestashop cupidatat non proident'),
(1, 5, 'Lorem ipsum dolor sit amet', 'Consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>', 'Excepteur sint prestashop cupidatat non proident');

INSERT INTO `PREFIX_range_price` (`id_range_price`, `id_carrier`, `delimiter1`, `delimiter2`) VALUES (1, 2, 0, 10000);
INSERT INTO `PREFIX_range_weight` (`id_range_weight`, `id_carrier`, `delimiter1`, `delimiter2`) VALUES (1, 2, 0, 10000);
INSERT INTO `PREFIX_delivery` (`id_delivery`, `id_range_price`, `id_range_weight`, `id_carrier`, `id_zone`, `price`) VALUES
(1, NULL, 1, 2, 1, 5.00),(2, NULL, 1, 2, 2, 5.00),(4, 1, NULL, 2, 1, 5.00),(5, 1, NULL, 2, 2, 5.00);

INSERT INTO `PREFIX_customer_group` (`id_customer`, `id_group`) VALUES (1, 1);
INSERT INTO `PREFIX_category_group` (`id_category`, `id_group`) VALUES (2, 1),(3, 1),(4, 1);

INSERT INTO `PREFIX_customer` (`id_customer`, `id_gender`, `id_default_group`, `secure_key`, `email`, `passwd`, `birthday`, `lastname`, `newsletter`, `optin`, `firstname`, `active`, `is_guest`, `date_add`, `date_upd`)
	VALUES (1, 1, 1, '47ce86627c1f3c792a80773c5d2deaf8', 'pub@prestashop.com', 'ad807bdf0426766c05c64041124d30ce', '1970-01-15', 'DOE', 1, 1, 'John', 1, 0, NOW(), NOW());
INSERT INTO `PREFIX_connections` (`id_connections`, `id_guest`, `id_page`, `ip_address`, `date_add`, `http_referer`)
	VALUES (1, 1, 1, '2130706433', NOW(), 'http://www.prestashop.com');
INSERT INTO `PREFIX_guest` (`id_guest`, `id_operating_system`, `id_web_browser`, `id_customer`, `javascript`, `screen_resolution_x`, `screen_resolution_y`, `screen_color`, `sun_java`, `adobe_flash`, `adobe_director`, `apple_quicktime`, `real_player`, `windows_media`, `accept_language`)
	VALUES (1, 1, 3, 1, 1, 1680, 1050, 32, 1, 1, 0, 1, 1, 0, 'en-us');

INSERT INTO `PREFIX_cart` (`id_cart`, `id_carrier`, `id_lang`, `id_address_delivery`, `id_address_invoice`, `id_currency`, `id_customer`, `id_guest`, `recyclable`, `gift`, `date_add`, `date_upd`)
	VALUES (1, 2, 2, 6, 6, 1, 1, 1, 1, 0, NOW(), NOW());
INSERT INTO `PREFIX_cart_product` (`id_cart`, `id_product`, `id_product_attribute`, `quantity`, `date_add`) VALUES (1, 7, 23, 1, NOW());
INSERT INTO `PREFIX_cart_product` (`id_cart`, `id_product`, `id_product_attribute`, `quantity`, `date_add`) VALUES (1, 9, 0, 1, NOW());

INSERT INTO `PREFIX_orders` (`id_order`, `id_carrier`, `id_lang`, `id_customer`, `id_cart`, `id_currency`, `id_address_delivery`, `id_address_invoice`, `secure_key`, `payment`, `module`, `recyclable`, `gift`, `gift_message`, `shipping_number`, `total_discounts`, `total_paid`, `total_paid_real`, `total_products`, `total_products_wt`, `total_shipping`, `total_wrapping`, `invoice_number`, `delivery_number`, `invoice_date`, `delivery_date`, `date_add`, `date_upd`)
	VALUES (1, 2, 2, 1, 1, 1, 2, 2, '47ce86627c1f3c792a80773c5d2deaf8', 'Chèque', 'cheque', 0, 0, '', '', '0.00', '625.98', '625.98', '516.72', '618.00', '7.98', '0.00', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NOW(), NOW());
INSERT INTO `PREFIX_order_detail` (`id_order_detail`, `id_order`, `product_id`, `product_attribute_id`, `product_name`, `product_quantity`, `product_quantity_return`, `product_price`, `product_quantity_discount`, `product_ean13`, `product_reference`, `product_supplier_reference`, `product_weight`, `tax_name`, `tax_rate`, `ecotax`, `download_hash`, `download_nb`, `download_deadline`)
	VALUES (1, 1, 7, 23, 'iPod touch - Capacité: 32Go', 1, 0, '392.140500', '0.000000', NULL, NULL, NULL, 0, 'TVA 19.6%', '19.60', '0.00', '', 0, '0000-00-00 00:00:00');
INSERT INTO `PREFIX_order_detail` (`id_order_detail`, `id_order`, `product_id`, `product_attribute_id`, `product_name`, `product_quantity`, `product_quantity_return`, `product_price`, `product_quantity_discount`, `product_ean13`, `product_reference`, `product_supplier_reference`, `product_weight`, `tax_name`, `tax_rate`, `ecotax`, `download_hash`, `download_nb`, `download_deadline`)
	VALUES (2, 1, 9, 0, 'Écouteurs à isolation sonore Shure SE210', 1, 0, '124.581900', '0.000000', NULL, NULL, NULL, 0, 'TVA 19.6%', '19.60', '0.00', '', 0, '0000-00-00 00:00:00');
INSERT INTO `PREFIX_order_history` (`id_order_history`, `id_employee`, `id_order`, `id_order_state`, `date_add`) VALUES (1, 0, 1, 1, NOW());

INSERT INTO `PREFIX_manufacturer` (`id_manufacturer`, `name`, `date_add`, `date_upd`, `active`) VALUES (1, 'Apple Computer, Inc', NOW(), NOW(), 1);
INSERT INTO `PREFIX_manufacturer` (`id_manufacturer`, `name`, `date_add`, `date_upd`, `active`) VALUES(2, 'Shure Incorporated', NOW(), NOW(), 1);

INSERT INTO `PREFIX_address` (`id_address`, `id_country`, `id_state`, `id_customer`, `id_manufacturer`, `id_supplier`, `alias`, `lastname`, `firstname`, `address1`, `postcode`, `city`, `phone`, `date_add`, `date_upd`, `active`, `deleted`)
	VALUES (1, 21, 5, 0, 1, 0, 'manufacturer', 'JOBS', 'STEVE', '1 Infinite Loop', '95014', 'Cupertino', '(800) 275-2273', NOW(), NOW(), 1, 0);
INSERT INTO `PREFIX_address` (`id_address`, `id_country`, `id_state`, `id_customer`, `id_manufacturer`, `id_supplier`, `alias`, `company`, `lastname`, `firstname`, `address1`, `address2`, `postcode`, `city`, `phone`, `date_add`, `date_upd`, `active`, `deleted`)
	VALUES (2, 8, 0, 1, 0, 0, 'Mon adresse', 'My Company', 'DOE', 'John', '16, Main street', '2nd floor', '75000', 'Paris ', '0102030405', NOW(), NOW(), 1, 0);

INSERT INTO `PREFIX_supplier` (`id_supplier`, `name`, `date_add`, `date_upd`, `active`) VALUES (1, 'AppleStore', NOW(), NOW(), 1);
INSERT INTO `PREFIX_supplier` (`id_supplier`, `name`, `date_add`, `date_upd`, `active`) VALUES (2, 'Shure Online Store', NOW(), NOW(), 1);

INSERT INTO `PREFIX_product` (`id_product`, `indexed`, `id_supplier`, `id_manufacturer`, `id_tax_rules_group`, `id_category_default`, `id_color_default`, `on_sale`, `online_only`, `ean13`, `ecotax`, `quantity`, `price`, `wholesale_price`, `reference`, `supplier_reference`, `weight`, `out_of_stock`, `quantity_discount`, `customizable`, `uploadable_files`, `text_fields`, `active`, `date_add`, `date_upd`) VALUES
(1, 1, 1, 1, 1, 2, 2, 0, 0, '0', 0.00, 800, 124.581940, 70.000000, '', '', 0.5, 2, 0, 0, 0, 0, 1, NOW(), NOW()),
(2, 1, 1, 1, 1, 2, 0, 0, 0, '0', 0.00, 100, 66.053500, 33.000000, '', '', 0, 2, 0, 0, 0, 0, 1, NOW(), NOW()),
(5, 1, 1, 1, 1, 4, 0, 0, 0, '0', 0.00, 274, 1504.180602, 1000.000000, '', NULL, 1.36, 2, 0, 0, 0, 0, 1, NOW(), NOW()),
(6, 1, 1, 1, 1, 4, 0, 0, 0, '0', 0.00, 250, 1170.568561, 0.000000, '', NULL, 0.75, 2, 0, 0, 0, 0, 1, NOW(), NOW()),
(7, 1, 0, 0, 1, 2, 0, 0, 0, '', 0.00, 180, 241.638796, 200.000000, '', NULL, 0, 2, 0, 0, 0, 0, 1, NOW(), NOW()),
(8, 1, 0, 0, 1, 3, 0, 0, 1, '', 0.00, 1, 25.041806, 0.000000, '', NULL, 0, 2, 0, 0, 0, 0, 1, NOW(), NOW()),
(9, 1, 2, 2, 1, 3, 0, 0, 1, '', 0.00, 1, 124.581940, 0.000000, '', NULL, 0, 2, 0, 0, 0, 0, 1, NOW(), NOW());


INSERT INTO `PREFIX_product_lang` (`id_product`, `id_lang`, `description`, `description_short`, `link_rewrite`, `meta_description`, `meta_keywords`, `meta_title`, `name`, `available_now`, `available_later`) VALUES
(1, 1, '<p><strong><span style="font-size: small;">Curved ahead of the curve.</span></strong></p>\r\n<p>For those about to rock, we give you nine amazing colors. But that''s only part of the story. Feel the curved, all-aluminum and glass design and you won''t want to put iPod nano down.</p>\r\n<p><strong><span style="font-size: small;">Great looks. And brains, too.</span></strong></p>\r\n<p>The new Genius feature turns iPod nano into your own highly intelligent, personal DJ. It creates playlists by finding songs in your library that go great together.</p>\r\n<p><strong><span style="font-size: small;">Made to move with your moves.</span></strong></p>\r\n<p>The accelerometer comes to iPod nano. Give it a shake to shuffle your music. Turn it sideways to view Cover Flow. And play games designed with your moves in mind.</p>', '<p>New design. New features. Now in 8GB and 16GB. iPod nano rocks like never before.</p>', 'ipod-nano', '', '', '', 'iPod Nano', 'In stock', ''),
(1, 2, '<p><span style="font-size: small;"><strong>Des courbes avantageuses.</strong></span></p>\r\n<p>Pour les amateurs de sensations, voici neuf nouveaux coloris. Et ce n''est pas tout ! Faites l''expérience du design elliptique en aluminum et verre. Vous ne voudrez plus le lâcher.</p>\r\n<p><strong><span style="font-size: small;">Beau et intelligent.</span></strong></p>\r\n<p>La nouvelle fonctionnalité Genius fait d''iPod nano votre DJ personnel. Genius crée des listes de lecture en recherchant dans votre bibliothèque les chansons qui vont bien ensemble.</p>\r\n<p><strong><span style="font-size: small;">Fait pour bouger avec vous.</span></strong></p>\r\n<p>iPod nano est équipé de l''accéléromètre. Secouez-le pour mélanger votre musique. Basculez-le pour afficher Cover Flow. Et découvrez des jeux adaptés à vos mouvements.</p>', '<p>Nouveau design. Nouvelles fonctionnalités. Désormais en 8 et 16 Go. iPod nano, plus rock que jamais.</p>', 'ipod-nano', '', '', '', 'iPod Nano', 'En stock', ''),
(2, 1, '<p><span style="font-size: small;"><strong>Instant attachment.</strong></span></p>\r\n<p>Wear up to 500 songs on your sleeve. Or your belt. Or your gym shorts. iPod shuffle is a badge of musical devotion. Now in new, more brilliant colors.</p>\r\n<p><span style="font-size: small;"><strong>Feed your iPod shuffle.</strong></span></p>\r\n<p>iTunes is your entertainment superstore. It’s your ultra-organized music collection and jukebox. And it’s how you load up your iPod shuffle in one click.</p>\r\n<p><span style="font-size: small;"><strong>Beauty and the beat.</strong></span></p>\r\n<p>Intensely colorful anodized aluminum complements the simple design of iPod shuffle. Now in blue, green, pink, red, and original silver.</p>', '<p>iPod shuffle, the world’s most wearable music player, now clips on in more vibrant blue, green, pink, and red.</p>', 'ipod-shuffle', '', '', '', 'iPod shuffle', 'In stock', ''),
(2, 2, '<p><span style="font-size: small;"><strong>Un lien immédiat.</strong></span></p>\r\n<p>Portez jusqu''à 500 chansons accrochées à votre manche, à votre ceinture ou à votre short. Arborez votre iPod shuffle comme signe extérieur de votre passion pour la musique. Existe désormais en quatre nouveaux coloris encore plus éclatants.</p>\r\n<p><span style="font-size: small;"><strong>Emplissez votre iPod shuffle.</strong></span></p>\r\n<p>iTunes est un immense magasin dédié au divertissement, une collection musicale parfaitement organisée et un jukebox. Vous pouvez en un seul clic remplir votre iPod shuffle de chansons.</p>\r\n<p><strong><span style="font-size: small;">La musique en technicolor.</span></strong></p>\r\n<p>iPod shuffle s''affiche désormais dans de nouveaux coloris intenses qui rehaussent le design épuré du boîtier en aluminium anodisé. Choisissez parmi le bleu, le vert, le rose, le rouge et l''argenté d''origine.</p>', '<p>iPod shuffle, le baladeur le plus portable du monde, se clippe maintenant en bleu, vert, rose et rouge.</p>', 'ipod-shuffle', '', '', '', 'iPod shuffle', 'En stock', ''),
(5, 1, '<p>MacBook Air is nearly as thin as your index finger. Practically every detail that could be streamlined has been. Yet it still has a 13.3-inch widescreen LED display, full-size keyboard, and large multi-touch trackpad. It’s incomparably portable without the usual ultraportable screen and keyboard compromises.</p><p>The incredible thinness of MacBook Air is the result of numerous size- and weight-shaving innovations. From a slimmer hard drive to strategically hidden I/O ports to a lower-profile battery, everything has been considered and reconsidered with thinness in mind.</p><p>MacBook Air is designed and engineered to take full advantage of the wireless world. A world in which 802.11n Wi-Fi is now so fast and so available, people are truly living untethered — buying and renting movies online, downloading software, and sharing and storing files on the web. </p>', 'MacBook Air is ultrathin, ultraportable, and ultra unlike anything else. But you don’t lose inches and pounds overnight. It’s the result of rethinking conventions. Of multiple wireless innovations. And of breakthrough design. With MacBook Air, mobile computing suddenly has a new standard.', 'macbook-air', '', '', '', 'MacBook Air', '', NULL),
(5, 2, '<p>MacBook Air est presque aussi fin que votre index. Pratiquement tout ce qui pouvait être simplifié l''a été. Il n''en dispose pas moins d''un écran panoramique de 13,3 pouces, d''un clavier complet et d''un vaste trackpad multi-touch. Incomparablement portable il vous évite les compromis habituels en matière d''écran et de clavier ultra-portables.</p><p>L''incroyable finesse de MacBook Air est le résultat d''un grand nombre d''innovations en termes de réduction de la taille et du poids. D''un disque dur plus fin à des ports d''E/S habilement dissimulés en passant par une batterie plus plate, chaque détail a été considéré et reconsidéré avec la finesse à l''esprit.</p><p>MacBook Air a été conçu et élaboré pour profiter pleinement du monde sans fil. Un monde dans lequel la norme Wi-Fi 802.11n est désormais si rapide et si accessible qu''elle permet véritablement de se libérer de toute attache pour acheter des vidéos en ligne, télécharger des logicééééiels, stocker et partager des fichiers sur le Web. </p>', 'MacBook Air est ultra fin, ultra portable et ultra différent de tout le reste. Mais on ne perd pas des kilos et des centimètres en une nuit. C''est le résultat d''une réinvention des normes. D''une multitude d''innovations sans fil. Et d''une révolution dans le design. Avec MacBook Air, l''informatique mobile prend soudain une nouvelle dimension.', 'macbook-air', '', '', '', 'MacBook Air', '', NULL),
(6, 1, 'Every MacBook has a larger hard drive, up to 250GB, to store growing media collections and valuable data.<br /><br />The 2.4GHz MacBook models now include 2GB of memory standard — perfect for running more of your favorite applications smoothly.', 'MacBook makes it easy to hit the road thanks to its tough polycarbonate case, built-in wireless technologies, and innovative MagSafe Power Adapter that releases automatically if someone accidentally trips on the cord.', 'macbook', '', '', '', 'MacBook', '', NULL),
(6, 2, 'Chaque MacBook est équipé d''un disque dur plus spacieux, d''une capacité atteignant 250 Go, pour stocker vos collections multimédia en expansion et vos données précieuses.<br /><br />Le modèle MacBook à 2,4 GHz intègre désormais 2 Go de mémoire en standard. L''idéal pour exécuter en souplesse vos applications préférées.', 'MacBook vous offre la liberté de mouvement grâce à son boîtier résistant en polycarbonate, à ses technologies sans fil intégrées et à son adaptateur secteur MagSafe novateur qui se déconnecte automatiquement si quelqu''un se prend les pieds dans le fil.', 'macbook', '', '', '', 'MacBook', '', NULL),
(7, 1, '<h3>Five new hands-on applications</h3>\r\n<p>View rich HTML email with photos as well as PDF, Word, and Excel attachments. Get maps, directions, and real-time traffic information. Take notes and read stock and weather reports.</p>\r\n<h3>Touch your music, movies, and more</h3>\r\n<p>The revolutionary Multi-Touch technology built into the gorgeous 3.5-inch display lets you pinch, zoom, scroll, and flick with your fingers.</p>\r\n<h3>Internet in your pocket</h3>\r\n<p>With the Safari web browser, see websites the way they were designed to be seen and zoom in and out with a tap.<sup>2</sup> And add Web Clips to your Home screen for quick access to favorite sites.</p>\r\n<h3>What''s in the box</h3>\r\n<ul>\r\n<li><span></span>iPod touch</li>\r\n<li><span></span>Earphones</li>\r\n<li><span></span>USB 2.0 cable</li>\r\n<li><span></span>Dock adapter</li>\r\n<li><span></span>Polishing cloth</li>\r\n<li><span></span>Stand</li>\r\n<li><span></span>Quick Start guide</li>\r\n</ul>', '<ul>\r\n<li>Revolutionary Multi-Touch interface</li>\r\n<li>3.5-inch widescreen color display</li>\r\n<li>Wi-Fi (802.11b/g)</li>\r\n<li>8 mm thin</li>\r\n<li>Safari, YouTube, Mail, Stocks, Weather, Notes, iTunes Wi-Fi Music Store, Maps</li>\r\n</ul>', 'ipod-touch', '', '', '', 'iPod touch', '', NULL),
(7, 2, '<h1>Titre 1</h1>\r\n<h2>Titre 2</h2>\r\n<h3>Titre 3</h3>\r\n<h4>Titre 4</h4>\r\n<h5>Titre 5</h5>\r\n<h6>Titre 6</h6>\r\n<ul>\r\n<li>UL</li>\r\n<li>UL</li>\r\n<li>UL</li>\r\n<li>UL</li>\r\n</ul>\r\n<ol>\r\n<li>OL</li>\r\n<li>OL</li>\r\n<li>OL</li>\r\n<li>OL</li>\r\n</ol>\r\n<p>paragraphe...</p>\r\n<p>paragraphe...</p>\r\n<p>paragraphe...</p>\r\n<table border="0">\r\n<thead> \r\n<tr>\r\n<th>th</th> <th>th</th> <th>th</th>\r\n</tr>\r\n</thead> \r\n<tbody>\r\n<tr>\r\n<td>td</td>\r\n<td>td</td>\r\n<td>td</td>\r\n</tr>\r\n<tr>\r\n<td>td</td>\r\n<td>td</td>\r\n<td>td</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<h3>Cinq nouvelles applications sous la main</h3>\r\n<p>Consultez vos e-mails au format HTML enrichi, avec photos et pieces jointes au format PDF, Word et Excel. Obtenez des cartes, des itinéraires et des informations sur l''état de la circulation en temps réel. Rédigez des notes et consultez les cours de la Bourse et les bulletins météo.</p>\r\n<h3>Touchez du doigt votre musique et vos vidéos. Entre autres.</h3>\r\n<p>La technologie multi-touch révolutionnaire intégrée au superbe écran de 3,5 pouces vous permet d''effectuer des zooms avant et arrière, de faire défiler et de feuilleter des pages à l''aide de vos seuls doigts.</p>\r\n<h3>Internet dans votre poche</h3>\r\n<p>Avec le navigateur Safari, vous pouvez consulter des sites web dans leur mise en page d''origine et effectuer un zoom avant et arrière d''une simple pression sur l''écran.</p>\r\n<h3>Contenu du coffret</h3>\r\n<ul>\r\n<li><span></span>iPod touch</li>\r\n<li><span></span>Écouteurs</li>\r\n<li><span></span>Câble USB 2.0</li>\r\n<li><span></span>Adaptateur Dock</li>\r\n<li><span></span>Chiffon de nettoyage</li>\r\n<li><span></span>Support</li>\r\n<li><span></span>Guide de démarrage rapide</li>\r\n</ul>\r\n<p> </p>', '<p>Interface multi-touch révolutionnaire<br />Écran panoramique couleur de 3,5 pouces<br />Wi-Fi (802.11b/g)<br />8 mm d''épaisseur<br />Safari, YouTube, iTunes Wi-Fi Music Store, Courrier, Cartes, Bourse, Météo, Notes</p>', 'ipod-touch', '', '', '', 'iPod touch', 'En stock', NULL),
(8, 1, '<p>Lorem ipsum</p>', '<p>Lorem ipsum</p>', 'belkin-leather-folio-for-ipod-nano-black-chocolate', '', '', '', 'Belkin Leather Folio for iPod nano - Black / Chocolate', '', NULL),
(8, 2, '<p><strong>Caractéristiques</strong></p>\r\n<li>Cuir doux résistant<br /> </li>\r\n<li>Accès au bouton Hold<br /> </li>\r\n<li>Fermeture magnétique<br /> </li>\r\n<li>Accès au Dock Connector<br /> </li>\r\n<li>Protège-écran</li>', '<p>Cet étui en cuir tendance assure une protection complète contre les éraflures et les petits aléas de la vie quotidienne. Sa conception élégante et compacte vous permet de glisser votre iPod directement dans votre poche ou votre sac à main.</p>', 'housse-portefeuille-en-cuir-ipod-nano-noir-chocolat', '', '', '', 'Housse portefeuille en cuir (iPod nano) - Noir/Chocolat', '', NULL),
(9, 1, '<div class="product-overview-full">Using Hi-Definition MicroSpeakers to deliver full-range audio, the ergonomic and lightweight design of the SE210 earphones is ideal for premium on-the-go listening on your iPod or iPhone. They offer the most accurate audio reproduction from both portable and home stereo audio sources--for the ultimate in precision highs and rich low end. In addition, the flexible design allows you to choose the most comfortable fit from a variety of wearing positions. <br /> <br /> <strong>Features </strong> <br /> \r\n<ul>\r\n<li>Sound-isolating design </li>\r\n<li> Hi-Definition MicroSpeaker with a single balanced armature driver </li>\r\n<li> Detachable, modular cable so you can make the cable longer or shorter depending on your activity </li>\r\n<li> Connector compatible with earphone ports on both iPod and iPhone </li>\r\n</ul>\r\n<strong>Specifications </strong><br /> \r\n<ul>\r\n<li>Speaker type: Hi-Definition MicroSpeaker </li>\r\n<li> Frequency range: 25Hz-18.5kHz </li>\r\n<li> Impedance (1kHz): 26 Ohms </li>\r\n<li> Sensitivity (1mW): 114 dB SPL/mW </li>\r\n<li> Cable length (with extension): 18.0 in./45.0 cm (54.0 in./137.1 cm) </li>\r\n</ul>\r\n<strong>In the box</strong><br /> \r\n<ul>\r\n<li>Shure SE210 earphones </li>\r\n<li> Extension cable (36.0 in./91.4 cm) </li>\r\n<li> Three pairs foam earpiece sleeves (small, medium, large) </li>\r\n<li> Three pairs soft flex earpiece sleeves (small, medium, large) </li>\r\n<li> One pair triple-flange earpiece sleeves </li>\r\n<li> Carrying case </li>\r\n</ul>\r\nWarranty<br /> Two-year limited <br />(For details, please visit <br />www.shure.com/PersonalAudio/CustomerSupport/ProductReturnsAndWarranty/index.htm.) <br /><br /> Mfr. Part No.: SE210-A-EFS <br /><br />Note: Products sold through this website that do not bear the Apple Brand name are serviced and supported exclusively by their manufacturers in accordance with terms and conditions packaged with the products. Apple''s Limited Warranty does not apply to products that are not Apple-branded, even if packaged or sold with Apple products. Please contact the manufacturer directly for technical support and customer service.</div>', '<p>Evolved from personal monitor technology road-tested by pro musicians and perfected by Shure engineers, the lightweight and stylish SE210 delivers full-range audio that''s free from outside noise.</p>', 'ecouteurs-a-isolation-sonore-shure-se210-blanc', '', '', '', 'Shure SE210 Sound-Isolating Earphones for iPod and iPhone', '', NULL),
(9, 2, '<p>Basés sur la technologie des moniteurs personnels testée sur la route par des musiciens professionnels et perfectionnée par les ingénieurs Shure, les écouteurs SE210, légers et élégants, fournissent une sortie audio à gamme étendue exempte de tout bruit externe.</p><br /><p><strong>Conception à isolation sonore <br /></strong>Les embouts à isolation sonore fournis bloquent plus de 90 % du bruit ambiant. Combinés à un design ergonomique séduisant et un câble modulaire, ils minimisent les intrusions du monde extérieur, vous permettant de vous concentrer sur votre musique. Conçus pour les amoureux de la musique qui souhaitent faire évoluer leur appareil audio portable, les écouteurs SE210 vous permettent d''emmener la performance avec vous. <br /> <br /><strong>Micro-transducteur haute définition <br /></strong>Développés pour une écoute de qualité supérieure en déplacement, les écouteurs SE210 utilisent un seul transducteur à armature équilibrée pour bénéficier d''une gamme audio étendue. Le résultat ? Un confort d''écoute époustouflant qui restitue tous les détails d''un spectacle live.</p>\r\n<p><strong>Le kit universel Deluxe comprend les éléments suivants : <br /></strong>- <strong><em>Embouts à isolation sonore</em></strong> <br />Les embouts à isolation sonore inclus ont un double rôle : bloquer les bruits ambiants et garantir un maintien et un confort personnalisés. Comme chaque oreille est différente, le kit universel Deluxe comprend trois tailles (S, M, L) d''embouts mousse et flexibles. Choisissez la taille et le style d''embout qui vous conviennent le mieux : une bonne étanchéité est un facteur clé pour optimiser l''isolation sonore et la réponse des basses, ainsi que pour accroître le confort en écoute prolongée.<br /><br />- <em><strong>Câble modulaire</strong></em> <br />En se basant sur les commentaires de nombreux utilisateurs, les ingénieurs de Shure ont développé une solution de câble détachable pour permettre un degré de personnalisation sans précédent. Le câble de 1 mètre fourni vous permet d''adapter votre confort en fonction de l''activité et de l''application.<br /> <br />- <em><strong>Étui de transport</strong></em> <br />Outre les embouts à isolation sonore et le câble modulaire, un étui de transport compact et résistant est fourni avec les écouteurs SE210 pour vous permettre de ranger vos écouteurs de manière pratique et sans encombres.<br /> <br />- <strong><em>Garantie limitée de deux ans <br /></em></strong>Chaque solution SE210 achetée est couverte par une garantie pièces et main-d''œuvre de deux ans.<br /><br /><strong>Caractéristiques techniques</strong></p>\r\n<ul>\r\n<li> Type de transducteur : micro-transducteur haute définition<br /></li>\r\n<li> Sensibilité (1 mW) : pression acoustique de 114 dB/mW<br /></li>\r\n<li> Impédance (à 1 kHz) : 26 W<br /></li>\r\n<li> Gamme de fréquences : 25 Hz – 18,5 kHz<br /></li>\r\n<li> Longueur de câble / avec rallonge : 45 cm / 136 cm<br /></li>\r\n</ul>\r\n<p><strong>Contenu du coffret<br /></strong></p>\r\n<ul>\r\n<li> Écouteurs Shure SE210<br /></li>\r\n<li> Kit universel Deluxe (embouts à isolation sonore, câble modulaire, étui de transport)</li>\r\n</ul>', '<p>Les écouteurs à isolation sonore ergonomiques et légers offrent la reproduction audio la plus fidèle en provenance de sources audio stéréo portables ou de salon.</p>', 'ecouteurs-a-isolation-sonore-shure-se210', '', '', '', 'Écouteurs à isolation sonore Shure SE210', '', NULL),
(7, 3, '<p>Cinco nuevas aplicaciones a mano</p>\r\n<p><br /> Consulta tu correo en formato HTML enriquecido, con fotos y ficheros adjuntos en formato PDF, Word y Excel. Consigue mapas, itinerarios e información sobre el estado de la carreteras en tiempo real. Escribe notas y consulta la bolsa y el tiempo. <br /> Alcanza con un dedo tu música y tus videos, entre otras cosas. <br /> La tecnología multi-touch revolucionaria integrada a la magnífica pantalla de 3,5 pulgadas te permitirá efectuar zoom hacia adelante y hacia atrás, y pasar y ojear las páginas solo con la ayuda de tus dedos.</p>\r\n<p>Internet en tu bolsillo</p>\r\n<p>Con el navegador Safari, podrás consultar sitios web en su compaginación de origen y efectuar un zoom hacia adelante y hacia atrás con la simple presión de un dedo en la pantalla.</p>\r\n<p>Contenido del estuche<br />    * iPod touch<br />    * Auriculares<br />    * Cable USB 2.0<br />    * Adaptador Dock<br />    * Paño de limpieza<br />    * Base<br />    * Guía de inicio rápido<br /> Título<br /> Párrafo</p>', '<p>Interfaz multi-touch revolucionaria<br /> Pantalla panorámica color de 3,5 pulgadas<br /> Wi-Fi (802.11b/g)<br /> 8 mm de espesor<br /> Safari, YouTube, iTunes Wi-Fi Music Store, Correo, Mapas, Bolsa, El tiempo, Notas</p>', 'ipod-touch', '', '', '', 'iPod touch', 'Disponible', ''),
(1, 3, '<p><strong>Curvas aerodinámicas.</strong></p>\r\n<p>Para los aficionados a las sensaciones fuertes, os presentamos nueve nuevos colores. ¡ Y eso no es todo ! Experimenta el diseño elíptico de aluminio y vidrio. ¡ No querrás separarte de él nunca más !</p>\r\n<p><strong><br /> Estético e inteligente.</strong></p>\r\n<p>La nueva aplicación Genius hace de iPod nano tu discjockey personal. Genuis crea listas de lectura buscando en tu biblioteca las canciones que combinan entre si.</p>\r\n<p><strong><br /> Hecho para moverse contigo.</strong><br /> iPod nano está equipado de un acelerómetro. Muévelo para mezclar tu música. Voltéalo para mostrar Cover Flow. Y descubre juegos adaptados a tus movimientos.</p>', '<p>Nuevo diseño. Nuevas aplicaciones. Ahora disponible en 8 y 16 Go. iPod nano, más rock que nunca.</p>', 'ipod-nano', '', '', '', 'iPod Nano', 'Disponible', ''),
(2, 3, '<p><strong>Un enlace inmediato.</strong><br /> <br /> Lleva hasta 500 canciones colgadas de tu manga, de tu cinturón o de tu pantalón. Presume con tu iPod shuffle como signo exterior de tu pasión por la música. Ahora ya existen cuatro nuevos colores más llamativos. <br /> <br /> <strong>Llena tu iPod shuffle.</strong><br /> <br /> iTunes es una enorme tienda dedicada a la diversión, una colección de música organizada perfectamente y un jukebox. Con tan solo un clic puedes llenar tu iPod shuffle con canciones. <br /> <br /> <strong>La música en tecnicolor. </strong><br /> <br /> iPod shuffle presenta nuevos colores vivos que realzan su diseño estilizado en aluminio anodizado. Elige entre azul, verde, rosa, rojo y el plateado de origen.</p>', '<p>iPod shuffle, el walkman más portátil del mundo, ahora en azul, verde, rosa y rojo.</p>', 'ipod-shuffle', '', '', '', 'iPod shuffle', 'Disponible', ''),
(6, 3, '<p>Cada MacBook está equipado de un disco duro más espacioso, de una capacidad de hasta  250 Go, para almacenar tus colecciones multimedia en expansión y tus datos más preciados. <br /> El modelo  MacBook de 2,4 GHz integra 2 Go de memoria en estándar. Lo ideal para realizar sin dificultad tus aplicaciones preferidas.</p>', '<p>MacBook te ofrece una gran libertad de movimientos gracias a su exterior resistente en policarbonato, a su tecnología sin cable y a su adaptador cargador sector innovador que se desconecta automáticamente si alguien se engancha en el cable.</p>', 'macbook', '', '', '', 'MacBook', 'Disponible', ''),
(5, 3, '<p>MacBook Air es casi tan fino como tu dedo. Se ha simplificado al máximo y a pesar de ello dispone de una pantalla panorámica de 13,3 pulgadas, de un teclado completo y de un amplio trackpad multi-touch. Portátil al 100%, te evitará tener que hacer un compromiso en lo que concierne a la pantalla y al teclado.  <br /> <br /> La increíble sutileza de MacBook Air es el resultado de un gran número de innovaciones en materia de reducción de tamaño y peso. Desde un disco duro más fino hasta puertos E/S disimulados hábilmente pasando por una batería más plana, cada detalle se consideró para que el resultado fuera lo más fino posible.<br /> <br /> MacBook Air fue creado y elaborado para disfrutar plenamente del mundo inalámbrico. Un mundo en el que la norma Wi-Fi 802.11n es tan rápida y accesible que permite liberarse completamente de cualquier atadura para comprar videos en línea, descargar programas, almacenar y compartir archivos en la Red.</p>', '<p>MacBook Air es ultra fino, ultra portátil y ultra diferente de todo el resto. Pero no se pierden kilos y centímetros en tan solo una noche. Todo esto es el resultado de un nuevo invento de normas. De un sinfín de novedades sin cable. Y de una revolución en el diseño. Con MacBook Air, la informática móvil adquiere una nueva dimensión.</p>', 'macbook-air', '', '', '', 'MacBook Air', 'Disponible', ''),
(8, 3, '<p><strong>Características</strong></p>\r\n<ul>\r\n<li> Cuero suave resistente</li>\r\n<li>Acceso a la tecla Hold</li>\r\n<li>Cierre magnético</li>\r\n<li>Acceso al Dock Conector</li>\r\n<li>Salva pantallas</li>\r\n</ul>', '<p>Este estuche de cuero de última moda garantiza una completa protección contra los arañazos y los pequeños contratiempos de la vida diaria. Su diseño elegante y compacto te permite meter tu Ipod directamente en tu bolsillo o en tu bolso.</p>', 'funda-cuero-ipod-nano-negro-chocolate', '', '', '', 'Leather Case (iPod nano) - Negro / Chocolate', 'Disponible', ''),
(9, 3, '<p>Los auriculares SE210, ligeros y elegantes, están basados en la tecnología de los monitores personales que los músicos profesionales utilizan en carretera y que los ingenieros de Shure han perfeccionado. También están provistos de una salida audio de gama extendida exenta de todo ruido exterior. <br /> <br /> <strong>Creado para un aislamiento sonoro</strong></p>\r\n<p>Las almohadillas provistas de un aislamiento sonoro bloquean más del 90% del ruido ambiente. Combinadas con un diseño ergonómico atractivo y un cable modular, minimizan las intrusiones del mundo exterior y te permiten concentrarte en tu música. Creados para los apasionados por la música que quieren que su aparato audio móvil evolucione, los auriculares  SE210 te permitirán llevar la tecnología allí donde tú vayas. <br /> <br /> <strong>Micro-transductor alta definición </strong><br /> Desarrollados para poder tener una audición de calidad durante los desplazamientos, los auriculares SE210 utilizan un único transductor con un armazón equilibrado para poder disfrutar de una gama audio extendida. ¿El resultado ? Un confort audio increíble que restituye cada detalle de un espectáculo en directo. <br /> <br /> <strong>El kit universal Deluxe incluye los siguientes elementos :</strong><br /> - Almohadillas para aislamiento sonoro<br /> Las almohadillas para el aislamiento sonoro tienen una doble función : bloquear el ruido ambiente y garantizar una estabilidad y un confort personalizados. Como cada oreja es diferente el kit universal Deluxe incluye tres tallas (S, M, L) de almohadillas de espuma y flexibles. Elige la talla y el estilo de almohadilla que mejor te convenga : un buen aislamiento es un factor clave tanto para optimizar el aislamiento sonoro y la respuesta de los bajos como para aumentar el confort durante una audición prolongada. <br /> <br /> - Cable modular</p>\r\n<p>Basándose en los comentarios de los numerosos usuarios, los ingenieros de Shure han creado una solución de cable separable para permitir un grado de personalización sin precedentes. El cable de 1 metro te permite adaptar el confort en función de la actividad del momento y de la aplicación. <br /> <br /> - Estuche para el transporte</p>\r\n<p>Además de las almohadillas de aislamiento sonoro y del cable modular, los auriculares SE210 están provistos de un estuche de transporte compacto y resistente para guardar los auriculares de manera práctica y sin dificultad. <br /> - Garantía límite de dos años <br /> Cada solución SE210 tiene una garantía piezas y mano de obra de dos años.</p>\r\n<p><br /> <em><strong>Características técnicas</strong></em></p>\r\n<ul>\r\n<li>Tipo de transductor : micro-transductor alta definición</li>\r\n<li>Sensibilidad (1 mW) : presión acústica de 114 dB/mW</li>\r\n<li>Impedancia : (à 1 kHz) : 26 W</li>\r\n<li>Gama de frecuencias : 25 Hz ˆ 18,5 kHz</li>\r\n<li>Longitud del cable / con alargador : 45 cm / 136 cm</li>\r\n</ul>\r\n<p><br /> <strong><em>Contenido de la caja</em></strong></p>\r\n<ul>\r\n<li>Altavoces Shure SE210</li>\r\n<li>Kit universal Deluxe (almohadillas de aislamiento sonoro, cable modular, estuche de transporte)</li>\r\n</ul>', '<p>Los auriculares con aislamiento ergonómicos y ligeros ofrecen la reproducción más fiel proveniente de fuentes audio estéreo móviles o de salón.</p>', 'auriculares-aislantes-del-sonido-shure-se210', '', '', '', 'Auriculares aislantes del sonido Shure SE210', 'Disponible', ''),
(1, 4, '<p><span style="font-size: small;">Immer eine Kurve voraus.</span></strong></p>\r\n<p>Für all die, die gleich losrocken wollen, gibt es jetzt neun tolle Farben zur Auswahl. Aber das ist nur ein Teil der Geschichte. Mit seinem runden Design, das komplett aus Aluminium und Glas besteht, werden Sie den iPod nano nicht mehr weglegen wollen.</p>\r\n<p><span style="font-size: small;">Tolles Design. Und viel Köpfchen.</Span></strong></p>\r\n<p>Die neue Genius-Funktion verwandelt den iPod nano in Ihren hoch intelligenten, persönlichen DJ. Es erstellt Abspiellisten aus den Songs in Ihrer Sammlung, die gut zusammenpassen.</P>\r\n<p><span style="font-size: small;">Passt sich Ihren Bewegungen an.</span></strong></p>\r\n<p>Der iPod nano jetzt mit Beschleunigungsmesser. Einmal schütteln, und Ihre Musik wird neu sortiert. Kippen Sie es zur Seite für die Cover Flow-Ansicht. Und spielen Sie mit den Bewegungen, an die Sie denken.</P>', '<p>New design. New features. Now in 8GB and 16GB. iPod nano rocks like never before.</p>', 'ipod-nano', '', '', '', 'iPod Nano', 'In stock', ''),
(2, 4, '<p>style="font-size: small;"><strong>Gleich festmachen.</strong></span></p>\r\n<p>Tragen Sie bis zu 500 Songs am Ärmel. Oder an Ihrem Gürtel. Oder an Ihrer Sporthose. iPod shuffle ist ein Erkennungszeichen echter Musikfans. Jetzt in neuen, noch leuchtenderen Farben.</P>\r\n<p>style="font-size: small;"><strong>Füttern Sie Ihren iPod shuffle.</Strong></span></p>\r\n<p>iTunes ist Ihr Super-Store für Unterhaltung. Es ist Ihre optimal organisierte Musik-Sammlung und Jukebox. Und Sie können Ihren iPod shuffle mit einem Klick laden.</P>\r\n<p>style="font-size: small;"><strong>Die Schöne und der Beat.</strong></span></p>\r\n<p>Das farbintensive eloxierte Aluminium ergänzt das schlichte Design des iPod shuffle. Jetzt in Blau, Grün, Rosa, Rot und klassischem Silber.</P>', '<p>iPod shuffle, the worldâs most wearable music player, now clips on in more vibrant blue, green, pink, and red.</p>', 'ipod-shuffle', '', '', '', 'iPod shuffle', 'In stock', ''),
(5, 4, '<p>MacBook Air ist kaum dicker als Ihr Zeigefinger. Nahezu jedes Detail wurde abgeflacht. Und dabei hat es immer noch einen 13,3-Zoll-Widescreen-LED-Display, eine Tastatur in voller Größe und einen großen Multi-Touch-Trackpad. Es besitzt eine unvergleichliche Tragbarkeit, ohne die üblichen Kompromisse für ultraportable Bildschirme und Tastaturen.</P>Der unglaublich dünne MacBook Air ist das Ergebnis zahlreicher Innovationen zur Größen- und Gewichtsoptimierung. Die flachere Festplatte, die strategisch versteckten I/O-Ports und eine noch flachere Batterie: Alles wurde immer wieder überdacht, immer mit dem Ziel, es noch dünner zu gestalten.</P>Das Design und Konzept von MacBook Air ist voll auf die Vorteile der Kabelfreiheit ausgerichtet. Eine Welt, in der 802.11n WLAN heutzutage so schnell und so leicht verfügbar ist, dass die Menschen heute grenzenlos Filme online kaufen oder mieten, Software downloaden und Dateien über das Internet teilen oder speichern können. </P>', 'MacBook Air is ultrathin, ultraportable, and ultra unlike anything else. But you donât lose inches and pounds overnight. Itâs the result of rethinking conventions. Of multiple wireless innovations. And of breakthrough design. With MacBook Air, mobile computing suddenly has a new standard.', 'macbook-Air', '', '', '', 'MacBook Air', '', NULL),
(6, 4, 'Jedes MacBook verfügt über eine größere Festplatte, bis zu 250GB, zum Speichern immer größer werdender Mediensammlungen und wertvoller Daten.<br/><br/>Die 2,4 GHz MacBook-Modelle haben nun 2 GB Standard-Arbeitsspeicher - ideal zum reibungslosen Abspielen Ihrer Lieblings-Anwendungen.', 'MacBook makes it easy to hit the road thanks to its tough polycarbonate case, built-in wireless technologies, and innovative MagSafe Power Adapter that releases automatically if someone accidentally trips on the cord.', 'macbook', '', '', '', 'MacBook', '', NULL),
(7, 4, '<h3>Fünf neue Hands-on-Anwendungen</h3>\r\n<p>Rich-HTML-E-Mails mit Fotos anzeigen sowie PDF-, Word-und Excel-Anhänge. Holen Sie sich Karten, Wegbeschreibungen und Echtzeit-Verkehrsinformationen. Sie können sich Notizen machen und Börsen- und Wetterberichte lesen.</P>\r\n<h3>Berühren Sie Ihre Musik, Filme und vieles mehr</h3>\r\n<p>Mit der revolutionären, in den wunderschönen 3,5-Zoll-Display integrierten Multi-Touch-Technologie können Sie  zuziehen, zoomen, scrollen und streichen.</p>\r\n<h3>Internet in Ihrer Tasche</h3>\r\n<p>Mit dem Safari-Webbrowser sehen Sie Webseiten so, wie sie gesehen werden sollten und vergrößern und verkleinern sie mit einer Berührung.<sup>2</sup>Fügen Sie Web-Clips zu Ihrer Startseite hinzu für den Schnellzugriff auf Ihre bevorzugten Webseiten.</p>\r\n<h3>Zum Set gehören/h3>\r\n<ul>\r\n<li><span></span>der iPod touch</li>\r\n<li><span></span>Ohrhörer</li>\r\n<li><span></span>USB 2.0-Kabel</li>\r\n<li><span></span>Anschluss-Adapter</li>\r\n<li><span></span>Poliertuch</li>\r\n<li><span></span>Basis</li>\r\n<li><span></span>Quick Start Guide</li>\r\n</ul>', '<ul>\r\n<li>Revolutionary Multi-Touch interface</li>\r\n<li>3.5-inch widescreen color display</li>\r\n<li>Wi-Fi (802.11b/g)</li>\r\n<li>8 mm thin</li>\r\n<li>Safari, YouTube, Mail, Stocks, Weather, Notes, iTunes Wi-Fi Music Store, Maps</li>\r\n</ul>', 'iPod-Touch', '', '', '', 'iPod touch', '', NULL),
(8, 4, '<p>Lorem ipsum</p>', '<p>Lorem ipsum</p>', 'lederhulle-belkin-fur-ipod-nano-schwarz-schokolade', '', '', '', 'Lederhülle Belkin für ipod nano - Schwarz/Schokolade', '', NULL),
(9, 4, '<div class="product-overview-full">Mit ihren hochauflösenden Micro-Lautsprechern, die vollen Klang liefern und ihrem ergonomischen, leichten Design sind die SE210 Ohrhörer ideal zum mobilen Extraklasse-Musik hören auf Ihrem iPod oder iPhone. Sie bieten die genaueste Tonwiedergabe, sowohl aus tragbaren als auch aus Home-Stereo-Audio-Quellen - für ultimative präzisen Höhen und kraftvolle Bässe. Darüber hinaus ermöglicht das flexible Design optimalen Tragekomfort durch eine Vielzahl von Tragemöglichkeiten. <br/><br/>Funktionen</strong><br/>\r\n<ul>\r\n<li>Klangisolierendes Design</li>\r\n<li>Hochauflösende Micro-Lautsprecher mit Single Balanced Armature-Treiber</li>\r\n<li>Abnehmbare modulare Kabel, die Sie je nach Aktivität länger oder kürzer einstellen können</li>\r\n<li>Kompatibler Stecker mit Kopfhörer-Anschlüssen für iPod und iPhone</li>\r\n</ul>\r\n<strong>Daten</strong><br/>\r\n<ul>\r\n<li>Lautsprecher-Typ: Hochauflösende Micro-Lautsprecher</li>\r\n<li> Frequenzbereich: 25Hz-18.5kHz </li>\r\n<li>Impedanz (1kHz): 26 Ohm </li>\r\n<li>Empfindlichkeit (1mW): 114 dB SPL/mW</li>\r\n<li> Kabellänge (mit Erweiterung): 18,0 Zoll/45,0 cm (54,0 Zoll/137,1 cm) </li>\r\n</ul>\r\n<strong>Im Set enthalten</strong><br/>\r\n<ul>\r\n<li>Shure SE210 Ohrhörer </li>\r\n<li> Verlängerungskabel (36,0 Zoll/91,4 cm) </li>\r\n<li> Drei Paar Schaumstoff-Hörmuschelhüllen (klein, mittel, groß) </li>\r\n<li> Drei Paar weiche Flex-Hörmuschelhüllen (klein, mittel, groß) </li>\r\n<li> Ein Paar Triple-Flange-Hörmuschelhüllen </li>\r\n<li> Trage-Etui </li>\r\n</ul>\r\nGarantie<br /> Zwei Jahre <br />(Einzelheiten hierzu finden Sie auf <br />www.shure.com/PersonalAudio/CustomerSupport/ProductReturnsAndWarranty/index.htm). <br /><br /> Mfr. Teilenummer: SE210-A-EFS <br /><br />Hinweis: Für Produkte auf dieser Website, die nicht den Markennamen Apple tragen, werden Service und Support ausschließlich von den Herstellern gemäß der den Produkten beiliegenden Nutzungsbedingungen übernommen. Die von Apple angebotene Garantiezeit gilt nicht für Produkte, die kein Apple-Markenzeichen tragen, selbst wenn diese zusammen mit Apple-Produkten verpackt oder verkauft wurden. Bitte wenden Sie sich direkt an den Hersteller für den technischen Support und Kundendienst.</div>', '<p>Evolved from personal monitor technology road-tested by pro musicians and perfected by Shure engineers, the lightweight and stylish SE210 delivers full-range audio that''s free from outside noise.</p>', 'klangisolierte-ohrhorer-shure-se210-weib', '', '', '', 'Shure SE210 Klangisolierte Ohrhörer für iPod und iPhone', '', NULL),
(1, 5, '<p><strong><span style="font-size: small;">Curve mozzafiato.</span></strong></p>\r\n<p>Per te che ami le sensazioni forti, ecco nove fantastici colori. Ma non è finito qui. Accarezza il design sinuoso fatto di vetro e alluminio dell\'iPod nano, e non lo lascerai più.</p>\r\n<p><strong><span style="font-size: small;">Bello e intelligente.</span></strong></p>\r\n<p>La nuova funzione Genius trasforma l\'iPod nano nel tuo DJ personale. Sa creare delle playlist andando a cercare nella libreria musicale le canzoni che stanno bene insieme.</p>\r\n<p><strong><span style="font-size: small;">Fatto per muoversi con te.</span></strong></p>\r\n<p>L\'accelerometro è integrato all\'iPod nano. Scuotilo per dare uno shuffle alla tua musica. Ruotalo di lato per vedere il Cover Flow. E divertiti con i giochi adattati alle tue movenze.</p>', '<p>Nuovo design. Nuove funzioni. Adesso in 8GB e 16GB. iPod nano, forte come non mai.</p>', 'ipod-nano', '', '', '', 'iPod Nano', 'In magazzino', ''),
(2, 5, '<p><span style="font-size: small;"><strong>Sempre attaccato.</strong></span></p>\r\n<p>Metti 500 canzoni in tasca. O nella cintura. O nei pantaloncini. iPod shuffle ti fa avere le canzoni sempre addosso. Adesso in colori più nuovi e brillanti.</p>\r\n<p><span style="font-size: small;"><strong>Ricarica il tuo iPod shuffle.</strong></span></p>\r\n<p>iTunes è il tuo superstore del divertimento. La tua raccolta musicale super organizzata, il tuo juke-box. E puoi ricaricare il tuo iPod shuffle con un click.</p>\r\n<p><span style="font-size: small;"><strong>Musica coloratissima.</strong></span></p>\r\n<p>Complementi dai colori intensi in alluminio anodizzato: questo è il design semplice di iPod shuffle. Adesso in blu, verde rosa, rosso, e argento originale.</p>', '<p>iPod shuffle, il lettore musicale più indossabile del mondo, adesso anche nelle tonalità più vibranti di blu, verde, rosa e rosso.</p>', 'ipod-shuffle', '', '', '', 'iPod shuffle', 'In magazzino', ''),
(5, 5, '<p>MacBook Air è sottile quasi come il tuo indice. Praticamente ogni dettaglio è stato semplificato al massimo. Eppure riesce ad avere uno schermo LED di 13,3 pollici, tastiera completa, e un ampio track-pad multi-touch. Incredibilmente portatile, non soffre dei compromessi tra schermo e tastiera.</p><p>La sottigliezza incredibile di MacBook Air è il risultato di moltissime innovazioni nel campo della riduzione di dimensioni e peso. Un hard drive più sottile, porte I/O strategicamente nascoste, batteria più piatta: tutto è stato ben calibrato pensando sempre alla sottigliezza.</p><p>MacBook Air è stato progettato e realizzato per godere a pieno dell\'universo del wireless. In un mondo in cui la norma 802.11n Wi-Fi è ormai rapida e disponibile, le persone vivono connesse -  acquistano e noleggiano film online,  scaricano programmi, condividono e conservano file nel web. </p>', 'MacBook Air è ultra-piatto, ultra-portatile, e ultra come nient\'altro al mondo.  Ma non si perdono chili e centimetri in una notte. E\' il risultato di una rielaborazione degli standard. Di moltissime innovazioni sul wireless. E di un design rivoluzionario. Con MacBook Air, l\'informatica mobile acquista una nuova dimensione.', 'macbook-air', '', '', '', 'MacBook Air', '', NULL),
(6, 5, 'Tutti i MacBook hanno un hard drive più ampio, fino a 250GB, per conservare le tue raccolte multimediali e i dati importanti.<br /><br />I modelli MacBook a 2,4GHz  ora includono 2GB di memoria standard — ideale per le tue applicazioni preferite.', 'MacBook ti offre il massimo della libertà di movimento grazie alla sua struttura resistente in policarbonato, alle tecnologie integrate wireless, e all\'innovativo MagSafe Power Adapter che si stacca automaticamente se qualcuno accidentalmente inciampa nel cavo.', 'macbook', '', '', '', 'MacBook', '', NULL),
(7, 5, '<h3>Cinque nuove applicazioni sotto mano</h3>\r\n<p>Consulta le tue e-mail in formato rich HTML con foto e allegati PDF, Word, e Excel. Ottieni mappe, indicazioni stradali e sul traffico in tempo reale.  Prendi appunti e consulta la Borsa e le previsioni meteo.</p>\r\n<h3>Tocca la musica, i film e altro ancora</h3>\r\n<p>La rivoluzionaria tecnologia Multi-Touch integrata al bellissimo schermo da 3,5 pollici ti permette di zoomare avanti e indietro, sfogliare e far scorrere le pagine con le dita.</p>\r\n<h3>Internet in tasca</h3>\r\n<p>Con il web browser Safari, consulta i siti web nella loro impaginazione originale e usa lo zoom avanti e indietro con la sola pressione delle dita.<sup>2</sup> Aggiungi Web Clips al tuo schermo per accedere subito ai siti preferiti.</p>\r\n<h3>Nella confezione</h3>\r\n<ul>\r\n<li><span></span>iPod touch</li>\r\n<li><span></span>Auricolari</li>\r\n<li><span></span>Cavo USB 2.0</li>\r\n<li><span></span>Adattatore Dock</li>\r\n<li><span></span>Panno per la pulizia</li>\r\n<li><span></span>Supporto</li>\r\n<li><span></span>Guida installazione rapida</li>\r\n</ul>', '<ul>\r\n<li>Interfaccia Multi-Touch rivoluzionaria</li>\r\n<li>Schermo widescreen a colori da 3,5 pollici</li>\r\n<li>Wi-Fi (802.11b/g)</li>\r\n<li>8 mm di spessore</li>\r\n<li>Safari, YouTube, Mail, Borsa, Meteo, Appunti, iTunes Wi-Fi Music Store, Mappe</li>\r\n</ul>', 'ipod-touch', '', '', '', 'iPod touch', '', NULL),
(8, 5, '<p>Lorem ipsum</p>', '<p>Lorem ipsum</p>', 'custodia-portafoglio-in-pelle-belkin-per-ipod-nano-nero-cioccolato', '', '', '', 'Custodia portafoglio in pelle Belkin per iPod nano - Nero/Cioccolato', '', NULL),
(9, 5, '<div class="product-overview-full">L\'ascolto con la tecnologia dei Micro-Auricolari ad Alta Definizione permette l\'ascolto ideale del tuo iPod o iPhone. E\' quanto ti offre il design leggero, ergonomico ed elegante degli auricolari SE210. Ti garantiscono un rendimento audio ad alto livello di stereo portatili e fissi, per un livello di precisione mai raggiunto prima.  Inoltre, la forma flessibile ti peremtte di scegliere la posizione migliore per indossarli. <br /> <br /> <strong>Caratteristiche</strong> <br /> \r\n<ul>\r\n<li>Design di isolamento del suono </li>\r\n<li> Micro-speaker ad alta definizione con driver singolo ad armatura bilanciata </li>\r\n<li> Cavo staccabile e regolabile in modo da poterlo allungare o accorciare in base alle tue attività </li>\r\n<li> Connettore compatibile con porte auricolari sia su iPod che iPhone </li>\r\n</ul>\r\n<strong>Specifiche tecniche </strong><br /> \r\n<ul>\r\n<li>Tipo speaker: MicroSpeaker ad alta definizione</li>\r\n<li> Gamma di frequenza: 25Hz-18.5kHz </li>\r\n<li> Impedenza (1kHz): 26 Ohms </li>\r\n<li> Sensibilità (1mW): 114 dB SPL/mW </li>\r\n<li> Lunghezza cavo (con prolunga): 18.0 in./45,0 cm (54.0 in./137,1 cm) </li>\r\n</ul>\r\n<strong>Nella confezione</strong><br /> \r\n<ul>\r\n<li>Auricolari Shure SE210 </li>\r\n<li> Cavo prolunga (36.0 in./91,4 cm) </li>\r\n<li> Tre paia di imbuti in spugna (small, medium, large) </li>\r\n<li> Tre paia di imbuti morbidi (small, medium, large) </li>\r\n<li> Un paio di imbuti a tripla aletta </li>\r\n<li> Custodia da viaggio </li>\r\n</ul>\r\nGaranzia<br /> Due anni limitata <br />(Per informazioni, visitare <br />www.shure.com/PersonalAudio/CustomerSupport/ProductReturnsAndWarranty/index.htm.) <br /><br /> Mfr. Parte N.: SE210-A-EFS <br /><br />Nota: I prodotti venduti tramite questo sito web e che non hanno il marchio Apple ricevono assistenza esclusivamente dai loro produttori con i termini e le condizioni contenute nella confezione del prodotto.  La Garanzia Limitata di Apple non si applica ai prodotti che non appartengono al marchio Apple, anche se imballati o venduti con i prodotti Apple . Contatta direttamente il produttore per supporto tecnico e servizio clienti.</div>', '<p>Basati sulla tecnologia all\'avanguardia, testati da musicisti professionisti, e messi a punto da ingegneri Shure, i leggeri ed eleganti SE210 offrono un suono nitido e privo di rumori di fondo.</p>', 'ecouteurs-a-isolation-sonore-shure-se210-blanc', '', '', '', 'auricolari-sound-isolating-shure-se210-per-ipod-e-iphone', '', NULL);

INSERT INTO `PREFIX_specific_price` (`id_product`, `id_shop`, `id_currency`, `id_country`, `id_group`, `price`, `from_quantity`, `reduction`, `reduction_type`, `from`, `to`) VALUES
(1, 0, 0, 0, 0, 0, 1, 0.05, 'percentage', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

INSERT INTO `PREFIX_category` (`id_category`, `id_parent`, `level_depth`, `nleft`, `nright`, `active`, `date_add`, `date_upd`, `position`) VALUES
(2, 1, 1, 2, 3, 1, NOW(), NOW(), 0),(3, 1, 1, 3, 4, 1, NOW(), NOW(), 1),(4, 1, 1, 4, 5, 1, NOW(), NOW(), 2);

INSERT INTO `PREFIX_category_lang` (`id_category`, `id_lang`, `name`, `description`, `link_rewrite`, `meta_title`, `meta_keywords`, `meta_description`) VALUES
(2, 1, 'iPods', 'Now that you can buy movies from the iTunes Store and sync them to your iPod, the whole world is your theater.', 'music-ipods', '', '', ''),
(2, 2, 'iPods', 'Il est temps, pour le meilleur lecteur de musique, de remonter sur scène pour un rappel. Avec le nouvel iPod, le monde est votre scène.', 'musique-ipods', '', '', ''),
(3, 1, 'Accessories', 'Wonderful accessories for your iPod', 'accessories-ipod', '', '', ''),
(3, 2, 'Accessoires', 'Tous les accessoires à la mode pour votre iPod', 'accessoires-ipod', '', '', ''),
(4, 1, 'Laptops', 'The latest Intel processor, a bigger hard drive, plenty of memory, and even more new features all fit inside just one liberating inch. The new Mac laptops have the performance, power, and connectivity of a desktop computer. Without the desk part.', 'laptops', 'Apple laptops', 'Apple laptops MacBook Air', 'Powerful and chic Apple laptops'),
(4, 2, 'Portables', 'Le tout dernier processeur Intel, un disque dur plus spacieux, de la mémoire à profusion et d''autres nouveautés. Le tout, dans à peine 2,59 cm qui vous libèrent de toute entrave. Les nouveaux portables Mac réunissent les performances, la puissance et la connectivité d''un ordinateur de bureau. Sans la partie bureau.', 'portables-apple', 'Portables Apple', 'portables apple macbook air', 'portables apple puissants et design'),
(4, 3, 'Portátiles', 'El último procesador Intel, un disco duro más grande, con profusión de memoria y otras novedades. Todo en sólo 2,59 cm libres de cualquier obstrucción. Las nuevas portátiles Mac cumplir rendimiento, potencia y conectividad de una computadora de escritorio. Sin la parte del escritorio.', 'portatiles-apple', 'Portátiles Apple', 'portátiles apple macbook air', 'portátiles apple poderoso y el diseño'),
(3, 3, 'Accesorios', 'Todos los accesorios de moda para tu iPod', 'ipod-accesorios', '', '', ''),
(2, 3, 'iPods', 'Es hora de que el mejor jugador de la música, al escenario para hacer un bis. Con el nuevo iPod, el mundo es tu escenario.', 'musica-ipods', '', '', ''),
(2, 4, 'iPods', 'Now that you can buy movies from the iTunes Store and sync them to your iPod, the whole world is your theater.', 'musik-iPods', '', '', ''),
(3, 4, 'Zubehör', 'Wonderful accessories for your iPod', 'zubehor-ipod', '', '', ''),
(4, 4, 'Laptops', 'The latest Intel processor, a bigger hard drive, plenty of memory, and even more new features all fit inside just one liberating inch. The new Mac laptops have the performance, power, and connectivity of a desktop computer. Without the desk part.', 'laptops', 'Apple laptops', 'Apple MacBook Air-Laptops', 'Powerful and chic Apple laptops'),
(2, 5, 'iPods', 'Adesso che puoi acquistare film dall\'iTunes Store e inserirli nel tuo iPod, il tuo mondo è un palcoscenico.', 'musica-ipods', '', '', ''),
(3, 5, 'Accessori', 'Fantastici accessori per il tuo iPod', 'accessori-ipod', '', '', ''),
(4, 5, 'Laptop', 'L\'ultimissimo processore Intel, hard drive più ampio, moltissima memoria, e ancora più funzioni tutte inserite in 2,54 centimetri. I nuovi laptop Mac offrono le prestazioni, la potenza e la connettività di un computer da tavolo. Senza bisogno del tavolo.', 'laptop', 'laptop Apple', 'laptot Apple MacBook Air', 'Laptop Apple potenti ed eleganti');

INSERT INTO `PREFIX_category_product` (`id_category`, `id_product`, `position`) VALUES
(1, 1, 0),(1, 2, 1),(1, 6, 2),(1, 7, 3),(2, 1, 0),(2, 2, 1),(2, 7, 2),(3, 8, 0),(3, 9, 1),(4, 5, 0),(4, 6, 1);

INSERT INTO `PREFIX_attribute_group` (`id_attribute_group`, `is_color_group`) VALUES (1, 0),(2, 1),(3, 0);

INSERT INTO `PREFIX_attribute_group_lang` (`id_attribute_group`, `id_lang`, `name`, `public_name`) VALUES
(1, 1, 'Disk space', 'Disk space'),(1, 2, 'Capacité', 'Capacité'),(2, 1, 'Color', 'Color'),(2, 2, 'Couleur', 'Couleur'),(3, 1, 'ICU', 'Processor'),
(3, 2, 'ICU', 'Processeur'),(1, 3, 'Capacidad', 'Capacidad'),(2, 3, 'Color', 'Color'),(3, 3, 'ICU', 'Procesador'),(1, 4, 'Speicherplatz', 'Disk space'),(2, 4, 'Farbe', 'Color'),(3, 4, 'ICU', 'Processor'),(1, 5, 'Spazio disco', 'Spazio disco'),(2, 5, 'Colore', 'Colore'),(3, 5, 'ICU', 'Processore');

INSERT INTO `PREFIX_attribute_impact` (`id_attribute_impact`, `id_product`, `id_attribute`, `weight`, `price`) VALUES
(1, 1, 2, 0, 60.00),(2, 1, 5, 0, 0.00),(3, 1, 16, 0, 50.00),(4, 1, 15, 0, 0.00),(5, 1, 4, 0, 0.00),(6, 1, 19, 0, 0.00),(7, 1, 3, 0, 0.00),(8, 1, 14, 0, 0.00),
(9, 1, 7, 0, 0.00),(10, 1, 20, 0, 0.00),(11, 1, 6, 0, 0.00),(12, 1, 18, 0, 0.00);

INSERT INTO `PREFIX_scene` (`id_scene`, `active`) VALUES (1, 1),(2, 1),(3, 1);

INSERT INTO `PREFIX_scene_category` (`id_scene`, `id_category`) VALUES (1, 2),(2, 2),(3, 4);

INSERT INTO `PREFIX_scene_lang` (`id_scene`, `id_lang`, `name`) VALUES
(1, 1, 'The iPods Nano'),(1, 2, 'Les iPods Nano'),(1, 3, 'El iPod Nano'),(1, 4, 'Die iPods Nano'),(1, 5, 'Gli iPod Nano'),
(2, 1, 'The iPods'),(2, 2, 'Les iPods'),(2, 3, 'El iPod'),(2, 4, 'Die iPods'),(2, 5, 'Gli iPod'),
(3, 1, 'The MacBooks'),(3, 2, 'Les MacBooks'),(3, 3, 'El MacBook'),(3, 4, 'Die MacBooks'),(3, 5, 'I MacBook');

INSERT INTO `PREFIX_scene_products` (`id_scene`, `id_product`, `x_axis`, `y_axis`, `zone_width`, `zone_height`) VALUES
(1, 1, 474, 15, 72, 166),(2, 2, 389, 137, 51, 46),(2, 7, 111, 83, 161, 108),(2, 1, 340, 31, 46, 151),(3, 6, 355, 37, 151, 103),(3, 6, 50, 47, 128, 84),
(3, 5, 198, 47, 137, 92),(1, 1, 394, 14, 73, 168),(1, 1, 318, 14, 69, 168),(1, 1, 244, 14, 66, 169),(1, 1, 180, 13, 59, 168),(1, 1, 6, 12, 30, 175),
(1, 1, 38, 12, 30, 170),(1, 1, 76, 14, 41, 169),(1, 1, 123, 13, 49, 169);

INSERT INTO `PREFIX_attribute` (`id_attribute`, `id_attribute_group`) VALUES (1, 1),(2, 1),(8, 1),(9, 1),(10, 3),(11, 3),(12, 1),(13, 1);
INSERT INTO `PREFIX_attribute` (`id_attribute`, `id_attribute_group`, `color`) VALUES (3, 2, '#D2D6D5'),(4, 2, '#008CB7'),(5, 2, '#F3349E'),(6, 2, '#93D52D'),
(7, 2, '#FD9812'),(15, 1, ''),(16, 1, ''),(17, 1, ''),(18, 2, '#7800F0'),(19, 2, '#F6EF04'),(20, 2, '#F60409'),(14, 2, '#000000');

INSERT INTO `PREFIX_attribute_lang` VALUES (1, 1, '2GB'),(1, 2, '2Go'),(1, 3, '2Go'),(2, 1, '4GB'),(2, 2, '4Go'),(2, 3, '4Go'),(3, 1, 'Metal'),(3, 2, 'Metal'),(3, 3, 'Metal'),
(4, 1, 'Blue'),(4, 2, 'Bleu'),(4, 3, 'Azul'),(5, 1, 'Pink'),(5, 2, 'Rose'),(5, 3, 'Rosa'),(6, 1, 'Green'),(6, 2, 'Vert'),(6, 3, 'Verde'),(7, 1, 'Orange'),(7, 2, 'Orange'),
(7, 3, 'Naranja'),(8, 1, 'Optional 64GB solid-state drive'),(8, 2, 'Disque dur SSD (solid-state drive) de 64 Go '),(8, 3, 'SSD (solid-state drive) 64 Go '),
(9, 1, '80GB Parallel ATA Drive @ 4200 rpm'),(9, 2, 'Disque dur PATA de 80 Go à 4 200 tr/min'),(9, 3, 'Disco duro PATA 80 Go 4 200 tr/min'),(10, 1, '1.60GHz Intel Core 2 Duo'),
(10, 2, 'Intel Core 2 Duo à 1,6 GHz'),(10, 3, 'Intel Core 2 Duo para 1,6 GHz'),(11, 1, '1.80GHz Intel Core 2 Duo'),(11, 2, 'Intel Core 2 Duo à 1,8 GHz'),
(11, 3, 'Intel Core 2 Duo para 1,8 GHz'),(12, 1, '80GB: 20,000 Songs'),(12, 2, '80 Go : 20 000 chansons'),(12, 3, '80 Go : 20 000 canciones'),(13, 1, '160GB: 40,000 Songs'),
(13, 2, '160 Go : 40 000 chansons'),(13, 3, '160 Go : 40 000 canciones'),(14, 2, 'Noir'),(14, 3, 'Negro'),(14, 1, 'Black'),(15, 1, '8Go'),(15, 2, '8Go'),(15, 3, '8Go'),
(16, 1, '16Go'),(16, 2, '16Go'),(16, 3, '16Go'),(17, 1, '32Go'),(17, 2, '32Go'),(17, 3, '32Go'),(1, 4, '2GB'),(2, 4, '4GB'),(3, 4, 'Metallic'),
(4, 4, 'Blau'),(5, 4, 'Pink'),(6, 4, 'Grün'),(7, 4, 'Orange'),(8, 4, 'Optionale 64 GB Solid-State-Drive'),
(9, 4, 'Parallele ATA 80GB Drive @ 4200 rpm'),(10, 4, '1.60GHz Intel Core 2 Duo'),
(11, 4, '1.80GHz Intel Core 2 Duo'),(12, 4, '80GB: 20.000 Songs'),(13, 4, '160GB: 40.000 Songs'),(14, 4, 'Schwarz'),(15, 4, '8Go'),
(16, 4, '16Go'),(17, 4, '32Go'),(1, 5, '2GB'),(2, 5, '4GB'),(3, 5, 'Metallico'),
(4, 5, 'Blu'),(5, 5, 'Rosa'),(6, 5, 'Verde'),(7, 5, 'Arancio'),(8, 5, 'Opzionale solid-state drive 64GB'),
(9, 5, '80GB Parallel ATA Drive @ 4200 rpm'),(10, 5, '1.60GHz Intel Core 2 Duo'),
(11, 5, '1.80GHz Intel Core 2 Duo'),(12, 5, '80GB: 20.000 canzoni'),(13, 5, '160GB: 40,000 canzoni'),(14, 5, 'Nero'),(15, 5, '8Go'),
(16, 5, '16Go'),(17, 5, '32Go');

INSERT INTO `PREFIX_attribute_lang` (`id_attribute`, `id_lang`, `name`) VALUES
(18, 1, 'Purple'),(18, 2, 'Violet'),(18, 3, 'Violeta'),(19, 1, 'Yellow'),(19, 2, 'Jaune'),(19, 3, 'Amarillo'),(20, 1, 'Red'),(20, 2, 'Rouge'),(20, 3, 'Rojo'),(18, 4, 'Violett'),(19, 4, 'Gelb'),(20, 4, 'Rot'),(18, 5, 'Viola'),(19, 5, 'Giallo'),(20, 5, 'Rosso');

INSERT INTO `PREFIX_product_attribute` (`id_product_attribute`, `id_product`, `reference`, `supplier_reference`, `ean13`, `wholesale_price`, `price`, `ecotax`, `quantity`, `weight`, `default_on`, `minimal_quantity`) VALUES
(30, 1, '', '', '', 0.000000, 0.00, 0.00, 50, 0, 0, 1),
(29, 1, '', '', '', 0.000000, 41.806020, 0.00, 50, 0, 0, 1),
(28, 1, '', '', '', 0.000000, 0.00, 0.00, 50, 0, 0, 1),
(27, 1, '', '', '', 0.000000, 41.806020, 0.00, 50, 0, 0, 1),
(26, 1, '', '', '', 0.000000, 0.00, 0.00, 50, 0, 0, 1),
(25, 1, '', '', '', 0.000000, 41.806020, 0.00, 50, 0, 0, 4),
(7, 2, '', '', '', 0.000000, 0.00, 0.00, 10, 0, 0, 1),
(8, 2, '', '', '', 0.000000, 0.00, 0.00, 20, 0, 1, 1),
(9, 2, '', '', '', 0.000000, 0.00, 0.00, 30, 0, 0, 1),
(10, 2, '', '', '', 0.000000, 0.00, 0.00, 40, 0, 0, 1),
(12, 5, '', NULL, '', 0.000000, 751.672241, 0.00, 100, 0, 0, 1),
(13, 5, '', NULL, '', 0.000000, 0.00, 0.00, 99, 0, 1, 1),
(14, 5, '', NULL, '', 0.000000, 225.752508, 0.00, 50, 0, 0, 1),
(15, 5, '', NULL, '', 0.000000, 977.424749, 0.00, 25, 0, 0, 1),
(23, 7, '', NULL, '', 0.000000, 150.501672, 0.00, 70, 0, 0, 1),
(22, 7, '', NULL, '', 0.000000, 75.250836, 0.00, 60, 0, 0, 1),
(19, 7, '', NULL, '', 0.000000, 0.00, 0.00, 50, 0, 1, 1),
(31, 1, '', '', '', 0.000000, 41.806020, 0.00, 50, 0, 1, 1),
(32, 1, '', '', '', 0.000000, 0.00, 0.00, 50, 0, 0, 1),
(33, 1, '', '', '', 0.000000, 41.806020, 0.00, 50, 0, 0, 1),
(34, 1, '', '', '', 0.000000, 0.00, 0.00, 50, 0, 0, 1),
(35, 1, '', '', '', 0.000000, 41.806020, 0.00, 50, 0, 0, 1),
(36, 1, '', '', '', 0.000000, 0.00, 0.00, 50, 0, 0, 1),
(39, 1, '', '', '', 0.000000, 41.806020, 0.00, 50, 0, 0, 1),
(40, 1, '', '', '', 0.000000, 0.00, 0.00, 50, 0, 0, 1),
(41, 1, '', '', '', 0.000000, 41.806020, 0.00, 50, 0, 0, 1),
(42, 1, '', '', '', 0.000000, 0.00, 0.00, 50, 0, 0, 1);

INSERT INTO `PREFIX_product_attribute_image` (`id_product_attribute`, `id_image`) VALUES (30, 44),(29, 44),(28, 45),(27, 45),(26, 38),(25, 38),(7, 46),(8, 47),(9, 49),
(10, 48),(12, 0),(13, 0),(14, 0),(15, 0),(23, 0),(22, 0),(19, 0),(31, 37),(32, 37),(33, 40),(34, 40),(35, 41),(36, 41),(39, 39),(40, 39),(41, 42),(42, 42);

INSERT INTO `PREFIX_product_attribute_combination` (`id_attribute`, `id_product_attribute`) VALUES (3, 9),(3, 12),(3, 13),(3, 14),(3, 15),(3, 29),(3, 30),(4, 7),(4, 25),
(4, 26),(5, 10),(5, 35),(5, 36),(6, 8),(6, 39),(6, 40),(7, 33),(7, 34),(8, 13),(8, 15),(9, 12),(9, 14),(10, 12),(10, 13),(11, 14),(11, 15),(14, 31),(14, 32),(15, 19),
(15, 26),(15, 28),(15, 30),(15, 32),(15, 34),(15, 36),(15, 40),(15, 42),(16, 22),(16, 25),(16, 27),(16, 29),(16, 31),(16, 33),(16, 35),(16, 39),(16, 41),(17, 23),(18, 41),(18, 42),(19, 27),(19, 28);

INSERT INTO `PREFIX_feature` (`id_feature`) VALUES (1), (2), (3), (4), (5);

INSERT INTO `PREFIX_feature_lang` (`id_feature`, `id_lang`, `name`) VALUES
(1, 1, 'Height'), (1, 2, 'Hauteur'),(2, 1, 'Width'), (2, 2, 'Largeur'),(3, 1, 'Depth'), (3, 2, 'Profondeur'),(4, 1, 'Weight'), (4, 2, 'Poids'),(5, 1, 'Headphone'), (5, 2, 'Prise casque'),
(1, 3, 'Alto'),(2, 3, 'Ancho'),(3, 3, 'Profundo'),(4, 3, 'Peso'),(5, 3, 'Toma auriculares'),
(1, 4, 'Höhe'),(2, 4, 'Breite'),(3, 4, 'Tiefe'),(4, 4, 'Gewicht'),(5, 4, 'Kopfhörer'),
(1, 5, 'Altezza'),(2, 5, 'Larghezza'),(3, 5, 'Profondità'),(4, 5, 'Peso'),(5, 5, 'Auricolare');

INSERT INTO `PREFIX_feature_product` (`id_feature`, `id_product`, `id_feature_value`) VALUES
(1, 1, 11),(1, 2, 15),(2, 1, 12),(2, 2, 16),(3, 1, 14),(3, 2, 18),(4, 1, 13),(4, 2, 17),(5, 1, 10),(5, 2, 10),(3, 7, 26),(5, 7, 9),(4, 7, 25),(2, 7, 24),(1, 7, 23);

INSERT INTO `PREFIX_feature_value` (`id_feature_value`, `id_feature`, `custom`) VALUES
(11, 1, 1),(15, 1, 1),(12, 2, 1),(16, 2, 1),(14, 3, 1),(18, 3, 1),(13, 4, 1),(17, 4, 1),(26, 3, 1),(25, 4, 1),(24, 2, 1),(23, 1, 1);

INSERT INTO `PREFIX_feature_value` (`id_feature_value`, `id_feature`, `custom`) VALUES (9, 5, NULL), (10, 5, NULL);

INSERT INTO `PREFIX_feature_value_lang` (`id_feature_value`, `id_lang`, `value`) VALUES
(13, 1, '49.2 g'),(13, 2, '49,2 g'),(13, 3, '49,2 g'),(13, 4, '49.2 g'),(13, 5, '49.2 g'),(12, 2, '52,3 mm'),(12, 1, '2.06 in'),(12, 3, '52.3 mm'),(12, 4, '52.3 mm'),(12, 5, '52.3 mm'),(11, 2, '69,8 mm'),(11, 1, '2.75 in'),(11, 3, '69.8 mm'),(11, 4, '69.8 mm'),(11, 5, '69.8 mm'),
(17, 2, '15,5 g'),(17, 1, '15.5 g'),(17, 3, '15.5 g'),(17, 4, '15.5 g'),(17, 5, '15.5 g'),(16, 2, '41,2 mm'),(16, 1, '1.62 in'),(16, 3, '41.2 mm'),(16, 4, '41.2 mm'),(16, 5, '41.2 mm'),(15, 2, '27,3 mm'),(15, 1, '1.07 in'),(15, 3, '27.3 mm'),(15, 4, '27.3 mm'),(15, 5, '27.3 mm'),(9, 1, 'Jack stereo'),(9, 4, 'Jack stereo'),(9, 5, 'Jack stereo'),
(9, 2, 'Jack stéréo'),(9, 3, 'Jack stereo'),(10, 1, 'Mini-jack stereo'),(10, 2, 'Mini-jack stéréo'),(10, 3, 'Mini-jack stéréo'),(10, 4, 'Mini-jack stéréo'),(10, 5, 'Mini-jack stéréo'),(14, 1, '0.26 in'),(14, 2, '6,5 mm'),(14, 3, '6,5 mm'),(14, 4, '6,5 mm'),(14, 5, '6,5 mm'),(18, 4, '10,5 mm'),(18, 5, '10,5 mm)'),
(18, 1, '0.41 in (clip included)'),(18, 2, '10,5 mm (clip compris)'),(18, 3, '10,5 mm (clip incluyendo)'),(26, 2, '8 mm'),(26, 1, '0.31 in'),(26, 3, '8 mm'),(26, 4, '8 mm'),(26, 5, '8 mm'),(25, 2, '120g'),(25, 3, '120g'),(25, 4, '120g'),(25, 5, '120g'),(25, 1, '120g'),(24, 2, '70 mm'),(24, 1, '2.76 in'),(24, 3, '70 mm'),(24, 4, '70 mm'),(24, 5, '70 mm'),
(23, 2, '110 mm'),(23, 3, '110 mm'),(23, 1, '4.33 in'),(23, 4, '4.33 in'),(23, 5, '4.33 in');

INSERT INTO `PREFIX_image` (`id_image`, `id_product`, `position`, `cover`) VALUES
(40, 1, 4, 0),(39, 1, 3, 0),(38, 1, 2, 0),(37, 1, 1, 1),(48, 2, 3, 0),(47, 2, 2, 0),(49, 2, 4, 0),(46, 2, 1, 1),(15, 5, 1, 1),(16, 5, 2, 0),(17, 5, 3, 0),(18, 6, 4, 0),(19, 6, 5, 0),
(20, 6, 1, 1),(24, 7, 1, 1),(33, 8, 1, 1),(27, 7, 3, 0),(26, 7, 2, 0),(29, 7, 4, 0),(30, 7, 5, 0),(32, 7, 6, 0),(36, 9, 1, 1),(41, 1, 5, 0),(42, 1, 6, 0),(44, 1, 7, 0),(45, 1, 8, 0);

INSERT INTO `PREFIX_image_lang` (`id_image`, `id_lang`, `legend`) VALUES
(40, 2, 'iPod Nano'),(40, 3, 'iPod Nano'),(40, 4, 'iPod Nano'),(40, 5, 'iPod Nano'),(40, 1, 'iPod Nano'),(39, 2, 'iPod Nano'),(39, 3, 'iPod Nano'),(39, 1, 'iPod Nano'),(39, 4, 'iPod Nano'),(39, 5, 'iPod Nano'),
(38, 2, 'iPod Nano'),(38, 3, 'iPod Nano'),(38, 1, 'iPod Nano'),(38, 4, 'iPod Nano'),(38, 5, 'iPod Nano'),
(37, 2, 'iPod Nano'),(37, 3, 'iPod Nano'),(37, 1, 'iPod Nano'),(37, 4, 'iPod Nano'),(37, 5, 'iPod Nano'),(48, 2, 'iPod shuffle'),(48, 3, 'iPod shuffle'),(48, 1, 'iPod shuffle'),(48, 4, 'iPod shuffle'),(48, 5, 'iPod shuffle'),(47, 2, 'iPod shuffle'),(47, 3, 'iPod shuffle'),(47, 4, 'iPod shuffle'),(47, 5, 'iPod shuffle'),
(47, 1, 'iPod shuffle'),(49, 2, 'iPod shuffle'),(49, 3, 'iPod shuffle'),(49, 1, 'iPod shuffle'),(49, 4, 'iPod shuffle'),(49, 5, 'iPod shuffle'),(46, 2, 'iPod shuffle'),(46, 3, 'iPod shuffle'),(46, 1, 'iPod shuffle'),(46, 4, 'iPod shuffle'),(46, 5, 'iPod shuffle'),
(10, 1, 'luxury-cover-for-ipod-video'),(10, 3, 'luxury-cover-for-ipod-video'),(10, 4, 'luxury-cover-for-ipod-video'),(10, 5, 'luxury-cover-for-ipod-video'),(10, 2, 'housse-luxe-pour-ipod-video'),(11, 1, 'cover'),(11, 2, 'housse'),(11, 3, 'cubrir'),(11, 4, 'cover'),(11, 5, 'cover'),
(12, 1, 'myglove-ipod-nano'),(12, 2, 'myglove-ipod-nano'),(12, 3, 'myglove-ipod-nano'),(12, 4, 'myglove-ipod-nano'),(12, 5, 'myglove-ipod-nano'),(13, 1, 'myglove-ipod-nano'),(13, 2, 'myglove-ipod-nano'),(13, 3, 'myglove-ipod-nano'),(13, 4, 'myglove-ipod-nano'),(13, 5, 'myglove-ipod-nano'),
(14, 1, 'myglove-ipod-nano'),(14, 2, 'myglove-ipod-nano'),(14, 3, 'myglove-ipod-nano'),(14, 4, 'myglove-ipod-nano'),(14, 5, 'myglove-ipod-nano'),(15, 1, 'MacBook Air'),(15, 2, 'macbook-air-1'),(15, 3, 'macbook-air-1'),(15, 4, 'macbook-air-1'),(15, 5, 'macbook-air-1'),(16, 1, 'MacBook Air'),(16, 2, 'macbook-air-2'),(16, 3, 'macbook-air-2'),(16, 4, 'macbook-air-2'),(16, 5, 'macbook-air-2'),(17, 1, 'MacBook Air'),(17, 2, 'macbook-air-3'),(17, 3, 'macbook-air-3'),(17, 4, 'macbook-air-3'),(17, 5, 'macbook-air-3'),(18, 1, 'MacBook Air'),(18, 2, 'macbook-air-4'),
(18, 3, 'macbook-air-4'),(18, 4, 'macbook-air-4'),(18, 5, 'macbook-air-4'),(19, 1, 'MacBook Air'),(19, 2, 'macbook-air-5'),(19, 3, 'macbook-air-5'),(19, 4, 'macbook-air-5'),(19, 5, 'macbook-air-5'),(20, 1, ' MacBook Air SuperDrive'),(20, 2, 'superdrive-pour-macbook-air-1'),
(20, 3, 'superdrive-pour-macbook-air-1'),(20, 4, 'superdrive-pour-macbook-air-1'),(20, 5, 'superdrive-pour-macbook-air-1'),(24, 2, 'iPod touch'),(24, 1, 'iPod touch'),(24, 3, 'iPod touch'),(24, 4, 'iPod touch'),(24, 5, 'iPod touch'),(33, 1, 'housse-portefeuille-en-cuir'),(33, 3, 'housse-portefeuille-en-cuir'),(33, 4, 'housse-portefeuille-en-cuir'),(33, 5, 'housse-portefeuille-en-cuir'),
(26, 1, 'iPod touch'),(26, 2, 'iPod touch'),(26, 3, 'iPod touch'),(26, 4, 'iPod touch'),(26, 5, 'iPod touch'),(27, 1, 'iPod touch'),(27, 2, 'iPod touch'),(27, 3, 'iPod touch'),(27, 4, 'iPod touch'),(27, 5, 'iPod touch'),(29, 1, 'iPod touch'),(29, 2, 'iPod touch'),(29, 3, 'iPod touch'),(29, 4, 'iPod touch'),(29, 5, 'iPod touch'),(30, 1, 'iPod touch'),(30, 2, 'iPod touch'),(30, 3, 'iPod touch'),(30, 4, 'iPod touch'),(30, 5, 'iPod touch'),(32, 1, 'iPod touch'),(32, 2, 'iPod touch'),(32, 3, 'iPod touch'),(32, 4, 'iPod touch'),(32, 5, 'iPod touch'),
(33, 2, 'housse-portefeuille-en-cuir-ipod-nano'),(36, 2, 'Écouteurs à isolation sonore Shure SE210'),(36, 3, 'Auriculares aislantes del sonido Shure SE210'),
(36, 1, 'Shure SE210 Sound-Isolating Earphones for iPod and iPhone'),(36, 4, 'Shure SE210 Sound-Isolating Earphones for iPod and iPhone'),(36, 5, 'Shure SE210 Sound-Isolating Earphones for iPod and iPhone'),(41, 1, 'iPod Nano'),(41, 2, 'iPod Nano'),(41, 3, 'iPod Nano'),(41, 4, 'iPod Nano'),(41, 5, 'iPod Nano'),(42, 1, 'iPod Nano'),(42, 2, 'iPod Nano'),
(42, 3, 'iPod Nano'),(42, 4, 'iPod Nano'),(42, 5, 'iPod Nano'),(44, 1, 'iPod Nano'),(44, 2, 'iPod Nano'),(44, 3, 'iPod Nano'),(44, 4, 'iPod Nano'),(44, 5, 'iPod Nano'),(45, 1, 'iPod Nano'),(45, 2, 'iPod Nano'),(45, 3, 'iPod Nano'),(45, 4, 'iPod Nano'),(45, 5, 'iPod Nano');

INSERT INTO `PREFIX_tag` (`id_tag`, `id_lang`, `name`) VALUES (5, 1, 'apple'),(6, 2, 'ipod'),(7, 2, 'nano'),(8, 2, 'apple'),(18, 2, 'shuffle'),
(19, 2, 'macbook'),(20, 2, 'macbookair'),(21, 2, 'air'),(22, 1, 'superdrive'),(27, 2, 'marche'),(26, 2, 'casque'),(25, 2, 'écouteurs'),
(24, 2, 'ipod touch tacticle'),(23, 1, 'Ipod touch'),(28, 1, 'ipod'),(29, 1, 'nano'),(30, 3, 'ipod'),(31, 3, 'nano'),(32, 3, 'apple'),(33, 1, 'shuffle'),
(34, 3, 'shuffle'),(35, 2, 'superdrive'),(36, 3, 'superdrive'),(37, 3, 'Ipod touch');

INSERT INTO `PREFIX_product_tag` (`id_product`, `id_tag`) VALUES (1, 5),(1, 6),(1, 7),(1, 8),(1, 28),(1, 29),(1, 30),(1, 31),(1, 32),(2, 6),(2, 18),(2, 28),
(2, 30),(2, 33),(2, 34),(5, 8),(5, 19),(5, 20),(5, 21),(6, 5),(6, 8),(6, 22),(6, 32),(6, 35),(6, 36),(7, 23),(7, 24),(7, 37),(9, 25),(9, 26),(9, 27);

INSERT INTO `PREFIX_alias` (`alias`, `search`, `active`, `id_alias`) VALUES ('piod', 'ipod', 1, 4),('ipdo', 'ipod', 1, 3);
INSERT INTO `PREFIX_order_message` (`id_order_message`, `date_add`) VALUES (1, NOW());
INSERT INTO `PREFIX_order_message_lang` (`id_order_message`, `id_lang`, `name`, `message`) VALUES
(1, 1, 'Delay', 'Hi,

Unfortunately, an item on your order is currently out of stock. This may cause a slight delay in delivery.
Please accept our apologies and rest assured that we are working hard to rectify this.

Best regards,');

INSERT INTO `PREFIX_order_message_lang` (`id_order_message`, `id_lang`, `name`, `message`) VALUES
(1, 2, 'Délai', 'Bonjour,

Un des éléments de votre commande est actuellement en réapprovisionnement, ce qui peut légèrement retarder son envoi.

Merci de votre compréhension.

Cordialement,');

INSERT INTO `PREFIX_order_message_lang` (`id_order_message`, `id_lang`, `name`, `message`) VALUES
(1, 3, 'Plazo', 'Hola,

Uno de los elementos de su solicitud se encuentra actualmente la reposición, el cual poco puede retrasar el envío.

Gracias por su comprensión.

Saludos cordiales,');

INSERT INTO `PREFIX_order_message_lang` (`id_order_message`, `id_lang`, `name`, `message`) VALUES
(1, 4, 'Frist', 'Hi,

Leider ist einer der Artikel aus Ihrer Bestellung momentan nicht auf Lager. Dies kann zu einer leichten Lieferverzögerung führen. Wir entschuldigen uns hierfür und bemühen uns schnellstens um Abhilfe.

Mit freundlichen Grüßen,');

INSERT INTO `PREFIX_order_message_lang` (`id_order_message`, `id_lang`, `name`, `message`) VALUES
(1, 5, 'Ritardo', 'Salve,

purtroppo un articolo che hai ordinato non è al momento in magazzino. Questo potrebbe provocare un leggero ritardo nella consegna.
Ti preghiamo di scusarci; ci stiamo organizzando per ovviare a questo inconveniente.

Cordialmente,');

/* Block CMS module*/

INSERT INTO `PREFIX_cms_block` (`id_cms_block`, `id_cms_category`, `name`, `location`, `position`) VALUES(1, 1, '', 0, 0);
INSERT INTO `PREFIX_cms_block_page` (`id_cms_block_page`, `id_cms_block`, `id_cms`, `is_category`) VALUES(1, 1, 1, 0), (2, 1, 2, 0), (3, 1, 3, 0), (4, 1, 4, 0), (5, 1, 5, 0);
INSERT INTO `PREFIX_cms_block_lang` (`id_cms_block`, `id_lang`, `name`) VALUES (1, 1, 'Information'),(1, 2, 'Informations'),(1, 3, 'Informaciónes'),(1, 4, 'Information'),(1, 5, 'Informazioni');

/* Currency/Country module */
INSERT INTO `PREFIX_module_currency` (`id_module`, `id_currency`) VALUES (3, 1),(3, 2),(3, 3),(4, 1),(4, 2),(4, 3),(6, 1),(6, 2),(6, 3);
INSERT INTO `PREFIX_module_group` (`id_module`, `id_group`) VALUES (3, 1),(4, 1),(6, 1);

INSERT INTO `PREFIX_module_country` (`id_module`, `id_country`) VALUES (3, 1),(3, 2),(3, 3),(3, 4),(3, 5),(3, 6),(3, 7),(3, 8),
(3, 9),(3, 10),(3, 11),(3, 12),(3, 13),(3, 14),(3, 15),(3, 16),(3, 17),(3, 18),(3, 19),(3, 20),(3, 21),(3, 22),(3, 23),(3, 24),
(3, 25),(3, 26),(3, 27),(3, 28),(3, 29),(3, 30),(3, 31),(3, 32),(3, 33),(3, 34),(4, 1),(4, 2),(4, 3),(4, 4),(4, 5),(4, 6),(4, 7),
(4, 8),(4, 9),(4, 10),(4, 11),(4, 12),(4, 13),(4, 14),(4, 15),(4, 16),(4, 17),(4, 18),(4, 19),(4, 20),(4, 21),(4, 22),(4, 23),
(4, 24),(4, 25),(4, 26),(4, 27),(4, 28),(4, 29),(4, 30),(4, 31),(4, 32),(4, 33),(4, 34),(6, 1),(6, 2),(6, 3),(6, 4),(6, 5),(6, 6),
(6, 7),(6, 8),(6, 9),(6, 10),(6, 11),(6, 12),(6, 13),(6, 14),(6, 15),(6, 16),(6, 17),(6, 18),(6, 19),(6, 20),(6, 21),(6, 22),(6, 23),
(6, 24),(6, 25),(6, 26),(6, 27),(6, 28),(6, 29),(6, 30),(6, 31),(6, 32),(6, 33),(6, 34);

INSERT INTO `PREFIX_search_index` (`id_product`, `id_word`, `weight`) VALUES (1, 1, 10),(1, 2, 10),(1, 3, 2),(1, 4, 1),(1, 5, 1),(1, 6, 1),
(1, 7, 1),(1, 8, 1),(1, 9, 1),(1, 10, 1),(1, 11, 1),(1, 12, 1),(1, 13, 1),(1, 14, 1),(1, 15, 1),(1, 16, 2),(1, 17, 1),(1, 18, 1),(1, 19, 1),
(1, 20, 1),(1, 21, 1),(1, 22, 1),(1, 23, 1),(1, 24, 1),(1, 25, 1),(1, 26, 1),(1, 27, 1),(1, 28, 1),(1, 29, 1),(1, 30, 1),(1, 31, 2),
(1, 32, 1),(1, 33, 1),(1, 34, 1),(1, 35, 1),(1, 36, 1),(1, 37, 1),(1, 38, 5),(1, 39, 1),(1, 40, 1),(1, 41, 1),(1, 42, 1),(1, 43, 1),
(1, 44, 1),(1, 45, 1),(1, 46, 1),(1, 47, 1),(1, 48, 1),(1, 49, 1),(1, 50, 1),(1, 51, 2),(1, 52, 2),(1, 53, 1),(1, 54, 1),(1, 55, 1),
(1, 56, 1),(1, 57, 1),(1, 58, 1),(1, 59, 1),(1, 60, 1),(1, 61, 1),(1, 62, 1),(1, 63, 1),(1, 64, 1),(1, 65, 1),(1, 66, 1),(1, 67, 3),
(1, 68, 3),(1, 69, 3),(1, 70, 4),(1, 71, 16),(1, 72, 4),(1, 73, 4),(1, 74, 4),(1, 75, 4),(1, 76, 4),(1, 77, 4),(1, 78, 4),(1, 79, 2),
(1, 80, 2),(1, 81, 2),(1, 82, 12),(1, 83, 12),(1, 84, 1),(1, 85, 2),(1, 86, 1),(1, 87, 2),(1, 88, 1),(1, 89, 1),(1, 90, 2),(1, 91, 1),
(1, 92, 1),(1, 93, 1),(1, 94, 1),(1, 95, 4),(1, 96, 1),(1, 97, 1),(1, 98, 1),(1, 99, 1),(1, 100, 1),(1, 101, 1),(1, 102, 1),
(1, 103, 1),(1, 104, 1),(1, 105, 1),(1, 106, 1),(1, 107, 1),(1, 108, 1),(1, 109, 2),(1, 110, 1),(1, 111, 1),(1, 112, 1),(1, 113, 1),
(1, 114, 1),(1, 115, 2),(1, 116, 2),(1, 117, 1),(1, 118, 3),(1, 119, 1),(1, 120, 1),(1, 121, 1),(1, 122, 1),(1, 123, 1),(1, 124, 1),
(1, 125, 1),(1, 126, 1),(1, 127, 1),(1, 128, 1),(1, 129, 1),(1, 130, 1),(1, 131, 1),(1, 132, 1),(1, 133, 1),(1, 134, 1),(1, 135, 1),
(1, 136, 1),(1, 137, 1),(1, 138, 1),(1, 139, 1),(1, 140, 1),(1, 141, 1),(1, 142, 1),(1, 143, 1),(1, 144, 1),(1, 145, 3),(1, 146, 7),
(1, 147, 3),(1, 148, 4),(1, 149, 16),(1, 150, 4),(1, 151, 4),(1, 152, 4),(1, 153, 4),(1, 154, 4),(1, 155, 4),(1, 156, 4),(1, 157, 2),
(1, 158, 2),(1, 159, 2),(1, 160, 9),(1, 161, 8),(1, 162, 1),(1, 163, 2),(1, 164, 1),(1, 165, 1),(1, 166, 1),(1, 167, 1),(1, 168, 1),
(1, 169, 1),(1, 170, 2),(1, 171, 1),(1, 172, 1),(1, 173, 4),(1, 174, 1),(1, 175, 1),(1, 176, 1),(1, 177, 1),(1, 178, 1),(1, 179, 1),
(1, 180, 1),(1, 181, 1),(1, 182, 1),(1, 183, 1),(1, 184, 1),(1, 185, 1),(1, 186, 1),(1, 187, 1),(1, 188, 1),(1, 189, 1),(1, 190, 1),
(1, 191, 1),(1, 192, 1),(1, 193, 1),(1, 194, 1),(1, 195, 1),(1, 196, 1),(1, 197, 1),(1, 198, 1),(1, 199, 1),(1, 200, 1),(1, 201, 1),
(1, 202, 1),(1, 203, 1),(1, 204, 1),(1, 205, 1),(1, 206, 1),(1, 207, 1),(1, 208, 1),(1, 209, 1),(1, 210, 1),(1, 211, 1),(1, 212, 1),
(1, 213, 1),(1, 214, 1),(1, 215, 1),(1, 216, 1),(1, 217, 1),(1, 218, 1),(1, 219, 1),(1, 220, 1),(1, 221, 1),(1, 222, 3),(1, 223, 3),
(1, 224, 3),(1, 225, 4),(1, 226, 16),(1, 227, 4),(1, 228, 4),(1, 229, 4),(1, 230, 4),(1, 231, 4),(1, 232, 4),(1, 233, 4),(1, 234, 2),
(1, 235, 2),(2, 1, 11),(2, 56, 10),(2, 236, 1),(2, 237, 1),(2, 238, 1),(2, 239, 1),(2, 57, 2),(2, 240, 1),(2, 241, 1),(2, 242, 2),
(2, 243, 1),(2, 244, 2),(2, 245, 2),(2, 246, 2),(2, 247, 1),(2, 248, 1),(2, 249, 1),(2, 45, 1),(2, 38, 7),(2, 250, 1),(2, 251, 1),(2, 252, 1),
(2, 253, 1),(2, 254, 1),(2, 255, 1),(2, 256, 1),(2, 257, 1),(2, 19, 1),(2, 258, 1),(2, 259, 1),(2, 260, 1),(2, 261, 1),(2, 262, 1),(2, 263, 1),
(2, 264, 1),(2, 265, 1),(2, 266, 1),(2, 267, 1),(2, 268, 1),(2, 269, 1),(2, 270, 1),(2, 271, 1),(2, 272, 1),(2, 273, 1),(2, 274, 1),(2, 3, 1),
(2, 275, 1),(2, 276, 1),(2, 277, 1),(2, 67, 3),(2, 68, 3),(2, 69, 3),(2, 73, 2),(2, 77, 2),(2, 70, 2),(2, 76, 2),(2, 278, 2),(2, 279, 2),
(2, 80, 2),(2, 81, 2),(2, 82, 15),(2, 280, 14),(2, 281, 1),(2, 282, 1),(2, 90, 2),(2, 283, 1),(2, 284, 1),(2, 285, 1),(2, 286, 1),(2, 287, 2),
(2, 288, 2),(2, 154, 3),(2, 289, 2),(2, 290, 1),(2, 291, 1),(2, 292, 1),(2, 0, 1),(2, 126, 2),(2, 294, 1),(2, 118, 7),(2, 295, 1),(2, 296, 1),
(2, 297, 1),(2, 298, 1),(2, 299, 1),(2, 300, 1),(2, 301, 1),(2, 302, 1),(2, 95, 1),(2, 136, 2),(2, 303, 1),(2, 88, 2),(2, 304, 1),(2, 100, 2),
(2, 101, 2),(2, 305, 1),(2, 306, 1),(2, 307, 1),(2, 308, 1),(2, 309, 1),(2, 310, 1),(2, 311, 1),(2, 312, 1),(2, 313, 1),(2, 314, 1),(2, 315, 1),
(2, 316, 1),(2, 317, 1),(2, 109, 1),(2, 318, 1),(2, 319, 1),(2, 320, 1),(2, 321, 1),(2, 322, 1),(2, 323, 1),(2, 124, 1),(2, 324, 1),(2, 325, 1),
(2, 85, 1),(2, 326, 1),(2, 327, 1),(2, 328, 1),(2, 329, 1),(2, 330, 1),(2, 331, 1),(2, 332, 1),(2, 333, 1),(2, 334, 1),(2, 145, 3),(2, 146, 3),
(2, 147, 3),(2, 151, 2),(2, 155, 2),(2, 148, 2),(2, 335, 2),(2, 336, 2),(2, 158, 2),(2, 159, 2),(2, 160, 11),(2, 337, 10),(2, 338, 1),(2, 339, 1),
(2, 340, 1),(2, 341, 1),(2, 166, 2),(2, 342, 2),(2, 343, 2),(2, 231, 3),(2, 344, 2),(2, 345, 1),(2, 346, 1),(2, 347, 1),(2, 348, 1),(2, 202, 2),
(2, 349, 1),(2, 350, 1),(2, 351, 1),(2, 352, 1),(2, 353, 1),(2, 354, 1),(2, 355, 1),(2, 356, 1),(2, 357, 1),(2, 213, 3),(2, 358, 1),(2, 359, 1),
(2, 179, 2),(2, 180, 2),(2, 360, 1),(2, 361, 1),(2, 362, 1),(2, 363, 1),(2, 364, 1),(2, 365, 1),(2, 366, 1),(2, 367, 1),(2, 368, 1),(2, 369, 1),
(2, 370, 1),(2, 371, 1),(2, 372, 1),(2, 373, 1),(2, 374, 1),(2, 375, 1),(2, 376, 1),(2, 377, 1),(2, 378, 1),(2, 163, 1),(2, 379, 1),(2, 184, 1),
(2, 380, 1),(2, 381, 1),(2, 204, 1),(2, 382, 1),(2, 383, 1),(2, 384, 1),(2, 222, 3),(2, 223, 3),(2, 224, 3),(2, 228, 2),(2, 232, 2),(2, 225, 2),
(2, 385, 2),(2, 386, 2),(2, 234, 2),(2, 235, 2),(5, 387, 10),(5, 388, 1),(5, 389, 1),(5, 390, 1),(5, 391, 1),(5, 392, 1),(5, 393, 1),(5, 394, 1),
(5, 395, 1),(5, 396, 1),(5, 397, 1),(5, 398, 2),(5, 399, 1),(5, 400, 1),(5, 401, 1),(5, 402, 2),(5, 403, 2),(5, 404, 1),(5, 3, 1),(5, 51, 2),
(5, 405, 1),(5, 406, 1),(5, 407, 1),(5, 408, 1),(5, 409, 1),(5, 410, 1),(5, 411, 1),(5, 38, 1),(5, 412, 1),(5, 413, 1),(5, 414, 1),(5, 415, 1),
(5, 416, 1),(5, 47, 1),(5, 417, 1),(5, 418, 1),(5, 419, 2),(5, 420, 1),(5, 421, 1),(5, 422, 1),(5, 423, 1),(5, 424, 1),(5, 425, 1),(5, 426, 1),
(5, 427, 1),(5, 428, 1),(5, 429, 1),(5, 430, 1),(5, 431, 1),(5, 432, 1),(5, 433, 1),(5, 434, 1),(5, 435, 1),(5, 436, 1),(5, 437, 1),(5, 438, 2),
(5, 439, 1),(5, 440, 1),(5, 441, 1),(5, 442, 1),(5, 443, 1),(5, 444, 1),(5, 445, 9),(5, 446, 1),(5, 447, 1),(5, 448, 1),(5, 449, 1),(5, 450, 1),
(5, 451, 1),(5, 452, 1),(5, 453, 1),(5, 454, 1),(5, 65, 1),(5, 455, 1),(5, 456, 1),(5, 457, 1),(5, 458, 1),(5, 237, 2),(5, 459, 1),(5, 460, 1),
(5, 461, 1),(5, 462, 1),(5, 463, 1),(5, 464, 1),(5, 465, 1),(5, 466, 1),(5, 467, 1),(5, 468, 1),(5, 469, 1),(5, 470, 1),(5, 471, 1),(5, 472, 1),
(5, 473, 1),(5, 474, 1),(5, 475, 1),(5, 476, 1),(5, 477, 3),(5, 68, 3),(5, 69, 3),(5, 70, 8),(5, 478, 4),(5, 479, 4),(5, 480, 4),(5, 481, 4),
(5, 482, 8),(5, 483, 8),(5, 484, 4),(5, 485, 4),(5, 486, 4),(5, 487, 4),(5, 488, 14),(5, 489, 3),(5, 490, 1),(5, 283, 2),(5, 491, 1),(5, 103, 2),
(5, 492, 1),(5, 493, 1),(5, 494, 1),(5, 495, 1),(5, 496, 1),(5, 497, 1),(5, 498, 1),(5, 499, 2),(5, 500, 3),(5, 501, 1),(5, 502, 1),(5, 503, 1),
(5, 504, 2),(5, 505, 2),(5, 506, 1),(5, 124, 2),(5, 85, 1),(5, 131, 2),(5, 507, 1),(5, 508, 1),(5, 509, 1),(5, 510, 1),(5, 511, 1),(5, 114, 1),
(5, 512, 1),(5, 513, 1),(5, 514, 1),(5, 118, 1),(5, 515, 1),(5, 516, 1),(5, 517, 1),(5, 518, 1),(5, 519, 1),(5, 520, 1),(5, 521, 1),(5, 522, 1),
(5, 523, 5),(5, 524, 1),(5, 525, 1),(5, 526, 1),(5, 527, 2),(5, 528, 1),(5, 529, 1),(5, 530, 1),(5, 531, 1),(5, 532, 1),(5, 109, 1),(5, 533, 1),
(5, 534, 1),(5, 535, 1),(5, 536, 1),(5, 537, 1),(5, 538, 1),(5, 539, 2),(5, 540, 1),(5, 541, 1),(5, 542, 1),(5, 543, 1),(5, 544, 1),(5, 545, 1),
(5, 546, 9),(5, 90, 2),(5, 547, 1),(5, 548, 1),(5, 549, 1),(5, 550, 1),(5, 551, 1),(5, 552, 1),(5, 553, 1),(5, 554, 1),(5, 555, 1),(5, 556, 1),
(5, 557, 1),(5, 558, 1),(5, 559, 1),(5, 95, 2),(5, 560, 1),(5, 561, 1),(5, 562, 2),(5, 563, 1),(5, 564, 1),(5, 565, 1),(5, 566, 1),(5, 88, 1),
(5, 567, 1),(5, 568, 1),(5, 569, 1),(5, 570, 1),(5, 571, 1),(5, 572, 1),(5, 573, 1),(5, 574, 1),(5, 575, 1),(5, 576, 1),(5, 577, 1),(5, 578, 1),
(5, 579, 1),(5, 580, 1),(5, 581, 1),(5, 582, 1),(5, 583, 3),(5, 146, 7),(5, 147, 3),(5, 584, 4),(5, 148, 8),(5, 585, 4),(5, 586, 8),(5, 587, 8),
(5, 588, 4),(5, 589, 4),(5, 590, 11),(5, 591, 3),(5, 592, 1),(5, 340, 2),(5, 593, 1),(5, 181, 2),(5, 594, 1),(5, 595, 1),(5, 596, 1),(5, 597, 1),
(5, 598, 1),(5, 371, 1),(5, 599, 1),(5, 600, 1),(5, 601, 3),(5, 162, 1),(5, 602, 1),(5, 603, 1),(5, 604, 1),(5, 605, 1),(5, 606, 1),(5, 607, 1),
(5, 163, 1),(5, 608, 1),(5, 609, 1),(5, 610, 1),(5, 611, 1),(5, 190, 1),(5, 612, 1),(5, 613, 1),(5, 614, 3),(5, 354, 1),(5, 615, 1),(5, 616, 1),
(5, 617, 1),(5, 618, 1),(5, 619, 1),(5, 620, 1),(5, 621, 2),(5, 622, 1),(5, 623, 1),(5, 624, 2),(5, 625, 1),(5, 626, 1),(5, 627, 1),(5, 628, 1),
(5, 629, 1),(5, 630, 1),(5, 631, 1),(5, 632, 1),(5, 633, 1),(5, 634, 1),(5, 635, 1),(5, 636, 1),(5, 637, 1),(5, 638, 1),(5, 639, 1),(5, 640, 1),
(5, 641, 1),(5, 642, 1),(5, 643, 1),(5, 644, 1),(5, 645, 5),(5, 646, 5),(5, 348, 1),(5, 647, 1),(5, 648, 1),(5, 649, 1),(5, 650, 1),(5, 651, 1),
(5, 652, 1),(5, 653, 1),(5, 654, 1),(5, 655, 1),(5, 173, 11),(5, 656, 1),(5, 657, 1),(5, 658, 1),(5, 659, 1),(5, 660, 1),(5, 661, 1),(5, 662, 2),
(5, 663, 1),(5, 664, 1),(5, 665, 1),(5, 666, 1),(5, 667, 1),(5, 668, 1),(5, 669, 1),(5, 670, 1),(5, 671, 1),(5, 672, 1),(5, 673, 1),(5, 674, 1),
(5, 675, 1),(5, 676, 1),(5, 677, 1),(5, 678, 1),(5, 679, 1),(5, 680, 1),(5, 681, 1),(5, 682, 3),(5, 223, 3),(5, 224, 3),(5, 225, 8),(5, 683, 4),
(5, 684, 8),(5, 685, 8),(5, 686, 4),(5, 687, 4),(6, 387, 9),(6, 688, 1),(6, 689, 1),(6, 690, 1),(6, 691, 1),(6, 692, 1),(6, 693, 1),(6, 694, 1),
(6, 695, 1),(6, 402, 1),(6, 696, 1),(6, 697, 1),(6, 698, 1),(6, 699, 1),(6, 700, 1),(6, 47, 1),(6, 701, 1),(6, 702, 1),(6, 703, 1),(6, 704, 1),
(6, 705, 1),(6, 706, 1),(6, 415, 1),(6, 707, 1),(6, 444, 1),(6, 708, 1),(6, 709, 1),(6, 710, 1),(6, 711, 1),(6, 712, 1),(6, 713, 1),(6, 714, 1),
(6, 715, 1),(6, 716, 1),(6, 717, 1),(6, 718, 1),(6, 719, 1),(6, 409, 1),(6, 720, 1),(6, 721, 1),(6, 242, 1),(6, 38, 1),(6, 722, 1),(6, 723, 1),
(6, 724, 1),(6, 477, 3),(6, 68, 7),(6, 69, 3),(6, 725, 4),(6, 488, 9),(6, 109, 1),(6, 726, 1),(6, 727, 1),(6, 728, 1),(6, 729, 1),(6, 327, 1),(6, 730, 1),
(6, 731, 1),(6, 732, 1),(6, 505, 1),(6, 733, 1),(6, 734, 1),(6, 735, 1),(6, 736, 1),(6, 737, 1),(6, 738, 1),(6, 739, 1),(6, 740, 1),(6, 510, 1),(6, 741, 1),
(6, 124, 1),(6, 553, 1),(6, 132, 1),(6, 523, 1),(6, 546, 1),(6, 90, 1),(6, 742, 1),(6, 500, 1),(6, 743, 1),(6, 744, 1),(6, 95, 2),(6, 580, 1),
(6, 745, 1),(6, 746, 1),(6, 747, 1),(6, 748, 1),(6, 749, 1),(6, 750, 1),(6, 751, 1),(6, 88, 1),(6, 752, 1),(6, 753, 1),(6, 754, 1),(6, 755, 1),
(6, 756, 1),(6, 757, 1),(6, 758, 1),(6, 583, 3),(6, 146, 3),(6, 147, 3),(6, 590, 9),(6, 759, 1),(6, 637, 1),(6, 760, 1),(6, 221, 1),(6, 761, 1),
(6, 356, 1),(6, 762, 1),(6, 763, 1),(6, 764, 1),(6, 606, 2),(6, 765, 1),(6, 766, 1),(6, 767, 1),(6, 768, 1),(6, 769, 1),(6, 770, 1),(6, 771, 1),
(6, 772, 1),(6, 653, 1),(6, 208, 1),(6, 209, 1),(6, 645, 1),(6, 646, 1),(6, 773, 1),(6, 774, 1),(6, 348, 1),(6, 173, 2),(6, 679, 1),(6, 775, 1),
(6, 776, 1),(6, 777, 1),(6, 778, 1),(6, 779, 1),(6, 780, 1),(6, 781, 1),(6, 782, 1),(6, 783, 1),(6, 784, 1),(6, 785, 1),(6, 786, 1),(6, 165, 1),
(6, 787, 1),(6, 682, 3),(6, 223, 3),(6, 224, 3),(7, 1, 11),(7, 788, 12),(7, 789, 2),(7, 427, 2),(7, 790, 1),(7, 791, 2),(7, 422, 1),(7, 792, 1),
(7, 793, 2),(7, 461, 2),(7, 794, 1),(7, 411, 1),(7, 795, 1),(7, 796, 1),(7, 797, 1),(7, 798, 1),(7, 799, 1),(7, 800, 1),(7, 259, 1),(7, 57, 1),
(7, 801, 1),(7, 802, 1),(7, 803, 1),(7, 804, 1),(7, 723, 1),(7, 60, 1),(7, 805, 1),(7, 806, 1),(7, 807, 1),(7, 51, 4),(7, 808, 1),(7, 809, 1),
(7, 810, 1),(7, 811, 1),(7, 812, 1),(7, 813, 1),(7, 814, 1),(7, 815, 1),(7, 816, 1),(7, 817, 1),(7, 818, 1),(7, 456, 1),(7, 819, 1),(7, 820, 1),
(7, 821, 1),(7, 822, 1),(7, 823, 1),(7, 38, 4),(7, 824, 1),(7, 825, 1),(7, 242, 1),(7, 826, 1),(7, 827, 1),(7, 37, 1),(7, 828, 1),(7, 829, 1),
(7, 830, 1),(7, 831, 1),(7, 832, 1),(7, 833, 1),(7, 834, 1),(7, 835, 1),(7, 836, 1),(7, 837, 1),(7, 838, 1),(7, 839, 1),(7, 840, 1),(7, 841, 1),
(7, 65, 1),(7, 842, 1),(7, 843, 1),(7, 844, 1),(7, 241, 1),(7, 845, 1),(7, 434, 1),(7, 846, 2),(7, 847, 1),(7, 722, 1),(7, 848, 1),(7, 849, 1),
(7, 850, 1),(7, 851, 1),(7, 852, 1),(7, 700, 1),(7, 853, 1),(7, 854, 1),(7, 855, 1),(7, 856, 1),(7, 857, 1),(7, 67, 3),(7, 858, 2),(7, 71, 2),
(7, 859, 2),(7, 81, 2),(7, 860, 2),(7, 861, 2),(7, 862, 2),(7, 82, 11),(7, 863, 11),(7, 864, 1),(7, 531, 2),(7, 865, 2),(7, 525, 1),(7, 866, 1),
(7, 867, 1),(7, 868, 1),(7, 869, 1),(7, 870, 1),(7, 308, 1),(7, 565, 1),(7, 871, 1),(7, 872, 1),(7, 873, 1),(7, 874, 2),(7, 875, 1),(7, 876, 1),
(7, 877, 2),(7, 878, 6),(7, 879, 3),(7, 880, 1),(7, 86, 1),(7, 757, 1),(7, 881, 1),(7, 882, 1),(7, 883, 2),(7, 884, 1),(7, 885, 2),(7, 886, 1),
(7, 887, 1),(7, 131, 2),(7, 888, 1),(7, 889, 1),(7, 890, 1),(7, 891, 1),(7, 892, 1),(7, 893, 1),(7, 894, 1),(7, 895, 1),(7, 896, 1),(7, 897, 1),
(7, 898, 1),(7, 899, 1),(7, 900, 1),(7, 901, 1),(7, 902, 1),(7, 903, 1),(7, 904, 1),(7, 905, 1),(7, 906, 1),(7, 907, 1),(7, 118, 2),(7, 136, 1),
(7, 576, 1),(7, 908, 1),(7, 909, 1),(7, 910, 1),(7, 911, 1),(7, 912, 1),(7, 524, 1),(7, 913, 1),(7, 109, 2),(7, 570, 1),(7, 914, 1),(7, 915, 1),
(7, 916, 2),(7, 917, 1),(7, 918, 1),(7, 919, 1),(7, 920, 1),(7, 921, 1),(7, 922, 1),(7, 923, 1),(7, 924, 1),(7, 925, 1),(7, 124, 2),(7, 926, 1),
(7, 927, 1),(7, 928, 1),(7, 318, 1),(7, 929, 1),(7, 930, 1),(7, 931, 1),(7, 932, 1),(7, 933, 1),(7, 334, 1),(7, 934, 1),(7, 935, 1),(7, 936, 1),
(7, 500, 1),(7, 937, 1),(7, 938, 1),(7, 939, 1),(7, 940, 1),(7, 941, 1),(7, 942, 1),(7, 943, 1),(7, 734, 1),(7, 944, 1),(7, 945, 1),(7, 946, 1),
(7, 947, 1),(7, 948, 1),(7, 949, 1),(7, 567, 1),(7, 145, 3),(7, 950, 4),(7, 951, 2),(7, 149, 2),(7, 952, 2),(7, 159, 2),(7, 953, 2),(7, 954, 2),
(7, 955, 2),(7, 160, 7),(7, 956, 7),(7, 957, 1),(7, 628, 2),(7, 958, 2),(7, 621, 3),(7, 622, 1),(7, 959, 1),(7, 960, 2),(7, 665, 2),(7, 961, 1),
(7, 962, 1),(7, 963, 2),(7, 964, 1),(7, 362, 1),(7, 965, 1),(7, 966, 1),(7, 967, 1),(7, 968, 2),(7, 969, 1),(7, 970, 1),(7, 971, 2),(7, 972, 1),
(7, 164, 1),(7, 165, 1),(7, 973, 1),(7, 974, 2),(7, 975, 1),(7, 976, 2),(7, 977, 1),(7, 978, 1),(7, 979, 1),(7, 980, 1),(7, 981, 1),(7, 982, 1),
(7, 983, 1),(7, 984, 1),(7, 985, 1),(7, 986, 1),(7, 987, 1),(7, 988, 1),(7, 989, 1),(7, 990, 1),(7, 991, 2),(7, 992, 1),(7, 993, 1),(7, 994, 1),
(7, 995, 1),(7, 615, 2),(7, 213, 1),(7, 996, 1),(7, 204, 1),(7, 997, 1),(7, 998, 1),(7, 764, 1),(7, 999, 1),(7, 1000, 1),(7, 1001, 1),
(7, 1002, 2),(7, 1003, 2),(7, 1004, 4),(7, 1005, 2),(7, 1006, 1),(7, 1007, 1),(7, 1008, 1),(7, 1009, 1),(7, 371, 1),(7, 1010, 1),(7, 1011, 1),
(7, 1012, 1),(7, 1013, 1),(7, 1014, 1),(7, 1015, 1),(7, 1016, 1),(7, 1017, 1),(7, 1018, 1),(7, 384, 1),(7, 1019, 1),(7, 1020, 1),(7, 1021, 1),
(7, 1022, 1),(7, 1023, 1),(7, 1024, 1),(7, 606, 1),(7, 765, 1),(7, 1025, 1),(7, 1026, 1),(7, 1027, 1),(7, 1028, 1),(7, 1029, 1),(7, 1030, 1),
(7, 1031, 1),(7, 1032, 1),(7, 1033, 1),(7, 222, 3),(7, 1034, 2),(7, 226, 2),(7, 1035, 2),(7, 235, 2),(7, 1036, 2),(7, 1037, 2),(7, 1038, 2),
(8, 1039, 6),(8, 1040, 6),(8, 1041, 6),(8, 1042, 6),(8, 1043, 6),(8, 1, 6),(8, 2, 6),(8, 1044, 6),(8, 1045, 6),(8, 1046, 2),(8, 1047, 2),
(8, 1048, 3),(8, 1049, 6),(8, 1050, 6),(8, 1051, 8),(8, 1052, 6),(8, 1053, 6),(8, 152, 6),(8, 1054, 6),(8, 1055, 1),(8, 1056, 1),(8, 1057, 1),
(8, 1058, 1),(8, 1059, 1),(8, 1060, 1),(8, 1061, 1),(8, 1062, 1),(8, 1063, 1),(8, 1064, 1),(8, 1065, 1),(8, 1066, 1),(8, 1067, 1),(8, 109, 1),
(8, 570, 1),(8, 1068, 1),(8, 118, 3),(8, 82, 1),(8, 1069, 1),(8, 124, 1),(8, 926, 1),(8, 882, 1),(8, 1070, 1),(8, 1071, 1),(8, 730, 1),(8, 1072, 2),
(8, 1073, 1),(8, 1074, 1),(8, 1075, 1),(8, 1076, 1),(8, 944, 1),(8, 1077, 1),(8, 1078, 1),(8, 1079, 3),(8, 1080, 6),(8, 1081, 6),(8, 1082, 6),
(8, 1083, 6),(8, 229, 6),(8, 1084, 6),(8, 1085, 1),(8, 1023, 1),(8, 1086, 2),(8, 1087, 1),(8, 1088, 1),(8, 1089, 1),(8, 1090, 1),(8, 1091, 1),
(8, 1092, 1),(8, 1093, 1),(8, 1094, 1),(8, 1095, 1),(8, 1096, 1),(8, 1097, 1),(8, 163, 1),(8, 1098, 1),(8, 1099, 1),(8, 669, 1),(8, 1100, 1),
(8, 160, 1),(8, 1101, 1),(8, 1013, 1),(8, 1102, 1),(8, 1103, 1),(8, 1104, 1),(8, 762, 1),(8, 1105, 2),(8, 1106, 1),(8, 1107, 1),(8, 1108, 1),
(8, 1109, 1),(8, 1025, 1),(8, 1110, 1),(8, 1111, 1),(8, 1112, 1),(8, 1113, 3),(9, 1114, 11),(9, 1115, 9),(9, 1116, 7),(9, 850, 8),(9, 1, 8),
(9, 1117, 8),(9, 1118, 1),(9, 442, 4),(9, 41, 1),(9, 1119, 1),(9, 826, 1),(9, 1120, 1),(9, 1121, 1),(9, 1122, 1),(9, 1123, 1),(9, 1124, 2),(9, 1125, 1),
(9, 1126, 1),(9, 1127, 2),(9, 1128, 3),(9, 20, 1),(9, 1129, 1),(9, 1130, 1),(9, 1131, 1),(9, 1132, 1),(9, 1133, 3),(9, 1134, 1),(9, 1135, 1),
(9, 1136, 1),(9, 1137, 1),(9, 3, 3),(9, 1138, 1),(9, 1139, 1),(9, 1140, 1),(9, 1141, 1),(9, 38, 2),(9, 840, 1),(9, 1142, 1),(9, 238, 2),(9, 1143, 1),
(9, 1144, 1),(9, 1145, 2),(9, 430, 1),(9, 845, 1),(9, 81, 1),(9, 1146, 1),(9, 1147, 1),(9, 1148, 1),(9, 1149, 1),(9, 805, 1),(9, 1150, 1),(9, 1151, 1),
(9, 1152, 1),(9, 1153, 1),(9, 1154, 1),(9, 1155, 1),(9, 1156, 1),(9, 1157, 1),(9, 4, 1),(9, 1158, 2),(9, 51, 5),(9, 1159, 1),(9, 1160, 1),(9, 1161, 1),
(9, 1162, 1),(9, 1163, 1),(9, 1164, 1),(9, 851, 4),(9, 1165, 1),(9, 1166, 1),(9, 1167, 1),(9, 1168, 1),(9, 1169, 1),(9, 1170, 1),(9, 1171, 1),(9, 1172, 1),
(9, 448, 1),(9, 1173, 1),(9, 1174, 1),(9, 1175, 1),(9, 1176, 1),(9, 1177, 1),(9, 1178, 1),(9, 1179, 1),(9, 1180, 1),(9, 1181, 1),(9, 1182, 1),(9, 1183, 1),
(9, 1184, 1),(9, 1185, 1),(9, 1186, 1),(9, 1187, 1),(9, 1188, 1),(9, 1189, 1),(9, 1190, 1),(9, 1191, 2),(9, 1192, 2),(9, 1193, 1),(9, 1194, 3),
(9, 1195, 3),(9, 1196, 2),(9, 1197, 2),(9, 1198, 2),(9, 1199, 1),(9, 1200, 1),(9, 1201, 1),(9, 1202, 1),(9, 1203, 1),(9, 1204, 1),(9, 1205, 2),
(9, 1206, 1),(9, 1207, 2),(9, 1208, 1),(9, 1209, 1),(9, 1210, 2),(9, 1211, 1),(9, 1212, 1),(9, 1213, 1),(9, 1214, 1),(9, 1215, 1),(9, 1216, 1),
(9, 22, 1),(9, 1217, 1),(9, 1218, 1),(9, 1219, 4),(9, 1220, 2),(9, 1221, 1),(9, 1222, 1),(9, 1223, 1),(9, 47, 2),(9, 1224, 1),(9, 68, 2),
(9, 1225, 1),(9, 1226, 1),(9, 1227, 1),(9, 1228, 1),(9, 1229, 1),(9, 1230, 1),(9, 1231, 1),(9, 1232, 1),(9, 1233, 1),(9, 1234, 1),(9, 1235, 2),
(9, 1236, 1),(9, 1237, 1),(9, 1238, 1),(9, 1239, 1),(9, 1240, 1),(9, 1241, 1),(9, 1242, 1),(9, 1243, 1),(9, 1244, 1),(9, 1245, 1),(9, 1246, 1),
(9, 1247, 1),(9, 1048, 3),(9, 1248, 3),(9, 942, 17),(9, 1249, 13),(9, 1250, 13),(9, 1251, 11),(9, 1252, 11),(9, 1253, 1),(9, 1254, 2),(9, 1255, 1),
(9, 1256, 1),(9, 1257, 5),(9, 90, 2),(9, 1258, 1),(9, 1259, 1),(9, 1260, 1),(9, 159, 1),(9, 583, 1),(9, 1261, 1),(9, 1262, 1),(9, 910, 1),
(9, 1263, 1),(9, 1264, 1),(9, 1265, 1),(9, 1266, 1),(9, 1267, 1),(9, 1268, 1),(9, 1269, 1),(9, 1270, 2),(9, 1271, 1),(9, 1272, 1),(9, 1273, 1),
(9, 1274, 1),(9, 1275, 1),(9, 1276, 3),(9, 1277, 2),(9, 1278, 1),(9, 103, 1),(9, 1279, 2),(9, 1280, 1),(9, 1065, 1),(9, 1281, 4),(9, 1282, 1),
(9, 1283, 1),(9, 1284, 1),(9, 1285, 1),(9, 85, 1),(9, 1286, 1),(9, 1287, 1),(9, 943, 7),(9, 1288, 3),(9, 1289, 1),(9, 1290, 1),(9, 562, 1),
(9, 1291, 1),(9, 109, 7),(9, 1292, 1),(9, 1293, 1),(9, 118, 2),(9, 136, 2),(9, 1294, 1),(9, 95, 7),(9, 1295, 1),(9, 1296, 1),(9, 918, 1),
(9, 1297, 1),(9, 931, 1),(9, 1298, 1),(9, 1299, 1),(9, 1300, 1),(9, 1301, 1),(9, 1302, 1),(9, 131, 3),(9, 1303, 2),(9, 1304, 2),(9, 1305, 2),
(9, 1306, 1),(9, 1307, 2),(9, 1308, 1),(9, 1309, 1),(9, 1310, 1),(9, 1311, 1),(9, 319, 1),(9, 1312, 2),(9, 1313, 1),(9, 1314, 1),(9, 1315, 1),
(9, 500, 1),(9, 499, 1),(9, 1316, 4),(9, 1317, 1),(9, 1318, 1),(9, 1319, 1),(9, 1320, 1),(9, 1321, 1),(9, 523, 1),(9, 1322, 1),(9, 1323, 1),
(9, 1324, 3),(9, 1325, 3),(9, 1326, 2),(9, 1327, 1),(9, 1328, 1),(9, 1329, 1),(9, 1330, 1),(9, 1331, 1),(9, 1332, 1),(9, 1333, 1),(9, 1334, 1),
(9, 1335, 1),(9, 1336, 1),(9, 1337, 1),(9, 299, 1),(9, 553, 2),(9, 1338, 1),(9, 1339, 1),(9, 1340, 1),(9, 1341, 1),(9, 1342, 1),(9, 1343, 1),
(9, 1344, 1),(9, 330, 1),(9, 544, 1),(9, 1345, 1),(9, 1346, 1),(9, 1347, 1),(9, 1348, 1),(9, 1349, 1),(9, 1350, 1),(9, 1351, 1),(9, 1352, 1),
(9, 1353, 1),(9, 1354, 1),(9, 1355, 1),(9, 1356, 1),(9, 1357, 1),(9, 1358, 1),(9, 1359, 1),(9, 1360, 1),(9, 1361, 1),(9, 1362, 1),(9, 1363, 1),
(9, 1364, 1),(9, 1365, 2),(9, 1366, 1),(9, 1367, 2),(9, 1368, 1),(9, 1369, 1),(9, 505, 2),(9, 1370, 1),(9, 1371, 1),(9, 1372, 2),(9, 570, 1),
(9, 1373, 1),(9, 1374, 1),(9, 1375, 1),(9, 1376, 1),(9, 1055, 3),(9, 1377, 2),(9, 1378, 1),(9, 1379, 1),(9, 730, 1),(9, 1380, 1),(9, 1381, 1),
(9, 1382, 1),(9, 1383, 1),(9, 1384, 2),(9, 1385, 1),(9, 1386, 2),(9, 1387, 1),(9, 1388, 1),(9, 889, 1),(9, 1389, 1),(9, 1390, 1),(9, 1391, 1),
(9, 1392, 1),(9, 1393, 1),(9, 938, 1),(9, 1394, 1),(9, 1395, 1),(9, 1396, 1),(9, 1397, 1),(9, 1398, 1),(9, 1399, 1),(9, 940, 1),(9, 941, 1),
(9, 1400, 1),(9, 1401, 1),(9, 1402, 1),(9, 1079, 3),(9, 1403, 3),(9, 1404, 4),(9, 1405, 4),(9, 1024, 12),(9, 1406, 6),(9, 1407, 6),(9, 1408, 12),
(9, 1409, 11),(9, 1410, 9),(9, 1411, 1),(9, 1412, 2),(9, 1413, 1),(9, 1414, 1),(9, 1415, 1),(9, 1416, 1),(9, 1417, 1),(9, 1418, 5),(9, 1419, 1),
(9, 1420, 1),(9, 1421, 1),(9, 1422, 1),(9, 1423, 1),(9, 1424, 3),(9, 1425, 1),(9, 764, 2),(9, 1426, 1),(9, 1427, 1),(9, 1428, 1),(9, 1429, 1),
(9, 1430, 2),(9, 1431, 1),(9, 1432, 2),(9, 1433, 1),(9, 1434, 1),(9, 1435, 2),(9, 1436, 1),(9, 1437, 3),(9, 1438, 2),(9, 1439, 1),(9, 181, 1),
(9, 1440, 3),(9, 356, 2),(9, 658, 2),(9, 173, 11),(9, 1441, 6),(9, 1442, 5),(9, 1443, 1),(9, 1444, 1),(9, 1445, 2),(9, 1446, 1),(9, 163, 1),
(9, 1447, 1),(9, 1448, 1),(9, 606, 7),(9, 1449, 3),(9, 1450, 1),(9, 1451, 1),(9, 662, 1),(9, 1452, 1),(9, 1453, 1),(9, 213, 2),(9, 1454, 1),
(9, 1455, 1),(9, 1456, 1),(9, 1457, 1),(9, 610, 1),(9, 1458, 1),(9, 1459, 1),(9, 1460, 1),(9, 1461, 1),(9, 1462, 1),(9, 1463, 1),(9, 1464, 2),
(9, 1465, 2),(9, 1466, 2),(9, 1467, 1),(9, 1468, 2),(9, 631, 1),(9, 1469, 2),(9, 1470, 1),(9, 1471, 2),(9, 1472, 1),(9, 1473, 1),(9, 1474, 2),
(9, 1475, 1),(9, 1476, 1),(9, 660, 1),(9, 601, 1),(9, 1477, 4),(9, 635, 1),(9, 1478, 1),(9, 653, 3),(9, 654, 1),(9, 1479, 1),(9, 1480, 1),
(9, 1481, 3),(9, 1482, 3),(9, 1483, 2),(9, 1484, 1),(9, 1485, 1),(9, 1486, 1),(9, 1487, 1),(9, 1488, 2),(9, 1489, 1),(9, 1490, 1),(9, 1491, 1),
(9, 1492, 1),(9, 354, 2),(9, 1493, 1),(9, 593, 1),(9, 1494, 1),(9, 1495, 1),(9, 1496, 1),(9, 1497, 1),(9, 381, 1),(9, 1498, 1),(9, 1499, 1),
(9, 1500, 1),(9, 1501, 1),(9, 1502, 1),(9, 1503, 1),(9, 1504, 1),(9, 1505, 1),(9, 1506, 1),(9, 1507, 1),(9, 1508, 1),(9, 1509, 1),(9, 1510, 1),
(9, 1511, 1),(9, 1512, 1),(9, 1513, 1),(9, 1514, 1),(9, 1515, 1),(9, 1516, 1),(9, 1517, 2),(9, 1518, 1),(9, 1519, 1),(9, 1520, 1),(9, 1521, 1),
(9, 1522, 1),(9, 1523, 1),(9, 669, 1),(9, 1524, 1),(9, 1525, 1),(9, 1526, 1),(9, 191, 1),(9, 1023, 3),(9, 1527, 2),(9, 1528, 1),(9, 1099, 1),
(9, 762, 1),(9, 1529, 1),(9, 1530, 1),(9, 1531, 1),(9, 786, 1),(9, 1532, 2),(9, 1533, 1),(9, 1534, 2),(9, 1535, 1),(9, 1536, 1),(9, 973, 1),
(9, 1537, 1),(9, 1103, 1),(9, 1538, 1),(9, 1539, 1),(9, 1540, 1),(9, 1021, 1),(9, 1541, 1),(9, 1542, 1),(9, 1543, 1),(9, 1544, 1),(9, 1545, 1),
(9, 1546, 1),(9, 1022, 1),(9, 1547, 1),(9, 1548, 1),(9, 1549, 1),(9, 1550, 1),(9, 1551, 1),(9, 1113, 3),(9, 1552, 3);

INSERT INTO `PREFIX_search_word` (`id_word`, `id_lang`, `word`) VALUES (1, 1, 'ipod'),(2, 1, 'nano'),(3, 1, 'design'),(4, 1, 'features'),(5, 1, '16gb'),
(6, 1, 'rocks'),(7, 1, 'like'),(8, 1, 'never'),(9, 1, 'before'),(10, 1, 'curved'),(11, 1, 'ahead'),(12, 1, 'curve'),(13, 1, 'those'),(14, 1, 'about'),
(15, 1, 'rock,'),(16, 1, 'give'),(17, 1, 'nine'),(18, 1, 'amazing'),(19, 1, 'colors'),(20, 1, 'that''s'),(21, 1, 'only'),(22, 1, 'part'),
(23, 1, 'story'),(24, 1, 'feel'),(25, 1, 'curved,'),(26, 1, 'allaluminum'),(27, 1, 'glass'),(28, 1, 'won''t'),(29, 1, 'want'),(30, 1, 'down'),
(31, 1, 'great'),(32, 1, 'looks'),(33, 1, 'brains,'),(34, 1, 'genius'),(35, 1, 'feature'),(36, 1, 'turns'),(37, 1, 'into'),(38, 1, 'your'),
(39, 1, 'highly'),(40, 1, 'intelligent,'),(41, 1, 'personal'),(42, 1, 'creates'),(43, 1, 'playlists'),(44, 1, 'finding'),(45, 1, 'songs'),
(46, 1, 'library'),(47, 1, 'that'),(48, 1, 'together'),(49, 1, 'made'),(50, 1, 'move'),(51, 1, 'with'),(52, 1, 'moves'),(53, 1, 'accelerometer'),
(54, 1, 'comes'),(55, 1, 'shake'),(56, 1, 'shuffle'),(57, 1, 'music'),(58, 1, 'turn'),(59, 1, 'sideways'),(60, 1, 'view'),(61, 1, 'cover'),(62, 1, 'flow'),
(63, 1, 'play'),(64, 1, 'games'),(65, 1, 'designed'),(66, 1, 'mind'),(67, 1, 'ipods'),(68, 1, 'apple'),(69, 1, 'computer,'),(70, 1, 'metal'),
(71, 1, '16go'),(72, 1, 'yellow'),(73, 1, 'blue'),(74, 1, 'black'),(75, 1, 'orange'),(76, 1, 'pink'),(77, 1, 'green'),(78, 1, 'purple'),(79, 1, 'g'),
(80, 1, 'minijack'),(81, 1, 'stereo'),(82, 2, 'ipod'),(83, 2, 'nano'),(84, 2, 'nouveau'),(85, 2, 'design'),(86, 2, 'nouvelles'),(87, 2, 'fonctionnalité'),
(88, 2, 'désormais'),(89, 2, 'nano,'),(90, 2, 'plus'),(91, 2, 'rock'),(92, 2, 'jamais'),(93, 2, 'courbes'),(94, 2, 'avantageuses'),(95, 2, 'pour'),
(96, 2, 'amateurs'),(97, 2, 'sensations,'),(98, 2, 'voici'),(99, 2, 'neuf'),(100, 2, 'nouveaux'),(101, 2, 'coloris'),(102, 2, 'n''est'),(103, 2, 'tout'),
(104, 2, 'faites'),(105, 2, 'l''expérience'),(106, 2, 'elliptique'),(107, 2, 'aluminum'),(108, 2, 'verre'),(109, 2, 'vous'),(110, 2, 'voudrez'),
(111, 2, 'lâcher'),(112, 2, 'beau'),(113, 2, 'intelligent'),(114, 2, 'nouvelle'),(115, 2, 'genius'),(116, 2, 'fait'),(117, 2, 'd''ipod'),(118, 2, 'votre'),
(119, 2, 'personnel'),(120, 2, 'crée'),(121, 2, 'listes'),(122, 2, 'lecture'),(123, 2, 'recherchant'),(124, 2, 'dans'),(125, 2, 'bibliothèque'),
(126, 2, 'chansons'),(127, 2, 'vont'),(128, 2, 'bien'),(129, 2, 'ensemble'),(130, 2, 'bouger'),(131, 2, 'avec'),(132, 2, 'équipé'),(133, 2, 'l''accéléromè'),
(134, 2, 'secouezle'),(135, 2, 'mélanger'),(136, 2, 'musique'),(137, 2, 'basculezle'),(138, 2, 'afficher'),(139, 2, 'cover'),(140, 2, 'flow'),
(141, 2, 'découvrez'),(142, 2, 'jeux'),(143, 2, 'adaptés'),(144, 2, 'mouvements'),(145, 2, 'ipods'),(146, 2, 'apple'),(147, 2, 'computer,'),(148, 2, 'metal'),
(149, 2, '16go'),(150, 2, 'jaune'),(151, 2, 'bleu'),(152, 2, 'noir'),(153, 2, 'orange'),(154, 2, 'rose'),(155, 2, 'vert'),(156, 2, 'violet'),
(157, 2, 'g'),(158, 2, 'minijack'),(159, 2, 'stéréo'),(160, 3, 'ipod'),(161, 3, 'nano'),(162, 3, 'nuevo'),(163, 3, 'diseño'),(164, 3, 'nuevas'),
(165, 3, 'aplicaciones'),(166, 3, 'ahora'),(167, 3, 'disponible'),(168, 3, 'nano,'),(169, 3, 'rock'),(170, 3, 'nunca'),(171, 3, 'curvas'),
(172, 3, 'aerodinámicas'),(173, 3, 'para'),(174, 3, 'aficionados'),(175, 3, 'sensaciones'),(176, 3, 'fuertes,'),(177, 3, 'presentamos'),(178, 3, 'nueve'),
(179, 3, 'nuevos'),(180, 3, 'colores'),(181, 3, 'todo'),(182, 3, 'experimenta'),(183, 3, 'elíptico'),(184, 3, 'aluminio'),(185, 3, 'vidrio'),
(186, 3, 'querrás'),(187, 3, 'separarte'),(188, 3, 'estético'),(189, 3, 'inteligente'),(190, 3, 'nueva'),(191, 3, 'aplicación'),(192, 3, 'genius'),
(193, 3, 'hace'),(194, 3, 'discjockey'),(195, 3, 'personal'),(196, 3, 'genuis'),(197, 3, 'crea'),(198, 3, 'listas'),(199, 3, 'lectura'),(200, 3, 'buscando'),
(201, 3, 'biblioteca'),(202, 3, 'canciones'),(203, 3, 'combinan'),(204, 3, 'entre'),(205, 3, 'hecho'),(206, 3, 'moverse'),(207, 3, 'contigo'),(208, 3, 'está'),
(209, 3, 'equipado'),(210, 3, 'acelerómetro'),(211, 3, 'muévelo'),(212, 3, 'mezclar'),(213, 3, 'música'),(214, 3, 'voltéalo'),(215, 3, 'mostrar'),
(216, 3, 'cover'),(217, 3, 'flow'),(218, 3, 'descubre'),(219, 3, 'juegos'),(220, 3, 'adaptados'),(221, 3, 'movimientos'),(222, 3, 'ipods'),(223, 3, 'apple'),
(224, 3, 'computer,'),(225, 3, 'metal'),(226, 3, '16go'),(227, 3, 'amarillo'),(228, 3, 'azul'),(229, 3, 'negro'),(230, 3, 'naranja'),(231, 3, 'rosa'),
(232, 3, 'verde'),(233, 3, 'violeta'),(234, 3, 'minijack'),(235, 3, 'stéréo'),(236, 1, 'shuffle,'),(237, 1, 'world'),(238, 1, 'most'),(239, 1, 'wearable'),
(240, 1, 'player,'),(241, 1, 'clips'),(242, 1, 'more'),(243, 1, 'vibrant'),(244, 1, 'blue,'),(245, 1, 'green,'),(246, 1, 'pink,'),(247, 1, 'instant'),
(248, 1, 'attachment'),(249, 1, 'wear'),(250, 1, 'sleeve'),(251, 1, 'belt'),(252, 1, 'shorts'),(253, 1, 'badge'),(254, 1, 'musical'),(255, 1, 'devotion'),
(256, 1, 'new,'),(257, 1, 'brilliant'),(258, 1, 'feed'),(259, 1, 'itunes'),(260, 1, 'entertainment'),(261, 1, 'superstore'),(262, 1, 'ultraorganized'),
(263, 1, 'collection'),(264, 1, 'jukebox'),(265, 1, 'load'),(266, 1, 'click'),(267, 1, 'beauty'),(268, 1, 'beat'),(269, 1, 'intensely'),(270, 1, 'colorful'),
(271, 1, 'anodized'),(272, 1, 'aluminum'),(273, 1, 'complements'),(274, 1, 'simple'),(275, 1, 'red,'),(276, 1, 'original'),(277, 1, 'silver'),(278, 1, '(clip'),
(279, 1, 'compris)'),(280, 2, 'shuffle'),(281, 2, 'shuffle,'),(282, 2, 'baladeur'),(283, 2, 'portable'),(284, 2, 'monde,'),(285, 2, 'clippe'),(286, 2, 'maintenant'),
(287, 2, 'bleu,'),(288, 2, 'vert,'),(289, 2, 'rouge'),(290, 2, 'lien'),(291, 2, 'immédiat'),(292, 2, 'portez'),(294, 2, 'accrochées'),(295, 2, 'manche,'),
(296, 2, 'ceinture'),(297, 2, 'short'),(298, 2, 'arborez'),(299, 2, 'comme'),(300, 2, 'signe'),(301, 2, 'extérieur'),(302, 2, 'passion'),(303, 2, 'existe'),
(304, 2, 'quatre'),(305, 2, 'encore'),(306, 2, 'éclatants'),(307, 2, 'emplissez'),(308, 2, 'itunes'),(309, 2, 'immense'),(310, 2, 'magasin'),(311, 2, 'dédié'),
(312, 2, 'divertissement,'),(313, 2, 'collection'),(314, 2, 'musicale'),(315, 2, 'parfaitement'),(316, 2, 'organisée'),(317, 2, 'jukebox'),(318, 2, 'pouvez'),
(319, 2, 'seul'),(320, 2, 'clic'),(321, 2, 'remplir'),(322, 2, 'technicolor'),(323, 2, 's''affiche'),(324, 2, 'intenses'),(325, 2, 'rehaussent'),(326, 2, 'épuré'),
(327, 2, 'boîtier'),(328, 2, 'aluminium'),(329, 2, 'anodisé'),(330, 2, 'choisissez'),(331, 2, 'parmi'),(332, 2, 'rose,'),(333, 2, 'l''argenté'),(334, 2, 'd''origine'),
(335, 2, '(clip'),(336, 2, 'compris)'),(337, 3, 'shuffle'),(338, 3, 'shuffle,'),(339, 3, 'walkman'),(340, 3, 'portátil'),(341, 3, 'mundo,'),(342, 3, 'azul,'),
(343, 3, 'verde,'),(344, 3, 'rojo'),(345, 3, 'enlace'),(346, 3, 'inmediato'),(347, 3, 'lleva'),(348, 3, 'hasta'),(349, 3, 'colgadas'),(350, 3, 'manga,'),
(351, 3, 'cinturón'),(352, 3, 'pantalón'),(353, 3, 'presume'),(354, 3, 'como'),(355, 3, 'signo'),(356, 3, 'exterior'),(357, 3, 'pasión'),(358, 3, 'existen'),
(359, 3, 'cuatro'),(360, 3, 'llamativos'),(361, 3, 'llena'),(362, 3, 'itunes'),(363, 3, 'enorme'),(364, 3, 'tienda'),(365, 3, 'dedicada'),(366, 3, 'diversión,'),
(367, 3, 'colección'),(368, 3, 'organizada'),(369, 3, 'perfectamente'),(370, 3, 'jukebox'),(371, 3, 'solo'),(372, 3, 'clic'),(373, 3, 'puedes'),(374, 3, 'llenar'),
(375, 3, 'tecnicolor'),(376, 3, 'presenta'),(377, 3, 'vivos'),(378, 3, 'realzan'),(379, 3, 'estilizado'),(380, 3, 'anodizado'),(381, 3, 'elige'),(382, 3, 'rosa,'),
(383, 3, 'plateado'),(384, 3, 'origen'),(385, 3, '(clip'),(386, 3, 'incluyendo)'),(387, 1, 'macbook'),(388, 1, 'ultrathin,'),(389, 1, 'ultraportable,'),
(390, 1, 'ultra'),(391, 1, 'unlike'),(392, 1, 'anything'),(393, 1, 'else'),(394, 1, 'lose'),(395, 1, 'inches'),(396, 1, 'pounds'),(397, 1, 'overnight'),
(398, 1, 'result'),(399, 1, 'rethinking'),(400, 1, 'conventions'),(401, 1, 'multiple'),(402, 1, 'wireless'),(403, 1, 'innovations'),(404, 1, 'breakthrough'),
(405, 1, 'air,'),(406, 1, 'mobile'),(407, 1, 'computing'),(408, 1, 'suddenly'),(409, 1, 'standard'),(410, 1, 'nearly'),(411, 1, 'thin'),(412, 1, 'index'),
(413, 1, 'finger'),(414, 1, 'practically'),(415, 1, 'every'),(416, 1, 'detail'),(417, 1, 'could'),(418, 1, 'streamlined'),(419, 1, 'been'),(420, 1, 'still'),
(421, 1, '133inch'),(422, 1, 'widescreen'),(423, 1, 'display,'),(424, 1, 'fullsize'),(425, 1, 'keyboard,'),(426, 1, 'large'),(427, 1, 'multitouch'),
(428, 1, 'trackpad'),(429, 1, 'incomparably'),(430, 1, 'portable'),(431, 1, 'without'),(432, 1, 'usual'),(433, 1, 'ultraportable'),(434, 1, 'screen'),
(435, 1, 'keyboard'),(436, 1, 'compromisesthe'),(437, 1, 'incredible'),(438, 1, 'thinness'),(439, 1, 'numerous'),(440, 1, 'size'),(441, 1, 'weightshaving'),
(442, 1, 'from'),(443, 1, 'slimmer'),(444, 1, 'hard'),(445, 1, 'drive'),(446, 1, 'strategically'),(447, 1, 'hidden'),(448, 1, 'ports'),(449, 1, 'lowerprofile'),
(450, 1, 'battery,'),(451, 1, 'everything'),(452, 1, 'considered'),(453, 1, 'reconsidered'),(454, 1, 'mindmacbook'),(455, 1, 'engineered'),(456, 1, 'take'),
(457, 1, 'full'),(458, 1, 'advantage'),(459, 1, 'which'),(460, 1, '80211n'),(461, 1, 'wifi'),(462, 1, 'fast'),(463, 1, 'available,'),(464, 1, 'people'),
(465, 1, 'truly'),(466, 1, 'living'),(467, 1, 'untethered'),(468, 1, 'buying'),(469, 1, 'renting'),(470, 1, 'movies'),(471, 1, 'online,'),(472, 1, 'downloading'),
(473, 1, 'software,'),(474, 1, 'sharing'),(475, 1, 'storing'),(476, 1, 'files'),(477, 1, 'laptops'),(478, 1, '80gb'),(479, 1, 'parallel'),(480, 1, '4200'),
(481, 1, '160ghz'),(482, 1, 'intel'),(483, 1, 'core'),(484, 1, 'optional'),(485, 1, '64gb'),(486, 1, 'solidstate'),(487, 1, '180ghz'),(488, 2, 'macbook'),
(489, 2, 'ultra'),(490, 2, 'fin,'),(491, 2, 'différent'),(492, 2, 'reste'),(493, 2, 'mais'),(494, 2, 'perd'),(495, 2, 'kilos'),(496, 2, 'centimètres'),
(497, 2, 'nuit'),(498, 2, 'c''est'),(499, 2, 'résultat'),(500, 2, 'd''une'),(501, 2, 'réinvention'),(502, 2, 'normes'),(503, 2, 'multitude'),
(504, 2, 'd''innovations'),(505, 2, 'sans'),(506, 2, 'révolution'),(507, 2, 'air,'),(508, 2, 'l''informatique'),(509, 2, 'mobile'),(510, 2, 'prend'),
(511, 2, 'soudain'),(512, 2, 'dimension'),(513, 2, 'presque'),(514, 2, 'aussi'),(515, 2, 'index'),(516, 2, 'pratiquement'),(517, 2, 'pouvait'),
(518, 2, 'être'),(519, 2, 'simplifié'),(520, 2, 'n''en'),(521, 2, 'dispose'),(522, 2, 'moins'),(523, 2, 'd''un'),(524, 2, 'écran'),(525, 2, 'panoramique'),
(526, 2, 'pouces,'),(527, 2, 'clavier'),(528, 2, 'complet'),(529, 2, 'vaste'),(530, 2, 'trackpad'),(531, 2, 'multitouch'),(532, 2, 'incomparablemen'),
(533, 2, 'évite'),(534, 2, 'compromis'),(535, 2, 'habituels'),(536, 2, 'matière'),(537, 2, 'd''écran'),(538, 2, 'ultraportablesl'),(539, 2, 'finesse'),
(540, 2, 'grand'),(541, 2, 'nombre'),(542, 2, 'termes'),(543, 2, 'réduction'),(544, 2, 'taille'),(545, 2, 'poids'),(546, 2, 'disque'),(547, 2, 'ports'),
(548, 2, 'habilement'),(549, 2, 'dissimulés'),(550, 2, 'passant'),(551, 2, 'batterie'),(552, 2, 'plate,'),(553, 2, 'chaque'),(554, 2, 'détail'),
(555, 2, 'considéré'),(556, 2, 'reconsidéré'),(557, 2, 'l''espritmacbook'),(558, 2, 'conçu'),(559, 2, 'élaboré'),(560, 2, 'profiter'),(561, 2, 'pleinement'),
(562, 2, 'monde'),(563, 2, 'lequel'),(564, 2, 'norme'),(565, 2, 'wifi'),(566, 2, '80211n'),(567, 2, 'rapide'),(568, 2, 'accessible'),(569, 2, 'qu''elle'),
(570, 2, 'permet'),(571, 2, 'véritablement'),(572, 2, 'libérer'),(573, 2, 'toute'),(574, 2, 'attache'),(575, 2, 'acheter'),(576, 2, 'vidéos'),(577, 2, 'ligne,'),
(578, 2, 'télécharger'),(579, 2, 'logicééééie'),(580, 2, 'stocker'),(581, 2, 'partager'),(582, 2, 'fichiers'),(583, 2, 'portables'),(584, 2, 'macbookair'),
(585, 2, 'pata'),(586, 2, 'intel'),(587, 2, 'core'),(588, 2, '(solidstate'),(589, 2, 'drive)'),(590, 3, 'macbook'),(591, 3, 'ultra'),(592, 3, 'fino,'),
(593, 3, 'diferente'),(594, 3, 'resto'),(595, 3, 'pero'),(596, 3, 'pierden'),(597, 3, 'kilos'),(598, 3, 'centímetros'),(599, 3, 'noche'),(600, 3, 'esto'),
(601, 3, 'resultado'),(602, 3, 'invento'),(603, 3, 'normas'),(604, 3, 'sinfín'),(605, 3, 'novedades'),(606, 3, 'cable'),(607, 3, 'revolución'),
(608, 3, 'air,'),(609, 3, 'informática'),(610, 3, 'móvil'),(611, 3, 'adquiere'),(612, 3, 'dimensión'),(613, 3, 'casi'),(614, 3, 'fino'),(615, 3, 'dedo'),
(616, 3, 'simplificado'),(617, 3, 'máximo'),(618, 3, 'pesar'),(619, 3, 'ello'),(620, 3, 'dispone'),(621, 3, 'pantalla'),(622, 3, 'panorámica'),
(623, 3, 'pulgadas,'),(624, 3, 'teclado'),(625, 3, 'completo'),(626, 3, 'amplio'),(627, 3, 'trackpad'),(628, 3, 'multitouch'),(629, 3, '100%,'),
(630, 3, 'evitará'),(631, 3, 'tener'),(632, 3, 'hacer'),(633, 3, 'compromiso'),(634, 3, 'concierne'),(635, 3, 'increíble'),(636, 3, 'sutileza'),
(637, 3, 'gran'),(638, 3, 'número'),(639, 3, 'innovaciones'),(640, 3, 'materia'),(641, 3, 'reducción'),(642, 3, 'tamaño'),(643, 3, 'peso'),(644, 3, 'desde'),
(645, 3, 'disco'),(646, 3, 'duro'),(647, 3, 'puertos'),(648, 3, 'disimulados'),(649, 3, 'hábilmente'),(650, 3, 'pasando'),(651, 3, 'batería'),
(652, 3, 'plana,'),(653, 3, 'cada'),(654, 3, 'detalle'),(655, 3, 'consideró'),(656, 3, 'fuera'),(657, 3, 'posible'),(658, 3, 'creado'),(659, 3, 'elaborado'),
(660, 3, 'disfrutar'),(661, 3, 'plenamente'),(662, 3, 'mundo'),(663, 3, 'inalámbrico'),(664, 3, 'norma'),(665, 3, 'wifi'),(666, 3, '80211n'),
(667, 3, 'rápida'),(668, 3, 'accesible'),(669, 3, 'permite'),(670, 3, 'liberarse'),(671, 3, 'completamente'),(672, 3, 'cualquier'),(673, 3, 'atadura'),
(674, 3, 'comprar'),(675, 3, 'videos'),(676, 3, 'línea,'),(677, 3, 'descargar'),(678, 3, 'programas,'),(679, 3, 'almacenar'),(680, 3, 'compartir'),
(681, 3, 'archivos'),(682, 3, 'portátiles'),(683, 3, 'pata'),(684, 3, 'intel'),(685, 3, 'core'),(686, 3, '(solidstate'),(687, 3, 'drive)'),(688, 1, 'makes'),
(689, 1, 'easy'),(690, 1, 'road'),(691, 1, 'thanks'),(692, 1, 'tough'),(693, 1, 'polycarbonate'),(694, 1, 'case,'),(695, 1, 'builtin'),(696, 1, 'technologies,'),
(697, 1, 'innovative'),(698, 1, 'magsafe'),(699, 1, 'power'),(700, 1, 'adapter'),(701, 1, 'releases'),(702, 1, 'automatically'),(703, 1, 'someone'),
(704, 1, 'accidentally'),(705, 1, 'trips'),(706, 1, 'cord'),(707, 1, 'larger'),(708, 1, 'drive,'),(709, 1, '250gb,'),(710, 1, 'store'),(711, 1, 'growing'),
(712, 1, 'media'),(713, 1, 'collections'),(714, 1, 'valuable'),(715, 1, 'datathe'),(716, 1, '24ghz'),(717, 1, 'models'),(718, 1, 'include'),(719, 1, 'memory'),
(720, 1, 'perfect'),(721, 1, 'running'),(722, 1, 'favorite'),(723, 1, 'applications'),(724, 1, 'smoothly'),(725, 1, 'superdrive'),(726, 2, 'offre'),
(727, 2, 'liberté'),(728, 2, 'mouvement'),(729, 2, 'grâce'),(730, 2, 'résistant'),(731, 2, 'polycarbonate,'),(732, 2, 'technologies'),(733, 2, 'intégrées'),
(734, 2, 'adaptateur'),(735, 2, 'secteur'),(736, 2, 'magsafe'),(737, 2, 'novateur'),(738, 2, 'déconnecte'),(739, 2, 'automatiquement'),(740, 2, 'quelqu''un'),
(741, 2, 'pieds'),(742, 2, 'spacieux,'),(743, 2, 'capacité'),(744, 2, 'atteignant'),(745, 2, 'collections'),(746, 2, 'multimédia'),(747, 2, 'expansion'),
(748, 2, 'données'),(749, 2, 'précieusesle'),(750, 2, 'modèle'),(751, 2, 'intègre'),(752, 2, 'mémoire'),(753, 2, 'standard'),(754, 2, 'l''idéal'),
(755, 2, 'exécuter'),(756, 2, 'souplesse'),(757, 2, 'applications'),(758, 2, 'préférées'),(759, 3, 'ofrece'),(760, 3, 'libertad'),(761, 3, 'gracias'),
(762, 3, 'resistente'),(763, 3, 'policarbonato,'),(764, 3, 'tecnología'),(765, 3, 'adaptador'),(766, 3, 'cargador'),(767, 3, 'sector'),(768, 3, 'innovador'),
(769, 3, 'desconecta'),(770, 3, 'automáticament'),(771, 3, 'alguien'),(772, 3, 'engancha'),(773, 3, 'espacioso,'),(774, 3, 'capacidad'),(775, 3, 'colecciones'),
(776, 3, 'multimedia'),(777, 3, 'expansión'),(778, 3, 'datos'),(779, 3, 'preciados'),(780, 3, 'modelo'),(781, 3, 'integra'),(782, 3, 'memoria'),(783, 3, 'estándar'),
(784, 3, 'ideal'),(785, 3, 'realizar'),(786, 3, 'dificultad'),(787, 3, 'preferidas'),(788, 1, 'touch'),(789, 1, 'revolutionary'),(790, 1, 'interface'),
(791, 1, '35inch'),(792, 1, 'color'),(793, 1, 'display'),(794, 1, '(80211b'),(795, 1, 'safari,'),(796, 1, 'youtube,'),(797, 1, 'mail,'),(798, 1, 'stocks,'),
(799, 1, 'weather,'),(800, 1, 'notes,'),(801, 1, 'store,'),(802, 1, 'maps'),(803, 1, 'five'),(804, 1, 'handson'),(805, 1, 'rich'),(806, 1, 'html'),
(807, 1, 'email'),(808, 1, 'photos'),(809, 1, 'well'),(810, 1, 'pdf,'),(811, 1, 'word,'),(812, 1, 'excel'),(813, 1, 'attachments'),(814, 1, 'maps,'),
(815, 1, 'directions,'),(816, 1, 'realtime'),(817, 1, 'traffic'),(818, 1, 'information'),(819, 1, 'notes'),(820, 1, 'read'),(821, 1, 'stock'),(822, 1, 'weather'),
(823, 1, 'reports'),(824, 1, 'music,'),(825, 1, 'movies,'),(826, 1, 'technology'),(827, 1, 'built'),(828, 1, 'gorgeous'),(829, 1, 'lets'),(830, 1, 'pinch,'),
(831, 1, 'zoom,'),(832, 1, 'scroll,'),(833, 1, 'flick'),(834, 1, 'fingers'),(835, 1, 'internet'),(836, 1, 'pocket'),(837, 1, 'safari'),(838, 1, 'browser,'),
(839, 1, 'websites'),(840, 1, 'they'),(841, 1, 'were'),(842, 1, 'seen'),(843, 1, 'zoom'),(844, 1, 'tap2'),(845, 1, 'home'),(846, 1, 'quick'),(847, 1, 'access'),
(848, 1, 'sites'),(849, 1, 'what''s'),(850, 1, 'earphones'),(851, 1, 'cable'),(852, 1, 'dock'),(853, 1, 'polishing'),(854, 1, 'cloth'),(855, 1, 'stand'),
(856, 1, 'start'),(857, 1, 'guide'),(858, 1, '32go'),(859, 1, 'jack'),(860, 1, '120g'),(861, 1, '70mm'),(862, 1, '110mm'),(863, 2, 'touch'),
(864, 2, 'interface'),(865, 2, 'révolutionnair'),(866, 2, 'couleur'),(867, 2, 'pouceswifi'),(868, 2, '(80211b'),(869, 2, 'd''épaisseursaf'),(870, 2, 'youtube,'),
(871, 2, 'music'),(872, 2, 'store,'),(873, 2, 'courrier,'),(874, 2, 'cartes,'),(875, 2, 'bourse,'),(876, 2, 'météo,'),(877, 2, 'notes'),(878, 2, 'titre'),
(879, 2, 'paragraphe'),(880, 2, 'cinq'),(881, 2, 'sous'),(882, 2, 'main'),(883, 2, 'consultez'),(884, 2, 'emails'),(885, 2, 'format'),(886, 2, 'html'),
(887, 2, 'enrichi,'),(888, 2, 'photos'),(889, 2, 'pieces'),(890, 2, 'jointes'),(891, 2, 'pdf,'),(892, 2, 'word'),(893, 2, 'excel'),(894, 2, 'obtenez'),
(895, 2, 'itinéraires'),(896, 2, 'informations'),(897, 2, 'l''état'),(898, 2, 'circulation'),(899, 2, 'temps'),(900, 2, 'réel'),(901, 2, 'rédigez'),
(902, 2, 'cours'),(903, 2, 'bourse'),(904, 2, 'bulletins'),(905, 2, 'météo'),(906, 2, 'touchez'),(907, 2, 'doigt'),(908, 2, 'entre'),(909, 2, 'autres'),
(910, 2, 'technologie'),(911, 2, 'intégrée'),(912, 2, 'superbe'),(913, 2, 'pouces'),(914, 2, 'd''effectuer'),(915, 2, 'zooms'),(916, 2, 'avant'),
(917, 2, 'arrière,'),(918, 2, 'faire'),(919, 2, 'défiler'),(920, 2, 'feuilleter'),(921, 2, 'pages'),(922, 2, 'l''aide'),(923, 2, 'seuls'),(924, 2, 'doigts'),
(925, 2, 'internet'),(926, 2, 'poche'),(927, 2, 'navigateur'),(928, 2, 'safari,'),(929, 2, 'consulter'),(930, 2, 'sites'),(931, 2, 'leur'),(932, 2, 'mise'),
(933, 2, 'page'),(934, 2, 'effectuer'),(935, 2, 'zoom'),(936, 2, 'arrière'),(937, 2, 'simple'),(938, 2, 'pression'),(939, 2, 'l''écran'),(940, 2, 'contenu'),
(941, 2, 'coffret'),(942, 2, 'écouteurs'),(943, 2, 'câble'),(944, 2, 'dock'),(945, 2, 'chiffon'),(946, 2, 'nettoyage'),(947, 2, 'support'),(948, 2, 'guide'),
(949, 2, 'démarrage'),(950, 2, 'tacticle'),(951, 2, '32go'),(952, 2, 'jack'),(953, 2, '120g'),(954, 2, '70mm'),(955, 2, '110mm'),(956, 3, 'touch'),
(957, 3, 'interfaz'),(958, 3, 'revolucionaria'),(959, 3, 'color'),(960, 3, 'pulgadas'),(961, 3, '(80211b'),(962, 3, 'espesor'),(963, 3, 'safari,'),
(964, 3, 'youtube,'),(965, 3, 'music'),(966, 3, 'store,'),(967, 3, 'correo,'),(968, 3, 'mapas,'),(969, 3, 'bolsa,'),(970, 3, 'tiempo,'),(971, 3, 'notas'),
(972, 3, 'cinco'),(973, 3, 'mano'),(974, 3, 'consulta'),(975, 3, 'correo'),(976, 3, 'formato'),(977, 3, 'html'),(978, 3, 'enriquecido,'),(979, 3, 'fotos'),
(980, 3, 'ficheros'),(981, 3, 'adjuntos'),(982, 3, 'pdf,'),(983, 3, 'word'),(984, 3, 'excel'),(985, 3, 'consigue'),(986, 3, 'itinerarios'),(987, 3, 'información'),
(988, 3, 'sobre'),(989, 3, 'estado'),(990, 3, 'carreteras'),(991, 3, 'tiempo'),(992, 3, 'real'),(993, 3, 'escribe'),(994, 3, 'bolsa'),(995, 3, 'alcanza'),
(996, 3, 'videos,'),(997, 3, 'otras'),(998, 3, 'cosas'),(999, 3, 'integrada'),(1000, 3, 'magnífica'),(1001, 3, 'permitirá'),(1002, 3, 'efectuar'),(1003, 3, 'zoom'),
(1004, 3, 'hacia'),(1005, 3, 'adelante'),(1006, 3, 'atrás,'),(1007, 3, 'pasar'),(1008, 3, 'ojear'),(1009, 3, 'páginas'),(1010, 3, 'ayuda'),(1011, 3, 'dedos'),
(1012, 3, 'internet'),(1013, 3, 'bolsillo'),(1014, 3, 'navegador'),(1015, 3, 'podrás'),(1016, 3, 'consultar'),(1017, 3, 'sitios'),(1018, 3, 'compaginación'),
(1019, 3, 'atrás'),(1020, 3, 'simple'),(1021, 3, 'presión'),(1022, 3, 'contenido'),(1023, 3, 'estuche'),(1024, 3, 'auriculares'),(1025, 3, 'dock'),(1026, 3, 'paño'),
(1027, 3, 'limpieza'),(1028, 3, 'base'),(1029, 3, 'guía'),(1030, 3, 'inicio'),(1031, 3, 'rápido'),(1032, 3, 'título'),(1033, 3, 'párrafo'),(1034, 3, '32go'),
(1035, 3, 'jack'),(1036, 3, '120g'),(1037, 3, '70mm'),(1038, 3, '110mm'),(1039, 1, 'housse'),(1040, 1, 'portefeuille'),(1041, 1, 'cuir'),(1042, 1, 'belkin'),
(1043, 1, 'pour'),(1044, 1, 'noir'),(1045, 1, 'chocolat'),(1046, 1, 'lorem'),(1047, 1, 'ipsum'),(1048, 1, 'accessories'),(1049, 2, 'housse'),(1050, 2, 'portefeuille'),
(1051, 2, 'cuir'),(1052, 2, '(ipod'),(1053, 2, 'nano)'),(1054, 2, 'chocolat'),(1055, 2, 'étui'),(1056, 2, 'tendance'),(1057, 2, 'assure'),(1058, 2, 'protection'),
(1059, 2, 'complète'),(1060, 2, 'contre'),(1061, 2, 'éraflures'),(1062, 2, 'petits'),(1063, 2, 'aléas'),(1064, 2, 'quotidienne'),(1065, 2, 'conception'),
(1066, 2, 'élégante'),(1067, 2, 'compacte'),(1068, 2, 'glisser'),(1069, 2, 'directement'),(1070, 2, 'caractéristiqu'),(1071, 2, 'doux'),(1072, 2, 'accès'),
(1073, 2, 'bouton'),(1074, 2, 'hold'),(1075, 2, 'fermeture'),(1076, 2, 'magnétique'),(1077, 2, 'connector'),(1078, 2, 'protègeécran'),(1079, 2, 'accessoires'),
(1080, 3, 'leather'),(1081, 3, 'case'),(1082, 3, '(ipod'),(1083, 3, 'nano)'),(1084, 3, 'chocolate'),(1085, 3, 'este'),(1086, 3, 'cuero'),(1087, 3, 'última'),
(1088, 3, 'moda'),(1089, 3, 'garantiza'),(1090, 3, 'completa'),(1091, 3, 'protección'),(1092, 3, 'contra'),(1093, 3, 'arañazos'),(1094, 3, 'pequeños'),
(1095, 3, 'contratiempos'),(1096, 3, 'vida'),(1097, 3, 'diaria'),(1098, 3, 'elegante'),(1099, 3, 'compacto'),(1100, 3, 'meter'),(1101, 3, 'directamente'),
(1102, 3, 'bolso'),(1103, 3, 'característica'),(1104, 3, 'suave'),(1105, 3, 'acceso'),(1106, 3, 'tecla'),(1107, 3, 'hold'),(1108, 3, 'cierre'),
(1109, 3, 'magnético'),(1110, 3, 'conector'),(1111, 3, 'salva'),(1112, 3, 'pantallas'),(1113, 3, 'accesorios'),(1114, 1, 'shure'),(1115, 1, 'se210'),
(1116, 1, 'soundisolating'),(1117, 1, 'iphone'),(1118, 1, 'evolved'),(1119, 1, 'monitor'),(1120, 1, 'roadtested'),(1121, 1, 'musicians'),(1122, 1, 'perfected'),
(1123, 1, 'engineers,'),(1124, 1, 'lightweight'),(1125, 1, 'stylish'),(1126, 1, 'delivers'),(1127, 1, 'fullrange'),(1128, 1, 'audio'),(1129, 1, 'free'),
(1130, 1, 'outside'),(1131, 1, 'noise'),(1132, 1, 'using'),(1133, 1, 'hidefinition'),(1134, 1, 'microspeakers'),(1135, 1, 'deliver'),(1136, 1, 'audio,'),
(1137, 1, 'ergonomic'),(1138, 1, 'ideal'),(1139, 1, 'premium'),(1140, 1, 'onthego'),(1141, 1, 'listening'),(1142, 1, 'offer'),(1143, 1, 'accurate'),
(1144, 1, 'reproduction'),(1145, 1, 'both'),(1146, 1, 'sourcesfor'),(1147, 1, 'ultimate'),(1148, 1, 'precision'),(1149, 1, 'highs'),
(1150, 1, 'addition,'),(1151, 1, 'flexible'),(1152, 1, 'allows'),(1153, 1, 'choose'),(1154, 1, 'comfortable'),(1155, 1, 'variety'),
(1156, 1, 'wearing'),(1157, 1, 'positions'),(1158, 1, 'microspeaker'),(1159, 1, 'single'),(1160, 1, 'balanced'),(1161, 1, 'armature'),
(1162, 1, 'driver'),(1163, 1, 'detachable,'),(1164, 1, 'modular'),(1165, 1, 'make'),(1166, 1, 'longer'),(1167, 1, 'shorter'),(1168, 1, 'depending'),
(1169, 1, 'activity'),(1170, 1, 'connector'),(1171, 1, 'compatible'),(1172, 1, 'earphone'),(1173, 1, 'specifications'),(1174, 1, 'speaker'),
(1175, 1, 'type'),(1176, 1, 'frequency'),(1177, 1, 'range'),(1178, 1, '25hz185khz'),(1179, 1, 'impedance'),(1180, 1, '(1khz)'),(1181, 1, 'ohms'),
(1182, 1, 'sensitivity'),(1183, 1, '(1mw)'),(1184, 1, 'length'),(1185, 1, '(with'),(1186, 1, 'extension)'),(1187, 1, '(540'),(1188, 1, '1371'),
(1189, 1, 'extension'),(1190, 1, '(360'),(1191, 1, 'three'),(1192, 1, 'pairs'),(1193, 1, 'foam'),(1194, 1, 'earpiece'),(1195, 1, 'sleeves'),
(1196, 1, '(small,'),(1197, 1, 'medium,'),(1198, 1, 'large)'),(1199, 1, 'soft'),(1200, 1, 'flex'),(1201, 1, 'pair'),(1202, 1, 'tripleflange'),
(1203, 1, 'carrying'),(1204, 1, 'case'),(1205, 1, 'warranty'),(1206, 1, 'twoyear'),(1207, 1, 'limited'),(1208, 1, '(for'),(1209, 1, 'details,'),
(1210, 1, 'please'),(1211, 1, 'visit'),(1212, 1, 'wwwshurecom'),(1213, 1, 'personalaudio'),(1214, 1, 'customersupport'),(1215, 1, 'productreturnsa'),
(1216, 1, 'indexhtm)'),(1217, 1, 'se210aefs'),(1218, 1, 'note'),(1219, 1, 'products'),(1220, 1, 'sold'),(1221, 1, 'through'),(1222, 1, 'this'),
(1223, 1, 'website'),(1224, 1, 'bear'),(1225, 1, 'brand'),(1226, 1, 'name'),(1227, 1, 'serviced'),(1228, 1, 'supported'),(1229, 1, 'exclusively'),
(1230, 1, 'their'),(1231, 1, 'manufacturers'),(1232, 1, 'accordance'),(1233, 1, 'terms'),(1234, 1, 'conditions'),(1235, 1, 'packaged'),
(1236, 1, 'apple''s'),(1237, 1, 'does'),(1238, 1, 'apply'),(1239, 1, 'applebranded,'),(1240, 1, 'even'),(1241, 1, 'contact'),(1242, 1, 'manufacturer'),
(1243, 1, 'directly'),(1244, 1, 'technical'),(1245, 1, 'support'),(1246, 1, 'customer'),(1247, 1, 'service'),(1248, 1, 'incorporated'),
(1249, 2, 'isolation'),(1250, 2, 'sonore'),(1251, 2, 'shure'),(1252, 2, 'se210'),(1253, 2, 'ergonomiques'),(1254, 2, 'légers'),(1255, 2, 'offrent'),
(1256, 2, 'reproduction'),(1257, 2, 'audio'),(1258, 2, 'fidèle'),(1259, 2, 'provenance'),(1260, 2, 'sources'),(1261, 2, 'salon'),(1262, 2, 'basés'),
(1263, 2, 'moniteurs'),(1264, 2, 'personnels'),(1265, 2, 'testée'),(1266, 2, 'route'),(1267, 2, 'musiciens'),(1268, 2, 'professionnels'),(1269, 2, 'perfectionnée'),
(1270, 2, 'ingénieurs'),(1271, 2, 'shure,'),(1272, 2, 'se210,'),(1273, 2, 'élégants,'),(1274, 2, 'fournissent'),(1275, 2, 'sortie'),(1276, 2, 'gamme'),
(1277, 2, 'étendue'),(1278, 2, 'exempte'),(1279, 2, 'bruit'),(1280, 2, 'externe'),(1281, 2, 'embouts'),(1282, 2, 'fournis'),(1283, 2, 'bloquent'),
(1284, 2, 'ambiant'),(1285, 2, 'combinés'),(1286, 2, 'ergonomique'),(1287, 2, 'séduisant'),(1288, 2, 'modulaire,'),(1289, 2, 'minimisent'),(1290, 2, 'intrusions'),
(1291, 2, 'extérieur,'),(1292, 2, 'permettant'),(1293, 2, 'concentrer'),(1294, 2, 'conçus'),(1295, 2, 'amoureux'),(1296, 2, 'souhaitent'),(1297, 2, 'évoluer'),
(1298, 2, 'appareil'),(1299, 2, 'portable,'),(1300, 2, 'permettent'),(1301, 2, 'd''emmener'),(1302, 2, 'performance'),(1303, 2, 'microtransducte'),(1304, 2, 'haute'),
(1305, 2, 'définition'),(1306, 2, 'développés'),(1307, 2, 'écoute'),(1308, 2, 'qualité'),(1309, 2, 'supérieure'),(1310, 2, 'déplacement,'),(1311, 2, 'utilisent'),
(1312, 2, 'transducteur'),(1313, 2, 'armature'),(1314, 2, 'équilibrée'),(1315, 2, 'bénéficier'),(1316, 2, 'confort'),(1317, 2, 'd''écoute'),(1318, 2, 'époustouflant'),
(1319, 2, 'restitue'),(1320, 2, 'tous'),(1321, 2, 'détails'),(1322, 2, 'spectacle'),(1323, 2, 'live'),(1324, 2, 'universel'),(1325, 2, 'deluxe'),
(1326, 2, 'comprend'),(1327, 2, 'éléments'),(1328, 2, 'suivants'),(1329, 2, 'inclus'),(1330, 2, 'double'),(1331, 2, 'rôle'),(1332, 2, 'bloquer'),
(1333, 2, 'bruits'),(1334, 2, 'ambiants'),(1335, 2, 'garantir'),(1336, 2, 'maintien'),(1337, 2, 'personnalisés'),(1338, 2, 'oreille'),(1339, 2, 'différente,'),
(1340, 2, 'trois'),(1341, 2, 'tailles'),(1342, 2, 'd''embouts'),(1343, 2, 'mousse'),(1344, 2, 'flexibles'),(1345, 2, 'style'),(1346, 2, 'd''embout'),
(1347, 2, 'conviennent'),(1348, 2, 'mieux'),(1349, 2, 'bonne'),(1350, 2, 'étanchéité'),(1351, 2, 'facteur'),(1352, 2, 'optimiser'),(1353, 2, 'l''isolation'),
(1354, 2, 'réponse'),(1355, 2, 'basses,'),(1356, 2, 'ainsi'),(1357, 2, 'accroître'),(1358, 2, 'prolongée'),(1359, 2, 'modulaire'),(1360, 2, 'basant'),
(1361, 2, 'commentaires'),(1362, 2, 'nombreux'),(1363, 2, 'utilisateurs,'),(1364, 2, 'développé'),(1365, 2, 'solution'),(1366, 2, 'détachable'),
(1367, 2, 'permettre'),(1368, 2, 'degré'),(1369, 2, 'personnalisatio'),(1370, 2, 'précédent'),(1371, 2, 'mètre'),(1372, 2, 'fourni'),(1373, 2, 'd''adapter'),
(1374, 2, 'fonction'),(1375, 2, 'l''activité'),(1376, 2, 'l''application'),(1377, 2, 'transport'),(1378, 2, 'outre'),(1379, 2, 'compact'),(1380, 2, 'ranger'),
(1381, 2, 'manière'),(1382, 2, 'pratique'),(1383, 2, 'encombres'),(1384, 2, 'garantie'),(1385, 2, 'limitée'),(1386, 2, 'deux'),(1387, 2, 'achetée'),
(1388, 2, 'couverte'),(1389, 2, 'maind''œuvre'),(1390, 2, 'anscaractérist'),(1391, 2, 'techniques'),(1392, 2, 'type'),(1393, 2, 'sensibilité'),
(1394, 2, 'acoustique'),(1395, 2, 'impédance'),(1396, 2, 'khz)'),(1397, 2, 'fréquences'),(1398, 2, 'longueur'),(1399, 2, 'rallonge'),(1400, 2, '(embouts'),
(1401, 2, 'sonore,'),(1402, 2, 'transport)'),(1403, 2, 'incorporated'),(1404, 2, 'casque'),(1405, 2, 'marche'),(1406, 3, 'aislantes'),(1407, 3, 'sonido'),
(1408, 3, 'shure'),(1409, 3, 'se210'),(1410, 3, 'aislamiento'),(1411, 3, 'ergonómicos'),(1412, 3, 'ligeros'),(1413, 3, 'ofrecen'),(1414, 3, 'reproducción'),
(1415, 3, 'fiel'),(1416, 3, 'proveniente'),(1417, 3, 'fuentes'),(1418, 3, 'audio'),(1419, 3, 'estéreo'),(1420, 3, 'móviles'),(1421, 3, 'salón'),(1422, 3, 'se210,'),
(1423, 3, 'elegantes,'),(1424, 3, 'están'),(1425, 3, 'basados'),(1426, 3, 'monitores'),(1427, 3, 'personales'),(1428, 3, 'músicos'),(1429, 3, 'profesionales'),
(1430, 3, 'utilizan'),(1431, 3, 'carretera'),(1432, 3, 'ingenieros'),(1433, 3, 'perfeccionado'),(1434, 3, 'también'),(1435, 3, 'provistos'),(1436, 3, 'salida'),
(1437, 3, 'gama'),(1438, 3, 'extendida'),(1439, 3, 'exenta'),(1440, 3, 'ruido'),(1441, 3, 'sonoro'),(1442, 3, 'almohadillas'),(1443, 3, 'provistas'),
(1444, 3, 'bloquean'),(1445, 3, 'ambiente'),(1446, 3, 'combinadas'),(1447, 3, 'ergonómico'),(1448, 3, 'atractivo'),(1449, 3, 'modular,'),(1450, 3, 'minimizan'),
(1451, 3, 'intrusiones'),(1452, 3, 'permiten'),(1453, 3, 'concentrarte'),(1454, 3, 'creados'),(1455, 3, 'apasionados'),(1456, 3, 'quieren'),(1457, 3, 'aparato'),
(1458, 3, 'evolucione,'),(1459, 3, 'permitirán'),(1460, 3, 'llevar'),(1461, 3, 'allí'),(1462, 3, 'donde'),(1463, 3, 'vayas'),(1464, 3, 'microtransducto'),
(1465, 3, 'alta'),(1466, 3, 'definición'),(1467, 3, 'desarrollados'),(1468, 3, 'poder'),(1469, 3, 'audición'),(1470, 3, 'calidad'),(1471, 3, 'durante'),
(1472, 3, 'desplazamientos'),(1473, 3, 'único'),(1474, 3, 'transductor'),(1475, 3, 'armazón'),(1476, 3, 'equilibrado'),(1477, 3, 'confort'),
(1478, 3, 'restituye'),(1479, 3, 'espectáculo'),(1480, 3, 'directo'),(1481, 3, 'universal'),(1482, 3, 'deluxe'),(1483, 3, 'incluye'),(1484, 3, 'siguientes'),
(1485, 3, 'elementos'),(1486, 3, 'tienen'),(1487, 3, 'doble'),(1488, 3, 'función'),(1489, 3, 'bloquear'),(1490, 3, 'garantizar'),(1491, 3, 'estabilidad'),
(1492, 3, 'personalizados'),(1493, 3, 'oreja'),(1494, 3, 'tres'),(1495, 3, 'tallas'),(1496, 3, 'espuma'),(1497, 3, 'flexibles'),(1498, 3, 'talla'),
(1499, 3, 'estilo'),(1500, 3, 'almohadilla'),(1501, 3, 'mejor'),(1502, 3, 'convenga'),(1503, 3, 'buen'),(1504, 3, 'factor'),(1505, 3, 'clave'),(1506, 3, 'tanto'),
(1507, 3, 'optimizar'),(1508, 3, 'respuesta'),(1509, 3, 'bajos'),(1510, 3, 'aumentar'),(1511, 3, 'prolongada'),(1512, 3, 'modular'),(1513, 3, 'basándose'),
(1514, 3, 'comentarios'),(1515, 3, 'numerosos'),(1516, 3, 'usuarios,'),(1517, 3, 'solución'),(1518, 3, 'separable'),(1519, 3, 'permitir'),(1520, 3, 'grado'),
(1521, 3, 'personalizació'),(1522, 3, 'precedentes'),(1523, 3, 'metro'),(1524, 3, 'adaptar'),(1525, 3, 'actividad'),(1526, 3, 'momento'),(1527, 3, 'transporte'),
(1528, 3, 'además'),(1529, 3, 'guardar'),(1530, 3, 'manera'),(1531, 3, 'práctica'),(1532, 3, 'garantía'),(1533, 3, 'límite'),(1534, 3, 'años'),(1535, 3, 'tiene'),
(1536, 3, 'piezas'),(1537, 3, 'obra'),(1538, 3, 'técnicas'),(1539, 3, 'tipo'),(1540, 3, 'sensibilidad'),(1541, 3, 'acústica'),(1542, 3, 'impedancia'),
(1543, 3, 'khz)'),(1544, 3, 'frecuencias'),(1545, 3, 'longitud'),(1546, 3, 'alargador'),(1547, 3, 'caja'),(1548, 3, 'altavoces'),(1549, 3, '(almohadillas'),
(1550, 3, 'sonoro,'),(1551, 3, 'transporte)'),(1552, 3, 'incorporated');

INSERT INTO `PREFIX_access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`) VALUES
(2, 1, 1, 1, 1, 1),
(2, 2, 1, 1, 1, 1),
(2, 3, 1, 1, 1, 1),
(2, 4, 0, 0, 0, 0),
(2, 5, 1, 1, 1, 1),
(2, 6, 0, 0, 0, 0),
(2, 7, 0, 0, 0, 0),
(2, 8, 0, 0, 0, 0),
(2, 9, 0, 0, 0, 0),
(2, 10, 0, 0, 0, 0),
(2, 11, 0, 0, 0, 0),
(2, 12, 1, 1, 1, 1),
(2, 13, 1, 1, 1, 1),
(2, 14, 0, 0, 0, 0),
(2, 15, 0, 0, 0, 0),
(2, 16, 0, 0, 0, 0),
(2, 17, 1, 1, 1, 1),
(2, 18, 0, 0, 0, 0),
(2, 19, 0, 0, 0, 0),
(2, 20, 1, 1, 1, 1),
(2, 21, 1, 1, 1, 1),
(2, 22, 0, 0, 0, 0),
(2, 23, 0, 0, 0, 0),
(2, 24, 0, 0, 0, 0),
(2, 26, 0, 0, 0, 0),
(2, 27, 0, 0, 0, 0),
(2, 28, 0, 0, 0, 0),
(2, 29, 0, 0, 0, 0),
(2, 30, 0, 0, 0, 0),
(2, 31, 0, 0, 0, 0),
(2, 32, 0, 0, 0, 0),
(2, 33, 0, 0, 0, 0),
(2, 34, 1, 1, 1, 1),
(2, 35, 0, 0, 0, 0),
(2, 36, 0, 0, 0, 0),
(2, 37, 0, 0, 0, 0),
(2, 38, 0, 0, 0, 0),
(2, 39, 0, 0, 0, 0),
(2, 40, 0, 0, 0, 0),
(2, 41, 0, 0, 0, 0),
(2, 42, 1, 1, 1, 1),
(2, 43, 0, 0, 0, 0),
(2, 44, 0, 0, 0, 0),
(2, 46, 0, 0, 0, 0),
(2, 47, 1, 1, 1, 1),
(2, 48, 0, 0, 0, 0),
(2, 49, 1, 1, 1, 1),
(2, 51, 0, 0, 0, 0),
(2, 52, 0, 0, 0, 0),
(2, 53, 0, 0, 0, 0),
(2, 54, 0, 0, 0, 0),
(2, 55, 1, 1, 1, 1),
(2, 56, 0, 0, 0, 0),
(2, 57, 0, 0, 0, 0),
(2, 58, 0, 0, 0, 0),
(2, 59, 1, 1, 1, 1),
(2, 60, 1, 1, 1, 1),
(2, 61, 0, 0, 0, 0),
(2, 62, 0, 0, 0, 0),
(2, 63, 0, 0, 0, 0),
(2, 64, 0, 0, 0, 0),
(2, 65, 0, 0, 0, 0),
(2, 66, 0, 0, 0, 0),
(2, 67, 0, 0, 0, 0),
(2, 68, 0, 0, 0, 0),
(2, 69, 0, 0, 0, 0),
(2, 70, 0, 0, 0, 0),
(2, 71, 0, 0, 0, 0),
(2, 72, 0, 0, 0, 0),
(2, 73, 1, 1, 1, 1),
(2, 80, 0, 0, 0, 0),
(2, 81, 0, 0, 0, 0),
(2, 82, 0, 0, 0, 0),
(2, 83, 0, 0, 0, 0),
(2, 84, 0, 0, 0, 0),
(2, 85, 0, 0, 0, 0),
(2, 86, 0, 0, 0, 0),
(2, 87, 0, 0, 0, 0),
(2, 88, 1, 1, 1, 1),
(3, 1, 1, 1, 1, 1),
(3, 2, 0, 0, 0, 0),
(3, 3, 0, 0, 0, 0),
(3, 4, 0, 0, 0, 0),
(3, 5, 0, 0, 0, 0),
(3, 6, 0, 0, 0, 0),
(3, 7, 0, 0, 0, 0),
(3, 8, 0, 0, 0, 0),
(3, 9, 1, 0, 0, 0),
(3, 10, 0, 0, 0, 0),
(3, 11, 0, 0, 0, 0),
(3, 12, 0, 0, 0, 0),
(3, 13, 0, 0, 0, 0),
(3, 14, 0, 0, 0, 0),
(3, 15, 0, 0, 0, 0),
(3, 16, 0, 0, 0, 0),
(3, 17, 0, 0, 0, 0),
(3, 18, 0, 0, 0, 0),
(3, 19, 0, 0, 0, 0),
(3, 20, 0, 0, 0, 0),
(3, 21, 0, 0, 0, 0),
(3, 22, 0, 0, 0, 0),
(3, 23, 0, 0, 0, 0),
(3, 24, 0, 0, 0, 0),
(3, 26, 0, 0, 0, 0),
(3, 27, 0, 0, 0, 0),
(3, 28, 0, 0, 0, 0),
(3, 29, 0, 0, 0, 0),
(3, 30, 0, 0, 0, 0),
(3, 31, 0, 0, 0, 0),
(3, 32, 1, 1, 1, 1),
(3, 33, 1, 1, 1, 1),
(3, 34, 0, 0, 0, 0),
(3, 35, 0, 0, 0, 0),
(3, 36, 0, 0, 0, 0),
(3, 37, 0, 0, 0, 0),
(3, 38, 0, 0, 0, 0),
(3, 39, 0, 0, 0, 0),
(3, 40, 0, 0, 0, 0),
(3, 41, 0, 0, 0, 0),
(3, 42, 0, 0, 0, 0),
(3, 43, 1, 0, 0, 0),
(3, 44, 0, 0, 0, 0),
(3, 46, 0, 0, 0, 0),
(3, 47, 0, 0, 0, 0),
(3, 48, 0, 0, 0, 0),
(3, 49, 0, 0, 0, 0),
(3, 51, 0, 0, 0, 0),
(3, 52, 0, 0, 0, 0),
(3, 53, 0, 0, 0, 0),
(3, 54, 0, 0, 0, 0),
(3, 55, 0, 0, 0, 0),
(3, 56, 0, 0, 0, 0),
(3, 57, 1, 1, 1, 1),
(3, 58, 0, 0, 0, 0),
(3, 59, 0, 0, 0, 0),
(3, 60, 0, 0, 0, 0),
(3, 61, 0, 0, 0, 0),
(3, 62, 0, 0, 0, 0),
(3, 63, 0, 0, 0, 0),
(3, 64, 0, 0, 0, 0),
(3, 65, 0, 0, 0, 0),
(3, 66, 0, 0, 0, 0),
(3, 67, 0, 0, 0, 0),
(3, 68, 0, 0, 0, 0),
(3, 69, 0, 0, 0, 0),
(3, 70, 0, 0, 0, 0),
(3, 71, 0, 0, 0, 0),
(3, 72, 0, 0, 0, 0),
(3, 73, 0, 0, 0, 0),
(3, 80, 0, 0, 0, 0),
(3, 81, 0, 0, 0, 0),
(3, 82, 0, 0, 0, 0),
(3, 83, 0, 0, 0, 0),
(3, 84, 0, 0, 0, 0),
(3, 85, 0, 0, 0, 0),
(3, 86, 0, 0, 0, 0),
(3, 87, 0, 0, 0, 0),
(3, 88, 1, 1, 1, 1),
(4, 1, 1, 1, 1, 1),
(4, 2, 1, 1, 1, 1),
(4, 3, 1, 1, 1, 1),
(4, 4, 0, 0, 0, 0),
(4, 5, 0, 0, 0, 0),
(4, 6, 1, 1, 1, 1),
(4, 7, 0, 0, 0, 0),
(4, 8, 0, 0, 0, 0),
(4, 9, 0, 0, 0, 0),
(4, 10, 1, 0, 0, 0),
(4, 11, 0, 0, 0, 0),
(4, 12, 1, 1, 1, 1),
(4, 13, 0, 0, 0, 0),
(4, 14, 0, 0, 0, 0),
(4, 15, 0, 0, 0, 0),
(4, 16, 0, 0, 0, 0),
(4, 17, 0, 0, 0, 0),
(4, 18, 0, 0, 0, 0),
(4, 19, 0, 0, 0, 0),
(4, 20, 0, 0, 0, 0),
(4, 21, 0, 0, 0, 0),
(4, 22, 0, 0, 0, 0),
(4, 23, 0, 0, 0, 0),
(4, 24, 0, 0, 0, 0),
(4, 26, 0, 0, 0, 0),
(4, 27, 0, 0, 0, 0),
(4, 28, 0, 0, 0, 0),
(4, 29, 0, 0, 0, 0),
(4, 30, 0, 0, 0, 0),
(4, 31, 0, 0, 0, 0),
(4, 32, 0, 0, 0, 0),
(4, 33, 0, 0, 0, 0),
(4, 34, 0, 0, 0, 0),
(4, 35, 0, 0, 0, 0),
(4, 36, 0, 0, 0, 0),
(4, 37, 0, 0, 0, 0),
(4, 38, 0, 0, 0, 0),
(4, 39, 0, 0, 0, 0),
(4, 40, 0, 0, 0, 0),
(4, 41, 0, 0, 0, 0),
(4, 42, 1, 1, 1, 1),
(4, 43, 1, 0, 0, 0),
(4, 44, 0, 0, 0, 0),
(4, 46, 0, 0, 0, 0),
(4, 47, 0, 0, 0, 0),
(4, 48, 0, 0, 0, 0),
(4, 49, 1, 1, 1, 1),
(4, 51, 0, 0, 0, 0),
(4, 52, 0, 0, 0, 0),
(4, 53, 0, 0, 0, 0),
(4, 54, 1, 1, 1, 1),
(4, 55, 0, 0, 0, 0),
(4, 56, 0, 0, 0, 0),
(4, 57, 0, 0, 0, 0),
(4, 58, 0, 0, 0, 0),
(4, 59, 1, 1, 1, 1),
(4, 60, 0, 0, 0, 0),
(4, 61, 0, 0, 0, 0),
(4, 62, 1, 1, 1, 1),
(4, 63, 1, 1, 1, 1),
(4, 64, 0, 0, 0, 0),
(4, 65, 1, 1, 1, 1),
(4, 66, 0, 0, 0, 0),
(4, 67, 0, 0, 0, 0),
(4, 68, 0, 0, 0, 0),
(4, 69, 0, 0, 0, 0),
(4, 70, 0, 0, 0, 0),
(4, 71, 0, 0, 0, 0),
(4, 72, 0, 0, 0, 0),
(4, 73, 0, 0, 0, 0),
(4, 80, 0, 0, 0, 0),
(4, 81, 0, 0, 0, 0),
(4, 82, 1, 1, 1, 1),
(4, 83, 0, 0, 0, 0),
(4, 84, 0, 0, 0, 0),
(4, 85, 0, 0, 0, 0),
(4, 86, 0, 0, 0, 0),
(4, 87, 0, 0, 0, 0),
(4, 88, 1, 1, 1, 1);

INSERT INTO `PREFIX_profile` (`id_profile`) VALUES (2),(3),(4);
INSERT INTO `PREFIX_profile_lang` (`id_lang`, `id_profile`, `name`) VALUES
(1, 2, 'Logistician'),(2, 2, 'Logisticien'),(3, 2, 'Logistician'),(4, 2, 'Logistiker'),(5, 2, 'Logista'),
(1, 3, 'Translator'),(2, 3, 'Traducteur'),(3, 3, 'Translator'),(4, 3, 'Übersetzer'),(5, 3, 'Traduttore'),
(1, 4, 'Salesman'),(2, 4, 'Commercial'),(3, 4, 'Salesman'),(4, 4, 'Verkäufer'),(5, 4, 'Venditore');

INSERT INTO `PREFIX_stock_mvt` (`id_stock_mvt`, `id_product`, `id_product_attribute`, `id_order`, `id_stock_mvt_reason`, `id_employee`, `quantity`, `date_add`, `date_upd`) VALUES
(1, 6, 0, 0, 2, 1, 250, NOW(), NOW()),(2, 8, 0, 0, 2, 1, 1, NOW(), NOW()),(3, 9, 0, 0, 2, 1, 1, NOW(), NOW()),(4, 2, 7, 0, 2, 1, 10, NOW(), NOW()),(5, 2, 8, 0, 2, 1, 20, NOW(), NOW()),(6, 2, 9, 0, 2, 1, 30, NOW(), NOW()),(7, 2, 10, 0, 2, 1, 40, NOW(), NOW()),(8, 5, 12, 0, 2, 1, 100, NOW(), NOW()),(9, 5, 13, 0, 2, 1, 99, NOW(), NOW()),(10, 5, 14, 0, 2, 1, 50, NOW(), NOW()),(11, 5, 15, 0, 2, 1, 25, NOW(), NOW()),(12, 7, 19, 0, 2, 1, 50, NOW(), NOW()),(13, 7, 22, 0, 2, 1, 60, NOW(), NOW()),(14, 7, 23, 0, 2, 1, 70, NOW(), NOW()),(15, 1, 25, 0, 2, 1, 50, NOW(), NOW()),(16, 1, 26, 0, 2, 1, 50, NOW(), NOW()),(17, 1, 27, 0, 2, 1, 50, NOW(), NOW()),(18, 1, 28, 0, 2, 1, 50, NOW(), NOW()),(19, 1, 29, 0, 2, 1, 50, NOW(), NOW()),(20, 1, 30, 0, 2, 1, 50, NOW(), NOW()),(21, 1, 31, 0, 2, 1, 50, NOW(), NOW()),(22, 1, 32, 0, 2, 1, 50, NOW(), NOW()),(23, 1, 33, 0, 2, 1, 50, NOW(), NOW()),(24, 1, 34, 0, 2, 1, 50, NOW(), NOW()),(25, 1, 35, 0, 2, 1, 50, NOW(), NOW()),(26, 1, 36, 0, 2, 1, 50, NOW(), NOW()),(27, 1, 39, 0, 2, 1, 50, NOW(), NOW()),(28, 1, 40, 0, 2, 1, 50, NOW(), NOW()),(29, 1, 41, 0, 2, 1, 50, NOW(), NOW()),(30, 1, 42, 0, 2, 1, 50, NOW(), NOW());

INSERT INTO `PREFIX_store` (`id_store`, `id_country`, `id_state`, `name`, `address1`, `address2`, `city`, `postcode`, `latitude`, `longitude`, `hours`, `phone`, `fax`, `email`, `note`, `active`, `date_add`, `date_upd`) VALUES
(1, 21, 9, 'Dade County', '3030 SW 8th St Miami', '', 'Miami', ' 33135', 25.765005, -80.243797, 'a:7:{i:0;s:13:"09:00 - 19:00";i:1;s:13:"09:00 - 19:00";i:2;s:13:"09:00 - 19:00";i:3;s:13:"09:00 - 19:00";i:4;s:13:"09:00 - 19:00";i:5;s:13:"10:00 - 16:00";i:6;s:13:"10:00 - 16:00";}', '', '', '', '', 1, '2010-11-09 10:53:13', '2010-11-09 10:53:13'),
(2, 21, 9, 'E Fort Lauderdale', '1000 Northeast 4th Ave Fort Lauderdale', '', 'miami', ' 33304', 26.137936, -80.139435, 'a:7:{i:0;s:13:"09:00 - 19:00";i:1;s:13:"09:00 - 19:00";i:2;s:13:"09:00 - 19:00";i:3;s:13:"09:00 - 19:00";i:4;s:13:"09:00 - 19:00";i:5;s:13:"10:00 - 16:00";i:6;s:13:"10:00 - 16:00";}', '', '', '', '', 1, '2010-11-09 10:56:26', '2010-11-09 10:56:26'),
(3, 21, 9, 'Pembroke Pines', '11001 Pines Blvd Pembroke Pines', '', 'miami', '33026', 26.009987, -80.294472, 'a:7:{i:0;s:13:"09:00 - 19:00";i:1;s:13:"09:00 - 19:00";i:2;s:13:"09:00 - 19:00";i:3;s:13:"09:00 - 19:00";i:4;s:13:"09:00 - 19:00";i:5;s:13:"10:00 - 16:00";i:6;s:13:"10:00 - 16:00";}', '', '', '', '', 1, '2010-11-09 10:58:42', '2010-11-09 11:01:11'),
(4, 21, 9, 'Coconut Grove', '2999 SW 32nd Avenue', '', ' Miami', ' 33133', 25.736296, -80.244797, 'a:7:{i:0;s:13:"09:00 - 19:00";i:1;s:13:"09:00 - 19:00";i:2;s:13:"09:00 - 19:00";i:3;s:13:"09:00 - 19:00";i:4;s:13:"09:00 - 19:00";i:5;s:13:"10:00 - 16:00";i:6;s:13:"10:00 - 16:00";}', '', '', '', '', 1, '2010-11-09 11:00:38', '2010-11-09 11:04:52'),
(5, 21, 9, 'N Miami/Biscayne', '12055 Biscayne Blvd', '', 'Miami', '33181', 25.886740, -80.163292, 'a:7:{i:0;s:13:"09:00 - 19:00";i:1;s:13:"09:00 - 19:00";i:2;s:13:"09:00 - 19:00";i:3;s:13:"09:00 - 19:00";i:4;s:13:"09:00 - 19:00";i:5;s:13:"10:00 - 16:00";i:6;s:13:"10:00 - 16:00";}', '', '', '', '', 1, '2010-11-09 11:11:28', '2010-11-09 11:11:28');

