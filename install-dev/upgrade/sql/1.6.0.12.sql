SET NAMES 'utf8';

INSERT INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES ('PS_CUSTOMER_NWSL', 1, NOW(), NOW()), ('PS_CUSTOMER_OPTIN', 1, NOW(), NOW());

INSERT INTO `PREFIX_order_state` (`invoice`, `send_email`, `module_name`, `color`, `unremovable`, `hidden`, `logable`, `delivery`, `shipped`, `paid`, `deleted`) VALUES ('0', '0', 'cashondelivery', '#4169E1', '1', '0', '0', '0', '0', '0', '0');
SET @id_order_state = LAST_INSERT_ID();
INSERT INTO `PREFIX_order_state_lang` (`id_order_state`, `id_lang`, `name`, `template`) (SELECT @id_order_state, id_lang, 'Waiting cod validation', '' FROM `PREFIX_lang`);

INSERT IGNORE INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_OS_COD_VALIDATION', @id_order_state, NOW(), NOW());

UPDATE `PREFIX_hook` set `position` = 1 where `name` in ('displayBanner', 'displayNav', 'displayTopColumn');

ALTER TABLE `PREFIX_product` ADD `pack_stock_type` int(11) UNSIGNED DEFAULT '3';
ALTER TABLE `PREFIX_product_shop` ADD `pack_stock_type` int(11) UNSIGNED DEFAULT '3';
ALTER TABLE `PREFIX_pack` ADD `id_product_attribute_item` int(10) UNSIGNED NOT NULL AFTER `id_product_item`;
ALTER TABLE `PREFIX_pack` DROP PRIMARY KEY;

/* PHP:p16012_pack_rework(); */;

ALTER TABLE `PREFIX_pack` ADD PRIMARY KEY (`id_product_pack`, `id_product_item`, `id_product_attribute_item`);

ALTER TABLE `PREFIX_order_state` ADD `pdf_delivery` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `paid`;
ALTER TABLE `PREFIX_order_state` ADD `pdf_invoice` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `pdf_delivery`;

ALTER TABLE `PREFIX_orders` CHANGE `shipping_number` `shipping_number` VARCHAR( 64 );

/* PHP:ps16012_update_alias(); */;

ALTER TABLE `PREFIX_store` CHANGE `hours` `hours` TEXT;

ALTER TABLE `PREFIX_cms_lang` ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_lang`;
ALTER TABLE `PREFIX_cms_lang` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_cms`, `id_shop`, `id_lang`);

ALTER TABLE `PREFIX_cms_category_lang` ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_lang`;
ALTER TABLE `PREFIX_cms_category_lang` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_cms_category`, `id_shop`, `id_lang`);

CREATE TABLE `PREFIX_cms_category_shop` (
	`id_cms_category` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`id_shop` INT(11) UNSIGNED NOT NULL ,
	PRIMARY KEY (`id_cms_category`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

/* PHP:cms_multishop(); */;

ALTER TABLE `PREFIX_customization_field_lang` ADD `id_shop` INT(10) UNSIGNED NOT NULL DEFAULT '1' AFTER `id_lang`;
ALTER TABLE `PREFIX_customization_field_lang` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_customization_field`, `id_shop`, `id_lang`);

/* PHP:customization_field_multishop_lang(); */;

ALTER TABLE `PREFIX_product` CHANGE `available_date` `available_date` DATE NOT NULL DEFAULT '0000-00-00';
ALTER TABLE `PREFIX_product_shop` CHANGE `available_date` `available_date` DATE NOT NULL DEFAULT '0000-00-00';
ALTER TABLE `PREFIX_product_attribute` CHANGE `available_date` `available_date` DATE NOT NULL DEFAULT '0000-00-00';
ALTER TABLE `PREFIX_product_attribute_shop` CHANGE `available_date` `available_date` DATE NOT NULL DEFAULT '0000-00-00';

ALTER TABLE `PREFIX_module_access` CHANGE `view` `view` TINYINT( 1 ) NOT NULL DEFAULT '0',
CHANGE `configure` `configure` TINYINT( 1 ) NOT NULL DEFAULT '0',
CHANGE `uninstall` `uninstall` TINYINT( 1 ) NOT NULL DEFAULT '0';

ALTER TABLE `PREFIX_shop_group` ADD KEY `deleted` (`deleted`, `name`);
ALTER TABLE `PREFIX_shop` DROP KEY `id_shop_group`;
ALTER TABLE `PREFIX_shop` DROP KEY `id_group_shop`;
ALTER TABLE `PREFIX_shop` ADD KEY `id_shop_group` (`id_shop_group`, `deleted`);
ALTER TABLE `PREFIX_shop_url` DROP KEY `id_shop`;
ALTER TABLE `PREFIX_shop_url` ADD KEY `id_shop` (`id_shop`, `main`);
ALTER TABLE `PREFIX_customization` ADD KEY `id_cart` (`id_cart`);
ALTER TABLE `PREFIX_product_sale` ADD KEY `quantity` (`quantity`);
ALTER TABLE `PREFIX_cart_rule` ADD KEY `id_customer` (`id_customer`, `active`, `date_to`);
ALTER TABLE `PREFIX_cart_rule` ADD KEY `group_restriction` (`group_restriction`, `active`, `date_to`);
ALTER TABLE `PREFIX_hook_module` ADD KEY `position` (`id_shop`, `position`);
ALTER TABLE `PREFIX_cart_product` DROP KEY `cart_product_index`;
ALTER IGNORE TABLE `PREFIX_cart_product` ADD PRIMARY KEY (`id_cart`,`id_product`,`id_product_attribute`,`id_address_delivery`);
ALTER TABLE `PREFIX_cart_product` ADD KEY `id_cart_order` (`id_cart`, `date_add`, `id_product`, `id_product_attribute`);
ALTER TABLE `PREFIX_customization` DROP KEY id_cart;
ALTER IGNORE TABLE `PREFIX_customization` ADD UNIQUE `id_cart_product` (`id_cart`, `id_product`, `id_product_attribute`);
ALTER TABLE `PREFIX_category` DROP KEY nleftright, DROP KEY nleft;
ALTER TABLE `PREFIX_category` ADD KEY `activenleft` (`active`,`nleft`), ADD KEY `activenright` (`active`,`nright`);
ALTER IGNORE TABLE `PREFIX_image_shop` DROP KEY `id_image`, ADD PRIMARY KEY (`id_image`, `id_shop`, `cover`);
ALTER TABLE PREFIX_product_tag ADD `id_lang` int(10) unsigned NOT NULL, ADD KEY (id_lang, id_tag);
UPDATE PREFIX_product_tag, PREFIX_tag SET PREFIX_product_tag.id_lang=PREFIX_tag.id_lang WHERE PREFIX_tag.id_tag=PREFIX_product_tag.id_tag;
ALTER TABLE PREFIX_product_attribute_shop ADD `id_product` int(10) unsigned NOT NULL, ADD KEY `id_product` (`id_product`, `id_shop`, `default_on`);
UPDATE PREFIX_product_attribute_shop, PREFIX_product_attribute
	SET PREFIX_product_attribute_shop.id_product=PREFIX_product_attribute.id_product
	WHERE PREFIX_product_attribute_shop.id_product_attribute=PREFIX_product_attribute.id_product_attribute;
ALTER TABLE PREFIX_image_shop ADD `id_product` int(10) unsigned NOT NULL, ADD KEY `id_product` (`id_product`, `id_shop`, `cover`);
UPDATE PREFIX_image_shop, PREFIX_image
	SET PREFIX_image_shop.id_product=PREFIX_image.id_product
	WHERE PREFIX_image_shop.id_image=PREFIX_image.id_image;
ALTER IGNORE TABLE `PREFIX_image_shop` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_image`, `id_shop`);
CREATE TABLE `PREFIX_tag_count` (
  `id_group` int(10) unsigned NOT NULL DEFAULT 0,
  `id_tag` int(10) unsigned NOT NULL DEFAULT 0,
  `id_lang` int(10) unsigned NOT NULL DEFAULT 0,
  `id_shop` int(11) unsigned NOT NULL DEFAULT 0,
  `counter` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_group`, `id_tag`),
  KEY (`id_group`, `id_lang`, `id_shop`, `counter`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
REPLACE INTO `PREFIX_tag_count` (id_group, id_tag, id_lang, id_shop, counter)
SELECT cg.id_group, t.id_tag, t.id_lang, ps.id_shop, COUNT(pt.id_tag) AS times
	FROM `PREFIX_product_tag` pt
	LEFT JOIN `PREFIX_tag` t ON (t.id_tag = pt.id_tag)
	LEFT JOIN `PREFIX_product` p ON (p.id_product = pt.id_product)
	INNER JOIN `PREFIX_product_shop` product_shop
		ON (product_shop.id_product = p.id_product)
	JOIN (SELECT DISTINCT id_group FROM `PREFIX_category_group`) cg
	JOIN (SELECT DISTINCT id_shop FROM `PREFIX_shop`) ps
	WHERE pt.`id_lang` = 1 AND product_shop.`active` = 1
	AND p.`id_product` IN (SELECT DISTINCT cp.`id_product` FROM `PREFIX_category_product` cp
													LEFT JOIN `PREFIX_category_group` cgo ON (cp.`id_category` = cgo.`id_category`)
													WHERE cgo.`id_group` = cg.id_group)
	AND product_shop.id_shop = ps.id_shop
	GROUP BY pt.id_tag, cg.id_group;
REPLACE INTO `PREFIX_tag_count` (id_group, id_tag, id_lang, id_shop, counter)
SELECT 0, t.id_tag, t.id_lang, ps.id_shop, COUNT(pt.id_tag) AS times
	FROM `PREFIX_product_tag` pt
	LEFT JOIN `PREFIX_tag` t ON (t.id_tag = pt.id_tag)
	LEFT JOIN `PREFIX_product` p ON (p.id_product = pt.id_product)
	INNER JOIN `PREFIX_product_shop` product_shop
		ON (product_shop.id_product = p.id_product)
	JOIN (SELECT DISTINCT id_shop FROM `PREFIX_shop`) ps
	WHERE pt.`id_lang` = 1 AND product_shop.`active` = 1
	AND product_shop.id_shop = ps.id_shop
	GROUP BY pt.id_tag;
	ALTER TABLE `PREFIX_product_supplier` ADD KEY `id_supplier` (`id_supplier`,`id_product`);
	ALTER TABLE `PREFIX_product` DROP KEY `product_manufacturer`, ADD KEY `product_manufacturer` (`id_manufacturer`, `id_product`);
