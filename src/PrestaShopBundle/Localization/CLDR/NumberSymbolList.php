<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Localization\CLDR;

/**
 * Class NumberSymbolList
 *
 * Number's symbols data bag. Regroups all symbols used when formatting a number in a given locale.
 * (decimal separator, thousands separator, etc)
 *
 * @package PrestaShopBundle\Localization\CLDR
 */
class NumberSymbolList
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
     * Optional. If specified, then for currency formatting/parsing this is used as the decimal separator instead of
     * using the regular decimal separator; otherwise, the regular decimal separator is used.
     *
     * @var string
     */
    public $currencyDecimal;

    /**
     * Optional. If specified, then for currency formatting/parsing this is used as the group separator instead of
     * using the regular group separator; otherwise, the regular group separator is used.
     *
     * @var string
     */
    public $currencyGroup;

    /**
     * Time separator character
     *
     * This replaces any use of the timeSeparator pattern character in a date-time format pattern (no timeSeparator
     * pattern character is currently defined, see note below). This allows the same time format to be used for multiple
     * number systems when the time separator depends on the number system. For example, the time format for Arabic
     * should be COLON when using the Latin numbering system (0, 1, 2, …), but when the Arabic numbering system is used
     * (٠‎ - ١‎ - ٢‎ …), the traditional time separator in older print styles was often ARABIC COMMA.
     *
     * @var string
     */
    public $timeSeparator;

    /**
     * Fills missing items of this list with default data
     *
     * @param NumberSymbolList $defaultList
     *
     * @return $this
     */
    public function fill(NumberSymbolList $defaultList)
    {
        foreach (get_object_vars($this) as $property => $value) {
            if (is_null($value) && !is_null($defaultList->$property)) {
                $this->$property = $defaultList->$property;
            }
        }

        return $this;
    }
}
