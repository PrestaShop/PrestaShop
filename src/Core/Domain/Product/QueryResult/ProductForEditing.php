<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
    /**
     * @var int
     */
    private $productId;

    /**
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var ProductBasicInformation
     */
    private $basicInformation;

    /**
     * @var CategoriesInformation
     */
    private $categoriesInformation;

    /**
     * @var ProductPricesInformation
     */
    private $pricesInformation;

    /**
     * @var ProductOptions
     */
    private $options;

    /**
     * @var ProductDetails
     */
    private $details;

    /**
     * @var ProductCustomizationOptions
     */
    private $customizationOptions;

    /**
     * @var ProductShippingInformation
     */
    private $shippingInformation;

    /**
     * @var ProductSeoOptions
     */
    private $productSeoOptions;

    /**
     * @var AttachmentInformation[]
     */
    private $associatedAttachments;

    /**
     * @var ProductStockInformation
     */
    private $stockInformation;

    /**
     * @var VirtualProductFileForEditing|null
     */
    private $virtualProductFile;

    /**
     * @var string
     */
    private $coverThumbnailUrl;

    /**
     * @param int $productId
     * @param string $type
     * @param bool $isActive
     * @param ProductCustomizationOptions $customizationOptions
     * @param ProductBasicInformation $basicInformation
     * @param CategoriesInformation $categoriesInformation
     * @param ProductPricesInformation $pricesInformation
     * @param ProductOptions $options
     * @param ProductDetails $details
     * @param ProductShippingInformation $shippingInformation
     * @param ProductSeoOptions $productSeoOptions
     * @param AttachmentInformation[] $associatedAttachments
     * @param ProductStockInformation $stockInformation
     * @param VirtualProductFileForEditing|null $virtualProductFile
     * @param string $coverThumbnailUrl
     */
    public function __construct(
        int $productId,
        string $type,
        bool $isActive,
        ProductCustomizationOptions $customizationOptions,
        ProductBasicInformation $basicInformation,
        CategoriesInformation $categoriesInformation,
        ProductPricesInformation $pricesInformation,
        ProductOptions $options,
        ProductDetails $details,
        ProductShippingInformation $shippingInformation,
        ProductSeoOptions $productSeoOptions,
        array $associatedAttachments,
        ProductStockInformation $stockInformation,
        ?VirtualProductFileForEditing $virtualProductFile,
        string $coverThumbnailUrl
    ) {
        $this->productId = $productId;
        $this->type = $type;
        $this->isActive = $isActive;
        $this->customizationOptions = $customizationOptions;
        $this->basicInformation = $basicInformation;
        $this->categoriesInformation = $categoriesInformation;
        $this->pricesInformation = $pricesInformation;
        $this->options = $options;
        $this->details = $details;
        $this->shippingInformation = $shippingInformation;
        $this->productSeoOptions = $productSeoOptions;
        $this->associatedAttachments = $associatedAttachments;
        $this->stockInformation = $stockInformation;
        $this->virtualProductFile = $virtualProductFile;
        $this->coverThumbnailUrl = $coverThumbnailUrl;
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
}
