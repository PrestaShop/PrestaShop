<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Product identity.
 */
class ProductId
{
    /**
     * @var int
     */
    private $productId;

    /**
     * @param int $productId
     *
     * @throws ProductConstraintException
     */
    public function __construct(int $productId)
    {
        $this->assertIntegerIsGreaterThanZero($productId);
        $this->productId = $productId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->productId;
    }

    /**
     * @param int $productId
     */
    private function assertIntegerIsGreaterThanZero(int $productId): void
    {
        if ($productId > 0) {
            return;
        }

        throw new ProductConstraintException(
            sprintf('Product id %s is invalid. Product id must be an integer greater than zero.', $productId),
            ProductConstraintException::INVALID_ID
        );
    }
}
