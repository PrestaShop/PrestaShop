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