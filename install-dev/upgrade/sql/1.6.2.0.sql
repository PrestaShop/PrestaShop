SET NAMES 'utf8';

/* Password reset token for new "Forgot my password screen */
CREATE TABLE IF NOT EXISTS `PREFIX_reset_token` (
  `id_reset_token` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_customer` int(10) unsigned NULL DEFAULT 0,
  `secure_key` varchar(32) DEFAULT NULL,
  `id_employee` int(10) unsigned NULL DEFAULT 0,
  `unique_token` varchar(40) DEFAULT NULL,
  `last_token_gen` datetime NOT NULL,
  `validity_date` datetime NOT NULL,
  PRIMARY KEY (`id_reset_token`),
  KEY (`id_customer`),
  KEY (`id_customer`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
