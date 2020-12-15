/* PHP:ps_1771_update_customer_note(); */;

UPDATE `PREFIX_currency` SET numeric_iso_code = LPAD(numeric_iso_code, 3, '0') WHERE LENGTH(numeric_iso_code) != 3;
