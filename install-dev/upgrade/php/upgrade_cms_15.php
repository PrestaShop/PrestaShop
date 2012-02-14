<?php
function upgrade_cms_15()
{
	$res = true;
	$id_module_cms = Db::getInstance()->getValue('SELECT id_module from `'._DB_PREFIX_.'_module where name="blockcms"`');
	if (!$id_module_cms)
		return true;

	$res &= Db::getInstance()->execute('CREATE TABLE `'._DB_PREFIX_.'_cms_shop` (
`id_cms` INT( 11 ) UNSIGNED NOT NULL,
`id_shop` INT( 11 ) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id_cms`, `id_shop`),
	KEY `id_shop` (`id_shop`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;');
	$res &= Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'_cms_shop` (id_shop, id_cms) (SELECT 1, id_cms FROM '._DB_PREFIX_.'_cms)');
	$res &= Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'._cms_block` ADD `id_shop` INT(11) UNSIGNED NOT NULL DEFAULT "1" AFTER `id_cms_block`');

	return $res;
}
