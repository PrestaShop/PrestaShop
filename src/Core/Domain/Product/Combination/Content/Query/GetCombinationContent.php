<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Content\Query;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class GetCombinationContent
{
    private CombinationId $combinationId;

    public function __construct(
        int $combinationId,
        private readonly ShopConstraint $shopConstraint,
    ) {
        $this->combinationId = new CombinationId($combinationId);
    }

    public function getCombinationId(): CombinationId
    {
        return $this->combinationId;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
