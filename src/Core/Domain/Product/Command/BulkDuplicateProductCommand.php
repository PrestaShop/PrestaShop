<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Duplicates multiple products
 */
class BulkDuplicateProductCommand
{
    /**
     * @var ProductId[]
     */
    private $productIds;

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
        ShopConstraint $shopConstraint
    ) {
        foreach ($productIds as $productId) {
            $this->productIds[] = new ProductId($productId);
        }
        $this->shopConstraint = $shopConstraint;
    }

    /**
     * @return ProductId[]
     */
    public function getProductIds(): array
    {
        return $this->productIds;
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
