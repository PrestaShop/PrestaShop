SET NAMES 'utf8';


ALTER TABLE `PREFIX_order_state` ADD COLUMN `deleted` tinyint(1) UNSIGNED NOT NULL default '0' AFTER `paid`;

ALTER TABLE `PREFIX_category` ADD COLUMN `is_root_category` tinyint(1) NOT NULL default '0' AFTER `position`;

UPDATE `PREFIX_category` SET `is_root_category` = 1 WHERE `id_category` = 1;

ALTER TABLE `PREFIX_image_type` DROP `id_theme`;

ALTER TABLE `PREFIX_specific_price_rule_condition` CHANGE `id_specific_price_rule_condition` `id_specific_price_rule_condition` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `PREFIX_specific_price_rule_condition_group` CHANGE `id_specific_price_rule_condition_group` `id_specific_price_rule_condition_group` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;

UPDATE `PREFIX_supply_order_state_lang` 
SET `name` = "order canceled" 
WHERE `name` = "order fenced" AND (`id_lang` = 1 OR `id_lang` = 3 OR `id_lang` = 4 OR `id_lang` = 5);

UPDATE `PREFIX_supply_order_state_lang` 
SET `name` = "Commande cloturée" 
WHERE `name` = "order fenced" AND id_lang = 2;
