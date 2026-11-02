SET SESSION sql_mode='';
SET NAMES 'utf8mb4';

CREATE TABLE IF NOT EXISTS `PREFIX_module_trade_credit` (
  `id_module` int(10) unsigned NOT NULL,
  `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_module`, `id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4 COLLATION;

CREATE TABLE IF NOT EXISTS `PREFIX_customer_trade_credit` (
  `id_customer` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(20, 6) NOT NULL,
  `date_add` DATETIME NOT NULL,
  `date_upd` DATETIME NOT NULL,
  PRIMARY KEY (`id_customer`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4 COLLATION;

CREATE TABLE IF NOT EXISTS `PREFIX_customer_trade_credit_operation` (
  `id_customer_trade_credit_operation` INT UNSIGNED AUTO_INCREMENT NOT NULL,
  `id_customer` INT UNSIGNED NOT NULL,
  `type` VARCHAR(16) NOT NULL,
  `amount` DECIMAL(20, 6) NOT NULL,
  `balance_after` DECIMAL(20, 6) NOT NULL,
  `id_cart` INT UNSIGNED DEFAULT NULL,
  `id_order` INT UNSIGNED DEFAULT NULL,
  `id_order_slip` INT UNSIGNED DEFAULT NULL,
  `id_employee` INT UNSIGNED DEFAULT NULL,
  `reference` VARCHAR(64) NOT NULL,
  `date_add` DATETIME NOT NULL,
  UNIQUE INDEX `reference` (`reference`),
  INDEX `id_customer` (`id_customer`),
  INDEX `id_cart` (`id_cart`),
  INDEX `id_order` (`id_order`),
  PRIMARY KEY (`id_customer_trade_credit_operation`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4 COLLATION;

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`)
SELECT 'PS_TRADE_CREDIT_DEFAULT_AMOUNT', '50000', NOW(), NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `PREFIX_configuration` WHERE `name` = 'PS_TRADE_CREDIT_DEFAULT_AMOUNT');

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`)
SELECT 'PS_TRADE_CREDIT_CURRENCY', 'PLN', NOW(), NOW() FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `PREFIX_configuration` WHERE `name` = 'PS_TRADE_CREDIT_CURRENCY');
