<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;

/**
 * Provides address id
 */
class AddressId
{
    /**
     * @var int
     */
    private $addressId;

    /**
     * @param int $addressId
     *
     * @throws AddressConstraintException
     */
    public function __construct($addressId)
    {
        $this->assertIsIntegerGreaterThanZero($addressId);
        $this->addressId = $addressId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->addressId;
    }

    /**
     * Validates that the value is integer and is greater than zero
     *
     * @param int $value
     *
     * @throws AddressConstraintException
     */
    private function assertIsIntegerGreaterThanZero($value)
    {
        if (!is_int($value) || 0 >= $value) {
            throw new AddressConstraintException(sprintf('Invalid address id "%s".', var_export($value, true)), AddressConstraintException::INVALID_ID);
        }
    }
}
