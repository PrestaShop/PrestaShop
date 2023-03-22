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

namespace PrestaShop\PrestaShop\Adapter\Product\Image\CommandHandler;

use Image;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\ProductImageSetting;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\SetProductImagesForAllShopCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\CommandHandler\SetProductImagesForAllShopHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotRemoveCoverException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

class SetProductImagesForAllShopHandler implements SetProductImagesForAllShopHandlerInterface
{
    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(
        ProductImageRepository $productImageRepository,
        ProductRepository $productRepository
    ) {
        $this->productImageRepository = $productImageRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(SetProductImagesForAllShopCommand $command): void
    {
        $productId = $command->getProductId();
        $imagesAssociatedToProduct = $this->productImageRepository->getImages($productId, ShopConstraint::allShops());
        $shopIdsAssociatedToProduct = $this->shopIdsToInt($this->productRepository->getAssociatedShopIds($productId));

        foreach ($imagesAssociatedToProduct as $image) {
            $shopsToAddImageTo = $this->extractShopsToAddImageTo($command->getProductImageSettings(), $image->id);
            $shopsToRemoveImageFrom = $this->getShopsToRemoveImageFrom($shopIdsAssociatedToProduct, $shopsToAddImageTo, $image);
            $image->associateTo($shopsToAddImageTo, $productId->getValue());

            if (!empty($shopsToRemoveImageFrom)) {
                $this->productImageRepository->deleteFromShops(
                    new ImageId((int) $image->id),
                    array_map(
                        static function (int $shopId): ShopId {
                            return new ShopId($shopId);
                        },
                        $shopsToRemoveImageFrom
                    )
                );
            }
        }
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
            $shopsToAddImageTo = $this->shopIdsToInt($productImageSetting->getShopIds());
        }

        return $shopsToAddImageTo;
    }

    /**
     * @param int[] $shopIdsAssociatedToProduct
     * @param int[] $shopsToAddImageTo
     * @param Image $image
     *
     * @return int[]
     */
    private function getShopsToRemoveImageFrom(array $shopIdsAssociatedToProduct, array $shopsToAddImageTo, Image $image): array
    {
        $shopsToRemoveImageFrom = array_diff($shopIdsAssociatedToProduct, $shopsToAddImageTo);
        $shopIdsAssociatedToImage = $this->shopIdsToInt($this->productImageRepository->getAssociatedShopIds(new ImageId($image->id)));
        $shopsToRemoveImageFrom = array_filter(
            $shopsToRemoveImageFrom,
            function (int $shopToRemoveImageFrom) use ($shopIdsAssociatedToImage): bool {
                return in_array($shopToRemoveImageFrom, $shopIdsAssociatedToImage, true);
            }
        );

        $shopIdsCovered = $this->shopIdsToInt($this->productImageRepository->getShopIdsByCoverId(new ImageId($image->id)));
        $coverToRemove = array_intersect($shopIdsCovered, $shopsToRemoveImageFrom);
        if (!empty($coverToRemove)) {
            throw new CannotRemoveCoverException();
        }

        return $shopsToRemoveImageFrom;
    }

    /**
     * @param ShopId[] $shopIds
     *
     * @return int[]
     */
    private function shopIdsToInt(array $shopIds): array
    {
        return array_map(
            function (ShopId $shopId): int {
                return $shopId->getValue();
            },
            $shopIds
        );
    }
}
