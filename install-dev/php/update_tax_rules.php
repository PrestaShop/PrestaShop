<?php


function update_tax_rules()
{
	// Add new columns
	Db::getInstance()->Execute('
	ALTER TABLE `'._DB_PREFIX_.'tax_rule`
		ADD `zipcode_from` INT NOT NULL AFTER `id_state` ,
		ADD `zipcode_to` INT NOT NULL AFTER `zipcode_from` ,
		ADD `behavior` INT NOT NULL AFTER `zipcode_to`,
		ADD `description` VARCHAR( 100 ) NOT NULL AFTER `id_tax`;
	');

	// Drop integrity constraint
	Db::getInstance()->Execute('
	ALTER TABLE `'._DB_PREFIX_.'tax_rule` DROP INDEX tax_rule
	');

	// Create new format rules
	Db::getInstance()->Execute('
	INSERT INTO `'._DB_PREFIX_.'tax_rule` (`id_tax_rules_group`, `id_country`, `id_state`, `id_tax`, `behavior`, `zipcode_from`, `zipcode_to`)
	SELECT r.`id_tax_rules_group`, r.`id_country`, r.`id_state`, r.`id_tax`, 0, z.`from_zip_code`, z.`to_zip_code`
	FROM `'._DB_PREFIX_.'tax_rule` r INNER JOIN `'._DB_PREFIX_.'county_zip_code` z ON (z.`id_county` = r.`id_county`)
	');

	// update behavior
	Db::getInstance()->Execute('
	UPDATE `'._DB_PREFIX_.'tax_rule` SET `behavior` = GREATEST(`state_behavior`, `county_behavior`);
	');


	// Clean old entries
	Db::getInstance()->Execute('
	DELETE FROM `'._DB_PREFIX_.'tax_rule`
	WHERE `id_county` != 0
	AND `zipcode_from` = 0
	');

	// Remove old columns
	Db::getInstance()->Execute('
	ALTER TABLE `'._DB_PREFIX_.'tax_rule`
	  DROP `id_county`,
	  DROP `state_behavior`,
	  DROP `county_behavior`
  ');
}

