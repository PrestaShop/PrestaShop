<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use PrestaShop\PrestaShop\Adapter\Search\SearchProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class SearchControllerCore extends ProductListingFrontController
{
    /** @var string */
    public $php_self = 'search';
    public $instant_search;
    public $ajax_search;

    protected $search_string;
    protected $search_tag;

    /**
     * Initialize the controller.
     *
     * @see FrontController::init()
     */
    public function init(): void
    {
        parent::init();

        $this->search_string = Tools::getValue('s');
        if (!$this->search_string) {
            $this->search_string = Tools::getValue('search_query');
        }

        $this->search_tag = Tools::getValue('tag');

        $this->context->smarty->assign(
            [
                'search_string' => $this->search_string,
                'search_tag' => $this->search_tag,
                'subcategories' => [],
            ]
        );
    }

    /**
     * Returns canonical URL for a search page with this term
     *
     * @return string
     */
    public function getCanonicalURL(): string
    {
        return $this->buildPaginatedUrl($this->context->link->getPageLink('search', null, null, ['s' => $this->search_string]));
    }

    /**
     * Initializes a set of commonly used variables related to the current page, available for use
     * in the template. @see FrontController::assignGeneralPurposeVariables for more information.
     *
     * @return array
     */
    public function getTemplateVarPage(): array
    {
        $page = parent::getTemplateVarPage();

        // Ensure that no search results page is indexed by search engines.
        $page['meta']['robots'] = 'noindex';

        return $page;
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent(): void
    {
        parent::initContent();

        $this->doProductSearch('catalog/listing/search', ['entity' => 'search']);
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
            ->setQueryType('search')
            ->setSortOrder(new SortOrder('product', 'position', 'desc'))
            ->setSearchString($this->search_string)
            ->setSearchTag($this->search_tag);

        return $query;
    }

    /**
     * Default product search provider used if no filtering module stood up for the job
     *
     * @return SearchProductSearchProvider
     */
    protected function getDefaultProductSearchProvider(): SearchProductSearchProvider
    {
        return new SearchProductSearchProvider(
            $this->getTranslator()
        );
    }

    public function getListingLabel(): string
    {
        return $this->getTranslator()->trans('Search results', [], 'Shop.Theme.Catalog');
    }

    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Search results', [], 'Shop.Theme.Catalog'),
            'url' => $this->getCurrentUrl(),
        ];

        return $breadcrumb;
    }
}
