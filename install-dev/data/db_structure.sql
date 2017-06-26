SET SESSION sql_mode = '';
SET NAMES 'utf8';

-- Can move to Entity, IDX_index created with doctrine..
CREATE TABLE `PREFIX_accessory` (
  `id_product_1` int(10) unsigned NOT NULL,
  `id_product_2` int(10) unsigned NOT NULL,
  KEY `accessory_product` (`id_product_1`,`id_product_2`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

-- No primary key in this table..
CREATE TABLE `PREFIX_access` (
  `id_profile` int(10) unsigned NOT NULL,
  `id_authorization_role` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_profile`,`id_authorization_role`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;
