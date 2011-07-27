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
*  @version  Release: $Revision: 7331 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class ProductControllerCore extends FrontController
{
	protected $product;
	public $php_self = 'product.php';
	protected $canonicalURL;

	public function setMedia()
	{
		parent::setMedia();

		$this->addCSS(_THEME_CSS_DIR_.'product.css');
		$this->addCSS(_PS_CSS_DIR_.'jquery.fancybox-1.3.4.css', 'screen');
		$this->addJS(array(
			_PS_JS_DIR_.'jquery/jquery.fancybox-1.3.4.js',
			_PS_JS_DIR_.'jquery/jquery.idTabs.modified.js',
			_PS_JS_DIR_.'jquery/jquery.scrollTo-1.4.2-min.js',
			_PS_JS_DIR_.'jquery/jquery.serialScroll-1.2.2-min.js',
			_THEME_JS_DIR_.'tools.js',
			_THEME_JS_DIR_.'product.js'));

		if (Configuration::get('PS_DISPLAY_JQZOOM') == 1)
		{
			$this->addCSS(_PS_CSS_DIR_.'jqzoom.css', 'screen');
			$this->addJS(_PS_JS_DIR_.'jquery/jquery.jqzoom.js');
		}
	}
	
	public function canonicalRedirection()
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

	public function preProcess()
	{
		if ($id_product = (int)Tools::getValue('id_product'))
			$this->product = new Product($id_product, true, $this->context->language->id);

		if (!Validate::isLoadedObject($this->product))
		{
			header('HTTP/1.1 404 Not Found');
			header('Status: 404 Not Found');
		}
		else
			$this->canonicalRedirection();

		parent::preProcess();
	}

	public function process()
	{
		parent::process();

		if (!Validate::isLoadedObject($this->product))
			$this->errors[] = Tools::displayError('Product not found');
		else
		{
			if (!Validate::isLoadedObject($this->product) OR !$this->product->isAssociatedToShop()
				OR (!$this->product->active AND (Tools::getValue('adtoken') != Tools::encrypt('PreviewProduct'.$this->product->id))
				|| !file_exists(dirname(__FILE__).'/../'.Tools::getValue('ad').'/ajax.php')))
			{
				header('HTTP/1.1 404 page not found');
				$this->errors[] = Tools::displayError('Product is no longer available.');
			}
			elseif (!$this->product->checkAccess(isset($this->context->customer) ? $this->context->customer->id : 0))
				$this->errors[] = Tools::displayError('You do not have access to this product.');
			else
			{
				$this->context->smarty->assign('virtual', ProductDownload::getIdFromIdProduct((int)($this->product->id)));

				if (!$this->product->active)
					$this->context->smarty->assign('adminActionDisplay', true);

				/* Product pictures management */
				require_once('images.inc.php');
				$this->context->smarty->assign('customizationFormTarget', Tools::safeOutput(urldecode($_SERVER['REQUEST_URI'])));

				if (Tools::isSubmit('submitCustomizedDatas'))
				{
					// If cart has not been saved, we need to do it so that customization fields can have an id_cart
					// We check that the cookie exists first to avoid ghost carts
					if (!$this->context->cart->id && isset($_COOKIE[$this->context->cookie->getName()]))
					{
						$this->context->cart->add();
						$this->context->cookie->id_cart = (int)$this->context->cart->id;
					}
					$this->pictureUpload($this->product, $this->context->cart);
					$this->textRecord($this->product, $this->context->cart);
					$this->formTargetFormat();
				}
				elseif (isset($_GET['deletePicture']) AND !$this->context->cart->deletePictureToProduct($this->product->id, Tools::getValue('deletePicture')))
					$this->errors[] = Tools::displayError('An error occurred while deleting the selected picture');

				$files = $this->context->cart->getProductCustomization($this->product->id, _CUSTOMIZE_FILE_, true);
				$pictures = array();
				foreach($files as $file)
					$pictures['pictures_'.$this->product->id.'_'.$file['index']] = $file['value'];

				$texts = $this->context->cart->getProductCustomization($this->product->id, _CUSTOMIZE_TEXTFIELD_, true);
				$textFields = array();
				foreach ($texts as $textField)
					$textFields['textFields_'.$this->product->id.'_'.$textField['index']] = str_replace('<br />', "\n", $textField['value']);
				$this->context->smarty->assign(array(
					'pictures' => $pictures,
					'textFields' => $textFields));

				$configs = Configuration::getMultiple(array('PS_ORDER_OUT_OF_STOCK', 'PS_LAST_QTIES'));

				/* Features / Values */
				$features = $this->product->getFrontFeatures($this->context->language->id);
				$attachments = ($this->product->cache_has_attachments) ? $this->product->getAttachments($this->context->language->id) : array();

				/* Category */
				$category = false;
				if (isset($_SERVER['HTTP_REFERER']) AND preg_match('!^(.*)\/([0-9]+)\-(.*[^\.])|(.*)id_category=([0-9]+)(.*)$!', $_SERVER['HTTP_REFERER'], $regs) AND !strstr($_SERVER['HTTP_REFERER'], '.html'))
				{
					if (isset($regs[2]) AND is_numeric($regs[2]))
					{
						if (Product::idIsOnCategoryId($this->product->id, array('0' => array('id_category' => (int)$regs[2]))))
							$category = new Category($regs[2], $this->context->language->id);
					}
					elseif (isset($regs[5]) AND is_numeric($regs[5]))
					{
						if (Product::idIsOnCategoryId($this->product->id, array('0' => array('id_category' => (int)$regs[5]))))
							$category = new Category($regs[5], $this->context->language->id);
					}
				}
				if (!$category)
					$category = new Category($this->product->id_category_default, (int)$this->context->cookie->id_lang);

				if (isset($category) AND Validate::isLoadedObject($category))
				{
					$this->context->smarty->assign(array(
						'path' => Tools::getPath((int)$category->id, $this->product->name, true),
						'category' => $category,
						'subCategories' => $category->getSubCategories($this->context->language->id, true),
						'id_category_current' => (int)($category->id),
						'id_category_parent' => (int)($category->id_parent),
						'return_category_name' => Tools::safeOutput($category->name),
					));
				}
				else
					$this->context->smarty->assign('path', Tools::getPath((int)$this->product->id_category_default, $this->product->name));

				$this->context->smarty->assign('return_link', (isset($category->id) AND $category->id) ? Tools::safeOutput($this->context->link->getCategoryLink($category)) : 'javascript: history.back();');

				$lang = Configuration::get('PS_LANG_DEFAULT');
				if (Pack::isPack($this->product->id) AND !Pack::isInStock((int)$this->product->id, $lang))
					$this->product->quantity = 0;

				$id_customer = (isset($this->context->customer) ? (int)($this->context->customer->id) : 0);
				$group_reduction = (100 - Group::getReduction($id_customer)) / 100;
				$id_group = (isset($this->context->customer) ? $this->context->customer->id_default_group : _PS_DEFAULT_CUSTOMER_GROUP_);
				$id_country = (int)($id_customer ? Customer::getCurrentCountry($id_customer) : Configuration::get('PS_COUNTRY_DEFAULT'));

				// Tax
				$tax = (float)(Tax::getProductTaxRate((int)($this->product->id), $this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
				$this->context->smarty->assign('tax_rate', $tax);

				$productPriceWithTax = Product::getPriceStatic($this->product->id, true, NULL, 6);
				if (Product::$_taxCalculationMethod == PS_TAX_INC)
					$productPriceWithTax = Tools::ps_round($productPriceWithTax, 2);
				$productPriceWithoutEcoTax = (float)($productPriceWithTax - $this->product->ecotax);

				$ecotax_rate = (float) Tax::getProductEcotaxRate($this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
            	$ecotaxTaxAmount = Tools::ps_round($this->product->ecotax, 2);
				if (Product::$_taxCalculationMethod == PS_TAX_INC && (int)Configuration::get('PS_TAX'))
					$ecotaxTaxAmount = Tools::ps_round($ecotaxTaxAmount * (1 + $ecotax_rate / 100), 2);

				$this->context->smarty->assign(array(
					'quantity_discounts' => $this->formatQuantityDiscounts(SpecificPrice::getQuantityDiscounts((int)($this->product->id), $this->id_current_shop, (int)($this->context->cookie->id_currency), $id_country, $id_group), $this->product->getPrice(Product::$_taxCalculationMethod == PS_TAX_INC, false), (float)($tax)),
					'product' => $this->product,
					'ecotax_tax_inc' => $ecotaxTaxAmount,
					'ecotax_tax_exc' => Tools::ps_round($this->product->ecotax, 2),
					'ecotaxTax_rate' => $ecotax_rate,
					'homeSize' => Image::getSize('home'),
					'product_manufacturer' => new Manufacturer($this->product->id_manufacturer, $this->context->language->id),
					'token' => Tools::getToken(false),
					'productPriceWithoutEcoTax' => (float)$productPriceWithoutEcoTax,
					'features' => $features,
					'attachments' => $attachments,
					'allow_oosp' => $this->product->isAvailableWhenOutOfStock((int)($this->product->out_of_stock)),
					'last_qties' =>  (int)Configuration::get('PS_LAST_QTIES'),
					'group_reduction' => $group_reduction,
					'col_img_dir' => _PS_COL_IMG_DIR_,
				));
				$this->context->smarty->assign(array(
					'HOOK_EXTRA_LEFT' => Module::hookExec('extraLeft'),
					'HOOK_EXTRA_RIGHT' => Module::hookExec('extraRight'),
					'HOOK_PRODUCT_OOS' => Hook::productOutOfStock($this->product),
					'HOOK_PRODUCT_FOOTER' => Hook::productFooter($this->product, $category),
					'HOOK_PRODUCT_ACTIONS' => Module::hookExec('productActions'),
					'HOOK_PRODUCT_TAB' =>  Module::hookExec('productTab'),
					'HOOK_PRODUCT_TAB_CONTENT' =>  Module::hookExec('productTabContent')
				));

				$images = $this->product->getImages((int)$this->context->language->id);
				$productImages = array();
				foreach ($images AS $k => $image)
				{
					if ($image['cover'])
					{
						$this->context->smarty->assign('mainImage', $images[0]);
						$cover = $image;
						$cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($this->product->id.'-'.$image['id_image']) : $image['id_image']);
						$cover['id_image_only'] = (int)($image['id_image']);
					}
					$productImages[(int)$image['id_image']] = $image;
				}
				if (!isset($cover))
					$cover = array('id_image' => $this->context->language->iso_code.'-default', 'legend' => 'No picture', 'title' => 'No picture');
				$size = Image::getSize('large');
				$this->context->smarty->assign(array(
					'cover' => $cover,
					'imgWidth' => (int)($size['width']),
					'mediumSize' => Image::getSize('medium'),
					'largeSize' => Image::getSize('large'),
					'accessories' => $this->product->getAccessories($this->context->language->id)));
				if (sizeof($productImages))
					$this->context->smarty->assign('images', $productImages);

				/* Attributes / Groups & colors */
				$colors = array();
				$attributesGroups = $this->product->getAttributesGroups($this->context->language->id);  // @todo (RM) should only get groups and not all declination ?
				if (is_array($attributesGroups) AND $attributesGroups)
				{
					$groups = array();
					$combinationImages = $this->product->getCombinationImages($this->context->language->id);
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

						if (!isset($groups[$row['id_attribute_group']]))
						{
							$groups[$row['id_attribute_group']] = array(
								'name' =>			$row['public_group_name'],
								'is_color_group' =>	$row['is_color_group'],
								'default' =>		-1,
							);
						}

						$groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = $row['attribute_name'];
						if ($row['default_on'] && $groups[$row['id_attribute_group']]['default'] == -1)
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

					foreach ($groups AS &$group)
						natcasesort($group['attributes']);

					foreach ($combinations AS $id_product_attribute => $comb)
					{
						$attributeList = '';
						foreach ($comb['attributes'] AS $id_attribute)
							$attributeList .= '\''.(int)($id_attribute).'\',';
						$attributeList = rtrim($attributeList, ',');
						$combinations[$id_product_attribute]['list'] = $attributeList;
					}

					$this->context->smarty->assign(array(
						'groups' => $groups,
						'combinaisons' => $combinations, /* Kept for compatibility purpose only */
						'combinations' => $combinations,
						'colors' => (sizeof($colors) AND $this->product->id_color_default) ? $colors : false,
						'combinationImages' => $combinationImages));
				}

				$this->context->smarty->assign(array(
					'no_tax' => Tax::excludeTaxeOption() OR !Tax::getProductTaxRate((int)$this->product->id, $this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}),
					'customizationFields' => ($this->product->customizable) ? $this->product->getCustomizationFields($this->context->language->id) : false,
				));

				// Pack management
				$this->context->smarty->assign('packItems', $this->product->cache_is_pack ? Pack::getItemTable($this->product->id, $this->context->language->id, true) : array());
				$this->context->smarty->assign('packs', Pack::getPacksTable($this->product->id, $this->context->language->id, true, 1));
			}
		}

		$this->context->smarty->assign(array(
			'ENT_NOQUOTES' => ENT_NOQUOTES,
			'outOfStockAllowed' => (int)(Configuration::get('PS_ORDER_OUT_OF_STOCK')),
			'errors' => $this->errors,
			'categories' => Category::getHomeCategories($this->context->language->id),
			'have_image' => (isset($cover) ? (int)$cover['id_image'] : false),
			'tax_enabled' => Configuration::get('PS_TAX'),
			'display_qties' => (int)(Configuration::get('PS_DISPLAY_QTIES')),
			'display_ht' => !Tax::excludeTaxeOption(),
			'ecotax' => (!sizeof($this->errors) AND $this->product->ecotax > 0 ? Tools::convertPrice((float)($this->product->ecotax)) : 0),
			'currencySign' => $this->context->currency->sign,
			'currencyRate' => $this->context->currency->conversion_rate,
			'currencyFormat' => $this->context->currency->format,
			'currencyBlank' => $this->context->currency->blank,
			'jqZoomEnabled' => Configuration::get('PS_DISPLAY_JQZOOM')
		));
	}

	public function displayContent()
	{
		parent::displayContent();
		$this->context->smarty->display(_PS_THEME_DIR_.'product.tpl');
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
				{
					// Store customization in database
					$cart->addPictureToProduct($this->product->id, $indexes[$fieldName], _CUSTOMIZE_FILE_, $fileName);
				}
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
					$cart->addTextFieldToProduct($this->product->id, $indexes[$fieldName], _CUSTOMIZE_TEXTFIELD_, $value);
			}
			elseif (in_array($fieldName, $authorizedTextFields) AND empty($value))
				$cart->deleteCustomizationToProduct((int)($this->product->id), $indexes[$fieldName]);
	}

	public function formTargetFormat()
	{
		$customizationFormTarget = Tools::safeOutput(urldecode($_SERVER['REQUEST_URI']));
		foreach ($_GET AS $field => $value)
			if (strncmp($field, 'group_', 6) == 0)
				$customizationFormTarget = preg_replace('/&group_([[:digit:]]+)=([[:digit:]]+)/', '', $customizationFormTarget);
		if (isset($_POST['quantityBackup']))
			$this->context->smarty->assign('quantityBackup', (int)($_POST['quantityBackup']));
		$this->context->smarty->assign('customizationFormTarget', $customizationFormTarget);
	}

	public function formatQuantityDiscounts($specificPrices, $price, $taxRate)
	{
		foreach ($specificPrices AS $key => &$row)
		{
			$row['quantity'] = &$row['from_quantity'];
			// The price may be directly set
			if ($row['price'] != 0)
			{
			    $cur_price = (Product::$_taxCalculationMethod == PS_TAX_EXC ? $row['price'] : $row['price'] * (1 + $taxRate / 100));

                if ($row['reduction_type'] == 'amount')
			        $cur_price = Product::$_taxCalculationMethod == PS_TAX_INC ? $cur_price - $row['reduction'] : $cur_price - ($row['reduction'] / (1 + $taxRate / 100));
			    else
				    $cur_price = $cur_price * ( 1  - ($row['reduction']));

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
