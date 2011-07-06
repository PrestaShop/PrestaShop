CREATE TABLE IF NOT EXISTS `PREFIX_fianet_fraud` (
		`id_cart` int(11) unsigned NOT NULL,
		`ip_address` int(11) NOT NULL,
		`date` datetime NOT NULL,
			KEY `id_cart_index` (`id_cart`),
			KEY `ip_address_index` (`ip_address`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_fianet_fraud_orders` (
		`id_order` int(11) NOT NULL,
		 `date_add` datetime NOT NULL,
			UNIQUE KEY `id_order` (`id_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
