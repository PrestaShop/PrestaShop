<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class importerosc extends ImportModule
{
	public function __construct()
	{
		$this->name = 'importerosc';
		$this->tab = 'migration_tools';
		$this->version = '1.0';
		$this->author = 'PrestaShop';
		$this->theImporter = 1;
		$this->need_instance = 1;

		parent::__construct ();

		$this->displayName = $this->l('Importer osCommerce');
		$this->description = $this->l('This module allows you to import from osCommerce to Prestashop.');
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('beforeAuthentication'))
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!parent::uninstall())
			return false;
		return true;
	}

	public function displaySpecificOptions()
	{
		$html = '';
		if (!$this->checkCategoriesLevel())
			$html .= $this->displayCategoriesLevelConf();

		$langagues = $this->executeS('SELECT * FROM  `'.bqSQL($this->prefix).'languages`');
		$curencies = $this->executeS('SELECT * FROM  `'.bqSQL($this->prefix).'currencies`');

		$html .= '<label style=\'width:220px\'>'.$this->l('Default osCommerce language  : ').'</label>
				<div class="margin-form">
				<select name=\'defaultOscLang\'><option value=\'0\'>------</option>';
				foreach($langagues AS $lang)
					$html .= '<option value="'.$lang['languages_id'].'">'.$lang['name'].'</option>';
		$html .= '</select></div>
				<label style=\'width:220px\'>'.$this->l('Default osCommerce currency : ').'</label>
				<div class=\'margin-form\'>
				<select name=\'defaultOscCurrency\'><option value=\'0\'>------</option>';
				foreach($curencies AS $curency)
					$html .= '<option value=\''.$curency['currencies_id'].'\'>'.$curency['title'].'</option>';
		$html .= '</select></div>';
		$html .= '<label style=\'width:220px\'>'.$this->l('Shop url : ').'</label>
				<div class=\'margin-form\'>
					http://<input type="text" name="shop_url">/
					<p>'.$this->l('Specify the root URL of your site oscommerce').'</p>
				</div>';
		return $html;
	}


	public function validateSpecificOptions()
	{
		$errors = array();
		if (Tools::getValue('defaultOscLang') == 0)
			$errors[] = $this->l('Please select a default language');
		if (Tools::getValue('defaultOscCurrency') == 0)
			$errors[] = $this->l('Please select a default currency');
		if (Tools::getValue('shop_url') == '')
			$errors[] = $this->l('Please set your shop url');
		if (!sizeof($errors))
			die('{"hasError" : false, "error" : []}');
		else
			die('{"hasError" : true, "error" : '.Tools::jsonEncode($errors).'}');
	}


	public function getDefaultIdLang ()
	{
		return Tools::getValue('defaultOscLang');
	}

	public function getDefaultIdCurrency ()
	{
		return Tools::getValue('defaultOscCurrency');
	}


	public function getLangagues($limit = 0, $nrb_import = 100)
	{
		$identifier = 'id_lang';

		$langagues = $this->executeS('
			SELECT languages_id as id_lang, name as name, code as iso_code, 1 as active, (\'m/j/Y\') as date_format_lite, (\'m/j/Y H:i:s\') as date_format_full
			FROM  `'.bqSQL($this->prefix).'languages`
			LIMIT '.(int)$limit.' , '.(int)$nrb_import);
		return $this->autoFormat($langagues, $identifier);
	}

	public function getCurrencies($limit = 0, $nrb_import = 100)
	{
		$identifier = 'id_currency';
		$currencies = $this->executeS('
									SELECT currencies_id as id_currency, title as name, code as iso_code, 0 as format, 999 as iso_code_num, 1 as decimals,
									CONCAT(`symbol_left`, `symbol_right`) as sign, value as conversion_rate
									FROM  `'.bqSQL($this->prefix).'currencies` LIMIT '.(int)($limit).' , '.(int)$nrb_import
									);
		return $this->autoFormat($currencies, $identifier);
	}

	public function getZones($limit = 0, $nrb_import = 100)
	{
		$identifier = 'id_zone';
		$zones = $this->executeS('SELECT geo_zone_id as id_zone, geo_zone_name as name, 1 as active FROM  `'.bqSQL($this->prefix).'geo_zones` LIMIT '.(int)($limit).' , '.(int)$nrb_import);
		return $this->autoFormat($zones, $identifier);
	}

	public function getCountries($limit = 0, $nrb_import = 100)
	{
		$multiLangFields = array('name');
		$keyLanguage = 'id_lang';
		$identifier = 'id_country';
		$defaultIdLang = $this->getDefaultIdLang();
		$countries = $this->executeS('
										SELECT countries_id as id_country, countries_name as name, countries_iso_code_2 as iso_code, '.(int)$defaultIdLang.' as id_lang,
										1 as id_zone, 0 as id_currency, 1 as contains_states, 1 as need_identification_number, 1 as active, 1 as display_tax_label
										FROM  `'.bqSQL($this->prefix).'countries` as c  LIMIT '.(int)($limit).' , '.(int)$nrb_import);

		return $this->autoFormat($countries, $identifier, $keyLanguage, $multiLangFields);
	}

	public function getStates($limit = 0, $nrb_import = 100)
	{
		$identifier = 'id_state';
		$states = array(
				0 => array(
						'id_state' => 0,
						'id_country' => 0,
						'id_zone' => 0,
						'iso_code' => 999,
						'name' => 'osc',
						'active' => 0
						)
					);
		return $this->autoFormat($states, $identifier);
	}

	public function getGroups()
	{
		$idLang = $this->getDefaultIdLang();
		return array( 1 => array(
								'id_group' => 1,
								'price_display_method' => 0,
								'name' => array($idLang => $this->l('Default osCommerce Group'))
								)
					);
	}

	public function getCustomers($limit = 0, $nrb_import = 100)
	{
		$genderMatch = array('m' => 1,'f' => 2);
		$identifier = 'id_customer';
		$customers = $this->executeS('
									SELECT c.`customers_id` as id_customer, 1 as id_default_group, c.`customers_gender` as id_gender, c.`customers_firstname` as firstname,
									IFNULL( STRCMP(c.`customers_newsletter`, \'\') , 0 ) as newsletter, c.`customers_lastname` as lastname,
									DATE(c.`customers_dob`) as birthday, c.`customers_email_address` as email, c.`customers_password` as passwd, 1 as active,
									ci.`customers_info_date_account_created` as date_add
									FROM  `'.bqSQL($this->prefix).'customers` c
									LEFT JOIN `'.bqSQL($this->prefix).'customers_info` ci  ON (c.`customers_id` = ci.`customers_info_id`)
									LIMIT '.(int)($limit).' , '.(int)$nrb_import
									);

		foreach($customers AS &$customer)
			if (isset($customer['id_gender']) && array_key_exists($customer['id_gender'], $genderMatch))
			   $customer['id_gender'] = $genderMatch[$customer['id_gender']];
			else
			   $customer['id_gender'] = 0;

		return $this->autoFormat($customers, $identifier);
	}

	public function getAddresses($limit = 0, $nrb_import = 100)
	{
		$identifier = 'id_address';
		$addresses = $this->executeS('
									SELECT address_book_id as id_address, customers_id as id_customer, CONCAT(customers_id, \'_address\') as alias, entry_company as company, entry_firstname as firstname,
									entry_lastname as lastname, entry_street_address as address1, entry_postcode as postcode, entry_city as city, entry_country_id as id_country, 0 as id_state
									FROM  `'.bqSQL($this->prefix).'address_book` LIMIT '.(int)($limit).' , '.(int)$nrb_import);
		return $this->autoFormat($addresses, $identifier);
	}

	public function getCategories($limit = 0, $nrb_import = 100)
	{
		$multiLangFields = array('name', 'link_rewrite');
		$keyLanguage = 'id_lang';
		$identifier = 'id_category';
		$categories = $this->executeS('
									SELECT c.categories_id as id_category, c.parent_id as id_parent, level as level_depth, cd.language_id as id_lang, cd.categories_name as name , 1 as active, categories_image as images
									FROM `'.bqSQL($this->prefix).'categories` c
									LEFT JOIN `'.bqSQL($this->prefix).'categories_description` cd ON (c.categories_id = cd.categories_id)
									WHERE cd.categories_name IS NOT NULL AND cd.language_id IS NOT NULL
									ORDER BY c.level ASC , c.`categories_id`
									LIMIT '.(int)($limit).' , '.(int)$nrb_import);
		foreach($categories as& $cat)
		{
			$cat['link_rewrite'] = Tools::link_rewrite($cat['name']);
			$cat['images'] = array(Tools::getProtocol().Tools::getValue('shop_url').'/images/'.$cat['images']);
		}

		return $this->autoFormat($categories, $identifier, $keyLanguage, $multiLangFields);
	}

	public function getAttributesGroups($limit = 0, $nrb_import = 100)
	{
		$multiLangFields = array('name', 'public_name');
		$keyLanguage = 'id_lang';
		$identifier = 'id_attribute_group';
		$countries = $this->executeS('
									SELECT products_options_id as id_attribute_group, products_options_name as name , products_options_name as public_name, language_id as id_lang, 0 as is_color_group
									FROM  `'.bqSQL($this->prefix).'products_options`
									LIMIT '.(int)($limit).' , '.(int)$nrb_import);
		return $this->autoFormat($countries, $identifier, $keyLanguage, $multiLangFields);
	}

	public function getAttributes($limit = 0, $nrb_import = 100)
	{
		$multiLangFields = array('name');
		$keyLanguage = 'id_lang';
		$identifier = 'id_attribute';
		$countries = $this->executeS('
									SELECT p.`products_options_values_id` as id_attribute, p.`products_options_values_name` as name, p.`language_id` as id_lang , po.`products_options_id` as id_attribute_group
									FROM  `'.bqSQL($this->prefix).'products_options_values` p
									LEFT JOIN `'.bqSQL($this->prefix).'products_options_values_to_products_options` po ON (po.products_options_values_id = p.products_options_values_id)
									ORDER BY p.`products_options_values_id`
									LIMIT '.(int)($limit).' , '.(int)$nrb_import);
		return $this->autoFormat($countries, $identifier, $keyLanguage, $multiLangFields);
	}

	public function getProducts($limit = 0, $nrb_import = 100)
	{
		$multiLangFields = array('name', 'link_rewrite', 'description');
		$keyLanguage = 'id_lang';
		$identifier = 'id_product';
		$products = $this->executeS('
									SELECT p.`products_id` as id_product, p.`products_quantity` as quantity, p.`products_model` as reference, p.`products_price` as price, p.`products_weight` as weight,
									IFNULL(STRCMP(p.`products_status`, \'\') , 0 ) as active, p.`manufacturers_id` as id_manufacturer, pd.language_id as id_lang, pd.products_name as name,
									pd.products_description as description, CONCAT(\''.pSQL(Tools::getProtocol()).pSQL(Tools::getValue('shop_url')).'\/images/\',p.`products_image`) as images,
									(SELECT ptc.categories_id FROM `'.bqSQL($this->prefix).'products_to_categories` ptc WHERE ptc.`products_id` = p.`products_id` LIMIT 1) as id_category_default,
									p.`products_date_added` as date_add
									FROM	`'.bqSQL($this->prefix).'products` p
									LEFT JOIN `'.bqSQL($this->prefix).'products_description` pd ON (p.products_id = pd.products_id)
									WHERE pd.products_name IS NOT NULL AND pd.language_id IS NOT NULL
									ORDER BY p.`products_id`
									LIMIT '.(int)($limit).' , '.(int)$nrb_import);

		$this->execute('CREATE TABLE IF NOT EXISTS`products_images` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`products_id` int(11) NOT NULL,
						`image` varchar(64) DEFAULT NULL,
						`htmlcontent` text,
						`sort_order` int(11) NOT NULL,
						PRIMARY KEY (`id`),
						KEY `products_images_prodid` (`products_id`)
						)');
		foreach($products as& $product)
		{
			$result = $this->executeS('SELECT `image` FROM `'.bqSQL($this->prefix).'products_images` WHERE products_id = '.(int)$product['id_product']);
			$images = array();
			foreach($result as $res)
				$images[] = Tools::getProtocol().Tools::getValue('shop_url').'/images/'.$res['image'];
			$product['images'] = array_merge(array($product['images']), $images);
			$product['link_rewrite'] = Tools::link_rewrite($product['name']);


			$result = $this->executeS('SELECT `categories_id` FROM `'.bqSQL($this->prefix).'products_to_categories` WHERE products_id = '.(int)$product['id_product']);
			$category_product = array('category_product' => array($product['id_category_default'] => $product['id_product']));
			foreach($result as $res)
				$category_product['category_product'][$res['categories_id']] = $product['id_product'];
			$product['association'] = $category_product;
		}
		return $this->autoFormat($products, $identifier, $keyLanguage, $multiLangFields);
	}

	public function getProductsCombination($limit = 0, $nrb_import = 100)
	{
		$identifier = 'id_product_attribute';
		$combinations = $this->executeS('
										SELECT products_attributes_id as id_product_attribute, products_id as id_product, options_values_price as price, options_values_id
										FROM  `'.bqSQL($this->prefix).'products_attributes` LIMIT '.(int)($limit).' , '.(int)$nrb_import);
		foreach($combinations as& $combination)
		{
			$combination['association'] = array('product_attribute_combination' => array($combination['options_values_id'] => $combination['id_product_attribute']));
			unset($combination['options_values_id']);
		}
		return $this->autoFormat($combinations, $identifier);
	}

	public function getManufacturers($limit = 0, $nrb_import = 100)
	{
		$identifier = 'id_manufacturer';
		$manufacturers = $this->executeS('
										SELECT manufacturers_id as id_manufacturer, manufacturers_name as name, 1 as active, manufacturers_image as images
										FROM  `'.bqSQL($this->prefix).'manufacturers` LIMIT '.(int)($limit).' , '.(int)$nrb_import);
		foreach($manufacturers as& $manufacturer)
			$manufacturer['images'] = array(Tools::getProtocol().Tools::getValue('shop_url').'/images/'.$manufacturer['images']);

		return $this->autoFormat($manufacturers, $identifier);
	}

	public function getOrdersStates($limit = 0, $nrb_import = 100)
	{
		$multiLangFields = array('name');
		$keyLanguage = 'id_lang';
		$identifier = 'id_order_state';
		$ordersStates = $this->executeS('
									SELECT `orders_status_id` as id_order_state, `language_id` as id_lang, `orders_status_name` as name , 1 as hidden
									FROM  `'.bqSQL($this->prefix).'orders_status`
									LIMIT '.(int)($limit).' , '.(int)$nrb_import);//IF(`public_flag` = 0, 1, 0) as hidden
		return $this->autoFormat($ordersStates, $identifier, $keyLanguage, $multiLangFields);
	}

	public function getOrders($limit = 0, $nrb_import = 100)
	{
		$orders = array();
		$addresses = $this->executeS('SELECT customers_id as id_customer, address_book_id as id_address FROM  `'.bqSQL($this->prefix).'address_book` GROUP BY customers_id');
		$matchAddresses = array();
		foreach($addresses as $address)
			$matchAddresses[$address['id_customer']] = $address['id_address'];
		$psCarrierDefault = (int)Configuration::get('PS_CARRIER_DEFAULT');
		$psCurrency = Currency::getCurrencies();

		foreach($psCurrency as $key => $currency)
		{
			$psCurrency[$currency['iso_code']] = $currency['id_currency'];
			unset($psCurrency[$key]);
		}

		$orders = $this->executeS('
								SELECT orders_id as id_cart, '.$psCarrierDefault.' as id_carrier, 1 as id_lang, currency as id_currency, customers_id as id_customer, payment_method as payment, 1 as valid,
								date_purchased as date_add, last_modified as date_upd
								FROM  `'.bqSQL($this->prefix).'orders` LIMIT '.(int)($limit).' , '.(int)$nrb_import);
		foreach($orders as $key => $order)
		{
			$orders[$key]['id_currency'] = (array_key_exists($order['id_currency'], $psCurrency) ? $psCurrency[$order['id_currency']] : 0);
			$orders[$key]['id_address_delivery'] = (array_key_exists($order['id_customer'], $matchAddresses) ?  $matchAddresses[$order['id_customer']] : 0);
			$orders[$key]['id_address_invoice'] = (array_key_exists($order['id_customer'], $matchAddresses) ?  $matchAddresses[$order['id_customer']] : 0);
			$orders[$key]['total_paid'] = $this->getValue('SELECT value FROM `'.bqSQL($this->prefix).'orders_total` WHERE `orders_id` = '.(int)$order['id_cart'].' AND class=\'ot_total\'');
			$orders[$key]['total_paid_real'] = $this->getValue('SELECT value FROM `'.bqSQL($this->prefix).'orders_total` WHERE `orders_id` = '.(int)$order['id_cart'].' AND class=\'ot_total\'');
			$orders[$key]['total_products'] = $this->getValue('SELECT value FROM `'.bqSQL($this->prefix).'orders_total` WHERE `orders_id` = '.(int)$order['id_cart'].' AND class=\'ot_shipping\'');
			$tax = $this->getValue('SELECT value FROM `'.bqSQL($this->prefix).'orders_total` WHERE `orders_id` = '.(int)$order['id_cart'].' AND class=\'ot_tax\'');
			$orders[$key]['total_products_wt'] = $this->getValue('SELECT value FROM `'.bqSQL($this->prefix).'orders_total` WHERE `orders_id` = '.(int)$order['id_cart'].' AND class=\'ot_total\'') - $tax;
			$orders[$key]['total_shipping'] = $this->getValue('SELECT value FROM `'.bqSQL($this->prefix).'orders_total` WHERE `orders_id` = '.(int)$order['id_cart'].' AND class=\'ot_shipping\'');
			$orders[$key]['total_discounts'] = 0;
			$orders[$key]['total_wrapping'] = 0;
			$orders[$key]['cart_products'] = $this->executeS('
														SELECT `orders_id` as id_cart, `products_id` as id_product, 0 as id_product_attribute, `products_quantity` as quantity
														FROM  `'.bqSQL($this->prefix).'orders_products` WHERE `orders_id` = '.(int)$order['id_cart']);
			$orders[$key]['order_products'] = $this->executeS('
														SELECT `orders_id` as id_order, `products_id` as product_id, 0 as product_attribute_id, `products_name` as product_name, `products_quantity` as product_quantity,
														`final_price` as product_price, 0 as product_weight
 														FROM  `'.bqSQL($this->prefix).'orders_products` WHERE `orders_id` = '.(int)$order['id_cart']);
			$orders[$key]['order_history'] = $this->executeS('
														SELECT `orders_status_history_id` as id_order_history, 0 as id_employee, `orders_id` as id_order, `orders_status_id` as id_order_state, `date_added` as date_add
														FROM  `'.bqSQL($this->prefix).'orders_status_history` WHERE `orders_id` = '.(int)$order['id_cart']);

		}
		return $orders;
	}

	private function autoFormat($items, $identifier, $keyLanguage = NULL, $multiLangFields = array())
	{
		$array = array();
		foreach ($items AS $item)
			if (sizeof($multiLangFields) && is_array($multiLangFields) && isset($array[$item[$identifier]][$multiLangFields[0]]))
				foreach ($multiLangFields AS $key)
					$array[$item[$identifier]][$key][$item[$keyLanguage]] = $item[$key];
			else
				foreach ($item AS $key => $value)
					if (sizeof($multiLangFields) AND in_array($key, $multiLangFields))
						$array[$item[$identifier]][$key] = array($item[$keyLanguage] => $value);
					elseif (sizeof($multiLangFields) AND $key == $keyLanguage)
						continue;
					else
						$array[$item[$identifier]][$key] = $value;
		return $array;
	}

	public function hookbeforeAuthentication($params)
	{
		$passwd = trim(Tools::getValue('passwd'));
		$email = trim(Tools::getValue('email'));
		$result = Db::getInstance()->GetRow('
	          SELECT *
	          FROM `'._DB_PREFIX_     .'customer`
	          WHERE `active` = 1 AND `email` = \''.pSQL($email).'\'');
		if ($result && !empty($result['passwd_'.$this->name]))
	    {
			if($this->checkPwd($passwd, $result['passwd_'.pSQL($this->name)]))
		 	{
				$ps_passwd =  md5(pSQL(_COOKIE_KEY_.$passwd));
				Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'customer`
				SET `passwd` = \''.pSQL($ps_passwd).'\', `passwd_'.bqSQL($this->name).'` = \'\'
				WHERE `'._DB_PREFIX_.'customer`.`id_customer` ='.(int)$result['id_customer'].' LIMIT 1');
			}
		}

	}

	private function checkPwd($passwd, $encrypt_pwd)
	{
		//checks the type of encryption password
		if (preg_match('/^[A-Z0-9]{32}\:[A-Z0-9]{2}$/i', $encrypt_pwd) === 1)
		{
			//salt
			$stack = explode(':', $encrypt_pwd);
      		if (sizeof($stack) != 2)
      			return false;

      		if (md5($stack[1] . $passwd) == $stack[0])
       			return true;
       		else
       			return false;
		}
		else
		{
			//phpass
			if (file_exists(dirname(__FILE__).'/passwordhash.php'))
			{
				include(dirname(__FILE__).'/passwordhash.php');
				$hasher = new PasswordHash(10, true);
				return $hasher->CheckPassword($passwd, $encrypt_pwd);
			}
			else
				return false;
		}

	}

	public function displayConfigConnector()
	{
		$content = '<script>var type_connector = "db";</script>
					<label>'.$this->l('Server').' : </label>
						<div class="margin-form">
							<input type="text" name="server" id="server" value="">
							<p>'.$this->l('(eg : mysql.mydomain.com)').'</p>
						</div>
					<label>'.$this->l('User').' : </label>
						<div class="margin-form">
							<input type="text" name="user" id="user" value="">
						</div>
					<label>'.$this->l('Password').' : </label>
						<div class="margin-form">
							<input type="password" name="password" id="password" value="">
							<p>'.$this->l('(password can be empty)').'</p>
						</div>
					<label>'.$this->l('Database').' : </label>
						<div class="margin-form" style="">
							<input type="text" name="database" id="database" value="">
						</div>
					<label>'.$this->l('Database prefix').' : </label>
						<div class="margin-form" style="">
							<input type="text" name="prefix" id="prefix" value="">
							<p>'.$this->l('(The prefix is optional. If all of your database tables start with "pref_", your prefix is "pref_")').'</p>
						</div>';
		return $content;
}

	public function checkCategoriesLevel()
	{
		$columns = $this->executeS('SHOW COLUMNS FROM `'.bqSQL($this->prefix).'categories` ');
		foreach($columns as $field)
			if ($field['Field'] ==  'level')
				return true;
		return false;
}

	public function displayCategoriesLevelConf()
	{
		$html = '<div class="warn" id="warn_category_level" style="width:450px;display:none">
					<img src="../img/admin/warn2.png">
				'.$this->l('You are trying to import categories and we\'ve detected, that your oscommerce database don\'t have the field "level" in the table categorie. You must have this field to continue the import of categories.');

		$html .= '<button class="button" onclick="addAndCalculateLevel();" style="padding:10px;font-size:13px;text:align:center">'.$this->l('Click to add and calculate the filed "level" .').'</button> <span id="loading" style="display:none"><img src="../img/loader.gif"></span></div>
				<div class="conf" id="conf_category_level" style="width:450px;display:none"><img src="../img/admin/ok2.png">'.$this->l('Level field\'s has been created and calculated, You can continue').'</div>';

		$html .= '
			<script>
			$(document).ready(function (){

				function checkCategorySelected()
				{
					if ($(\'#id_category_on:radio\').attr(\'checked\'))
					{
						$(\'#warn_category_level\').show();
						$(\'#checkAndSaveConfig\').attr(\'disabled\', \'disabled\');
						$(\'#checkAndSaveConfig\').hide();
					}
					else
					{
						$(\'#warn_category_level\').hide();
						$(\'#checkAndSaveConfig\').removeAttr(\'disabled\');
						$(\'#checkAndSaveConfig\').show();
					}

				}
				checkCategorySelected();
				$(\'input[name="getCategories"]\').change( function () {
					checkCategorySelected();
				});

			});

			function addAndCalculateLevel()
			{
				if (confirm(\''.$this->l('It is highly recommended to backup your database before proceeding. Did you make a backup?').'\'))
				{
					$(\'#loading\').show();
					$.ajax({
				       type: "GET",
				       url: "../modules/importerosc/ajax.php",
				       async: false,
				       cache: false,
				       dataType : "json",
				       data: "ajax=true&token='.sha1(_COOKIE_KEY_.'importosc').'&server="+$(\'#server\').val()+"&user="+$(\'#user\').val()+"&password="+$(\'#password\').val()+"&database="+$(\'#database\').val()+"&prefix="+$(\'#prefix\').val() ,
				       success: function (jsonData)
				       {
							if (jsonData.hasError)
	    						alert(jsonData.error);
	    					else
	    					{
	    						$(\'#warn_category_level\').remove();
	    						$(\'#checkAndSaveConfig\').removeAttr(\'disabled\');
								$(\'#checkAndSaveConfig\').show();
	    						$(\'#conf_category_level\').show();
	    					}
				       },
				      error: function (XMLHttpRequest, textStatus, errorThrown)
				       {

				       }
				   });
			   }
			   return false;
			}
			</script>';

		return $html;
	}

	public function createLevelAndCalculate()
	{
		if ($this->checkCategoriesLevel())
			die('{"hasError" : false}');

		if ($this->createLevel())
			$this->calculateLevel();
		else
			die('{"hasError" : true, "error" : "'.$this->l('Can not ALTER TABLE').'"}');
	}

	public function createLevel()
	{
		return $this->execute('ALTER TABLE `'.bqSQL($this->prefix).'categories` ADD `level` INT NOT NULL');
	}

	public function calculateLevel()
	{
		$this->updateLevel($this->getSubCat(0), 1);
		die('{"hasError" : false}');
	}

	public function updateLevel($ids_cat, $level = 1)
	{
		$this->execute('
			UPDATE `'.bqSQL($this->prefix).'categories`
			SET level = '.(int)$level.'
			WHERE categories_id IN ('.implode(',', $ids_cat).')');
		foreach($ids_cat as $id)
			if ($sub_cat = $this->getSubCat($id))
				$this->updateLevel($sub_cat, $level + 1);

	}

	public function getSubCat($id_parent)
	{
		$result = $this->executeS('SELECT `categories_id` FROM `'.bqSQL($this->prefix).'categories` WHERE `parent_id`='.(int)$id_parent);
		if (!is_array($result) OR empty($result))
			return false;
		return $this->formatCategoriesIds($result);
	}

	public function formatCategoriesIds($result)
	{
		$return = array();
		foreach($result as $key => $val)
			$return[] = $val['categories_id'];
		return $return;
	}
}

?>
