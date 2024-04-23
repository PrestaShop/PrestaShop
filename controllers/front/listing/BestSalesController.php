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
use PrestaShop\PrestaShop\Adapter\BestSales\BestSalesProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class BestSalesControllerCore extends ProductListingFrontController
{
    /** @var string */
    public $php_self = 'best-sales';

    /**
     * Returns canonical URL for best-sales page
     *
     * @return string
     */
    public function getCanonicalURL(): string
    {
        return $this->buildPaginatedUrl($this->context->link->getPageLink('best-sales'));
    }

    /**
     * Initializes controller.
     *
     * @see FrontController::init()
     *
     * @throws PrestaShopException
     */
    public function init()
    {
        if (Configuration::get('PS_DISPLAY_BEST_SELLERS')) {
            parent::init();
        } else {
            Tools::redirect('pagenotfound');
        }
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $this->doProductSearch('catalog/listing/best-sales', ['entity' => 'best-sales']);
    }

    /**
     * Gets the product search query for the controller. This is a set of information that
     * a filtering module or the default provider will use to fetch our products.
     *
     * @return ProductSearchQuery
     */
    protected function getProductSearchQuery()
    {
        $query = new ProductSearchQuery();
        $query
            ->setQueryType('best-sales')
            ->setSortOrder(new SortOrder('product', 'sales', 'desc'));

        return $query;
    }

    /**
     * Default product search provider used if no filtering module stood up for the job
     *
     * @return BestSalesProductSearchProvider
     */
    protected function getDefaultProductSearchProvider()
    {
        return new BestSalesProductSearchProvider(
            $this->getTranslator()
        );
    }

    public function getListingLabel()
    {
        return $this->getTranslator()->trans('Best sellers', [], 'Shop.Theme.Catalog');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Best sellers', [], 'Shop.Theme.Catalog'),
            'url' => $this->context->link->getPageLink('best-sales'),
        ];

        return $breadcrumb;
    }
}
