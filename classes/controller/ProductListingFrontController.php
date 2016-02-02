<?php

use PrestaShop\PrestaShop\Core\Business\Product\ProductPresenter;
use PrestaShop\PrestaShop\Core\Business\Product\ProductPresentationSettings;
use PrestaShop\PrestaShop\Core\Business\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Business\Product\Search\PaginationResult;
use PrestaShop\PrestaShop\Core\Business\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Business\Product\Search\Facet;
use PrestaShop\PrestaShop\Core\Business\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Core\Business\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Business\Product\Search\FacetsRendererInterface;

/**
 * This class is the base class for all front-end "product listing" controllers,
 * like "CategoryController", that is, controllers whose primary job is
 * to display a list of products and filters to make navigation easier.
 */
abstract class ProductListingFrontControllerCore extends ProductPresentingFrontController
{
    /**
     * This method is used by "prepareProductForTemplate" to add missing fields
     * to the product array.
     * The minimal fields that must be contained in $rawProduct is "id_product".
     * You should not need to use this method directly.
     *
     * @internal
     * @param array $rawProduct an associative array with at least the "id_product" key
     * @return array
     */
    private function addMissingProductFields(array $rawProduct)
    {
        $id_shop = (int)$this->getProductSearchContext()->getIdShop();
        $id_lang = (int)$this->getProductSearchContext()->getIdLang();
        $id_product = (int)$rawProduct['id_product'];
        $prefix = _DB_PREFIX_;

        $nb_days_new_product = (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        if (!Validate::isUnsignedInt($nb_days_new_product)) {
            $nb_days_new_product = 20;
        }
        $now = date('Y-m-d') . ' 00:00:00';

        $sql = "SELECT
                    p.*,
                    pl.*,
                    (DATE_SUB('$now', INTERVAL $nb_days_new_product DAY) > 0) as new
                FROM {$prefix}product p
                INNER JOIN {$prefix}product_lang pl
                    ON pl.id_product = p.id_product
                    AND pl.id_shop = $id_shop
                    AND pl.id_lang = $id_lang
                    AND p.id_product = $id_product";

        $rows = Db::getInstance()->executeS($sql);
        return array_merge($rows[0], $rawProduct);
    }

    /**
     * Takes an associative array with at least the "id_product" key
     * and returns an array containing all information necessary for
     * rendering the product in the template.
     *
     * @param  array  $rawProduct an associative array with at least the "id_product" key
     * @return array  a product ready for templating
     */
    private function prepareProductForTemplate(array $rawProduct)
    {
        $enrichedProduct = $this->addMissingProductFields(
            $rawProduct
        );

        $product = Product::getProductProperties(
            $this->getProductSearchContext()->getIdLang(),
            $enrichedProduct,
            $this->context
        );

        $presenter = $this->getProductPresenter();
        $settings = $this->getProductPresentationSettings();

        return $presenter->presentForListing(
            $settings,
            $product,
            $this->context->language
        );
    }

    /**
     * Runs "prepareProductForTemplate" on the collection
     * of product ids passed in.
     *
     * @param  array  $products array of arrays containing at list the "id_product" key
     * @return array of products ready for templating
     */
    protected function prepareMultipleProductsForTemplate(array $products)
    {
        return array_map([$this, 'prepareProductForTemplate'], $products);
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
        return (new ProductSearchContext)
            ->setIdShop($this->context->shop->id)
            ->setIdLang($this->context->language->id)
        ;
    }

    /**
     * Converts a Facet to an array with all necessary
     * information for templating.
     *
     * @param  Facet  $facet
     * @return array ready for templating
     */
    protected function prepareFacetForTemplate(Facet $facet)
    {
        $facetsArray = $facet->toArray();
        foreach ($facetsArray['filters'] as &$filter) {
            $filter['facetLabel'] = $facet->getLabel();
            if ($filter['nextEncodedFacets']) {
                $filter['nextEncodedFacetsURL'] = $this->makeURL([
                    'q' => $filter['nextEncodedFacets'],
                    'page' => null
                ]);
            } else {
                $filter['nextEncodedFacetsURL'] = $this->makeURL([
                    'q' => null
                ]);
            }
        }
        unset($filter);
        return $facetsArray;
    }

    /**
     * Renders an array of facets.
     *
     * @param  array  $facets
     * @return string the HTML of the facets
     */
    protected function renderFacets(array $facets, SortOrder $sortOrder)
    {
        $facetsVar = array_map([$this, 'prepareFacetForTemplate'], $facets);

        $activeFilters = [];
        foreach ($facetsVar as $facet) {
            foreach ($facet['filters'] as $filter) {
                if ($filter['active']) {
                    $activeFilters[] = $filter;
                }
            }
        }

        return $this->render('catalog/_partials/facets.tpl', [
            'facets'        => $facetsVar,
            'jsEnabled'     => $this->ajax,
            'activeFilters' => $activeFilters,
            'sort_order'    => $sortOrder->getURLParameter()
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
     * @param  ProductSearchQuery $query
     * @return ProductSearchProviderInterface or null
     */
    private function getProductSearchProviderFromModules($query)
    {
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
        /**
         * To render the page we need to find something (a ProductSearchProviderInterface)
         * that knows how to query products.
         */

        // the search provider will need a context (language, shop...) to do its job
        $context    = $this->getProductSearchContext();

        // the controller generates the query...
        $query      = $this->getProductSearchQuery();

        // ...modules decide if they can handle it (first one that can is used)
        $provider   = $this->getProductSearchProviderFromModules($query);

        // if no module wants to do the query, then the core feature is used
        if (null === $provider) {
            $provider = $this->getDefaultProductSearchProvider();
        }

        // we need to set a few parameters from back-end preferences
        $query
            ->setResultsPerPage(Configuration::get('PS_PRODUCTS_PER_PAGE'))
            ->setPage(max((int)Tools::getValue('page'), 1))
        ;

        // set the sort order if provided in the URL
        if (($encodedSortOrder = Tools::getValue('order'))) {
            $query->setSortOrder(SortOrder::fromURLParameter(
                $encodedSortOrder
            ));
        }

        // get the parameters containing the encoded facets from the URL
        $encodedFacets = Tools::getValue('q');

        /**
         * The controller is agnostic of facets.
         * It's up to the search module to add facets to the query
         * if it supports it.
         *
         * Facets are encoded in the "q" URL parameter, which is passed
         * to the search provider.
         */

        $provider->addFacetsToQuery(
            $context,
            $encodedFacets,
            $query
        );

        // Now the query contains all facets, that is,
        // additional ways to refine the query that are defined
        // by the module but of which the controller knows nothing.
        //
        // We're ready to run the actual query!

        $result = $provider->runQuery(
            $context,
            $query
        );

        // prepare the products
        $products = $this->prepareMultipleProductsForTemplate(
            $result->getProducts()
        );

        // render the facets
        if ($provider instanceof FacetsRendererInterface) {
            // with the provider if it wants to
            $ps_search_facets = $provider->renderFacets(
                $context,
                $result->getNextQuery()->getFacets(),
                $query->getSortOrder()
            );
        } else {
            // with the core
            $ps_search_facets = $this->renderFacets(
                $result->getNextQuery()->getFacets(),
                $query->getSortOrder()
            );
        }


        // prepare the pagination
        $pagination = $this->getTemplateVarPagination(
            $result->getPaginationResult()
        );

        // prepare the sort orders
        // note that, again, the product controller is sort-orders
        // agnostic
        // a module can easily add specific sort orders that it needs
        // to support (e.g. sort by "energy efficiency")
        $sort_orders = $this->getTemplateVarSortOrders(
            $result->getAvailableSortOrders(),
            $query->getSortOrder()->getURLParameter()
        );

        return [
            'products'          => $products,
            'sort_orders'       => $sort_orders,
            'pagination'        => $pagination,
            'ps_search_facets' => $ps_search_facets,
            'ps_search_encoded_facets' => $result->getEncodedFacets(),
            'jsEnabled'         => $this->ajax
        ];
    }

    /**
     * Pagination is HARD. We let the core do the heavy lifting from
     * a simple representation of the pagination.
     *
     * Generated URLs will include the page number, obviously,
     * but also the sort order and the "q" (facets) parameters.
     *
     * @param  PaginationResult $result the number of pages etc.
     * @return an array that makes rendering the pagination very easy
     */
    protected function getTemplateVarPagination(PaginationResult $result)
    {
        return array_map(function ($link) {
            $link['url'] = $this->makeURL([
                'page'  => $link['page']
            ]);
            return $link;
        }, $result->buildLinks());
    }

    /**
     * Prepares the sort-order links.
     *
     * Sort order links contain the current encoded facets if any,
     * but not the page number because normally when you change the sort order
     * you want to go back to page one.
     *
     * @param  array  $sortOrders                   the available sort orders
     * @param  string $currentSortOrderURLParameter used to know which of the sort orders (if any) is active
     * @return array
     */
    protected function getTemplateVarSortOrders(array $sortOrders, $currentSortOrderURLParameter)
    {
        return array_map(function ($sortOrder) use ($currentSortOrderURLParameter) {
            $order = $sortOrder->toArray();
            $order['current'] = $order['urlParameter'] === $currentSortOrderURLParameter;
            $order['url'] = $this->makeURL([
                'order' => $order['urlParameter'],
                'page'  => null
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

        $products = $this->render('catalog/products.tpl', $search);

        $data = [
            'products'            => $products,
            'ps_search_facets'   => $search['ps_search_facets'],
            'current_url'         => $this->makeURL([
                'q' => $search['ps_search_encoded_facets']
            ])
        ];

        return $data;
    }

    /**
     * Finally, the methods that wraps it all:
     *
     * If we're doing AJAX, output a JSON of the necessary product search related
     * variables.
     *
     * If we're not doing AJAX, then render the whole page with the given template.
     *
     * @param  string $template the template for this page
     * @return no return
     */
    protected function doProductSearch($template)
    {
        if ($this->ajax) {
            ob_end_clean();
            header('Content-Type: application/json');
            die(json_encode($this->getAjaxProductSearchVariables()));
        } else {
            $this->context->smarty->assign($this->getProductSearchVariables());
            $this->setTemplate($template);
        }
    }

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
