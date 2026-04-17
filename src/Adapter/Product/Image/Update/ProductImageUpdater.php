<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Image\Update;

use Image;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\Uploader\ProductImageUploader;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotDeleteProductImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotUpdateProductImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Grid\Position\GridPositionUpdaterInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdateFactoryInterface;
use PrestaShop\PrestaShop\Core\Image\Exception\CannotUnlinkImageException;

class ProductImageUpdater
{
    /**
     * @var ProductImageUploader
     */
    private $productImageUploader;

    /**
     * @var PositionUpdateFactoryInterface
     */
    private $positionUpdateFactory;

    /**
     * @var PositionDefinition
     */
    private $positionDefinition;

    /**
     * @var GridPositionUpdaterInterface
     */
    private $positionUpdater;

    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @param ProductImageUploader $productImageUploader
     * @param PositionUpdateFactoryInterface $positionUpdateFactory
     * @param PositionDefinition $positionDefinition
     * @param GridPositionUpdaterInterface $positionUpdater
     */
    public function __construct(
        ProductImageUploader $productImageUploader,
        PositionUpdateFactoryInterface $positionUpdateFactory,
        PositionDefinition $positionDefinition,
        GridPositionUpdaterInterface $positionUpdater,
        ProductImageRepository $productImageRepository
    ) {
        $this->productImageUploader = $productImageUploader;
        $this->positionUpdateFactory = $positionUpdateFactory;
        $this->positionDefinition = $positionDefinition;
        $this->positionUpdater = $positionUpdater;
        $this->productImageRepository = $productImageRepository;
    }

    /**
     * @param ImageId $imageId
     *
     * @throws CannotDeleteProductImageException
     * @throws CannotUnlinkImageException
     */
    public function deleteImage(ImageId $imageId)
    {
        $image = $this->productImageRepository->getImageById($imageId);

        $this->productImageUploader->remove($image);
        $this->productImageRepository->delete($image);

        $this->productImageRepository->updateMissingCovers(new ProductId((int) $image->id_product));
    }

    /**
     * @param Image $newCover
     *
     * @throws CannotUpdateProductImageException
     */
    public function updateProductCover(Image $newCover, ShopConstraint $shopConstraint): void
    {
        if ($shopConstraint->getShopGroupId() !== null) {
            throw new InvalidShopConstraintException('Image has no features related with shop group use single shop and all shops constraints');
        } elseif ($shopConstraint->forAllShops()) {
            $shopIds = $this->productImageRepository->getAssociatedShopIds(new ImageId((int) $newCover->id));
        } elseif ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds()) {
            $shopIds = $shopConstraint->getShopIds();
        } else {
            $shopIds = [$shopConstraint->getShopId()];
        }

        $productId = new ProductId((int) $newCover->id_product);
        foreach ($shopIds as $shopId) {
            $currentCover = $this->productImageRepository->findCoverImageId($productId, $shopId);

            if ($currentCover !== null && $currentCover->getValue() === (int) $newCover->id) {
                continue;
            }

            if ($currentCover) {
                $currentImage = $this->productImageRepository->get($currentCover, $shopId);
                $this->updateCover($currentImage, false, $shopId);
            }

            $this->updateCover($newCover, true, $shopId);
        }
    }

    /**
     * @param Image $image
     * @param int $newPosition
     *
     * @throws CannotUpdateProductImageException
     */
    public function updatePosition(Image $image, int $newPosition): void
    {
        $oldPosition = (int) $image->position;
        $positionsData = [
            'positions' => [
                [
                    'rowId' => (int) $image->id_image,
                    'oldPosition' => $oldPosition,
                    'newPosition' => $newPosition,
                ],
            ],
            'parentId' => (int) $image->id_product,
        ];

        try {
            $positionUpdate = $this->positionUpdateFactory->buildPositionUpdate($positionsData, $this->positionDefinition);
            $this->positionUpdater->update($positionUpdate);
        } catch (PositionDataException|PositionUpdateException $e) {
            throw new CannotUpdateProductImageException(
                'Cannot update image position',
                CannotUpdateProductImageException::FAILED_UPDATE_POSITION,
                $e
            );
        }
    }

    /**
     * @param Image $image
     * @param bool $isCover
     *
     * @throws CannotUpdateProductImageException
     */
    private function updateCover(Image $image, bool $isCover, ShopId $shopId): void
    {
        $image->cover = $isCover;
        $this->productImageRepository->partialUpdateForShops(
            $image,
            ['cover'],
            [$shopId],
            CannotUpdateProductImageException::FAILED_UPDATE_COVER
        );
    }
}
