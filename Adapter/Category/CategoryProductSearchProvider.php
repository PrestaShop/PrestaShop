<?php

namespace PrestaShop\PrestaShop\Adapter\Category;

use PrestaShop\PrestaShop\Core\Business\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Business\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Business\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Business\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Business\Product\Search\PaginationResult;
use PrestaShop\PrestaShop\Core\Business\Product\Search\SortOrderFactory;
use PrestaShop\PrestaShop\Adapter\Translator;
use Category;

class CategoryProductSearchProvider implements ProductSearchProviderInterface
{
    private $translator;
    private $category;
    private $sortOrderFactory;

    public function __construct(
        Translator $translator,
        Category $category
    ) {
        $this->translator = $translator;
        $this->category = $category;
        $this->sortOrderFactory = new SortOrderFactory($this->translator);
    }

    private function getProductsOrCount(
        ProductSearchContext $context,
        ProductSearchQuery $query,
        $type = 'products'
    ) {
        return $this->category->getProducts(
            $context->getIdLang(),
            $query->getPage(),
            $query->getResultsPerPage(),
            $query->getSortOrder()->toLegacyOrderBy(),
            $query->getSortOrder()->toLegacyOrderWay(),
            $type !== 'products'
        );
    }

    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        $products = $this->getProductsOrCount($context, $query, 'products');
        $count = $this->getProductsOrCount($context, $query, 'count');

        $result = new ProductSearchResult;
        $result->setProducts($products);

        $pagination = new PaginationResult;
        $pagination
            ->setTotalResultsCount($count)
            ->setPage($query->getPage())
            ->setResultsCount(count($products))
            ->setPagesCount(ceil($count / $query->getResultsPerPage()))
        ;
        $result->setPaginationResult($pagination);

        $result->setAvailableSortOrders(
            $this->sortOrderFactory->getDefaultSortOrders()
        );

        return $result;
    }
}
