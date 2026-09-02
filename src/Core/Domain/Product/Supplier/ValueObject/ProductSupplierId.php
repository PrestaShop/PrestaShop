<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierConstraintException;

/**
 * Holds product supplier identification value
 */
class ProductSupplierId
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->assertValueIsGreaterThanZero($value);
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     *
     * @throws ProductSupplierConstraintException
     */
    private function assertValueIsGreaterThanZero(int $value): void
    {
        if (0 >= $value) {
            throw new ProductSupplierConstraintException(
                sprintf('Invalid product supplier id %d', $value),
                ProductSupplierConstraintException::INVALID_ID
            );
        }
    }
}
