<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Toggles product status on or off.
 */
class ToggleProductStatusCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @param int $productId
     */
    public function __construct($productId)
    {
        $this->productId = new ProductId($productId);
    }

    /**
     * @return ProductId
     */
    public function getProductId()
    {
        return $this->productId;
    }
}
