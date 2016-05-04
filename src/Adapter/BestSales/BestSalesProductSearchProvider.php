<?php

namespace PrestaShop\PrestaShop\Adapter\BestSales;

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Adapter\Translator;
use ProductSale;
use Tools;

class BestSalesProductSearchProvider implements ProductSearchProviderInterface
{
    private $translator;
    private $sortOrderFactory;

    public function __construct(
        Translator $translator
    ) {
        $this->translator = $translator;
        $this->sortOrderFactory = new SortOrderFactory($this->translator);
    }

    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        if (!$products = ProductSale::getBestSales(
            $context->getIdLang(),
            $query->getPage(),
            $query->getResultsPerPage(),
            $query->getSortOrder()->toLegacyOrderBy(),
            $query->getSortOrder()->toLegacyOrderWay()
        )) {
            $products = [];
        }

        $count = (int)ProductSale::getNbSales();

        $result = new ProductSearchResult();
        $result
            ->setProducts($products)
            ->setTotalProductsCount($count)
        ;

        $result->setAvailableSortOrders(
            [
                (new SortOrder('product', 'name', 'asc'))->setLabel(
                    $this->translator->trans('Name, A to Z', [], 'Product')
                ),
                (new SortOrder('product', 'name', 'desc'))->setLabel(
                    $this->translator->trans('Name, Z to A', [], 'Product')
                ),
                (new SortOrder('product', 'price', 'asc'))->setLabel(
                    $this->translator->trans('Price, low to high', [], 'Product')
                ),
                (new SortOrder('product', 'price', 'desc'))->setLabel(
                    $this->translator->trans('Price, high to low', [], 'Product')
                )
            ]
        );

        return $result;
    }
}
