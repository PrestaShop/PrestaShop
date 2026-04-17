<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    public function init(): void
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
    public function initContent(): void
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
    protected function getProductSearchQuery(): ProductSearchQuery
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
    protected function getDefaultProductSearchProvider(): BestSalesProductSearchProvider
    {
        return new BestSalesProductSearchProvider(
            $this->getTranslator()
        );
    }

    public function getListingLabel(): string
    {
        return $this->getTranslator()->trans('Best sellers', [], 'Shop.Theme.Catalog');
    }

    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Best sellers', [], 'Shop.Theme.Catalog'),
            'url' => $this->context->link->getPageLink('best-sales'),
        ];

        return $breadcrumb;
    }
}
