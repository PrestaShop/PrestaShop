SET NAMES 'utf8';

UPDATE `PREFIX_configuration` c SET `value` = '{"avoid":[]}' WHERE `name` IN ('PS_INVCE_INVOICE_ADDR_RULES', 'PS_INVCE_DELIVERY_ADDR_RULES');