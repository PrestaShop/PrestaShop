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

namespace PrestaShopBundle\Localization\Specification;

/**
 * Number's symbols data bag. Regroups all symbols used when formatting a number
 * (decimal separator, thousands separator, etc.).
 */
class NumberSymbolList
{
    public function __construct(
        $decimal,
        $group,
        $list,
        $percentSign,
        $minusSign,
        $plusSign,
        $exponential,
        $superscriptingExponent,
        $perMille,
        $infinity,
        $nan
    ) {
        $this->decimal                = $decimal;
        $this->group                  = $group;
        $this->list                   = $list;
        $this->percentSign            = $percentSign;
        $this->minusSign              = $minusSign;
        $this->plusSign               = $plusSign;
        $this->exponential            = $exponential;
        $this->superscriptingExponent = $superscriptingExponent;
        $this->perMille               = $perMille;
        $this->infinity               = $infinity;
        $this->nan                    = $nan;
    }

    /**
     * Decimal separator character
     *
     * Separates the integer and fractional part of the number.
     *
     * @var string
     */
    protected $decimal;

    /**
     * Digits group separator character
     *
     * separates clusters of integer digits to make large numbers more legible; commonly used for thousands(grouping
     * size 3, e.g. "100,000,000") or in some locales, ten-thousands (grouping size 4, e.g. "1,0000,0000").
     *
     * @var string
     */
    protected $group;

    /**
     * List elements separator character
     *
     * Symbol used to separate numbers in a list intended to represent structured data such as an array.
     *
     * @var string
     */
    protected $list;

    /**
     * Percent sign character
     *
     * Used to indicate a percentage (1/100th) amount.
     *
     * @var string
     */
    protected $percentSign;

    /**
     * Minus sign character
     *
     * Symbol used to denote negative value.
     *
     * @var string
     */
    protected $minusSign;

    /**
     * Plus sign character
     *
     * Symbol used to denote positive value.
     * It can be used to produce modified patterns, so that 3.12 is formatted as "+3.12", for example.
     *
     * @var string
     */
    protected $plusSign;

    /**
     * Exponential character
     *
     * Symbol separating the mantissa and exponent values.
     *
     * @var string
     */
    protected $exponential;

    /**
     * Superscripting exponent character
     *
     * Used in numbers to show a format like "1.23 × 10^4"
     * (exponential character is a shortcut for "× 10^n" notation)
     *
     * @var string
     */
    protected $superscriptingExponent;

    /**
     * Permille sign character
     *
     * Used to define them as a per-mille (1/1000th) amount.
     *
     * @var string
     */
    protected $perMille;

    /**
     * The infinity sign. Corresponds to the IEEE infinity bit pattern.
     *
     * @var string
     */
    protected $infinity;

    /**
     * The NaN (Not A Number) sign. Corresponds to the IEEE NaN bit pattern.
     *
     * @var string
     */
    protected $nan;

    /**
     * Optional. If specified, then for currency formatting/parsing this is used as the decimal separator instead of
     * using the regular decimal separator; otherwise, the regular decimal separator is used.
     *
     * @var string
     */
    protected $currencyDecimal;

    /**
     * Optional. If specified, then for currency formatting/parsing this is used as the group separator instead of
     * using the regular group separator; otherwise, the regular group separator is used.
     *
     * @var string
     */
    protected $currencyGroup;

    /**
     * Get the decimal separator
     *
     * @return string
     */
    public function getDecimal()
    {
        return $this->decimal;
    }

    /**
     * Get the digit groups separator
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Get the list elements separator
     *
     * @return string
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * Get the percent sign
     *
     * @return string
     */
    public function getPercentSign()
    {
        return $this->percentSign;
    }

    /**
     * Get the minus sign
     *
     * @return string
     */
    public function getMinusSign()
    {
        return $this->minusSign;
    }

    /**
     * Get the plus sign
     *
     * @return string
     */
    public function getPlusSign()
    {
        return $this->plusSign;
    }

    /**
     * Get the exponential character
     *
     * @return string
     */
    public function getExponential()
    {
        return $this->exponential;
    }

    /**
     * Get the exponent character
     *
     * @return string
     */
    public function getSuperscriptingExponent()
    {
        return $this->superscriptingExponent;
    }

    /**
     * Gert the per mille symbol (often "‰")
     *
     * @see https://en.wikipedia.org/wiki/Per_mille
     *
     * @return string
     */
    public function getPerMille()
    {
        return $this->perMille;
    }

    /**
     * Get the infinity symbol (often "∞")
     *
     * @see https://en.wikipedia.org/wiki/Infinity_symbol
     *
     * @return string
     */
    public function getInfinity()
    {
        return $this->infinity;
    }

    /**
     * Get the NaN (not a number) sign.
     *
     * @return string
     */
    public function getNan()
    {
        return $this->nan;
    }
}
