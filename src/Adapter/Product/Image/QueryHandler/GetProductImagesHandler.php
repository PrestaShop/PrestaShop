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

namespace PrestaShop\PrestaShop\Adapter\Product\Image\QueryHandler;

use Image;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageMultiShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryHandler\GetProductImagesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImage;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Handles @see GetProductImages query
 */
final class GetProductImagesHandler implements GetProductImagesHandlerInterface
{
    /**
     * @var ProductImageMultiShopRepository
     */
    private $productImageRepository;

    /**
     * @var ProductImagePathFactory
     */
    private $productImageUrlFactory;

    /**
     * @param ProductImageMultiShopRepository $productImageRepository
     * @param ProductImagePathFactory $productImageUrlFactory
     */
    public function __construct(
        ProductImageMultiShopRepository $productImageRepository,
        ProductImagePathFactory $productImageUrlFactory
    ) {
        $this->productImageRepository = $productImageRepository;
        $this->productImageUrlFactory = $productImageUrlFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetProductImages $query): array
    {
        if (!$query->getShopConstraint()->getShopId()) {
            throw new InvalidShopConstraintException('Only single shop constraint is supported');
        }

        // we still use hardcoded AllShops constraint here to get images for all the shops
        // but when we format the image we will check if it is cover for the shopId from query,
        // because that is the only property of image that differs between shops
        $images = $this->productImageRepository->getImages($query->getProductId(), ShopConstraint::allShops());
        $productImages = [];
        foreach ($images as $image) {
            $productImages[] = $this->formatImage(
                $image,
                $this->productImageRepository->getAssociatedShopIds(new ImageId($image->id)),
                $query->getShopConstraint()->getShopId()
            );
        }

        return $productImages;
    }

    /**
     * @param Image $image
     *
     * @return ProductImage
     */
    private function formatImage(Image $image, array $shopIds, ShopId $currentShopId): ProductImage
    {
        $imageIdValue = (int) $image->id;
        $imageId = new ImageId($imageIdValue);
        $coverId = $this->productImageRepository->getCoverImageId(new ProductId((int) $image->id_product), $currentShopId);

        return new ProductImage(
            $imageIdValue,
            $coverId && ($imageIdValue === $coverId->getValue()),
            (int) $image->position,
            $image->legend,
            $this->productImageUrlFactory->getPath($imageId),
            $this->productImageUrlFactory->getPathByType($imageId, ProductImagePathFactory::IMAGE_TYPE_SMALL_DEFAULT),
            array_map(
                static function (ShopId $shopId): int {
                    return $shopId->getValue();
                },
                $shopIds
            )
        );
    }
}
