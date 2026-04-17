<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;

/**
 * Out of range behavior value for Carriers
 */
class OutOfRangeBehavior
{
    /**
     * Use the highest range if we are out of range on order
     */
    public const USE_HIGHEST_RANGE = 0;

    /**
     * Disable carrier if we are out of range on order
     */
    public const DISABLED = 1;

    /**
     * A list of available values
     */
    public const AVAILABLE_VALUES = [
        self::USE_HIGHEST_RANGE,
        self::DISABLED,
    ];

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     *
     * @throws CarrierConstraintException
     */
    public function __construct(int $value)
    {
        $this->assertValue($value);
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
     * @throws CarrierConstraintException
     */
    private function assertValue(int $value): void
    {
        if (!in_array($value, self::AVAILABLE_VALUES, true)) {
            throw new CarrierConstraintException(
                sprintf(
                    'Invalid range behaviour %s. Valid types are: [%s]',
                    $value,
                    implode(',', self::AVAILABLE_VALUES)
                ),
                CarrierConstraintException::INVALID_RANGE_BEHAVIOR
            );
        }
    }
}
