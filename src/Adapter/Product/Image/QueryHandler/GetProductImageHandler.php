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
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImage;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryHandler\GetProductImageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImage;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopAssociationNotFound;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Handles @see GetProductImage query
 */
#[AsQueryHandler]
class GetProductImageHandler implements GetProductImageHandlerInterface
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
     * @param ProductImageRepository $productImageRepository
     * @param ProductImagePathFactory $productImageUrlFactory
     */
    public function __construct(
        ProductImageRepository $productImageRepository,
        ProductImagePathFactory $productImageUrlFactory
    ) {
        $this->productImageRepository = $productImageRepository;
        $this->productImageUrlFactory = $productImageUrlFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(GetProductImage $query): ProductImage
    {
        $imageId = $query->getImageId();

        // Sometimes we need to show the image for shop even when it is not associated, but then the "cover" field is hidden,
        // so in that case remaining info can be loaded from any other shop (only cover differs between shops)
        try {
            $image = $this->productImageRepository->getByShopConstraint($imageId, $query->getShopConstraint());
            $isCover = (bool) $image->cover;
        } catch (ShopAssociationNotFound) {
            // If image is not associated with certain shop, then fall back to any other shop image (by using all shops constraint).
            $image = $this->productImageRepository->getByShopConstraint($imageId, ShopConstraint::allShops());
            // hardcode cover to false, because image cannot be a cover if it is not associated to this shop.
            $isCover = false;
        }

        return new ProductImage(
            (int) $image->id,
            $isCover,
            (int) $image->position,
            $image->legend,
            $this->productImageUrlFactory->getPath($imageId),
            $this->productImageUrlFactory->getPathByType($imageId, ProductImagePathFactory::IMAGE_TYPE_SMALL_DEFAULT),
            array_map(
                static function (ShopId $shopId): int {
                    return $shopId->getValue();
                },
                $this->productImageRepository->getAssociatedShopIds($imageId)
            )
        );
    }
}
