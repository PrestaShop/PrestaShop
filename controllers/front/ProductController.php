<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductExtraContentFinder;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class ProductControllerCore extends ProductPresentingFrontControllerCore
{
    public $php_self = 'product';

    /** @var Product */
    protected $product;

    /** @var Category */
    protected $category;

    protected $redirectionExtraExcludedKeys = ['id_product_attribute', 'rewrite'];

    /**
     * @var array
     */
    protected $combinations;

    private $quantity_discounts;
    private $adminNotifications = array();

    public function canonicalRedirection($canonical_url = '')
    {
        if (Validate::isLoadedObject($this->product)) {
            if (!$this->product->hasCombinations()) {
                unset($_GET['id_product_attribute']);
            } else if (!$this->isValidCombination(Tools::getValue('id_product_attribute'), $this->product->id)) {
                $_GET['id_product_attribute'] = Product::getDefaultAttribute($this->product->id);
            }
            $idProductAttribute = $this->getIdProductAttribute(false);
            parent::canonicalRedirection($this->context->link->getProductLink(
                $this->product,
                null,
                null,
                null,
                null,
                null,
                $idProductAttribute
            ));
        }
    }

    /**
     * Initialize product controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        $id_product = (int) Tools::getValue('id_product');

        $this->setTemplate('catalog/product', array(
            'entity' => 'product',
            'id' => $id_product,
        ));

        if ($id_product) {
            $this->product = new Product($id_product, true, $this->context->language->id, $this->context->shop->id);
        }

        if (!Validate::isLoadedObject($this->product)) {
            Tools::redirect('index.php?controller=404');
        } else {
            $this->canonicalRedirection();
            /*
             * If the product is associated to the shop
             * and is active or not active but preview mode (need token + file_exists)
             * allow showing the product
             * In all the others cases => 404 "Product is no longer available"
             */
            $isAssociatedToProduct = (
                Tools::getValue('adtoken') == Tools::getAdminToken(
                    'AdminProducts'
                    .(int) Tab::getIdFromClassName('AdminProducts')
                    .(int) Tools::getValue('id_employee')
                )
                && $this->product->isAssociatedToShop()
            );
            $isPreview = ('1' === Tools::getValue('preview'));
            if ((!$this->product->isAssociatedToShop() || !$this->product->active) && !$isPreview) {
                if ($isAssociatedToProduct) {
                    $this->adminNotifications['inactive_product'] = array(
                        'type' => 'warning',
                        'message' => $this->trans('This product is not visible to your customers.', array(), 'Shop.Notifications.Warning'),
                    );
                } else {
                    if (!$this->product->id_type_redirected ||
                        (in_array($this->product->redirect_type, array('301-product', '302-product')) && $this->product->id_type_redirected == $this->product->id)
                    ) {
                        $this->product->redirect_type = '404';
                    }

                    switch ($this->product->redirect_type) {
                        case '301-product':
                            header('HTTP/1.1 301 Moved Permanently');
                            header('Location: '.$this->context->link->getProductLink($this->product->id_type_redirected));
                            exit;
                        break;
                        case '302-product':
                            header('HTTP/1.1 302 Moved Temporarily');
                            header('Cache-Control: no-cache');
                            header('Location: '.$this->context->link->getProductLink($this->product->id_type_redirected));
                            exit;
                        break;
                        case '301-category':
                            header('HTTP/1.1 301 Moved Permanently');
                            header('Location: '.$this->context->link->getCategoryLink($this->product->id_type_redirected));
                            exit;
                            break;
                        case '302-category':
                            header('HTTP/1.1 302 Moved Temporarily');
                            header('Cache-Control: no-cache');
                            header('Location: '.$this->context->link->getCategoryLink($this->product->id_type_redirected));
                            exit;
                            break;
                        case '404':
                        default:
                            header('HTTP/1.1 404 Not Found');
                            header('Status: 404 Not Found');
                            $this->errors[] = $this->trans('This product is no longer available.', array(), 'Shop.Notifications.Error');
                            $this->setTemplate('errors/404');
                            break;
                    }
                }
            } elseif (!$this->product->checkAccess(isset($this->context->customer->id) && $this->context->customer->id ? (int) $this->context->customer->id : 0)) {
                header('HTTP/1.1 403 Forbidden');
                header('Status: 403 Forbidden');
                $this->errors[] = $this->trans('You do not have access to this product.', array(), 'Shop.Notifications.Error');
                $this->setTemplate('errors/forbidden');
            } else {
                if ($isAssociatedToProduct && $isPreview) {
                    $this->adminNotifications['inactive_product'] = array(
                        'type' => 'warning',
                        'message' => $this->trans('This product is not visible to your customers.', array(), 'Shop.Notifications.Warning'),
                    );
                }
                // Load category
                $id_category = false;
                if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] == Tools::secureReferrer($_SERVER['HTTP_REFERER']) // Assure us the previous page was one of the shop
                    && preg_match('~^.*(?<!\/content)\/([0-9]+)\-(.*[^\.])|(.*)id_(category|product)=([0-9]+)(.*)$~', $_SERVER['HTTP_REFERER'], $regs)) {
                    // If the previous page was a category and is a parent category of the product use this category as parent category
                    $id_object = false;
                    if (isset($regs[1]) && is_numeric($regs[1])) {
                        $id_object = (int) $regs[1];
                    } elseif (isset($regs[5]) && is_numeric($regs[5])) {
                        $id_object = (int) $regs[5];
                    }
                    if ($id_object) {
                        $referers = array($_SERVER['HTTP_REFERER'], urldecode($_SERVER['HTTP_REFERER']));
                        if (in_array($this->context->link->getCategoryLink($id_object), $referers)) {
                            $id_category = (int) $id_object;
                        } elseif (isset($this->context->cookie->last_visited_category) && (int) $this->context->cookie->last_visited_category && in_array($this->context->link->getProductLink($id_object), $referers)) {
                            $id_category = (int) $this->context->cookie->last_visited_category;
                        }
                    }
                }
                if (!$id_category || !Category::inShopStatic($id_category, $this->context->shop) || !Product::idIsOnCategoryId((int) $this->product->id, array('0' => array('id_category' => $id_category)))) {
                    $id_category = (int) $this->product->id_category_default;
                }
                $this->category = new Category((int) $id_category, (int) $this->context->cookie->id_lang);
                $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
                $moduleManager = $moduleManagerBuilder->build();

                if (isset($this->context->cookie) && isset($this->category->id_category) && !($moduleManager->isInstalled('ps_categorytree') && $moduleManager->isEnabled('ps_categorytree'))) {
                    $this->context->cookie->last_visited_category = (int) $this->category->id_category;
                }
            }
        }
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        if (!$this->errors) {
            if (Pack::isPack((int) $this->product->id)
                && !Pack::isInStock((int) $this->product->id, $this->product->minimal_quantity, $this->context->cart)
            ) {
                $this->product->quantity = 0;
            }

            $this->product->description = $this->transformDescriptionWithImg($this->product->description);

            $priceDisplay = Product::getTaxCalculationMethod((int) $this->context->cookie->id_customer);
            $productPrice = 0;
            $productPriceWithoutReduction = 0;

            if (!$priceDisplay || $priceDisplay == 2) {
                $productPrice = $this->product->getPrice(true, null, 6);
                $productPriceWithoutReduction = $this->product->getPriceWithoutReduct(false, null);
            } elseif ($priceDisplay == 1) {
                $productPrice = $this->product->getPrice(false, null, 6);
                $productPriceWithoutReduction = $this->product->getPriceWithoutReduct(true, null);
            }

            if (Tools::isSubmit('submitCustomizedData')) {
                // If cart has not been saved, we need to do it so that customization fields can have an id_cart
                // We check that the cookie exists first to avoid ghost carts
                if (!$this->context->cart->id && isset($_COOKIE[$this->context->cookie->getName()])) {
                    $this->context->cart->add();
                    $this->context->cookie->id_cart = (int) $this->context->cart->id;
                }
                $this->pictureUpload();
                $this->textRecord();
            } elseif (Tools::getIsset('deletePicture') && !$this->context->cart->deleteCustomizationToProduct($this->product->id, Tools::getValue('deletePicture'))) {
                $this->errors[] = $this->trans('An error occurred while deleting the selected picture.', array(), 'Shop.Notifications.Error');
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
                'textFields' => $text_fields, ));

            $this->product->customization_required = false;
            $customization_fields = $this->product->customizable ? $this->product->getCustomizationFields($this->context->language->id) : false;
            if (is_array($customization_fields)) {
                foreach ($customization_fields as &$customization_field) {
                    if ($customization_field['type'] == 0) {
                        $customization_field['key'] = 'pictures_'.$this->product->id.'_'.$customization_field['id_customization_field'];
                    } elseif ($customization_field['type'] == 1) {
                        $customization_field['key'] = 'textFields_'.$this->product->id.'_'.$customization_field['id_customization_field'];
                    }
                }
                unset($customization_field);
            }

            // Assign template vars related to the category + execute hooks related to the category
            $this->assignCategory();
            // Assign template vars related to the price and tax
            $this->assignPriceAndTax();

            // Assign attributes combinations to the template
            $this->assignAttributesCombinations();

            // Pack management
            $pack_items = Pack::isPack($this->product->id) ? Pack::getItemTable($this->product->id, $this->context->language->id, true) : array();

            $assembler = new ProductAssembler($this->context);
            $presenter = new ProductListingPresenter(
                new ImageRetriever(
                    $this->context->link
                ),
                $this->context->link,
                new PriceFormatter(),
                new ProductColorsRetriever(),
                $this->getTranslator()
            );
            $presentationSettings = $this->getProductPresentationSettings();

            $presentedPackItems = array();
            foreach ($pack_items as $item) {
                $presentedPackItems[] = $presenter->present(
                    $this->getProductPresentationSettings(),
                    $assembler->assembleProduct($item),
                    $this->context->language
                );
            }

            $this->context->smarty->assign('packItems', $presentedPackItems);
            $this->context->smarty->assign('noPackPrice', $this->product->getNoPackPrice());
            $this->context->smarty->assign('displayPackPrice', ($pack_items && $productPrice < $this->product->getNoPackPrice()) ? true : false);
            $this->context->smarty->assign('packs', Pack::getPacksTable($this->product->id, $this->context->language->id, true, 1));

            $accessories = $this->product->getAccessories($this->context->language->id);
            if (is_array($accessories)) {
                foreach ($accessories as &$accessory) {
                    $accessory = $presenter->present(
                        $presentationSettings,
                        Product::getProductProperties($this->context->language->id, $accessory, $this->context),
                        $this->context->language
                    );
                }
                unset($accessory);
            }

            if ($this->product->customizable) {
                $customization_datas = $this->context->cart->getProductCustomization($this->product->id, null, true);
            }

            $product_for_template = $this->getTemplateVarProduct();

            $filteredProduct = Hook::exec(
                'filterProductContent',
                array('object' => $product_for_template),
                null,
                false,
                true,
                false,
                null,
                true
            );
            if (!empty($filteredProduct['object'])) {
                $product_for_template = $filteredProduct['object'];
            }

            $productManufacturer = new Manufacturer((int) $this->product->id_manufacturer, $this->context->language->id);

            $manufacturerImageUrl = $this->context->link->getManufacturerImageLink($productManufacturer->id);
            $undefinedImage = $this->context->link->getManufacturerImageLink(null);
            if ($manufacturerImageUrl === $undefinedImage) {
                $manufacturerImageUrl = null;
            }

            $productBrandUrl = $this->context->link->getManufacturerLink($productManufacturer->id);

            $this->context->smarty->assign(array(
                'priceDisplay' => $priceDisplay,
                'productPriceWithoutReduction' => $productPriceWithoutReduction,
                'customizationFields' => $customization_fields,
                'id_customization' => empty($customization_datas) ? null : $customization_datas[0]['id_customization'],
                'accessories' => $accessories,
                'product' => $product_for_template,
                'displayUnitPrice' => (!empty($this->product->unity) && $this->product->unit_price_ratio > 0.000000) ? true : false,
                'product_manufacturer' => $productManufacturer,
                'manufacturer_image_url' => $manufacturerImageUrl,
                'product_brand_url' => $productBrandUrl,
            ));

            // Assign attribute groups to the template
            $this->assignAttributesGroups($product_for_template);
        }

        parent::initContent();
    }

    public function displayAjaxQuickview()
    {
        $product_for_template = $this->getTemplateVarProduct();

        ob_end_clean();
        header('Content-Type: application/json');
        $this->ajaxRender(Tools::jsonEncode(array(
            'quickview_html' => $this->render('catalog/_partials/quickview', $product_for_template),
            'product' => $product_for_template,
        )));
    }

    public function displayAjaxRefresh()
    {
        $product = $this->getTemplateVarProduct();
        $minimalProductQuantity = $this->getMinimalProductOrDeclinationQuantity($product);
        $isPreview = ('1' === Tools::getValue('preview'));

        ob_end_clean();
        header('Content-Type: application/json');
        $this->ajaxRender(Tools::jsonEncode(array(
            'product_prices' => $this->render('catalog/_partials/product-prices'),
            'product_cover_thumbnails' => $this->render('catalog/_partials/product-cover-thumbnails'),
            'product_customization' => $this->render(
                'catalog/_partials/product-customization',
                array(
                    'customizations' => $product['customizations'],
                )
            ),
            'product_details' => $this->render('catalog/_partials/product-details'),
            'product_variants' => $this->render('catalog/_partials/product-variants'),
            'product_discounts' => $this->render('catalog/_partials/product-discounts'),
            'product_add_to_cart' => $this->render('catalog/_partials/product-add-to-cart'),
            'product_additional_info' => $this->render('catalog/_partials/product-additional-info'),
            'product_images_modal' => $this->render('catalog/_partials/product-images-modal'),
            'product_url' => $this->context->link->getProductLink(
                $product['id_product'],
                null,
                null,
                null,
                $this->context->language->id,
                null,
                $product['id_product_attribute'],
                false,
                false,
                true,
                $isPreview ? array('preview' => '1') : array()
            ),
            'product_minimal_quantity' => $minimalProductQuantity,
            'product_has_combinations' => !empty($this->combinations),
            'id_product_attribute' => $product['id_product_attribute'],
        )));
    }

    /**
     * Get minimal product quantity or minimal product combination quantity
     *
     * @param $product
     * @return int
     */
    protected function getMinimalProductOrDeclinationQuantity($product)
    {
        $productAttributeId = $product['id_product_attribute'];
        $minimalProductQuantity = 1;

        if ($this->combinations) {
            $minimalCombinationProductQuantity = (int) ($this->combinations[$productAttributeId]['minimal_quantity']);
            if ($minimalCombinationProductQuantity) { // Ensure the minimal product combination quantity is not 0;
                $minimalProductQuantity = $minimalCombinationProductQuantity;
            }
        } elseif (array_key_exists('minimal_quantity', $product)) {
            $minimalProductQuantity = $product['minimal_quantity'];
        }

        return $minimalProductQuantity;
    }

    /**
     * Assign price and tax to the template.
     */
    protected function assignPriceAndTax()
    {
        $id_customer = (isset($this->context->customer) ? (int) $this->context->customer->id : 0);
        $id_group = (int) Group::getCurrent()->id;
        $id_country = $id_customer ? (int) Customer::getCurrentCountry($id_customer) : (int) Tools::getCountry();

        // Tax
        $tax = (float) $this->product->getTaxesRate(new Address((int) $this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
        $this->context->smarty->assign('tax_rate', $tax);

        $product_price_with_tax = Product::getPriceStatic($this->product->id, true, null, 6);
        if (Product::$_taxCalculationMethod == PS_TAX_INC) {
            $product_price_with_tax = Tools::ps_round($product_price_with_tax, 2);
        }

        $id_currency = (int) $this->context->cookie->id_currency;
        $id_product = (int) $this->product->id;
        $id_product_attribute = Tools::getValue('id_product_attribute', null);
        $id_shop = $this->context->shop->id;

        $quantity_discounts = SpecificPrice::getQuantityDiscounts($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_product_attribute, false, (int) $this->context->customer->id);
        foreach ($quantity_discounts as &$quantity_discount) {
            if ($quantity_discount['id_product_attribute']) {
                $combination = new Combination((int) $quantity_discount['id_product_attribute']);
                $attributes = $combination->getAttributesName((int) $this->context->language->id);
                foreach ($attributes as $attribute) {
                    $quantity_discount['attributes'] = $attribute['name'].' - ';
                }
                $quantity_discount['attributes'] = rtrim($quantity_discount['attributes'], ' - ');
            }
            if ((int) $quantity_discount['id_currency'] == 0 && $quantity_discount['reduction_type'] == 'amount') {
                $quantity_discount['reduction'] = Tools::convertPriceFull($quantity_discount['reduction'], null, Context::getContext()->currency);
            }
        }

        $product_price = $this->product->getPrice(Product::$_taxCalculationMethod == PS_TAX_INC, false);
        $this->quantity_discounts = $this->formatQuantityDiscounts($quantity_discounts, $product_price, (float) $tax, $this->product->ecotax);

        $this->context->smarty->assign(array(
            'no_tax' => Tax::excludeTaxeOption() || !$tax,
            'tax_enabled' => Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC'),
            'customer_group_without_tax' => Group::getPriceDisplayMethod($this->context->customer->id_default_group),
        ));
    }

    /**
     * Assign template vars related to attribute groups and colors.
     */
    protected function assignAttributesGroups($product_for_template = null)
    {
        $colors = array();
        $groups = array();
        $this->combinations = array();

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
                    $colors[$row['id_attribute']]['attributes_quantity'] += (int) $row['quantity'];
                }
                if (!isset($groups[$row['id_attribute_group']])) {
                    $groups[$row['id_attribute_group']] = array(
                        'group_name' => $row['group_name'],
                        'name' => $row['public_group_name'],
                        'group_type' => $row['group_type'],
                        'default' => -1,
                    );
                }

                $groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = array(
                    'name' => $row['attribute_name'],
                    'html_color_code' => $row['attribute_color'],
                    'texture' => (@filemtime(_PS_COL_IMG_DIR_.$row['id_attribute'].'.jpg')) ? _THEME_COL_DIR_.$row['id_attribute'].'.jpg' : '',
                    'selected' => (isset($product_for_template['attributes'][$row['id_attribute_group']]['id_attribute']) && $product_for_template['attributes'][$row['id_attribute_group']]['id_attribute'] == $row['id_attribute']) ? true : false,
                );

                //$product.attributes.$id_attribute_group.id_attribute eq $id_attribute
                if ($row['default_on'] && $groups[$row['id_attribute_group']]['default'] == -1) {
                    $groups[$row['id_attribute_group']]['default'] = (int) $row['id_attribute'];
                }
                if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']])) {
                    $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
                }
                $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] += (int) $row['quantity'];

                $this->combinations[$row['id_product_attribute']]['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
                $this->combinations[$row['id_product_attribute']]['attributes'][] = (int) $row['id_attribute'];
                $this->combinations[$row['id_product_attribute']]['price'] = (float) $row['price'];

                // Call getPriceStatic in order to set $combination_specific_price
                if (!isset($combination_prices_set[(int) $row['id_product_attribute']])) {
                    $combination_specific_price = null;
                    Product::getPriceStatic((int) $this->product->id, false, $row['id_product_attribute'], 6, null, false, true, 1, false, null, null, null, $combination_specific_price);
                    $combination_prices_set[(int) $row['id_product_attribute']] = true;
                    $this->combinations[$row['id_product_attribute']]['specific_price'] = $combination_specific_price;
                }
                $this->combinations[$row['id_product_attribute']]['ecotax'] = (float) $row['ecotax'];
                $this->combinations[$row['id_product_attribute']]['weight'] = (float) $row['weight'];
                $this->combinations[$row['id_product_attribute']]['quantity'] = (int) $row['quantity'];
                $this->combinations[$row['id_product_attribute']]['reference'] = $row['reference'];
                $this->combinations[$row['id_product_attribute']]['unit_impact'] = $row['unit_price_impact'];
                $this->combinations[$row['id_product_attribute']]['minimal_quantity'] = $row['minimal_quantity'];
                if ($row['available_date'] != '0000-00-00' && Validate::isDate($row['available_date'])) {
                    $this->combinations[$row['id_product_attribute']]['available_date'] = $row['available_date'];
                    $this->combinations[$row['id_product_attribute']]['date_formatted'] = Tools::displayDate($row['available_date']);
                } else {
                    $this->combinations[$row['id_product_attribute']]['available_date'] = $this->combinations[$row['id_product_attribute']]['date_formatted'] = '';
                }

                if (!isset($combination_images[$row['id_product_attribute']][0]['id_image'])) {
                    $this->combinations[$row['id_product_attribute']]['id_image'] = -1;
                } else {
                    $this->combinations[$row['id_product_attribute']]['id_image'] = $id_image = (int) $combination_images[$row['id_product_attribute']][0]['id_image'];
                    if ($row['default_on']) {
                        foreach ($this->context->smarty->tpl_vars['product']->value['images'] as $image) {
                            if ($image['cover'] == 1) {
                                $current_cover = $image;
                            }
                        }
                        if (!isset($current_cover)) {
                            $current_cover = array_values($this->context->smarty->tpl_vars['product']->value['images'])[0];
                        }

                        if (is_array($combination_images[$row['id_product_attribute']])) {
                            foreach ($combination_images[$row['id_product_attribute']] as $tmp) {
                                if ($tmp['id_image'] == $current_cover['id_image']) {
                                    $this->combinations[$row['id_product_attribute']]['id_image'] = $id_image = (int) $tmp['id_image'];
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
                                $cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($this->product->id.'-'.$id_image) : (int) $id_image);
                                $cover['id_image_only'] = (int) $id_image;
                                $this->context->smarty->assign('cover', $cover);
                            }
                        }
                    }
                }
            }

            // wash attributes list depending on available attributes depending on selected preceding attributes
            $current_selected_attributes = array();
            $count = 0;
            foreach ($groups as &$group) {
                $count++;
                if ($count > 1) {
                    //find attributes of current group, having a possible combination with current selected
                    $id_product_attributes = array(0);
                    $query = 'SELECT pac.`id_product_attribute`
                        FROM `'._DB_PREFIX_.'product_attribute_combination` pac
                        INNER JOIN `'._DB_PREFIX_.'product_attribute` pa ON pa.id_product_attribute = pac.id_product_attribute
                        WHERE id_product = '.$this->product->id.' AND id_attribute IN ('.implode(',', array_map('intval', $current_selected_attributes)).')
                        GROUP BY id_product_attribute
                        HAVING COUNT(id_product) = '.count($current_selected_attributes);
                    if ($results = Db::getInstance()->executeS($query)) {
                        foreach ($results as $row) {
                            $id_product_attributes[] = $row['id_product_attribute'];
                        }
                    }
                    $id_attributes = Db::getInstance()->executeS('SELECT `id_attribute` FROM `'._DB_PREFIX_.'product_attribute_combination` pac2
                        WHERE `id_product_attribute` IN ('.implode(',', array_map('intval', $id_product_attributes)).')
                        AND id_attribute NOT IN ('.implode(',', array_map('intval', $current_selected_attributes)).')');
                    foreach ($id_attributes as $k => $row) {
                        $id_attributes[$k] = (int)$row['id_attribute'];
                    }
                    foreach ($group['attributes'] as $key => $attribute) {
                        if (!in_array((int)$key, $id_attributes)) {
                            unset($group['attributes'][$key]);
                            unset($group['attributes_quantity'][$key]);
                        }
                    }
                }
                //find selected attribute or first of group
                $index = 0;
                $current_selected_attribute = 0;
                foreach ($group['attributes'] as $key => $attribute) {
                    if ($index === 0) {
                        $current_selected_attribute = $key;
                    }
                    if ($attribute['selected']) {
                        $current_selected_attribute = $key;
                        break;
                    }
                }
                if ($current_selected_attribute > 0) {
                    $current_selected_attributes[] = $current_selected_attribute;
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
            foreach ($this->combinations as $id_product_attribute => $comb) {
                $attribute_list = '';
                foreach ($comb['attributes'] as $id_attribute) {
                    $attribute_list .= '\''.(int) $id_attribute.'\',';
                }
                $attribute_list = rtrim($attribute_list, ',');
                $this->combinations[$id_product_attribute]['list'] = $attribute_list;
            }

            $this->context->smarty->assign(array(
                'groups' => $groups,
                'colors' => (count($colors)) ? $colors : false,
                'combinations' => $this->combinations,
                'combinationImages' => $combination_images,
            ));
        } else {
            $this->context->smarty->assign(array(
                'groups' => array(),
                'colors' => false,
                'combinations' => array(),
                'combinationImages' => array(),
            ));
        }
    }

    /**
     * Get and assign attributes combinations informations.
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
            'attributesCombinations' => $attributes_combinations,
            'attribute_anchor_separator' => Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR'),
        ));
    }

    /**
     * Assign template vars related to category.
     */
    protected function assignCategory()
    {
        // Assign category to the template
        if (($this->category === false || !Validate::isLoadedObject($this->category) || !$this->category->inShop() || !$this->category->isAssociatedToShop()) && Category::inShopStatic($this->product->id_category_default, $this->context->shop)) {
            $this->category = new Category((int) $this->product->id_category_default, (int) $this->context->language->id);
        }

        $sub_categories = array();
        if (Validate::isLoadedObject($this->category)) {
            $sub_categories = $this->category->getSubCategories($this->context->language->id, true);

            // various assignements before Hook::exec
            $this->context->smarty->assign(array(
                'category' => $this->category,
                'subCategories' => $sub_categories,
                'id_category_current' => (int) $this->category->id,
                'id_category_parent' => (int) $this->category->id_parent,
                'return_category_name' => Tools::safeOutput($this->category->getFieldByLang('name')),
                'categories' => Category::getHomeCategories($this->context->language->id, true, (int) $this->context->shop->id),
            ));
        }
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
                $authorized_file_fields[(int) $field_id['id_customization_field']] = 'file'.(int) $field_id['id_customization_field'];
            }
        }
        $indexes = array_flip($authorized_file_fields);
        foreach ($_FILES as $field_name => $file) {
            if (in_array($field_name, $authorized_file_fields) && isset($file['tmp_name']) && !empty($file['tmp_name'])) {
                $file_name = md5(uniqid(rand(), true));
                if ($error = ImageManager::validateUpload($file, (int) Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE'))) {
                    $this->errors[] = $error;
                }

                $product_picture_width = (int) Configuration::get('PS_PRODUCT_PICTURE_WIDTH');
                $product_picture_height = (int) Configuration::get('PS_PRODUCT_PICTURE_HEIGHT');
                $tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                if ($error || (!$tmp_name || !move_uploaded_file($file['tmp_name'], $tmp_name))) {
                    return false;
                }
                /* Original file */
                if (!ImageManager::resize($tmp_name, _PS_UPLOAD_DIR_.$file_name)) {
                    $this->errors[] = $this->trans('An error occurred during the image upload process.', array(), 'Shop.Notifications.Error');
                } elseif (!ImageManager::resize($tmp_name, _PS_UPLOAD_DIR_.$file_name.'_small', $product_picture_width, $product_picture_height)) {
                    $this->errors[] = $this->trans('An error occurred during the image upload process.', array(), 'Shop.Notifications.Error');
                } elseif (!chmod(_PS_UPLOAD_DIR_.$file_name, 0777) || !chmod(_PS_UPLOAD_DIR_.$file_name.'_small', 0777)) {
                    $this->errors[] = $this->trans('An error occurred during the image upload process.', array(), 'Shop.Notifications.Error');
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
                $authorized_text_fields[(int) $field_id['id_customization_field']] = 'textField'.(int) $field_id['id_customization_field'];
            }
        }

        $indexes = array_flip($authorized_text_fields);
        foreach ($_POST as $field_name => $value) {
            if (in_array($field_name, $authorized_text_fields) && $value != '') {
                if (!Validate::isMessage($value)) {
                    $this->errors[] = $this->trans('Invalid message', array(), 'Shop.Notifications.Error');
                } else {
                    $this->context->cart->addTextFieldToProduct($this->product->id, $indexes[$field_name], Product::CUSTOMIZE_TEXTFIELD, $value);
                }
            } elseif (in_array($field_name, $authorized_text_fields) && $value == '') {
                $this->context->cart->deleteCustomizationToProduct((int) $this->product->id, $indexes[$field_name]);
            }
        }
    }

    /**
     * Calculation of currency-converted discounts for specific prices on product
     *
     * @param array $specific_prices array of specific prices definitions (DEFAULT currency)
     * @param float $price           current price in CURRENT currency
     * @param float $tax_rate        in percents
     * @param float $ecotax_amount   in DEFAULT currency, with tax
     *
     * @return array
     */
    protected function formatQuantityDiscounts($specific_prices, $price, $tax_rate, $ecotax_amount)
    {
        $priceFormatter = new PriceFormatter();

        foreach ($specific_prices as $key => &$row) {
            $row['quantity'] = &$row['from_quantity'];
            if ($row['price'] >= 0) {
                // The price may be directly set

                /** @var float $currentPriceDefaultCurrency current price with taxes in default currency */
                $currentPriceDefaultCurrency = (!$row['reduction_tax'] ? $row['price'] : $row['price'] * (1 + $tax_rate / 100)) + (float) $ecotax_amount;
                // Since this price is set in default currency,
                // we need to convert it into current currency
                $row['id_currency'];
                $currentPriceCurrentCurrency = Tools::convertPrice($currentPriceDefaultCurrency, $this->context->currency, true, $this->context);

                if ($row['reduction_type'] == 'amount') {
                    $currentPriceCurrentCurrency -= ($row['reduction_tax'] ? $row['reduction'] : $row['reduction'] / (1 + $tax_rate / 100));
                    $row['reduction_with_tax'] = $row['reduction_tax'] ? $row['reduction'] : $row['reduction'] / (1 + $tax_rate / 100);
                } else {
                    $currentPriceCurrentCurrency *= 1 - $row['reduction'];
                }
                $row['real_value'] = $price > 0 ? $price - $currentPriceCurrentCurrency : $currentPriceCurrentCurrency;
                $discountPrice = $price - $row['real_value'];

                if (Configuration::get('PS_DISPLAY_DISCOUNT_PRICE')) {
                    if ($row['reduction_tax'] == 0 && !$row['price']) {
                        $row['discount'] = $priceFormatter->format($price - ($price * $row['reduction_with_tax']));
                    } else {
                        $row['discount'] = $priceFormatter->format($price - $row['real_value']);
                    }
                } else {
                    $row['discount'] = $priceFormatter->format($row['real_value']);
                }
            } else {
                if ($row['reduction_type'] == 'amount') {
                    if (Product::$_taxCalculationMethod == PS_TAX_INC) {
                        $row['real_value'] = $row['reduction_tax'] == 1 ? $row['reduction'] : $row['reduction'] * (1 + $tax_rate / 100);
                    } else {
                        $row['real_value'] = $row['reduction_tax'] == 0 ? $row['reduction'] : $row['reduction'] / (1 + $tax_rate / 100);
                    }
                    $row['reduction_with_tax'] = $row['reduction_tax'] ? $row['reduction'] : $row['reduction'] +  ($row['reduction'] * $tax_rate) / 100;
                    $discountPrice = $price - $row['real_value'];
                    if (Configuration::get('PS_DISPLAY_DISCOUNT_PRICE')) {
                        if ($row['reduction_tax'] == 0 && !$row['price']) {
                            $row['discount'] = $priceFormatter->format($price - ($price * $row['reduction_with_tax']));
                        } else {
                            $row['discount'] = $priceFormatter->format($price - $row['real_value']);
                        }
                    } else {
                        $row['discount'] = $priceFormatter->format($row['real_value']);
                    }
                } else {
                    $row['real_value'] = $row['reduction'] * 100;
                    $discountPrice = $price - $price * $row['reduction'];
                    if (Configuration::get('PS_DISPLAY_DISCOUNT_PRICE')) {
                        if ($row['reduction_tax'] == 0) {
                            $row['discount'] = $priceFormatter->format($price - ($price * $row['reduction_with_tax']));
                        } else {
                            $row['discount'] = $priceFormatter->format($price - ($price * $row['reduction']));
                        }
                    } else {
                        $row['discount'] = $row['real_value'].'%';
                    }
                }
            }

            $row['save'] = $priceFormatter->format((($price * $row['quantity']) - ($discountPrice * $row['quantity'])));
            $row['nextQuantity'] = (isset($specific_prices[$key + 1]) ? (int) $specific_prices[$key + 1]['from_quantity'] : -1);
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

    /**
     * Return id_product_attribute by id_product_attribute request parameter
     * or by the group request parameter.
     *
     * @param bool $useGroups
     * @return int
     */
    private function getIdProductAttribute($useGroups = false)
    {
        $requestedIdProductAttribute = (int) Tools::getValue('id_product_attribute');

        if ($useGroups === true) {
            $groups = Tools::getValue('group');

            if (!empty($groups)) {
                $requestedIdProductAttribute = (int) Product::getIdProductAttributesByIdAttributes(
                    $this->product->id,
                    $groups
                );
            }
        }

        if (!Configuration::get('PS_DISP_UNAVAILABLE_ATTR')) {
            $productAttributes = array_filter(
                $this->product->getAttributeCombinations(),
                function ($elem) {
                    return $elem['quantity'] > 0;
                }
            );
            $productAttribute = array_filter(
                $productAttributes,
                function ($elem) use ($requestedIdProductAttribute) {
                    return $elem['id_product_attribute'] == $requestedIdProductAttribute;
                }
            );

            if (empty($productAttribute) && !empty($productAttributes)) {
                return (int)array_shift($productAttributes)['id_product_attribute'];
            }
        }

        return $requestedIdProductAttribute;
    }

    public function getTemplateVarProduct()
    {
        $productSettings = $this->getProductPresentationSettings();
        // Hook displayProductExtraContent
        $extraContentFinder = new ProductExtraContentFinder();

        $product = $this->objectPresenter->present($this->product);
        $product['id_product'] = (int) $this->product->id;
        $product['out_of_stock'] = (int) $this->product->out_of_stock;
        $product['new'] = (int) $this->product->new;
        $product['id_product_attribute'] = $this->getIdProductAttribute(true);
        $product['minimal_quantity'] = $this->getProductMinimalQuantity($product);
        $product['quantity_wanted'] = $this->getRequiredQuantity($product);
        $product['extraContent'] = $extraContentFinder->addParams(array('product' => $this->product))->present();
        $product['ecotax'] = Tools::convertPrice((float) $product['ecotax'], $this->context->currency, true, $this->context);

        $product_full = Product::getProductProperties($this->context->language->id, $product, $this->context);

        $product_full = $this->addProductCustomizationData($product_full);

        $product_full['show_quantities'] = (bool) (
            Configuration::get('PS_DISPLAY_QTIES')
            && Configuration::get('PS_STOCK_MANAGEMENT')
            && $this->product->quantity > 0
            && $this->product->available_for_order
            && !Configuration::isCatalogMode()
        );
        $product_full['quantity_label'] = ($this->product->quantity > 1) ? $this->trans('Items', array(), 'Shop.Theme.Catalog') : $this->trans('Item', array(), 'Shop.Theme.Catalog');
        $product_full['quantity_discounts'] = $this->quantity_discounts;

        if ($product_full['unit_price_ratio'] > 0) {
            $unitPrice = ($productSettings->include_taxes) ? $product_full['price'] : $product_full['price_tax_exc'];
            $product_full['unit_price'] = $unitPrice / $product_full['unit_price_ratio'];
        }

        $group_reduction = GroupReduction::getValueForProduct($this->product->id, (int) Group::getCurrent()->id);
        if ($group_reduction === false) {
            $group_reduction = Group::getReduction((int) $this->context->cookie->id_customer) / 100;
        }
        $product_full['customer_group_discount'] = $group_reduction;

        $presenter = $this->getProductPresenter();

        return $presenter->present(
            $productSettings,
            $product_full,
            $this->context->language
        );
    }

    /**
     * @param $product
     * @return int
     */
    protected function getProductMinimalQuantity($product)
    {
        $minimal_quantity = 1;

        if ($product['id_product_attribute']) {
            $combination = $this->findProductCombinationById($product['id_product_attribute']);
            if ($combination['minimal_quantity']) {
                $minimal_quantity = $combination['minimal_quantity'];
            }
        } else {
            $minimal_quantity = $this->product->minimal_quantity;
        }

        return $minimal_quantity;
    }

    /**
     * @param $combinationId
     * @return null|ProductController
     */
    public function findProductCombinationById($combinationId)
    {
        $foundCombination = null;
        $combinations = $this->product->getAttributesGroups($this->context->language->id);
        foreach ($combinations as $combination) {
            if ((int) ($combination['id_product_attribute']) === $combinationId) {
                $foundCombination = $combination;

                break;
            }
        }

        return $foundCombination;
    }

    /**
     * @param $product
     * @return int
     */
    protected function getRequiredQuantity($product)
    {
        $requiredQuantity = (int) Tools::getValue('quantity_wanted', $this->getProductMinimalQuantity($product));
        if ($requiredQuantity < $product['minimal_quantity']) {
            $requiredQuantity = $product['minimal_quantity'];
        }

        return $requiredQuantity;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $categoryDefault = new Category($this->product->id_category_default, $this->context->language->id);

        foreach ($categoryDefault->getAllParents() as $category) {
            if ($category->id_parent != 0 && !$category->is_root_category) {
                $breadcrumb['links'][] = $this->getCategoryPath($category);
            }
        }

        if (!$categoryDefault->is_root_category) {
            $breadcrumb['links'][] = $this->getCategoryPath($categoryDefault);
        }

        $breadcrumb['links'][] = array(
            'title' => $this->product->name,
            'url' => $this->context->link->getProductLink($this->product, null, null, null, null, null, (int) $this->getIdProductAttribute()),
        );

        return $breadcrumb;
    }

    protected function addProductCustomizationData(array $product_full)
    {
        if ($product_full['customizable']) {
            $customizationData = array(
                'fields' => array(),
            );

            $customized_data = array();

            $already_customized = $this->context->cart->getProductCustomization(
                $product_full['id_product'],
                null,
                true
            );

            $id_customization = 0;
            foreach ($already_customized as $customization) {
                $id_customization = $customization['id_customization'];
                $customized_data[$customization['index']] = $customization;
            }

            $customization_fields = $this->product->getCustomizationFields($this->context->language->id);
            if (is_array($customization_fields)) {
                foreach ($customization_fields as $customization_field) {
                    // 'id_customization_field' maps to what is called 'index'
                    // in what Product::getProductCustomization() returns
                    $key = $customization_field['id_customization_field'];

                    $field['label'] = $customization_field['name'];
                    $field['id_customization_field'] = $customization_field['id_customization_field'];
                    $field['required'] = $customization_field['required'];

                    switch ($customization_field['type']) {
                        case Product::CUSTOMIZE_FILE:
                            $field['type'] = 'image';
                            $field['image'] = null;
                            $field['input_name'] = 'file'.$customization_field['id_customization_field'];
                            break;
                        case Product::CUSTOMIZE_TEXTFIELD:
                            $field['type'] = 'text';
                            $field['text'] = '';
                            $field['input_name'] = 'textField'.$customization_field['id_customization_field'];
                            break;
                        default:
                            $field['type'] = null;
                    }

                    if (array_key_exists($key, $customized_data)) {
                        $data = $customized_data[$key];
                        $field['is_customized'] = true;
                        switch ($customization_field['type']) {
                            case Product::CUSTOMIZE_FILE:
                                $imageRetriever = new ImageRetriever($this->context->link);
                                $field['image'] = $imageRetriever->getCustomizationImage(
                                    $data['value']
                                );
                                $field['remove_image_url'] = $this->context->link->getProductDeletePictureLink(
                                    $product_full,
                                    $customization_field['id_customization_field']
                                );
                                break;
                            case Product::CUSTOMIZE_TEXTFIELD:
                                $field['text'] = $data['value'];
                                break;
                        }
                    } else {
                        $field['is_customized'] = false;
                    }

                    $customizationData['fields'][] = $field;
                }
            }
            $product_full['customizations'] = $customizationData;
            $product_full['id_customization'] = $id_customization;
            $product_full['is_customizable'] = true;
        } else {
            $product_full['customizations'] = array(
                'fields' => array(),
            );
            $product_full['id_customization'] = 0;
            $product_full['is_customizable'] = false;
        }

        return $product_full;
    }

    /**
     *
     * @return array
     */
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        $page['body_classes']['product-id-'.$this->product->id] = true;
        $page['body_classes']['product-'.$this->product->name] = true;
        $page['body_classes']['product-id-category-'.$this->product->id_category_default] = true;
        $page['body_classes']['product-id-manufacturer-'.$this->product->id_manufacturer] = true;
        $page['body_classes']['product-id-supplier-'.$this->product->id_supplier] = true;

        if ($this->product->on_sale) {
            $page['body_classes']['product-on-sale'] = true;
        }

        if ($this->product->available_for_order) {
            $page['body_classes']['product-available-for-order'] = true;
        }

        if ($this->product->customizable) {
            $page['body_classes']['product-customizable'] = true;
        }
        $page['admin_notifications'] = array_merge($page['admin_notifications'], $this->adminNotifications);

        return $page;
    }

    /**
     * @inheritdoc
     *
     * Indicates if the provided combination exists and belongs to the product
     *
     * @param int $productAttributeId
     * @param int $productId
     *
     * @return bool
     */
    protected function isValidCombination($productAttributeId, $productId)
    {
        if ($productAttributeId > 0 && $productId > 0) {
            $combination = new Combination($productAttributeId);

            return (
                Validate::isLoadedObject($combination)
                && $combination->id_product == $productId
            );
        }

        return false;
    }
}
