<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProductsForAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler\SearchProductsForAssociationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForAssociation;

#[AsQueryHandler]
class SearchProductsForAssociationHandler implements SearchProductsForAssociationHandlerInterface
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var ProductImagePathFactory
     */
    private $productImagePathFactory;

    /**
     * @param ProductRepository $productRepository
     * @param ProductImagePathFactory $productImagePathFactory
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductImagePathFactory $productImagePathFactory
    ) {
        $this->productRepository = $productRepository;
        $this->productImagePathFactory = $productImagePathFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(SearchProductsForAssociation $query): array
    {
        $foundProducts = $this->productRepository->searchProducts(
            $query->getPhrase(),
            $query->getLanguageId(),
            $query->getShopId(),
            $query->getLimit()
        );

        $productsForAssociation = [];
        foreach ($foundProducts as $foundProduct) {
            $productsForAssociation[] = $this->createResult($foundProduct);
        }

        return $productsForAssociation;
    }

    /**
     * @param array $foundProduct
     *
     * @return ProductForAssociation
     */
    private function createResult(array $foundProduct): ProductForAssociation
    {
        if (empty($foundProduct['id_image'])) {
            $imagePath = $this->productImagePathFactory->getNoImagePath(ProductImagePathFactory::IMAGE_TYPE_HOME_DEFAULT);
        } else {
            $imagePath = $this->productImagePathFactory->getPathByType(
                new ImageId((int) $foundProduct['id_image']),
                ProductImagePathFactory::IMAGE_TYPE_HOME_DEFAULT
            );
        }

        return new ProductForAssociation(
            (int) $foundProduct['id_product'],
            $foundProduct['name'],
            $foundProduct['reference'] ?? '',
            $imagePath,
            $foundProduct['product_type'],
        );
    }
}
