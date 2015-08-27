SET NAMES 'utf8';

UPDATE `PREFIX_image_shop` ish, `PREFIX_image` i SET ish.id_product = i.id_product WHERE i.id_image=ish.id_image;

REPLACE INTO `PREFIX_tag_count` (id_group, id_tag, id_lang, id_shop, counter)
			SELECT cg.id_group, t.id_tag, t.id_lang, ps.id_shop, COUNT(pt.id_tag) AS times
				FROM `PREFIX_product_tag` pt
				LEFT JOIN `PREFIX_tag` t ON (t.id_tag = pt.id_tag AND t.id_lang = pt.id_lang)
				LEFT JOIN `PREFIX_product` p ON (p.id_product = pt.id_product)
				INNER JOIN `PREFIX_product_shop` product_shop
					ON (product_shop.id_product = p.id_product)
				JOIN (SELECT DISTINCT id_group FROM `PREFIX_category_group`) cg
				JOIN (SELECT DISTINCT id_shop FROM `PREFIX_shop`) ps
				WHERE product_shop.`active` = 1
				AND EXISTS(SELECT 1 FROM `PREFIX_category_product` cp
								LEFT JOIN `PREFIX_category_group` cgo ON (cp.`id_category` = cgo.`id_category`)
								WHERE cgo.`id_group` = cg.id_group AND p.`id_product` = cp.`id_product`)
				AND product_shop.id_shop = ps.id_shop
				GROUP BY pt.id_tag, pt.id_lang, cg.id_group, ps.id_shop;
REPLACE INTO `PREFIX_tag_count` (id_group, id_tag, id_lang, id_shop, counter)
			SELECT 0, t.id_tag, t.id_lang, ps.id_shop, COUNT(pt.id_tag) AS times
				FROM `PREFIX_product_tag` pt
				LEFT JOIN `PREFIX_tag` t ON (t.id_tag = pt.id_tag AND t.id_lang = pt.id_lang)
				LEFT JOIN `PREFIX_product` p ON (p.id_product = pt.id_product)
				INNER JOIN `PREFIX_product_shop` product_shop
					ON (product_shop.id_product = p.id_product)
				JOIN (SELECT DISTINCT id_shop FROM `PREFIX_shop`) ps
				WHERE product_shop.`active` = 1
				AND product_shop.id_shop = ps.id_shop
				GROUP BY pt.id_tag, pt.id_lang, ps.id_shop;

TRUNCATE TABLE `PREFIX_smarty_last_flush`;