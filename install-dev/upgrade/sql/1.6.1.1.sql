SET NAMES 'utf8';

ALTER TABLE `PREFIX_customer_message` CHANGE `message` `message` MEDIUMTEXT NOT NULL;
UPDATE `PREFIX_tax_rules_group` SET `date_add` = NOW(), `date_upd` = NOW() WHERE `date_add` = '0000-00-00 00:00:00';

/* PHP:ps1611_fill_old_order_invoice_shop_addresses(); */;
