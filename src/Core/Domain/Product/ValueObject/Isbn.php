<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Holds product ISBN code value
 */
class Isbn
{
    /**
     * Valid ISBN regex pattern
     * Source : https://www.oreilly.com/library/view/regular-expressions-cookbook/9781449327453/ch04s13.html
     */
    public const VALID_PATTERN = '/' .
        '^' .
        '(?:ISBN(?:-1[03])?:? )?' . // Optional ISBN/ISBN-10/ISBN-13 identifier.
        '(?=[0-9X]{10}$' .          // Require 10 digits/Xs (no separators).
        '|' .                       // Or:
        '(?=(?:[0-9]+[- ]){3})' .   // Require 3 separators
        '[- 0-9X]{13}$' .           // Out of 13 characters total.
        '|' .                       // Or:
        '97[89][0-9]{10}$' .        // 978/979 plus 10 digits (13 total).
        '|' .                       // Or:
        '(?=(?:[0-9]+[- ]){4})' .   // Require 4 separators
        '[- 0-9]{17}$' .            // Out of 17 characters total.
        ')' .                       // End format pre-checks.
        '(?:97[89][- ]?)?' .        // Optional ISBN-13 prefix.
        '[0-9]{1,5}[- ]?' .         // 1-5 digit group identifier.
        '[0-9]+[- ]?[0-9]+[- ]?' .  // Publisher and title identifiers.
        '[0-9X]' .                  // Check digit.
        '$' .
        '/i';                       // Case insensitive.

    /**
     * Maximum allowed symbols
     */
    public const MAX_LENGTH = 32;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->assertIsbnIsValid($value);
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
    private function assertIsbnIsValid(string $value): void
    {
        if ((strlen($value) <= self::MAX_LENGTH && preg_match(self::VALID_PATTERN, $value)) || !$value) {
            return;
        }

        throw new ProductConstraintException(
            sprintf(
                'Invalid ISBN "%s". It should match pattern "%s" and cannot exceed %s symbols',
                $value,
                self::VALID_PATTERN,
                self::MAX_LENGTH
            ),
            ProductConstraintException::INVALID_ISBN
        );
    }
}
