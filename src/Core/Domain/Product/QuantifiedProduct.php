<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Transfers product data
 */
class QuantifiedProduct
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var CombinationId
     */
    private $combinationId;

    /**
     * @param int $productId
     * @param int $quantity
     * @param int $combinationId
     */
    public function __construct(
        int $productId,
        int $quantity,
        ?int $combinationId = null
    ) {
        $this->productId = new ProductId($productId);
        $this->quantity = $quantity;
        $this->combinationId = $combinationId ? new CombinationId($combinationId) : null;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return CombinationId|null
     */
    public function getCombinationId(): ?CombinationId
    {
        return $this->combinationId;
    }
}
