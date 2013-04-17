<?php
/*
 * 2007-2013 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2013 PrestaShop SA
 *  @version  Release: $Revision: 7060 $
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class PSCleaner extends Module
{
	public function __construct()
	{
		$this->name = 'pscleaner';
		$this->tab = 'administration';
		$this->version = '0.9';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('PrestaShop Cleaner');
		$this->description = $this->l('Check and fix functional integrity constraints and remove default data');
		$this->secure_key = Tools::encrypt($this->name);
	}

	public function getContent()
	{
		$html = '<h2>'.$this->l('Be really careful with this tool - There is no possible rollback!').'</h2>';
		if (Tools::isSubmit('submitCheckAndFix'))
			$html .= (count($logs = self::checkAndFix()) ? print_r($logs, true) : $this->l('Nothing that need to be cleaned')).'<br /><br />';
		if (Tools::isSubmit('submitTruncateCatalog'))
		{
			self::truncate('catalog');
			$html .= '<div class="conf">'.$this->l('Catalog truncated').'</div>';
		}
		if (Tools::isSubmit('submitTruncateSales'))
		{
			self::truncate('sales');
			$html .= '<div class="conf">'.$this->l('Orders and customers truncated').'</div>';
		}

		$html .= '
		<script type="text/javascript">
			$(document).ready(function(){
				$("#submitTruncateCatalog").submit(function(){
					if (!$(\'#checkTruncateCatalog\').attr(\'checked\'))
						alert(\''.addslashes($this->l('Please tick the checkbox above')).'\');
					else if (confirm(\''.addslashes($this->l('Are you sure that you want to delete all catalog data?')).'\'))
						return true; 
					return false;
				});
				$("#submitTruncateSales").submit(function(){
					if (!$(\'#checkTruncateSales\').attr(\'checked\'))
						alert(\''.addslashes($this->l('Please tick the checkbox above')).'\');
					else if (confirm(\''.addslashes($this->l('Are you sure that you want to delete all sales data?')).'\'))
						return true; 
					return false;
				});
			});
		</script>
		<form id="submitTruncateCatalog" action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend>'.$this->l('Catalog').'</legend>
				<p>
					<label style="float:none;width:auto">
						<input id="checkTruncateCatalog" type="checkbox" />
						'.$this->l('I understand that all the catalog data will be removed without possible rollback:').'
						'.$this->l('products, features, categories, tags, images, prices, attachments, scenes, stocks, attribute groups and values, manufacturers, suppliers...').'
					</label>
				</p>
				<input type="submit" class="button" name="submitTruncateCatalog" value="'.$this->l('Delete catalog').'" />
			</fieldset>
		</form>
		<br /><br />
		<form id="submitTruncateSales" action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend>'.$this->l('Orders and customers').'</legend>
				<p>
					<label style="float:none;width:auto">
						<input id="checkTruncateSales" type="checkbox" />
						'.$this->l('I understand that all the orders and customers will be removed without possible rollback:').'
						'.$this->l('customers, carts, orders, connections, guests, messages, stats...').'
					</label>
				</p>
				<input type="submit" class="button" id="submitTruncateSales" name="submitTruncateSales" value="'.$this->l('Delete orders & customers').'"/>
			</fieldset>
		</form>
		<br /><br />
		<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend>'.$this->l('Functional integrity constraints').'</legend>
				<input type="submit" class="button" name="submitCheckAndFix" value="'.$this->l('Check & fix').'" />
			</fieldset>
		</form>';
		return $html;
	}

	public static function checkAndFix()
	{
		$db = Db::getInstance();
		$logs = array();
		
		// Simple Cascade Delete
		$queries = array(
			// 0 => DELETE FROM __table__, 1 => WHERE __id__ NOT IN, 2 => NOT IN __table__, 3 => __id__ used in the "NOT IN" table, 4 => module_name
			array('access', 'id_profile', 'profile', 'id_profile'),
			array('access', 'id_tab', 'tab', 'id_tab'),
			array('accessory', 'id_product_1', 'product', 'id_product'),
			array('accessory', 'id_product_2', 'product', 'id_product'),
			array('address_format', 'id_country', 'country', 'id_country'),
			array('attribute', 'id_attribute_group', 'attribute_group', 'id_attribute_group'),
			array('carrier_group', 'id_carrier', 'carrier', 'id_carrier'),
			array('carrier_group', 'id_group', 'group', 'id_group'),
			array('carrier_zone', 'id_carrier', 'carrier', 'id_carrier'),
			array('carrier_zone', 'id_zone', 'zone', 'id_zone'),
			array('cart_cart_rule', 'id_cart', 'cart', 'id_cart'),
			array('cart_product', 'id_cart', 'cart', 'id_cart'),
			array('cart_rule_carrier', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
			array('cart_rule_carrier', 'id_carrier', 'carrier', 'id_carrier'),
			array('cart_rule_combination', 'id_cart_rule_1', 'cart_rule', 'id_cart_rule'),
			array('cart_rule_combination', 'id_cart_rule_2', 'cart_rule', 'id_cart_rule'),
			array('cart_rule_country', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
			array('cart_rule_country', 'id_country', 'country', 'id_country'),
			array('cart_rule_group', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
			array('cart_rule_group', 'id_group', 'group', 'id_group'),
			array('cart_rule_product_rule_group', 'id_cart_rule', 'cart_rule', 'id_cart_rule'),
			array('cart_rule_product_rule', 'id_product_rule_group', 'cart_rule_product_rule_group', 'id_product_rule_group'),
			array('cart_rule_product_rule_value', 'id_product_rule', 'cart_rule_product_rule', 'id_product_rule'),
			array('category_group', 'id_category', 'category', 'id_category'),
			array('category_group', 'id_group', 'group', 'id_group'),
			array('category_product', 'id_category', 'category', 'id_category'),
			array('category_product', 'id_product', 'product', 'id_product'),
			array('cms', 'id_cms_category', 'cms_category', 'id_cms_category'),
			array('cms_block', 'id_cms_category', 'cms_category', 'id_cms_category'),
			array('cms_block_page', 'id_cms', 'cms', 'id_cms'),
			array('cms_block_page', 'id_cms_block', 'cms_block', 'id_cms_block'),
			array('compare', 'id_customer', 'customer', 'id_customer'),
			array('compare_product', 'id_compare', 'compare', 'id_compare'),
			array('compare_product', 'id_product', 'product', 'id_product'),
			array('connections', 'id_shop_group', 'shop_group', 'id_shop_group'),
			array('connections', 'id_shop', 'shop', 'id_shop'),
			array('connections_page', 'id_connections', 'connections', 'id_connections'),
			array('connections_page', 'id_page', 'page', 'id_page'),
			array('connections_source', 'id_connections', 'connections', 'id_connections'),
			array('customer', 'id_shop_group', 'shop_group', 'id_shop_group'),
			array('customer', 'id_shop', 'shop', 'id_shop'),
			array('customer_group', 'id_group', 'group', 'id_group'),
			array('customer_group', 'id_customer', 'customer', 'id_customer'),
			array('customer_message', 'id_customer_thread', 'customer_thread', 'id_customer_thread'),
			array('customer_thread', 'id_shop', 'shop', 'id_shop'),
			array('customization', 'id_cart', 'cart', 'id_cart'),
			array('customization_field', 'id_product', 'product', 'id_product'),
			array('customized_data', 'id_customization', 'customization', 'id_customization'),
			array('delivery', 'id_shop', 'shop', 'id_shop'),
			array('delivery', 'id_shop_group', 'shop_group', 'id_shop_group'),
			array('delivery', 'id_carrier', 'carrier', 'id_carrier'),
			array('delivery', 'id_zone', 'zone', 'id_zone'),
			array('editorial', 'id_shop', 'shop', 'id_shop', 'editorial'),
			array('favorite_product', 'id_product', 'product', 'id_product'),
			array('favorite_product', 'id_customer', 'customer', 'id_customer'),
			array('favorite_product', 'id_shop', 'shop', 'id_shop'),
			array('feature_product', 'id_feature', 'feature', 'id_feature'),
			array('feature_product', 'id_product', 'product', 'id_product'),
			array('feature_value', 'id_feature', 'feature', 'id_feature'),
			array('group_reduction', 'id_group', 'group', 'id_group'),
			array('group_reduction', 'id_category', 'category', 'id_category'),
			array('homeslider', 'id_shop', 'shop', 'id_shop'),
			array('homeslider', 'id_homeslider_slides', 'homeslider_slides', 'id_homeslider_slides'),
			array('hook_module', 'id_hook', 'hook', 'id_hook'),
			array('hook_module', 'id_module', 'module', 'id_module'),
			array('hook_module_exceptions', 'id_hook', 'hook', 'id_hook'),
			array('hook_module_exceptions', 'id_module', 'module', 'id_module'),
			array('hook_module_exceptions', 'id_shop', 'shop', 'id_shop'),
			array('image', 'id_product', 'product', 'id_product'),
			array('message', 'id_cart', 'cart', 'id_cart'),
			array('message_readed', 'id_message', 'message', 'id_message'),
			array('message_readed', 'id_employee', 'employee', 'id_employee'),
			array('module_access', 'id_profile', 'profile', 'id_profile'),
			array('module_access', 'id_module', 'module', 'id_module'),
			array('module_country', 'id_module', 'module', 'id_module'),
			array('module_country', 'id_country', 'country', 'id_country'),
			array('module_country', 'id_shop', 'shop', 'id_shop'),
			array('module_currency', 'id_module', 'module', 'id_module'),
			array('module_currency', 'id_currency', 'currency', 'id_currency'),
			array('module_currency', 'id_shop', 'shop', 'id_shop'),
			array('module_group', 'id_module', 'module', 'id_module'),
			array('module_group', 'id_group', 'group', 'id_group'),
			array('module_group', 'id_shop', 'shop', 'id_shop'),
			array('module_preference', 'id_employee', 'employee', 'id_employee'),
			array('orders', 'id_shop', 'shop', 'id_shop'),
			array('orders', 'id_shop_group', 'group_shop', 'id_shop_group'),
			array('order_carrier', 'id_order', 'orders', 'id_order'),
			array('order_cart_rule', 'id_order', 'orders', 'id_order'),
			array('order_detail', 'id_order', 'orders', 'id_order'),
			array('order_detail_tax', 'id_order_detail', 'order_detail', 'id_order_detail'),
			array('order_history', 'id_order', 'orders', 'id_order'),
			array('order_invoice', 'id_order', 'orders', 'id_order'),
			array('order_invoice_payment', 'id_order', 'orders', 'id_order'),
			array('order_invoice_tax', 'id_order_invoice', 'order_invoice', 'id_order_invoice'),
			array('order_return', 'id_order', 'orders', 'id_order'),
			array('order_return_detail', 'id_order_return', 'order_return', 'id_order_return'),
			array('order_slip', 'id_order', 'orders', 'id_order'),
			array('order_slip_detail', 'id_order_slip', 'order_slip', 'id_order_slip'),
			array('pack', 'id_product_pack', 'product', 'id_product'),
			array('pack', 'id_product_item', 'product', 'id_product'),
			array('page', 'id_page_type', 'page_type', 'id_page_type'),
			array('page_viewed', 'id_shop', 'shop', 'id_shop'),
			array('page_viewed', 'id_shop_group', 'shop_group', 'id_shop_group'),
			array('page_viewed', 'id_date_range', 'date_range', 'id_date_range'),
			array('product_attachment', 'id_attachment', 'attachment', 'id_attachment'),
			array('product_attachment', 'id_product', 'product', 'id_product'),
			array('product_attribute', 'id_product', 'product', 'id_product'),
			array('product_attribute_combination', 'id_product_attribute', 'product_attribute', 'id_product_attribute'),
			array('product_attribute_combination', 'id_attribute', 'attribute', 'id_attribute'),
			array('product_attribute_image', 'id_image', 'image', 'id_image'),
			array('product_attribute_image', 'id_product_attribute', 'product_attribute', 'id_product_attribute'),
			array('product_carrier', 'id_product', 'product', 'id_product'),
			array('product_carrier', 'id_shop', 'shop', 'id_shop'),
			array('product_carrier', 'id_carrier_reference', 'carrier', 'id_reference'),
			array('product_country_tax', 'id_product', 'product', 'id_product'),
			array('product_country_tax', 'id_country', 'country', 'id_country'),
			array('product_country_tax', 'id_tax', 'tax', 'id_tax'),
			array('product_download', 'id_product', 'product', 'id_product'),
			array('product_group_reduction_cache', 'id_product', 'product', 'id_product'),
			array('product_group_reduction_cache', 'id_group', 'group', 'id_group'),
			array('product_sale', 'id_product', 'product', 'id_product'),
			array('product_supplier', 'id_product', 'product', 'id_product'),
			array('product_supplier', 'id_supplier', 'supplier', 'id_supplier'),
			array('product_tag', 'id_product', 'product', 'id_product'),
			array('product_tag', 'id_tag', 'tag', 'id_tag'),
			array('range_price', 'id_carrier', 'carrier', 'id_carrier'),
			array('range_weight', 'id_carrier', 'carrier', 'id_carrier'),
			array('referrer_cache', 'id_referrer', 'referrer', 'id_referrer'),
			array('referrer_cache', 'id_connections_source', 'connections_source', 'id_connections_source'),
			array('scene_category', 'id_scene', 'scene', 'id_scene'),
			array('scene_category', 'id_category', 'category', 'id_category'),
			array('scene_products', 'id_scene', 'scene', 'id_scene'),
			array('scene_products', 'id_product', 'product', 'id_product'),
			array('search_index', 'id_product', 'product', 'id_product'),
			array('search_word', 'id_lang', 'lang', 'id_lang'),
			array('search_word', 'id_shop', 'shop', 'id_shop'),
			array('shop_url', 'id_shop', 'shop', 'id_shop'),
			array('specific_price_priority', 'id_product', 'product', 'id_product'),
			array('stock', 'id_warehouse', 'warehouse', 'id_warehouse'),
			array('stock', 'id_product', 'product', 'id_product'),
			array('stock_available', 'id_product', 'product', 'id_product'),
			array('stock_available', 'id_shop', 'shop', 'id_shop'),
			array('stock_available', 'id_shop_group', 'shop_group', 'id_shop_group'),
			array('stock_mvt', 'id_stock', 'stock', 'id_stock'),
			array('tab_module_preference', 'id_employee', 'employee', 'id_employee'),
			array('tab_module_preference', 'id_tab', 'tab', 'id_tab'),
			array('tax_rule', 'id_country', 'country', 'id_country'),
			array('theme_specific', 'id_theme', 'theme', 'id_theme'),
			array('theme_specific', 'id_shop', 'shop', 'id_shop'),
			array('warehouse_carrier', 'id_warehouse', 'warehouse', 'id_warehouse'),
			array('warehouse_carrier', 'id_carrier', 'carrier', 'id_carrier'),
			array('warehouse_product_location', 'id_product', 'product', 'id_product'),
			array('warehouse_product_location', 'id_warehouse', 'warehouse', 'id_warehouse'),
		);

		$queries = self::bulle($queries);
		foreach ($queries as $query_array)
		{
			// If this is a module and the module is not installed, we continue
			if (isset($query_array[4]) && !Module::isInstalled($query_array[4]))
				continue;

			$query = 'DELETE FROM `'._DB_PREFIX_.$query_array[0].'` WHERE `'.$query_array[1].'` NOT IN (SELECT `'.$query_array[3].'` FROM `'._DB_PREFIX_.$query_array[2].'`)';
			$db->Execute($query);
			if ($affected_rows = $db->Affected_Rows())
				$logs[$query] = $affected_rows;
		}

		// _lang table cleaning
		$tables = Db::getInstance()->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'%_\\_lang"');
		foreach ($tables as $table)
		{
			$table_lang = current($table);
			$table = str_replace('_lang', '', $table_lang);
			$id_table = 'id_'.preg_replace('/^'._DB_PREFIX_.'/', '', $table);
			
			$query = 'DELETE FROM `'.bqSQL($table_lang).'` WHERE `'.bqSQL($id_table).'` NOT IN (SELECT `'.bqSQL($id_table).'` FROM `'.bqSQL($table).'`)';
			$db->Execute($query);
			if ($affected_rows = $db->Affected_Rows())
				$logs[$query] = $affected_rows;

			$query = 'DELETE FROM `'.bqSQL($table_lang).'` WHERE `id_lang` NOT IN (SELECT `id_lang` FROM `'._DB_PREFIX_.'lang`)';
			$db->Execute($query);
			if ($affected_rows = $db->Affected_Rows())
				$logs[$query] = $affected_rows;
		}
		
		// _shop table cleaning
		$tables = Db::getInstance()->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'%_\\_shop"');
		foreach ($tables as $table)
		{
			$table_shop = current($table);
			$table = str_replace('_shop', '', $table_shop);
			$id_table = 'id_'.preg_replace('/^'._DB_PREFIX_.'/', '', $table);

			if (in_array($table_shop, array(_DB_PREFIX_.'carrier_tax_rules_group_shop')))
				continue;
			
			$query = 'DELETE FROM `'.bqSQL($table_shop).'` WHERE `'.bqSQL($id_table).'` NOT IN (SELECT `'.bqSQL($id_table).'` FROM `'.bqSQL($table).'`)';
			$db->Execute($query);
			if ($affected_rows = $db->Affected_Rows())
				$logs[$query] = $affected_rows;

			$query = 'DELETE FROM `'.bqSQL($table_shop).'` WHERE `id_shop` NOT IN (SELECT `id_shop` FROM `'._DB_PREFIX_.'shop`)';
			$db->Execute($query);
			if ($affected_rows = $db->Affected_Rows())
				$logs[$query] = $affected_rows;
		}

		Category::regenerateEntireNtree();

		// @Todo: Remove attachment files, images...
		Image::clearTmpDir();
		
		return $logs;
	}

	public function truncate($case)
	{
		$db = Db::getInstance();

		switch ($case)
		{
			case 'catalog':
				$id_home = Configuration::get('PS_HOME_CATEGORY');
				$id_root = Configuration::get('PS_ROOT_CATEGORY');
				Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category` WHERE id_category NOT IN ('.(int)$id_home.', '.(int)$id_root.')');
				Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category_lang` WHERE id_category NOT IN ('.(int)$id_home.', '.(int)$id_root.')');
				Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category_shop` WHERE id_category NOT IN ('.(int)$id_home.', '.(int)$id_root.')');
				foreach (scandir(_PS_CAT_IMG_DIR_) as $dir)
					if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $dir))
						unlink(_PS_CAT_IMG_DIR_.$dir);
				$tables = array(
					'product',
					'product_shop',
					'feature_product',
					'product_lang',
					'category_product',
					'product_tag',
					'image',
					'image_lang',
					'image_shop',
					'specific_price',
					'specific_price_priority',
					'product_carrier',
					'cart_product',
					'compare_product',
					'product_attachment',
					'product_country_tax',
					'product_download',
					'product_group_reduction_cache',
					'product_sale',
					'product_supplier',
					'scene_products',
					'warehouse_product_location',
					'stock',
					'stock_available',
					'stock_mvt',
					'customization',
					'customization_field',
					'supply_order_detail',
					'attribute_impact',
					'product_attribute',
					'product_attribute_shop',
					'product_attribute_combination',
					'product_attribute_image',
					'attribute',
					'attribute_impact',
					'attribute_lang',
					'attribute_group',
					'attribute_group_lang',
					'attribute_group_shop',
					'attribute_shop',
					'product_attribute',
					'product_attribute_shop',
					'product_attribute_combination',
					'product_attribute_image',
					'stock_available',
					'manufacturer',
					'manufacturer_lang',
					'manufacturer_shop',
					'supplier',
					'supplier_lang',
					'supplier_shop',
					'customization',
					'customization_field',
					'customization_field_lang',
					'customized_data',
					'feature',
					'feature_lang',
					'feature_product',
					'feature_shop',
					'feature_value',
					'feature_value_lang',
					'pack',
					'scene',
					'scene_category',
					'scene_lang',
					'scene_products',
					'scene_shop',
					'search_index',
					'search_word',
					'specific_price',
					'specific_price_priority',
					'specific_price_rule',
					'specific_price_rule_condition',
					'specific_price_rule_condition_group',
					'stock',
					'stock_available',
					'stock_mvt',
				);
				foreach ($tables as $table)
					$db->execute('TRUNCATE TABLE `'._DB_PREFIX_.bqSQL($table).'`');
				$db->execute('DELETE FROM `'._DB_PREFIX_.'address` WHERE id_manufacturer > 0 OR id_supplier > 0 OR id_warehouse > 0');

				Image::deleteAllImages(_PS_PROD_IMG_DIR_);
				if (!file_exists(_PS_PROD_IMG_DIR_))
					mkdir(_PS_PROD_IMG_DIR_);
				foreach (scandir(_PS_MANU_IMG_DIR_) as $dir)
					if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $dir))
						unlink(_PS_MANU_IMG_DIR_.$dir);
				foreach (scandir(_PS_SUPP_IMG_DIR_) as $dir)
					if (preg_match('/^[0-9]+(\-(.*))?\.jpg$/', $dir))
						unlink(_PS_SUPP_IMG_DIR_.$dir);
				break;

			case 'sales':
				$tables = array(
					'customer',
					'cart',
					'cart_product',
					'connections',
					'connections_page',
					'connections_source',
					'customer_group',
					'customer_message',
					'customer_message_sync_imap',
					'customer_thread',
					'guest',
					'message',
					'message_readed',
					'orders',
					'order_carrier',
					'order_cart_rule',
					'order_detail',
					'order_detail_tax',
					'order_history',
					'order_invoice',
					'order_invoice_payment',
					'order_invoice_tax',
					'order_payment',
					'order_return',
					'order_return_detail',
					'order_return_state',
					'order_return_state_lang',
					'order_slip',
					'order_slip_detail',
					'page',
					'pagenotfound',
					'page_type',
					'page_viewed',
					'referrer_cache',
					'sekeyword',
				);
				foreach ($tables as $table)
					$db->execute('TRUNCATE TABLE `'._DB_PREFIX_.bqSQL($table).'`');
				$db->execute('DELETE FROM `'._DB_PREFIX_.'address` WHERE id_customer > 0');
				break;
		}
	}
	
	public static function cleanAndOptimize()
	{
		// Clean (carts...)
	}
	
	protected static function bulle($array)
	{
		$sorted = false;
		$size = count($array);
		while (!$sorted)
		{
			$sorted = true;
			for ($i = 0; $i < $size - 1; ++$i)
				for ($j = $i + 1; $j < $size; ++$j)
				{
					if ($array[$i][2] == $array[$j][0])
					{
						// var_dump(array($array[$i], $array[$j]));
						$tmp = $array[$i];
						$array[$i] = $array[$j];
						$array[$j] = $tmp;
						$sorted = false;
					}
				}
		}
		return $array;
	}
}
