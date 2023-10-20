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

namespace PrestaShopBundle\Utils;

use PrestaShop\PrestaShop\Core\Util\ArabicToLatinDigitConverter;

/**
 * Converts strings into floats.
 */
class FloatParser
{
    /**
     * @var ArabicToLatinDigitConverter
     */
    private $arabicToLatinNumberConverter;

    public function __construct(ArabicToLatinDigitConverter $arabicToLatinDigitConverter = null)
    {
        $this->arabicToLatinNumberConverter = $arabicToLatinDigitConverter ?? new ArabicToLatinDigitConverter();
    }

    /**
     * Constructs a float value from an arbitrarily-formatted string.
     *
     * This method supports any thousand and decimal separator.
     * If the string is ambiguous (e.g. 123,456) the interpreter will interpret the last group of numbers
     * as the decimal part.
     *
     * In order to prevent unexpected behavior, make sure that your value has a decimal part.
     *
     * Examples:
     * - '123,456' --> 123.456
     * - '123,456,00' --> 123456.00
     * - '12,345,678 --> 12345.678
     *
     * @param string $value
     *
     * @throws \InvalidArgumentException if the provided value is not a string
     *                                   or if it cannot be interpreted as a number
     *
     * @return float
     */
    public function fromString($value)
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException(sprintf('Invalid argument: string expected, got %s', gettype($value)));
        }

        $value = trim($value);
        if ('' === $value) {
            return 0.0;
        }

        // replace arabic numbers by latin
        $value = $this->arabicToLatinNumberConverter->convert($value);

        // remove all non-digit characters
        $split = preg_split('/[^\dE-]+/', $value);

        if (1 === count($split)) {
            // there's no decimal part
            return (float) $value;
        }

        foreach ($split as $part) {
            if ('' === $part) {
                throw new \InvalidArgumentException(sprintf('Invalid argument: "%s" cannot be interpreted as a number', $value));
            }
        }

        // use the last part as decimal
        $decimal = array_pop($split);

        // reconstruct the number using dot as decimal separator
        $value = implode('', $split) . '.' . $decimal;

        return (float) $value;
    }
}
