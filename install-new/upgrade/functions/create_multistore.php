<?php

function create_multistore()
{
	$res = true;

	$themes = scandir(dirname(__FILE__).'/../../themes');
	foreach ($themes AS $theme)
		if (is_dir(dirname(__FILE__).'/../../themes/'.$theme) && $theme != '.' &&  $theme != '..' && $theme != 'prestashop')
			$res &= Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'theme (`id_theme`, `name`) VALUES(\'\', \''.pSQL($theme).'\')');
	$res &= Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'shop SET id_theme = (SELECT id_theme FROM '._DB_PREFIX_.'theme WHERE name=\''.pSQL(_THEME_NAME_).'\') WHERE id_shop = 1');
	$shop_domain = Db::getInstance()->getValue('SELECT `value`
															FROM `'._DB_PREFIX_.'_configuration` 
															WHERE `name`=\'PS_SHOP_DOMAIN\'');
	$shop_domain_ssl = Db::getInstance()->getValue('SELECT `value`
															FROM `'._DB_PREFIX_.'_configuration` 
															WHERE `name`=\'PS_SHOP_DOMAIN_SSL\'');
	if(empty($shop_domain))
	{
		$shop_domain = Tools::getHttpHost();
		$shop_domain_ssl = Tools::getHttpHost();
	}

	$_PS_DIRECTORY_ = trim(str_replace(' ', '%20', INSTALLER__PS_BASE_URI), '/');
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
