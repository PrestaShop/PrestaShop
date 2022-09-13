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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Localization\Specification;

use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;

/**
 * Number's symbols data object. Regroups all symbols used when formatting a number
 * (decimal separator, thousands separator, etc.).
 */
class NumberSymbolList
{
    /**
     * Decimal separator character.
     *
     * Separates the integer and fractional part of the number.
     *
     * @var string|null
     */
    protected $decimal;

    /**
     * Digits group separator character.
     *
     * separates clusters of integer digits to make large numbers more legible; commonly used for thousands(grouping
     * size 3, e.g. "100,000,000") or in some locales, ten-thousands (grouping size 4, e.g. "1,0000,0000").
     *
     * @var string|null
     */
    protected $group;

    /**
     * List elements separator character.
     *
     * Symbol used to separate numbers in a list intended to represent structured data such as an array.
     *
     * @var string|null
     */
    protected $list;

    /**
     * Percent sign character.
     *
     * Used to indicate a percentage (1/100th) amount.
     *
     * @var string|null
     */
    protected $percentSign;

    /**
     * Minus sign character.
     *
     * Symbol used to denote negative value.
     *
     * @var string|null
     */
    protected $minusSign;

    /**
     * Plus sign character.
     *
     * Symbol used to denote positive value.
     * It can be used to produce modified patterns, so that 3.12 is formatted as "+3.12", for example.
     *
     * @var string|null
     */
    protected $plusSign;

    /**
     * Exponential character.
     *
     * Symbol separating the mantissa and exponent values.
     *
     * @var string|null
     */
    protected $exponential;

    /**
     * Superscripting exponent character.
     *
     * Used in numbers to show a format like "1.23 × 10^4"
     * (exponential character is a shortcut for "× 10^n" notation)
     *
     * @var string|null
     */
    protected $superscriptingExponent;

    /**
     * Permille sign character.
     *
     * Used to define them as a per-mille (1/1000th) amount.
     *
     * @var string|null
     */
    protected $perMille;

    /**
     * The infinity sign. Corresponds to the IEEE infinity bit pattern.
     *
     * @var string|null
     */
    protected $infinity;

    /**
     * The NaN (Not A Number) sign. Corresponds to the IEEE NaN bit pattern.
     *
     * @var string|null
     */
    protected $nan;

    /**
     * NumberSymbolList constructor.
     *
     * @param string $decimal Decimal separator character
     * @param string $group Digits group separator character
     * @param string $list List elements separator character
     * @param string $percentSign Percent sign character
     * @param string $minusSign Minus sign character
     * @param string $plusSign Plus sign character
     * @param string $exponential Exponential character
     * @param string $superscriptingExponent Superscripting exponent character
     * @param string $perMille Permille sign character
     * @param string $infinity The infinity sign. Corresponds to the IEEE infinity bit pattern.
     * @param string $nan The NaN (Not A Number) sign. Corresponds to the IEEE NaN bit pattern.
     *
     * @throws LocalizationException
     */
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
        $this->decimal = $decimal;
        $this->group = $group;
        $this->list = $list;
        $this->percentSign = $percentSign;
        $this->minusSign = $minusSign;
        $this->plusSign = $plusSign;
        $this->exponential = $exponential;
        $this->superscriptingExponent = $superscriptingExponent;
        $this->perMille = $perMille;
        $this->infinity = $infinity;
        $this->nan = $nan;

        $this->validateData();
    }

    /**
     * Get the decimal separator.
     *
     * @return string
     */
    public function getDecimal()
    {
        return $this->decimal;
    }

    /**
     * Get the digit groups separator.
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Get the list elements separator.
     *
     * @return string
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * Get the percent sign.
     *
     * @return string
     */
    public function getPercentSign()
    {
        return $this->percentSign;
    }

    /**
     * Get the minus sign.
     *
     * @return string
     */
    public function getMinusSign()
    {
        return $this->minusSign;
    }

    /**
     * Get the plus sign.
     *
     * @return string
     */
    public function getPlusSign()
    {
        return $this->plusSign;
    }

    /**
     * Get the exponential character.
     *
     * @return string
     */
    public function getExponential()
    {
        return $this->exponential;
    }

    /**
     * Get the exponent character.
     *
     * @return string
     */
    public function getSuperscriptingExponent()
    {
        return $this->superscriptingExponent;
    }

    /**
     * Gert the per mille symbol (often "‰").
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
     * Get the infinity symbol (often "∞").
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

    /**
     * Symbols list validation.
     *
     * @throws LocalizationException
     */
    protected function validateData()
    {
        if (!isset($this->decimal)
            || !is_string($this->decimal)
        ) {
            throw new LocalizationException('Invalid decimal : ' . print_r($this->decimal, true));
        }

        if (!isset($this->group)
            || !is_string($this->group)
        ) {
            throw new LocalizationException('Invalid group : ' . print_r($this->group, true));
        }

        if (!isset($this->list)
            || !is_string($this->list)
        ) {
            throw new LocalizationException('Invalid symbols list : ' . print_r($this->list, true));
        }

        if (!isset($this->percentSign)
            || !is_string($this->percentSign)
        ) {
            throw new LocalizationException('Invalid percentSign : ' . print_r($this->percentSign, true));
        }

        if (!isset($this->minusSign)
            || !is_string($this->minusSign)
        ) {
            throw new LocalizationException('Invalid minusSign : ' . print_r($this->minusSign, true));
        }

        if (!isset($this->plusSign)
            || !is_string($this->plusSign)
        ) {
            throw new LocalizationException('Invalid plusSign : ' . print_r($this->plusSign, true));
        }

        if (!isset($this->exponential)
            || !is_string($this->exponential)
        ) {
            throw new LocalizationException('Invalid exponential : ' . print_r($this->exponential, true));
        }

        if (!isset($this->superscriptingExponent)
            || !is_string($this->superscriptingExponent)
        ) {
            throw new LocalizationException('Invalid superscriptingExponent : ' . print_r($this->superscriptingExponent, true));
        }

        if (!isset($this->perMille)
            || !is_string($this->perMille)
        ) {
            throw new LocalizationException('Invalid perMille : ' . print_r($this->perMille, true));
        }

        if (!isset($this->infinity)
            || !is_string($this->infinity)
        ) {
            throw new LocalizationException('Invalid infinity : ' . print_r($this->infinity, true));
        }

        if (!isset($this->nan)
            || !is_string($this->nan)
        ) {
            throw new LocalizationException('Invalid nan : ' . print_r($this->nan, true));
        }
    }

    /**
     * To array function
     *
     * @return array
     */
    public function toArray()
    {
        return [
            $this->getDecimal(),
            $this->getGroup(),
            $this->getList(),
            $this->getPercentSign(),
            $this->getMinusSign(),
            $this->getPlusSign(),
            $this->getExponential(),
            $this->getSuperscriptingExponent(),
            $this->getPerMille(),
            $this->getInfinity(),
            $this->getNaN(),
        ];
    }
}
