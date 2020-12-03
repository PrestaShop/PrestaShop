/* PHP:ps_1771_update_customer_note(); */;

UPDATE `PREFIX_currency` SET numeric_iso_code = CONCAT('0', numeric_iso_code) WHERE LENGTH(numeric_iso_code) = 2;
