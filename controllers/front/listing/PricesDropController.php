<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use PrestaShop\PrestaShop\Adapter\PricesDrop\PricesDropProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class PricesDropControllerCore extends ProductListingFrontController
{
    /** @var string */
    public $php_self = 'prices-drop';

    /**
     * Returns canonical URL for prices-drop page
     *
     * @return string
     */
    public function getCanonicalURL(): string
    {
        return $this->buildPaginatedUrl($this->context->link->getPageLink('prices-drop'));
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent(): void
    {
        parent::initContent();

        $this->doProductSearch('catalog/listing/prices-drop', ['entity' => 'prices-drop']);
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
            ->setQueryType('prices-drop')
            ->setSortOrder(new SortOrder('product', 'name', 'asc'));

        return $query;
    }

    /**
     * Default product search provider used if no filtering module stood up for the job
     *
     * @return PricesDropProductSearchProvider
     */
    protected function getDefaultProductSearchProvider(): PricesDropProductSearchProvider
    {
        return new PricesDropProductSearchProvider(
            $this->getTranslator()
        );
    }

    public function getListingLabel(): string
    {
        return $this->trans(
            'Prices drop',
            [],
            'Shop.Theme.Catalog'
        );
    }

    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Prices drop', [], 'Shop.Theme.Catalog'),
            'url' => $this->context->link->getPageLink('prices-drop'),
        ];

        return $breadcrumb;
    }
}
