<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\AttachmentInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\QueryResult\VirtualProductFileForEditing;

/**
 * Product information for editing
 */
class ProductForEditing
{
    public function __construct(
        private int $productId,
        private string $type,
        private bool $isActive,
        private ProductCustomizationOptions $customizationOptions,
        private ProductBasicInformation $basicInformation,
        private CategoriesInformation $categoriesInformation,
        private ProductPricesInformation $pricesInformation,
        private ProductOptions $options,
        private ProductDetails $details,
        private ProductShippingInformation $shippingInformation,
        private ProductSeoOptions $productSeoOptions,
        private array $associatedAttachments,
        private ProductStockInformation $stockInformation,
        private ?VirtualProductFileForEditing $virtualProductFile,
        private string $coverThumbnailUrl,
        private array $shopIds,
    ) {
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return ProductCustomizationOptions
     */
    public function getCustomizationOptions(): ProductCustomizationOptions
    {
        return $this->customizationOptions;
    }

    /**
     * @return ProductBasicInformation
     */
    public function getBasicInformation(): ProductBasicInformation
    {
        return $this->basicInformation;
    }

    /**
     * @return CategoriesInformation
     */
    public function getCategoriesInformation(): CategoriesInformation
    {
        return $this->categoriesInformation;
    }

    /**
     * @return ProductPricesInformation
     */
    public function getPricesInformation(): ProductPricesInformation
    {
        return $this->pricesInformation;
    }

    /**
     * @return ProductOptions
     */
    public function getOptions(): ProductOptions
    {
        return $this->options;
    }

    /**
     * @return ProductDetails
     */
    public function getDetails(): ProductDetails
    {
        return $this->details;
    }

    /**
     * @return ProductShippingInformation
     */
    public function getShippingInformation(): ProductShippingInformation
    {
        return $this->shippingInformation;
    }

    /**
     * @return ProductSeoOptions
     */
    public function getProductSeoOptions(): ProductSeoOptions
    {
        return $this->productSeoOptions;
    }

    /**
     * @return AttachmentInformation[]
     */
    public function getAssociatedAttachments(): array
    {
        return $this->associatedAttachments;
    }

    /**
     * @return ProductStockInformation
     */
    public function getStockInformation(): ProductStockInformation
    {
        return $this->stockInformation;
    }

    /**
     * @return VirtualProductFileForEditing|null
     */
    public function getVirtualProductFile(): ?VirtualProductFileForEditing
    {
        return $this->virtualProductFile;
    }

    /**
     * @return string
     */
    public function getCoverThumbnailUrl(): string
    {
        return $this->coverThumbnailUrl;
    }

    /**
     * @return int[]
     */
    public function getShopIds(): array
    {
        return $this->shopIds;
    }
}
