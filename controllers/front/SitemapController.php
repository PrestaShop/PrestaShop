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
class SitemapControllerCore extends FrontController
{
    /** @var string */
    public $php_self = 'sitemap';

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $sitemapUrls = [
            'our_offers' => [
                'name' => $this->trans('Our Offers', [], 'Shop.Theme.Global'),
                'links' => $this->getOffersLinks(),
            ],
            'categories' => [
                'name' => $this->trans('Categories', [], 'Shop.Theme.Catalog'),
                'links' => $this->getCategoriesLinks(),
            ],
            'your_account' => [
                'name' => $this->trans('Your account', [], 'Shop.Theme.Customeraccount'),
                'links' => $this->getUserAccountLinks(),
            ],
            'pages' => [
                'name' => $this->trans('Pages', [], 'Shop.Theme.Catalog'),
                'links' => $this->getPagesLinks(),
            ],
        ];

        /*
         * Allows modules to add own urls (even whole new groups) to frontend sitemap.
         * For example landing pages, blog posts and others.
         */
        Hook::exec(
            'actionModifyFrontendSitemap',
            ['urls' => &$sitemapUrls]
        );

        /*
         * Backward compatibility with older themes.
         * This should be removed as soon as possible, because $pages variable is overwriting
         * our global template variable assigned in FrontController.
         */
        $this->context->smarty->assign(
            [
                'our_offers' => !empty($sitemapUrls['our_offers']['name']) ? $sitemapUrls['our_offers']['name'] : '',
                'categories' => !empty($sitemapUrls['categories']['name']) ? $sitemapUrls['categories']['name'] : '',
                'your_account' => !empty($sitemapUrls['your_account']['name']) ? $sitemapUrls['your_account']['name'] : '',
                'pages' => !empty($sitemapUrls['pages']['name']) ? $sitemapUrls['pages']['name'] : '',
                'links' => [
                    'offers' => !empty($sitemapUrls['our_offers']['links']) ? $sitemapUrls['our_offers']['links'] : [],
                    'pages' => !empty($sitemapUrls['pages']['links']) ? $sitemapUrls['pages']['links'] : [],
                    'user_account' => !empty($sitemapUrls['your_account']['links']) ? $sitemapUrls['your_account']['links'] : [],
                    'categories' => !empty($sitemapUrls['categories']['links']) ? $sitemapUrls['categories']['links'] : [],
                ],
            ]
        );

        $this->context->smarty->assign('sitemapUrls', $sitemapUrls);
        parent::initContent();
        $this->setTemplate('cms/sitemap');
    }

    public function getCategoriesLinks()
    {
        return [Category::getRootCategory()->recurseLiteCategTree(0, 0, null, null, 'sitemap')];
    }

    /**
     * @return array
     */
    protected function getPagesLinks()
    {
        $cms = CMSCategory::getRecurseCategory($this->context->language->id, 1, 1, 1);
        $links = $this->getCmsTree($cms);

        $links[] = [
            'id' => 'stores-page',
            'label' => $this->trans('Our stores', [], 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('stores'),
        ];

        $links[] = [
            'id' => 'contact-page',
            'label' => $this->trans('Contact us', [], 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('contact'),
        ];

        $links[] = [
            'id' => 'sitemap-page',
            'label' => $this->trans('Sitemap', [], 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('sitemap'),
        ];

        return $links;
    }

    /**
     * @return array
     */
    protected function getCmsTree($cms)
    {
        $links = [];

        foreach ($cms['cms'] as $p) {
            $links[] = [
                'id' => 'cms-page-' . $p['id_cms'],
                'label' => $p['meta_title'],
                'url' => $p['link'],
            ];
        }

        if (isset($cms['children'])) {
            foreach ($cms['children'] as $c) {
                $links[] = [
                    'id' => 'cms-category-' . $c['id_cms_category'],
                    'label' => $c['name'],
                    'url' => $c['link'],
                    'children' => $this->getCmsTree($c),
                ];
            }
        }

        return $links;
    }

    /**
     * @return array
     */
    protected function getUserAccountLinks()
    {
        $links = [];

        $links[] = [
            'id' => 'login-page',
            'label' => $this->trans('Log in', [], 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('authentication'),
        ];

        $links[] = [
            'id' => 'register-page',
            'label' => $this->trans('Create new account', [], 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('registration'),
        ];

        return $links;
    }

    /**
     * @return array
     */
    protected function getOffersLinks()
    {
        $links = [
            [
                'id' => 'new-product-page',
                'label' => $this->trans('New products', [], 'Shop.Theme.Catalog'),
                'url' => $this->context->link->getPageLink('new-products'),
            ],
        ];

        if (!Configuration::isCatalogMode()) {
            if (Configuration::get('PS_DISPLAY_BEST_SELLERS')) {
                $links[] = [
                    'id' => 'best-sales-page',
                    'label' => $this->trans('Best sellers', [], 'Shop.Theme.Catalog'),
                    'url' => $this->context->link->getPageLink('best-sales'),
                ];
            }

            $links[] = [
                'id' => 'prices-drop-page',
                'label' => $this->trans('Price drop', [], 'Shop.Theme.Catalog'),
                'url' => $this->context->link->getPageLink('prices-drop'),
            ];
        }

        if (Configuration::get('PS_DISPLAY_MANUFACTURERS')) {
            $manufacturers = Manufacturer::getLiteManufacturersList($this->context->language->id, 'sitemap');
            $links[] = [
                'id' => 'manufacturer-page',
                'label' => $this->trans('Brands', [], 'Shop.Theme.Catalog'),
                'url' => $this->context->link->getPageLink('manufacturer'),
                'children' => $manufacturers,
            ];
        }

        if (Configuration::get('PS_DISPLAY_SUPPLIERS')) {
            $suppliers = Supplier::getLiteSuppliersList($this->context->language->id, 'sitemap');
            $links[] = [
                'id' => 'supplier-page',
                'label' => $this->trans('Suppliers', [], 'Shop.Theme.Catalog'),
                'url' => $this->context->link->getPageLink('supplier'),
                'children' => $suppliers,
            ];
        }

        return $links;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Sitemap', [], 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('sitemap', true),
        ];

        return $breadcrumb;
    }
}
