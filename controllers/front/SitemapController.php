<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
class SitemapControllerCore extends FrontController
{
    public $php_self = 'sitemap';

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $this->context->smarty->assign(
            array(
                'our_offers' => $this->trans('Our Offers', array(), 'Shop.Theme.Global'),
                'categories' => $this->trans('Categories', array(), 'Shop.Theme.Catalog'),
                'your_account' => $this->trans('Your account', array(), 'Shop.Theme.Customeraccount'),
                'pages' => $this->trans('Pages', array(), 'Shop.Theme.Catalog'),
                'links' => array(
                    'offers' => $this->getOffersLinks(),
                    'pages' => $this->getPagesLinks(),
                    'user_account' => $this->getUserAccountLinks(),
                    'categories' => $this->getCategoriesLinks(),
                ),
            )
        );

        parent::initContent();
        $this->setTemplate('cms/sitemap');
    }

    public function getCategoriesLinks()
    {
        return array(Category::getRootCategory()->recurseLiteCategTree(0, 0, null, null, 'sitemap'));
    }

    /**
     * @return array
     */
    protected function getPagesLinks()
    {
        $cms = CMSCategory::getRecurseCategory($this->context->language->id, 1, 1, 1);
        $links = $this->getCmsTree($cms);

        $links[] = array(
            'id' => 'stores-page',
            'label' => $this->trans('Our stores', array(), 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('stores'),
        );

        $links[] = array(
            'id' => 'contact-page',
            'label' => $this->trans('Contact us', array(), 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('contact'),
        );

        $links[] = array(
            'id' => 'sitemap-page',
            'label' => $this->trans('Sitemap', array(), 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('sitemap'),
        );

        return $links;
    }

    /**
     * @return array
     */
    protected function getCmsTree($cms)
    {
        $links = array();

        foreach ($cms['cms'] as $p) {
            $links[] = array(
                'id' => 'cms-page-' . $p['id_cms'],
                'label' => $p['meta_title'],
                'url' => $p['link'],
            );
        }

        if (isset($cms['children'])) {
            foreach ($cms['children'] as $c) {
                $links[] = array(
                    'id' => 'cms-category-' . $c['id_cms_category'],
                    'label' => $c['name'],
                    'url' => $c['link'],
                    'children' => $this->getCmsTree($c),
                );
            }
        }

        return $links;
    }

    /**
     * @return array
     */
    protected function getUserAccountLinks()
    {
        $links = array();

        $links[] = array(
            'id' => 'login-page',
            'label' => $this->trans('Log in', array(), 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('authentication'),
        );

        $links[] = array(
            'id' => 'register-page',
            'label' => $this->trans('Create new account', array(), 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('authentication'),
        );

        return $links;
    }

    /**
     * @return array
     */
    protected function getOffersLinks()
    {
        $links = array(
            array(
                'id' => 'new-product-page',
                'label' => $this->trans('New products', array(), 'Shop.Theme.Catalog'),
                'url' => $this->context->link->getPageLink('new-products'),
            ),
        );

        if (Configuration::isCatalogMode() && Configuration::get('PS_DISPLAY_BEST_SELLERS')) {
            $links[] = array(
                'id' => 'best-sales-page',
                'label' => $this->trans('Best sellers', array(), 'Shop.Theme.Catalog'),
                'url' => $this->context->link->getPageLink('best-sales'),
            );
            $links[] = array(
                'id' => 'prices-drop-page',
                'label' => $this->trans('Price drop', array(), 'Shop.Theme.Catalog'),
                'url' => $this->context->link->getPageLink('prices-drop'),
            );
        }

        if (Configuration::get('PS_DISPLAY_SUPPLIERS')) {
            $manufacturers = Manufacturer::getLiteManufacturersList($this->context->language->id, 'sitemap');
            $links[] = array(
                'id' => 'manufacturer-page',
                'label' => $this->trans('Brands', array(), 'Shop.Theme.Catalog'),
                'url' => $this->context->link->getPageLink('manufacturer'),
                'children' => $manufacturers,
            );

            $suppliers = Supplier::getLiteSuppliersList($this->context->language->id, 'sitemap');
            $links[] = array(
                'id' => 'supplier-page',
                'label' => $this->trans('Suppliers', array(), 'Shop.Theme.Catalog'),
                'url' => $this->context->link->getPageLink('supplier'),
                'children' => $suppliers,
            );
        }

        return $links;
    }
}
