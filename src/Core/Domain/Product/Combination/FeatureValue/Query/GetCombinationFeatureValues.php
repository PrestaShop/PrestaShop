<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\Query;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Get FeatureValue associated to a Combination
 */
class GetCombinationFeatureValues
{
    private CombinationId $combinationId;

    protected ShopId $shopId;

    public function __construct(int $combinationId, int $shopId)
    {
        $this->combinationId = new CombinationId($combinationId);
        $this->shopId = new ShopId($shopId);
    }

    public function getCombinationId(): CombinationId
    {
        return $this->combinationId;
    }

    public function getShopId(): ShopId
    {
        return $this->shopId;
    }
}
