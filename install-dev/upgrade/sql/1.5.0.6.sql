SET NAMES 'utf8';

UPDATE `PREFIX_orders` o
SET o.`current_state` = (
	SELECT oh.`id_order_state`
	FROM `PREFIX_order_history` oh
	WHERE oh.`id_order` = o.`id_order`
	ORDER BY oh.`date_add` DESC
	LIMIT 1
);

ALTER TABLE `PREFIX_cart_rule` ADD `gift_product_attribute` int(10) unsigned NOT NULL default 0 AFTER `gift_product`;

UPDATE `PREFIX_product` set is_virtual = 1 WHERE id_product IN (SELECT id_product FROM `PREFIX_product_download` WHERE active = 1);
