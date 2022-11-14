<?php

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Image\CommandHandler;

use Image;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\ProductImageSetting;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\SetProductImagesForAllShopCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\CommandHandler\SetProductImagesForAllShopHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotRemoveCoverException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

class SetProductImagesForAllShopHandler implements SetProductImagesForAllShopHandlerInterface
{
    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @var ProductImageMultiShopRepository
     */
    private $productImageMultiShopRepository;
    /**
     * @var ProductMultiShopRepository
     */
    private $productMultiShopRepository;

    public function __construct(
        ProductImageRepository $productImageRepository,
        ProductMultiShopRepository $productMultiShopRepository,
        ProductImageMultiShopRepository $productImageMultiShopRepository
    ) {
        $this->productImageRepository = $productImageRepository;
        $this->productMultiShopRepository = $productMultiShopRepository;
        $this->productImageMultiShopRepository = $productImageMultiShopRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(SetProductImagesForAllShopCommand $command): void
    {
        $imagesAssociatedToProduct = $this->productImageRepository->getImages($command->getProductId());
        $shopIdsAssociatedToProduct = $this->getShopIdsAssociatedToProduct($command->getProductId());

        foreach ($imagesAssociatedToProduct as $image) {
            $shopsToAddImageTo = $this->extractShopsToAddImageTo($command->getProductImageSettings(), $image->id);
            $shopsToRemoveImageFrom = $this->getShopsToRemoveImageFrom($shopIdsAssociatedToProduct, $shopsToAddImageTo, $image);
            $this->associateImageToShops($command->getProductId()->getValue(), $image, $shopsToAddImageTo);
            $this->productImageMultiShopRepository->deleteFromShops($image, $shopsToRemoveImageFrom);
        }
    }

    /**
     * @param ProductId $productId
     *
     * @return int[]
     */
    private function getShopIdsAssociatedToProduct(ProductId $productId): array
    {
        $shopIdsAssociatedToProduct = array_map(
            static function (ShopId $shopId): int {
                return $shopId->getValue();
            },
            $this->productMultiShopRepository->getAssociatedShopIds($productId)
        );

        return $shopIdsAssociatedToProduct;
    }

    /**
     * @param ProductImageSetting[] $productImageSettings
     * @param int $imageId
     *
     * @return ProductImageSetting|null
     */
    private function getProductImageSettingByImage(array $productImageSettings, int $imageId): ?ProductImageSetting
    {
        $productImageSettingsFiltered = array_filter(
            $productImageSettings,
            function (ProductImageSetting $productImageSetting) use ($imageId): bool {
                return $productImageSetting->getImageId()->getValue() === $imageId;
            }
        );

        return reset($productImageSettingsFiltered) ?: null;
    }

    /**
     * @param ProductImageSetting[] $productImageSettings
     * @param int $imageId
     *
     * @return array
     */
    private function extractShopsToAddImageTo(array $productImageSettings, int $imageId): array
    {
        $productImageSetting = $this->getProductImageSettingByImage($productImageSettings, $imageId);
        $shopsToAddImageTo = [];
        if ($productImageSetting) {
            $shopsToAddImageTo = array_map(
                static function (ShopId $shopId): int {
                    return $shopId->getValue();
                },
                $productImageSetting->getShopIds()
            );
        }

        return $shopsToAddImageTo;
    }

    /**
     * @param array $shopIdsAssociatedToProduct
     * @param array $shopsToAddImageTo
     * @param Image $image
     *
     * @return array
     */
    private function getShopsToRemoveImageFrom(array $shopIdsAssociatedToProduct, array $shopsToAddImageTo, Image $image): array
    {
        $shopsToRemoveImageFrom = array_diff($shopIdsAssociatedToProduct, $shopsToAddImageTo);
        $shopIdsAssociatedToImage = array_map(
            function (ShopId $shopId): int {
                return $shopId->getValue();
            },
            $this->productImageMultiShopRepository->getAssociatedShopIds(new ImageId($image->id))
        );
        $shopsToRemoveImageFrom = array_filter(
            $shopsToRemoveImageFrom,
            function (int $shopToRemoveImageFrom) use ($shopIdsAssociatedToImage): bool {
                return in_array($shopToRemoveImageFrom, $shopIdsAssociatedToImage, true);
            }
        );

        $shopIdsCovered = array_map(
            function (ShopId $shopId): int {
                return $shopId->getValue();
            },
            $this->productImageMultiShopRepository->getShopIdsCoveredBy(new ImageId($image->id))
        );
        $coverToRemove = array_intersect($shopIdsCovered, $shopsToRemoveImageFrom);
        if (!empty($coverToRemove)) {
            throw new CannotRemoveCoverException();
        }

        return $shopsToRemoveImageFrom;
    }

    /**
     * @param int $productId
     * @param Image $image
     * @param array $shopsToAddImageTo
     *
     * @return void
     */
    private function associateImageToShops(int $productId, Image $image, array $shopsToAddImageTo): void
    {
        //We need to set id_product otherwise the association is done but product_id will be null in ps_image_shop
        $image->id_product = $productId;
        $image->associateTo($shopsToAddImageTo);
    }
}
