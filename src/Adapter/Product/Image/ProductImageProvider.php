<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Image;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Provider\ProductImageProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

class ProductImageProvider implements ProductImageProviderInterface
{
    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var ProductImagePathFactory
     */
    private $productImagePathFactory;

    public function __construct(
        ProductImageRepository $productImageRepository,
        CombinationRepository $combinationRepository,
        ProductImagePathFactory $productImagePathFactory
    ) {
        $this->productImageRepository = $productImageRepository;
        $this->productImagePathFactory = $productImagePathFactory;
        $this->combinationRepository = $combinationRepository;
    }

    public function getProductCoverUrl(ProductId $productId, ShopId $shopId): string
    {
        $imageId = $this->productImageRepository->getDefaultImageId($productId, $shopId);

        return $imageId ?
            $this->productImagePathFactory->getPath($imageId) :
            $this->productImagePathFactory->getNoImagePath(ProductImagePathFactory::IMAGE_TYPE_SMALL_DEFAULT)
        ;
    }

    public function getCombinationCoverUrl(CombinationId $combinationId, ShopId $shopId): string
    {
        $imageId = $this->productImageRepository->getPreviewCombinationProduct($combinationId);

        if ($imageId) {
            return $this->productImagePathFactory->getPath($imageId);
        }

        $productId = $this->combinationRepository->getProductId($combinationId);

        return $this->getProductCoverUrl($productId, $shopId);
    }
}
