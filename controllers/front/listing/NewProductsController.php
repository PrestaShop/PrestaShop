<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use PrestaShop\PrestaShop\Adapter\NewProducts\NewProductsProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class NewProductsControllerCore extends ProductListingFrontController
{
    /** @var string */
    public $php_self = 'new-products';

    /**
     * Returns canonical URL for new-products page
     *
     * @return string
     */
    public function getCanonicalURL(): string
    {
        return $this->buildPaginatedUrl($this->context->link->getPageLink('new-products'));
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent(): void
    {
        parent::initContent();

        $this->doProductSearch('catalog/listing/new-products', ['entity' => 'new-products']);
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
            ->setQueryType('new-products')
            ->setSortOrder(new SortOrder('product', 'date_add', 'desc'));

        return $query;
    }

    /**
     * Default product search provider used if no filtering module stood up for the job
     *
     * @return NewProductsProductSearchProvider
     */
    protected function getDefaultProductSearchProvider(): NewProductsProductSearchProvider
    {
        return new NewProductsProductSearchProvider(
            $this->getTranslator()
        );
    }

    public function getListingLabel(): string
    {
        return $this->trans(
            'New products',
            [],
            'Shop.Theme.Catalog'
        );
    }

    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('New products', [], 'Shop.Theme.Catalog'),
            'url' => $this->context->link->getPageLink('new-products'),
        ];

        return $breadcrumb;
    }
}
