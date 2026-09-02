<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Update;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;
use Product;

/**
 * Provides methods to update related products (a.k.a accessories)
 */
class RelatedProductsUpdater
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param ProductRepository $productRepository
     */
    public function __construct(
        ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @param ProductId $productId
     * @param ProductId[] $relatedProductIds
     */
    public function setRelatedProducts(ProductId $productId, array $relatedProductIds): void
    {
        $product = $this->productRepository->getProductByDefaultShop($productId);

        if (empty($relatedProductIds)) {
            $this->deleteRelatedProducts($product);

            return;
        }

        $this->productRepository->assertAllProductsExists($relatedProductIds);

        $this->deleteRelatedProducts($product);
        $this->insertRelatedProducts($product, $relatedProductIds);
    }

    /**
     * @param Product $product
     * @param array $relatedProductIds
     *
     * @throws CoreException
     */
    private function insertRelatedProducts(Product $product, array $relatedProductIds): void
    {
        $ids = array_map(function (ProductId $productId): int {
            return $productId->getValue();
        }, $relatedProductIds);

        try {
            $product->changeAccessories($ids);
        } catch (PrestaShopException) {
            throw new CoreException(sprintf(
                'Error occurred when updating related products for product #%d',
                $product->id
            ));
        }
    }

    /**
     * @param Product $product
     *
     * @throws CannotUpdateProductException
     * @throws CoreException
     */
    private function deleteRelatedProducts(Product $product): void
    {
        try {
            if (!$product->deleteAccessories()) {
                throw new CannotUpdateProductException(sprintf(
                    'Failed to delete related products for product #%d',
                    $product->id
                ));
            }
        } catch (PrestaShopException) {
            throw new CoreException(sprintf(
                'Error occurred when updating related products for product #%d',
                $product->id
            ));
        }
    }
}
