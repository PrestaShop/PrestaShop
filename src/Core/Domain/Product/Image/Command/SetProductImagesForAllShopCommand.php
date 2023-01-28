<?php

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class SetProductImagesForAllShopCommand
{
    /**
     * @var ProductImageSetting[]
     */
    private $productImageSettings = [];

    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @param int $productId
     */
    public function __construct(int $productId)
    {
        $this->productId = new ProductId($productId);
    }

    public function addProductSetting(ProductImageSetting $productImageSetting): self
    {
        $this->productImageSettings[] = $productImageSetting;

        return $this;
    }

    /**
     * @return ProductImageSetting[]
     */
    public function getProductImageSettings(): array
    {
        return $this->productImageSettings;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }
}
