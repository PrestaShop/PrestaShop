<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerConstraintException;

/**
 * Provides manufacturer id
 */
class ManufacturerId implements ManufacturerIdInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @param int $id
     *
     * @throws ManufacturerConstraintException
     */
    public function __construct($id)
    {
        $this->assertIsIntegerGreaterThanZero($id);
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->id;
    }

    /**
     * Validates that the value is integer and is greater than zero
     *
     * @param int $value
     *
     * @throws ManufacturerConstraintException
     */
    private function assertIsIntegerGreaterThanZero($value)
    {
        if (!is_int($value) || 0 >= $value) {
            throw new ManufacturerConstraintException(
                sprintf('Invalid manufacturer id "%s".', var_export($value, true)),
                ManufacturerConstraintException::INVALID_ID
            );
        }
    }
}
