<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
