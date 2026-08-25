<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;

/**
 * Shipping method value for Carriers
 */
class ShippingMethod
{
    /**
     * Use weight to calculate shipping cost
     */
    public const BY_WEIGHT = 1;

    /**
     * Use price to calculate shipping cost
     */
    public const BY_PRICE = 2;

    /**
     * A list of available values
     */
    public const AVAILABLE_VALUES = [
        self::BY_WEIGHT,
        self::BY_PRICE,
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
                    'Invalid shipping method %s. Valid types are: [%s]',
                    $value,
                    implode(',', self::AVAILABLE_VALUES)
                ),
                CarrierConstraintException::INVALID_SHIPPING_METHOD
            );
        }
    }
}
