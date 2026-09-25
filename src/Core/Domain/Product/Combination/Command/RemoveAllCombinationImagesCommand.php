<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class RemoveAllCombinationImagesCommand
{
    /**
     * @var CombinationId
     */
    private $combinationId;

    /**
     * @param int $combinationId
     */
    /**
     * @var ShopConstraint|null
     */
    private $shopConstraint;

    public function __construct(int $combinationId, ?ShopConstraint $shopConstraint = null)
    {
        $this->combinationId = new CombinationId($combinationId);
        $this->shopConstraint = $shopConstraint;
    }

    public function getShopConstraint(): ?ShopConstraint
    {
        return $this->shopConstraint;
    }

    /**
     * @return CombinationId
     */
    public function getCombinationId(): CombinationId
    {
        return $this->combinationId;
    }
}
