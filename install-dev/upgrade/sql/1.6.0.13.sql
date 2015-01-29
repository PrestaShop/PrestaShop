SET NAMES 'utf8';

UPDATE `PREFIX_configuration` SET `value` = CONCAT('#', `value`) WHERE `name` LIKE 'PS_%_PREFIX';

UPDATE `PREFIX_configuration_lang` SET `value` = CONCAT('#', `value`) WHERE `id_configuration` IN (SELECT `id_configuration` FROM `PREFIX_configuration` WHERE `name` LIKE 'PS_%_PREFIX');


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
ALTER TABLE PREFIX_product_attribute_shop ADD `id_product` int(10) unsigned NOT NULL, ADD KEY `id_product` (`id_product`, `id_shop`, `default_on`);
UPDATE PREFIX_product_attribute_shop, PREFIX_product_attribute
	SET PREFIX_product_attribute_shop.id_product=PREFIX_product_attribute.id_product
	WHERE PREFIX_product_attribute_shop.id_product_attribute=PREFIX_product_attribute.id_product_attribute;
ALTER TABLE PREFIX_image_shop ADD `id_product` int(10) unsigned NOT NULL, ADD KEY `id_product` (`id_product`, `id_shop`, `cover`);
UPDATE PREFIX_image_shop, PREFIX_image
	SET PREFIX_image_shop.id_product=PREFIX_image.id_product
	WHERE PREFIX_image_shop.id_image=PREFIX_image.id_image;
ALTER IGNORE TABLE `PREFIX_image_shop` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_image`, `id_shop`);
ALTER TABLE `PREFIX_product_supplier` ADD KEY `id_supplier` (`id_supplier`,`id_product`);
ALTER TABLE `PREFIX_product` DROP KEY `product_manufacturer`, ADD KEY `product_manufacturer` (`id_manufacturer`, `id_product`);
