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

use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;

/**
 * CLDR Currency entity. This is an immutable data object.
 *
 * This class represents the immutable object of CLDR data for a specific currency, translated in a given language.
 * It is the only data object visible and handleable by "outside" code (meaning non-CLDR code).
 * CLDR Locale objects aggregate multiple CLDR Currency instances (available currencies), and return this class when
 * asked for a given currency.
 */
final class Currency implements CurrencyInterface
{
    /**
     * Alphabetic ISO 4217 currency code.
     *
     * @var string
     */
    private $isoCode;

    /**
     * Numeric ISO 4217 currency code.
     *
     * @var string
     */
    private $numericIsoCode;

    /**
     * Number of decimal digits to display for a price in this currency.
     *
     * @var int
     */
    private $decimalDigits;

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
    private $displayNames;

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
    private $symbols;

    public function __construct(CurrencyData $currencyData)
    {
        $this->isoCode = $currencyData->getIsoCode();
        $this->numericIsoCode = $currencyData->getNumericIsoCode();
        $this->decimalDigits = $currencyData->getDecimalDigits();
        $this->displayNames = $currencyData->getDisplayNames();
        $this->symbols = $currencyData->getSymbols();
    }

    /**
     * Get the ISO code of this currency.
     *
     * @return string
     *                The currency's ISO 4217 code
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * Get the numeric ISO code of this currency.
     *
     * @return string The currency's ISO 4217 numeric code
     */
    public function getNumericIsoCode()
    {
        return $this->numericIsoCode;
    }

    /**
     * Get the number of decimal digits to display when formatting a price with this currency.
     *
     * @return int The number of decimal digits to display
     */
    public function getDecimalDigits()
    {
        return $this->decimalDigits;
    }

    /**
     * Get the display name for the passed count context.
     *
     * @param string $countContext
     *                             The count context
     *                             "default" = talking about the currency (e.g.: "used currency is Euro")
     *                             "one"     = talking about one unit of this currency (e.g.: "one euro")
     *                             "other"   = talking about several units of this currency (e.g.: "ten euros")
     *
     * @return string The wanted display name
     */
    public function getDisplayName($countContext = CurrencyInterface::DISPLAY_NAME_COUNT_DEFAULT)
    {
        if (!in_array($countContext, [CurrencyInterface::DISPLAY_NAME_COUNT_DEFAULT, CurrencyInterface::DISPLAY_NAME_COUNT_ONE, CurrencyInterface::DISPLAY_NAME_COUNT_OTHER])) {
            throw new LocalizationException(
                sprintf(
                    'Unknown display name: "%s"',
                    print_r($countContext, true)
                )
            );
        }

        return $this->displayNames[$countContext];
    }

    /**
     * Get the symbol of this currency. Narrow symbol is returned by default.
     *
     * @param string $type Possible value: "default" ("$") and "narrow" ("US$")
     *
     * @return string The currency's symbol
     *
     * @throws LocalizationException When an invalid symbol type is passed
     */
    public function getSymbol($type = CurrencyInterface::SYMBOL_TYPE_NARROW)
    {
        if (!in_array($type, [CurrencyInterface::SYMBOL_TYPE_NARROW, CurrencyInterface::SYMBOL_TYPE_DEFAULT])) {
            throw new LocalizationException(
                sprintf(
                    'Unknown symbol type: "%s"',
                    print_r($type, true)
                )
            );
        }

        if (isset($this->symbols[$type])) {
            return $this->symbols[$type];
        }

        return $this->symbols[CurrencyInterface::SYMBOL_TYPE_DEFAULT];
    }
}
