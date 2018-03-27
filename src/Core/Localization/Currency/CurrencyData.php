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

namespace PrestaShop\PrestaShop\Core\Localization\Currency;

/**
 * Localization Currency data object
 *
 * This class is only used internally, it is mutable and overridable until fully built. It can then be used as
 * an intermediary data bag to build a real Localization/Currency (immutable) object.
 */
class CurrencyData
{
    /**
     * Is this currency active ?
     *
     * @var bool
     */
    public $isActive;

    /**
     * Conversion rate of this currency against the default shop's currency
     *
     * Price in currency A * currency A's conversion rate = price in default currency
     *
     * Example:
     * Given the Euro as default shop's currency,
     * If 1 dollar = 1.31 euros,
     * Then conversion rate for Dollar will be 1.31
     *
     * @var float
     */
    public $conversionRate;

    /**
     * Currency's alphabetic ISO code (ISO 4217)
     *
     * @see https://www.iso.org/iso-4217-currency-codes.html
     *
     * @var string
     */
    public $isoCode;

    /**
     * Currency's numeric ISO code (ISO 4217)
     *
     * @see https://www.iso.org/iso-4217-currency-codes.html
     *
     * @var string
     */
    public $numericIsoCode;

    /**
     * Currency's symbols, by locale code
     *
     * eg.: $symbolsUSD = [
     *     'en-US' => '$',
     *     'es-CO' => 'US$', // In Colombia, colombian peso's symbol is "$". They have to differentiate foreign dollars.
     * ]
     *
     * @var string[]
     */
    public $symbols;

    /**
     * Number of decimal digits to use with this currency
     *
     * @var int
     */
    public $precision;

    /**
     * the currency's name, by locale code
     *
     * @var string[]
     */
    public $names;

    public function overrideWith(CurrencyData $currencyData)
    {
        if (isset($currencyData->isActive)) {
            $this->isActive = $currencyData->isActive;
        }

        if (isset($currencyData->conversionRate)) {
            $this->conversionRate = $currencyData->conversionRate;
        }

        if (isset($currencyData->isoCode)) {
            $this->isoCode = $currencyData->isoCode;
        }

        if (isset($currencyData->numericIsoCode)) {
            $this->numericIsoCode = $currencyData->numericIsoCode;
        }

        if (isset($currencyData->symbols)) {
            foreach ($currencyData->symbols as $type => $symbol) {
                $this->symbols[$type] = $symbol;
            }
        }

        if (isset($currencyData->precision)) {
            $this->precision = $currencyData->precision;
        }

        if (isset($currencyData->names)) {
            foreach ($currencyData->names as $localeCode => $name) {
                $this->names[$localeCode] = $name;
            }
        }

        return $this;
    }
}
