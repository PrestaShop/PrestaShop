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

namespace PrestaShop\PrestaShop\Core\Localization\CLDR;

/**
 * Number's symbols data object. Regroups all symbols used when formatting a number
 * (decimal separator, thousands separator, etc.).
 */
class NumberSymbolsData
{
    /**
     * Decimal separator character.
     *
     * Separates the integer and fractional part of the number.
     *
     * @var string
     */
    protected $decimal;

    /**
     * Digits group separator character.
     *
     * separates clusters of integer digits to make large numbers more legible; commonly used for thousands(grouping
     * size 3, e.g. "100,000,000") or in some locales, ten-thousands (grouping size 4, e.g. "1,0000,0000").
     *
     * @var string
     */
    protected $group;

    /**
     * List elements separator character.
     *
     * Symbol used to separate numbers in a list intended to represent structured data such as an array.
     *
     * @var string
     */
    protected $list;

    /**
     * Percent sign character.
     *
     * Used to indicate a percentage (1/100th) amount.
     *
     * @var string
     */
    protected $percentSign;

    /**
     * Minus sign character.
     *
     * Symbol used to denote negative value.
     *
     * @var string
     */
    protected $minusSign;

    /**
     * Plus sign character.
     *
     * Symbol used to denote positive value.
     * It can be used to produce modified patterns, so that 3.12 is formatted as "+3.12", for example.
     *
     * @var string
     */
    protected $plusSign;

    /**
     * Exponential character.
     *
     * Symbol separating the mantissa and exponent values.
     *
     * @var string
     */
    protected $exponential;

    /**
     * Superscripting exponent character.
     *
     * Used in numbers to show a format like "1.23 × 10^4"
     * (exponential character is a shortcut for "× 10^n" notation)
     *
     * @var string
     */
    protected $superscriptingExponent;

    /**
     * Permille sign character.
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
     * Separator used in date-time formatting.
     *
     * eg.: ":" => 20:00:00 (latn)
     * eg.: "," => 20,00,00 (arab)
     *
     * @var string
     */
    protected $timeSeparator;

    /**
     * Will be set when decimal separator is different when formatting a price.
     *
     * @var string
     */
    protected $currencyDecimal;

    /**
     * Will be set when digits grouping is different when formatting a price.
     *
     * @var string
     */
    protected $currencyGroup;

    /**
     * Override this object's symbols with another NumberSymbolsData object.
     *
     * @param NumberSymbolsData $symbolsData Symbols to use for the override
     *
     * @return $this Fluent interface
     */
    public function overrideWith(NumberSymbolsData $symbolsData)
    {
        if (null !== $symbolsData->getDecimal()) {
            $this->setDecimal($symbolsData->getDecimal());
        }

        if (null !== $symbolsData->getGroup()) {
            $this->setGroup($symbolsData->getGroup());
        }

        if (null !== $symbolsData->getList()) {
            $this->setList($symbolsData->getList());
        }

        if (null !== $symbolsData->getPercentSign()) {
            $this->setPercentSign($symbolsData->getPercentSign());
        }

        if (null !== $symbolsData->getMinusSign()) {
            $this->setMinusSign($symbolsData->getMinusSign());
        }

        if (null !== $symbolsData->getPlusSign()) {
            $this->setPlusSign($symbolsData->getPlusSign());
        }

        if (null !== $symbolsData->getExponential()) {
            $this->setExponential($symbolsData->getExponential());
        }

        if (null !== $symbolsData->getSuperscriptingExponent()) {
            $this->setSuperscriptingExponent($symbolsData->getSuperscriptingExponent());
        }

        if (null !== $symbolsData->getPerMille()) {
            $this->setPerMille($symbolsData->getPerMille());
        }

        if (null !== $symbolsData->getInfinity()) {
            $this->setInfinity($symbolsData->getInfinity());
        }

        if (null !== $symbolsData->getNan()) {
            $this->setNan($symbolsData->getNan());
        }

        if (null !== $symbolsData->getTimeSeparator()) {
            $this->setTimeSeparator($symbolsData->getTimeSeparator());
        }

        if (null !== $symbolsData->getCurrencyDecimal()) {
            $this->setCurrencyDecimal($symbolsData->getCurrencyDecimal());
        }

        if (null !== $symbolsData->getCurrencyGroup()) {
            $this->setCurrencyGroup($symbolsData->getCurrencyGroup());
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDecimal()
    {
        return $this->decimal;
    }

    /**
     * @param string $decimal
     *
     * @return NumberSymbolsData
     */
    public function setDecimal($decimal)
    {
        $this->decimal = $decimal;

        return $this;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param string $group
     *
     * @return NumberSymbolsData
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return string
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param string $list
     *
     * @return NumberSymbolsData
     */
    public function setList($list)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * @return string
     */
    public function getPercentSign()
    {
        return $this->percentSign;
    }

    /**
     * @param string $percentSign
     *
     * @return NumberSymbolsData
     */
    public function setPercentSign($percentSign)
    {
        $this->percentSign = $percentSign;

        return $this;
    }

    /**
     * @return string
     */
    public function getMinusSign()
    {
        return $this->minusSign;
    }

    /**
     * @param string $minusSign
     *
     * @return NumberSymbolsData
     */
    public function setMinusSign($minusSign)
    {
        $this->minusSign = $minusSign;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlusSign()
    {
        return $this->plusSign;
    }

    /**
     * @param string $plusSign
     *
     * @return NumberSymbolsData
     */
    public function setPlusSign($plusSign)
    {
        $this->plusSign = $plusSign;

        return $this;
    }

    /**
     * @return string
     */
    public function getExponential()
    {
        return $this->exponential;
    }

    /**
     * @param string $exponential
     *
     * @return NumberSymbolsData
     */
    public function setExponential($exponential)
    {
        $this->exponential = $exponential;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuperscriptingExponent()
    {
        return $this->superscriptingExponent;
    }

    /**
     * @param string $superscriptingExponent
     *
     * @return NumberSymbolsData
     */
    public function setSuperscriptingExponent($superscriptingExponent)
    {
        $this->superscriptingExponent = $superscriptingExponent;

        return $this;
    }

    /**
     * @return string
     */
    public function getPerMille()
    {
        return $this->perMille;
    }

    /**
     * @param string $perMille
     *
     * @return NumberSymbolsData
     */
    public function setPerMille($perMille)
    {
        $this->perMille = $perMille;

        return $this;
    }

    /**
     * @return string
     */
    public function getInfinity()
    {
        return $this->infinity;
    }

    /**
     * @param string $infinity
     *
     * @return NumberSymbolsData
     */
    public function setInfinity($infinity)
    {
        $this->infinity = $infinity;

        return $this;
    }

    /**
     * @return string
     */
    public function getNan()
    {
        return $this->nan;
    }

    /**
     * @param string $nan
     *
     * @return NumberSymbolsData
     */
    public function setNan($nan)
    {
        $this->nan = $nan;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimeSeparator()
    {
        return $this->timeSeparator;
    }

    /**
     * @param string $timeSeparator
     *
     * @return NumberSymbolsData
     */
    public function setTimeSeparator($timeSeparator)
    {
        $this->timeSeparator = $timeSeparator;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrencyDecimal()
    {
        return $this->currencyDecimal;
    }

    /**
     * @param string $currencyDecimal
     *
     * @return NumberSymbolsData
     */
    public function setCurrencyDecimal($currencyDecimal)
    {
        $this->currencyDecimal = $currencyDecimal;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrencyGroup()
    {
        return $this->currencyGroup;
    }

    /**
     * @param string $currencyGroup
     *
     * @return NumberSymbolsData
     */
    public function setCurrencyGroup($currencyGroup)
    {
        $this->currencyGroup = $currencyGroup;

        return $this;
    }
}
