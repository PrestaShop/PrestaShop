<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Represents product or category id to which customer should be redirected in case product is disabled
 */
class RedirectTarget
{
    public const NO_TARGET = 0;

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     *
     * @throws ProductConstraintException
     */
    public function __construct(int $value)
    {
        $this->assertTargetValueIsValid($value);
        $this->value = $value;
    }

    public function isNoTarget(): bool
    {
        return $this->value === static::NO_TARGET;
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
     * @throws ProductConstraintException
     */
    private function assertTargetValueIsValid(int $value): void
    {
        if ($value === static::NO_TARGET) {
            return;
        }

        if ($value <= 0) {
            throw new ProductConstraintException(
                sprintf('Invalid redirect target "%d". It cannot be less than or equal to 0', $value),
                ProductConstraintException::INVALID_REDIRECT_TARGET
            );
        }
    }
}
