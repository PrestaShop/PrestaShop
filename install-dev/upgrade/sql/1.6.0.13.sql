SET NAMES 'utf8';

UPDATE `PREFIX_configuration` SET `value` = CONCAT('#', `value`) WHERE `name` LIKE 'PS_%_PREFIX';

UPDATE `PREFIX_configuration_lang` SET `value` = CONCAT('#', `value`) WHERE `id_configuration` IN (SELECT `id_configuration` FROM `PREFIX_configuration` WHERE `name` LIKE 'PS_%_PREFIX');

ALTER TABLE PREFIX_product_tag ADD `id_lang` int(10) unsigned NOT NULL, ADD KEY (id_lang, id_tag);
UPDATE PREFIX_product_tag, PREFIX_tag SET PREFIX_product_tag.id_lang=PREFIX_tag.id_lang WHERE PREFIX_tag.id_tag=PREFIX_product_tag.id_tag;
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