<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\Query;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Gets preview url for product which points to front-end of the product.
 */
class GetProductPreviewUrl
{
    /**
     * @var ProductId ProductId
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
