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

        $this->setTemplate('cms/sitemap.tpl');
    }

    public function getTemplateVarSitemap()
    {
        $pages = [];
        $catalog_mode = Configuration::get('PS_CATALOG_MODE');

        $cms = CMSCategory::getRecurseCategory($this->context->language->id, 1, 1, 1);
        foreach ($cms['cms'] as $p) {
            $pages[] = [
                'id' => 'cms-page-'.$p['id_cms'],
                'label' => $p['meta_title'],
                'url' => $this->context->link->getCMSLink(new CMS($p['id_cms'])),
            ];
        }

        if (Configuration::get('PS_STORES_DISPLAY_SITEMAP')) {
            $pages[] = [
                'id' => 'stores-page',
                'label' => $this->l('Our stores'),
                'url' => $this->context->link->getPageLink('stores'),
            ];
        }

        $pages[] = [
            'id' => 'contact-page',
            'label' => $this->l('Contact us'),
            'url' => $this->context->link->getPageLink('contact'),
        ];

        $pages[] = [
            'id' => 'sitemap-page',
            'label' => $this->l('Sitemap'),
            'url' => $this->context->link->getPageLink('sitemap'),
        ];

        $pages[] = [
            'id' => 'login-page',
            'label' => $this->l('Log in'),
            'url' => $this->context->link->getPageLink('authentication'),
        ];

        $pages[] = [
            'id' => 'register-page',
            'label' => $this->l('Create new account'),
            'url' => $this->context->link->getPageLink('authentication'),
        ];

        $catalog = [
            'new-product' => [
                'id' => 'new-product-page',
                'label' => $this->l('New products'),
                'url' => $this->context->link->getPageLink('new-products'),
            ],
        ];

        if ($catalog_mode && Configuration::get('PS_DISPLAY_BEST_SELLERS')) {
            $catalog['best-sales'] = [
                'id' => 'best-sales-page',
                'label' => $this->l('Best sellers'),
                'url' => $this->context->link->getPageLink('best-sales'),
            ];
            $catalog['prices-drop'] = [
                'id' => 'prices-drop-page',
                'label' => $this->l('Price drop'),
                'url' => $this->context->link->getPageLink('prices-drop'),
            ];
        }

        $catalog['manufacturer'] = [
            'id' => 'manufacturer-page',
            'label' => $this->l('Manufacturers'),
            'url' => $this->context->link->getPageLink('manufacturer'),
        ];

        $catalog['supplier'] = [
            'id' => 'supplier-page',
            'label' => $this->l('Suppliers'),
            'url' => $this->context->link->getPageLink('supplier'),
        ];

        $categories = Category::getRootCategory()->recurseLiteCategTree(0, 0, null, null, 'sitemap');
        $catalog['category'] = [
            'id' => 'category-page',
            'label' => $this->l('Categories'),
            'url' => '#',
            'children' => $categories['children'],
        ];

        $sitemap = [[
                'id' => 'page-page',
                'label' => $this->l('Pages'),
                'url' => '#',
                'children' => $pages,
            ],[
                'id' => 'catalog-page',
                'label' => $this->l('Catalog'),
                'url' => '#',
                'children' => $catalog,
            ],
        ];

        return $sitemap;
    }
}
