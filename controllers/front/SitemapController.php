<?php
/**
 * 2007-2016 PrestaShop
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class SitemapControllerCore extends FrontController
{
    public $php_self = 'sitemap';

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign(array(
            'sitemap' => $this->getTemplateVarSitemap(),
        ));

        $this->setTemplate('cms/sitemap');
    }

    public function getTemplateVarSitemap()
    {
        $pages = [];
        $catalog_mode = Configuration::isCatalogMode();

        $cms = CMSCategory::getRecurseCategory($this->context->language->id, 1, 1, 1);
        foreach ($cms['cms'] as $p) {
            $pages[] = [
                'id' => 'cms-page-'.$p['id_cms'],
                'label' => $p['meta_title'],
                'url' => $this->context->link->getCMSLink(new CMS($p['id_cms'])),
            ];
        }

        $pages[] = [
            'id' => 'stores-page',
            'label' => $this->trans('Our stores', array(), 'Shop.Theme'),
            'url' => $this->context->link->getPageLink('stores'),
        ];

        $pages[] = [
            'id' => 'contact-page',
            'label' => $this->trans('Contact us', array(), 'Shop.Theme'),
            'url' => $this->context->link->getPageLink('contact'),
        ];

        $pages[] = [
            'id' => 'sitemap-page',
            'label' => $this->trans('Sitemap', array(), 'Shop.Theme'),
            'url' => $this->context->link->getPageLink('sitemap'),
        ];

        $pages[] = [
            'id' => 'login-page',
            'label' => $this->trans('Log in', array(), 'Shop.Theme'),
            'url' => $this->context->link->getPageLink('authentication'),
        ];

        $pages[] = [
            'id' => 'register-page',
            'label' => $this->trans('Create new account', array(), 'Shop.Theme'),
            'url' => $this->context->link->getPageLink('authentication'),
        ];

        $catalog = [
            'new-product' => [
                'id' => 'new-product-page',
                'label' => $this->trans('New products', array(), 'Shop.Theme.Catalog'),
                'url' => $this->context->link->getPageLink('new-products'),
            ],
        ];

        if ($catalog_mode && Configuration::get('PS_DISPLAY_BEST_SELLERS')) {
            $catalog['best-sales'] = [
                'id' => 'best-sales-page',
                'label' => $this->trans('Best sellers', array(), 'Shop.Theme.Catalog'),
                'url' => $this->context->link->getPageLink('best-sales'),
            ];
            $catalog['prices-drop'] = [
                'id' => 'prices-drop-page',
                'label' => $this->trans('Price drop', array(), 'Shop.Theme.Catalog'),
                'url' => $this->context->link->getPageLink('prices-drop'),
            ];
        }

        if (Configuration::get('PS_DISPLAY_SUPPLIERS')) {
            $manufacturers = Manufacturer::getLiteManufacturersList($this->context->language->id, 'sitemap');
            $catalog['manufacturer'] = [
                'id' => 'manufacturer-page',
                'label' => $this->trans('Brands', array(), 'Shop.Theme.Catalog'),
                'url' => $this->context->link->getPageLink('manufacturer'),
                'children' => $manufacturers,
            ];

            $suppliers = Supplier::getLiteSuppliersList($this->context->language->id, 'sitemap');
            $catalog['supplier'] = [
                'id' => 'supplier-page',
                'label' => $this->trans('Suppliers', array(), 'Shop.Theme.Catalog'),
                'url' => $this->context->link->getPageLink('supplier'),
                'children' => $suppliers,
            ];
        }

        $categories = Category::getRootCategory()->recurseLiteCategTree(0, 0, null, null, 'sitemap');
        $catalog['category'] = [
            'id' => 'category-page',
            'label' => $this->trans('Categories', array(), 'Shop.Theme.Catalog'),
            'url' => '#',
            'children' => $categories['children'],
        ];

        $sitemap = [[
                'id' => 'page-page',
                'label' => $this->trans('Pages', array(), 'Shop.Theme'),
                'url' => '#',
                'children' => $pages,
            ],[
                'id' => 'catalog-page',
                'label' => $this->trans('Catalog', array(), 'Shop.Theme'),
                'url' => '#',
                'children' => $catalog,
            ],
        ];

        return $sitemap;
    }
}
