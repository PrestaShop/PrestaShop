SET SESSION sql_mode = '';
SET NAMES 'utf8';

INSERT INTO `PREFIX_category_lang` SELECT
`entity`.`id_category`, 1, `entity`.`id_lang`, `entity`.`name`, `entity`.`description`, `entity`.`link_rewrite`, `entity`.`meta_title`, `entity`.`meta_keywords`, `entity`.`meta_description`
FROM `PREFIX_category_lang` entity
LEFT JOIN `PREFIX_category_lang` entity2 ON `entity2`.`id_shop` = 1 AND `entity`.`id_category` = `entity2`.`id_category`
WHERE `entity2`.`id_shop` IS NULL;

INSERT INTO `PREFIX_cms_category_lang` SELECT
`entity`.`id_cms_category`, `entity`.`id_lang`, 1, `entity`.`name`, `entity`.`description`, `entity`.`link_rewrite`, `entity`.`meta_title`, `entity`.`meta_keywords`, `entity`.`meta_description`
FROM `PREFIX_cms_category_lang` entity
LEFT JOIN `PREFIX_cms_category_lang` entity2 ON `entity2`.`id_shop` = 1 AND `entity`.`id_cms_category` = `entity2`.`id_cms_category`
WHERE `entity2`.`id_shop` IS NULL;

INSERT INTO `PREFIX_cms_lang` SELECT
`entity`.`id_cms`, `entity`.`id_lang`, 1, `entity`.`meta_title`, `entity`.`head_seo_title`, `entity`.`meta_description`, `entity`.`meta_keywords`, `entity`.`content`, `entity`.`link_rewrite`
FROM `PREFIX_cms_lang` entity
LEFT JOIN `PREFIX_cms_lang` entity2 ON `entity2`.`id_shop` = 1 AND `entity`.`id_cms` = `entity2`.`id_cms`
WHERE `entity2`.`id_shop` IS NULL;

INSERT INTO `PREFIX_cms_role_lang` SELECT
`entity`.`id_cms_role`, `entity`.`id_lang`, 1, `entity`.`name`
FROM `PREFIX_cms_role_lang` entity
LEFT JOIN `PREFIX_cms_role_lang` entity2 ON `entity2`.`id_shop` = 1 AND `entity`.`id_cms_role` = `entity2`.`id_cms_role`
WHERE `entity2`.`id_shop` IS NULL;

INSERT INTO `PREFIX_customization_field_lang` SELECT
`entity`.`id_customization_field`, `entity`.`id_lang`, 1, `entity`.`name`
FROM `PREFIX_customization_field_lang` entity
LEFT JOIN `PREFIX_customization_field_lang` entity2 ON `entity2`.`id_shop` = 1 AND `entity`.`id_customization_field` = `entity2`.`id_customization_field`
WHERE `entity2`.`id_shop` IS NULL;

INSERT INTO `PREFIX_info_lang` SELECT
`entity`.`id_info`, 1, `entity`.`id_lang`, `entity`.`text`
FROM `PREFIX_info_lang` entity
LEFT JOIN `PREFIX_info_lang` entity2 ON `entity2`.`id_shop` = 1 AND `entity`.`id_info` = `entity2`.`id_info`
WHERE `entity2`.`id_shop` IS NULL;

INSERT INTO `PREFIX_meta_lang` SELECT
`entity`.`id_meta`, 1, `entity`.`id_lang`, `entity`.title, `entity`.`description`, `entity`.`keywords`, `entity`.`url_rewrite`
FROM `PREFIX_meta_lang` entity
LEFT JOIN `PREFIX_meta_lang` entity2 ON `entity2`.`id_shop` = 1 AND `entity`.`id_meta` = `entity2`.`id_meta`
WHERE `entity2`.`id_shop` IS NULL;

INSERT INTO `PREFIX_product_lang` SELECT
`entity`.`id_product`, 1, `entity`.`id_lang`, `entity`.`description`, `entity`.`description_short`, `entity`.`link_rewrite`, `entity`.`meta_description`, `entity`.`meta_keywords`, `entity`.`meta_title`, `entity`.`name`, `entity`.`available_now`, `entity`.`available_later`, `entity`.`delivery_in_stock`, `entity`.`delivery_out_stock`
FROM `PREFIX_product_lang` entity
LEFT JOIN `PREFIX_product_lang` entity2 ON `entity2`.`id_shop` = 1 AND `entity`.`id_product` = `entity2`.`id_product`
WHERE `entity2`.`id_shop` IS NULL;
