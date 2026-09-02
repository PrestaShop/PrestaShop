<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\Provider;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopAssociationNotFound;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

interface ProductImageProviderInterface
{
    /**
     * @param ProductId $productId
     * @param ShopId $shopId
     *
     * @return string
     *
     * @throws ShopAssociationNotFound
     */
    public function getProductCoverUrl(ProductId $productId, ShopId $shopId): string;

    /**
     * @param CombinationId $combinationId
     * @param ShopId $shopId
     *
     * @return string
     *
     * @throws ShopAssociationNotFound
     */
    public function getCombinationCoverUrl(CombinationId $combinationId, ShopId $shopId): string;
}
