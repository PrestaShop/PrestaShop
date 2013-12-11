SET NAMES 'utf8';

INSERT INTO `PREFIX_hook_module` (`id_module`, `id_shop`, `id_hook`, `position`) (
	SELECT m.id_module, s.id_shop, h.id_hook, 0
	FROM `PREFIX_module` m, `PREFIX_shop` s, `PREFIX_hook` h
	WHERE m.name IN ('dashgoals', 'dashactivity', 'dashtrends', 'dashproducts')
	AND h.name IN ('actionAdminControllerSetMedia')
);

ALTER TABLE `PREFIX_customer` CHANGE `ape` `ape` VARCHAR( 14 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
ALTER TABLE `PREFIX_country` ADD `siret_code_format` VARCHAR( 14 ) NOT NULL ;
ALTER TABLE `PREFIX_country` ADD `ape_code_format` VARCHAR( 14 ) NOT NULL ;
