<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;

/**
 * Stock identifier.
 */
class StockId
{
    /**
     * @var int
     */
    private $stockId;

    /**
     * @param int $stockId
     *
     * @throws ProductStockConstraintException
     */
    public function __construct(int $stockId)
    {
        $this->assertIsGreaterThanZero($stockId);

        $this->stockId = $stockId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->stockId;
    }

    /**
     * @param int $stockId
     */
    private function assertIsGreaterThanZero(int $stockId): void
    {
        if (0 > $stockId) {
            throw new ProductStockConstraintException(
                sprintf('Stock id %s is invalid. Stock id must be number that is greater than zero.', var_export($stockId, true)),
                ProductStockConstraintException::INVALID_ID
            );
        }
    }
}
