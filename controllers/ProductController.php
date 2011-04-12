<?php
/*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ProductControllerCore extends FrontController
{
	protected $product;

	public function setMedia()
	{
		parent::setMedia();

		Tools::addCSS(_THEME_CSS_DIR_.'product.css');
		Tools::addCSS(_PS_CSS_DIR_.'jquery.fancybox-1.3.4.css', 'screen');
		Tools::addJS(array(
			_PS_JS_DIR_.'jquery/jquery.fancybox-1.3.4.js',
			_PS_JS_DIR_.'jquery/jquery.idTabs.modified.js',
			_PS_JS_DIR_.'jquery/jquery.scrollTo-1.4.2-min.js',
			_PS_JS_DIR_.'jquery/jquery.serialScroll-1.2.2-min.js',
			_THEME_JS_DIR_.'tools.js',
			_THEME_JS_DIR_.'product.js'));

		if (Configuration::get('PS_DISPLAY_JQZOOM') == 1)
		{
			Tools::addCSS(_PS_CSS_DIR_.'jqzoom.css', 'screen');
			Tools::addJS(_PS_JS_DIR_.'jquery/jquery.jqzoom.js');
		}
	}

	public function preProcess()
	{
		if ($id_product = (int)Tools::getValue('id_product'))
			$this->product = new Product($id_product, true, self::$cookie->id_lang);
			
		if (!Validate::isLoadedObject($this->product))
		{
			header('HTTP/1.1 404 Not Found');
			header('Status: 404 Not Found');
		}
		else
		{
			// Automatically redirect to the canonical URL if the current in is the right one
			// $_SERVER['HTTP_HOST'] must be replaced by the real canonical domain
			if (Validate::isLoadedObject($this->product))
			{
				$canonicalURL = self::$link->getProductLink($this->product);
				if (!preg_match('/^'.Tools::pRegexp($canonicalURL, '/').'([&?].*)?$/', Tools::getProtocol().$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']))
				{
					header('HTTP/1.0 301 Moved');
					if (defined('_PS_MODE_DEV_') AND _PS_MODE_DEV_)
						die('[Debug] This page has moved<br />Please use the following URL instead: <a href="'.$canonicalURL.'">'.$canonicalURL.'</a>');
					Tools::redirectLink($canonicalURL);
				}
			}
		}

		parent::preProcess();

		if((int)(Configuration::get('PS_REWRITING_SETTINGS')))
			if ($id_product = (int)Tools::getValue('id_product'))
			{
				$rewrite_infos = Product::getUrlRewriteInformations((int)$id_product);

				$default_rewrite = array();
				foreach ($rewrite_infos AS $infos)
					$default_rewrite[$infos['id_lang']] = self::$link->getProductLink((int)$id_product, $infos['link_rewrite'], $infos['category_rewrite'], $infos['ean13'], (int)$infos['id_lang']);

				self::$smarty->assign('lang_rewrite_urls', $default_rewrite);
			}
	}

	public function process()
	{
		parent::process();
		global $cart;

		if (!$id_product = (int)(Tools::getValue('id_product')) OR !Validate::isUnsignedId($id_product))
			$this->errors[] = Tools::displayError('Product not found');
		else
		{
			if (!Validate::isLoadedObject($this->product)
				OR (!$this->product->active AND (Tools::getValue('adtoken') != Tools::encrypt('PreviewProduct'.$this->product->id))
				|| !file_exists(dirname(__FILE__).'/../'.Tools::getValue('ad').'/ajax.php')))
			{
				header('HTTP/1.1 404 page not found');
				$this->errors[] = Tools::displayError('Pproduct is no longer available.');
			}
			elseif (!$this->product->checkAccess((int)(self::$cookie->id_customer)))
				$this->errors[] = Tools::displayError('You do not have access to this product.');
			else
			{
				self::$smarty->assign('virtual', ProductDownload::getIdFromIdProduct((int)($this->product->id)));

				if (!$this->product->active)
					self::$smarty->assign('adminActionDisplay', true);

				/* rewrited url set */
				$rewrited_url = self::$link->getProductLink($this->product->id, $this->product->link_rewrite);

				/* Product pictures management */
				require_once('images.inc.php');
				self::$smarty->assign('customizationFormTarget', Tools::safeOutput(urldecode($_SERVER['REQUEST_URI'])));

				if (Tools::isSubmit('submitCustomizedDatas'))
				{
					$this->pictureUpload($this->product, $cart);
					$this->textRecord($this->product, $cart);
					$this->formTargetFormat();
				}
				elseif (isset($_GET['deletePicture']) AND !$cart->deletePictureToProduct((int)($this->product->id), (int)(Tools::getValue('deletePicture'))))
					$this->errors[] = Tools::displayError('An error occurred while deleting the selected picture');

				$files = self::$cookie->getFamily('pictures_'.(int)($this->product->id));
				$textFields = self::$cookie->getFamily('textFields_'.(int)($this->product->id));
				foreach ($textFields as $key => $textField)
					$textFields[$key] = str_replace('<br />', "\n", $textField);
				self::$smarty->assign(array(
					'pictures' => $files,
					'textFields' => $textFields));

				$productPriceWithTax = Product::getPriceStatic($id_product, true, NULL, 6);
				if (Product::$_taxCalculationMethod == PS_TAX_INC)
					$productPriceWithTax = Tools::ps_round($productPriceWithTax, 2);

				$productPriceWithoutEcoTax = (float)($productPriceWithTax - $this->product->ecotax);
				$configs = Configuration::getMultiple(array('PS_ORDER_OUT_OF_STOCK', 'PS_LAST_QTIES'));

				/* Features / Values */
				$features = $this->product->getFrontFeatures((int)(self::$cookie->id_lang));
				$attachments = $this->product->getAttachments((int)(self::$cookie->id_lang));

				/* Category */
				$category = false;
				if (isset($_SERVER['HTTP_REFERER']) AND preg_match('!^(.*)\/([0-9]+)\-(.*[^\.])|(.*)id_category=([0-9]+)(.*)$!', $_SERVER['HTTP_REFERER'], $regs) AND !strstr($_SERVER['HTTP_REFERER'], '.html'))
				{
					if (isset($regs[2]) AND is_numeric($regs[2]))
					{
						if (Product::idIsOnCategoryId((int)($this->product->id), array('0' => array('id_category' => (int)($regs[2])))))
							$category = new Category((int)($regs[2]), (int)(self::$cookie->id_lang));
					}
					elseif (isset($regs[5]) AND is_numeric($regs[5]))
					{
						if (Product::idIsOnCategoryId((int)($this->product->id), array('0' => array('id_category' => (int)($regs[5])))))
							$category = new Category((int)($regs[5]), (int)(self::$cookie->id_lang));
					}
				}
				if (!$category)
					$category = new Category($this->product->id_category_default, (int)(self::$cookie->id_lang));

				if (isset($category) AND Validate::isLoadedObject($category))
				{
					self::$smarty->assign(array(
					'path' => Tools::getPath((int)$category->id, $this->product->name, true),
					'category' => $category,
					'subCategories' => $category->getSubCategories((int)(self::$cookie->id_lang), true),
					'id_category_current' => (int)($category->id),
					'id_category_parent' => (int)($category->id_parent),
					'return_category_name' => Tools::safeOutput($category->name)));
				}
				else
					self::$smarty->assign('path', Tools::getPath((int)$this->product->id_category_default, $this->product->name));

				self::$smarty->assign('return_link', (isset($category->id) AND $category->id) ? Tools::safeOutput(self::$link->getCategoryLink($category)) : 'javascript: history.back();');

				$lang = Configuration::get('PS_LANG_DEFAULT');
				if (Pack::isPack((int)($this->product->id), (int)($lang)) AND !Pack::isInStock((int)($this->product->id), (int)($lang)))
					$this->product->quantity = 0;

				$group_reduction = (100 - Group::getReduction((int)(self::$cookie->id_customer))) / 100;
				$id_customer = (isset(self::$cookie->id_customer) AND self::$cookie->id_customer) ? (int)(self::$cookie->id_customer) : 0;
				$id_group = $id_customer ? (int)(Customer::getDefaultGroupId($id_customer)) : _PS_DEFAULT_CUSTOMER_GROUP_;
				$id_country = (int)($id_customer ? Customer::getCurrentCountry($id_customer) : Configuration::get('PS_COUNTRY_DEFAULT'));

				// Tax
				$tax = (float)(Tax::getProductTaxRate((int)($this->product->id), $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
				self::$smarty->assign('tax_rate', $tax);

				$ecotax_rate = (float) Tax::getProductEcotaxRate($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
                $ecotaxTaxAmount = Tools::ps_round($this->product->ecotax, 2);
				if (Product::$_taxCalculationMethod == PS_TAX_INC)
					$ecotaxTaxAmount = Tools::ps_round($ecotaxTaxAmount * (1 + $ecotax_rate / 100), 2);

				self::$smarty->assign(array(
					'quantity_discounts' => $this->formatQuantityDiscounts(SpecificPrice::getQuantityDiscounts((int)($this->product->id), (int)(Shop::getCurrentShop()), (int)(self::$cookie->id_currency), $id_country, $id_group), $this->product->getPrice(Product::$_taxCalculationMethod == PS_TAX_INC, false), (float)($tax)),
					'product' => $this->product,
					'ecotax_tax_inc' => $ecotaxTaxAmount,
					'ecotax_tax_exc' => Tools::ps_round($this->product->ecotax, 2),
					'ecotaxTax_rate' => $ecotax_rate,
					'homeSize' => Image::getSize('home'),
					'product_manufacturer' => new Manufacturer((int)($this->product->id_manufacturer), Configuration::get('PS_LANG_DEFAULT')),
					'token' => Tools::getToken(false),
					'productPriceWithoutEcoTax' => (float)($productPriceWithoutEcoTax),
					'features' => $features,
					'attachments' => $attachments,
					'allow_oosp' => $this->product->isAvailableWhenOutOfStock((int)($this->product->out_of_stock)),
					'last_qties' =>  (int)($configs['PS_LAST_QTIES']),
					'group_reduction' => $group_reduction,
					'col_img_dir' => _PS_COL_IMG_DIR_,
				));
				self::$smarty->assign(array(
					'HOOK_EXTRA_LEFT' => Module::hookExec('extraLeft'),
					'HOOK_EXTRA_RIGHT' => Module::hookExec('extraRight'),
					'HOOK_PRODUCT_OOS' => Hook::productOutOfStock($this->product),
					'HOOK_PRODUCT_FOOTER' => Hook::productFooter($this->product, $category),
					'HOOK_PRODUCT_ACTIONS' => Module::hookExec('productActions'),
					'HOOK_PRODUCT_TAB' =>  Module::hookExec('productTab'),
					'HOOK_PRODUCT_TAB_CONTENT' =>  Module::hookExec('productTabContent')
				));

				$images = $this->product->getImages((int)(self::$cookie->id_lang));
				$productImages = array();
				foreach ($images AS $k => $image)
				{
					if ($image['cover'])
					{
						self::$smarty->assign('mainImage', $images[0]);
						$cover = $image;
						$cover['id_image'] = (int)($this->product->id).'-'.$cover['id_image'];
						$cover['id_image_only'] = (int)($image['id_image']);
					}
					$productImages[(int)($image['id_image'])] = $image;
				}
				if (!isset($cover))
					$cover = array('id_image' => Language::getIsoById(self::$cookie->id_lang).'-default', 'legend' => 'No picture', 'title' => 'No picture');
				$size = Image::getSize('large');
				self::$smarty->assign(array(
					'cover' => $cover,
					'imgWidth' => (int)($size['width']),
					'mediumSize' => Image::getSize('medium'),
					'largeSize' => Image::getSize('large'),
					'accessories' => $this->product->getAccessories((int)(self::$cookie->id_lang))));
				if (sizeof($productImages))
					self::$smarty->assign('images', $productImages);

				/* Attributes / Groups & colors */
				$colors = array();
				$attributesGroups = $this->product->getAttributesGroups((int)(self::$cookie->id_lang));
				if (is_array($attributesGroups) AND sizeof($attributesGroups))
				{
					$combinationImages = $this->product->getCombinationImages((int)(self::$cookie->id_lang));
					foreach ($attributesGroups AS $k => $row)
					{
						/* Color management */
						if (((isset($row['attribute_color']) AND $row['attribute_color']) OR (file_exists(_PS_COL_IMG_DIR_.$row['id_attribute'].'.jpg'))) AND $row['id_attribute_group'] == $this->product->id_color_default)
						{
							$colors[$row['id_attribute']]['value'] = $row['attribute_color'];
							$colors[$row['id_attribute']]['name'] = $row['attribute_name'];
							if (!isset($colors[$row['id_attribute']]['attributes_quantity']))
								$colors[$row['id_attribute']]['attributes_quantity'] = 0;
							$colors[$row['id_attribute']]['attributes_quantity'] += (int)($row['quantity']);
						}

						$groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = $row['attribute_name'];
						$groups[$row['id_attribute_group']]['name'] = $row['public_group_name'];
						$groups[$row['id_attribute_group']]['is_color_group'] = $row['is_color_group'];
						if ($row['default_on'])
							$groups[$row['id_attribute_group']]['default'] = (int)($row['id_attribute']);
						if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']]))
							$groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
						$groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] += (int)($row['quantity']);

						$combinations[$row['id_product_attribute']]['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
						$combinations[$row['id_product_attribute']]['attributes'][] = (int)($row['id_attribute']);
						$combinations[$row['id_product_attribute']]['price'] = (float)($row['price']);
						$combinations[$row['id_product_attribute']]['ecotax'] = (float)($row['ecotax']);
						$combinations[$row['id_product_attribute']]['weight'] = (float)($row['weight']);
						$combinations[$row['id_product_attribute']]['quantity'] = (int)($row['quantity']);
						$combinations[$row['id_product_attribute']]['reference'] = $row['reference'];
						$combinations[$row['id_product_attribute']]['unit_impact'] = $row['unit_price_impact'];
						$combinations[$row['id_product_attribute']]['minimal_quantity'] = $row['minimal_quantity'];
						$combinations[$row['id_product_attribute']]['id_image'] = isset($combinationImages[$row['id_product_attribute']][0]['id_image']) ? $combinationImages[$row['id_product_attribute']][0]['id_image'] : -1;
					}
					//wash attributes list (if some attributes are unavailables and if allowed to wash it)
					if (!Product::isAvailableWhenOutOfStock($this->product->out_of_stock) && Configuration::get('PS_DISP_UNAVAILABLE_ATTR') == 0)
					{
						foreach ($groups AS &$group)
							foreach ($group['attributes_quantity'] AS $key => &$quantity)
								if (!$quantity)
									unset($group['attributes'][$key]);
						foreach ($colors AS $key => $color)
							if (!$color['attributes_quantity'])
								unset($colors[$key]);
					}
					foreach($groups AS &$group)
						natcasesort($group['attributes']);
					foreach ($combinations AS $id_product_attribute => $comb)
					{
						$attributeList = '';
						foreach ($comb['attributes'] AS $id_attribute)
							$attributeList .= '\''.(int)($id_attribute).'\',';
						$attributeList = rtrim($attributeList, ',');
						$combinations[$id_product_attribute]['list'] = $attributeList;
					}
					self::$smarty->assign(array(
						'groups' => $groups,
						'combinaisons' => $combinations, /* Kept for compatibility purpose only */
						'combinations' => $combinations,
						'colors' => (sizeof($colors) AND $this->product->id_color_default) ? $colors : false,
						'combinationImages' => $combinationImages));
				}

				self::$smarty->assign(array(
					'no_tax' => Tax::excludeTaxeOption() OR !Tax::getProductTaxRate((int)$this->product->id, $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}),
					'customizationFields' => $this->product->getCustomizationFields((int)(self::$cookie->id_lang))
				));

				// Pack management
				self::$smarty->assign('packItems', $this->product->cache_is_pack ? Pack::getItemTable($this->product->id, (int)(self::$cookie->id_lang), true) : array());
				self::$smarty->assign('packs', Pack::getPacksTable($this->product->id, (int)(self::$cookie->id_lang), true, 1));
			}
		}

		self::$smarty->assign(array(
			'ENT_NOQUOTES' => ENT_NOQUOTES,
			'outOfStockAllowed' => (int)(Configuration::get('PS_ORDER_OUT_OF_STOCK')),
			'errors' => $this->errors,
			'categories' => Category::getHomeCategories((int)(self::$cookie->id_lang)),
			'have_image' => Product::getCover((int)(Tools::getValue('id_product'))),
			'tax_enabled' => Configuration::get('PS_TAX'),
			'display_qties' => (int)(Configuration::get('PS_DISPLAY_QTIES')),
			'display_ht' => !Tax::excludeTaxeOption(),
			'ecotax' => (!sizeof($this->errors) AND $this->product->ecotax > 0 ? Tools::convertPrice((float)($this->product->ecotax)) : 0),
		));

		global $currency;
		self::$smarty->assign(array(
			'currencySign' => $currency->sign,
			'currencyRate' => $currency->conversion_rate,
			'currencyFormat' => $currency->format,
			'currencyBlank' => $currency->blank,
			'jqZoomEnabled' => Configuration::get('PS_DISPLAY_JQZOOM')
		));
	}

	public function displayContent()
	{
		parent::displayContent();
		self::$smarty->display(_PS_THEME_DIR_.'product.tpl');
	}

	public function pictureUpload(Product $product, Cart $cart)
	{
		if (!$fieldIds = $this->product->getCustomizationFieldIds())
			return false;
		$authorizedFileFields = array();
		foreach ($fieldIds AS $fieldId)
			if ($fieldId['type'] == _CUSTOMIZE_FILE_)
				$authorizedFileFields[(int)($fieldId['id_customization_field'])] = 'file'.(int)($fieldId['id_customization_field']);
		$indexes = array_flip($authorizedFileFields);
		foreach ($_FILES AS $fieldName => $file)
			if (in_array($fieldName, $authorizedFileFields) AND isset($file['tmp_name']) AND !empty($file['tmp_name']))
			{
				$fileName = md5(uniqid(rand(), true));
				if ($error = checkImage($file, (int)(Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE'))))
					$this->errors[] = $error;

				if ($error OR (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS') OR !move_uploaded_file($file['tmp_name'], $tmpName)))
					return false;
				/* Original file */
				elseif (!imageResize($tmpName, _PS_UPLOAD_DIR_.$fileName))
					$this->errors[] = Tools::displayError('An error occurred during the image upload.');
				/* A smaller one */
				elseif (!imageResize($tmpName, _PS_UPLOAD_DIR_.$fileName.'_small', (int)(Configuration::get('PS_PRODUCT_PICTURE_WIDTH')), (int)(Configuration::get('PS_PRODUCT_PICTURE_HEIGHT'))))
					$this->errors[] = Tools::displayError('An error occurred during the image upload.');
				elseif (!chmod(_PS_UPLOAD_DIR_.$fileName, 0777) OR !chmod(_PS_UPLOAD_DIR_.$fileName.'_small', 0777))
					$this->errors[] = Tools::displayError('An error occurred during the image upload.');
				else
					$cart->addPictureToProduct((int)($this->product->id), $indexes[$fieldName], $fileName);
				unlink($tmpName);
			}
		return true;
	}

	public function textRecord(Product $product, Cart $cart)
	{
		if (!$fieldIds = $this->product->getCustomizationFieldIds())
			return false;
		$authorizedTextFields = array();
		foreach ($fieldIds AS $fieldId)
			if ($fieldId['type'] == _CUSTOMIZE_TEXTFIELD_)
				$authorizedTextFields[(int)($fieldId['id_customization_field'])] = 'textField'.(int)($fieldId['id_customization_field']);
		$indexes = array_flip($authorizedTextFields);
		foreach ($_POST AS $fieldName => $value)
			if (in_array($fieldName, $authorizedTextFields) AND !empty($value))
			{
				if (!Validate::isMessage($value))
					$this->errors[] = Tools::displayError('Invalid message');
				else
					$cart->addTextFieldToProduct((int)($this->product->id), $indexes[$fieldName], $value);
			}
			elseif (in_array($fieldName, $authorizedTextFields) AND empty($value))
				$cart->deleteTextFieldFromProduct((int)($this->product->id), $indexes[$fieldName]);
	}

	public function formTargetFormat()
	{
		$customizationFormTarget = Tools::safeOutput(urldecode($_SERVER['REQUEST_URI']));
		foreach ($_GET AS $field => $value)
			if (strncmp($field, 'group_', 6) == 0)
				$customizationFormTarget = preg_replace('/&group_([[:digit:]]+)=([[:digit:]]+)/', '', $customizationFormTarget);
		if (isset($_POST['quantityBackup']))
			self::$smarty->assign('quantityBackup', (int)($_POST['quantityBackup']));
		self::$smarty->assign('customizationFormTarget', $customizationFormTarget);
	}

	public function formatQuantityDiscounts($specificPrices, $price, $taxRate)
	{
		foreach ($specificPrices AS $key => &$row)
		{
			$row['quantity'] = &$row['from_quantity'];
			if ($row['price'] != 0) // The price may be directly set
			{
			    $cur_price = (Product::$_taxCalculationMethod == PS_TAX_EXC ? $row['price'] : $row['price'] * (1 + $taxRate / 100));

                if ($row['reduction_type'] == 'amount')
			    {
			        $cur_price = Product::$_taxCalculationMethod == PS_TAX_INC ? $cur_price - $row['reduction'] : $cur_price - ($row['reduction'] / (1 + $taxRate / 100));
			    } else {
				    $cur_price = $cur_price * ( 1  - ($row['reduction']));
			    }

			    $row['real_value'] = $price - $cur_price;
			}
			else
			{
			    if ($row['reduction_type'] == 'amount')
			    {
			        $row['real_value'] = Product::$_taxCalculationMethod == PS_TAX_INC ? $row['reduction'] : $row['reduction'] / (1 + $taxRate / 100);
			    } else {
				    $row['real_value'] = $row['reduction'] * 100;
			    }
			}
			$row['nextQuantity'] = (isset($specificPrices[$key + 1]) ? (int)($specificPrices[$key + 1]['from_quantity']) : -1);
		}
		return $specificPrices;
	}
}

