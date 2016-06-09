<?php

namespace PrestaShop\PrestaShop\Adapter\PricesDrop;

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Adapter\Translator;
use Product;
use Tools;

class PricesDropProductSearchProvider implements ProductSearchProviderInterface
{
    private $translator;
    private $sortOrderFactory;

    public function __construct(
        Translator $translator
    ) {
        $this->translator = $translator;
        $this->sortOrderFactory = new SortOrderFactory($this->translator);
    }

    private function getProductsOrCount(
        ProductSearchContext $context,
        ProductSearchQuery $query,
        $type = 'products'
    ) {
        return Product::getPricesDrop(
            $context->getIdLang(),
            $query->getPage(),
            $query->getResultsPerPage(),
            $type !== 'products',
            $query->getSortOrder()->toLegacyOrderBy(),
            $query->getSortOrder()->toLegacyOrderWay()
        );
    }

    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        if (!$products = $this->getProductsOrCount($context, $query, 'products')) {
            $products = array();
        }
        $count = $this->getProductsOrCount($context, $query, 'count');

        $result = new ProductSearchResult();
        $result
            ->setProducts($products)
            ->setTotalProductsCount($count)
        ;

        $result->setAvailableSortOrders(
            [
                (new SortOrder('product', 'name', 'asc'))->setLabel(
                    $this->translator->trans('Name, A to Z', array(), 'Shop-Theme-Catalog')
                ),
                (new SortOrder('product', 'name', 'desc'))->setLabel(
                    $this->translator->trans('Name, Z to A', array(), 'Shop-Theme-Catalog')
                ),
                (new SortOrder('product', 'price', 'asc'))->setLabel(
                    $this->translator->trans('Price, low to high', array(), 'Shop-Theme-Catalog')
                ),
                (new SortOrder('product', 'price', 'desc'))->setLabel(
                    $this->translator->trans('Price, high to low', array(), 'Shop-Theme-Catalog')
                )
            ]
        );

        return $result;
    }
}
