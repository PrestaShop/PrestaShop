<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class Criteo extends Module
{
	public function __construct()
	{
	 	$this->name = 'criteo';
	 	$this->tab = 'advertising_marketing';
	 	$this->version = '1.0';

	 	parent::__construct();

		$this->displayName = $this->l('Criteo');
		$this->description = $this->l('Criteo product export and tag display.');
	}

	public function install()
	{
        return (parent::install() AND $this->registerHook('header')
        				AND $this->registerHook('productfooter')
        				AND $this->registerHook('home')
        				AND $this->registerHook('shoppingCartExtra')
        				AND $this->registerHook('orderConfirmation'));
	}

	public function uninstall()
	{
		return parent::uninstall();
	}

	private function _postProcess()
	{
		$isUpdated = false;
		if (Tools::getValue('id_criteo_conversion'))
		{
			Configuration::updateValue('CRITEO_ID_CONVERSION', pSQL(Tools::getValue('id_criteo_conversion')));
			$isUpdated = true;
		}
		if (Tools::getValue('id_criteo_normal'))
		{
			Configuration::updateValue('CRITEO_ID_NORMAL', pSQL(Tools::getValue('id_criteo_normal')));
			$isUpdated = true;		
		}
		if (Tools::getValue('url_widget_criteo'))
		{
			Configuration::updateValue('CRITEO_URL_WIDGET', pSQL(Tools::getValue('url_widget_criteo')));
			$isUpdated = true;
		}
		if ($isUpdated)
			return true;
		return false;
	}

	public function getContent()
	{
    	return '
    	<fieldset>
    		'.($this->_postProcess() ? $this->displayConfirmation($this->l('Settings are updated').'<img src="http://www.prestashop.com/modules/criteo.png?normal_id='.urlencode(Tools::getValue('id_criteo_normal')).'&conversion_id='.Tools::getValue('id_criteo_conversion').'" style="float:right" />') : '').'
        	<legend><img src="'.$this->_path.'logo.gif" alt="" title=""/> '.$this->l('Criteo Export').'</legend>
            	<form method="post" action="" name="criteoForm">
         			<label for="id_normal_criteo">'.$this->l('Criteo normal identifier').' :</label>
         			<input type="text" value="'.Configuration::get('CRITEO_ID_NORMAL').'" name="id_criteo_normal" /><br /><br />
                	<label for="id_convertion_criteo">'.$this->l('Criteo conversion identifier').' :</label>
                	<input type="text" value="'.Configuration::get('CRITEO_ID_CONVERSION').'" name="id_criteo_conversion" /><br /><br />
                    <label for="url_widget_criteo">'.$this->l('URL Widget').' :</label>
                    <input type="text" value="'.Configuration::get('CRITEO_URL_WIDGET').'" name="url_widget_criteo" /><br /><br />
                    <center><input type="submit" class="button" name="submitCriteo" value="'.$this->l('Submit').'" /></center>
                </form>
              	'.$this->l('URL to communicate to Criteo:').'
                <a href="'.Tools::getProtocol().$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/'.$this->name.'/export_csv.php">'.Tools::getProtocol().$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/'.$this->name.'/export_csv.php</a><br />
                '.$this->l('URL to communicate to Criteo:').'
                    <a href="'.Tools::getProtocol().$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/'.$this->name.'/export_xml.php">'.Tools::getProtocol().$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/'.$this->name.'/export_xml.php</a>
       	</fieldset>';
	}

	private static function getAllSubCats(&$all_cats, $id_cat, $id_lang)
	{
		$category = new Category(intval($id_cat));

		$sub_cats = $category->getSubcategories($id_lang);
		if(count($sub_cats) > 0)
			foreach ($sub_cats AS $sub_cat)
			{
				$all_cats[] = $sub_cat['id_category'];
				self::getAllSubCats($all_cats, $sub_cat['id_category'], $id_lang);
			}
	}

	public static function buildCSV()
	{
		global $cookie, $country_infos;
		$cookie = new Cookie('ps');
		$country_infos = array('id_group' => 0, 'id_tax' => 1);
		$html = '';
		/* First line, columns */
		$columns = array('id', 'name', 'smallimage', 'bigimage', 'producturl', 'description',	'price', 'retailprice', 'discount', 'recommendable', 'instock');

		foreach ($columns AS $column)
			$html .= $column.'|';
		$html = rtrim($html, '|')."\n";

		/* Setting parameters */
		$conf = Configuration::getMultiple(array(
			'PS_REWRITING_SETTINGS',
			'PS_LANG_DEFAULT',
			'PS_SHIPPING_FREE_PRICE',
			'PS_SHIPPING_HANDLING',
			'PS_SHIPPING_METHOD',
			'PS_SHIPPING_FREE_WEIGHT',
			'PS_COUNTRY_DEFAULT',
			'PS_SHOP_NAME',
			'PS_CURRENCY_DEFAULT',
			'PS_CARRIER_DEFAULT'));
		/* Language */
		$language = new Language(intval($conf['PS_LANG_DEFAULT']));

		/* Link instance for products links */
		$link = new Link();
		$result = Db::getInstance()->ExecuteS('
		SELECT DISTINCT p.`id_product`, i.`id_image`
		FROM `'._DB_PREFIX_.'product` p
		JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.id_product = p.id_product)
		LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.id_product = p.id_product)
		WHERE p.`active` = 1 AND i.id_image IS NOT NULL 									
		GROUP BY p.id_product');
		foreach ($result AS $k => $row)
		{
			if (Pack::isPack(intval($row['id_product'])))
				continue;			
			$product = new Product(intval($row['id_product']), true);

			if (Validate::isLoadedObject($product))
			{
				$line = array();
				$line[] = $product->id;
				$line[] = $product->manufacturer_name.' - '.$product->name[intval($conf['PS_LANG_DEFAULT'])];
				$line[] = Tools::getProtocol().$_SERVER['HTTP_HOST']._THEME_PROD_DIR_.intval($product->id).'-'.$row['id_image'].'-small.jpg';
				$line[] = Tools::getProtocol().$_SERVER['HTTP_HOST']._THEME_PROD_DIR_.intval($product->id).'-'.$row['id_image'].'-thickbox.jpg';
				$line[] = $link->getProductLink(intval($product->id), $product->link_rewrite[intval($conf['PS_LANG_DEFAULT'])], $product->ean13).'&utm_source=criteo&aff=criteo';
				$line[] = str_replace(array("\n", "\r", "\t", '|'), '', strip_tags(html_entity_decode($product->description_short[intval($conf['PS_LANG_DEFAULT'])], ENT_COMPAT, 'UTF-8')));

				$price = $product->getPrice(true, intval(Product::getDefaultAttribute($product->id)));

				$line[] = number_format($price, 2, '.', '');
				$line[] = number_format($product->getPrice(true, intval(Product::getDefaultAttribute(intval($product->id))), 6, NULL, false, false), 2, '.', '');
				$line[] = $product->getPrice(true, NULL, 2, NULL, true);
				$line[] = '1';
				$line[] = '1';

				foreach ($line AS $column)
					$html .= trim($column).'|';
				$html = rtrim($html, '|')."\n";
			}
		}
		echo $html;
	}

	public static function buildXML()
	{
		global $cookie, $country_infos;

		$cookie = new Cookie('ps');
		$country_infos = array('id_group' => 0, 'id_tax' => 1);
		$html = '<products>'."\n";
		/* First line, columns */
		$columns = array('id', 'name', 'smallimage', 'bigimage', 'producturl', 'description',	'price', 'retailprice', 'discount', 'recommendable', 'instock');
		
		/* Setting parameters */
		$conf = Configuration::getMultiple(array(
			'PS_REWRITING_SETTINGS',
			'PS_LANG_DEFAULT',
			'PS_SHIPPING_FREE_PRICE',
			'PS_SHIPPING_HANDLING',
			'PS_SHIPPING_METHOD',
			'PS_SHIPPING_FREE_WEIGHT',
			'PS_COUNTRY_DEFAULT',
			'PS_SHOP_NAME',
			'PS_CURRENCY_DEFAULT',
			'PS_CARRIER_DEFAULT'));
		/* Language */
		$language = new Language(intval($conf['PS_LANG_DEFAULT']));

		/* Link instance for products links */
		$link = new Link();

		/* Searching for products */
		$result = Db::getInstance()->ExecuteS('
		SELECT DISTINCT p.`id_product`, i.`id_image`
		FROM `'._DB_PREFIX_.'product` p
		JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.id_product = p.id_product)
		LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.id_product = p.id_product)
		WHERE p.`active` = 1 AND i.id_image IS NOT NULL
		GROUP BY p.id_product');

		foreach ($result AS $k => $row)
		{
			if (Pack::isPack(intval($row['id_product'])))
				continue;			
			$product = new Product(intval($row['id_product']), true);

			if (Validate::isLoadedObject($product))
			{
				$line = array();
				$line[] = $product->manufacturer_name.' - '.$product->name[intval($conf['PS_LANG_DEFAULT'])];

				$line[] = Tools::getProtocol().$_SERVER['HTTP_HOST']._THEME_PROD_DIR_.intval($product->id).'-'.$row['id_image'].'-small.jpg';
				$line[] = Tools::getProtocol().$_SERVER['HTTP_HOST']._THEME_PROD_DIR_.intval($product->id).'-'.$row['id_image'].'-thickbox.jpg';
				$line[] = $link->getProductLink(intval($product->id), $product->link_rewrite[intval($conf['PS_LANG_DEFAULT'])], $product->ean13).'&utm_source=criteo&aff=criteo';
				$line[] = str_replace(array("\n", "\r", "\t", '|'), '', strip_tags(html_entity_decode($product->description_short[intval($conf['PS_LANG_DEFAULT'])], ENT_COMPAT, 'UTF-8')));

				$price = $product->getPrice(true, intval(Product::getDefaultAttribute($product->id)));

				$line[] = number_format($price, 2, '.', '');
				$line[] = number_format($product->getPrice(true, intval(Product::getDefaultAttribute(intval($product->id))), 6, NULL, false, false), 2, '.', '');
				$line[] = $product->getPrice(true, NULL, 2, NULL, true);
				$line[] = '1';
				$line[] = '1';

				$cptXML = 1;
				$html .= "\t".'<product id="'.$product->id.'">'."\n";
				foreach ($line AS $column)
				{
					$html .= "\t\t".'<'.$columns[$cptXML].'>'.$column.'</'.$columns[$cptXML].'>'."\n";
					$cptXML++;
				}
				$html .= "\t".'</product>'."\n";
			}
		}
		$html .= '</products>'."\n";
		echo $html;
	}

	public function hookProductFooter($params)
	{
		return '<script type="text/javascript">
		//<![CDATA[
		document.write(\'<div id="cto_se_'.Configuration::get('CRITEO_ID_NORMAL').'_ac" style="display:none">\\
						<div class="ctoWidgetServer">'.Configuration::get('CRITEO_URL_WIDGET').'</div>\\
						<div class="ctoDataType">sendEvent</div>\\
						<div class="ctoParams">wi='.Configuration::get('CRITEO_ID_NORMAL').'&pt1=2&i='.$params['product']->id.'</div>\\
						</div>\');
		//]]>
		</script>';
	}

	public function hookProductList($params)
	{
		$i = 0;
		$strproducts = '';
		
		if (!isset($params['products']) OR !$params['products'])
			return '';
		
		foreach ($params['products'] AS $product)
		{
			if ($i > 2)
				break;

			$strproducts .= 'i'.++$i.'='.$product['id_product'].'&';
		}
		return	'<script type="text/javascript">
		//<![CDATA[
		document.write(\'<div id="cto_tr_'.Configuration::get('CRITEO_ID_NORMAL').'_ac" style="display:none">\\
						<div class="ctoWidgetServer">'.Configuration::get('CRITEO_URL_WIDGET').'</div>\\
						<div class="ctoDataType">sendEvent</div>\\
						<div class="ctoParams">pt1=3&wi='.Configuration::get('CRITEO_ID_NORMAL').'&'.rtrim($strproducts, '&').'</div>\\
						</div>\');
		//]]>
		</script>';
		
	}
	
	public function hookShoppingCartExtra($params)
	{
		foreach ($params['products'] AS $product)
			$strproducts .= 'i'.++$i.'='.$product['id_product'].'&p'.$i.'='.$product['price'].'&q'.$i.'='.$product['quantity'].'&';

		$strproducts = substr($strproducts, 0, -1);

		return	'<script type="text/javascript">
		//<![CDATA[
		document.write(\'<div id="cto_tr_'.Configuration::get('CRITEO_ID_CONVERSION').'_ac" style="display:none">\\
						<div class="ctoWidgetServer">'.Configuration::get('CRITEO_URL_WIDGET').'</div>\\
						<div class="ctoDataType">transaction</div>\\
						<div class="ctoParams">wi='.Configuration::get('CRITEO_ID_CONVERSION').'&s=0&'.rtrim($strproducts, '&').'</div>\\
						</div>\');
		//]]>
		</script>';
	}
	
	public function hookOrderConfirmation($params)
	{
			global $cookie, $country_infos;
			$cookie = new Cookie('ps');
		$country_infos = array('id_group' => 0,
								'id_tax' => 1);
		$cart = new Cart(intval($params['objOrder']->id_cart));
		$products = $cart->getProducts();
		$strproducts = '';
		$i = 0;
		foreach ($products AS $product)
			$strproducts .= 'i'.++$i.'='.$product['id_product'].'&p'.$i.'='.$product['price'].'&q'.$i.'='.$product['quantity'].'&';

		$strproducts = substr($strproducts, 0, -1);

		return	'<script type="text/javascript">
		//<![CDATA[
		document.write(\'<div id="cto_tr_'.Configuration::get('CRITEO_ID_CONVERSION').'_ac" style="display:none">\\
						<div class="ctoWidgetServer">'.Configuration::get('CRITEO_URL_WIDGET').'</div>\\
						<div class="ctoDataType">transaction</div>\\
						<div class="ctoParams">wi='.Configuration::get('CRITEO_ID_CONVERSION').'&t='.intval($params['objOrder']->id).'&s=1&'.rtrim($strproducts, '&').'</div>\\
						</div>\');
		//]]>
		</script>';
	}

	public function hookHeader($params)
	{
		return '<script type="text/javascript" src="http://ld2.criteo.com/criteo_ld.js"></script>';
	}
	
	public function hookHome($params)
	{
		return '<script type="text/javascript">
			//<![CDATA[
			document.write(\'<div id="cto_se_'.Configuration::get('CRITEO_ID_NORMAL').'_ac" style="display:none">\\
							<div class="ctoWidgetServer">'.Configuration::get('CRITEO_URL_WIDGET').'</div>\\
							<div class="ctoDataType">sendEvent</div>\\
							<div class="ctoParams">wi='.Configuration::get('CRITEO_ID_NORMAL').'&pt1=0&pt2=1</div>\\
							</div>\');
			//]]>
		</script>';
	}
}

