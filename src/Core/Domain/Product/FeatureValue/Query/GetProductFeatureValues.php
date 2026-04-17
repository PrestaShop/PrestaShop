<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Query;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Get FeatureValue associated to a Product
 */
class GetProductFeatureValues
{
    private ProductId $productId;

    protected ShopId $shopId;

    public function __construct(int $productId, int $shopId)
    {
        $this->productId = new ProductId($productId);
        $this->shopId = new ShopId($shopId);
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function getShopId(): ShopId
    {
        return $this->shopId;
    }
}
