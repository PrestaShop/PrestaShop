CREATE TABLE IF NOT EXISTS `PREFIX_dejala_cart` (
  `id_cart` int(10) unsigned NOT NULL,
  `id_dejala_product` int(10) unsigned NOT NULL,
  `shipping_date` int(11) NULL DEFAULT NULL, 
  `id_delivery` int(11) NULL DEFAULT NULL,
  `mode` varchar(5) NULL DEFAULT 'TEST',
  `cart_date_upd` datetime DEFAULT '0000-00-00 00:00:00',
  `delivery_price` float DEFAULT NULL,
  PRIMARY KEY (`id_cart`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
