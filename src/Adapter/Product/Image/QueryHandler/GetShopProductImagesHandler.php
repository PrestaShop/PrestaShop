<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Image\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetShopProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryHandler\GetShopProductImagesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\Shop\ShopProductImagesCollection;

/**
 * Handles @see GetShopProductImages query
 */
#[AsQueryHandler]
final class GetShopProductImagesHandler implements GetShopProductImagesHandlerInterface
{
    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    public function __construct(
        ProductImageRepository $productImageRepository
    ) {
        $this->productImageRepository = $productImageRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(GetShopProductImages $query): ShopProductImagesCollection
    {
        return $this->productImageRepository->getImagesFromAllShop($query->getProductId());
    }
}
