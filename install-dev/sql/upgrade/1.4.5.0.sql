SET NAMES 'utf8';

INSERT IGNORE INTO `PREFIX_configuration` (`name`, `value`, `date_add`, `date_upd`) VALUES
('PS_RESTRICT_DELIVERED_COUNTRIES', '0', NOW(), NOW());

UPDATE `PREFIX_country_lang`
SET `name` = 'United States'
WHERE `name` = 'United State'
AND `id_lang` = (
	SELECT `id_lang`
	FROM `PREFIX_lang`
	WHERE `iso_code` = 'en'
	LIMIT 1
);

ALTER TABLE `PREFIX_discount` ADD `include_tax` TINYINT(1) NOT NULL DEFAULT '0';

UPDATE `PREFIX_order_detail` SET `product_price` = `product_price` /( 1-(`group_reduction`/100));

DELETE FROM `PREFIX_configuration` WHERE name IN ('PS_LAYERED_BITLY_USERNAME', 'PS_LAYERED_BITLY_API_KEY', 'PS_LAYERED_SHARE') LIMIT 3;

INSERT INTO `PREFIX_hook` (`name`, `title`, `description`, `position`, `live_edit`) VALUES
('attributeGroupForm', 'Add fields to the form "attribute group"', 'Add fields to the form "attribute group"', 0, 0),
('afterSaveAttributeGroup', 'On saving attribute group', 'On saving attribute group', 0, 0),
('afterDeleteAttributeGroup', 'On deleting attribute group', 'On deleting "attribute group', 0, 0),
('featureForm', 'Add fileds to the form "feature"', 'Add fileds to the form "feature"', 0, 0),
('afterSaveFeature', 'On saving attribute feature', 'On saving attribute feature', 0, 0),
('afterDeleteFeature', 'On deleting attribute feature', 'On deleting attribute feature', 0, 0),
('afterSaveProduct', 'On saving products', 'On saving products', 0, 0),
('productListAssign', 'Assign product list to a category', 'Assign product list to a category', 0, 0);

