<?php

namespace PrestaShop\PrestaShop\Core\Business\Product\Search\Provider;

use PrestaShop\PrestaShop\Core\Business\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Business\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Business\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Business\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Foundation\Database\AutoPrefixingDatabase;

class CategoryProductSearchProvider implements ProductSearchProviderInterface
{
    private $db;

    public function __construct(AutoPrefixingDatabase $db)
    {
        $this->db = $db;
    }

    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        $result = new ProductSearchResult;

        $id_shop = (int)$context->getIdShop();
        $id_category = (int)$query->getIdCategory();

        $products = $this->db->select(
            "SELECT product.id_product
                FROM prefix_category_product cp
                    INNER JOIN prefix_product_shop product
                        ON product.id_shop = $id_shop
                        AND product.id_product = cp.id_product
                WHERE
                    cp.id_category = $id_category
            "
        );

        $result->setProducts($products);

        return $result;
    }

    public function addFacetsToQuery(
        ProductSearchContext $context,
        $encodedFacets,
        ProductSearchQuery $query
    ) {
        // Nothing to do here.
    }
}
