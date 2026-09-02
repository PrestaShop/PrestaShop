<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;

/**
 * Toggles manufacturer status
 */
class ToggleManufacturerStatusCommand
{
    /**
     * @var ManufacturerId
     */
    private $manufacturerId;

    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @param int $manufacturerId
     * @param bool $expectedStatus
     *
     * @throws ManufacturerConstraintException
     */
    public function __construct($manufacturerId, $expectedStatus)
    {
        $this->assertIsBool($expectedStatus);
        $this->expectedStatus = $expectedStatus;
        $this->manufacturerId = new ManufacturerId($manufacturerId);
    }

    /**
     * @return ManufacturerId
     */
    public function getManufacturerId()
    {
        return $this->manufacturerId;
    }

    /**
     * @return bool
     */
    public function getExpectedStatus()
    {
        return $this->expectedStatus;
    }

    /**
     * Validates that value is of type boolean
     *
     * @param bool $value
     *
     * @throws ManufacturerConstraintException
     */
    private function assertIsBool($value)
    {
        if (!is_bool($value)) {
            throw new ManufacturerConstraintException(sprintf('Status must be of type bool, but given %s', var_export($value, true)), ManufacturerConstraintException::INVALID_STATUS);
        }
    }
}
