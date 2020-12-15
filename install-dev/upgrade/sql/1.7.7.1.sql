/* PHP:ps_1771_update_customer_note(); */;
/* PHP:ps_1771_update_cart_reference_order(); */;

SET SESSION sql_mode='';
SET NAMES 'utf8';

UPDATE `PREFIX_currency` SET numeric_iso_code = LPAD(numeric_iso_code, 3, '0') WHERE LENGTH(numeric_iso_code) != 3;

/* add id_reference in cart */
ALTER TABLE `PREFIX_cart`
    ADD `reference_order` VARCHAR(9) NULL DEFAULT NULL,
    ADD INDEX (id_order);

/* update previous cart reference_order fields */
UPDATE `PREFIX_cart` c
LEFT JOIN `PREFIX_orders` o ON o.id_cart = c.id_cart
SET c.reference_order = o.reference
WHERE c.reference_order IS NULL;
