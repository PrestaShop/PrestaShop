SET SESSION sql_mode = '';
SET NAMES 'utf8';

/* cart rules without code did not use to work since the fix. Deactive them to avoid side-effects */
UPDATE `PREFIX_cart_rule` SET `active` = 0 WHERE `code` = '';
