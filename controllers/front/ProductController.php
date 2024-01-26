<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\Manufacturer\ManufacturerPresenter;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use PrestaShop\PrestaShop\Core\Product\ProductExtraContentFinder;
use PrestaShopBundle\Security\Admin\LegacyAdminTokenValidator;

class ProductControllerCore extends ProductPresentingFrontControllerCore
{
    /** @var string */
    public $php_self = 'product';

    /** @var int */
    protected $id_product;

    /** @var int|null */
    protected $id_product_attribute;

    /** @var Product */
    protected $product;

    /** @var Category|null */
    protected $category;

    protected $redirectionExtraExcludedKeys = ['id_product_attribute', 'rewrite'];

    /**
     * @var array
     */
    protected $combinations;

    protected $quantity_discounts;

    /**
     * @var array
     */
    protected $adminNotifications = [];

    /**
     * @var bool
     */
    protected $isQuickView = false;

    /**
     * @var bool
     */
    protected $isPreview = false;

    public function canonicalRedirection(string $canonical_url = '')
    {
        // This is there to prevent error, because this function is also called
        // in parent front controller before we have even loaded our data.
        if (!Validate::isLoadedObject($this->product)) {
            return;
        }

        // We check if the combination is valid, if not, we reset it redirect to pure product URL without combination.
        if (!$this->product->hasCombinations() || !$this->isValidCombination($this->id_product_attribute, $this->product->id)) {
            unset($_GET['id_product_attribute']);
            $this->id_product_attribute = null;
        }

        // If the attribute id is present in the url we use it to perform the redirection, this will fix any domain
        // or rewriting error and redirect to the appropriate url.
        parent::canonicalRedirection($this->context->link->getProductLink(
            $this->product,
            null,
            null,
            null,
            null,
            null,
            $this->id_product_attribute
        ));
    }

    /**
     * Returns canonical URL for the current product
     *
     * @return string
     */
    public function getCanonicalURL(): string
    {
        $product = $this->context->smarty->getTemplateVars('product');

        if (!($product instanceof ProductLazyArray)) {
            return '';
        }

        return $product->getCanonicalUrl();
    }

    /**
     * Initialize product controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        // Get proper IDs
        $this->id_product = (int) Tools::getValue('id_product');
        $this->id_product_attribute = (int) Tools::getValue('id_product_attribute', null);

        // Load viewing modes
        if ('1' === Tools::getValue('quickview')) {
            $this->setQuickViewMode();
        }

        // We are in a preview mode only if proper admin token was also provided in the URL
        if ('1' === Tools::getValue('preview')) {
            $adminTokenValidator = $this->getContainer()->get(LegacyAdminTokenValidator::class);
            $isAdminTokenValid = $adminTokenValidator->isTokenValid('AdminProducts', (int) Tools::getValue('id_employee'), Tools::getValue('adtoken'));
            if ($isAdminTokenValid) {
                $this->setPreviewMode();
            }
        }

        // Try to load product object, otherwise immediately redirect to 404
        if ($this->id_product) {
            $this->product = new Product($this->id_product, true, $this->context->language->id, $this->context->shop->id);
        }
        if (!Validate::isLoadedObject($this->product)) {
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
            $this->errors[] = $this->trans('This product is no longer available.', [], 'Shop.Notifications.Error');
            $this->setTemplate('errors/404');

            return;
        }

        // Check if the user is on correct URL and redirect if needed
        $this->canonicalRedirection();

        // Set proper template to product
        $this->setTemplate('catalog/product', [
            'entity' => 'product',
            'id' => $this->id_product,
        ]);

        // Performs multiple checks and redirects user to error page if needed
        $this->checkPermissionsToViewProduct();

        // Load product category
        $this->initializeCategory();
    }

    /**
     * Performs multiple checks and redirects user to error page if needed
     */
    public function checkPermissionsToViewProduct()
    {
        // If the person accessing the product page is admin with proper token, we only do limited checks
        if ($this->isPreview()) {
            if (!$this->product->isAssociatedToShop() || !$this->product->active) {
                $this->adminNotifications['inactive_product'] = [
                    'type' => 'warning',
                    'message' => $this->trans('This product is not visible to your customers.', [], 'Shop.Notifications.Warning'),
                ];
            }

            return;
        }

        // Now the checks for public
        // If product is disabled or doesn't belong to this shop, we respect the redirection settings
        if (!$this->product->isAssociatedToShop() || !$this->product->active) {
            if (!$this->product->id_type_redirected) {
                if (in_array($this->product->redirect_type, [RedirectType::TYPE_CATEGORY_PERMANENT, RedirectType::TYPE_CATEGORY_TEMPORARY])) {
                    $this->product->id_type_redirected = $this->product->id_category_default;
                }
            } elseif (in_array($this->product->redirect_type, [RedirectType::TYPE_PRODUCT_PERMANENT, RedirectType::TYPE_PRODUCT_TEMPORARY]) && $this->product->id_type_redirected == $this->product->id) {
                $this->product->redirect_type = RedirectType::TYPE_NOT_FOUND;
            }

            $redirect_type = $this->product->redirect_type;
            // If product has no specified redirect settings, we get default from configuration
            if (empty($redirect_type) || $redirect_type == RedirectType::TYPE_DEFAULT) {
                $redirect_type = Configuration::get('PS_PRODUCT_REDIRECTION_DEFAULT');
            }

            switch ($redirect_type) {
                case RedirectType::TYPE_PRODUCT_PERMANENT:
                    header('HTTP/1.1 301 Moved Permanently');
                    header('Location: ' . $this->context->link->getProductLink($this->product->id_type_redirected));
                    exit;
                case RedirectType::TYPE_PRODUCT_TEMPORARY:
                    header('HTTP/1.1 302 Moved Temporarily');
                    header('Cache-Control: no-cache');
                    header('Location: ' . $this->context->link->getProductLink($this->product->id_type_redirected));
                    exit;
                case RedirectType::TYPE_CATEGORY_PERMANENT:
                    header('HTTP/1.1 301 Moved Permanently');
                    header('Location: ' . $this->context->link->getCategoryLink($this->product->id_type_redirected));
                    exit;
                case RedirectType::TYPE_CATEGORY_TEMPORARY:
                    header('HTTP/1.1 302 Moved Temporarily');
                    header('Cache-Control: no-cache');
                    header('Location: ' . $this->context->link->getCategoryLink($this->product->id_type_redirected));
                    exit;
                case RedirectType::TYPE_SUCCESS_DISPLAYED:
                    break;
                case RedirectType::TYPE_NOT_FOUND_DISPLAYED:
                    // We want to send this response only on initial page load
                    // Sending it for ajax requests can cause problems in scripts relying on 200 response
                    if (!$this->ajax) {
                        header('HTTP/1.1 404 Not Found');
                        header('Status: 404 Not Found');
                    }

                    break;
                case RedirectType::TYPE_GONE_DISPLAYED:
                    // We want to send this response only on initial page load
                    // Sending it for ajax requests can cause problems in scripts relying on 200 response
                    if (!$this->ajax) {
                        header('HTTP/1.1 410 Gone');
                        header('Status: 410 Gone');
                    }

                    break;
                case RedirectType::TYPE_GONE:
                    header('HTTP/1.1 410 Gone');
                    header('Status: 410 Gone');
                    $this->errors[] = $this->trans('This product is no longer available.', [], 'Shop.Notifications.Error');
                    $this->setTemplate('errors/410');

                    break;
                case RedirectType::TYPE_NOT_FOUND:
                default:
                    header('HTTP/1.1 404 Not Found');
                    header('Status: 404 Not Found');
                    $this->errors[] = $this->trans('This product is no longer available.', [], 'Shop.Notifications.Error');
                    $this->setTemplate('errors/404');

                    break;
            }
        }

        // Check if customer is allowed to access this product
        if (!$this->product->checkAccess(isset($this->context->customer->id) && $this->context->customer->id ? (int) $this->context->customer->id : 0)) {
            header('HTTP/1.1 403 Forbidden');
            header('Status: 403 Forbidden');
            $this->errors[] = $this->trans('You do not have access to this product.', [], 'Shop.Notifications.Error');
            $this->setTemplate('errors/forbidden');
        }
    }

    /**
     * Loads related category to current visit. First it tries to get a category the user came from - it uses HTTP referer for this.
     * If no category is found (or it's invalid), we use product's default category.
     */
    public function initializeCategory()
    {
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
                $referers = [$_SERVER['HTTP_REFERER'], urldecode($_SERVER['HTTP_REFERER'])];
                if (in_array($this->context->link->getCategoryLink($id_object), $referers)) {
                    $id_category = (int) $id_object;
                } elseif (isset($this->context->cookie->last_visited_category) && (int) $this->context->cookie->last_visited_category && in_array($this->context->link->getProductLink($id_object), $referers)) {
                    $id_category = (int) $this->context->cookie->last_visited_category;
                }
            }
        }
        if (!$id_category || !Category::inShopStatic($id_category, $this->context->shop) || !Product::idIsOnCategoryId((int) $this->product->id, ['0' => ['id_category' => $id_category]])) {
            $id_category = (int) $this->product->id_category_default;
        }

        // Load category and store it in cookie
        $this->category = new Category((int) $id_category, (int) $this->context->cookie->id_lang);
        $this->context->cookie->last_visited_category = (int) $this->category->id_category;
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

            $pictures = [];
            $text_fields = [];
            if ($this->product->customizable) {
                $files = $this->context->cart->getProductCustomization($this->product->id, Product::CUSTOMIZE_FILE, true);
                foreach ($files as $file) {
                    $pictures['pictures_' . $this->product->id . '_' . $file['index']] = $file['value'];
                }

                $texts = $this->context->cart->getProductCustomization($this->product->id, Product::CUSTOMIZE_TEXTFIELD, true);

                foreach ($texts as $text_field) {
                    $text_fields['textFields_' . $this->product->id . '_' . $text_field['index']] = str_replace('<br />', "\n", $text_field['value']);
                }
            }

            $this->context->smarty->assign([
                'pictures' => $pictures,
                'textFields' => $text_fields, ]);

            $this->product->customization_required = false;
            $customization_fields = $this->product->customizable ? $this->product->getCustomizationFields($this->context->language->id) : false;
            if (is_array($customization_fields)) {
                foreach ($customization_fields as &$customization_field) {
                    if ($customization_field['type'] == Product::CUSTOMIZE_FILE) {
                        $customization_field['key'] = 'pictures_' . $this->product->id . '_' . $customization_field['id_customization_field'];
                    } elseif ($customization_field['type'] == Product::CUSTOMIZE_TEXTFIELD) {
                        $customization_field['key'] = 'textFields_' . $this->product->id . '_' . $customization_field['id_customization_field'];
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

            // Add notification about this product being in cart
            $this->addCartQuantityNotification();

            // Pack management
            $pack_items = Pack::isPack($this->product->id) ? Pack::getItemTable($this->product->id, $this->context->language->id, true) : [];

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

            $presentedPackItems = [];
            foreach ($pack_items as $item) {
                $presentedPackItems[] = $presenter->present(
                    $this->getProductPresentationSettings(),
                    $assembler->assembleProduct($item),
                    $this->context->language
                );
            }

            $this->context->smarty->assign('packItems', $presentedPackItems);
            $this->context->smarty->assign('noPackPrice', $this->product->getNoPackPrice());
            $this->context->smarty->assign('displayPackPrice', ($pack_items && $productPrice < Pack::noPackPrice((int) $this->product->id)));
            $this->context->smarty->assign('priceDisplay', $priceDisplay);
            $this->context->smarty->assign('packs', Pack::getPacksTable($this->product->id, $this->context->language->id, true, 1));

            $accessories = $this->product->getAccessories($this->context->language->id);
            if (is_array($accessories)) {
                foreach ($accessories as &$accessory) {
                    $accessory = $presenter->present(
                        $presentationSettings,
                        $assembler->assembleProduct($accessory),
                        $this->context->language
                    );
                }
                unset($accessory);
            }

            if ($this->product->customizable) {
                $customization_datas = $this->context->cart->getProductCustomization($this->product->id, null, true);
            }

            $product_for_template = $this->getTemplateVarProduct();

            // Chained hook call - if multiple modules are hooked here, they will receive the result of the previous one as a parameter
            $filteredProduct = Hook::exec(
                'filterProductContent',
                ['object' => $product_for_template],
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

            // Prepare information about product manufacturer
            $productManufacturer = null;
            $manufacturerImageUrl = null;
            $productBrandUrl = null;

            if (!empty($this->product->id_manufacturer)) {
                $manufacturerPresenter = new ManufacturerPresenter($this->context->link);
                $productManufacturer = $manufacturerPresenter->present(
                    new Manufacturer((int) $this->product->id_manufacturer, $this->context->language->id),
                    $this->context->language
                );

                // These two variables are deprecated are kept just for backward compatibility and will be removed in v10
                $manufacturerImageUrl = $productManufacturer['image']['small']['url'];
                $productBrandUrl = $productManufacturer['url'];
            }

            $this->context->smarty->assign([
                'priceDisplay' => $priceDisplay,
                'productPriceWithoutReduction' => $productPriceWithoutReduction,
                'customizationFields' => $customization_fields,
                'id_customization' => empty($customization_datas) ? null : $customization_datas[0]['id_customization'],
                'accessories' => $accessories,
                'product' => $product_for_template,
                'displayUnitPrice' => !empty($product_for_template['unit_price_tax_excluded']),
                'product_manufacturer' => $productManufacturer,
                'manufacturer_image_url' => $manufacturerImageUrl,
                'product_brand_url' => $productBrandUrl,
            ]);

            // Assign attribute groups to the template
            $this->assignAttributesGroups($product_for_template);
        }

        parent::initContent();
    }

    /**
     * Processes submitted customizations
     *
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
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
            $this->errors[] = $this->trans('An error occurred while deleting the selected picture.', [], 'Shop.Notifications.Error');
        }
    }

    public function displayAjaxQuickview()
    {
        $productForTemplate = $this->getTemplateVarProduct();
        ob_end_clean();
        header('Content-Type: application/json');

        $this->setQuickViewMode();

        $this->ajaxRender(json_encode([
            'quickview_html' => $this->render(
                'catalog/_partials/quickview',
                $productForTemplate instanceof AbstractLazyArray ?
                $productForTemplate->jsonSerialize() :
                $productForTemplate
            ),
            'product' => $productForTemplate,
        ]));
    }

    public function displayAjaxRefresh()
    {
        $product = $this->getTemplateVarProduct();
        $minimalProductQuantity = $this->getProductMinimalQuantity($product);

        ob_end_clean();
        header('Content-Type: application/json');
        $this->ajaxRender(json_encode([
            'product_prices' => $this->render('catalog/_partials/product-prices'),
            'product_cover_thumbnails' => $this->render('catalog/_partials/product-cover-thumbnails'),
            'product_customization' => $this->render(
                'catalog/_partials/product-customization',
                [
                    'customizations' => $product['customizations'],
                ]
            ),
            'product_details' => $this->render('catalog/_partials/product-details'),
            'product_variants' => $this->render('catalog/_partials/product-variants'),
            'product_discounts' => $this->render('catalog/_partials/product-discounts'),
            'product_add_to_cart' => $this->render('catalog/_partials/product-add-to-cart'),
            'product_additional_info' => $this->render('catalog/_partials/product-additional-info'),
            'product_images_modal' => $this->render('catalog/_partials/product-images-modal'),
            'product_flags' => $this->render('catalog/_partials/product-flags'),
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
                $this->isPreview() ? ['preview' => '1'] : []
            ),
            'product_minimal_quantity' => $minimalProductQuantity,
            'product_has_combinations' => !empty($this->combinations),
            'id_product_attribute' => $product['id_product_attribute'],
            'id_customization' => $product['id_customization'],
            'product_title' => $this->getTemplateVarPage()['meta']['title'],
            'is_quick_view' => $this->isQuickView(),
        ]));
    }

    /**
     * Displays information, if the customer has this product in cart already.
     */
    protected function addCartQuantityNotification()
    {
        if ((bool) Configuration::get('PS_DISPLAY_AMOUNT_IN_CART') !== true) {
            return;
        }

        // Get quantity of this product in cart, it will return an array with
        // quantity of this single product and also quantity in packs
        $quantities = $this->context->cart->getProductQuantityInAllVariants(
            $this->id_product
        );

        // Render nice notifications so the user knows what is happening
        if ($quantities['standalone_quantity'] > 0 && $quantities['pack_quantity'] > 0) {
            $this->info[] = $this->trans(
                'Your cart contains %1s of these products and another %2s of these are included in packs in your cart.',
                [$quantities['standalone_quantity'], $quantities['pack_quantity']],
                'Shop.Theme.Catalog'
            );
        } elseif ($quantities['standalone_quantity'] > 0) {
            $this->info[] = $this->trans(
                'Your cart contains %1s of these products.',
                [$quantities['standalone_quantity']],
                'Shop.Theme.Catalog'
            );
        } elseif ($quantities['pack_quantity'] > 0) {
            $this->info[] = $this->trans(
                '%1s of these products are included in packs in your cart.',
                [$quantities['pack_quantity']],
                'Shop.Theme.Catalog'
            );
        }
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
        $id_product_attribute = $this->getIdProductAttributeByGroupOrRequestOrDefault();
        $id_shop = $this->context->shop->id;

        $quantity_discounts = SpecificPrice::getQuantityDiscounts($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_product_attribute, false, (int) $this->context->customer->id);
        foreach ($quantity_discounts as &$quantity_discount) {
            if ($quantity_discount['id_product_attribute']) {
                $combination = new Combination((int) $quantity_discount['id_product_attribute']);
                $attributes = $combination->getAttributesName((int) $this->context->language->id);
                foreach ($attributes as $attribute) {
                    $quantity_discount['attributes'] = $attribute['name'] . ' - ';
                }
                $quantity_discount['attributes'] = rtrim($quantity_discount['attributes'], ' - ');
            }
            if ((int) $quantity_discount['id_currency'] == 0 && $quantity_discount['reduction_type'] == 'amount') {
                $quantity_discount['reduction'] = Tools::convertPriceFull($quantity_discount['reduction'], null, Context::getContext()->currency);
            }
        }
        unset($quantity_discount);

        $product_price = $this->product->getPrice(Product::$_taxCalculationMethod == PS_TAX_INC, $id_product_attribute, 6, null, false, false);

        $this->quantity_discounts = $this->formatQuantityDiscounts($quantity_discounts, $product_price, (float) $tax, $this->product->ecotax);

        $this->context->smarty->assign([
            'no_tax' => !Configuration::get('PS_TAX') || !$tax,
            'tax_enabled' => Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC'),
            'customer_group_without_tax' => Group::getPriceDisplayMethod($this->context->customer->id_default_group),
        ]);
    }

    /**
     * Assign template vars related to attribute groups and colors.
     */
    protected function assignAttributesGroups(ProductLazyArray|null $product_for_template = null)
    {
        $colors = [];
        $groups = [];
        $this->combinations = [];

        /** @todo (RM) should only get groups and not all declination ? */
        $attributes_groups = $this->product->getAttributesGroups($this->context->language->id);
        if (is_array($attributes_groups) && $attributes_groups) {
            $combination_images = $this->product->getCombinationImages($this->context->language->id);
            $combination_prices_set = [];
            foreach ($attributes_groups as $k => $row) {
                // Color management
                if (isset($row['is_color_group']) && $row['is_color_group'] && (isset($row['attribute_color']) && $row['attribute_color']) || (file_exists(_PS_COL_IMG_DIR_ . $row['id_attribute'] . '.jpg'))) {
                    $colors[$row['id_attribute']]['value'] = $row['attribute_color'];
                    $colors[$row['id_attribute']]['name'] = $row['attribute_name'];
                    if (!isset($colors[$row['id_attribute']]['attributes_quantity'])) {
                        $colors[$row['id_attribute']]['attributes_quantity'] = 0;
                    }
                    $colors[$row['id_attribute']]['attributes_quantity'] += max((int) $row['quantity'], 0);
                }
                if (!isset($groups[$row['id_attribute_group']])) {
                    $groups[$row['id_attribute_group']] = [
                        'group_name' => $row['group_name'],
                        'name' => $row['public_group_name'],
                        'group_type' => $row['group_type'],
                        'default' => -1,
                    ];
                }

                $groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = [
                    'name' => $row['attribute_name'],
                    'html_color_code' => $row['attribute_color'],
                    'texture' => (@filemtime(_PS_COL_IMG_DIR_ . $row['id_attribute'] . '.jpg')) ? _THEME_COL_DIR_ . $row['id_attribute'] . '.jpg' : '',
                    'selected' => (isset($product_for_template['attributes'][$row['id_attribute_group']]['id_attribute']) && $product_for_template['attributes'][$row['id_attribute_group']]['id_attribute'] == $row['id_attribute']) ? true : false,
                ];

                //$product.attributes.$id_attribute_group.id_attribute eq $id_attribute
                if ($row['default_on'] && $groups[$row['id_attribute_group']]['default'] == -1) {
                    $groups[$row['id_attribute_group']]['default'] = (int) $row['id_attribute'];
                }
                if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']])) {
                    $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
                }
                $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] += max((int) $row['quantity'], 0);

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
                $this->combinations[$row['id_product_attribute']]['ean13'] = $row['ean13'];
                $this->combinations[$row['id_product_attribute']]['mpn'] = $row['mpn'];
                $this->combinations[$row['id_product_attribute']]['upc'] = $row['upc'];
                $this->combinations[$row['id_product_attribute']]['isbn'] = $row['isbn'];
                $this->combinations[$row['id_product_attribute']]['unit_impact'] = $row['unit_price_impact'];
                $this->combinations[$row['id_product_attribute']]['minimal_quantity'] = $row['minimal_quantity'];
                if (!empty($row['available_date']) && $row['available_date'] != '0000-00-00' && Validate::isDate($row['available_date'])) {
                    $this->combinations[$row['id_product_attribute']]['available_date'] = $row['available_date'];
                    $this->combinations[$row['id_product_attribute']]['date_formatted'] = Tools::displayDate($row['available_date']);
                } else {
                    $this->combinations[$row['id_product_attribute']]['available_date'] = $this->combinations[$row['id_product_attribute']]['date_formatted'] = '';
                }
                $this->combinations[$row['id_product_attribute']]['available_now'] = $row['available_now'];
                $this->combinations[$row['id_product_attribute']]['available_later'] = $row['available_later'];

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
                                $this->context->smarty->assign('images', $product_images);
                            }

                            $cover = $current_cover;

                            if (isset($cover) && is_array($cover) && isset($product_images) && is_array($product_images)) {
                                $product_images[$cover['id_image']]['cover'] = 0;
                                if (isset($product_images[$id_image])) {
                                    $cover = $product_images[$id_image];
                                }
                                $cover['id_image'] = (int) $id_image;
                                $cover['id_image_only'] = (int) $id_image;
                                $this->context->smarty->assign('cover', $cover);
                            }
                        }
                    }
                }
            }

            // wash attributes list depending on available attributes depending on selected preceding attributes
            $current_selected_attributes = [];
            $count = 0;
            foreach ($groups as &$group) {
                ++$count;
                if ($count > 1) {
                    //find attributes of current group, having a possible combination with current selected
                    $id_product_attributes = [0];
                    $query = 'SELECT pac.`id_product_attribute`
                        FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                        INNER JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.id_product_attribute = pac.id_product_attribute
                        WHERE id_product = ' . $this->product->id . ' AND id_attribute IN (' . implode(',', array_map('intval', $current_selected_attributes)) . ')
                        GROUP BY id_product_attribute
                        HAVING COUNT(id_product) = ' . count($current_selected_attributes);
                    if ($results = Db::getInstance()->executeS($query)) {
                        foreach ($results as $row) {
                            $id_product_attributes[] = $row['id_product_attribute'];
                        }
                    }
                    $id_attributes = Db::getInstance()->executeS('SELECT pac2.`id_attribute` FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac2' .
                        ((!Product::isAvailableWhenOutOfStock($this->product->out_of_stock) && 0 == Configuration::get('PS_DISP_UNAVAILABLE_ATTR')) ?
                        ' INNER JOIN `' . _DB_PREFIX_ . 'stock_available` pa ON pa.id_product_attribute = pac2.id_product_attribute
                        WHERE pa.quantity > 0 AND ' :
                        ' WHERE ') .
                        'pac2.`id_product_attribute` IN (' . implode(',', array_map('intval', $id_product_attributes)) . ')
                        AND pac2.id_attribute NOT IN (' . implode(',', array_map('intval', $current_selected_attributes)) . ')');
                    foreach ($id_attributes as $k => $row) {
                        $id_attributes[$k] = (int) $row['id_attribute'];
                    }
                    foreach ($group['attributes'] as $key => $attribute) {
                        if (!in_array((int) $key, $id_attributes)) {
                            unset(
                                $group['attributes'][$key],
                                $group['attributes_quantity'][$key]
                            );
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
                    foreach ($group['attributes_quantity'] as $key => $quantity) {
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
                    $attribute_list .= '\'' . (int) $id_attribute . '\',';
                }
                $attribute_list = rtrim($attribute_list, ',');
                $this->combinations[$id_product_attribute]['list'] = $attribute_list;
            }
            unset($group);

            $this->context->smarty->assign([
                'groups' => $groups,
                'colors' => (count($colors)) ? $colors : false,
                'combinations' => $this->combinations,
                'combinationImages' => $combination_images,
            ]);
        } else {
            $this->context->smarty->assign([
                'groups' => [],
                'colors' => false,
                'combinations' => [],
                'combinationImages' => [],
            ]);
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
                    $val = str_replace(Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR'), '_', Tools::str2url(str_replace([',', '.'], '-', $val)));
                }
            }
        } else {
            $attributes_combinations = [];
        }
        $this->context->smarty->assign([
            'attributesCombinations' => $attributes_combinations,
            'attribute_anchor_separator' => Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR'),
        ]);
    }

    /**
     * Assign template vars related to category.
     */
    protected function assignCategory()
    {
        // Assign category to the template
        if (
            (empty($this->category) || !Validate::isLoadedObject($this->category) || !$this->category->inShop() || !$this->category->isAssociatedToShop())
            && Category::inShopStatic($this->product->id_category_default, $this->context->shop)
        ) {
            $this->category = new Category((int) $this->product->id_category_default, (int) $this->context->language->id);
        }

        $sub_categories = [];
        if (Validate::isLoadedObject($this->category)) {
            $sub_categories = $this->category->getSubCategories($this->context->language->id, true);

            // various assignements before Hook::exec
            $this->context->smarty->assign([
                'category' => $this->category,
                'subCategories' => $sub_categories,
                'subcategories' => $sub_categories,
                'id_category_current' => (int) $this->category->id,
                'id_category_parent' => (int) $this->category->id_parent,
                'return_category_name' => Tools::safeOutput($this->category->getFieldByLang('name')),
                'categories' => Category::getHomeCategories($this->context->language->id, true, (int) $this->context->shop->id),
            ]);
        }
    }

    protected function transformDescriptionWithImg(string $desc)
    {
        $reg = '/\[img\-([0-9]+)\-(left|right)\-([a-zA-Z0-9-_]+)\]/';
        while (preg_match($reg, $desc, $matches)) {
            $link_lmg = $this->context->link->getImageLink($this->product->link_rewrite, $matches[1], $matches[3]);
            $class = $matches[2] == 'left' ? 'class="imageFloatLeft"' : 'class="imageFloatRight"';
            $html_img = '<img src="' . $link_lmg . '" alt="" ' . $class . '/>';
            $desc = str_replace($matches[0], $html_img, $desc);
        }

        return $desc;
    }

    protected function pictureUpload()
    {
        if (!$field_ids = $this->product->getCustomizationFieldIds()) {
            return false;
        }
        $authorized_file_fields = [];
        foreach ($field_ids as $field_id) {
            if ($field_id['type'] == Product::CUSTOMIZE_FILE) {
                $authorized_file_fields[(int) $field_id['id_customization_field']] = 'file' . (int) $field_id['id_customization_field'];
            }
        }
        $indexes = array_flip($authorized_file_fields);
        foreach ($_FILES as $field_name => $file) {
            if (in_array($field_name, $authorized_file_fields) && isset($file['tmp_name']) && !empty($file['tmp_name'])) {
                $file_name = md5(uniqid((string) mt_rand(0, mt_getrandmax()), true));
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
                if (!ImageManager::resize($tmp_name, _PS_UPLOAD_DIR_ . $file_name)) {
                    $this->errors[] = $this->trans('An error occurred during the image upload process.', [], 'Shop.Notifications.Error');
                } elseif (!ImageManager::resize($tmp_name, _PS_UPLOAD_DIR_ . $file_name . '_small', $product_picture_width, $product_picture_height)) {
                    $this->errors[] = $this->trans('An error occurred during the image upload process.', [], 'Shop.Notifications.Error');
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

        $authorized_text_fields = [];
        foreach ($field_ids as $field_id) {
            if ($field_id['type'] == Product::CUSTOMIZE_TEXTFIELD) {
                $authorized_text_fields[(int) $field_id['id_customization_field']] = 'textField' . (int) $field_id['id_customization_field'];
            }
        }

        $indexes = array_flip($authorized_text_fields);
        foreach ($_POST as $field_name => $value) {
            if (in_array($field_name, $authorized_text_fields) && $value != '') {
                if (!Validate::isMessage($value)) {
                    $this->errors[] = $this->trans('Invalid message.', [], 'Shop.Notifications.Error');
                } else {
                    $this->context->cart->addTextFieldToProduct($this->product->id, $indexes[$field_name], Product::CUSTOMIZE_TEXTFIELD, $value);
                }
            } elseif (in_array($field_name, $authorized_text_fields) && $value == '') {
                $this->context->cart->deleteCustomizationToProduct((int) $this->product->id, $indexes[$field_name]);
            }
        }
    }

    /**
     * Calculation of currency-converted discounts for specific prices on product.
     *
     * @param array $specific_prices array of specific prices definitions (DEFAULT currency)
     * @param float $price current price in CURRENT currency
     * @param float $tax_rate in percents
     * @param float $ecotax_amount in DEFAULT currency, with tax
     *
     * @return array
     */
    protected function formatQuantityDiscounts(array $specific_prices, float $price, float $tax_rate, float $ecotax_amount)
    {
        $priceCalculationMethod = Group::getPriceDisplayMethod(Group::getCurrent()->id);
        $isTaxIncluded = false;

        if ($priceCalculationMethod !== null && (int) $priceCalculationMethod === PS_TAX_INC) {
            $isTaxIncluded = true;
        }

        foreach ($specific_prices as $key => &$row) {
            $specificPriceFormatter = new SpecificPriceFormatter(
                $row,
                $isTaxIncluded,
                $this->context->currency,
                Configuration::get('PS_DISPLAY_DISCOUNT_PRICE')
            );
            $row = $specificPriceFormatter->formatSpecificPrice($price, $tax_rate, $ecotax_amount);
            $row['nextQuantity'] = (isset($specific_prices[$key + 1]) ? (int) $specific_prices[$key + 1]['from_quantity'] : -1);
        }

        return $specific_prices;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return Category|null
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Return id_product_attribute by id_product_attribute request parameter.
     *
     * @return int
     */
    protected function getIdProductAttributeByRequest()
    {
        $requestedIdProductAttribute = (int) Tools::getValue('id_product_attribute');

        return $this->tryToGetAvailableIdProductAttribute($requestedIdProductAttribute);
    }

    /**
     * Return id_product_attribute by id_product_attribute group parameter,
     * or request parameter, or the default attribute as a fallback.
     *
     * @return int|null
     *
     * @throws PrestaShopException
     */
    private function getIdProductAttributeByGroupOrRequestOrDefault()
    {
        // If the product has no combinations, we return early
        if (!$this->product->hasCombinations()) {
            return null;
        }

        // Try to retrieve associated product combination id by group
        $idProductAttribute = $this->getIdProductAttributeByGroup();

        // Try to retrieve associated product combination id in request (GET/POST)
        if (null === $idProductAttribute) {
            $idProductAttribute = (int) Tools::getValue('id_product_attribute');
        }

        // Try to retrieve default associated product combination id
        if (0 === $idProductAttribute) {
            $idProductAttribute = (int) Product::getDefaultAttribute($this->product->id);
        }

        return $this->tryToGetAvailableIdProductAttribute($idProductAttribute);
    }

    /**
     * If the PS_DISP_UNAVAILABLE_ATTR functionality is enabled, this method check
     * if $checkedIdProductAttribute is available.
     * If not try to return the first available attribute, if none are available
     * simply returns the input.
     *
     * @param int $checkedIdProductAttribute
     *
     * @return int
     */
    protected function tryToGetAvailableIdProductAttribute(int $checkedIdProductAttribute)
    {
        if (!Configuration::get('PS_DISP_UNAVAILABLE_ATTR')) {
            $productCombinations = $this->product->getAttributeCombinations();
            if (!Product::isAvailableWhenOutOfStock($this->product->out_of_stock)) {
                $availableProductAttributes = array_filter(
                    $productCombinations,
                    function ($elem) {
                        return $elem['quantity'] > 0;
                    }
                );
            } else {
                $availableProductAttributes = $productCombinations;
            }

            $availableProductAttribute = array_filter(
                $availableProductAttributes,
                function ($elem) use ($checkedIdProductAttribute) {
                    return $elem['id_product_attribute'] == $checkedIdProductAttribute;
                }
            );

            if (empty($availableProductAttribute) && count($availableProductAttributes)) {
                // if selected combination is NOT available ($availableProductAttribute) but they are other alternatives ($availableProductAttributes), then we'll try to get the closest.
                if (!Product::isAvailableWhenOutOfStock($this->product->out_of_stock)) {
                    // first lets get information of the selected combination.
                    $checkProductAttribute = array_filter(
                        $productCombinations,
                        function ($elem) use ($checkedIdProductAttribute) {
                            return $elem['id_product_attribute'] == $checkedIdProductAttribute || (!$checkedIdProductAttribute && $elem['default_on']);
                        }
                    );
                    if (count($checkProductAttribute)) {
                        // now lets find other combinations for the selected attributes.
                        $alternativeProductAttribute = [];
                        foreach ($checkProductAttribute as $key => $attribute) {
                            $alternativeAttribute = array_filter(
                                $availableProductAttributes,
                                function ($elem) use ($attribute) {
                                    return $elem['id_attribute'] == $attribute['id_attribute'] && !$elem['is_color_group'];
                                }
                            );
                            foreach ($alternativeAttribute as $key => $value) {
                                $alternativeProductAttribute[$key] = $value;
                            }
                        }

                        if (count($alternativeProductAttribute)) {
                            // if alternative combination is found, order the list by quantity to use the one with more stock.
                            usort($alternativeProductAttribute, function ($a, $b) {
                                if ($a['quantity'] == $b['quantity']) {
                                    return 0;
                                }

                                return ($a['quantity'] > $b['quantity']) ? -1 : 1;
                            });

                            return (int) array_shift($alternativeProductAttribute)['id_product_attribute'];
                        }
                    }
                }

                return (int) array_shift($availableProductAttributes)['id_product_attribute'];
            }
        }

        return $checkedIdProductAttribute;
    }

    /**
     * Return id_product_attribute by the group request parameter.
     *
     * @return int|null
     *
     * @throws PrestaShopException
     */
    private function getIdProductAttributeByGroup()
    {
        try {
            $groups = Tools::getValue('group');
            if (empty($groups)) {
                return null;
            }

            return (int) Product::getIdProductAttributeByIdAttributes(
                $this->product->id,
                $groups,
                true
            );
        } catch (Exception $e) {
            PrestaShopLogger::addLog(
                'Error: ' . $e->getMessage(),
                1,
                $e->getCode(),
                'Product'
            );
        }

        return 0;
    }

    public function getTemplateVarProduct()
    {
        $productSettings = $this->getProductPresentationSettings();
        // Hook displayProductExtraContent
        $extraContentFinder = new ProductExtraContentFinder();

        $product = $this->objectPresenter->present($this->product);
        $product['out_of_stock'] = (int) $this->product->out_of_stock;
        $product['id_product_attribute'] = $this->getIdProductAttributeByGroupOrRequestOrDefault();
        $product['minimal_quantity'] = $this->getProductMinimalQuantity($product);
        $product['quantity_wanted'] = $this->getRequiredQuantity($product);
        $product['extraContent'] = $extraContentFinder->addParams(['product' => $this->product])->present();
        $product['ecotax_tax_inc'] = $this->product->getEcotax(null, true, true);
        $product['ecotax'] = Tools::convertPrice($this->getProductEcotax($product), $this->context->currency, true, $this->context);

        $product_full = Product::getProductProperties($this->context->language->id, $product, $this->context);

        $product_full = $this->addProductCustomizationData($product_full);

        $product_full['show_quantities'] = (bool) (
            Configuration::get('PS_DISPLAY_QTIES')
            && Configuration::get('PS_STOCK_MANAGEMENT')
            && $this->product->quantity > 0
            && $this->product->available_for_order
            && !Configuration::isCatalogMode()
        );
        $product_full['quantity_label'] = ($this->product->quantity > 1) ? $this->trans('Items', [], 'Shop.Theme.Catalog') : $this->trans('Item', [], 'Shop.Theme.Catalog');
        $product_full['quantity_discounts'] = $this->quantity_discounts;

        // Adapt unit price to display settings
        $product_full['unit_price'] = $productSettings->include_taxes ? $product_full['unit_price_tax_included'] : $product_full['unit_price_tax_excluded'];

        $group_reduction = GroupReduction::getValueForProduct($this->product->id, (int) Group::getCurrent()->id);
        if ($group_reduction === false) {
            $group_reduction = Group::getReduction((int) $this->context->cookie->id_customer) / 100;
        }
        $product_full['customer_group_discount'] = $group_reduction;
        $product_full['title'] = $this->getProductPageTitle();

        // round display price (without formatting, we don't want the currency symbol here, just the raw rounded value
        $product_full['rounded_display_price'] = Tools::ps_round(
            $product_full['price'],
            Context::getContext()->currency->precision
        );

        $presenter = $this->getProductPresenter();

        return $presenter->present(
            $productSettings,
            $product_full,
            $this->context->language
        );
    }

    /**
     * @param array $product
     *
     * @return int
     */
    protected function getProductMinimalQuantity(ProductLazyArray|array $product)
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
     * @param array $product
     *
     * @return float
     */
    protected function getProductEcotax(array $product): float
    {
        $ecotax = $product['ecotax'];

        if ($product['id_product_attribute']) {
            $combination = $this->findProductCombinationById($product['id_product_attribute']);
            if (isset($combination['ecotax']) && $combination['ecotax'] > 0) {
                $ecotax = $combination['ecotax'];
            }
        }
        if ($ecotax) {
            // Try to get price display from already assigned smarty variable for better performance
            $priceDisplay = $this->context->smarty->getTemplateVars('priceDisplay');
            if (null === $priceDisplay) {
                $priceDisplay = Product::getTaxCalculationMethod((int) $this->context->cookie->id_customer);
            }

            $useTax = $priceDisplay == 0;
            if ($useTax) {
                $ecotax *= (1 + Tax::getProductEcotaxRate() / 100);
            }
        }

        return (float) $ecotax;
    }

    /**
     * @param int $combinationId
     *
     * @return ProductController|null
     */
    public function findProductCombinationById(int $combinationId)
    {
        $combinations = $this->product->getAttributesGroups($this->context->language->id, $combinationId);

        if (!is_array($combinations) || empty($combinations)) {
            return null;
        }

        return reset($combinations);
    }

    /**
     * @param array $product
     *
     * @return int
     */
    protected function getRequiredQuantity(array $product)
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
            /** @var Category $category */
            if ($category->id_parent != 0 && !$category->is_root_category && $category->active) {
                $breadcrumb['links'][] = [
                    'title' => $category->name,
                    'url' => $this->context->link->getCategoryLink($category),
                ];
            }
        }

        if ($categoryDefault->id_parent != 0 && !$categoryDefault->is_root_category && $categoryDefault->active) {
            $breadcrumb['links'][] = [
                'title' => $categoryDefault->name,
                'url' => $this->context->link->getCategoryLink($categoryDefault),
            ];
        }

        $breadcrumb['links'][] = [
            'title' => $this->product->name,
            'url' => $this->context->link->getProductLink($this->product, null, null, null, null, null, (int) $this->getIdProductAttributeByRequest()),
        ];

        return $breadcrumb;
    }

    protected function addProductCustomizationData(array $product_full)
    {
        if ($product_full['customizable']) {
            $customizationData = [
                'fields' => [],
            ];

            $customized_data = [];

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
                            $field['input_name'] = 'file' . $customization_field['id_customization_field'];

                            break;
                        case Product::CUSTOMIZE_TEXTFIELD:
                            $field['type'] = 'text';
                            $field['text'] = '';
                            $field['input_name'] = 'textField' . $customization_field['id_customization_field'];

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
            $product_full['customizations'] = [
                'fields' => [],
            ];
            $product_full['id_customization'] = 0;
            $product_full['is_customizable'] = false;
        }

        return $product_full;
    }

    /**
     * Initializes a set of commonly used variables related to the current page, available for use
     * in the template. @see FrontController::assignGeneralPurposeVariables for more information.
     *
     * @return array
     */
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        if (!Validate::isLoadedObject($this->product)) {
            $page['title'] = $this->trans('The page you are looking for was not found.', [], 'Shop.Theme.Global');
            $page['page_name'] = 'pagenotfound';

            return $page;
        }

        $page['body_classes']['product-id-' . $this->product->id] = true;
        $page['body_classes']['product-' . $this->product->name] = true;
        $page['body_classes']['product-id-category-' . $this->product->id_category_default] = true;
        $page['body_classes']['product-id-manufacturer-' . $this->product->id_manufacturer] = true;
        $page['body_classes']['product-id-supplier-' . $this->product->id_supplier] = true;

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
        $page['meta']['title'] = $this->getProductPageTitle($page['meta']);

        return $page;
    }

    /**
     * @param array|null $meta
     *
     * @return string
     */
    private function getProductPageTitle(array $meta = null)
    {
        $title = $this->product->name;
        if (isset($meta['title'])) {
            $title = $meta['title'];
        } elseif (isset($meta['meta_title'])) {
            $title = $meta['meta_title'];
        }
        if (!Configuration::get('PS_PRODUCT_ATTRIBUTES_IN_TITLE')) {
            return $title;
        }

        $idProductAttribute = $this->getIdProductAttributeByGroupOrRequestOrDefault();
        if ($idProductAttribute) {
            $attributes = $this->product->getAttributeCombinationsById($idProductAttribute, $this->context->language->id);
            if (is_array($attributes) && count($attributes) > 0) {
                foreach ($attributes as $attribute) {
                    $title .= ' ' . $attribute['group_name'] . ' ' . $attribute['attribute_name'];
                }
            }
        }

        return $title;
    }

    /**
     * {@inheritdoc}
     *
     * Indicates if the provided combination exists and belongs to the product
     *
     * @param int $productAttributeId
     * @param int $productId
     *
     * @return bool
     */
    protected function isValidCombination(int $productAttributeId, int $productId)
    {
        if ($productAttributeId > 0 && $productId > 0) {
            $combination = new Combination($productAttributeId);

            return
                Validate::isLoadedObject($combination)
                && $combination->id_product == $productId;
        }

        return false;
    }

    /**
     * Return information whether we are or not in quick view mode.
     *
     * @return bool
     */
    public function isQuickView(): bool
    {
        return $this->isQuickView;
    }

    /**
     * Set quick view mode.
     *
     * @param bool $enabled
     */
    public function setQuickViewMode(bool $enabled = true)
    {
        $this->isQuickView = $enabled;
    }

    /**
     * Return information whether we are or not in preview mode.
     *
     * @return bool
     */
    public function isPreview(): bool
    {
        return $this->isPreview;
    }

    /**
     * Set preview mode.
     *
     * @param bool $enabled
     */
    public function setPreviewMode(bool $enabled = true)
    {
        $this->isPreview = $enabled;
    }
}
