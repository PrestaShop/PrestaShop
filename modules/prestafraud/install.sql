CREATE TABLE IF NOT EXISTS `PREFIX_prestafraud_carrier`
(
	`id_carrier` int(11) default NULL,
	`id_prestafraud_carrier_type` int(11) default NULL,
	PRIMARY KEY  (`id_carrier`, `id_prestafraud_carrier_type`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_prestafraud_payment`
(
	`id_module` int(11) default NULL,
	`id_prestafraud_payment_type` int(11) default NULL,
	PRIMARY KEY  (`id_module`, `id_prestafraud_payment_type`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_prestafraud_carts`
(
	`id_cart` int(11) default NULL,
	`ip_address` int(11) default NULL,
	`date` DATETIME NOT NULL,
	PRIMARY KEY  (`id_cart`, `ip_address`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_prestafraud_orders`
(
	`id_order` int(11) default NULL,
	`scoring` decimal(2,2) default NULL,
	`comment` VARCHAR(255) default NULL,
	PRIMARY KEY  (`id_order`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
