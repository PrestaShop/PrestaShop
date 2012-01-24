SET NAMES 'utf8';

/* PHP:add_column_order_state_deleted_if_not_exists(); */;

ALTER TABLE `PREFIX_category` ADD COLUMN `is_root_category` tinyint(1) NOT NULL default '0' AFTER `position`;

UPDATE `PREFIX_category` SET `is_root_category` = 1 WHERE `id_category` = 1;

ALTER TABLE `PREFIX_image_type` DROP `id_theme`;

ALTER TABLE `PREFIX_specific_price_rule_condition` CHANGE `id_specific_price_rule_condition` `id_specific_price_rule_condition` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `PREFIX_specific_price_rule_condition_group` CHANGE `id_specific_price_rule_condition_group` `id_specific_price_rule_condition_group` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `PREFIX_category_shop` (
  `id_category` int(11) NOT NULL,
  `id_shop` int(11) NOT NULL,
  PRIMARY KEY (`id_category`, `id_shop`),
  UNIQUE KEY `id_category_shop` (`id_category`,`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;


/* PHP:generate_root_category_for_multishop(); */;


CREATE TABLE `PREFIX_module_preference` (
  `id_module_preference` int(11) NOT NULL auto_increment,
  `id_employee` int(11) NOT NULL,
  `module` varchar(255) NOT NULL,
  `interest` tinyint(1) default NULL,
  `favorite` tinyint(1) default NULL,
  PRIMARY KEY (`id_module_preference`),
  UNIQUE KEY `employee_module` (`id_employee`, `module`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;
