<?php

namespace PrestaShopBundle\Service;

use PrestaShop\PrestaShop\Adapter\Product\ProductDataProvider;

class ProductService {
    /** @var ProductDataProvider */
    protected $dataProvider;

    public function __construct(ProductDataProvider $dataProvider) {
        $this->dataProvider = $dataProvider;
    }

    public function cleanupOldTempProducts()
    {
        $oldProducts = \Product::getOldTempProducts();

        foreach ($oldProducts as $oldProduct) {
            $id_product = $oldProduct['id_product'];
            /** @var \Product $product */
            $product = $this->dataProvider->getProduct($id_product);
            $product->delete();
        }
    }

}