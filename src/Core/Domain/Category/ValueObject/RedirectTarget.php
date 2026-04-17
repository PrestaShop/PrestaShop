<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Category\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;

/**
 * Represent category id to which customer should be redirected in case category is disabled
 */
class RedirectTarget
{
    public const NO_TARGET = 0;

    private int $value;

    /**
     * @param int $value
     *
     * @throws CategoryConstraintException
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

    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @throws CategoryConstraintException
     */
    private function assertTargetValueIsValid(int $value): void
    {
        if ($value === static::NO_TARGET) {
            return;
        }

        if ($value <= 0) {
            throw new CategoryConstraintException(
                sprintf('Invalid redirect target "%d". It cannot be less than or equal to 0', $value),
                CategoryConstraintException::INVALID_REDIRECT_TARGET
            );
        }
    }
}
