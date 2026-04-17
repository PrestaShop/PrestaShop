<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

class Gtin
{
    /**
     * Valid gtin regex pattern
     */
    public const VALID_PATTERN = '/^[0-9]{0,14}$/';

    /**
     * Maximum allowed symbols
     */
    public const MAX_LENGTH = 14;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->assertGtinIsValid($value);
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @throws ProductConstraintException
     */
    private function assertGtinIsValid(string $value): void
    {
        if (strlen($value) <= self::MAX_LENGTH && preg_match(self::VALID_PATTERN, $value)) {
            return;
        }

        throw new ProductConstraintException(
            sprintf(
                'Invalid GTIN "%s". It should match pattern "%s" and cannot exceed %s symbols',
                $value,
                self::VALID_PATTERN,
                self::MAX_LENGTH
            ),
            ProductConstraintException::INVALID_GTIN
        );
    }
}
