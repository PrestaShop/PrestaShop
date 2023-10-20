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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Image\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Image\ProductImageFileValidator;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\Update\ProductImageUpdater;
use PrestaShop\PrestaShop\Adapter\Product\Image\Uploader\ProductImageUploader;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\UpdateProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\CommandHandler\UpdateProductImageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotUpdateProductImageException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopAssociationNotFound;

class UpdateProductImageHandler implements UpdateProductImageHandlerInterface
{
    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @var ProductImageUpdater
     */
    private $productImageUpdater;

    /**
     * @var ProductImageUploader
     */
    private $productImageUploader;

    /**
     * @var ProductImageFileValidator
     */
    private $imageValidator;

    /**
     * @param ProductImageRepository $productImageRepository
     * @param ProductImageUpdater $productImageUpdater
     * @param ProductImageUploader $productImageUploader
     * @param ProductImageFileValidator $imageValidator
     */
    public function __construct(
        ProductImageRepository $productImageRepository,
        ProductImageUpdater $productImageUpdater,
        ProductImageUploader $productImageUploader,
        ProductImageFileValidator $imageValidator
    ) {
        $this->productImageRepository = $productImageRepository;
        $this->productImageUpdater = $productImageUpdater;
        $this->productImageUploader = $productImageUploader;
        $this->imageValidator = $imageValidator;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(UpdateProductImageCommand $command): void
    {
        $shopConstraint = $command->getShopConstraint();

        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Shop group constraint is not supported');
        }

        $imageId = $command->getImageId();

        if (null !== $command->getFilePath()) {
            $this->imageValidator->assertFileUploadLimits($command->getFilePath());
            $this->imageValidator->assertIsValidImageType($command->getFilePath());
        }

        // shop constraint assertion above already ensures that it is either shopId or allShops
        $shopId = $shopConstraint->getShopId() ?: null;

        if (!$shopId) {
            $associatedShopIds = $this->productImageRepository->getAssociatedShopIds($imageId);
            // this we makes sure to load image from shop in which it exists,
            // else legacy ObjectModel would try to load context shop and some required data would end up being empty
            // only is_cover prop is multi-shop compatible now and is handled separately,
            // so we don't really care from which shop other properties are loaded
            $shopId = reset($associatedShopIds);

            if (!$shopId) {
                throw new ShopAssociationNotFound('Image is not associated to any shop');
            }
        }

        $image = $this->productImageRepository->get(
            $command->getImageId(),
            $shopId
        );

        if (null !== $command->getLocalizedLegends()) {
            $image->legend = $command->getLocalizedLegends();
            $this->productImageRepository->partialUpdateForShops(
                $image,
                ['legend' => array_keys($command->getLocalizedLegends())],
                [$shopId],
                CannotUpdateProductImageException::FAILED_UPDATE_COVER
            );
        }

        if ($command->isCover()) {
            $this->productImageUpdater->updateProductCover($image, $command->getShopConstraint());
        }

        if (null !== $command->getFilePath()) {
            $this->productImageUploader->upload($image, $command->getFilePath());
        }

        if (null !== $command->getPosition()) {
            $this->productImageUpdater->updatePosition($image, $command->getPosition());
        }
    }
}
