<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class EditStandardProductCommand extends AbstractProductCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @param int $productId
     * @param array $localisedProductNames
     */
    public function __construct(int $productId, array $localisedProductNames)
    {
        parent::__construct($localisedProductNames);

        $this->productId = new ProductId($productId);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }
}
