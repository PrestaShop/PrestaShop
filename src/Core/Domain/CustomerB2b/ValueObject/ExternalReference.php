<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerB2b\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Exception\CustomerB2bConstraintException;

/**
 * Reference of the customer in an external system, such as an ERP.
 */
final class ExternalReference
{
    public const MAX_LENGTH = 255;

    /**
     * @throws CustomerB2bConstraintException
     */
    public function __construct(
        private readonly string $value,
    ) {
        $this->assertLengthIsValid($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function assertLengthIsValid(string $value): void
    {
        if ('' !== $value && mb_strlen($value) <= self::MAX_LENGTH) {
            return;
        }

        throw new CustomerB2bConstraintException(\sprintf('External reference must contain from 1 to %d characters.', self::MAX_LENGTH), CustomerB2bConstraintException::INVALID_EXTERNAL_REF);
    }
}
