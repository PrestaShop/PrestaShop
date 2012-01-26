<?php

require_once(_PS_INSTALLER_PHP_UPGRADE_DIR_.'add_new_tab.php');

function create_multistore()
{
	$res = true;

	// @todo : use _PS_ROOT_DIR_
	if (defined('__PS_BASE_URI__'))
		$INSTALLER__PS_BASE_URI = __PS_BASE_URI__;
	else
	{
		// note: create_multistore is called for 1.5.0.0 upgrade
		// so, __PS_BASE_URI__ should be always defined in settings.inc.php
		// @todo generate __PS_BASE_URI__ using $_SERVER['REQUEST_URI'], just in case
		return false;
	}
	$all_themes_dir = _PS_ROOT_DIR_.'/themes';
	$themes = scandir($all_themes_dir);
	foreach ($themes AS $theme)
		if (is_dir($all_themes_dir.'/'.$theme) 
				&& $theme[0] != '.' 
				&& $theme != 'prestashop')
		{
			$sql = 'INSERT INTO 
				'._DB_PREFIX_.'theme (`id_theme`, name) 
				VALUES("", "'.Db::getInstance()->escape($theme).'")';
			$res &= Db::getInstance()->execute($sql);
		}
	$res &= Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'shop 
		SET
			name = (SELECT value 
				FROM '._DB_PREFIX_.'configuration 
				WHERE name = "PS_SHOP_NAME"
			),
			id_theme = (SELECT id_theme FROM '._DB_PREFIX_.'theme WHERE name="'.Db::getInstance()->escape(_THEME_NAME_).'") 
		WHERE id_shop = 1');
	$shop_domain = Db::getInstance()->getValue('SELECT `value`
															FROM `'._DB_PREFIX_.'_configuration` 
															WHERE `name`="PS_SHOP_DOMAIN"');
	$shop_domain_ssl = Db::getInstance()->getValue('SELECT `value`
															FROM `'._DB_PREFIX_.'_configuration` 
															WHERE `name`="PS_SHOP_DOMAIN_SSL"');
	if(empty($shop_domain))
	{
		$shop_domain = create_multistore_getHttpHost();
		$shop_domain_ssl = create_multistore_getHttpHost();
	}

	$_PS_DIRECTORY_ = trim(str_replace(' ', '%20', $INSTALLER__PS_BASE_URI), '/');
	$_PS_DIRECTORY_ = ($_PS_DIRECTORY_) ? '/'.$_PS_DIRECTORY_.'/' : '/';
	$res &= Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'shop_url` (`id_shop`, `domain`, `domain_ssl`, `physical_uri`, `virtual_uri`, `main`, `active`) 
																			VALUES(1, \''.pSQL($shop_domain).'\', \''.pSQL($shop_domain_ssl).'\', \''.pSQL($_PS_DIRECTORY_).'\', \'\', 1, 1)');

	// Stock conversion
	$sql = 'INSERT INTO `'._DB_PREFIX_.'.stock` (`id_product`, `id_product_attribute`, `id_group_shop`, `id_shop`, `quantity`)
	VALUES (SELECT `p.id_product`, 0, 1, 1, `p.quantity` FROM `'._DB_PREFIX_.'.product` p);';
	$res &= Db::getInstance()->execute($sql);

	$sql = 'INSERT INTO `'._DB_PREFIX_.'.stock` (`id_product`, `id_product_attribute`, `id_group_shop`, `id_shop`, `quantity`)
	VALUES (SELECT `id_product`, `id_product_attribute`, 1, 1, `quantity` FROM `'._DB_PREFIX_.'product_attribute` p);';
	$res &= Db::getInstance()->execute($sql);

	// Add admin tabs
	$shopTabId = add_new_tab('AdminShop', 'it:Shops|es:Shops|fr:Boutiques|de:Shops|en:Shops',  0, true);
	add_new_tab('AdminGroupShop', 'it:Group Shops|es:Group Shops|fr:Groupes de boutique|de:Group Shops|en:Group Shops', $shopTabId);
	add_new_tab('AdminShopUrl', 'it:Shop Urls|es:Shop Urls|fr:URLs de boutique|de:Shop Urls|en:Shop Urls', $shopTabId);

	return $res;
}

function create_multistore_getHttpHost()
{
	$host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
	return $host;
}
