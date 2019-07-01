<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Removes multiple products.
 */
class BulkDeleteProductCommand
{
    /**
     * @var ProductId[]
     */
    private $productIds;

    /**
     * @param array $productIds
     */
    public function __construct(array $productIds)
    {
        foreach ($productIds as $productId) {
            $this->productIds[] = new ProductId($productId);
        }
    }

    /**
     * @return ProductId[]
     */
    public function getProductIds()
    {
        return $this->productIds;
    }
}
