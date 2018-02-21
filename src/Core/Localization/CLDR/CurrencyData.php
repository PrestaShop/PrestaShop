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

class CurrencyData
{
    /**
     * Alphabetic ISO 4217 currency code
     *
     * @var string
     */
    public $isoCode;

    /**
     * Numeric ISO 4217 currency code
     *
     * @var string
     */
    public $numericIsoCode;

    /**
     * Number of decimal digits to display for a price in this currency
     *
     * @var int
     */
    public $decimalDigits;

    /**
     * Possible names depending on count context.
     *
     * e.g. : "Used currency is dollar" (default), "I need one dollar" (one), "I need five dollars" (other)
     * [
     *     'default' => 'dollar',
     *     'one'     => 'dollar',
     *     'other'   => 'dollars',
     * ]
     *
     * @var string[]
     */
    public $displayNames;

    /**
     * Possible symbols (PrestaShop is using narrow)
     *
     * e.g.:
     * [
     *     'default' => 'US$',
     *     'narrow' => '$',
     * ]
     *
     * @var string[]
     */
    public $symbols;

    public function overrideWith(CurrencyData $currencyData)
    {
        if (isset($currencyData->isoCode)) {
            $this->isoCode = $currencyData->isoCode;
        }

        if (isset($currencyData->numericIsoCode)) {
            $this->numericIsoCode = $currencyData->numericIsoCode;
        }

        if (isset($currencyData->decimalDigits)) {
            $this->decimalDigits = $currencyData->decimalDigits;
        }

        if (isset($currencyData->displayNames)) {
            foreach ($currencyData->displayNames as $countContext => $name) {
                $this->displayNames[$countContext] = $name;
            }
        }

        if (isset($currencyData->symbols)) {
            foreach ($currencyData->symbols as $type => $symbol) {
                $this->symbols[$type] = $symbol;
            }
        }

        return $this;
    }
}
