SET SESSION sql_mode = '';
SET NAMES 'utf8';

ALTER TABLE `PREFIX_cms_lang`
	ADD `head_seo_title` varchar(128) DEFAULT NULL AFTER `meta_title`;
