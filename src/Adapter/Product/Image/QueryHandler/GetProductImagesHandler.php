<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Image\QueryHandler;

use Image;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryHandler\GetProductImagesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImage;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Handles @see GetProductImages query
 */
#[AsQueryHandler]
final class GetProductImagesHandler implements GetProductImagesHandlerInterface
{
    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @var ProductImagePathFactory
     */
    private $productImageUrlFactory;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param ProductImageRepository $productImageRepository
     * @param ProductImagePathFactory $productImageUrlFactory
     * @param ProductRepository $productRepository
     */
    public function __construct(
        ProductImageRepository $productImageRepository,
        ProductImagePathFactory $productImageUrlFactory,
        ProductRepository $productRepository
    ) {
        $this->productImageRepository = $productImageRepository;
        $this->productImageUrlFactory = $productImageUrlFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetProductImages $query): array
    {
        if (!$query->getShopConstraint()->getShopId()) {
            throw new InvalidShopConstraintException('Only single shop constraint is supported');
        }

        $shopId = $query->getShopConstraint()->getShopId();
        $productId = $query->getProductId();
        $this->productRepository->assertProductIsAssociatedToShop($productId, $shopId);
        $coverId = $this->productImageRepository->findCoverImageId($productId, $shopId);

        // we still use hardcoded AllShops constraint here to get images for all the shops
        // but when we format the image we will check if it is cover for the shopId from query,
        // because cover is the only property of image that might differ between shops
        $images = $this->productImageRepository->getImages($productId, ShopConstraint::allShops());

        $productImages = [];
        foreach ($images as $image) {
            // if for some reason there is no cover, we set first found image as cover to avoid further errors
            if (!$coverId) {
                $imageId = new ImageId($image->id);
                $coverId = $imageId;
            }

            $productImages[] = $this->formatImage(
                $image,
                $this->productImageRepository->getAssociatedShopIds(new ImageId($image->id)),
                $coverId
            );
        }

        return $productImages;
    }

    /**
     * @param Image $image
     *
     * @return ProductImage
     */
    private function formatImage(Image $image, array $shopIds, ImageId $coverId): ProductImage
    {
        $imageIdValue = (int) $image->id;
        $imageId = new ImageId($imageIdValue);

        return new ProductImage(
            $imageIdValue,
            $coverId->getValue() === $imageIdValue,
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
