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

use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LinkCore
{
    /** @var bool Rewriting activation */
    public $allow;
    public $url;
    public static $cache = array('page' => array());

    public $protocol_link;
    public $protocol_content;

    public $ssl_enable;

    protected static $category_disable_rewrite = null;

    /**
     * Constructor (initialization only)
     */
    public function __construct($protocol_link = null, $protocol_content = null)
    {
        $this->allow = (int)Configuration::get('PS_REWRITING_SETTINGS');
        $this->url = $_SERVER['SCRIPT_NAME'];
        $this->protocol_link = $protocol_link;
        $this->protocol_content = $protocol_content;

        if (!defined('_PS_BASE_URL_')) {
            define('_PS_BASE_URL_', Tools::getShopDomain(true));
        }
        if (!defined('_PS_BASE_URL_SSL_')) {
            define('_PS_BASE_URL_SSL_', Tools::getShopDomainSsl(true));
        }

        if (Link::$category_disable_rewrite === null) {
            Link::$category_disable_rewrite = array(Configuration::get('PS_HOME_CATEGORY'), Configuration::get('PS_ROOT_CATEGORY'));
        }

        $this->ssl_enable = Configuration::get('PS_SSL_ENABLED');
    }

    /**
     * Create a link to delete a product
     *
     * @param mixed $product ID of the product OR a Product object
     * @param int $id_picture ID of the picture to delete
     * @return string
     */
    public function getProductDeletePictureLink($product, $id_picture)
    {
        $deletePictureLinks = Hook::exec(
            'actionGetProductDeletePictureLink',
            array(
                'product' => $product,
                'alias' => $id_picture,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($deletePictureLinks) && is_array($deletePictureLinks)) {
            foreach ($deletePictureLinks as $deletePictureLink) {
                if (!empty($deletePictureLink)) {
                    return $deletePictureLink;
                }
            }
        }
        $url = $this->getProductLink($product);
        return $url.((strpos($url, '?')) ? '&' : '?').'deletePicture='.$id_picture;
    }

    /**
     * Create a link to a product
     *
     * @param mixed $product Product object (can be an ID product, but deprecated)
     * @param string $alias
     * @param string $category
     * @param string $ean13
     * @param int $id_lang
     * @param int $id_shop (since 1.5.0) ID shop need to be used when we generate a product link for a product in a cart
     * @param int $ipa ID product attribute
     * @return string
     */
    public function getProductLink($product, $alias = null, $category = null, $ean13 = null, $id_lang = null, $id_shop = null, $ipa = 0, $force_routes = false, $relative_protocol = false, $add_anchor = false, $extra_params = [])
    {
        $dispatcher = Dispatcher::getInstance();

        $productLinks = Hook::exec(
            'actionGetProductLink',
            array(
                'product' => $product,
                'alias' => $alias,
                'category' => $category,
                'ean13' => $ean13,
                'id_lang' => $id_lang,
                'id_shop' => $id_shop,
                'ipa' => $ipa,
                'force_routes' => $force_routes,
                'relative_protocol' => $relative_protocol,
                'add_anchor' => $add_anchor,
                'extra_params' => $extra_params,
                'category_disable_rewrite' => self::$category_disable_rewrite,
                'link' => $this,
            0),
            null,
            true,
            false
        );
        if (!empty($productLinks) && is_array($productLinks)) {
            foreach ($productLinks as $productLink) {
                if (!empty($productLink)) {
                    return $productLink;
                }
            }
        }

        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($id_shop, null, $relative_protocol).$this->getLangLink($id_lang, null, $id_shop);

        if (!is_object($product)) {
            if (is_array($product) && isset($product['id_product'])) {
                $product = new Product($product['id_product'], false, $id_lang, $id_shop);
            } elseif ((int)$product) {
                $product = new Product((int)$product, false, $id_lang, $id_shop);
            } else {
                throw new PrestaShopException('Invalid product vars');
            }
        }

        // Set available keywords
        $params = array();
        $params['id'] = $product->id;
        $params['id_product_attribute'] = $ipa;
        $params['rewrite'] = (!$alias) ? $product->getFieldByLang('link_rewrite') : $alias;
        $params['ean13'] = (!$ean13) ? $product->ean13 : $ean13;
        $params['meta_keywords'] =    Tools::str2url($product->getFieldByLang('meta_keywords'));
        $params['meta_title'] = Tools::str2url($product->getFieldByLang('meta_title'));

        if ($dispatcher->hasKeyword('product_rule', $id_lang, 'manufacturer', $id_shop)) {
            $params['manufacturer'] = Tools::str2url($product->isFullyLoaded ? $product->manufacturer_name : Manufacturer::getNameById($product->id_manufacturer));
        }

        if ($dispatcher->hasKeyword('product_rule', $id_lang, 'supplier', $id_shop)) {
            $params['supplier'] = Tools::str2url($product->isFullyLoaded ? $product->supplier_name : Supplier::getNameById($product->id_supplier));
        }

        if ($dispatcher->hasKeyword('product_rule', $id_lang, 'price', $id_shop)) {
            $params['price'] = $product->isFullyLoaded ? $product->price : Product::getPriceStatic($product->id, false, null, 6, null, false, true, 1, false, null, null, null, $product->specificPrice);
        }

        if ($dispatcher->hasKeyword('product_rule', $id_lang, 'tags', $id_shop)) {
            $params['tags'] = Tools::str2url($product->getTags($id_lang));
        }

        if ($dispatcher->hasKeyword('product_rule', $id_lang, 'category', $id_shop)) {
            $params['category'] = (!is_null($product->category) && !empty($product->category)) ? Tools::str2url($product->category) : Tools::str2url($category);
        }

        if ($dispatcher->hasKeyword('product_rule', $id_lang, 'reference', $id_shop)) {
            $params['reference'] = Tools::str2url($product->reference);
        }

        if ($dispatcher->hasKeyword('product_rule', $id_lang, 'categories', $id_shop)) {
            $params['category'] = (!$category) ? $product->category : $category;
            $cats = array();
            foreach ($product->getParentCategories($id_lang) as $cat) {
                if (!in_array($cat['id_category'], Link::$category_disable_rewrite)) {
                    //remove root and home category from the URL
                    $cats[] = $cat['link_rewrite'];
                }
            }
            $params['categories'] = implode('/', $cats);
        }
        $anchor = $ipa ? $product->getAnchor((int)$ipa, (bool)$add_anchor) : '';

        return $url.$dispatcher->createUrl('product_rule', $id_lang, array_merge($params, $extra_params), $force_routes, $anchor, $id_shop);
    }

    public function getRemoveFromCartURL(
        $id_product,
        $id_product_attribute,
        $id_customization = null
    ) {
        $removeFromCartUrls = Hook::exec(
            'actionGetRemoveFromCartURL',
            array(
                'id_product' => $id_product,
                'id_product_attribute' => $id_product_attribute,
                'id_customization' => $id_customization,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($removeFromCartUrls) && is_array($removeFromCartUrls)) {
            foreach ($removeFromCartUrls as $removeFromCartUrl) {
                if (!empty($removeFromCartUrl)) {
                    return $removeFromCartUrl;
                }
            }
        }
        $params = [
            'delete' => 1,
            'id_product' => $id_product,
            'id_product_attribute' => $id_product_attribute
        ];

        if ($id_customization) {
            $params['id_customization'] = $id_customization;
        }

        return $this->getPageLink(
            'cart',
            true,
            null,
            $params,
            false
        );
    }

    public function getUpQuantityCartURL(
        $id_product,
        $id_product_attribute,
        $id_customization = null
    ) {
        $upQuantityCartUrls = Hook::exec(
            'actionGetUpQuantityCartURL',
            array(
                'id_product' => $id_product,
                'id_product_attribute' => $id_product_attribute,
                'id_customization' => $id_customization,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($upQuantityCartUrls) && is_array($upQuantityCartUrls)) {
            foreach ($upQuantityCartUrls as $up_quantity_cart_url) {
                if (!empty($up_quantity_cart_url)) {
                    return $up_quantity_cart_url;
                }
            }
        }
        $params = [
            'update' => 1,
            'op' => 'up',
            'id_product' => $id_product,
            'id_product_attribute' => $id_product_attribute,
            'token' => Tools::getToken(false)
        ];

        if ($id_customization) {
            $params['id_customization'] = $id_customization;
        }

        return $this->getPageLink(
            'cart',
            true,
            null,
            $params,
            false
        );
    }

    public function getDownQuantityCartURL(
        $id_product,
        $id_product_attribute,
        $id_customization = null
    ) {
        $downQuantityCartUrls = Hook::exec(
            'actionGetDownQuantityCartURL',
            array(
                'id_product' => $id_product,
                'id_product_attribute' => $id_product_attribute,
                'id_customization' => $id_customization,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($downQuantityCartUrls) && is_array($downQuantityCartUrls)) {
            foreach ($downQuantityCartUrls as $down_quantity_cart_url) {
                if (!empty($down_quantity_cart_url)) {
                    return $down_quantity_cart_url;
                }
            }
        }
        $params = [
            'update' => 1,
            'op' => 'down',
            'id_product' => $id_product,
            'id_product_attribute' => $id_product_attribute,
            'token' => Tools::getToken(false)
        ];

        if ($id_customization) {
            $params['id_customization'] = $id_customization;
        }

        return $this->getPageLink(
            'cart',
            true,
            null,
            $params,
            false
        );
    }

    public function getAddToCartURL($id_product, $id_product_attribute)
    {
        $addToCartUrls = Hook::exec(
            'actionGetAddToCartURL',
            array(
                'id_product' => $id_product,
                'id_product_attribute' => $id_product_attribute,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($addToCartUrls) && is_array($addToCartUrls)) {
            foreach ($addToCartUrls as $up_quantity_cart_url) {
                if (!empty($up_quantity_cart_url)) {
                    return $up_quantity_cart_url;
                }
            }
        }
        $params = [
            'add' => 1,
            'id_product' => $id_product,
            'id_product_attribute' => $id_product_attribute
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
     * Create a link to a category
     *
     * @param mixed $category Category object (can be an ID category, but deprecated)
     * @param string $alias
     * @param int $id_lang
     * @param string $selected_filters Url parameter to autocheck filters of the module blocklayered
     * @return string
     */
    public function getCategoryLink($category, $alias = null, $id_lang = null, $selected_filters = null, $id_shop = null, $relative_protocol = false)
    {
        $categoryLinks = Hook::exec(
            'actionGetCategoryLink',
            array(
                'category' => $category,
                'alias' => $alias,
                'id_lang' => $id_lang,
                'id_shop' => $id_shop,
                'selected_filters' => $selected_filters,
                'relative_protocol' => $relative_protocol,
                'category_disable_rewrite' => self::$category_disable_rewrite,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($categoryLinks) && is_array($categoryLinks)) {
            foreach ($categoryLinks as $categoryLink) {
                if (!empty($categoryLink)) {
                    return $categoryLink;
                }
            }
        }

        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($id_shop, null, $relative_protocol).$this->getLangLink($id_lang, null, $id_shop);

        if (!is_object($category)) {
            $category = new Category($category, $id_lang);
        }

        // Set available keywords
        $params = array();
        $params['id'] = $category->id;
        $params['rewrite'] = (!$alias) ? $category->link_rewrite : $alias;
        $params['meta_keywords'] =    Tools::str2url($category->getFieldByLang('meta_keywords'));
        $params['meta_title'] = Tools::str2url($category->getFieldByLang('meta_title'));

        // Selected filters is used by the module blocklayered
        $selected_filters = is_null($selected_filters) ? '' : $selected_filters;

        if (empty($selected_filters)) {
            $rule = 'category_rule';
        } else {
            $rule = 'layered_rule';
            $params['selected_filters'] = $selected_filters;
        }

        return $url.Dispatcher::getInstance()->createUrl($rule, $id_lang, $params, $this->allow, '', $id_shop);
    }

    /**
     * Create a link to a CMS category
     *
     * @param mixed $category CMSCategory object (can be an ID category, but deprecated)
     * @param string $alias
     * @param int $id_lang
     * @return string
     */
    public function getCMSCategoryLink($cms_category, $alias = null, $id_lang = null, $id_shop = null, $relative_protocol = false)
    {
        $cmsCategoryLinks = Hook::exec(
            'actionGetCMSCategoryLink',
            array(
                'cms_category' => $cms_category,
                'alias' => $alias,
                'id_lang' => $id_lang,
                'id_shop' => $id_shop,
                'relative_protocol' => $relative_protocol,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($cmsCategoryLinks) && is_array($cmsCategoryLinks)) {
            foreach ($cmsCategoryLinks as $cmsCategoryLink) {
                if (!empty($cmsCategoryLink)) {
                    return $cmsCategoryLink;
                }
            }
        }

        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($id_shop, null, $relative_protocol).$this->getLangLink($id_lang, null, $id_shop);

        $dispatcher = Dispatcher::getInstance();
        if (!is_object($cms_category)) {
            if ($alias !== null && !$dispatcher->hasKeyword('cms_category_rule', $id_lang, 'meta_keywords', $id_shop) && !$dispatcher->hasKeyword('cms_category_rule', $id_lang, 'meta_title', $id_shop)) {
                return $url.$dispatcher->createUrl('cms_category_rule', $id_lang, array('id' => (int)$cms_category, 'rewrite' => (string)$alias), $this->allow, '', $id_shop);
            }
            $cms_category = new CMSCategory($cms_category, $id_lang);
        }
        if (is_array($cms_category->link_rewrite) && isset($cms_category->link_rewrite[(int)$id_lang])) {
            $cms_category->link_rewrite = $cms_category->link_rewrite[(int)$id_lang];
        }
        if (is_array($cms_category->meta_keywords) && isset($cms_category->meta_keywords[(int)$id_lang])) {
            $cms_category->meta_keywords = $cms_category->meta_keywords[(int)$id_lang];
        }
        if (is_array($cms_category->meta_title) && isset($cms_category->meta_title[(int)$id_lang])) {
            $cms_category->meta_title = $cms_category->meta_title[(int)$id_lang];
        }

        // Set available keywords
        $params = array();
        $params['id'] = $cms_category->id;
        $params['rewrite'] = (!$alias) ? $cms_category->link_rewrite : $alias;
        $params['meta_keywords'] = Tools::str2url($cms_category->meta_keywords);
        $params['meta_title'] = Tools::str2url($cms_category->meta_title);

        return $url.$dispatcher->createUrl('cms_category_rule', $id_lang, $params, $this->allow, '', $id_shop);
    }

    /**
     * Create a link to a CMS page
     *
     * @param mixed $cms CMS object (can be an ID CMS, but deprecated)
     * @param string $alias
     * @param bool $ssl
     * @param int $id_lang
     * @return string
     */
    public function getCMSLink($cms, $alias = null, $ssl = null, $id_lang = null, $id_shop = null, $relative_protocol = false)
    {
        $cmsLinks = Hook::exec(
            'actionGetCMSLink',
            array(
                'cms' => $cms,
                'alias' => $alias,
                'ssl' => $ssl,
                'id_lang' => $id_lang,
                'id_shop' => $id_shop,
                'relative_protocol' => $relative_protocol,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($cmsLinks) && is_array($cmsLinks)) {
            foreach ($cmsLinks as $cmsLink) {
                if (!empty($cmsLink)) {
                    return $cmsLink;
                }
            }
        }

        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($id_shop, $ssl, $relative_protocol).$this->getLangLink($id_lang, null, $id_shop);

        $dispatcher = Dispatcher::getInstance();
        if (!is_object($cms)) {
            if ($alias !== null && !$dispatcher->hasKeyword('cms_rule', $id_lang, 'meta_keywords', $id_shop) && !$dispatcher->hasKeyword('cms_rule', $id_lang, 'meta_title', $id_shop)) {
                return $url.$dispatcher->createUrl('cms_rule', $id_lang, array('id' => (int)$cms, 'rewrite' => (string)$alias), $this->allow, '', $id_shop);
            }
            $cms = new CMS($cms, $id_lang);
        }

        // Set available keywords
        $params = array();
        $params['id'] = $cms->id;
        $params['rewrite'] = (!$alias) ? (is_array($cms->link_rewrite) ? $cms->link_rewrite[(int)$id_lang] : $cms->link_rewrite) : $alias;

        $params['meta_keywords'] = '';
        if (isset($cms->meta_keywords) && !empty($cms->meta_keywords)) {
            $params['meta_keywords'] = is_array($cms->meta_keywords) ?  Tools::str2url($cms->meta_keywords[(int)$id_lang]) :  Tools::str2url($cms->meta_keywords);
        }

        $params['meta_title'] = '';
        if (isset($cms->meta_title) && !empty($cms->meta_title)) {
            $params['meta_title'] = is_array($cms->meta_title) ? Tools::str2url($cms->meta_title[(int)$id_lang]) : Tools::str2url($cms->meta_title);
        }

        return $url.$dispatcher->createUrl('cms_rule', $id_lang, $params, $this->allow, '', $id_shop);
    }

    /**
     * Create a link to a supplier
     *
     * @param mixed $supplier Supplier object (can be an ID supplier, but deprecated)
     * @param string $alias
     * @param int $id_lang
     * @return string
     */
    public function getSupplierLink($supplier, $alias = null, $id_lang = null, $id_shop = null, $relative_protocol = false)
    {
        $supplierLinks = Hook::exec(
            'actionGetSupplierLink',
            array(
                'supplier' => $supplier,
                'alias' => $alias,
                'id_lang' => $id_lang,
                'id_shop' => $id_shop,
                'relative_protocol' => $relative_protocol,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($supplierLinks) && is_array($supplierLinks)) {
            foreach ($supplierLinks as $supplierLink) {
                if (!empty($supplierLink)) {
                    return $supplierLink;
                }
            }
        }

        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($id_shop, null, $relative_protocol).$this->getLangLink($id_lang, null, $id_shop);

        $dispatcher = Dispatcher::getInstance();
        if (!is_object($supplier)) {
            if ($alias !== null && !$dispatcher->hasKeyword('supplier_rule', $id_lang, 'meta_keywords', $id_shop) && !$dispatcher->hasKeyword('supplier_rule', $id_lang, 'meta_title', $id_shop)) {
                return $url.$dispatcher->createUrl('supplier_rule', $id_lang, array('id' => (int)$supplier, 'rewrite' => (string)$alias), $this->allow, '', $id_shop);
            }
            $supplier = new Supplier($supplier, $id_lang);
        }

        // Set available keywords
        $params = array();
        $params['id'] = $supplier->id;
        $params['rewrite'] = (!$alias) ? $supplier->link_rewrite : $alias;
        $params['meta_keywords'] =    Tools::str2url($supplier->meta_keywords);
        $params['meta_title'] = Tools::str2url($supplier->meta_title);

        return $url.$dispatcher->createUrl('supplier_rule', $id_lang, $params, $this->allow, '', $id_shop);
    }

    /**
     * Create a link to a manufacturer
     *
     * @param mixed $manufacturer Manufacturer object (can be an ID supplier, but deprecated)
     * @param string $alias
     * @param int $id_lang
     * @return string
     */
    public function getManufacturerLink($manufacturer, $alias = null, $id_lang = null, $id_shop = null, $relative_protocol = false)
    {
        $manufacturerLinks = Hook::exec(
            'actionGetManufacturerLink',
            array(
                'manufacturer' => $manufacturer,
                'alias' => $alias,
                'id_lang' => $id_lang,
                'id_shop' => $id_shop,
                'relative_protocol' => $relative_protocol,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($manufacturerLinks) && is_array($manufacturerLinks)) {
            foreach ($manufacturerLinks as $manufacturerLink) {
                if (!empty($manufacturerLink)) {
                    return $manufacturerLink;
                }
            }
        }

        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($id_shop, null, $relative_protocol).$this->getLangLink($id_lang, null, $id_shop);

        $dispatcher = Dispatcher::getInstance();
        if (!is_object($manufacturer)) {
            if ($alias !== null && !$dispatcher->hasKeyword('manufacturer_rule', $id_lang, 'meta_keywords', $id_shop) && !$dispatcher->hasKeyword('manufacturer_rule', $id_lang, 'meta_title', $id_shop)) {
                return $url.$dispatcher->createUrl('manufacturer_rule', $id_lang, array('id' => (int)$manufacturer, 'rewrite' => (string)$alias), $this->allow, '', $id_shop);
            }
            $manufacturer = new Manufacturer($manufacturer, $id_lang);
        }

        // Set available keywords
        $params = array();
        $params['id'] = $manufacturer->id;
        $params['rewrite'] = (!$alias) ? $manufacturer->link_rewrite : $alias;
        $params['meta_keywords'] =    Tools::str2url($manufacturer->meta_keywords);
        $params['meta_title'] = Tools::str2url($manufacturer->meta_title);

        return $url.$dispatcher->createUrl('manufacturer_rule', $id_lang, $params, $this->allow, '', $id_shop);
    }

    /**
     * Create a link to a module
     *
     * @since 1.5.0
     * @param string $module Module name
     * @param string $process Action name
     * @param int $id_lang
     * @return string
     */
    public function getModuleLink($module, $controller = 'default', array $params = array(), $ssl = null, $id_lang = null, $id_shop = null, $relative_protocol = false)
    {
        $moduleLinks = Hook::exec(
            'actionGetModuleLink',
            array(
                'module' => $module,
                'controller' => $controller,
                'params' => $params,
                'ssl' => $ssl,
                'id_lang' => $id_lang,
                'id_shop' => $id_shop,
                'relative_protocol' => $relative_protocol,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($moduleLinks) && is_array($moduleLinks)) {
            foreach ($moduleLinks as $moduleLink) {
                if (!empty($moduleLink)) {
                    return $moduleLink;
                }
            }
        }

        if (!$id_lang) {
            $id_lang = Context::getContext()->language->id;
        }

        $url = $this->getBaseLink($id_shop, $ssl, $relative_protocol).$this->getLangLink($id_lang, null, $id_shop);

        // Set available keywords
        $params['module'] = $module;
        $params['controller'] = $controller ? $controller : 'default';

        // If the module has its own route ... just use it !
        if (Dispatcher::getInstance()->hasRoute('module-'.$module.'-'.$controller, $id_lang, $id_shop)) {
            return $this->getPageLink('module-'.$module.'-'.$controller, $ssl, $id_lang, $params);
        } else {
            return $url.Dispatcher::getInstance()->createUrl('module', $id_lang, $params, $this->allow, '', $id_shop);
        }
    }

    /**
     * Use controller name to create a link
     *
     * @param string $controller
     * @param bool $with_token include or not the token in the url
     * @param array(string) $sfRouteParams Optional parameters to use into New architecture specific cases. If these specific cases should redirect to legacy URLs, then this parameter is used to complete GET query string.
     *
     * @return string url
     */
    public function getAdminLink($controller, $with_token = true, $sfRouteParams = array())
    {
        // Cannot generate admin link from front
        if (!defined('_PS_ADMIN_DIR_')) {
            return '';
        }

        $adminLinks = Hook::exec(
            'actionGetAdminLink',
            array(
                'product' => $controller,
                'with_token' => $with_token,
                'sfRouteParams' => $sfRouteParams,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($adminLinks) && is_array($adminLinks)) {
            foreach ($adminLinks as $adminLink) {
                if (!empty($adminLink)) {
                    return $adminLink;
                }
            }
        }

        $params = $with_token ? array('token' => Tools::getAdminTokenLite($controller)) : array();

        // Even if URL rewriting is not enabled, the page handled by Symfony must work !
        // For that, we add an 'index.php' in the URL before the route
        global $kernel; // sf kernel
        if ($kernel instanceof Symfony\Component\HttpKernel\HttpKernelInterface) {
            $sfRouter = $kernel->getContainer()->get('router');
        }

        switch ($controller) {
            case 'AdminProducts':
                // New architecture modification: temporary behavior to switch between old and new controllers.
                $pagePreference = $kernel->getContainer()->get('prestashop.core.admin.page_preference_interface');
                $redirectLegacy = $pagePreference->getTemporaryShouldUseLegacyPage('product');
                if (!$redirectLegacy) {
                    if (array_key_exists('id_product', $sfRouteParams)) {
                        if (array_key_exists('deleteproduct', $sfRouteParams)) {
                            return $sfRouter->generate('admin_product_unit_action',
                                array('action' => 'delete', 'id' => $sfRouteParams['id_product'])
                            );
                        }
                        //default: if (array_key_exists('updateproduct', $sfRouteParams))
                        return $sfRouter->generate('admin_product_form',
                            array('id' => $sfRouteParams['id_product'])
                        );
                    }
                    if (array_key_exists('submitFilterproduct', $sfRouteParams)) {
                        $routeParams = array();
                        if (array_key_exists('filter_column_sav_quantity', $sfRouteParams)) {
                            $routeParams['quantity'] = $sfRouteParams['filter_column_sav_quantity'];
                        }
                        if (array_key_exists('filter_column_active', $sfRouteParams)) {
                            $routeParams['active'] = $sfRouteParams['filter_column_active'];
                        }
                        return $sfRouter->generate('admin_product_catalog_filters', $routeParams);
                    }
                    return $sfRouter->generate('admin_product_catalog');
                } else {
                    $params = array_merge($params, $sfRouteParams);
                }
                break;
            case 'AdminModulesSf':
                // New architecture modification: temporary behavior to switch between old and new controllers.
                return $sfRouter->generate('admin_module_catalog', array(), UrlGeneratorInterface::ABSOLUTE_URL);
        }

        $id_lang = Context::getContext()->language->id;

        return $this->getBaseLink().basename(_PS_ADMIN_DIR_).'/'.Dispatcher::getInstance()->createUrl($controller, $id_lang, $params, false);
    }

    /**
     * Returns a link to a product image for display
     * Note: the new image filesystem stores product images in subdirectories of img/p/
     *
     * @param string $name rewrite link of the image
     * @param string $ids id part of the image filename - can be "id_product-id_image" (legacy support, recommended) or "id_image" (new)
     * @param string $type
     */
    public function getImageLink($name, $ids, $type = null)
    {
        $not_default = false;
        $moduleManagerBuilder = new ModuleManagerBuilder();
        $moduleManager = $moduleManagerBuilder->build();

        $imageLinks = Hook::exec(
            'actionGetImageLink',
            array(
                'name' => $name,
                'ids' => $ids,
                'type' => $type,
                'module_manager' => $moduleManager,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($imageLinks) && is_array($imageLinks)) {
            foreach ($imageLinks as $imageLink) {
                if (!empty($imageLink)) {
                    return $imageLink;
                }
            }
        }

        // Check if module is installed, enabled, customer is logged in and watermark logged option is on
        if (Configuration::get('WATERMARK_LOGGED') && ($moduleManager->isInstalled('watermark') && $moduleManager->isEnabled('watermark')) && isset(Context::getContext()->customer->id)) {
            $type .= '-'.Configuration::get('WATERMARK_HASH');
        }

        // legacy mode or default image
        $theme = ((Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.$ids.($type ? '-'.$type : '').'-'.Context::getContext()->shop->theme_name.'.jpg')) ? '-'.Context::getContext()->shop->theme_name : '');
        if ((Configuration::get('PS_LEGACY_IMAGES')
            && (file_exists(_PS_PROD_IMG_DIR_.$ids.($type ? '-'.$type : '').$theme.'.jpg')))
            || ($not_default = strpos($ids, 'default') !== false)) {
            if ($this->allow == 1 && !$not_default) {
                $uri_path = __PS_BASE_URI__.$ids.($type ? '-'.$type : '').$theme.'/'.$name.'.jpg';
            } else {
                $uri_path = _THEME_PROD_DIR_.$ids.($type ? '-'.$type : '').$theme.'.jpg';
            }
        } else {
            // if ids if of the form id_product-id_image, we want to extract the id_image part
            $split_ids = explode('-', $ids);
            $id_image = (isset($split_ids[1]) ? $split_ids[1] : $split_ids[0]);
            $theme = ((Shop::isFeatureActive() && file_exists(_PS_PROD_IMG_DIR_.Image::getImgFolderStatic($id_image).$id_image.($type ? '-'.$type : '').'-'.(int)Context::getContext()->shop->theme_name.'.jpg')) ? '-'.Context::getContext()->shop->theme_name : '');
            if ($this->allow == 1) {
                $uri_path = __PS_BASE_URI__.$id_image.($type ? '-'.$type : '').$theme.'/'.$name.'.jpg';
            } else {
                $uri_path = _THEME_PROD_DIR_.Image::getImgFolderStatic($id_image).$id_image.($type ? '-'.$type : '').$theme.'.jpg';
            }
        }

        return $this->protocol_content.Tools::getMediaServer($uri_path).$uri_path;
    }

    public function getMediaLink($filepath)
    {
        $mediaLinks = Hook::exec(
            'actionGetMediaLink',
            array(
                'product' => $filepath,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($mediaLinks) && is_array($mediaLinks)) {
            foreach ($mediaLinks as $mediaLink) {
                if (!empty($mediaLink)) {
                    return $mediaLink;
                }
            }
        }

        return $this->protocol_content.Tools::getMediaServer($filepath).$filepath;
    }

    /**
     * Create a simple link
     *
     * @param string $controller
     * @param bool $ssl
     * @param int $id_lang
     * @param string|array $request
     * @param bool $request_url_encode Use URL encode
     *
     * @return string Page link
     */
    public function getPageLink($controller, $ssl = null, $id_lang = null, $request = null, $request_url_encode = false, $id_shop = null, $relative_protocol = false)
    {
        $pageLinks = Hook::exec(
            'actionGetPageLink',
            array(
                'controller' => $controller,
                'ssl' => $ssl,
                'id_lang' => $id_lang,
                'request' => $request,
                'request_url_encode' => $request_url_encode,
                'id_shop' => $id_shop,
                'relative_protocol' => $relative_protocol,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($pageLinks) && is_array($pageLinks)) {
            foreach ($pageLinks as $pageLink) {
                if (!empty($pageLink)) {
                    return $pageLink;
                }
            }
        }

        //If $controller contains '&' char, it means that $controller contains request data and must be parsed first
        $p = strpos($controller, '&');
        if ($p !== false) {
            $request = substr($controller, $p + 1);
            $request_url_encode = false;
            $controller = substr($controller, 0, $p);
        }

        $controller = Tools::strReplaceFirst('.php', '', $controller);
        if (!$id_lang) {
            $id_lang = (int)Context::getContext()->language->id;
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
            // @FIXME html_entity_decode has been added due to '&amp;' => '%3B' ...
            $request = html_entity_decode($request);
            if ($request_url_encode) {
                $request = urlencode($request);
            }
            parse_str($request, $request);
        }

        if ($controller === 'cart' && (!empty($request['add']) || !empty($request['delete'])) && Configuration::get('PS_TOKEN_ENABLE')) {
            $request['token'] = Tools::getToken(false);
        }

        $uri_path = Dispatcher::getInstance()->createUrl($controller, $id_lang, $request, false, '', $id_shop);

        return $this->getBaseLink($id_shop, $ssl, $relative_protocol).$this->getLangLink($id_lang, null, $id_shop).ltrim($uri_path, '/');
    }

    public function getCatImageLink($name, $id_category, $type = null)
    {
        $catImageLinks = Hook::exec(
            'actionGetCatImageLink',
            array(
                'name' => $name,
                'id_category' => $id_category,
                'type' => $type,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($catImageLinks) && is_array($catImageLinks)) {
            foreach ($catImageLinks as $catImageLink) {
                if (!empty($catImageLink)) {
                    return $catImageLink;
                }
            }
        }

        if ($this->allow == 1 && $type) {
            $uri_path = __PS_BASE_URI__.'c/'.$id_category.'-'.$type.'/'.$name.'.jpg';
        } else {
            $uri_path = _THEME_CAT_DIR_.$id_category.($type ? '-'.$type : '').'.jpg';
        }
        return $this->protocol_content.Tools::getMediaServer($uri_path).$uri_path;
    }

    /**
     * Create link after language change, for the change language block
     *
     * @param int $id_lang Language ID
     * @return string link
     */
    public function getLanguageLink($id_lang, Context $context = null)
    {
        $languageLinks = Hook::exec(
            'actionGetLanguageLink',
            array(
                'id_lang' => $id_lang,
                'context' => $context,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($languageLinks) && is_array($languageLinks)) {
            foreach ($languageLinks as $languageLink) {
                if (!empty($languageLink)) {
                    return $languageLink;
                }
            }
        }

        if (!$context) {
            $context = Context::getContext();
        }

        $params = $_GET;
        unset($params['isolang'], $params['controller']);

        if (!$this->allow) {
            $params['id_lang'] = $id_lang;
        } else {
            unset($params['id_lang']);
        }

        $controller = Dispatcher::getInstance()->getController();

        if (!empty($context->controller->php_self)) {
            $controller = $context->controller->php_self;
        }

        if ($controller == 'product' && isset($params['id_product'])) {
            return $this->getProductLink((int)$params['id_product'], null, null, null, (int)$id_lang);
        } elseif ($controller == 'category' && isset($params['id_category'])) {
            return $this->getCategoryLink((int)$params['id_category'], null, (int)$id_lang);
        } elseif ($controller == 'supplier' && isset($params['id_supplier'])) {
            return $this->getSupplierLink((int)$params['id_supplier'], null, (int)$id_lang);
        } elseif ($controller == 'manufacturer' && isset($params['id_manufacturer'])) {
            return $this->getManufacturerLink((int)$params['id_manufacturer'], null, (int)$id_lang);
        } elseif ($controller == 'cms' && isset($params['id_cms'])) {
            return $this->getCMSLink((int)$params['id_cms'], null, null, (int)$id_lang);
        } elseif ($controller == 'cms' && isset($params['id_cms_category'])) {
            return $this->getCMSCategoryLink((int)$params['id_cms_category'], null, (int)$id_lang);
        } elseif (isset($params['fc']) && $params['fc'] == 'module') {
            $module = Validate::isModuleName(Tools::getValue('module')) ? Tools::getValue('module') : '';
            if (!empty($module)) {
                unset($params['fc'], $params['module']);
                return $this->getModuleLink($module, $controller, $params, null, (int)$id_lang);
            }
        }

        return $this->getPageLink($controller, null, $id_lang, $params);
    }

    public function goPage($url, $p)
    {
        $url = rtrim(str_replace('?&', '?', $url), '?');
        return $url.($p == 1 ? '' : (!strstr($url, '?') ? '?' : '&').'p='.(int)$p);
    }

    /**
     * Get pagination link
     *
     * @param string $type Controller name
     * @param int $id_object
     * @param bool $nb Show nb element per page attribute
     * @param bool $sort Show sort attribute
     * @param bool $pagination Show page number attribute
     * @param bool $array If false return an url, if true return an array
     */
    public function getPaginationLink($type, $id_object, $nb = false, $sort = false, $pagination = false, $array = false)
    {
        $paginationLinks = Hook::exec(
            'actionGetPaginationLink',
            array(
                'type' => $type,
                'id_object' => $id_object,
                'nb' => $nb,
                'sort' => $sort,
                'pagination' => $pagination,
                'array' => $array,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($paginationLinks) && is_array($paginationLinks)) {
            foreach ($paginationLinks as $paginationLink) {
                if (!empty($paginationLink)) {
                    return $paginationLink;
                }
            }
        }

        // If no parameter $type, try to get it by using the controller name
        if (!$type && !$id_object) {
            $method_name = 'get'.Dispatcher::getInstance()->getController().'Link';
            if (method_exists($this, $method_name) && isset($_GET['id_'.Dispatcher::getInstance()->getController()])) {
                $type = Dispatcher::getInstance()->getController();
                $id_object = $_GET['id_'.$type];
            }
        }

        if ($type && $id_object) {
            $url = $this->{'get'.$type.'Link'}($id_object, null);
        } else {
            if (isset(Context::getContext()->controller->php_self)) {
                $name = Context::getContext()->controller->php_self;
            } else {
                $name = Dispatcher::getInstance()->getController();
            }
            $url = $this->getPageLink($name);
        }

        $vars = array();
        $vars_nb = array('n');
        $vars_sort = array('orderby', 'orderway');
        $vars_pagination = array('p');

        foreach ($_GET as $k => $value) {
            if ($k != 'id_'.$type && $k != 'controller') {
                if (Configuration::get('PS_REWRITING_SETTINGS') && ($k == 'isolang' || $k == 'id_lang')) {
                    continue;
                }
                $if_nb = (!$nb || ($nb && !in_array($k, $vars_nb)));
                $if_sort = (!$sort || ($sort && !in_array($k, $vars_sort)));
                $if_pagination = (!$pagination || ($pagination && !in_array($k, $vars_pagination)));
                if ($if_nb && $if_sort && $if_pagination) {
                    if (!is_array($value)) {
                        $vars[urlencode($k)] = $value;
                    } else {
                        foreach (explode('&', http_build_query(array($k => $value), '', '&')) as $key => $val) {
                            $data = explode('=', $val);
                            $vars[urldecode($data[0])] = $data[1];
                        }
                    }
                }
            }
        }

        if (!$array) {
            if (count($vars)) {
                return $url.(!strstr($url, '?') && ($this->allow == 1 || $url == $this->url) ? '?' : '&').http_build_query($vars, '', '&');
            } else {
                return $url;
            }
        }

        $vars['requestUrl'] = $url;

        if ($type && $id_object) {
            $vars['id_'.$type] = (is_object($id_object) ? (int)$id_object->id : (int)$id_object);
        }

        if (!$this->allow == 1) {
            $vars['controller'] = Dispatcher::getInstance()->getController();
        }
        return $vars;
    }

    public function addSortDetails($url, $orderby, $orderway)
    {
        return $url.(!strstr($url, '?') ? '?' : '&').'orderby='.urlencode($orderby).'&orderway='.urlencode($orderway);
    }

    public function getLangLink($id_lang = null, Context $context = null, $id_shop = null)
    {
        $langLinks = Hook::exec(
            'actionGetLangLink',
            array(
                'id_lang' => $id_lang,
                'context' => $context,
                'id_shop' => $id_shop,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($langLinks) && is_array($langLinks)) {
            foreach ($langLinks as $langLink) {
                if (!empty($langLink)) {
                    return $langLink;
                }
            }
        }

        if (!$context) {
            $context = Context::getContext();
        }

        if ((!$this->allow && in_array($id_shop, array($context->shop->id,  null))) || !Language::isMultiLanguageActivated($id_shop) || !(int)Configuration::get('PS_REWRITING_SETTINGS', null, null, $id_shop)) {
            return '';
        }

        if (!$id_lang) {
            $id_lang = $context->language->id;
        }

        return Language::getIsoById($id_lang).'/';
    }

    public function getBaseLink($id_shop = null, $ssl = null, $relative_protocol = false)
    {
        $base_links = Hook::exec(
            'actionGetBaseLink',
            array(
                'id_shop' => $id_shop,
                'ssl' => $ssl,
                'relative_protocol' => $relative_protocol,
                'link' => $this,
            ),
            null,
            true,
            false
        );
        if (!empty($base_links) && is_array($base_links)) {
            foreach ($base_links as $base_link) {
                if (!empty($base_link)) {
                    return $base_link;
                }
            }
        }

        static $force_ssl = null;

        if ($ssl === null) {
            if ($force_ssl === null) {
                $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            }
            $ssl = $force_ssl;
        }

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $id_shop !== null) {
            $shop = new Shop($id_shop);
        } else {
            $shop = Context::getContext()->shop;
        }

        if ($relative_protocol) {
            $base = '//'.($ssl && $this->ssl_enable ? $shop->domain_ssl : $shop->domain);
        } else {
            $base = (($ssl && $this->ssl_enable) ? 'https://'.$shop->domain_ssl : 'http://'.$shop->domain);
        }

        return $base.$shop->getBaseURI();
    }

    public static function getQuickLink($url)
    {
        $quickLinks = Hook::exec(
            'actionGetQuickLink',
            array(
                'url' => $url,
            ),
            null,
            true,
            false
        );
        if (!empty($quickLinks) && is_array($quickLinks)) {
            foreach ($quickLinks as $quickLink) {
                if (!empty($quickLink)) {
                    return $quickLink;
                }
            }
        }

        // We need to know if we are in Legacy or SF environment
        if (Tools::getIsset('token')) {
            $parsedUrl = parse_url($url);
            $output = array();
            if (is_array($parsedUrl) && isset($parsedUrl['query'])) {
                parse_str($parsedUrl['query'], $output);
                unset($output['token'], $output['conf'], $output['id_quick_access']);
            }
            return 'index.php?'.http_build_query($output);
        }

        return str_replace('/'.basename(_PS_ADMIN_DIR_).'/', '', $url);
    }

    public function matchQuickLink($url)
    {
        $quicklink = $this->getQuickLink($url);
        if (isset($quicklink) && $quicklink === ($this->getQuickLink($_SERVER['REQUEST_URI']))) {
            return true;
        } else {
            return false;
        }
    }

    public static function getUrlSmarty($params, &$smarty)
    {
        $smartyLinks = Hook::exec(
            'actionGetUrlSmarty',
            array(
                'params' => $params,
                'smarty' => &$smarty,
            ),
            null,
            true,
            false
        );
        if (!empty($smartyLinks) && is_array($smartyLinks)) {
            foreach ($smartyLinks as $smartyLink) {
                if (!empty($smartyLink)) {
                    return $smartyLink;
                }
            }
        }

        $context = Context::getContext();

        if (!isset($params['params'])) {
            $params['params'] = [];
        }

        if (isset($params['id'])) {
            $entity = str_replace('-', '_', $params['entity']);
            $id = ['id_'.$entity => $params['id']];
            $params['params'] = array_merge($id, $params['params']);
        }

        $default = [
            'id_lang' => $context->language->id,
            'id_shop' => null,
            'alias' => null,
            'ssl' => null,
            'relative_protocol' => false,
        ];
        $params = array_merge($default, $params);

        $url_parameters = http_build_query($params['params']);

        switch ($params['entity']) {
            case "category":
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
            case "cms":
                $link = $context->link->getCMSLink(
                    new CMS($params['id'], $params['id_lang']),
                    $params['alias'],
                    $params['ssl'],
                    $params['id_lang'],
                    $params['id_shop'],
                    $params['relative_protocol']
                );
                break;
            case "module":
                $params = array_merge([
                    'selected_filters' => null,
                    'params' => array(),
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
            default:
                $link = $context->link->getPageLink(
                    $params['entity'],
                    $params['ssl'],
                    $params['id_lang'],
                    $url_parameters,
                    false,
                    $params['id_shop'],
                    $params['relative_protocol']
                );
                break;
        }

        return $link;
    }
}
