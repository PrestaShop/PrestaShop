<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;

/**
 * Toggles manufacturer status in bulk action
 */
class BulkToggleManufacturerStatusCommand
{
    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @var ManufacturerId[]
     */
    private $manufacturerIds;

    /**
     * @param int[] $manufacturerIds
     * @param bool $expectedStatus
     *
     * @throws ManufacturerConstraintException
     * @throws ManufacturerConstraintException
     */
    public function __construct(array $manufacturerIds, $expectedStatus)
    {
        $this->assertIsBool($expectedStatus);
        $this->expectedStatus = $expectedStatus;
        $this->setManufacturerIds($manufacturerIds);
    }

    /**
     * @return bool
     */
    public function getExpectedStatus()
    {
        return $this->expectedStatus;
    }

    /**
     * @return ManufacturerId[]
     */
    public function getManufacturerIds()
    {
        return $this->manufacturerIds;
    }

    /**
     * @param int[] $manufacturerIds
     *
     * @throws ManufacturerConstraintException
     */
    private function setManufacturerIds(array $manufacturerIds)
    {
        foreach ($manufacturerIds as $manufacturerId) {
            $this->manufacturerIds[] = new ManufacturerId($manufacturerId);
        }
    }

    /**
     * Validates that value is of type boolean
     *
     * @param mixed $value
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
