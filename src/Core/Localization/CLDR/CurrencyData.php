<?php

/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Localization\CLDR;

/**
 * The CurrencyData class is the exact representation of Currency's data structure inside CLDR xml data files.
 *
 * This class is only used internally, it is mutable and overridable until fully built. It can then be used as
 * an intermediary data bag to build a real CLDR Currency (immutable) object.
 */
class CurrencyData
{
    /**
     * Alphabetic ISO 4217 currency code.
     *
     * @var string
     */
    protected $isoCode;

    /**
     * Numeric ISO 4217 currency code.
     *
     * @var string
     */
    protected $numericIsoCode;

    /**
     * Number of decimal digits to display for a price in this currency.
     *
     * @var int
     */
    protected $decimalDigits;

    /**
     * Possible names depending on count context.
     *
     * e.g.: "Used currency is dollar" (default), "I need one dollar" (one), "I need five dollars" (other)
     * [
     *     'default' => 'dollar',
     *     'one'     => 'dollar',
     *     'other'   => 'dollars',
     * ]
     *
     * @var string[]
     */
    protected $displayNames;

    /**
     * Possible symbols (PrestaShop is using narrow).
     *
     * e.g.:
     * [
     *     'default' => 'US$',
     *     'narrow' => '$',
     * ]
     *
     * @var string[]
     */
    protected $symbols;

    /**
     * Is the currency used somewhere, or was it deactivated in all territories
     *
     * @var bool|null
     */
    protected $active;

    /**
     * Override this object's data with another CurrencyData object.
     *
     * @param CurrencyData $currencyData
     *                                   Currency data to use for the override
     *
     * @return $this
     *               Fluent interface
     */
    public function overrideWith(CurrencyData $currencyData)
    {
        if (null !== $currencyData->getIsoCode()) {
            $this->setIsoCode($currencyData->getIsoCode());
        }

        if (null !== $currencyData->getNumericIsoCode()) {
            $this->setNumericIsoCode($currencyData->getNumericIsoCode());
        }

        if (null !== $currencyData->isActive()) {
            $this->setActive($currencyData->isActive());
        }

        if (null !== $currencyData->getDecimalDigits()) {
            $this->setDecimalDigits($currencyData->getDecimalDigits());
        }

        if (null !== $currencyData->getDisplayNames()) {
            if (null === $this->displayNames) {
                $this->displayNames = [];
            }
            $this->displayNames = array_merge($this->displayNames, $currencyData->getDisplayNames());
        }

        if (null !== $currencyData->getSymbols()) {
            if (null === $this->symbols) {
                $this->symbols = [];
            }
            $this->symbols = array_merge($this->symbols, $currencyData->getSymbols());
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * @param string $isoCode
     *
     * @return CurrencyData
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getNumericIsoCode()
    {
        return $this->numericIsoCode;
    }

    /**
     * @param string $numericIsoCode
     *
     * @return CurrencyData
     */
    public function setNumericIsoCode($numericIsoCode)
    {
        $this->numericIsoCode = $numericIsoCode;

        return $this;
    }

    /**
     * @return int
     */
    public function getDecimalDigits()
    {
        return $this->decimalDigits;
    }

    /**
     * @param int $decimalDigits
     *
     * @return CurrencyData
     */
    public function setDecimalDigits($decimalDigits)
    {
        $this->decimalDigits = $decimalDigits;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getDisplayNames()
    {
        return $this->displayNames;
    }

    /**
     * @param string[] $displayNames
     *
     * @return CurrencyData
     */
    public function setDisplayNames($displayNames)
    {
        $this->displayNames = $displayNames;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getSymbols()
    {
        return $this->symbols;
    }

    /**
     * @param string[] $symbols
     *
     * @return CurrencyData
     */
    public function setSymbols($symbols)
    {
        $this->symbols = $symbols;

        return $this;
    }

    /**
     * is currency still active in some territory
     *
     * @return bool|null
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = (bool) $active;
    }
}
