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

/**
 * Created by PhpStorm.
 * User: thomasleviandier
 * Date: 2018-12-20
 * Time: 16:44
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
interface CurrencyInterface
{
    public const SYMBOL_TYPE_NARROW = 'narrow';
    public const DISPLAY_NAME_COUNT_DEFAULT = 'default';
    public const SYMBOL_TYPE_DEFAULT = 'default';
    public const DISPLAY_NAME_COUNT_ONE = 'one';
    public const DISPLAY_NAME_COUNT_OTHER = 'other';

    /**
     * Get the ISO code of this currency.
     *
     * @return string
     *                The currency's ISO 4217 code
     */
    public function getIsoCode();

    /**
     * Get the numeric ISO code of this currency.
     *
     * @return string
     *                The currency's ISO 4217 numeric code
     */
    public function getNumericIsoCode();

    /**
     * Get the number of decimal digits to display when formatting a price with this currency.
     *
     * @return int
     *             The number of decimal digits to display
     */
    public function getDecimalDigits();

    /**
     * Get the display name for the passed count context.
     *
     * @param string $countContext
     *                             The count context
     *                             "default" = talking about the currency (e.g.: "used currency is Euro")
     *                             "one"     = talking about one unit of this currency (e.g.: "one euro")
     *                             "other"   = talking about several units of this currency (e.g.: "ten euros")
     *
     * @return string
     *                The wanted display name
     */
    public function getDisplayName($countContext = self::DISPLAY_NAME_COUNT_DEFAULT);

    /**
     * Get the symbol of this currency. Narrow symbol is returned by default.
     *
     * @param string $type
     *                     Possible value: "default" ("$") and "narrow" ("US$")
     *
     * @return string
     *                The currency's symbol
     *
     * @throws LocalizationException
     *                               When an invalid symbol type is passed
     */
    public function getSymbol($type = self::SYMBOL_TYPE_NARROW);
}
