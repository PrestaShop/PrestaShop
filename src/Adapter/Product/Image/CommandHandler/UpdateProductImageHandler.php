<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Image\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Image\ProductImageFileValidator;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\Update\ProductImageUpdater;
use PrestaShop\PrestaShop\Adapter\Product\Image\Uploader\ProductImageUploader;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\UpdateProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\CommandHandler\UpdateProductImageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotUpdateProductImageException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopAssociationNotFound;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;

#[AsCommandHandler]
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

        $shopId = null;
        if ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds()) {
            $shopId = $shopConstraint->getShopIds()[0];
        } elseif ($shopConstraint->forAllShops()) {
            $associatedShopIds = $this->productImageRepository->getAssociatedShopIds($imageId);
            // this we makes sure to load image from shop in which it exists,
            // else legacy ObjectModel would try to load context shop and some required data would end up being empty
            // only is_cover prop is multi-shop compatible now and is handled separately,
            // so we don't really care from which shop other properties are loaded
            $shopId = reset($associatedShopIds);
            if (!$shopId) {
                throw new ShopAssociationNotFound('Image is not associated to any shop');
            }
        } elseif ($shopConstraint->getShopId()) {
            $shopId = $shopConstraint->getShopId();
        }

        if (!$shopId) {
            throw new InvalidShopConstraintException('Could not deduce shopId from provided ShopConstraint');
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
