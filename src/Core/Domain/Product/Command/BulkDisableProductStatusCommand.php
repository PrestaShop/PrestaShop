<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Disables multiple statuses for products.
 */
class BulkDisableProductStatusCommand
{
    /**
     * @var ProductId[]
     */
    private $productIds;

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
