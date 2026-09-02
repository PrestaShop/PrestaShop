<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Pack\QueryResult;

/**
 * Holds packed product data
 */
class PackedProduct
{
    /**
     * @var int
     */
    private $productId;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var int
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
        int $combinationId
    ) {
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->combinationId = $combinationId;
    }

    /**
     * @return int
     */
    public function getProductId(): int
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
     * @return int
     */
    public function getCombinationId(): int
    {
        return $this->combinationId;
    }
}
