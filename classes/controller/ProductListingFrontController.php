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
use PrestaShop\PrestaShop\Core\Product\Search\Facet;
use PrestaShop\PrestaShop\Core\Product\Search\FacetsRendererInterface;
use PrestaShop\PrestaShop\Core\Product\Search\Pagination;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

/**
 * This class is the base class for all front-end "product listing" controllers,
 * like "CategoryController", that is, controllers whose primary job is
 * to display a list of products and filters to make navigation easier.
 */
abstract class ProductListingFrontControllerCore extends ProductPresentingFrontController
{
    /**
     * Generates an URL to a product listing controller
     * with only the essential query params and page remaining.
     *
     * @param string $canonicalUrl an url to a listing controller page
     *
     * @return string a canonical URL for the current page in the list
     */
    public function buildPaginatedUrl(string $canonicalUrl): string
    {
        $parsedUrl = parse_url($canonicalUrl);
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $params);
        } else {
            $params = [];
        }
        $page = (int) Tools::getValue('page');
        if ($page > 1) {
            $params['page'] = $page;
        } else {
            unset($params['page']);
        }

        return http_build_url($parsedUrl, ['query' => http_build_query($params)]);
    }

    /**
     * Takes an associative array with at least the "id_product" key
     * and returns an array containing all information necessary for
     * rendering the product in the template.
     *
     * @param array $rawProduct an associative array with at least the "id_product" key
     *
     * @return array a product ready for templating
     */
    // @phpstan-ignore-next-line
    private function prepareProductForTemplate(array $rawProduct)
    {
        // Enrich data of product
        $product = (new ProductAssembler($this->context))
            ->assembleProduct($rawProduct);

        // Prepare configuration
        $presenter = $this->getProductPresenter();
        $settings = $this->getProductPresentationSettings();

        // Present and return product
        return $presenter->present(
            $settings,
            $product,
            $this->context->language
        );
    }

    /**
     * Runs "prepareProductForTemplate" on the collection
     * of product ids passed in.
     *
     * @param array $products array of arrays containing at list the "id_product" key
     *
     * @return array of products ready for templating
     */
    protected function prepareMultipleProductsForTemplate(array $products)
    {
        // Enrich data set of products
        $products = (new ProductAssembler($this->context))
            ->assembleProducts($products);

        // Prepare configuration
        $presenter = $this->getProductPresenter();
        $settings = $this->getProductPresentationSettings();

        // Present and return each product
        foreach ($products as &$product) {
            $product = $presenter->present(
                $settings,
                $product,
                $this->context->language
            );
        }

        return $products;
    }

    /**
     * The ProductSearchContext is passed to search providers
     * so that they can avoid using the global id_lang and such
     * variables. This method acts as a factory for the ProductSearchContext.
     *
     * @return ProductSearchContext a search context for the queries made by this controller
     */
    protected function getProductSearchContext()
    {
        return (new ProductSearchContext())
            ->setIdShop($this->context->shop->id)
            ->setIdLang($this->context->language->id)
            ->setIdCurrency($this->context->currency->id)
            ->setIdCustomer($this->context->customer ? $this->context->customer->id : null);
    }

    /**
     * Converts a Facet to an array with all necessary
     * information for templating.
     *
     * @param Facet $facet
     *
     * @return array ready for templating
     */
    protected function prepareFacetForTemplate(Facet $facet)
    {
        $facetsArray = $facet->toArray();
        foreach ($facetsArray['filters'] as &$filter) {
            $filter['facetLabel'] = $facet->getLabel();
            if ($filter['nextEncodedFacets']) {
                $filter['nextEncodedFacetsURL'] = Tools::updateCurrentQueryString([
                    'q' => $filter['nextEncodedFacets'],
                    'page' => null,
                ]);
            } else {
                $filter['nextEncodedFacetsURL'] = Tools::updateCurrentQueryString([
                    'q' => null,
                ]);
            }
        }
        unset($filter);

        return $facetsArray;
    }

    /**
     * Renders an array of facets.
     *
     * @param ProductSearchResult $result
     *
     * @return string the HTML of the facets
     */
    protected function renderFacets(ProductSearchResult $result)
    {
        $facetCollection = $result->getFacetCollection();
        // not all search providers generate menus
        if (empty($facetCollection)) {
            return '';
        }

        $facetsVar = array_map(
            [$this, 'prepareFacetForTemplate'],
            $facetCollection->getFacets()
        );

        $activeFilters = [];
        foreach ($facetsVar as $facet) {
            foreach ($facet['filters'] as $filter) {
                if ($filter['active']) {
                    $activeFilters[] = $filter;
                }
            }
        }

        return $this->render('catalog/_partials/facets', [
            'facets' => $facetsVar,
            'js_enabled' => $this->ajax,
            'activeFilters' => $activeFilters,
            'sort_order' => $result->getCurrentSortOrder()->toString(),
            'clear_all_link' => Tools::updateCurrentQueryString(['q' => null, 'page' => null]),
        ]);
    }

    /**
     * Renders an array of active filters.
     *
     * @param ProductSearchResult $result
     *
     * @return string the HTML of the facets
     */
    protected function renderActiveFilters(ProductSearchResult $result)
    {
        $facetCollection = $result->getFacetCollection();
        // not all search providers generate menus
        if (empty($facetCollection)) {
            return '';
        }

        $facetsVar = array_map(
            [$this, 'prepareFacetForTemplate'],
            $facetCollection->getFacets()
        );

        $activeFilters = [];
        foreach ($facetsVar as $facet) {
            foreach ($facet['filters'] as $filter) {
                if ($filter['active']) {
                    $activeFilters[] = $filter;
                }
            }
        }

        return $this->render('catalog/_partials/active_filters', [
            'activeFilters' => $activeFilters,
            'clear_all_link' => Tools::updateCurrentQueryString(['q' => null, 'page' => null]),
        ]);
    }

    /**
     * This method is the heart of the search provider delegation
     * mechanism.
     *
     * It executes the `productSearchProvider` hook (array style),
     * and returns the first one encountered.
     *
     * This provides a well specified way for modules to execute
     * the search query instead of the core.
     *
     * The hook is called with the $query argument, which allows
     * modules to decide if they can manage the query.
     *
     * For instance, if two search modules are installed and
     * one module knows how to search by category but not by manufacturer,
     * then "ManufacturerController" will use one module to do the query while
     * "CategoryController" will use another module to do the query.
     *
     * If no module can perform the query then null is returned.
     *
     * @param ProductSearchQuery $query
     *
     * @return ProductSearchProviderInterface|null
     */
    private function getProductSearchProviderFromModules($query)
    {
        // An array [module_name => module_output] will be returned
        $providers = Hook::exec(
            'productSearchProvider',
            ['query' => $query],
            null,
            true
        );

        if (!is_array($providers)) {
            $providers = [];
        }

        foreach ($providers as $provider) {
            if ($provider instanceof ProductSearchProviderInterface) {
                return $provider;
            }
        }

        return null;
    }

    /**
     * This returns all template variables needed for rendering
     * the product list, the facets, the pagination and the sort orders.
     *
     * @return array variables ready for templating
     */
    protected function getProductSearchVariables()
    {
        /*
         * To render the page we need to find something (a ProductSearchProviderInterface)
         * that knows how to query products.
         */

        // the search provider will need a context (language, shop...) to do its job
        $context = $this->getProductSearchContext();

        // the controller generates the query...
        $query = $this->getProductSearchQuery();

        // ...modules decide if they can handle it (first one that can is used)
        $provider = $this->getProductSearchProviderFromModules($query);

        // if no module wants to do the query, then the core feature is used
        if (null === $provider) {
            $provider = $this->getDefaultProductSearchProvider();
        }

        $resultsPerPage = (int) Tools::getValue('resultsPerPage');
        if ($resultsPerPage <= 0) {
            $resultsPerPage = Configuration::get('PS_PRODUCTS_PER_PAGE');
        }

        // we need to set a few parameters from back-end preferences
        $query
            ->setResultsPerPage($resultsPerPage)
            ->setPage(max((int) Tools::getValue('page'), 1))
        ;

        // set the sort order if provided in the URL
        if (($encodedSortOrder = Tools::getValue('order'))) {
            $query->setSortOrder(SortOrder::newFromString(
                $encodedSortOrder
            ));
        }

        // get the parameters containing the encoded facets from the URL
        $encodedFacets = Tools::getValue('q');

        /*
         * The controller is agnostic of facets.
         * It's up to the search module to use /define them.
         *
         * Facets are encoded in the "q" URL parameter, which is passed
         * to the search provider through the query's "$encodedFacets" property.
         */

        $query->setEncodedFacets($encodedFacets);

        Hook::exec('actionProductSearchProviderRunQueryBefore', [
            'query' => $query,
        ]);

        // We're ready to run the actual query!

        /** @var ProductSearchResult $result */
        $result = $provider->runQuery(
            $context,
            $query
        );

        Hook::exec('actionProductSearchProviderRunQueryAfter', [
            'query' => $query,
            'result' => $result,
        ]);

        if (Configuration::get('PS_CATALOG_MODE') && !Configuration::get('PS_CATALOG_MODE_WITH_PRICES')) {
            $this->disablePriceControls($result);
        }

        // sort order is useful for template,
        // add it if undefined - it should be the same one
        // as for the query anyway
        if (!$result->getCurrentSortOrder()) {
            $result->setCurrentSortOrder($query->getSortOrder());
        }

        // prepare the products
        $products = $this->prepareMultipleProductsForTemplate(
            $result->getProducts()
        );

        // render the facets
        if ($provider instanceof FacetsRendererInterface) {
            // with the provider if it wants to
            $rendered_facets = $provider->renderFacets(
                $context,
                $result
            );
            $rendered_active_filters = $provider->renderActiveFilters(
                $context,
                $result
            );
        } else {
            // with the core
            $rendered_facets = $this->renderFacets(
                $result
            );
            $rendered_active_filters = $this->renderActiveFilters(
                $result
            );
        }

        $pagination = $this->getTemplateVarPagination(
            $query,
            $result
        );

        // prepare the sort orders
        // note that, again, the product controller is sort-orders
        // agnostic
        // a module can easily add specific sort orders that it needs
        // to support (e.g. sort by "energy efficiency")
        $sort_orders = $this->getTemplateVarSortOrders(
            $result->getAvailableSortOrders(),
            $query->getSortOrder()->toString()
        );

        $sort_selected = false;
        if (!empty($sort_orders)) {
            foreach ($sort_orders as $order) {
                if (isset($order['current']) && true === $order['current']) {
                    $sort_selected = $order['label'];

                    break;
                }
            }
        }

        $searchVariables = [
            'result' => $result,
            'label' => $this->getListingLabel(),
            'products' => $products,
            'sort_orders' => $sort_orders,
            'sort_selected' => $sort_selected,
            'pagination' => $pagination,
            'rendered_facets' => $rendered_facets,
            'rendered_active_filters' => $rendered_active_filters,
            'js_enabled' => $this->ajax,
            'current_url' => Tools::updateCurrentQueryString([
                'q' => $result->getEncodedFacets(),
            ]),
        ];

        Hook::exec('filterProductSearch', ['searchVariables' => &$searchVariables]);
        Hook::exec('actionProductSearchAfter', $searchVariables);

        return $searchVariables;
    }

    /**
     * Removes price information from result (in facet collection and available sorters)
     * Usually used for catalog mode.
     *
     * @param ProductSearchResult $result
     */
    protected function disablePriceControls(ProductSearchResult $result)
    {
        if ($result->getFacetCollection()) {
            $filteredFacets = [];
            /** @var Facet $facet */
            foreach ($result->getFacetCollection()->getFacets() as $facet) {
                if ('price' === $facet->getType()) {
                    continue;
                }
                $filteredFacets[] = $facet;
            }
            $result->getFacetCollection()->setFacets($filteredFacets);
        }

        if ($result->getAvailableSortOrders()) {
            $filteredOrders = [];
            /** @var SortOrder $sortOrder */
            foreach ($result->getAvailableSortOrders() as $sortOrder) {
                if ('price' === $sortOrder->getField()) {
                    continue;
                }
                $filteredOrders[] = $sortOrder;
            }
            $result->setAvailableSortOrders($filteredOrders);
        }
    }

    /**
     * Pagination is HARD. We let the core do the heavy lifting from
     * a simple representation of the pagination.
     *
     * Generated URLs will include the page number, obviously,
     * but also the sort order and the "q" (facets) parameters.
     *
     * @param ProductSearchQuery $query
     * @param ProductSearchResult $result
     *
     * @return array An array that makes rendering the pagination very easy
     */
    protected function getTemplateVarPagination(
        ProductSearchQuery $query,
        ProductSearchResult $result
    ) {
        $pagination = new Pagination();
        $pagination
            ->setPage($query->getPage())
            ->setPagesCount(
                (int) ceil($result->getTotalProductsCount() / $query->getResultsPerPage())
            )
        ;

        $totalItems = $result->getTotalProductsCount();
        $itemsShownFrom = ($query->getResultsPerPage() * ($query->getPage() - 1)) + 1;
        $itemsShownTo = $query->getResultsPerPage() * $query->getPage();

        $pages = array_map(function ($link) {
            $link['url'] = Tools::updateCurrentQueryString([
                'page' => $link['page'] > 1 ? $link['page'] : null,
            ]);

            return $link;
        }, $pagination->buildLinks());

        //Filter next/previous link on first/last page
        $pages = array_filter($pages, function ($page) use ($pagination) {
            if ('previous' === $page['type'] && 1 === $pagination->getPage()) {
                return false;
            }
            if ('next' === $page['type'] && $pagination->getPagesCount() === $pagination->getPage()) {
                return false;
            }

            return true;
        });

        return [
            'total_items' => $totalItems,
            'items_shown_from' => $itemsShownFrom,
            'items_shown_to' => ($itemsShownTo <= $totalItems) ? $itemsShownTo : $totalItems,
            'current_page' => $pagination->getPage(),
            'pages_count' => $pagination->getPagesCount(),
            'pages' => $pages,
            // Compare to 3 because there are the next and previous links
            'should_be_displayed' => (count($pagination->buildLinks()) > 3),
        ];
    }

    /**
     * Prepares the sort-order links.
     *
     * Sort order links contain the current encoded facets if any,
     * but not the page number because normally when you change the sort order
     * you want to go back to page one.
     *
     * @param array $sortOrders the available sort orders
     * @param string $currentSortOrderURLParameter used to know which of the sort orders (if any) is active
     *
     * @return array
     */
    protected function getTemplateVarSortOrders(array $sortOrders, $currentSortOrderURLParameter)
    {
        return array_map(function ($sortOrder) use ($currentSortOrderURLParameter) {
            $order = $sortOrder->toArray();
            $order['current'] = $order['urlParameter'] === $currentSortOrderURLParameter;
            $order['url'] = Tools::updateCurrentQueryString([
                'order' => $order['urlParameter'],
                'page' => null,
            ]);

            return $order;
        }, $sortOrders);
    }

    /**
     * Similar to "getProductSearchVariables" but used in AJAX queries.
     *
     * It returns an array with the HTML for the products and facets,
     * and the current URL to put it in the browser URL bar (we don't want to
     * break the back button!).
     *
     * @return array
     */
    protected function getAjaxProductSearchVariables()
    {
        $search = $this->getProductSearchVariables();

        $rendered_products_top = $this->render('catalog/_partials/products-top', ['listing' => $search]);
        $rendered_products = $this->render('catalog/_partials/products', ['listing' => $search]);
        $rendered_products_bottom = $this->render('catalog/_partials/products-bottom', ['listing' => $search]);

        $data = array_merge(
            [
                'rendered_products_top' => $rendered_products_top,
                'rendered_products' => $rendered_products,
                'rendered_products_bottom' => $rendered_products_bottom,
            ],
            $search
        );

        if (!empty($data['products']) && is_array($data['products'])) {
            $data['products'] = $this->prepareProductArrayForAjaxReturn($data['products']);
        }

        return $data;
    }

    /**
     * Cleans the products array with only whitelisted properties.
     *
     * @param array[] $products
     *
     * @return array[] Filtered product list
     */
    protected function prepareProductArrayForAjaxReturn(array $products)
    {
        $filter = $this->get('prestashop.core.filter.front_end_object.search_result_product_collection');

        return $filter->filter($products);
    }

    /**
     * Finally, the methods that wraps it all:.
     *
     * If we're doing AJAX, output a JSON of the necessary product search related
     * variables.
     *
     * If we're not doing AJAX, then render the whole page with the given template.
     *
     * @param string $template the template for this page
     */
    protected function doProductSearch($template, $params = [], $locale = null)
    {
        if ($this->ajax) {
            ob_end_clean();
            header('Content-Type: application/json');
            $this->ajaxRender(json_encode($this->getAjaxProductSearchVariables()));

            return;
        } else {
            $variables = $this->getProductSearchVariables();
            $this->context->smarty->assign([
                'listing' => $variables,
            ]);
            $this->setTemplate($template, $params, $locale);
        }
    }

    abstract public function getListingLabel();

    /**
     * Gets the product search query for the controller.
     * That is, the minimum contract with which search modules
     * must comply.
     *
     * @return ProductSearchQuery
     */
    abstract protected function getProductSearchQuery();

    /**
     * We cannot assume that modules will handle the query,
     * so we need a default implementation for the search provider.
     *
     * @return ProductSearchProviderInterface
     */
    abstract protected function getDefaultProductSearchProvider();
}
