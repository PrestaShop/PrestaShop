SET NAMES 'utf8';

CREATE TABLE IF NOT EXISTS `PREFIX_postcode` (
	`id_postcode` INT(10) NOT NULL AUTO_INCREMENT,
	`id_country` INT(10) NULL DEFAULT NULL,
	`id_zone` INT(10) NULL DEFAULT NULL,
	`postcode` VARCHAR(32) NOT NULL,
	`active` tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id_postcode`),
	KEY `id_country` (`id_country`),
	KEY `id_zone` (`id_zone`),
	KEY `active` (`active`),
	UNIQUE KEY `postcode` (`postcode`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8 COLLATION;

SET @id_parent = (SELECT `id_parent` FROM `PREFIX_tab` WHERE `class_name` = 'AdminStates' LIMIT 1);
SET @position = (SELECT `position`+1 FROM `PREFIX_tab` WHERE `id_parent` = @id_parent ORDER BY position DESC LIMIT 1);
/* PHP:add_new_tab(AdminPostcodes, en:Postcodes|it:CAP,  1); */;
UPDATE `PREFIX_tab` SET `id_parent` = @id_parent, `position` = @position WHERE `class_name` = 'AdminPostcodes' LIMIT 1;
