<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

class ProductDeleter
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    public function __construct(
        ProductRepository $productRepository,
        CombinationRepository $combinationRepository,
        ProductImageRepository $productImageRepository
    ) {
        $this->productRepository = $productRepository;
        $this->combinationRepository = $combinationRepository;
        $this->productImageRepository = $productImageRepository;
    }

    /**
     * @param ProductId $productId
     * @param ShopId[] $shopIds
     */
    public function deleteFromShops(ProductId $productId, array $shopIds): void
    {
        if (empty($shopIds)) {
            return;
        }

        $this->removeImages(
            $productId,
            $shopIds
        );
        $this->removeCombinations($productId, $shopIds);
        $this->productRepository->deleteFromShops($productId, $shopIds);
    }

    /**
     * @param ProductId $productId
     * @param ShopConstraint $shopConstraint
     */
    public function deleteByShopConstraint(ProductId $productId, ShopConstraint $shopConstraint): void
    {
        $shopIds = $this->productRepository->getShopIdsByConstraint($productId, $shopConstraint);

        $this->removeImages($productId, $shopIds);
        $this->removeCombinations($productId, $shopIds);
        $this->productRepository->deleteFromShops($productId, $shopIds);
    }

    /**
     * @param ProductId $productId
     * @param ShopId[] $shopIds
     */
    private function removeImages(ProductId $productId, array $shopIds): void
    {
        foreach ($shopIds as $shopId) {
            $imageIds = $this->productImageRepository->getImageIds($productId, ShopConstraint::shop($shopId->getValue()));
            foreach ($imageIds as $imageId) {
                $this->productImageRepository->deleteFromShops($imageId, [$shopId]);
            }
        }
    }

    /**
     * @param ProductId $productId
     * @param ShopId[] $shopIds
     */
    private function removeCombinations(ProductId $productId, array $shopIds): void
    {
        if (!$this->productRepository->hasCombinations($productId)) {
            return;
        }

        foreach ($shopIds as $shopId) {
            $this->combinationRepository->deleteByProductId($productId, ShopConstraint::shop($shopId->getValue()));
        }
    }
}
