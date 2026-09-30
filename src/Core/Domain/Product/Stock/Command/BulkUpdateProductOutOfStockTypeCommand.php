<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Sets the behavior applied when several products run out of stock.
 */
class BulkUpdateProductOutOfStockTypeCommand
{
    /**
     * @var ProductId[]
     */
    private $productIds;

    /**
     * @var OutOfStockType
     */
    private $outOfStockType;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @param int[] $productIds
     *
     * @throws ProductConstraintException
     */
    public function __construct(
        array $productIds,
        int $outOfStockType,
        ShopConstraint $shopConstraint
    ) {
        foreach ($productIds as $productId) {
            $this->productIds[] = new ProductId($productId);
        }
        $this->outOfStockType = new OutOfStockType($outOfStockType);
        $this->shopConstraint = $shopConstraint;
    }

    /**
     * @return ProductId[]
     */
    public function getProductIds(): array
    {
        return $this->productIds;
    }

    public function getOutOfStockType(): OutOfStockType
    {
        return $this->outOfStockType;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
