<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetRelatedProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\GetRelatedProductsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\RelatedProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

#[AsQueryHandler]
class GetRelatedProductsHandler implements GetRelatedProductsHandlerInterface
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
     * @var ProductImagePathFactory
     */
    private $productImagePathFactory;

    /**
     * @param ProductRepository $productRepository
     * @param ProductImageRepository $productImageRepository
     * @param ProductImagePathFactory $productImagePathFactory
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductImageRepository $productImageRepository,
        ProductImagePathFactory $productImagePathFactory
    ) {
        $this->productRepository = $productRepository;
        $this->productImageRepository = $productImageRepository;
        $this->productImagePathFactory = $productImagePathFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetRelatedProducts $query): array
    {
        $results = $this->productRepository->getRelatedProducts($query->getProductId(), $query->getLanguageId());
        $relatedProducts = [];

        foreach ($results as $result) {
            $productId = new ProductId((int) $result['id_product']);
            // related products are not multishop compatible,
            // so we just use default product shop to retrieve info required by multishop repositories
            $shopId = $this->productRepository->getProductDefaultShopId($productId);
            $imageId = $this->productImageRepository->getDefaultImageId($productId, $shopId);
            $imagePath = $imageId ?
                $this->productImagePathFactory->getPathByType($imageId, ProductImagePathFactory::IMAGE_TYPE_HOME_DEFAULT) :
                $this->productImagePathFactory->getNoImagePath(ProductImagePathFactory::IMAGE_TYPE_HOME_DEFAULT)
            ;

            $relatedProducts[] = new RelatedProduct(
                $productId->getValue(),
                $result['name'],
                $result['reference'],
                $imagePath
            );
        }

        return $relatedProducts;
    }
}
