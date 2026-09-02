<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Deletes multiple combinations
 */
class BulkDeleteCombinationCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var CombinationId[]
     */
    private $combinationIds;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @param int $productId
     * @param int[] $combinationIds
     * @param ShopConstraint $shopConstraint
     */
    public function __construct(
        int $productId,
        array $combinationIds,
        ShopConstraint $shopConstraint
    ) {
        $this->productId = new ProductId($productId);
        $this->setCombinationIds($combinationIds);
        $this->shopConstraint = $shopConstraint;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return CombinationId[]
     */
    public function getCombinationIds(): array
    {
        return $this->combinationIds;
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    /**
     * @param int[] $combinationIds
     */
    private function setCombinationIds(array $combinationIds): void
    {
        foreach ($combinationIds as $combinationId) {
            $this->combinationIds[] = new CombinationId($combinationId);
        }
    }
}
