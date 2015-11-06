<?php
/**
 * 2007-2015 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class ProductControllerCore extends FrontController
{
    public $php_self = 'product';

    /** @var Product */
    protected $product;

    /** @var Category */
    protected $category;

    public function setMedia()
    {
        parent::setMedia();
        if (count($this->errors)) {
            return;
        }

        if (!$this->useMobileTheme()) {
            $this->addCSS(_THEME_CSS_DIR_.'product.css');
            $this->addCSS(_THEME_CSS_DIR_.'print.css', 'print');
            $this->addJqueryPlugin(array('fancybox', 'idTabs', 'scrollTo', 'serialScroll', 'bxslider'));
            $this->addJS(array(
                _THEME_JS_DIR_.'tools.js',  // retro compat themes 1.5
                _THEME_JS_DIR_.'product.js'
            ));
        } else {
            $this->addJqueryPlugin(array('scrollTo', 'serialScroll'));
            $this->addJS(array(
                _THEME_JS_DIR_.'tools.js',  // retro compat themes 1.5
                _THEME_MOBILE_JS_DIR_.'product.js',
                _THEME_MOBILE_JS_DIR_.'jquery.touch-gallery.js'
            ));
        }

        if (Configuration::get('PS_DISPLAY_JQZOOM') == 1) {
            $this->addJqueryPlugin('jqzoom');
        }
    }

    public function canonicalRedirection($canonical_url = '')
    {
        if (Tools::getValue('live_edit')) {
            return;
        }
        if (Validate::isLoadedObject($this->product)) {
            parent::canonicalRedirection($this->context->link->getProductLink($this->product));
        }
    }

    /**
     * Initialize product controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        if ($id_product = (int)Tools::getValue('id_product')) {
            $this->product = new Product($id_product, true, $this->context->language->id, $this->context->shop->id);
        }

        if (!Validate::isLoadedObject($this->product)) {
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
            $this->errors[] = Tools::displayError('Product not found');
        } else {
            $this->canonicalRedirection();
            /*
             * If the product is associated to the shop
             * and is active or not active but preview mode (need token + file_exists)
             * allow showing the product
             * In all the others cases => 404 "Product is no longer available"
             */
            if (!$this->product->isAssociatedToShop() || !$this->product->active) {
                if (Tools::getValue('adtoken') == Tools::getAdminToken('AdminProducts'.(int)Tab::getIdFromClassName('AdminProducts').(int)Tools::getValue('id_employee')) && $this->product->isAssociatedToShop()) {
                    // If the product is not active, it's the admin preview mode
                    $this->context->smarty->assign('adminActionDisplay', true);
                } else {
                    $this->context->smarty->assign('adminActionDisplay', false);
                    if (!$this->product->id_product_redirected || $this->product->id_product_redirected == $this->product->id) {
                        $this->product->redirect_type = '404';
                    }

                    switch ($this->product->redirect_type) {
                        case '301':
                            header('HTTP/1.1 301 Moved Permanently');
                            header('Location: '.$this->context->link->getProductLink($this->product->id_product_redirected));
                            exit;
                        break;
                        case '302':
                            header('HTTP/1.1 302 Moved Temporarily');
                            header('Cache-Control: no-cache');
                            header('Location: '.$this->context->link->getProductLink($this->product->id_product_redirected));
                            exit;
                        break;
                        case '404':
                        default:
                            header('HTTP/1.1 404 Not Found');
                            header('Status: 404 Not Found');
                            $this->errors[] = Tools::displayError('This product is no longer available.');
                        break;
                    }
                }
            } elseif (!$this->product->checkAccess(isset($this->context->customer->id) && $this->context->customer->id ? (int)$this->context->customer->id : 0)) {
                header('HTTP/1.1 403 Forbidden');
                header('Status: 403 Forbidden');
                $this->errors[] = Tools::displayError('You do not have access to this product.');
            } else {
                // Load category
                $id_category = false;
                if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] == Tools::secureReferrer($_SERVER['HTTP_REFERER']) // Assure us the previous page was one of the shop
                    && preg_match('~^.*(?<!\/content)\/([0-9]+)\-(.*[^\.])|(.*)id_(category|product)=([0-9]+)(.*)$~', $_SERVER['HTTP_REFERER'], $regs)) {
                    // If the previous page was a category and is a parent category of the product use this category as parent category
                    $id_object = false;
                    if (isset($regs[1]) && is_numeric($regs[1])) {
                        $id_object = (int)$regs[1];
                    } elseif (isset($regs[5]) && is_numeric($regs[5])) {
                        $id_object = (int)$regs[5];
                    }
                    if ($id_object) {
                        $referers = array($_SERVER['HTTP_REFERER'],urldecode($_SERVER['HTTP_REFERER']));
                        if (in_array($this->context->link->getCategoryLink($id_object), $referers)) {
                            $id_category = (int)$id_object;
                        } elseif (isset($this->context->cookie->last_visited_category) && (int)$this->context->cookie->last_visited_category && in_array($this->context->link->getProductLink($id_object), $referers)) {
                            $id_category = (int)$this->context->cookie->last_visited_category;
                        }
                    }
                }
                if (!$id_category || !Category::inShopStatic($id_category, $this->context->shop) || !Product::idIsOnCategoryId((int)$this->product->id, array('0' => array('id_category' => $id_category)))) {
                    $id_category = (int)$this->product->id_category_default;
                }
                $this->category = new Category((int)$id_category, (int)$this->context->cookie->id_lang);
                if (isset($this->context->cookie) && isset($this->category->id_category) && !(Module::isInstalled('blockcategories') && Module::isEnabled('blockcategories'))) {
                    $this->context->cookie->last_visited_category = (int)$this->category->id_category;
                }
            }
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if (!$this->errors) {
            if (Pack::isPack((int)$this->product->id) && !Pack::isInStock((int)$this->product->id)) {
                $this->product->quantity = 0;
            }

            $this->product->description = $this->transformDescriptionWithImg($this->product->description);

            // Assign to the template the id of the virtual product. "0" if the product is not downloadable.
            $this->context->smarty->assign('virtual', ProductDownload::getIdFromIdProduct((int)$this->product->id));

            $this->context->smarty->assign('customizationFormTarget', Tools::safeOutput(urldecode($_SERVER['REQUEST_URI'])));

            $priceDisplay = Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer);
            $productPrice = 0;
            $productPriceWithoutReduction = 0;

            if (!$priceDisplay || $priceDisplay == 2) {
                $productPrice = $this->product->getPrice(true, null, 6);
                $productPriceWithoutReduction = $this->product->getPriceWithoutReduct(false, null);
            } elseif ($priceDisplay == 1) {
                $productPrice = $this->product->getPrice(false, null, 6);
                $productPriceWithoutReduction = $this->product->getPriceWithoutReduct(true, null);
            }

            if (Tools::isSubmit('submitCustomizedDatas')) {
                // If cart has not been saved, we need to do it so that customization fields can have an id_cart
                // We check that the cookie exists first to avoid ghost carts
                if (!$this->context->cart->id && isset($_COOKIE[$this->context->cookie->getName()])) {
                    $this->context->cart->add();
                    $this->context->cookie->id_cart = (int)$this->context->cart->id;
                }
                $this->pictureUpload();
                $this->textRecord();
                $this->formTargetFormat();
            } elseif (Tools::getIsset('deletePicture') && !$this->context->cart->deleteCustomizationToProduct($this->product->id, Tools::getValue('deletePicture'))) {
                $this->errors[] = Tools::displayError('An error occurred while deleting the selected picture.');
            }

            $pictures = array();
            $text_fields = array();
            if ($this->product->customizable) {
                $files = $this->context->cart->getProductCustomization($this->product->id, Product::CUSTOMIZE_FILE, true);
                foreach ($files as $file) {
                    $pictures['pictures_'.$this->product->id.'_'.$file['index']] = $file['value'];
                }

                $texts = $this->context->cart->getProductCustomization($this->product->id, Product::CUSTOMIZE_TEXTFIELD, true);

                foreach ($texts as $text_field) {
                    $text_fields['textFields_'.$this->product->id.'_'.$text_field['index']] = str_replace('<br />', "\n", $text_field['value']);
                }
            }

            $this->context->smarty->assign(array(
                'pictures' => $pictures,
                'textFields' => $text_fields));

            $this->product->customization_required = false;
            $customization_fields = $this->product->customizable ? $this->product->getCustomizationFields($this->context->language->id) : false;
            if (is_array($customization_fields)) {
                foreach ($customization_fields as $customization_field) {
                    if ($this->product->customization_required = $customization_field['required']) {
                        break;
                    }
                }
            }

            // Assign template vars related to the category + execute hooks related to the category
            $this->assignCategory();
            // Assign template vars related to the price and tax
            $this->assignPriceAndTax();

            // Assign template vars related to the images
            $this->assignImages();
            // Assign attribute groups to the template
            $this->assignAttributesGroups();

            // Assign attributes combinations to the template
            $this->assignAttributesCombinations();

            $this->assignLabels($productPrice, $productPriceWithoutReduction);

            // Pack management
            $pack_items = Pack::isPack($this->product->id) ? Pack::getItemTable($this->product->id, $this->context->language->id, true) : array();
            $this->context->smarty->assign('packItems', $pack_items);
            $this->context->smarty->assign('packs', Pack::getPacksTable($this->product->id, $this->context->language->id, true, 1));

            if (isset($this->category->id) && $this->category->id) {
                $return_link = Tools::safeOutput($this->context->link->getCategoryLink($this->category));
            } else {
                $return_link = 'javascript: history.back();';
            }

            $accessories = $this->product->getAccessories($this->context->language->id);
            if ($this->product->cache_is_pack || count($accessories)) {
                $this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');
            }
            if ($this->product->customizable) {
                $customization_datas = $this->context->cart->getProductCustomization($this->product->id, null, true);
            }

            $this->context->smarty->assign(array(
                'priceDisplay' => $priceDisplay,
                'productPrice' => $productPrice,
                'productPriceWithoutReduction' => $productPriceWithoutReduction,
                'display_quantities' => ((bool)Configuration::get('PS_DISPLAY_QTIES') && (bool)Configuration::get('PS_STOCK_MANAGEMENT') && (bool)$this->product->available_for_order && !((bool)Configuration::get('PS_CATALOG_MODE') || (Group::isFeatureActive() && !(bool)Group::getCurrent()->show_prices))) ? true : false,
                'stock_management' => Configuration::get('PS_STOCK_MANAGEMENT'),
                'customizationFields' => $customization_fields,
                'id_customization' => empty($customization_datas) ? null : $customization_datas[0]['id_customization'],
                'accessories' => $accessories,
                'return_link' => $return_link,
                'product' => $this->product,
                'product_manufacturer' => new Manufacturer((int)$this->product->id_manufacturer, $this->context->language->id),
                'token' => Tools::getToken(false),
                'features' => $this->product->getFrontFeatures($this->context->language->id),
                'attachments' => (($this->product->cache_has_attachments) ? $this->product->getAttachments($this->context->language->id) : array()),
                'allow_oosp' => $this->product->isAvailableWhenOutOfStock((int)$this->product->out_of_stock),
                'last_qties' =>  (int)Configuration::get('PS_LAST_QTIES'),
                'HOOK_EXTRA_LEFT' => Hook::exec('displayLeftColumnProduct'),
                'HOOK_EXTRA_RIGHT' => Hook::exec('displayRightColumnProduct'),
                'HOOK_PRODUCT_OOS' => Hook::exec('actionProductOutOfStock', array('product' => $this->product)),
                'HOOK_PRODUCT_ACTIONS' => Hook::exec('displayProductButtons', array('product' => $this->product)),
                'HOOK_PRODUCT_TAB' =>  Hook::exec('displayProductTab', array('product' => $this->product)),
                'HOOK_PRODUCT_TAB_CONTENT' =>  Hook::exec('displayProductTabContent', array('product' => $this->product)),
                'HOOK_PRODUCT_CONTENT' =>  Hook::exec('displayProductContent', array('product' => $this->product)),
                'display_qties' => (int)Configuration::get('PS_DISPLAY_QTIES'),
                'display_ht' => !Tax::excludeTaxeOption(),
                'jqZoomEnabled' => Configuration::get('PS_DISPLAY_JQZOOM'),
                'ENT_NOQUOTES' => ENT_NOQUOTES,
                'outOfStockAllowed' => (int)Configuration::get('PS_ORDER_OUT_OF_STOCK'),
                'errors' => $this->errors,
                'body_classes' => array(
                    $this->php_self.'-'.$this->product->id,
                    $this->php_self.'-'.$this->product->link_rewrite,
                    'category-'.(isset($this->category) ? $this->category->id : ''),
                    'category-'.(isset($this->category) ? $this->category->getFieldByLang('link_rewrite') : '')
                ),
                'display_discount_price' => Configuration::get('PS_DISPLAY_DISCOUNT_PRICE'),
            ));
        }
        $this->setTemplate('catalog/product.tpl');
    }

    /**
     * Assign price and tax to the template
     */
    protected function assignPriceAndTax()
    {
        $id_customer = (isset($this->context->customer) ? (int)$this->context->customer->id : 0);
        $id_group = (int)Group::getCurrent()->id;
        $id_country = $id_customer ? (int)Customer::getCurrentCountry($id_customer) : (int)Tools::getCountry();

        $group_reduction = GroupReduction::getValueForProduct($this->product->id, $id_group);
        if ($group_reduction === false) {
            $group_reduction = Group::getReduction((int)$this->context->cookie->id_customer) / 100;
        }

        // Tax
        $tax = (float)$this->product->getTaxesRate(new Address((int)$this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
        $this->context->smarty->assign('tax_rate', $tax);

        $product_price_with_tax = Product::getPriceStatic($this->product->id, true, null, 6);
        if (Product::$_taxCalculationMethod == PS_TAX_INC) {
            $product_price_with_tax = Tools::ps_round($product_price_with_tax, 2);
        }
        $product_price_without_eco_tax = (float)$product_price_with_tax - $this->product->ecotax;

        $ecotax_rate = (float)Tax::getProductEcotaxRate($this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $ecotax_tax_amount = Tools::ps_round($this->product->ecotax, 2);
        if (Product::$_taxCalculationMethod == PS_TAX_INC && (int)Configuration::get('PS_TAX')) {
            $ecotax_tax_amount = Tools::ps_round($ecotax_tax_amount * (1 + $ecotax_rate / 100), 2);
        }

        $id_currency = (int)$this->context->cookie->id_currency;
        $id_product = (int)$this->product->id;
        $id_shop = $this->context->shop->id;

        $quantity_discounts = SpecificPrice::getQuantityDiscounts($id_product, $id_shop, $id_currency, $id_country, $id_group, null, true, (int)$this->context->customer->id);
        foreach ($quantity_discounts as &$quantity_discount) {
            if ($quantity_discount['id_product_attribute']) {
                $combination = new Combination((int)$quantity_discount['id_product_attribute']);
                $attributes = $combination->getAttributesName((int)$this->context->language->id);
                foreach ($attributes as $attribute) {
                    $quantity_discount['attributes'] = $attribute['name'].' - ';
                }
                $quantity_discount['attributes'] = rtrim($quantity_discount['attributes'], ' - ');
            }
            if ((int)$quantity_discount['id_currency'] == 0 && $quantity_discount['reduction_type'] == 'amount') {
                $quantity_discount['reduction'] = Tools::convertPriceFull($quantity_discount['reduction'], null, Context::getContext()->currency);
            }
        }

        $product_price = $this->product->getPrice(Product::$_taxCalculationMethod == PS_TAX_INC, false);
        $address = new Address($this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $this->context->smarty->assign(array(
            'quantity_discounts' => $this->formatQuantityDiscounts($quantity_discounts, $product_price, (float)$tax, $ecotax_tax_amount),
            'ecotax_tax_inc' => $ecotax_tax_amount,
            'ecotax_tax_exc' => Tools::ps_round($this->product->ecotax, 2),
            'ecotaxTax_rate' => $ecotax_rate,
            'productPriceWithoutEcoTax' => (float)$product_price_without_eco_tax,
            'group_reduction' => $group_reduction,
            'no_tax' => Tax::excludeTaxeOption() || !$this->product->getTaxesRate($address),
            'ecotax' => (!count($this->errors) && $this->product->ecotax > 0 ? Tools::convertPrice((float)$this->product->ecotax) : 0),
            'tax_enabled' => Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC'),
            'customer_group_without_tax' => Group::getPriceDisplayMethod($this->context->customer->id_default_group),
        ));
    }

    /**
     * Assign template vars related to images
     */
    protected function assignImages()
    {
        $images = $this->product->getImages((int)$this->context->cookie->id_lang);
        $product_images = array();

        if (count($images) > 0) {
            foreach ($images as $image) {
                $product_images[(int)$image['id_image']] = array(
                    'cover' => $image['cover'],
                    'id_image' => (int)$image['id_image'],
                    'legend' => !empty($image['legend']) ? $image['legend'] : $this->product->name,
                    'urls' => $this->getImageUrls('products', (int)$image['id_image']),
                    'position' => $image['position'],
                );
            }
        } else {
            $product_images[] = array(
                'cover' => true,
                'id_image' => 0,
                'legend' => $this->product->name,
                'urls' => $this->getImageUrls('products'),
                'position' => 1,
            );
        }

        $this->context->smarty->assign(array(
            'images' => $product_images,
            'col_img_dir' => _PS_COL_IMG_DIR_,
        ));
    }

    protected function getImageUrls($type, $id_image = null)
    {
        $urls = array();
        $product_images_types = array_reverse(ImageType::getImagesTypes($type, true));

        foreach ($product_images_types as $image_type) {
            $urls[$image_type['name']] = array(
                'link' => !is_null($id_image) ? $this->context->link->getImageLink($this->product->link_rewrite, (int)$id_image, $image_type['name']) : _THEME_PROD_DIR_.$this->context->language->iso_code.'-default-large_default.jpg',
                'width' => (int)$image_type['width'],
                'height' => (int)$image_type['height'],
                );
        }

        $urls['default'] = current($urls);

        return $urls;
    }

    protected function assignLabels($productPrice = 0, $productPriceWithoutReduction = 0)
    {
        $labels = array();

        if ($this->product->new) {
            $labels['new'] = array(
                'type' => 'new',
                'label' => $this->l('New'),
                );
        }

        if ($this->product->on_sale) {
            $labels['on_sale'] = array(
                'type' => 'on_sale',
                'label' => $this->l('Sale!'),
                );
        }

        if ($this->product->online_only) {
            $labels['online_only'] = array(
                'type' => 'online_only',
                'label' => $this->l('Online only'),
                );
        }

        if ($this->product->specificPrice && $this->product->specificPrice['reduction'] && (int)$productPriceWithoutReduction > (int)$productPrice) {
            $labels['discount'] = array(
                'type' => 'discount',
                'label' => $this->l('Reduced price!'),
                );
        }

        $this->context->smarty->assign(array(
            'labels' => $labels
        ));
    }

    /**
     * Assign template vars related to attribute groups and colors
     */
    protected function assignAttributesGroups()
    {
        $colors = array();
        $groups = array();

        // @todo (RM) should only get groups and not all declination ?
        $attributes_groups = $this->product->getAttributesGroups($this->context->language->id);
        if (is_array($attributes_groups) && $attributes_groups) {
            $combination_images = $this->product->getCombinationImages($this->context->language->id);
            $combination_prices_set = array();
            foreach ($attributes_groups as $k => $row) {
                // Color management
                if (isset($row['is_color_group']) && $row['is_color_group'] && (isset($row['attribute_color']) && $row['attribute_color']) || (file_exists(_PS_COL_IMG_DIR_.$row['id_attribute'].'.jpg'))) {
                    $colors[$row['id_attribute']]['value'] = $row['attribute_color'];
                    $colors[$row['id_attribute']]['name'] = $row['attribute_name'];
                    if (!isset($colors[$row['id_attribute']]['attributes_quantity'])) {
                        $colors[$row['id_attribute']]['attributes_quantity'] = 0;
                    }
                    $colors[$row['id_attribute']]['attributes_quantity'] += (int)$row['quantity'];
                }
                if (!isset($groups[$row['id_attribute_group']])) {
                    $groups[$row['id_attribute_group']] = array(
                        'group_name' => $row['group_name'],
                        'name' => $row['public_group_name'],
                        'group_type' => $row['group_type'],
                        'default' => -1,
                    );
                }

                $groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = $row['attribute_name'];
                if ($row['default_on'] && $groups[$row['id_attribute_group']]['default'] == -1) {
                    $groups[$row['id_attribute_group']]['default'] = (int)$row['id_attribute'];
                }
                if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']])) {
                    $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
                }
                $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] += (int)$row['quantity'];

                $combinations[$row['id_product_attribute']]['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
                $combinations[$row['id_product_attribute']]['attributes'][] = (int)$row['id_attribute'];
                $combinations[$row['id_product_attribute']]['price'] = (float)$row['price'];

                // Call getPriceStatic in order to set $combination_specific_price
                if (!isset($combination_prices_set[(int)$row['id_product_attribute']])) {
                    Product::getPriceStatic((int)$this->product->id, false, $row['id_product_attribute'], 6, null, false, true, 1, false, null, null, null, $combination_specific_price);
                    $combination_prices_set[(int)$row['id_product_attribute']] = true;
                    $combinations[$row['id_product_attribute']]['specific_price'] = $combination_specific_price;
                }
                $combinations[$row['id_product_attribute']]['ecotax'] = (float)$row['ecotax'];
                $combinations[$row['id_product_attribute']]['weight'] = (float)$row['weight'];
                $combinations[$row['id_product_attribute']]['quantity'] = (int)$row['quantity'];
                $combinations[$row['id_product_attribute']]['reference'] = $row['reference'];
                $combinations[$row['id_product_attribute']]['unit_impact'] = $row['unit_price_impact'];
                $combinations[$row['id_product_attribute']]['minimal_quantity'] = $row['minimal_quantity'];
                if ($row['available_date'] != '0000-00-00' && Validate::isDate($row['available_date'])) {
                    $combinations[$row['id_product_attribute']]['available_date'] = $row['available_date'];
                    $combinations[$row['id_product_attribute']]['date_formatted'] = Tools::displayDate($row['available_date']);
                } else {
                    $combinations[$row['id_product_attribute']]['available_date'] = $combinations[$row['id_product_attribute']]['date_formatted'] = '';
                }

                if (!isset($combination_images[$row['id_product_attribute']][0]['id_image'])) {
                    $combinations[$row['id_product_attribute']]['id_image'] = -1;
                } else {
                    $combinations[$row['id_product_attribute']]['id_image'] = $id_image = (int)$combination_images[$row['id_product_attribute']][0]['id_image'];
                    if ($row['default_on']) {
                        foreach ($this->context->smarty->tpl_vars['images']->value as $image) {
                            if ($image['cover'] == 1) {
                                $current_cover = $image;
                            }
                        }

                        if (is_array($combination_images[$row['id_product_attribute']])) {
                            foreach ($combination_images[$row['id_product_attribute']] as $tmp) {
                                if ($tmp['id_image'] == $current_cover['id_image']) {
                                    $combinations[$row['id_product_attribute']]['id_image'] = $id_image = (int)$tmp['id_image'];
                                    break;
                                }
                            }
                        }

                        if ($id_image > 0) {
                            if (isset($this->context->smarty->tpl_vars['images']->value)) {
                                $product_images = $this->context->smarty->tpl_vars['images']->value;
                            }
                            if (isset($product_images) && is_array($product_images) && isset($product_images[$id_image])) {
                                $product_images[$id_image]['cover'] = 1;
                                $this->context->smarty->assign('mainImage', $product_images[$id_image]);
                                if (count($product_images)) {
                                    $this->context->smarty->assign('images', $product_images);
                                }
                            }
                            $cover = $current_cover;
                            if (isset($cover) && is_array($cover) && isset($product_images) && is_array($product_images)) {
                                $product_images[$cover['id_image']]['cover'] = 0;
                                if (isset($product_images[$id_image])) {
                                    $cover = $product_images[$id_image];
                                }
                                $cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($this->product->id.'-'.$id_image) : (int)$id_image);
                                $cover['id_image_only'] = (int)$id_image;
                                $this->context->smarty->assign('cover', $cover);
                            }
                        }
                    }
                }
            }

            // wash attributes list (if some attributes are unavailables and if allowed to wash it)
            if (!Product::isAvailableWhenOutOfStock($this->product->out_of_stock) && Configuration::get('PS_DISP_UNAVAILABLE_ATTR') == 0) {
                foreach ($groups as &$group) {
                    foreach ($group['attributes_quantity'] as $key => &$quantity) {
                        if ($quantity <= 0) {
                            unset($group['attributes'][$key]);
                        }
                    }
                }

                foreach ($colors as $key => $color) {
                    if ($color['attributes_quantity'] <= 0) {
                        unset($colors[$key]);
                    }
                }
            }
            foreach ($combinations as $id_product_attribute => $comb) {
                $attribute_list = '';
                foreach ($comb['attributes'] as $id_attribute) {
                    $attribute_list .= '\''.(int)$id_attribute.'\',';
                }
                $attribute_list = rtrim($attribute_list, ',');
                $combinations[$id_product_attribute]['list'] = $attribute_list;
            }

            $this->context->smarty->assign(array(
                'groups' => $groups,
                'colors' => (count($colors)) ? $colors : false,
                'combinations' => $combinations,
                'combinationImages' => $combination_images
            ));
        }
    }

    /**
     * Get and assign attributes combinations informations
     */
    protected function assignAttributesCombinations()
    {
        $attributes_combinations = Product::getAttributesInformationsByProduct($this->product->id);
        if (is_array($attributes_combinations) && count($attributes_combinations)) {
            foreach ($attributes_combinations as &$ac) {
                foreach ($ac as &$val) {
                    $val = str_replace(Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR'), '_', Tools::link_rewrite(str_replace(array(',', '.'), '-', $val)));
                }
            }
        } else {
            $attributes_combinations = array();
        }
        $this->context->smarty->assign(array(
            'attributesCombinations' =>  $attributes_combinations,
            'attribute_anchor_separator' => Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR')
            )
        );
    }

    /**
     * Assign template vars related to category
     */
    protected function assignCategory()
    {
        // Assign category to the template
        if ($this->category !== false && Validate::isLoadedObject($this->category) && $this->category->inShop() && $this->category->isAssociatedToShop()) {
            $path = Tools::getPath($this->category->id, $this->product->name, true);
        } elseif (Category::inShopStatic($this->product->id_category_default, $this->context->shop)) {
            $this->category = new Category((int)$this->product->id_category_default, (int)$this->context->language->id);
            if (Validate::isLoadedObject($this->category) && $this->category->active && $this->category->isAssociatedToShop()) {
                $path = Tools::getPath((int)$this->product->id_category_default, $this->product->name);
            }
        }
        if (!isset($path) || !$path) {
            $path = Tools::getPath((int)$this->context->shop->id_category, $this->product->name);
        }

        $sub_categories = array();
        if (Validate::isLoadedObject($this->category)) {
            $sub_categories = $this->category->getSubCategories($this->context->language->id, true);

            // various assignements before Hook::exec
            $this->context->smarty->assign(array(
                'path' => $path,
                'category' => $this->category,
                'subCategories' => $sub_categories,
                'id_category_current' => (int)$this->category->id,
                'id_category_parent' => (int)$this->category->id_parent,
                'return_category_name' => Tools::safeOutput($this->category->getFieldByLang('name')),
                'categories' => Category::getHomeCategories($this->context->language->id, true, (int)$this->context->shop->id)
            ));
        }
        $this->context->smarty->assign(array('HOOK_PRODUCT_FOOTER' => Hook::exec('displayFooterProduct', array('product' => $this->product, 'category' => $this->category))));
    }

    protected function transformDescriptionWithImg($desc)
    {
        $reg = '/\[img\-([0-9]+)\-(left|right)\-([a-zA-Z0-9-_]+)\]/';
        while (preg_match($reg, $desc, $matches)) {
            $link_lmg = $this->context->link->getImageLink($this->product->link_rewrite, $this->product->id.'-'.$matches[1], $matches[3]);
            $class = $matches[2] == 'left' ? 'class="imageFloatLeft"' : 'class="imageFloatRight"';
            $html_img = '<img src="'.$link_lmg.'" alt="" '.$class.'/>';
            $desc = str_replace($matches[0], $html_img, $desc);
        }
        return $desc;
    }

    protected function pictureUpload()
    {
        if (!$field_ids = $this->product->getCustomizationFieldIds()) {
            return false;
        }
        $authorized_file_fields = array();
        foreach ($field_ids as $field_id) {
            if ($field_id['type'] == Product::CUSTOMIZE_FILE) {
                $authorized_file_fields[(int)$field_id['id_customization_field']] = 'file'.(int)$field_id['id_customization_field'];
            }
        }
        $indexes = array_flip($authorized_file_fields);
        foreach ($_FILES as $field_name => $file) {
            if (in_array($field_name, $authorized_file_fields) && isset($file['tmp_name']) && !empty($file['tmp_name'])) {
                $file_name = md5(uniqid(rand(), true));
                if ($error = ImageManager::validateUpload($file, (int)Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE'))) {
                    $this->errors[] = $error;
                }

                $product_picture_width = (int)Configuration::get('PS_PRODUCT_PICTURE_WIDTH');
                $product_picture_height = (int)Configuration::get('PS_PRODUCT_PICTURE_HEIGHT');
                $tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                if ($error || (!$tmp_name || !move_uploaded_file($file['tmp_name'], $tmp_name))) {
                    return false;
                }
                /* Original file */
                if (!ImageManager::resize($tmp_name, _PS_UPLOAD_DIR_.$file_name)) {
                    $this->errors[] = Tools::displayError('An error occurred during the image upload process.');
                }
                /* A smaller one */
                elseif (!ImageManager::resize($tmp_name, _PS_UPLOAD_DIR_.$file_name.'_small', $product_picture_width, $product_picture_height)) {
                    $this->errors[] = Tools::displayError('An error occurred during the image upload process.');
                } elseif (!chmod(_PS_UPLOAD_DIR_.$file_name, 0777) || !chmod(_PS_UPLOAD_DIR_.$file_name.'_small', 0777)) {
                    $this->errors[] = Tools::displayError('An error occurred during the image upload process.');
                } else {
                    $this->context->cart->addPictureToProduct($this->product->id, $indexes[$field_name], Product::CUSTOMIZE_FILE, $file_name);
                }
                unlink($tmp_name);
            }
        }
        return true;
    }

    protected function textRecord()
    {
        if (!$field_ids = $this->product->getCustomizationFieldIds()) {
            return false;
        }

        $authorized_text_fields = array();
        foreach ($field_ids as $field_id) {
            if ($field_id['type'] == Product::CUSTOMIZE_TEXTFIELD) {
                $authorized_text_fields[(int)$field_id['id_customization_field']] = 'textField'.(int)$field_id['id_customization_field'];
            }
        }

        $indexes = array_flip($authorized_text_fields);
        foreach ($_POST as $field_name => $value) {
            if (in_array($field_name, $authorized_text_fields) && $value != '') {
                if (!Validate::isMessage($value)) {
                    $this->errors[] = Tools::displayError('Invalid message');
                } else {
                    $this->context->cart->addTextFieldToProduct($this->product->id, $indexes[$field_name], Product::CUSTOMIZE_TEXTFIELD, $value);
                }
            } elseif (in_array($field_name, $authorized_text_fields) && $value == '') {
                $this->context->cart->deleteCustomizationToProduct((int)$this->product->id, $indexes[$field_name]);
            }
        }
    }

    protected function formTargetFormat()
    {
        $customization_form_target = Tools::safeOutput(urldecode($_SERVER['REQUEST_URI']));
        foreach ($_GET as $field => $value) {
            if (strncmp($field, 'group_', 6) == 0) {
                $customization_form_target = preg_replace('/&group_([[:digit:]]+)=([[:digit:]]+)/', '', $customization_form_target);
            }
        }
        if (isset($_POST['quantityBackup'])) {
            $this->context->smarty->assign('quantityBackup', (int)$_POST['quantityBackup']);
        }
        $this->context->smarty->assign('customizationFormTarget', $customization_form_target);
    }

    protected function formatQuantityDiscounts($specific_prices, $price, $tax_rate, $ecotax_amount)
    {
        foreach ($specific_prices as $key => &$row) {
            $row['quantity'] = &$row['from_quantity'];
            if ($row['price'] >= 0) {
                // The price may be directly set

                $cur_price = (!$row['reduction_tax'] ? $row['price'] : $row['price'] * (1 + $tax_rate / 100)) + (float)$ecotax_amount;

                if ($row['reduction_type'] == 'amount') {
                    $cur_price -= ($row['reduction_tax'] ? $row['reduction'] : $row['reduction'] / (1 + $tax_rate / 100));
                    $row['reduction_with_tax'] = $row['reduction_tax'] ? $row['reduction'] : $row['reduction'] / (1 + $tax_rate / 100);
                } else {
                    $cur_price *= 1 - $row['reduction'];
                }

                $row['real_value'] = $price > 0 ? $price - $cur_price : $cur_price;
            } else {
                if ($row['reduction_type'] == 'amount') {
                    if (Product::$_taxCalculationMethod == PS_TAX_INC) {
                        $row['real_value'] = $row['reduction_tax'] == 1 ? $row['reduction'] : $row['reduction'] * (1 + $tax_rate / 100);
                    } else {
                        $row['real_value'] = $row['reduction_tax'] == 0 ? $row['reduction'] : $row['reduction'] / (1 + $tax_rate / 100);
                    }
                    $row['reduction_with_tax'] = $row['reduction_tax'] ? $row['reduction'] : $row['reduction'] +  ($row['reduction'] *$tax_rate) / 100;
                } else {
                    $row['real_value'] = $row['reduction'] * 100;
                }
            }
            $row['nextQuantity'] = (isset($specific_prices[$key + 1]) ? (int)$specific_prices[$key + 1]['from_quantity'] : - 1);
        }
        return $specific_prices;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function getCategory()
    {
        return $this->category;
    }
}
