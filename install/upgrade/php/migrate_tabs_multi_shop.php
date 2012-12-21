<?php

/**
 * Migrate BO tabs for multi-shop new reorganization
 */
function migrate_tabs_multi_shop()
{
	include_once(_PS_INSTALL_PATH_.'upgrade/php/add_new_tab.php');
	include_once(_PS_INSTALL_PATH_.'upgrade/php/migrate_tabs_15.php');

	$nbr_shop = Db::getInstance()->getValue('SELECT count(id_shop) FROM '._DB_PREFIX_.'shop');
	$tab_shop_group_active = false;
	
	//check if current configuration has more than one shop
	if ($nbr_shop > 1)
	{
		Db::getInstance()->update('configuration', array('value' => true), 'name = \'PS_MULTISHOP_FEATURE_ACTIVE\'');
		$tab_shop_group_active = true;		
	}
	
	// ===== remove AdminParentShop from BO menu =====
	$admin_parent_shop_id = get_tab_id('AdminParentShop');
	$admin_shop_group_id = get_tab_id('AdminShopGroup');
	Db::getInstance()->delete('tab', 'id_tab IN ('.(int)$admin_shop_group_id.', '.(int)$admin_parent_shop_id.')');
	Db::getInstance()->delete('tab_lang', 'id_tab IN ('.(int)$admin_shop_group_id.', '.(int)$admin_parent_shop_id.')');
	
	// ===== add AdminShopGroup to parent AdminTools =====
	$admin_shop_group_id = add_new_tab('AdminShopGroup', 'en:Multi-shop|fr:Multiboutique|es:Multi-tienda|de:Multi-shop|it:Multi-shop', get_tab_id('AdminTools'), true);
	Db::getInstance()->update('tab', array('active' => $tab_shop_group_active), 'id_tab = '.(int)$admin_shop_group_id);
	
	// ===== hide AdminShopUrl and AdminShop =====
	Db::getInstance()->update('tab', array('id_parent' => '-1'), 'id_tab IN ('.get_tab_id('AdminShop').', '.get_tab_id('AdminShopUrl').')');	
}

