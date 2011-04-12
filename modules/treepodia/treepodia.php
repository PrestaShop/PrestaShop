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

class SimpleXMLExtended extends SimpleXMLElement
{
	public function addCData($nodename, $cdata_text)
	{
		$node = $this->addChild($nodename);
		$node = dom_import_simplexml($node);
		$no = $node->ownerDocument;
		$node->appendChild($no->createCDATASection($cdata_text));
	}
}

class Treepodia extends Module
{
	private $allowed_extensions = array('.png','.gif','.jpg');

	function __construct()
	{
	 	$this->name = 'treepodia';
	 	$this->tab = 'front_office_features';
	 	$this->version = '1.2';
		$this->displayName = 'Treepodia';

	 	parent::__construct();
		$this->description = $this->l('Cover your entire catalog with product videos in 24 hours.');

		if ($this->active && !Configuration::get('TREEPODIA_ACCOUNT_CODE'))
			$this->warning = $this->l('You have not set your Treepodia configuration.');
	}

	function install()
	{
		if (!parent::install())
			return false;

		$this->registerHook('extraLeft');
		$this->registerHook('footer');
		$this->registerHook('orderConfirmation');

		$token = Configuration::get('TREEPODIA_TOKEN') ? Configuration::get('TREEPODIA_TOKEN') : Tools::passwdGen(16);
		$type = Configuration::get('TREEPODIA_INTEGRATION_TYPE') ? Configuration::get('TREEPODIA_INTEGRATION_TYPE') : 0;
		$logo = Configuration::get('TREEPODIA_PLAY_LOGO') ? Configuration::get('TREEPODIA_PLAY_LOGO') : '4-7.png';
		$position = Configuration::get('TREEPODIA_POSITION') ?  Configuration::get('TREEPODIA_POSITION') : 1;
		$hook = Configuration::get('TREEPODIA_HOOK') ?  Configuration::get('TREEPODIA_HOOK') : 0;

		Configuration::updateValue('TREEPODIA_TOKEN', $token);
		Configuration::updateValue('TREEPODIA_INTEGRATION_TYPE', $type);
		Configuration::updateValue('TREEPODIA_PLAY_LOGO', $logo);
		Configuration::updateValue('TREEPODIA_POSITION', $position);
        Configuration::updateValue('TREEPODIA_HOOK', $hook);

		return true;
	}

	private function _getShopURL()
	{
		return ((Configuration::get('PS_SSL_ENABLED') OR (isset($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) != 'off')) ? Tools::getShopDomainSsl() : Tools::getShopDomain()).__PS_BASE_URI__;
	}

	public function generateXmlFlow()
	{
		global $cart, $cookie;
		$cookie->id_lang = (int)(Configuration::get('PS_LANG_DEFAULT')); // url rewriting case

		$cart = new Cart();
		$link = new Link();
		$defaultCurrencyIsoCode = strtoupper(Db::getInstance()->getValue('SELECT c.iso_code FROM '._DB_PREFIX_.'currency c WHERE c.id_currency = '.(int)(Configuration::get('PS_CURRENCY_DEFAULT'))));
		$defaultIdLang = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$hasImageLink = method_exists($link, 'getImageLink');

		$sqlLangs = Db::getInstance()->ExecuteS('SELECT l.id_lang, l.iso_code FROM '._DB_PREFIX_.'lang l');

		foreach ($sqlLangs AS $sqlLang)
		{
			$langs[$sqlLang[ 'id_lang' ]] = $sqlLang[ 'iso_code' ] ;
		}


		$xmlString = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<products>
</products>
XML;

		$products = new SimpleXMLExtended($xmlString);

		$infos = $products->addChild('infos');
		$infos->addCData('url', $this->_getShopURL());
		$infos->addCData('logo', $this->_getShopURL());

		$languages = Db::getInstance()->ExecuteS('
		SELECT l.iso_code
		FROM '._DB_PREFIX_.'lang l
		WHERE l.active = 1');

		foreach ($languages AS $language)
			$infos->addChild('lang', $language['iso_code']);

		$sqlProducts = Db::getInstance()->ExecuteS('
		SELECT p.id_product, p.reference, p.weight, m.name manufacturer, s.name supplier, p.on_sale, p.id_manufacturer, pd.id_product_download
		FROM '._DB_PREFIX_.'product p
		LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
		LEFT JOIN '._DB_PREFIX_.'supplier s ON (s.id_supplier = p.id_supplier)
		LEFT JOIN '._DB_PREFIX_.'product_download pd ON (pd.id_product = p.id_product)
		WHERE p.active = 1');

		foreach ($sqlProducts AS $sqlProduct)
		{
			$id_product = $sqlProduct['id_product'] ;

			$product = $products->addChild('product') ;
			$product->addChild('sku', (int)($sqlProduct['id_product']));
			$product->addCData('manufacturer', $sqlProduct['manufacturer']);
			if (file_exists(dirname(__FILE__).'/../../img/m/'.(int)($sqlProduct['id_manufacturer']).'jpg'))
				$product->addCData('manufacturer-logo', $_SERVER['HTTP_HOST'].__PS_BASE_URI__.'img/m/'.(int)($sqlProduct['id_manufacturer']).'jpg');
			$product->addChild('weight', (float)($sqlProduct['weight']));
			$product->addChild('weight_unit', strtolower(Configuration::get('PS_WEIGHT_UNIT')));
			$product->addCData('supplier', $sqlProduct['supplier']);
			$name = $product->addChild('name');
			$languageVariant = $name->addChild('language-variant');

			$texts = Db::getInstance()->ExecuteS('
			SELECT pl.name, pl.description_short, pl.link_rewrite, l.iso_code, l.id_lang
			FROM '._DB_PREFIX_.'product_lang pl
			LEFT JOIN '._DB_PREFIX_.'lang l ON (l.id_lang = pl.id_lang)
			WHERE pl.id_product = '.(int)($sqlProduct['id_product']));

			foreach ($texts AS $text)
			{
				$variant = $languageVariant->addChild('variant');
				$variant->addChild('locale', $text['iso_code']);
				$variant->addCData('value', $text['name']);
				if ($text['id_lang'] == $defaultIdLang)
				{
					$productName = $text['name'];
					$productLinkRewrite = $text['link_rewrite'];
				}
			}

			$product->addCData('page-url', $link->getProductLink((int)($sqlProduct['id_product']), $productLinkRewrite));
			$shortDescription = $product->addChild('short-description');
			$languageVariant = $shortDescription->addChild('language-variant');

			foreach ($texts AS $text)
			{
				$variant = $languageVariant->addChild('variant');
				$variant->addChild('locale', $text['iso_code']);
				$variant->addCData('value', Tools::htmlentitiesDecodeUTF8(strip_tags($text['description_short'])));

			}

			$accessories = Db::getInstance()->ExecuteS('
			SELECT a.id_product_2
			FROM '._DB_PREFIX_.'accessory a
			WHERE a.id_product_1 = '.(int)($sqlProduct['id_product']));

			foreach ($accessories AS $accessory)
				$product->addChild('accessory-sku', (int)($accessory['id_product_2']));

			$price = $product->addChild('price');
			$price->addChild('currency', $defaultCurrencyIsoCode);
			$price->addChild('retail-price-with-tax', Product::getPriceStatic((int)($sqlProduct['id_product']), true, NULL, 6, NULL, false, false));
			$price->addChild('retail-price-without-tax', Product::getPriceStatic((int)($sqlProduct['id_product']), false, NULL, 6, NULL, false, false));
			$price->addChild('final-retail-price-with-tax', Product::getPriceStatic((int)($sqlProduct['id_product']), true));
			$price->addChild('final-retail-price-without-tax', Product::getPriceStatic((int)($sqlProduct['id_product']), false, NULL, 6, NULL, false, true, 1, false, NULL, NULL, NULL, $specificPrice));
			$price->addChild('reduction_percent', ($specificPrice AND $specificPrice['reduction_type'] == 'percentage') ? $specificPrice['reduction'] * 100 : 0.00);
			$price->addChild('reduction_price', ($specificPrice AND $specificPrice['reduction_type'] == 'amount') ? (float)($specificPrice['reduction']) : 0.00);
			$price->addChild('display-on-sale', (int)($sqlProduct['on_sale']));

			$product->addChild('downloadable', $sqlProduct['id_product_download'] >= 1 ? 1 : 0);

			$pack = Db::getInstance()->ExecuteS('
			SELECT p.id_product, pp.quantity
			FROM '._DB_PREFIX_.'pack pp
			LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = pp.id_product_item)
			WHERE pp.id_product_pack = '.(int)($sqlProduct['id_product']));

			if (sizeof($pack))
			{
				$pack = $product->addChild('pack');
				foreach ($pack AS $p)
				{
					$packItem = $pack->addChild('pack-item');
					$packItem->addChild('quantity', (int)($p['quantity']));
					$packItem->addChild('sku', (int)($p['id_product']));
				}
			}

			$images = Db::getInstance()->ExecuteS('
			SELECT i.id_image, il.legend, l.iso_code
			FROM '._DB_PREFIX_.'image i
			LEFT JOIN '._DB_PREFIX_.'image_lang il ON (il.id_image = i.id_image)
			LEFT JOIN '._DB_PREFIX_.'lang l ON (l.id_lang = il.id_lang)
			WHERE i.id_product = '.(int)($sqlProduct['id_product']));

			$imagesLegends = array();
			foreach ($images AS $image)
			{
				$imagesLegends[(int)($image['id_image'])][$image['iso_code']]['legend'] = $image['legend'];
				$imagesLegends[(int)($image['id_image'])][$image['iso_code']]['iso_code'] = $image['iso_code'];
			}

			$imagesAlreadyDone = array();
			foreach ($images AS $imageSQL)
			{
				if (isset($imagesAlreadyDone[$imageSQL['id_image']]))
					continue;
				else
					$imagesAlreadyDone[(int)($imageSQL['id_image'])] = 1;


				global $cookie;
				$cookie->id_lang = Configuration::get('PS_LANG_DEFAULT');

				$image = $product->addChild('image');
				$image->addAttribute('id',$imageSQL['id_image']);
				if ($hasImageLink)
					$image->addCData('image-url', $link->getImageLink($productLinkRewrite, (int)($sqlProduct['id_product']).'-'.(int)($imageSQL['id_image']), 'large'));
				/* For 1.0/1.1 compatibility purpose only */
				else
					$image->addCData('image-url', $this->_getImageLink($productLinkRewrite, (int)($sqlProduct['id_product']).'-'.(int)($imageSQL['id_image']), 'large'));

				if (isset($imagesLegends[$imageSQL['id_image']]) AND sizeof($imagesLegends[$imageSQL['id_image']]))
				{
					$imageCaption = $image->addChild('image-caption');
					$languageVariant = $imageCaption->addChild('language-variant');

					foreach ($imagesLegends[(int)($imageSQL['id_image'])] AS $legend)
					{
						$variant = $languageVariant->addChild('variant');
						$variant->addChild('locale', $legend['iso_code']);
						$variant->addCData('value', $legend['legend']);

					}
				}
			}

			$quantityDiscounts = SpecificPrice::getQuantityDiscounts((int)($sqlProduct['id_product']), (int)(Shop::getCurrentShop()), 0, 0, 0);

			foreach ($quantityDiscounts AS $quantityDiscount)
			{
				$discount = $product->addChild('discount');
				$discount->addChild('discount-quantity', (int)($quantityDiscount['from_quantity']));
				$discount->addChild('discount-value', ((float)($quantityDiscount['price']) AND $quantityDiscount['reduction_type'] == 'amount') ? (float)($quantityDiscount['price']) : $quantityDiscount['reduction'] * 100);
				$discount->addChild('discount-type', ($quantityDiscount['reduction_type'] == 'amount' ? $defaultCurrencyIsoCode : '%'));
			}

			$categories = Db::getInstance()->ExecuteS('
			SELECT cl.name, l.iso_code
			FROM '._DB_PREFIX_.'category_product cp
			LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = cp.id_category)
			LEFT JOIN '._DB_PREFIX_.'lang l ON (l.id_lang = cl.id_lang)
			WHERE cp.id_product = '.(int)($sqlProduct['id_product']));

			if (sizeof($categories))
			{
				$category = $product->addChild('category');
				$languageVariant = $category->addChild('language-variant');

				foreach ($categories AS $category)
				{
					$variant = $languageVariant->addChild('variant');
					$variant->addChild('locale', $category['iso_code']);
					$variant->addCData('value', $category['name']);
				}
			}

			$tags = Db::getInstance()->ExecuteS('
			SELECT pt.id_product, pt.id_tag, l.iso_code, t.name
			FROM '._DB_PREFIX_.'product_tag pt
			LEFT JOIN '._DB_PREFIX_.'tag t ON (t.id_tag = pt.id_tag)
			LEFT JOIN '._DB_PREFIX_.'lang l ON (l.id_lang = t.id_lang)
			WHERE pt.id_product = '.(int)($sqlProduct['id_product']));


			if (!empty($tags) && sizeof($tags) > 0)
			{
				$tagsTexts = array();
				$tagsIso = array();
				foreach ($tags AS $tag)
				{
					if (!in_array($tag['iso_code'], $tagsIso))
						$tagsIso[] = $tag['iso_code'];

					$tagsTexts[$tag['iso_code']][] = $tag['name'];
				}

				$tags_item = $product->addChild('tags');

				foreach ($tagsIso AS $iso)
				{
					$languageVariant = $tags_item->addChild('language-variant');
					$languageVariant->addChild('locale', $iso);
					$languageVariant->addCData('value', implode(',', $tagsTexts[$iso]));
				}
			}

			$groupAttributes = Db::getInstance()->ExecuteS('
			SELECT DISTINCT agl.id_attribute_group, agl.name, l.iso_code, a.id_attribute, al.name as attribute_name, al.id_lang, pa.id_product_attribute
			FROM '._DB_PREFIX_.'attribute_group_lang agl
			LEFT JOIN '._DB_PREFIX_.'attribute a ON (a.id_attribute_group = agl.id_attribute_group)
			LEFT JOIN '._DB_PREFIX_.'attribute_lang al ON (al.id_attribute = a.id_attribute)
			LEFT JOIN '._DB_PREFIX_.'lang l ON (l.id_lang = al.id_lang)
			LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON (pac.id_attribute = a.id_attribute)
			LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product_attribute = pac.id_product_attribute)
			WHERE pa.id_product = '.(int)($sqlProduct['id_product']) .'
			GROUP BY a.id_attribute, l.iso_code');

			$groups = array() ;
			foreach ($groupAttributes AS $groupAttribute)
			{
				$id_group_attribute = $groupAttribute[ 'id_attribute_group' ] ;
				$id_attribute = $groupAttribute[ 'id_attribute' ] ;
				$id_product_attribute = $groupAttribute[ 'id_product_attribute' ] ;

				$groups[$id_group_attribute][ 'name' ][$groupAttribute[ 'iso_code' ]] = $groupAttribute[ 'name' ] ;
				$groups[$id_group_attribute][ 'attributes' ][$groupAttribute[ 'id_attribute' ]][$groupAttribute[ 'iso_code' ]] = $groupAttribute[ 'attribute_name' ] ;
			}

			if(!empty($groups))	{
				foreach ($groups AS $id_group => $group)
				{
					$xml_group = $product->addChild('attribute-group');
					$xml_group->addAttribute('id', $id_group);

					if(!empty($group[ 'name' ]))	{
						$nameGroup = $xml_group->addChild('name');
						$languageVariant = $nameGroup->addChild('language-variant');

						foreach ($group[ 'name' ] AS $iso2 => $name_group)
						{
								$variant = $languageVariant->addChild('variant');
								$variant->addChild('locale', $iso2);
								$variant->addCData('value', $name_group);
						}
					}

					if(!empty($group[ 'attributes' ]))	{
						foreach ($group[ 'attributes' ] AS $id_attribute => $attribute)
						{
							$xml_attribute = $xml_group->addChild('attribute');
							$xml_attribute->addAttribute('id', $id_attribute);
							$languageVariant = $xml_attribute->addChild('language-variant');

							foreach ($attribute AS $iso2 => $name_attribute)
							{
								$variant = $languageVariant->addChild('variant');
								$variant->addChild('locale', $iso2);
								$variant->addCData('value', $name_attribute);
							}
						}
					}

				}
			 }

			 $groupAttributes = Db::getInstance()->ExecuteS('
			SELECT agl.id_attribute_group, agl.name, l.iso_code, a.id_attribute, al.name attribute_name, al.id_lang, pa.id_product_attribute
			FROM '._DB_PREFIX_.'attribute_group_lang agl
			LEFT JOIN '._DB_PREFIX_.'lang l ON (l.id_lang = agl.id_lang)
			LEFT JOIN '._DB_PREFIX_.'attribute a ON (a.id_attribute_group = agl.id_attribute_group)
			LEFT JOIN '._DB_PREFIX_.'attribute_lang al ON (al.id_attribute = a.id_attribute)
			LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON (pac.id_attribute = a.id_attribute)
			LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product_attribute = pac.id_product_attribute)
			WHERE pa.id_product = '.(int)($sqlProduct['id_product']));

			$combinaison = array() ;

			foreach ($groupAttributes AS $groupAttribute)
			{
				$id_group_attribute = $groupAttribute[ 'id_attribute_group' ] ;
				$id_attribute = $groupAttribute[ 'id_attribute' ] ;
				$id_product_attribute = $groupAttribute[ 'id_product_attribute' ] ;
				$combinaison[$id_product_attribute][$id_group_attribute] = $id_attribute ;
			}

			$productAttributes = Db::getInstance()->ExecuteS('
			SELECT pa.id_product_attribute, pa.weight, pa.quantity, pi.id_image
			FROM '._DB_PREFIX_.'product_attribute pa
			LEFT JOIN '._DB_PREFIX_.'product_attribute_image pi ON (pa.id_product_attribute = pi.id_product_attribute)
			WHERE pa.id_product = '.(int)($sqlProduct['id_product']));

			if(!empty($productAttributes))	{
				foreach ($productAttributes AS $productAttribute)
				{
					$id_product_attribute = (int)($productAttribute['id_product_attribute']) ;

					$attributeCombination = $product->addChild('attribute-combination');
					$attributeCombination->addAttribute('id', $id_product_attribute);
					$attributeCombination->addChild('weight', (float)($sqlProduct['weight'] + $productAttribute['weight']));
					$attributeCombination->addChild('final-retail-price-with-tax', Product::getPriceStatic((int)($sqlProduct['id_product']), true, $id_product_attribute));
					$attributeCombination->addChild('final-retail-price-without-tax', Product::getPriceStatic((int)($sqlProduct['id_product']), false, $id_product_attribute));
					$attributeCombination->addChild('quantity', $productAttribute['quantity'] );

					if (isset($productAttribute['id_image']) && !empty($productAttribute['id_image']))
					{
						$image = $attributeCombination->addChild('image');
						$image->addAttribute('ref-id', $productAttribute['id_image']);
					}

					if( isset($combinaison[$id_product_attribute]) && !empty($combinaison[$id_product_attribute]) )	{

						foreach ($combinaison[$id_product_attribute] AS $id_group_attribute => $id_attribute)
						{
							$attribute = $attributeCombination->addChild('attribute');
							$attribute->addAttribute('group-ref-id', $id_group_attribute);
							$attribute->addAttribute('ref-id', $id_attribute);
						}
					}
				}
			}

		}

		echo $products->asXML();
	}


	private function _displayCSSAndJS()
	{
		return '<style>
					.logo-item { border: 0px solid black; width: 70px; height: 70px; float: left; display: block; text-align: center; background-color: #FFFFFF; margin:  5px;}
					.logo {  cursor: pointer; }
					#logo-selector { border: 1px solid #DFD5C3; text-align: center; margin-top: 8px;}
					#selector label { text-align: left; width: auto; margin: 0px; padding; 0px; }
					#change-logo {color: rgb(38, 140, 205); text-decoration: underline; font-size: 0.8em; }
					#current { font-weight: bold; }
					#treepodia-logo { float: left; }
					.position-title { font-weight: bold; }
					.position { margin-right: 5px; margin-top: 5px; text-align: left; display: block; float: left; }
					#fake-product-picture { padding: 2px; padding-top: 100px; width: 300px; height: 120px; text-align: center; background-color: #FFF6D3; border: 1px solid #DFD5C3; font-size: 25px; color: #268CCD; }
					.position-label { display: block; float: left; text-align: left; width: 100px; }
					.link-trpd { color: #268CCD; font-weight: bold; text-decoration: underline; }
				</style>
				<script type="text/javascript" src="'._MODULE_DIR_.$this->name.'/treepodia.js"></script>';
	}

	private function _displayImageSelector()
	{
		$max_width = 60;
		$max_height = 60;
		$dir = _PS_MODULE_DIR_.$this->name.'/logos/';

		$out =  '<div id="selector">'.
				$this->l('Select a picture below or upload a new one.').'<br />
				<label>'.$this->l('New logo:').' </label>
				<input type="file" name="trpd_logo_file" />
				<div id="logo-selector">';

		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					$filename = $dir.$file;

					if (is_file($filename) AND in_array(substr($filename, -4), $this->allowed_extensions))
					{
						list($width, $height) = getimagesize($filename);

						if ($width > $height)
						{
							if ($width > $max_width)
							{
								$ratio = $width / $max_width;
								$width = $max_width;
								$height = $height / $ratio;
							}
						} else {
							if ($height > $max_height)
							{
								$ratio = $height / $max_height;
								$height = $max_height;
								$width = $width / $ratio;
							}
						}

						$out .= '<div class="logo-item"><img class="logo" onclick="setPreview(\''._MODULE_DIR_.$this->name.'/logos/'.$file.'\',\''. $file.'\')" src="'._MODULE_DIR_.$this->name.'/logos/'.$file.'" width="'.$width.'" height="'.$height.'" alt="" /></div>';
					}
				}

				closedir($dh);
			}
		}
		$out .= '<div class="clear"></div>
				</div>
				</div>';

		return $out;
	}

	public function displayForm()
	{
		global $cookie;

		$lang = new Language((int)($cookie->id_lang));

		$output = $this->_displayCSSAndJS().'<h2>'.$this->displayName.'</h2>
		<img id="treepodia-logo" src="'.__PS_BASE_URI__.'modules/'.$this->name.'/logo.png'.'" alt="" />
		<p>'.$this->l('It is well known that product videos increase sales, customer engagement and loyalty, and enhance your shoppers experience dramatically. They also enhance your shoppers\' overall experience and have a strong positive effect on your search engine visibility.').'. <a target="_blank" class="link-trpd" href="http://www.prestashop.com/en/partner_treepodia">'.$this->l('Learn more').'</a></p><div class="clear" /><br />';


		$output .= '<script type="text/javascript">
					function redirectToTreepodia()
					{
						var url = \'https://www.treepodia.com/prestashop/api/register?token='.Configuration::get('TREEPODIA_TOKEN').'&lang='.$lang->iso_code.'&prestashop-api='.$this->_getShopURL().'modules/treepodia/api\';
						window.open(url);
					}

					function signInTripodia()
					{
						var url = \'https://www.treepodia.com/prestashop/api/sign-in?token='.Configuration::get('TREEPODIA_TOKEN').'&lang='.$lang->iso_code.'&prestashop-api='.$this->_getShopURL().'modules/treepodia/api\';
						window.open(url);
					}
					</script>
					<fieldset>
					<legend><img src="'.__PS_BASE_URI__.'modules/'.$this->name.'/logo.gif" alt="" />'.$this->l('My Treepodia account').'</legend>';

		if (Configuration::get('TREEPODIA_ACCOUNT_CODE') == NULL)
		{
			$output .= '<p style="width: 75%; float: left; margin-right: 20px;">'.$this->l('You are not yet registered for a Treepodia video account. Please register with Treepodia.').'</p>
			<p style="width: 10%; float: left;"><input type="button" class="button" value="'.$this->l('Registration').'" onclick="redirectToTreepodia();" style="padding: 15px; font-weight: bold; font-size: 13px;"></p>
			<div style="clear: both;"></div>';

		}
		else
		{
			$output .= '<label>'.$this->l('Video Account Code').'</label>
						<div class="margin-form">
							<input type="text" style="width: 160px;" value="'.Tools::htmlentitiesUTF8(Configuration::get('TREEPODIA_ACCOUNT_CODE')).'" readonly/><br >'
							.$this->l('Your video account code uniquely identifies your store and was assigned to you automatically.').
						'</div><br />
						<p>
							'.$this->l('Sign in to your video account to change your preferences, settings and see your business performance reports.').' <input type="button" class="button" value="'.$this->l('Sign in').'" onclick="signInTripodia();" style="padding: 15px; font-weight: bold; font-size: 13px;">
						</p>';
		}

		$output .= '</fieldset><br />';

		if (Configuration::get('TREEPODIA_ACCOUNT_CODE') != NULL)
        {
			$output .= '
			<fieldset>
				<legend><img src="'.__PS_BASE_URI__.'modules/'.$this->name.'/logo.gif" alt="" />'.$this->l('Settings').'</legend>
				<form method="post" action="'.$_SERVER['REQUEST_URI'].'" enctype="multipart/form-data">
					<input type="radio" name="trpd_integration_type" value="0" style="vertical-align: middle;" '.((int)(Configuration::get('TREEPODIA_INTEGRATION_TYPE')) == 0 ? 'checked' : '').'/> <label style="font-size: 14px; color: #268CCD; float: none;">'.$this->l('Use built-in integration').'</label>
					<p>'.$this->l('The built-in integration automatically embeds Treepodia integration code into your store and displays a link to the video on your product page.').'</p><br />
					<p id="current">'.$this->l('Current logo:').'<br />
					<div id="preview-container">
					<img id="preview-logo" alt="" src="'._MODULE_DIR_.$this->name.'/logos/'.Tools::htmlentitiesUTF8(Configuration::get('TREEPODIA_PLAY_LOGO')).'" /><br />

					<input type="hidden" name="trpd_play_logo" id="trpd_play_logo" value="'.Configuration::get('TREEPODIA_PLAY_LOGO').'" /></div>
					<a id="change-logo" href="" >'.$this->l('Change logo').'</a></p>

						'.$this->_displayImageSelector().'
					<br /><br />

					<div>
				    <p id="current">'.$this->l('Display logo:').'<br />
				    <select name="trpd_hook_position">
				        <option value="0" '.(Configuration::get('TREEPODIA_HOOK') == 0 ? 'selected="selected"' : '' ).'>'.$this->l('Above Useful links').'</option>
  				        <option value="1" '.(Configuration::get('TREEPODIA_HOOK') == 1 ? 'selected="selected"' : '' ).'>'.$this->l('Above More-Info').'</option>
				    </select>
					<p class="position-title">'.$this->l('Place logo here:').'</p>
					<div id="fake-product-picture">'.$this->l('Product picture').'</div>
					<input type="radio" id="left" class="position" name="trpd_position" value="0" '.((int)(Configuration::get('TREEPODIA_POSITION')) == 0 ? 'checked' : '' ).'/><label class="position-label" for="left">'.$this->l('Left').'</label>
					<input type="radio" id="center" class="position" name="trpd_position" value="1" '.((int)(Configuration::get('TREEPODIA_POSITION')) == 1 ? 'checked' : '' ).'/><label class="position-label" for="center">'.$this->l('Center').'</label>
					<input type="radio" id="right" class="position" name="trpd_position" value="2" '.((int)(Configuration::get('TREEPODIA_POSITION')) == 2 ? 'checked' : '' ).'/><label class="position-label" for="right">'.$this->l('Right').'</label>
					</div>
					<div class="clear" />
					<br />
					<input type="radio" name="trpd_integration_type" value="1" style="vertical-align: middle;" '.((int)(Configuration::get('TREEPODIA_INTEGRATION_TYPE')) == 1 ? 'checked' : '').' /> <label style="font-size: 14px; color: #268CCD; float: none;">'.$this->l('Or, implement your own integration').'</label>
					<p style="margin-bottom: 30px;">'.$this->l('Use this option if you wish to implement the Treepodia integration code on your own. The built-in integration code will be deactivated if you choose this option.').' <a target="_blank" href="https://www.treepodia.com/prestashop/api/alternative-integration" style="color: rgb(38, 140, 205); text-decoration: underline;">'.$this->l('Read here how to implement Treepodia integration code on your own.').'</a></p>
					<center><input type="submit" class="button" name="submitTreepodia" value="'.$this->l('Save settings').'" /></center>
				</form>
			</fieldset>';
		}

		return $output;
	}

	public function getContent()
	{

		$out = '';

		if (Tools::isSubmit('submitTreepodia'))
		{
			$errors = $this->_validateForm();

			if (empty($errors))
			{
				$out .= $this->_postProcess();
			}
			else
				$out .= $errors;
		}

		$out .= $this->displayForm();

		return $out;
	}

	private function _validateForm()
	{
		$errors = '';
		if (Tools::getValue('trpd_integration_type') != 0 AND Tools::getValue('trpd_integration_type') != 1)
			$errors .= $this->displayError('Invalid Integration Type');

		$file = $_FILES['trpd_logo_file'];

		if ($file AND !empty($file['name']))
		{
			if ($file['error'])
			{
				switch ($file['error'])
				{
					case 1:
						$errors .= $this->displayError('The file is too large.');
						break;

                   case 2:
						$errors .= $this->displayError('The file is too large.');
						break;

					case 3:
						$errors .= $this->displayError('The file was partialy uploaded');
						break;

					case 4:
						$errors .= $this->displayError('The file is empty');
						break;
				}
			}

			if (!in_array(substr($file['name'], -4), $this->allowed_extensions))
				$errors .= $this->displayError($this->l('Invalid file type'));
		} else {
			if (!is_file(_PS_MODULE_DIR_.$this->name.'/logos/'.Tools::getValue('trpd_play_logo')))
				$errors .= $this->displayError('Invalid logo');
		}

		return $errors;
	}

	private function _postProcess()
	{
		Configuration::updateValue('TREEPODIA_INTEGRATION_TYPE', (int)(Tools::getValue('trpd_integration_type')));

		$position = Tools::getValue('trpd_position');
		if ($position < 0 || $position > 2) $position = 0;

		Configuration::updateValue('TREEPODIA_POSITION', $position);

		$file = $_FILES['trpd_logo_file'];
		if ($file AND $file['name'])
		{
			$name = time().'-'.$_FILES['trpd_logo_file']['name'];
			move_uploaded_file($_FILES['trpd_logo_file']['tmp_name'], _PS_MODULE_DIR_.$this->name.'/logos/'.$name);
			Configuration::updateValue('TREEPODIA_PLAY_LOGO', $name);
		}
		else
			Configuration::updateValue('TREEPODIA_PLAY_LOGO', Tools::getValue('trpd_play_logo'));

		$this->_sendUpdate();

        if (Tools::getValue('trpd_hook_position') == 0)
        {
            Configuration::updateValue('TREEPODIA_HOOK', 0);
            $this->unregisterHook(Hook::get('productFooter'));
            $this->registerHook('extraLeft');
        } else {
            Configuration::updateValue('TREEPODIA_HOOK', 1);
            $this->unregisterHook(Hook::get('extraLeft'));
            $this->registerHook('productFooter');
        }

		$dataSync = '<img src="http://www.prestashop.com/modules/'.$this->name.'.png?account_code='.Configuration::get('TREEPODIA_ACCOUNT_CODE').'" style="float:right" />';

		return $this->displayConfirmation($this->l('Settings updated').$dataSync);
	}

	private function _sendUpdate()
	{
		$host = 'www.treepodia.com';
		$uri = '/prestashop/api/account-settings-set';

		if (!ini_get('allow_url_fopen'))
			return false;

		$socket = fsockopen('ssl://'.$host, 443, $errno, $errstr, 30);

		if ($socket)
		{
			$params = 'prestashop-api='._PS_BASE_URL_._MODULE_DIR_.$this->name.'/api'.
				 '&account-code='.Configuration::get('TREEPODIA_ACCOUNT_CODE').
				 '&integration='.(Configuration::get('TREEPODIA_INTEGRATION_TYPE') == 0 ? 'built-in' : 'custom').
				 '&watch-video-image-url='._PS_BASE_URL_._MODULE_DIR_.$this->name.'/logos/'.Configuration::get('TREEPODIA_PLAY_LOGO').
				 '&watch-video-image-location=top-left';

			$header = 'GET '.$uri.'?'.$params.' HTTP/1.1'."\r\n";
			$header .= 'Host: '.$host."\r\n";
			$header .= 'Content-Type: application/x-www-form-urlencoded' . "\r\n";
			$header .= 'Content-Length: '.strlen($params)."\r\n".
			$header .= 'Connection: Close'."\r\n\r\n";

			fputs($socket, $header.$params);

			$result = '';
			while (!feof($socket))
			{
				$result .= trim(fgets($socket, 1024));
			}

			fclose($socket);
		}

	}

	private function _isAccountCode($accountCode)
	{
		return ereg('^[A-Z]+[0-9]+$', $accountCode);
	}

	public function hookExtraLeft($params)
	{
		global $smarty;

		if (!Configuration::get('TREEPODIA_ACCOUNT_CODE') OR Configuration::get('TREEPODIA_INTEGRATION_TYPE') != 0)
			return '';

		switch(Configuration::get('TREEPODIA_POSITION'))
		{
			case 0:
				$position = 'left';
				break;
			case 1:
				$position = 'center';
				break;
			case 2:
				$position = 'right';
				break;
			default:
				$position = 'left';
		}

		$smarty->assign(array('position' => $position, 'img_src' => _MODULE_DIR_.$this->name.'/logos/'.Configuration::get('TREEPODIA_PLAY_LOGO')));
		return $this->display(__FILE__, 'product.tpl');
	}

	public function hookProductFooter($params)
	{
	    return $this->hookExtraLeft($params);
	}


	public function hookFooter($params)
	{
		global $smarty;

		$id_product = Tools::getValue('id_product');
		if (!Configuration::get('TREEPODIA_ACCOUNT_CODE') OR Configuration::get('TREEPODIA_INTEGRATION_TYPE') != 0)
			return '';

		if (!empty($id_product))
		{
			$smarty->assign(array('account_id' => Configuration::get('TREEPODIA_ACCOUNT_CODE'),
								  'product_sku' => (int)($id_product)));

			return $this->display(__FILE__, 'footer.tpl');
		}
	}

	public function hookProductDescription($params)
	{
		return $this->hookExtraLeft($params);
	}

	/* For 1.0/1.1 compatibility purpose only */
	private function _getImageLink($name, $ids, $type = null)
	{
		return ($this->allow == 1) ? (__PS_BASE_URI__.$ids.($type ? '-'.$type : '').'/'.$name.'.jpg') : (_THEME_PROD_DIR_.$ids.($type ? '-'.$type : '').'.jpg');
	}


	public function hookOrderConfirmation($params)
	{
		$order = $params['objOrder'];
		$products = $order->getProducts();

		global $smarty;

		$smarty->assign(array('account_id' => Configuration::get('TREEPODIA_ACCOUNT_CODE'), 'products' => $products));

		return $this->display(__FILE__, 'tracking.tpl');
	}
}

