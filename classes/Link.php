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
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Feature\TokenInUrls;
use PrestaShopBundle\Routing\Converter\LegacyUrlConverter;
use PrestaShopBundle\Service\TransitionalBehavior\AdminPagePreferenceInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LinkCore
{
    /** @var bool Rewriting activation */
    protected $allow;
    protected $url;
    public static $cache = ['page' => []];

    public $protocol_link;
    public $protocol_content;

    protected $ssl_enable;
    protected $urlShopId = null;

    protected static $category_disable_rewrite = null;

    /**
     * Constructor (initialization only).
     *
     * @param string|null $protocolLink
     * @param string|null $protocolContent
     */
    public function __construct($protocolLink = null, $protocolContent = null)
    {
        $this->allow = (bool) Configuration::get('PS_REWRITING_SETTINGS');
        $this->url = $_SERVER['SCRIPT_NAME'];
        $this->protocol_link = $protocolLink;
        $this->protocol_content = $protocolContent;

        if (!defined('_PS_BASE_URL_')) {
            define('_PS_BASE_URL_', Tools::getShopDomain(true));
        }
        if (!defined('_PS_BASE_URL_SSL_')) {
            define('_PS_BASE_URL_SSL_', Tools::getShopDomainSsl(true));
        }

        if (Link::$category_disable_rewrite === null) {
            Link::$category_disable_rewrite = [
                Configuration::get('PS_HOME_CATEGORY'),
                Configuration::get('PS_ROOT_CATEGORY'),
            ];
        }

        $this->ssl_enable = Configuration::get('PS_SSL_ENABLED');
    }

    /**
     * Create a link to delete a product.
     *
     * @param Product|array|int $product ID of the product OR a Product object
     * @param int $idPicture ID of the picture to delete
     *
     * @return string
     */
    public function getProductDeletePictureLink($product, $idPicture)
    {
        $url = $this->getProductLink($product);

        return $url . ((strpos($url, '?')) ? '&' : '?') . 'deletePicture=' . $idPicture;
    }

    /**
     * Return a product object from various product format.
     *
     * @param Product|array|int $product
     * @param int $idLang
     * @param int $idShop
     *
     * @return Product
     *
     * @throws PrestaShopException
     */
    public function getProductObject($product, $idLang, $idShop)
    {
        if (!is_object($product)) {
            if (is_array($product) && isset($product['id_product'])) {
                $product = new Product($product['id_product'], false, $idLang, $idShop);
            } elseif ((int) $product) {
                $product = new Product((int) $product, false, $idLang, $idShop);
            } else {
                throw new PrestaShopException('Invalid product vars');
            }
        }

        return $product;
    }

    /**
     * Create a link to a product.
     *
     * @param ProductCore|array|int $product Product object (can be an ID product, but deprecated)
     * @param string|null $alias
     * @param string|null $category
     * @param string|null $ean13
     * @param int|null $idLang
     * @param int|null $idShop (since 1.5.0) ID shop need to be used when we generate a product link for a product in a cart
     * @param int|null $idProductAttribute ID product attribute
     * @param bool $force_routes
     * @param bool $relativeProtocol
     * @param bool $withIdInAnchor
     * @param array $extraParams
     * @param bool $addAnchor
     *
     * @return string
     *
     * @throws PrestaShopException
     */
    public function getProductLink(
        $product,
        $alias = null,
        $category = null,
        $ean13 = null,
        $idLang = null,
        $idShop = null,
        $idProductAttribute = null,
        $force_routes = false,
        $relativeProtocol = false,
        $withIdInAnchor = false,
        $extraParams = [],
        bool $addAnchor = true
    ) {
        $dispatcher = Dispatcher::getInstance();

        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($idShop, null, $relativeProtocol) . $this->getLangLink($idLang, null, $idShop);

        // Set available keywords
        $params = [];

        if (!is_object($product)) {
            if (is_array($product) && isset($product['id_product'])) {
                $params['id'] = $product['id_product'];
            } elseif ((int) $product) {
                $params['id'] = $product;
            } else {
                throw new PrestaShopException('Invalid product vars');
            }
        } else {
            $params['id'] = $product->id;
        }

        //Attribute equal to 0 or empty is useless, so we force it to null so that it won't be inserted in query parameters
        if (empty($idProductAttribute)) {
            $idProductAttribute = null;
        }
        $params['id_product_attribute'] = $idProductAttribute;
        if (!$alias) {
            $product = $this->getProductObject($product, $idLang, $idShop);
        }
        $params['rewrite'] = (!$alias) ? $product->getFieldByLang('link_rewrite') : $alias;
        if (!$ean13) {
            $product = $this->getProductObject($product, $idLang, $idShop);
        }
        $params['ean13'] = (!$ean13) ? $product->ean13 : $ean13;
        if ($dispatcher->hasKeyword('product_rule', $idLang, 'meta_keywords', $idShop)) {
            $product = $this->getProductObject($product, $idLang, $idShop);
            $params['meta_keywords'] = Tools::str2url($product->getFieldByLang('meta_keywords'));
        }
        if ($dispatcher->hasKeyword('product_rule', $idLang, 'meta_title', $idShop)) {
            $product = $this->getProductObject($product, $idLang, $idShop);
            $params['meta_title'] = Tools::str2url($product->getFieldByLang('meta_title'));
        }

        if ($dispatcher->hasKeyword('product_rule', $idLang, 'manufacturer', $idShop)) {
            $product = $this->getProductObject($product, $idLang, $idShop);
            $params['manufacturer'] = Tools::str2url($product->isFullyLoaded ? $product->manufacturer_name : Manufacturer::getNameById($product->id_manufacturer));
        }

        if ($dispatcher->hasKeyword('product_rule', $idLang, 'supplier', $idShop)) {
            $product = $this->getProductObject($product, $idLang, $idShop);
            $params['supplier'] = Tools::str2url($product->isFullyLoaded ? $product->supplier_name : Supplier::getNameById($product->id_supplier));
        }

        if ($dispatcher->hasKeyword('product_rule', $idLang, 'price', $idShop)) {
            $product = $this->getProductObject($product, $idLang, $idShop);
            $params['price'] = $product->isFullyLoaded ? $product->price : Product::getPriceStatic($product->id, false, null, 6, null, false, true, 1, false, null, null, null, $product->specificPrice);
        }

        if ($dispatcher->hasKeyword('product_rule', $idLang, 'tags', $idShop)) {
            $product = $this->getProductObject($product, $idLang, $idShop);
            $params['tags'] = Tools::str2url($product->getTags($idLang));
        }

        if ($dispatcher->hasKeyword('product_rule', $idLang, 'category', $idShop)) {
            if (!$category) {
                $product = $this->getProductObject($product, $idLang, $idShop);
            }
            $params['category'] = (!$category) ? $product->category : $category;
        }

        if ($dispatcher->hasKeyword('product_rule', $idLang, 'reference', $idShop)) {
            $product = $this->getProductObject($product, $idLang, $idShop);
            $params['reference'] = Tools::str2url($product->reference);
        }

        if ($dispatcher->hasKeyword('product_rule', $idLang, 'categories', $idShop)) {
            $product = $this->getProductObject($product, $idLang, $idShop);
            $params['category'] = (!$category) ? $product->category : $category;
            $cats = [];
            foreach ($product->getParentCategories($idLang) as $cat) {
                if (!in_array($cat['id_category'], Link::$category_disable_rewrite)) {
                    //remove root and home category from the URL
                    $cats[] = $cat['link_rewrite'];
                }
            }
            $params['categories'] = implode('/', $cats);
        }
        if ($idProductAttribute) {
            $product = $this->getProductObject($product, $idLang, $idShop);
        }

        $anchor = $addAnchor && $idProductAttribute ? $product->getAnchor((int) $idProductAttribute, (bool) $withIdInAnchor) : '';

        return $url . $dispatcher->createUrl('product_rule', $idLang, array_merge($params, $extraParams), $force_routes, $anchor, $idShop);
    }

    /**
     * Get the URL to remove the Product from the Cart.
     *
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param int|null $idCustomization
     *
     * @return string
     */
    public function getRemoveFromCartURL(
        $idProduct,
        $idProductAttribute,
        $idCustomization = null
    ) {
        $params = [
            'delete' => 1,
            'id_product' => $idProduct,
            'id_product_attribute' => $idProductAttribute,
        ];

        if ($idCustomization) {
            $params['id_customization'] = $idCustomization;
        }

        return $this->getPageLink(
            'cart',
            true,
            null,
            $params,
            false
        );
    }

    /**
     * Get URL to add one Product to Cart.
     *
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param int|null $idCustomization
     *
     * @return string
     */
    public function getUpQuantityCartURL(
        $idProduct,
        $idProductAttribute,
        $idCustomization = null
    ) {
        return $this->getUpdateQuantityCartURL($idProduct, $idProductAttribute, $idCustomization, 'up');
    }

    /**
     * Get URL to remove one Product to Cart.
     *
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param int|null $idCustomization
     *
     * @return string
     */
    public function getDownQuantityCartURL(
        $idProduct,
        $idProductAttribute,
        $idCustomization = null
    ) {
        return $this->getUpdateQuantityCartURL($idProduct, $idProductAttribute, $idCustomization, 'down');
    }

    /**
     * Get URL to update quantity of Product in Cart.
     *
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param int|null $idCustomization
     * @param string|null $op
     *
     * @return string
     */
    public function getUpdateQuantityCartURL(
        $idProduct,
        $idProductAttribute,
        $idCustomization = null,
        $op = null
    ) {
        $params = [
            'update' => 1,
            'id_product' => $idProduct,
            'id_product_attribute' => $idProductAttribute,
            'token' => Tools::getToken(false),
        ];

        if (null !== $op) {
            $params['op'] = $op;
        }

        if ($idCustomization) {
            $params['id_customization'] = $idCustomization;
        }

        return $this->getPageLink(
            'cart',
            true,
            null,
            $params,
            false
        );
    }

    /**
     * Get add to Cart URL.
     *
     * @param int $idProduct
     * @param int $idProductAttribute
     *
     * @return string
     */
    public function getAddToCartURL($idProduct, $idProductAttribute)
    {
        $params = [
            'add' => 1,
            'id_product' => $idProduct,
            'id_product_attribute' => $idProductAttribute,
        ];

        return $this->getPageLink(
            'cart',
            true,
            null,
            $params,
            false
        );
    }

    /**
     * Return a category object from various category format.
     *
     * @param Category|array|int $category
     * @param int $idLang
     *
     * @return Category
     *
     * @throws PrestaShopException
     */
    public function getCategoryObject($category, $idLang)
    {
        if (!is_object($category)) {
            if (isset($category['id_category'])) {
                $category = new Category($category['id_category'], $idLang);
            } elseif ((int) $category) {
                $category = new Category((int) $category, $idLang);
            } else {
                throw new PrestaShopException('Invalid category vars');
            }
        }

        return $category;
    }

    /**
     * Create a link to a category.
     *
     * @param CategoryCore|array|int|string $category Category object (can be an ID category, but deprecated)
     * @param string|null $alias
     * @param int|null $idLang
     * @param string|null $selectedFilters Url parameter to autocheck filters of the module blocklayered
     * @param int|null $idShop
     * @param bool $relativeProtocol
     *
     * @return string
     */
    public function getCategoryLink(
        $category,
        $alias = null,
        $idLang = null,
        $selectedFilters = null,
        $idShop = null,
        $relativeProtocol = false
    ) {
        $dispatcher = Dispatcher::getInstance();

        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($idShop, null, $relativeProtocol) . $this->getLangLink($idLang, null, $idShop);

        // Set available keywords
        $params = [];
        if (Validate::isLoadedObject($category)) {
            $params['id'] = $category->id;
        } elseif (is_array($category) && isset($category['id_category'])) {
            $params['id'] = $category['id_category'];
        } elseif (is_int($category) || (is_string($category) && ctype_digit($category))) {
            $params['id'] = (int) $category;
        } else {
            throw new \InvalidArgumentException('Invalid category parameter');
        }

        if ((int) $params['id'] === 0) {
            Tools::displayAsDeprecated('Generating URL with id 0 is deprecated');
        }

        $rule = 'category_rule';

        if (!$alias) {
            $category = $this->getCategoryObject($category, $idLang);
        }
        $params['rewrite'] = (!$alias) ? $category->link_rewrite : $alias;
        if ($dispatcher->hasKeyword($rule, $idLang, 'meta_keywords', $idShop)) {
            $category = $this->getCategoryObject($category, $idLang);
            $params['meta_keywords'] = Tools::str2url($category->getFieldByLang('meta_keywords'));
        }
        if ($dispatcher->hasKeyword($rule, $idLang, 'meta_title', $idShop)) {
            $category = $this->getCategoryObject($category, $idLang);
            $params['meta_title'] = Tools::str2url($category->getFieldByLang('meta_title'));
        }

        return $url . Dispatcher::getInstance()->createUrl($rule, $idLang, $params, $this->allow, '', $idShop);
    }

    /**
     * Create a link to a CMS category.
     *
     * @param CMSCategory|int $cmsCategory CMSCategory object (can be an ID category, but deprecated)
     * @param string|null $alias
     * @param int|null $idLang
     * @param int|null $idShop
     * @param bool $relativeProtocol
     *
     * @return string
     */
    public function getCMSCategoryLink(
        $cmsCategory,
        $alias = null,
        $idLang = null,
        $idShop = null,
        $relativeProtocol = false
    ) {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($idShop, null, $relativeProtocol) . $this->getLangLink($idLang, null, $idShop);

        $dispatcher = Dispatcher::getInstance();
        if (!is_object($cmsCategory)) {
            if ($alias !== null && !$dispatcher->hasKeyword('cms_category_rule', $idLang, 'meta_keywords', $idShop) && !$dispatcher->hasKeyword('cms_category_rule', $idLang, 'meta_title', $idShop)) {
                return $url . $dispatcher->createUrl('cms_category_rule', $idLang, ['id' => (int) $cmsCategory, 'rewrite' => (string) $alias], $this->allow, '', $idShop);
            }
            $cmsCategory = new CMSCategory($cmsCategory, $idLang);
        }
        if (is_array($cmsCategory->link_rewrite) && isset($cmsCategory->link_rewrite[(int) $idLang])) {
            $cmsCategory->link_rewrite = $cmsCategory->link_rewrite[(int) $idLang];
        }
        if (is_array($cmsCategory->meta_keywords) && isset($cmsCategory->meta_keywords[(int) $idLang])) {
            $cmsCategory->meta_keywords = $cmsCategory->meta_keywords[(int) $idLang];
        }
        if (is_array($cmsCategory->meta_title) && isset($cmsCategory->meta_title[(int) $idLang])) {
            $cmsCategory->meta_title = $cmsCategory->meta_title[(int) $idLang];
        }

        // Set available keywords
        $params = [];
        $params['id'] = $cmsCategory->id;
        $params['rewrite'] = (!$alias) ? $cmsCategory->link_rewrite : $alias;
        $params['meta_keywords'] = Tools::str2url($cmsCategory->meta_keywords);
        $params['meta_title'] = Tools::str2url($cmsCategory->meta_title);

        return $url . $dispatcher->createUrl('cms_category_rule', $idLang, $params, $this->allow, '', $idShop);
    }

    /**
     * Create a link to a CMS page.
     *
     * @param CMS|int $cms CMS object
     * @param string|null $alias
     * @param bool|null $ssl
     * @param int|null $idLang
     * @param int|null $idShop
     * @param bool $relativeProtocol
     *
     * @return string
     */
    public function getCMSLink(
        $cms,
        $alias = null,
        $ssl = null,
        $idLang = null,
        $idShop = null,
        $relativeProtocol = false
    ) {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($idShop, $ssl, $relativeProtocol) . $this->getLangLink($idLang, null, $idShop);

        $dispatcher = Dispatcher::getInstance();
        if (!is_object($cms)) {
            if ($alias !== null && !$dispatcher->hasKeyword('cms_rule', $idLang, 'meta_keywords', $idShop) && !$dispatcher->hasKeyword('cms_rule', $idLang, 'meta_title', $idShop)) {
                return $url . $dispatcher->createUrl('cms_rule', $idLang, ['id' => (int) $cms, 'rewrite' => (string) $alias], $this->allow, '', $idShop);
            }
            $cms = new CMS($cms, $idLang);
        }

        // Set available keywords
        $params = [];
        $params['id'] = $cms->id;
        $params['rewrite'] = (!$alias) ? (is_array($cms->link_rewrite) ? $cms->link_rewrite[(int) $idLang] : $cms->link_rewrite) : $alias;

        $params['meta_keywords'] = '';
        if (isset($cms->meta_keywords) && !empty($cms->meta_keywords)) {
            $params['meta_keywords'] = is_array($cms->meta_keywords) ? Tools::str2url($cms->meta_keywords[(int) $idLang]) : Tools::str2url($cms->meta_keywords);
        }

        $params['meta_title'] = '';
        if (isset($cms->meta_title) && !empty($cms->meta_title)) {
            $params['meta_title'] = is_array($cms->meta_title) ? Tools::str2url($cms->meta_title[(int) $idLang]) : Tools::str2url($cms->meta_title);
        }

        return $url . $dispatcher->createUrl('cms_rule', $idLang, $params, $this->allow, '', $idShop);
    }

    /**
     * Create a link to a supplier.
     *
     * @param Supplier|int $supplier Supplier object (can be an ID supplier, but deprecated)
     * @param string|null $alias
     * @param int|null $idLang
     * @param int|null $idShop
     * @param bool $relativeProtocol
     *
     * @return string
     */
    public function getSupplierLink(
        $supplier,
        $alias = null,
        $idLang = null,
        $idShop = null,
        $relativeProtocol = false
    ) {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($idShop, null, $relativeProtocol) . $this->getLangLink($idLang, null, $idShop);

        $dispatcher = Dispatcher::getInstance();
        if (!is_object($supplier)) {
            if ($alias !== null &&
                !$dispatcher->hasKeyword('supplier_rule', $idLang, 'meta_keywords', $idShop) &&
                !$dispatcher->hasKeyword('supplier_rule', $idLang, 'meta_title', $idShop)
            ) {
                return $url . $dispatcher->createUrl(
                    'supplier_rule',
                    $idLang,
                    ['id' => (int) $supplier, 'rewrite' => (string) $alias],
                    $this->allow,
                    '',
                    $idShop
                );
            }
            $supplier = new Supplier($supplier, $idLang);
        }

        // Set available keywords
        $params = [];
        $params['id'] = $supplier->id;
        $params['rewrite'] = (!$alias) ? $supplier->link_rewrite : $alias;
        $params['meta_keywords'] = Tools::str2url($supplier->meta_keywords);
        $params['meta_title'] = Tools::str2url($supplier->meta_title);

        return $url . $dispatcher->createUrl('supplier_rule', $idLang, $params, $this->allow, '', $idShop);
    }

    /**
     * Create a link to a manufacturer.
     *
     * @param Manufacturer|int $manufacturer Manufacturer object (can be an ID supplier, but deprecated)
     * @param string|null $alias
     * @param int|null $idLang
     * @param int|null $idShop
     * @param bool $relativeProtocol
     *
     * @return string
     */
    public function getManufacturerLink(
        $manufacturer,
        $alias = null,
        $idLang = null,
        $idShop = null,
        $relativeProtocol = false
    ) {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($idShop, null, $relativeProtocol) . $this->getLangLink($idLang, null, $idShop);

        $dispatcher = Dispatcher::getInstance();
        if (!is_object($manufacturer)) {
            if ($alias !== null && !$dispatcher->hasKeyword('manufacturer_rule', $idLang, 'meta_keywords', $idShop) && !$dispatcher->hasKeyword('manufacturer_rule', $idLang, 'meta_title', $idShop)) {
                return $url . $dispatcher->createUrl('manufacturer_rule', $idLang, ['id' => (int) $manufacturer, 'rewrite' => (string) $alias], $this->allow, '', $idShop);
            }
            $manufacturer = new Manufacturer($manufacturer, $idLang);
        }

        // Set available keywords
        $params = [];
        $params['id'] = $manufacturer->id;
        $params['rewrite'] = (!$alias) ? $manufacturer->link_rewrite : $alias;
        $params['meta_keywords'] = Tools::str2url($manufacturer->meta_keywords);
        $params['meta_title'] = Tools::str2url($manufacturer->meta_title);

        return $url . $dispatcher->createUrl('manufacturer_rule', $idLang, $params, $this->allow, '', $idShop);
    }

    /**
     * Create a link to a module.
     *
     * @since    1.5.0
     *
     * @param string $module Module name
     * @param string $controller
     * @param array $params
     * @param bool|null $ssl
     * @param int|null $idLang
     * @param int|null $idShop
     * @param bool $relativeProtocol
     *
     * @return string
     */
    public function getModuleLink(
        $module,
        $controller = 'default',
        array $params = [],
        $ssl = null,
        $idLang = null,
        $idShop = null,
        $relativeProtocol = false
    ) {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($idShop, $ssl, $relativeProtocol) . $this->getLangLink($idLang, null, $idShop);

        // Set available keywords
        $params['module'] = $module;
        $params['controller'] = $controller ? $controller : 'default';

        // If the module has its own route ... just use it !
        if (Dispatcher::getInstance()->hasRoute('module-' . $module . '-' . $controller, $idLang, $idShop)) {
            return $this->getPageLink('module-' . $module . '-' . $controller, $ssl, $idLang, $params);
        } else {
            return $url . Dispatcher::getInstance()->createUrl('module', $idLang, $params, $this->allow, '', $idShop);
        }
    }

    /**
     * Use controller name to create a link.
     *
     * Warning on fallback to Symfony Router, this exceptions can be thrown
     * - RouteNotFoundException If the named route doesn't exist
     * - MissingMandatoryParametersException When some parameters are missing that are mandatory for the route
     * - InvalidParameterException When a parameter value for a placeholder is not correct because it does not match the requirement
     *
     * @param string $controller
     * @param bool $withToken include or not the token in the url
     * @param array $sfRouteParams (Since 1.7.0.0) Optional parameters to use into New architecture specific cases. If these specific cases should redirect to legacy URLs, then this parameter is used to complete GET query string
     * @param array $params (Since 1.7.0.3) Optional
     *
     * @return string url
     */
    public function getAdminLink($controller, $withToken = true, $sfRouteParams = [], $params = [])
    {
        // Cannot generate admin link from front
        if (!defined('_PS_ADMIN_DIR_')) {
            return '';
        }

        if (!is_array($sfRouteParams)) {
            $sfRouteParams = [];
        }

        if ($withToken && !TokenInUrls::isDisabled()) {
            $params['token'] = Tools::getAdminTokenLite($controller);
        }

        // Even if URL rewriting is not enabled, the page handled by Symfony must work !
        // For that, we add an 'index.php' in the URL before the route
        $sfContainer = SymfonyContainer::getInstance();
        $sfRouter = null;
        $legacyUrlConverter = null;
        if (null !== $sfContainer) {
            /** @var UrlGeneratorInterface $sfRouter */
            $sfRouter = $sfContainer->get('router');
            /** @var LegacyUrlConverter $legacyUrlConverter */
            $legacyUrlConverter = $sfContainer->get('prestashop.bundle.routing.converter.legacy_url_converter');
        }

        if (!empty($sfRouteParams['route']) && null !== $sfRouter) {
            $sfRoute = $sfRouteParams['route'];
            unset($sfRouteParams['route']);

            return $sfRouter->generate($sfRoute, $sfRouteParams, UrlGeneratorInterface::ABSOLUTE_URL);
        }

        $routeName = '';
        switch ($controller) {
            case 'AdminProducts':
                // New architecture modification: temporary behavior to switch between old and new controllers.
                /** @var AdminPagePreferenceInterface $pagePreference */
                $pagePreference = $sfContainer->get('prestashop.core.admin.page_preference_interface');
                $redirectLegacy = $pagePreference->getTemporaryShouldUseLegacyPage('product');
                if (!$redirectLegacy) {
                    if (array_key_exists('id_product', $sfRouteParams)) {
                        if (array_key_exists('deleteproduct', $sfRouteParams)) {
                            return $sfRouter->generate(
                                'admin_product_unit_action',
                                ['action' => 'delete', 'id' => $sfRouteParams['id_product']]
                            );
                        }
                        //default: if (array_key_exists('updateproduct', $sfRouteParams))
                        return $sfRouter->generate(
                            'admin_product_form',
                            ['id' => $sfRouteParams['id_product']]
                        );
                    }
                    if (array_key_exists('submitFilterproduct', $sfRouteParams)) {
                        $routeParams = [];
                        if (array_key_exists('filter_column_sav_quantity', $sfRouteParams)) {
                            $routeParams['quantity'] = $sfRouteParams['filter_column_sav_quantity'];
                        }
                        if (array_key_exists('filter_column_active', $sfRouteParams)) {
                            $routeParams['active'] = $sfRouteParams['filter_column_active'];
                        }

                        return $sfRouter->generate('admin_product_catalog_filters', $routeParams);
                    }

                    return $sfRouter->generate('admin_product_catalog', $sfRouteParams);
                } else {
                    $params = array_merge($params, $sfRouteParams);
                }

                break;

            case 'AdminTranslations':
                // In case of email body translations we want to get a link to legacy controller,
                // in other cases - it's the migrated controller
                if (isset($params['selected-emails']) && $params['selected-emails'] === 'body') {
                    break;
                }

                // In case of modules translations get a link to legacy controller
                if (isset($params['type']) && $params['type'] === 'modules' && isset($params['module'])) {
                    break;
                }

                // When params are empty or only token exists we want to use default translations route.
                if (empty($params) || 1 === count($params) && isset($params['token'])) {
                    $routeName = 'admin_international_translations_show_settings';
                }

                break;

            case 'AdminEmployees':
                if (!isset($params['action'])) {
                    break;
                }

                if ('toggleMenu' === $params['action']) {
                    // Linking legacy toggle menu action to migrated action.
                    $routeName = 'admin_employees_toggle_navigation';
                } elseif ('formLanguage' === $params['action']) {
                    // Linking legacy change form language action to migrated action.
                    $routeName = 'admin_employees_change_form_language';
                }

                break;
        }

        if (!empty($routeName) && null !== $sfRouter) {
            $sfRoute = array_key_exists('route', $sfRouteParams) ? $sfRouteParams['route'] : $routeName;

            return $sfRouter->generate($sfRoute, $sfRouteParams, UrlGeneratorInterface::ABSOLUTE_URL);
        }

        if (empty($routeName) && null !== $legacyUrlConverter) {
            try {
                $conversionParameters = array_merge(['controller' => $controller], $sfRouteParams, $params);
                unset($conversionParameters['token']);

                return $legacyUrlConverter->convertByParameters($conversionParameters);
            } catch (CoreException $e) {
                //The url could not be converted so we fallback on legacy url
            }
        }

        $idLang = Context::getContext()->language->id;

        return $this->getAdminBaseLink() . basename(_PS_ADMIN_DIR_) . '/' . Dispatcher::getInstance()->createUrl($controller, $idLang, $params);
    }

    /**
     * Warning on fallback to Symfony Router, this exceptions can be thrown
     * - RouteNotFoundException If the named route doesn't exist
     * - MissingMandatoryParametersException When some parameters are missing that are mandatory for the route
     * - InvalidParameterException When a parameter value for a placeholder is not correct because it does not match the requirement
     *
     * @param array $tab
     *
     * @return string
     */
    public function getTabLink(array $tab)
    {
        if (!empty($tab['route_name'])) {
            $sfContainer = SymfonyContainer::getInstance();
            if (null !== $sfContainer) {
                /** @var UrlGeneratorInterface $sfRouter */
                $sfRouter = $sfContainer->get('router');

                return $sfRouter->generate($tab['route_name']);
            }
        }

        return $this->getAdminLink($tab['class_name']);
    }

    /**
     * Used when you explicitly want to create a LEGACY admin link, this should be deprecated
     * in 9.x
     *
     * @param string $controller
     * @param bool $withToken
     * @param array $params
     *
     * @return string
     */
    public function getLegacyAdminLink($controller, $withToken = true, $params = [])
    {
        $idLang = Context::getContext()->language->id;

        if ($withToken && !TokenInUrls::isDisabled()) {
            $params['token'] = Tools::getAdminTokenLite($controller);
        }

        return $this->getAdminBaseLink() . basename(_PS_ADMIN_DIR_) . '/' . Dispatcher::getInstance()->createUrl($controller, $idLang, $params);
    }

    /**
     * @param int|null $idShop
     * @param bool|null $ssl
     * @param bool $relativeProtocol
     *
     * @return string
     */
    public function getAdminBaseLink($idShop = null, $ssl = null, $relativeProtocol = false)
    {
        if (null === $ssl) {
            $ssl = Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
        }

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
            if (null === $idShop) {
                $idShop = $this->getMatchingUrlShopId();
            }

            //Use the matching shop if present, or fallback on the default one
            if (null !== $idShop) {
                $shop = new Shop($idShop);
            } else {
                $shop = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));
            }
            list($domainSSL, $domain) = $this->getShopDomainActive($shop, $idShop);
        } else {
            $shop = Context::getContext()->shop;
            $domainSSL = $shop->domain_ssl;
            $domain = $shop->domain;
        }

        if ($relativeProtocol) {
            $base = '//' . ($ssl && $this->ssl_enable ? $domainSSL : $domain);
        } else {
            $base = (($ssl && $this->ssl_enable) ? 'https://' . $domainSSL : 'http://' . $domain);
        }

        return $base . $shop->getBaseURI();
    }

    /**
     * Search for a shop whose domain matches the current url.
     *
     * @return int|null
     */
    public function getMatchingUrlShopId()
    {
        if (null === $this->urlShopId) {
            $host = Tools::getHttpHost();
            $request_uri = rawurldecode($_SERVER['REQUEST_URI']);

            $sql = 'SELECT s.id_shop, CONCAT(su.physical_uri, su.virtual_uri) AS uri, su.domain, su.main
                    FROM ' . _DB_PREFIX_ . 'shop_url su
                    LEFT JOIN ' . _DB_PREFIX_ . 'shop s ON (s.id_shop = su.id_shop)
                    WHERE (su.domain = \'' . pSQL($host) . '\' OR su.domain_ssl = \'' . pSQL($host) . '\')
                        AND s.active = 1
                        AND s.deleted = 0
                    ORDER BY LENGTH(CONCAT(su.physical_uri, su.virtual_uri)) DESC';

            try {
                $result = Db::getInstance()->executeS($sql);
            } catch (PrestaShopDatabaseException $e) {
                return null;
            }

            foreach ($result as $row) {
                // A shop matching current URL was found
                if (preg_match('#^' . preg_quote($row['uri'], '#') . '#i', $request_uri)) {
                    $this->urlShopId = $row['id_shop'];

                    break;
                }
            }
        }

        return $this->urlShopId;
    }

    /**
     * Returns a link to a product image for display
     * Note: image filesystem stores product images in subdirectories of img/p/.
     *
     * @param string $name Rewrite link of the image
     * @param string|int $idImage numeric ID of product image or a name of default image like "fr-default"
     * @param string|null $type Image thumbnail name (small_default, medium_default, large_default, etc.)
     * @param string $extension What image extension should the link point to
     *
     * @return string
     */
    public function getImageLink($name, $idImage, $type = null, string $extension = 'jpg')
    {
        $type = ($type ? '-' . $type : '');
        $idImage = (string) $idImage;

        // Default image like "fr-default"
        if (strpos($idImage, 'default') !== false) {
            $theme = ((Shop::isFeatureActive() && file_exists(_PS_PRODUCT_IMG_DIR_ . $idImage . $type . '-' . Context::getContext()->shop->theme_name . '.jpg')) ? '-' . Context::getContext()->shop->theme_name : '');
            $uriPath = _THEME_PROD_DIR_ . $idImage . $type . $theme . '.' . $extension;

        // Regular image with numeric ID
        } else {
            // We will still process the old way of requesting images in a form of productID-imageID, but notify developers
            if (strpos($idImage, '-')) {
                $idImage = explode('-', $idImage)[1];
                if (_PS_MODE_DEV_) {
                    trigger_error(
                        'Passing image identifier in the old format is deprecated, use only image ID. This fallback will be removed in next major.',
                        E_USER_DEPRECATED
                    );
                }
            }

            $theme = ((Shop::isFeatureActive() && file_exists(_PS_PRODUCT_IMG_DIR_ . Image::getImgFolderStatic($idImage) . $idImage . $type . '-' . (int) Context::getContext()->shop->theme_name . '.jpg')) ? '-' . Context::getContext()->shop->theme_name : '');

            // If friendly URLs are enabled
            if ($this->allow) {
                $uriPath = __PS_BASE_URI__ . $idImage . $type . $theme . '/' . $name . '.' . $extension;
            // If friendly URLs are disabled
            } else {
                $uriPath = _THEME_PROD_DIR_ . Image::getImgFolderStatic($idImage) . $idImage . $type . $theme . '.' . $extension;
            }
        }

        return $this->protocol_content . Tools::getMediaServer($uriPath) . $uriPath;
    }

    /**
     * Returns a link to a supplier image for display.
     *
     * @param int $idSupplier
     * @param string|null $type Image thumbnail name (small_default, medium_default, large_default, etc.)
     * @param string $extension What image extension should the link point to
     *
     * @return string
     */
    public function getSupplierImageLink($idSupplier, $type = null, string $extension = 'jpg')
    {
        $idSupplier = (int) $idSupplier;

        if (file_exists(_PS_SUPP_IMG_DIR_ . $idSupplier . (empty($type) ? '.' . $extension : '-' . $type . '.' . $extension))) {
            $uriPath = _THEME_SUP_DIR_ . $idSupplier . (empty($type) ? '.' . $extension : '-' . $type . '.' . $extension);
        } elseif (!empty($type) && file_exists(_PS_SUPP_IMG_DIR_ . $idSupplier . '.' . $extension)) { // !empty($type) because if is empty, is already tested
            $uriPath = _THEME_SUP_DIR_ . $idSupplier . '.' . $extension;
        } elseif (file_exists(_PS_SUPP_IMG_DIR_ . Context::getContext()->language->iso_code . (empty($type) ? '.' . $extension : '-default-' . $type . '.' . $extension))) {
            $uriPath = _THEME_SUP_DIR_ . Context::getContext()->language->iso_code . (empty($type) ? '.' . $extension : '-default-' . $type . '.' . $extension);
        } else {
            $uriPath = _THEME_SUP_DIR_ . Context::getContext()->language->iso_code . '.' . $extension;
        }

        return $this->protocol_content . Tools::getMediaServer($uriPath) . $uriPath;
    }

    /**
     * Returns a link to a manufacturer image for display.
     *
     * @param int $idManufacturer
     * @param string|null $type Image thumbnail name (small_default, medium_default, large_default, etc.)
     * @param string $extension What image extension should the link point to
     *
     * @return string
     */
    public function getManufacturerImageLink($idManufacturer, $type = null, string $extension = 'jpg')
    {
        $idManufacturer = (int) $idManufacturer;

        if (file_exists(_PS_MANU_IMG_DIR_ . $idManufacturer . (empty($type) ? '.' . $extension : '-' . $type . '.' . $extension))) {
            $uriPath = _THEME_MANU_DIR_ . $idManufacturer . (empty($type) ? '.' . $extension : '-' . $type . '.' . $extension);
        } elseif (!empty($type) && file_exists(_PS_MANU_IMG_DIR_ . $idManufacturer . '.' . $extension)) { // !empty($type) because if is empty, is already tested
            $uriPath = _THEME_MANU_DIR_ . $idManufacturer . '.' . $extension;
        } elseif (file_exists(_PS_MANU_IMG_DIR_ . Context::getContext()->language->iso_code . (empty($type) ? '.' . $extension : '-default-' . $type . '.' . $extension))) {
            $uriPath = _THEME_MANU_DIR_ . Context::getContext()->language->iso_code . (empty($type) ? '.' . $extension : '-default-' . $type . '.' . $extension);
        } else {
            $uriPath = _THEME_MANU_DIR_ . Context::getContext()->language->iso_code . '.' . $extension;
        }

        return $this->protocol_content . Tools::getMediaServer($uriPath) . $uriPath;
    }

    /**
     * Returns a link to a store image for display.
     *
     * @param string $name
     * @param int $idStore
     * @param string|null $type Image thumbnail name (small_default, medium_default, large_default, etc.)
     * @param string $extension What image extension should the link point to
     *
     * @return string
     */
    public function getStoreImageLink($name, $idStore, $type = null, string $extension = 'jpg')
    {
        $idStore = (int) $idStore;

        if (file_exists(_PS_STORE_IMG_DIR_ . $idStore . (empty($type) ? '.' . $extension : '-' . $type . '.' . $extension))) {
            $uriPath = _THEME_STORE_DIR_ . $idStore . (empty($type) ? '.' . $extension : '-' . $type . '.' . $extension);
        } elseif (!empty($type) && file_exists(_PS_STORE_IMG_DIR_ . $idStore . '.' . $extension)) { // !empty($type) because if is empty, is already tested
            $uriPath = _THEME_STORE_DIR_ . $idStore . '.' . $extension;
        } elseif (file_exists(_PS_STORE_IMG_DIR_ . Context::getContext()->language->iso_code . (empty($type) ? '.' . $extension : $type . '.' . $extension))) {
            $uriPath = _THEME_STORE_DIR_ . Context::getContext()->language->iso_code . (empty($type) ? '.' . $extension : $type . '.' . $extension);
        } else {
            $uriPath = _THEME_STORE_DIR_ . Context::getContext()->language->iso_code . '.' . $extension;
        }

        return $this->protocol_content . Tools::getMediaServer($uriPath) . $uriPath;
    }

    /**
     * @param string $filepath
     *
     * @return string
     */
    public function getMediaLink($filepath)
    {
        return $this->protocol_content . Tools::getMediaServer($filepath) . $filepath;
    }

    /**
     * Create a simple link.
     *
     * @param string $controller
     * @param bool|null $ssl
     * @param int|null $idLang
     * @param string|array|null $request
     * @param bool $requestUrlEncode Use URL encode
     * @param int|null $idShop
     * @param bool $relativeProtocol
     *
     * @return string Page link
     */
    public function getPageLink($controller, $ssl = null, $idLang = null, $request = null, $requestUrlEncode = false, $idShop = null, $relativeProtocol = false)
    {
        //If $controller contains '&' char, it means that $controller contains request data and must be parsed first
        $p = strpos($controller, '&');
        if ($p !== false) {
            $request = substr($controller, $p + 1);
            $requestUrlEncode = false;
            $controller = substr($controller, 0, $p);
        }

        $controller = Tools::strReplaceFirst('.php', '', $controller);
        if (!$idLang) {
            $idLang = (int) Context::getContext()->language->id;
        }

        //need to be unset because getModuleLink need those params when rewrite is enable
        if (is_array($request)) {
            if (isset($request['module'])) {
                unset($request['module']);
            }
            if (isset($request['controller'])) {
                unset($request['controller']);
            }
        } else {
            /** @FIXME html_entity_decode has been added due to '&amp;' => '%3B' ... */
            $request = html_entity_decode($request ?? '');
            if ($requestUrlEncode) {
                $request = urlencode($request);
            }
            parse_str($request, $request);
        }

        if ($controller === 'cart' && (!empty($request['add']) || !empty($request['delete'])) && Configuration::get('PS_TOKEN_ENABLE')) {
            $request['token'] = Tools::getToken(false);
        }

        $uriPath = Dispatcher::getInstance()->createUrl($controller, $idLang, $request, false, '', $idShop);

        return $this->getBaseLink($idShop, $ssl, $relativeProtocol) . $this->getLangLink($idLang, null, $idShop) . ltrim($uriPath, '/');
    }

    /**
     * @param string $name
     * @param int $idCategory
     * @param string|null $type Image thumbnail name (small_default, medium_default, large_default, etc.)
     * @param string $extension What image extension should the link point to
     *
     * @return string
     */
    public function getCatImageLink($name, $idCategory, $type = null, string $extension = 'jpg')
    {
        if ($this->allow && $type) {
            $uriPath = __PS_BASE_URI__ . 'c/' . $idCategory . '-' . $type . '/' . $name . '.' . $extension;
        } else {
            $uriPath = _THEME_CAT_DIR_ . $idCategory . ($type ? '-' . $type : '') . '.' . $extension;
        }

        return $this->protocol_content . Tools::getMediaServer($uriPath) . $uriPath;
    }

    /**
     * Create link after language change, for the change language block.
     *
     * @param int $idLang Language ID
     * @param Context|null $context the context if needed
     *
     * @return string link
     */
    public function getLanguageLink($idLang, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $params = $_GET;
        unset($params['isolang'], $params['controller']);

        if (!$this->allow) {
            $params['id_lang'] = $idLang;
        } else {
            unset($params['id_lang']);
        }

        $controller = Dispatcher::getInstance()->getController();

        if (!empty($context->controller->php_self)) {
            $controller = $context->controller->php_self;
        }

        if ($controller == 'product' && isset($params['id_product'])) {
            return $this->getProductLink((int) $params['id_product'], null, null, null, (int) $idLang);
        } elseif ($controller == 'category' && isset($params['id_category'])) {
            return $this->getCategoryLink((int) $params['id_category'], null, (int) $idLang);
        } elseif ($controller == 'supplier' && isset($params['id_supplier'])) {
            return $this->getSupplierLink((int) $params['id_supplier'], null, (int) $idLang);
        } elseif ($controller == 'manufacturer' && isset($params['id_manufacturer'])) {
            return $this->getManufacturerLink((int) $params['id_manufacturer'], null, (int) $idLang);
        } elseif ($controller == 'cms' && isset($params['id_cms'])) {
            return $this->getCMSLink(new CMS((int) $params['id_cms']), null, null, (int) $idLang);
        } elseif ($controller == 'cms' && isset($params['id_cms_category'])) {
            return $this->getCMSCategoryLink(new CMSCategory((int) $params['id_cms_category']), null, (int) $idLang);
        } elseif (isset($params['fc']) && $params['fc'] == 'module') {
            $module = Validate::isModuleName(Tools::getValue('module')) ? Tools::getValue('module') : '';
            if (!empty($module)) {
                unset($params['fc'], $params['module']);

                return $this->getModuleLink($module, $controller, $params, null, (int) $idLang);
            }
        }

        return $this->getPageLink($controller, null, $idLang, $params);
    }

    /**
     * @param string $url
     * @param int $p
     *
     * @return string
     */
    public function goPage($url, $p)
    {
        $url = rtrim(str_replace('?&', '?', $url), '?');

        return $url . ($p == 1 ? '' : (!strstr($url, '?') ? '?' : '&') . 'p=' . (int) $p);
    }

    /**
     * Get pagination link.
     *
     * @param string $type Controller name
     * @param int $idObject
     * @param bool $nb Show nb element per page attribute
     * @param bool $sort Show sort attribute
     * @param bool $pagination Show page number attribute
     * @param bool $array If false return an url, if true return an array
     *
     * @return string|array
     */
    public function getPaginationLink($type, $idObject, $nb = false, $sort = false, $pagination = false, $array = false)
    {
        // If no parameter $type, try to get it by using the controller name
        if (!$type && !$idObject) {
            $method_name = 'get' . Dispatcher::getInstance()->getController() . 'Link';
            if (method_exists($this, $method_name) && isset($_GET['id_' . Dispatcher::getInstance()->getController()])) {
                $type = Dispatcher::getInstance()->getController();
                $idObject = $_GET['id_' . $type];
            }
        }

        if ($type && $idObject) {
            $url = $this->{'get' . $type . 'Link'}($idObject, null);
        } else {
            if (isset(Context::getContext()->controller->php_self)) {
                $name = Context::getContext()->controller->php_self;
            } else {
                $name = Dispatcher::getInstance()->getController();
            }
            $url = $this->getPageLink($name);
        }

        $vars = [];
        $varsNb = ['n'];
        $varsSort = ['order'];
        $varsPagination = ['page'];

        foreach ($_GET as $k => $value) {
            if ($k != 'id_' . $type && $k != 'controller') {
                if (Configuration::get('PS_REWRITING_SETTINGS') && ($k == 'isolang' || $k == 'id_lang')) {
                    continue;
                }
                $ifNb = (!$nb || !in_array($k, $varsNb));
                $ifSort = (!$sort || !in_array($k, $varsSort));
                $ifPagination = (!$pagination || !in_array($k, $varsPagination));
                if ($ifNb && $ifSort && $ifPagination) {
                    if (!is_array($value)) {
                        $vars[urlencode($k)] = $value;
                    } else {
                        foreach (explode('&', http_build_query([$k => $value], '', '&')) as $key => $val) {
                            $data = explode('=', $val);
                            $vars[urldecode($data[0])] = $data[1];
                        }
                    }
                }
            }
        }

        if (!$array) {
            if (count($vars)) {
                return $url . (!strstr($url, '?') && ($this->allow || $url == $this->url) ? '?' : '&') . http_build_query($vars, '', '&');
            } else {
                return $url;
            }
        }

        $vars['requestUrl'] = $url;

        if ($type && $idObject) {
            $vars['id_' . $type] = (is_object($idObject) ? (int) $idObject->id : (int) $idObject);
        }

        if (!$this->allow) {
            $vars['controller'] = Dispatcher::getInstance()->getController();
        }

        return $vars;
    }

    /**
     * @param string $url
     * @param string $orderBy
     * @param string $orderWay
     *
     * @return string
     */
    public function addSortDetails($url, $orderBy, $orderWay)
    {
        return $url . (!strstr($url, '?') ? '?' : '&') . 'orderby=' . urlencode($orderBy) . '&orderway=' . urlencode($orderWay);
    }

    /**
     * @param int|null $idLang
     * @param Context|null $context
     * @param int|null $idShop
     *
     * @return string
     */
    protected function getLangLink($idLang = null, Context $context = null, $idShop = null)
    {
        static $psRewritingSettings = null;
        if ($psRewritingSettings === null) {
            $psRewritingSettings = (int) Configuration::get('PS_REWRITING_SETTINGS', null, null, $idShop);
        }

        if (!$context) {
            $context = Context::getContext();
        }

        if ((!$this->allow && in_array($idShop, [$context->shop->id,  null])) || !Language::isMultiLanguageActivated($idShop) || !$psRewritingSettings) {
            return '';
        }

        if (!$idLang) {
            $idLang = $context->language->id;
        }

        return Language::getIsoById($idLang) . '/';
    }

    /**
     * @param int|null $idShop
     * @param bool|null $ssl
     * @param bool $relativeProtocol
     *
     * @return string
     */
    public function getBaseLink($idShop = null, $ssl = null, $relativeProtocol = false)
    {
        if (null === $ssl) {
            $ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
        }

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $idShop !== null) {
            $shop = new Shop($idShop);
        } else {
            $shop = Context::getContext()->shop;
        }

        list($domainSSL, $domain) = $this->getShopDomainActive($shop, $idShop);

        if ($relativeProtocol) {
            $base = '//' . ($ssl && $this->ssl_enable ? $domainSSL : $domain);
        } else {
            $base = (($ssl && $this->ssl_enable) ? 'https://' . $domainSSL : 'http://' . $domain);
        }

        return $base . $shop->getBaseURI();
    }

    /**
     * Clean url http://website.com/admin_dir/foo => foo
     * Remove index.php?
     * Remove last char if it's ? or &
     * Remove token if exists.
     *
     * @param string $url
     *
     * @return string
     */
    public static function getQuickLink($url)
    {
        $legacyEnvironment = stripos($url, 'controller');

        $patterns = [
            '#' . Context::getContext()->link->getBaseLink() . '#',
            '#' . basename(_PS_ADMIN_DIR_) . '/#',
            '/index.php/',
            '/_?token=[a-zA-Z0-9\_]+/',
        ];

        // If __PS_BASE_URI__ = '/', it destroys urls when is 'product/new' or 'modules/manage' (vhost for example)
        if ('/' !== __PS_BASE_URI__) {
            $patterns[] = '#' . __PS_BASE_URI__ . '#';
        }

        $url = preg_replace($patterns, '', $url);
        $url = trim($url, '?&/');

        return 'index.php' . (!empty($legacyEnvironment) ? '?' : '/') . $url;
    }

    /**
     * Check if url match with current url.
     *
     * @param string $url
     *
     * @return bool
     */
    public function matchQuickLink($url)
    {
        $quickLink = $this->getQuickLink($url);

        return $quickLink === ($this->getQuickLink($_SERVER['REQUEST_URI']));
    }

    /**
     * @param array $params
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public static function getUrlSmarty($params)
    {
        $context = Context::getContext();

        if (!isset($params['params'])) {
            $params['params'] = [];
        }

        if (isset($params['id'])) {
            $entity = str_replace('-', '_', $params['entity']);
            $id = ['id_' . $entity => $params['id']];
            $params['params'] = array_merge($id, $params['params']);
        }

        $default = [
            'id_lang' => $context->language->id,
            'id_shop' => null,
            'alias' => null,
            'ssl' => null,
            'relative_protocol' => true,
            'with_id_in_anchor' => false,
            'extra_params' => [],
            'add_anchor' => true,
        ];
        $params = array_merge($default, $params);

        $urlParameters = http_build_query($params['params']);

        switch ($params['entity']) {
            case 'supplier':
                $link = $context->link->getSupplierLink(
                    new Supplier($params['id'], $params['id_lang']),
                    $params['alias'],
                    $params['id_lang'],
                    $params['id_shop'],
                    $params['relative_protocol']
                );
                break;
            case 'language':
                $link = $context->link->getLanguageLink($params['id']);

                break;
            case 'product':
                $link = $context->link->getProductLink(
                    $params['id'],
                    $params['alias'],
                    (isset($params['category']) ? $params['category'] : null),
                    (isset($params['ean13']) ? $params['ean13'] : null),
                    $params['id_lang'],
                    $params['id_shop'],
                    (isset($params['ipa']) ? (int) $params['ipa'] : 0),
                    false,
                    $params['relative_protocol'],
                    $params['with_id_in_anchor'],
                    $params['extra_params'],
                    $params['add_anchor']
                );

                break;
            case 'category':
                $params = array_merge(['selected_filters' => null], $params);
                $link = $context->link->getCategoryLink(
                    new Category($params['id'], $params['id_lang']),
                    $params['alias'],
                    $params['id_lang'],
                    $params['selected_filters'],
                    $params['id_shop'],
                    $params['relative_protocol']
                );

                break;
            case 'categoryImage':
                $params = array_merge(['selected_filters' => null], $params);
                $link = $context->link->getCatImageLink(
                    $params['name'],
                    $params['id'],
                    $params['type'] = (isset($params['type']) ? $params['type'] : null)
                );

                break;
            case 'manufacturer':
                $link = $context->link->getManufacturerLink(
                    new Manufacturer($params['id'], $params['id_lang']),
                    $params['alias'],
                    $params['id_lang'],
                    $params['id_shop'],
                    $params['relative_protocol']
                );
                break;
            case 'manufacturerImage':
                $link = $context->link->getManufacturerImageLink(
                    (int) $params['id'],
                    $params['type'] = (isset($params['type']) ? $params['type'] : null)
                );

                break;
            case 'supplierImage':
                $link = $context->link->getSupplierImageLink(
                    (int) $params['id'],
                    $params['type'] = (isset($params['type']) ? $params['type'] : null)
                );

                break;
            case 'cms':
                $link = $context->link->getCMSLink(
                    new CMS($params['id'], $params['id_lang']),
                    $params['alias'],
                    $params['ssl'],
                    $params['id_lang'],
                    $params['id_shop'],
                    $params['relative_protocol']
                );

                break;
            case 'module':
                $params = array_merge([
                    'selected_filters' => null,
                    'params' => [],
                    'controller' => 'default',
                ], $params);
                $link = $context->link->getModuleLink(
                    $params['name'],
                    $params['controller'],
                    $params['params'],
                    $params['ssl'],
                    $params['id_lang'],
                    $params['id_shop'],
                    $params['relative_protocol']
                );

                break;
            case 'sf':
                if (!array_key_exists('route', $params)) {
                    throw new \InvalidArgumentException('You need to setup a `route` attribute.');
                }

                $sfContainer = SymfonyContainer::getInstance();
                if (null !== $sfContainer) {
                    /** @var UrlGeneratorInterface $sfRouter */
                    $sfRouter = $sfContainer->get('router');

                    if (array_key_exists('sf-params', $params)) {
                        return $sfRouter->generate($params['route'], $params['sf-params'], UrlGeneratorInterface::ABSOLUTE_URL);
                    }
                    $link = $sfRouter->generate($params['route'], [], UrlGeneratorInterface::ABSOLUTE_URL);
                } else {
                    throw new \InvalidArgumentException('You can\'t use Symfony router in legacy context.');
                }

                break;
            default:
                $link = $context->link->getPageLink(
                    $params['entity'],
                    $params['ssl'],
                    $params['id_lang'],
                    $urlParameters,
                    false,
                    $params['id_shop'],
                    $params['relative_protocol']
                );

                break;
        }

        return $link;
    }

    /**
     * @param Shop $shop
     * @param int|null $idShop
     *
     * @return array<string>
     */
    private function getShopDomainActive(Shop $shop, ?int $idShop): array
    {
        $domainSSL = $shop->domain_ssl;
        $domain = $shop->domain;
        if ((Shop::getContext() === Shop::CONTEXT_ALL || Shop::getContext(
                ) === Shop::CONTEXT_GROUP) && empty($idShop)) {
            if (Shop::getContext() === Shop::CONTEXT_GROUP) {
                $allShops = Shop::getShops(true, $shop->id_shop_group);
            } else {
                $allShops = Shop::getShops();
            }
            foreach ($allShops as $shopDomain) {
                if ($shopDomain['domain_ssl'] === Tools::getHttpHost() || $shopDomain['domain'] === Tools::getHttpHost(
                    )) {
                    $domainSSL = $shopDomain['domain_ssl'];
                    $domain = $shopDomain['domain'];
                    break;
                }
            }
        }

        return [$domainSSL, $domain];
    }
}
