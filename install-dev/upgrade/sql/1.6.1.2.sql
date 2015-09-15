SET NAMES 'utf8';

UPDATE `PREFIX_image_shop` ish, `PREFIX_image` i SET ish.id_product = i.id_product WHERE i.id_image=ish.id_image;

REPLACE INTO `PREFIX_tag_count` (id_group, id_tag, id_lang, id_shop, counter)
			SELECT cg.id_group, pt.id_tag, pt.id_lang, id_shop, COUNT(pt.id_tag) AS times
				FROM `PREFIX_product_tag` pt
				INNER JOIN `PREFIX_product_shop` product_shop
					USING (id_product)
				JOIN (SELECT DISTINCT id_group FROM `PREFIX_category_group`) cg
				WHERE product_shop.`active` = 1
				AND EXISTS(SELECT 1 FROM `PREFIX_category_product` cp
								LEFT JOIN `PREFIX_category_group` cgo ON (cp.`id_category` = cgo.`id_category`)
								WHERE cgo.`id_group` = cg.id_group AND product_shop.`id_product` = cp.`id_product`)
				GROUP BY pt.id_tag, pt.id_lang, cg.id_group, id_shop ORDER BY NULL;
REPLACE INTO `PREFIX_tag_count` (id_group, id_tag, id_lang, id_shop, counter)
			SELECT 0, pt.id_tag, pt.id_lang, id_shop, COUNT(pt.id_tag) AS times
				FROM `PREFIX_product_tag` pt
				INNER JOIN `PREFIX_product_shop` product_shop
					USING (id_product)
				WHERE product_shop.`active` = 1
				GROUP BY pt.id_tag, pt.id_lang, id_shop ORDER BY NULL;

TRUNCATE TABLE `PREFIX_smarty_last_flush`;