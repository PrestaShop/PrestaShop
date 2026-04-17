<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Repository;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Provider\ProductImageProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductPreview;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Returns preview data for a product or a list of product
 *
 * @todo add function for the list that should be used in the new product search API
 */
class ProductPreviewRepository
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductImageProviderInterface
     */
    private $productImageProvider;

    public function __construct(
        ProductRepository $productRepository,
        ProductImageProviderInterface $productImageProvider
    ) {
        $this->productRepository = $productRepository;
        $this->productImageProvider = $productImageProvider;
    }

    public function getPreview(ProductId $productId, LanguageId $languageId): ProductPreview
    {
        $shopId = $this->productRepository->getProductDefaultShopId($productId);
        $product = $this->productRepository->get($productId, $shopId);

        return new ProductPreview(
            $productId->getValue(),
            $product->name[$languageId->getValue()] ?? reset($product->name),
            $this->productImageProvider->getProductCoverUrl($productId, $shopId)
        );
    }
}
