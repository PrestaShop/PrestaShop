<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Query which provides combination for editing
 */
class GetCombinationForEditing
{
    /**
     * @var CombinationId
     */
    private $combinationId;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @param int $combinationId
     */
    public function __construct(
        int $combinationId,
        ShopConstraint $shopConstraint
    ) {
        $this->combinationId = new CombinationId($combinationId);
        $this->shopConstraint = $shopConstraint;
    }

    /**
     * @return CombinationId
     */
    public function getCombinationId(): CombinationId
    {
        return $this->combinationId;
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
