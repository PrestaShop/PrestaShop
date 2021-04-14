SET SESSION sql_mode='';
SET NAMES 'utf8mb4';

/* PHP:ps_1780_add_feature_flag_tab(); */;

/* this table should be created by Doctrine but we need to perform INSERT and the 1.7.8.0.sql script is called
before Doctrine schema update */
/* consequently we create the table manually */
CREATE TABLE IF NOT EXISTS `PREFIX_feature_flag` (
  `id_feature_flag` INT(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(191) COLLATE utf8mb4_general_ci NOT NULL,
  `state` TINYINT(1) NOT NULL DEFAULT '0',
  `label_wording` VARCHAR(191) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `label_domain` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `description_wording` VARCHAR(191) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `description_domain` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id_feature_flag`),
  UNIQUE KEY `UNIQ_91700F175E237E06` (`name`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `PREFIX_feature_flag` (`name`, `state`, `label_wording`, `label_domain`, `description_wording`, `description_domain`)
VALUES
	('product_page_v2', 0, 'Experimental product page', 'Admin.Advparameters.Feature', 'This page is a work in progress. It includes new combination management features and other features under development (virtual products, packs, etc.)', 'Admin.Advparameters.Help');

