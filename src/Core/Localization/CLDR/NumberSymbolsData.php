<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Localization\CLDR;

/**
 * Number's symbols data object. Regroups all symbols used when formatting a number
 * (decimal separator, thousands separator, etc.).
 */
class NumberSymbolsData
{
    /**
     * Decimal separator character
     *
     * Separates the integer and fractional part of the number.
     *
     * @var string
     */
    public $decimal;

    /**
     * Digits group separator character
     *
     * separates clusters of integer digits to make large numbers more legible; commonly used for thousands(grouping
     * size 3, e.g. "100,000,000") or in some locales, ten-thousands (grouping size 4, e.g. "1,0000,0000").
     *
     * @var string
     */
    public $group;

    /**
     * List elements separator character
     *
     * Symbol used to separate numbers in a list intended to represent structured data such as an array.
     *
     * @var string
     */
    public $list;

    /**
     * Percent sign character
     *
     * Used to indicate a percentage (1/100th) amount.
     *
     * @var string
     */
    public $percentSign;

    /**
     * Minus sign character
     *
     * Symbol used to denote negative value.
     *
     * @var string
     */
    public $minusSign;

    /**
     * Plus sign character
     *
     * Symbol used to denote positive value.
     * It can be used to produce modified patterns, so that 3.12 is formatted as "+3.12", for example.
     *
     * @var string
     */
    public $plusSign;

    /**
     * Exponential character
     *
     * Symbol separating the mantissa and exponent values.
     *
     * @var string
     */
    public $exponential;

    /**
     * Superscripting exponent character
     *
     * Used in numbers to show a format like "1.23 × 10^4"
     * (exponential character is a shortcut for "× 10^n" notation)
     *
     * @var string
     */
    public $superscriptingExponent;

    /**
     * Permille sign character
     *
     * Used to define them as a per-mille (1/1000th) amount.
     *
     * @var string
     */
    public $perMille;

    /**
     * The infinity sign. Corresponds to the IEEE infinity bit pattern.
     *
     * @var string
     */
    public $infinity;

    /**
     * The NaN (Not A Number) sign. Corresponds to the IEEE NaN bit pattern.
     *
     * @var string
     */
    public $nan;

    /**
     * Separator used in date-time formatting.
     *
     * eg.: ":" => 20:00:00 (latn)
     * eg.: "," => 20,00,00 (arab)
     *
     * @var string
     */
    public $timeSeparator;

    /**
     * Will be set when decimal separator is different when formatting a price
     *
     * @var string
     */
    public $currencyDecimal;

    /**
     * Will be set when digits grouping is different when formatting a price
     *
     * @var string
     */
    public $currencyGroup;

    /**
     * Override this object's symbols with another NumberSymbolsData object
     *
     * @param NumberSymbolsData $symbolsData
     *  Symbols to use for the override
     *
     * @return $this
     *  Fluent interface
     */
    public function override(NumberSymbolsData $symbolsData)
    {
        if (isset($symbolsData->decimal)) {
            $this->decimal = $symbolsData->decimal;
        }

        if (isset($symbolsData->group)) {
            $this->group = $symbolsData->group;
        }

        if (isset($symbolsData->list)) {
            $this->list = $symbolsData->list;
        }

        if (isset($symbolsData->percentSign)) {
            $this->percentSign = $symbolsData->percentSign;
        }

        if (isset($symbolsData->minusSign)) {
            $this->minusSign = $symbolsData->minusSign;
        }

        if (isset($symbolsData->plusSign)) {
            $this->plusSign = $symbolsData->plusSign;
        }

        if (isset($symbolsData->exponential)) {
            $this->exponential = $symbolsData->exponential;
        }

        if (isset($symbolsData->superscriptingExponent)) {
            $this->superscriptingExponent = $symbolsData->superscriptingExponent;
        }

        if (isset($symbolsData->perMille)) {
            $this->perMille = $symbolsData->perMille;
        }

        if (isset($symbolsData->infinity)) {
            $this->infinity = $symbolsData->infinity;
        }

        if (isset($symbolsData->nan)) {
            $this->nan = $symbolsData->nan;
        }

        if (isset($symbolsData->timeSeparator)) {
            $this->timeSeparator = $symbolsData->timeSeparator;
        }

        if (isset($symbolsData->currencyDecimal)) {
            $this->currencyDecimal = $symbolsData->currencyDecimal;
        }

        if (isset($symbolsData->currencyGroup)) {
            $this->currencyGroup = $symbolsData->currencyGroup;
        }

        return $this;
    }
}
