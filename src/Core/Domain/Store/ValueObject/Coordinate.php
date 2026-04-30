<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Store\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreConstraintException;

/**
 * Encapsulates a geographic coordinate (latitude or longitude).
 * Accepts null to represent an unset coordinate.
 */
class Coordinate
{
    private ?float $value;

    public function __construct(?float $value)
    {
        if (null !== $value && !preg_match('/^-?[0-9]{1,8}\.[0-9]{1,8}$/', number_format($value, 8, '.', ''))) {
            throw new StoreConstraintException(
                sprintf('Invalid coordinate value "%s"', $value),
                StoreConstraintException::INVALID_COORDINATE
            );
        }

        $this->value = $value;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }
}
