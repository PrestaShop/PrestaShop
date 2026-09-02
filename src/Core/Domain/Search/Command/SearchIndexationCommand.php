<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Search\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Triggers search indexation.
 */
class SearchIndexationCommand
{
    private bool $full;

    private ?ProductId $productId;

    private ShopConstraint $shopConstraint;

    public function __construct(
        bool $full = false,
        ?ShopConstraint $shopConstraint = null,
        ?ProductId $productId = null
    ) {
        $this->full = $full;
        $this->shopConstraint = $shopConstraint ?? ShopConstraint::allShops();
        $this->productId = $productId;
    }

    /**
     * Indicates if full reindex is requested.
     */
    public function isFull(): bool
    {
        return $this->full;
    }

    /**
     * Returns shop constraint for indexation.
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    /**
     * Return product id for indexation
     */
    public function getProductId(): ?ProductId
    {
        return $this->productId;
    }
}
