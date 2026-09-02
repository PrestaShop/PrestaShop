<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;

/**
 * Class SupplierId
 */
class SupplierId implements SupplierIdInterface
{
    /**
     * @var int
     */
    private $value;

    /**
     * @param int $supplierId
     *
     * @throws SupplierException
     */
    public function __construct(int $supplierId)
    {
        $this->assertIsIntegerGreaterThanZero($supplierId);
        $this->value = $supplierId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $supplierId
     *
     * @throws SupplierException
     */
    private function assertIsIntegerGreaterThanZero(int $supplierId)
    {
        if (0 >= $supplierId) {
            throw new SupplierException(sprintf('Invalid Supplier id: %s', var_export($supplierId, true)));
        }
    }
}
