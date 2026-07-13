<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

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
     * Bulk-replace the per-image settings.
     *
     * The existing addProductSetting() adder is fine for hand-crafted call sites
     * that already have a stream of ProductImageSetting objects to feed in one at
     * a time, but it does not let the Symfony serializer bind an incoming array
     * of settings from a request body in a single hop: the denormalizer needs a
     * matching public setter for the property to fill it.
     *
     * @param ProductImageSetting[] $productImageSettings
     */
    public function setProductImageSettings(array $productImageSettings): self
    {
        $this->productImageSettings = $productImageSettings;

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
