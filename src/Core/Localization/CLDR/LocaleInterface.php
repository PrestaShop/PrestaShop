<?php
/**
 * Created by PhpStorm.
 * User: thomasleviandier
 * Date: 2019-01-15
 * Time: 10:51
 */

namespace PrestaShop\PrestaShop\Core\Localization\CLDR;


use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;

/**
 * CLDR Locale entity. This is an immutable data object.
 *
 * This class represents the immutable object of CLDR data for a specific locale, translated in a given language.
 * It is the only data object visible and handleable by "outside" code (meaning non-CLDR code).
 */
interface LocaleInterface
{
    /**
     * Get the code of this Locale (simplified IETF notation).
     *
     * @return string
     *                The locale code
     */
    public function getLocaleCode();

    /**
     * Get all available numbering systems for this locale.
     *
     * @return string[]
     */
    public function getNumberingSystems();

    /**
     * Get the default numbering system for this locale.
     *
     * @return string
     */
    public function getDefaultNumberingSystem();

    /**
     * Get the minimum grouping digits number when formatting numbers for this locale.
     *
     * @return int
     */
    public function getMinimumGroupingDigits();

    /**
     * Get all available number symbols lists, by numbering system.
     *
     * @return NumberSymbolsData[]
     *                             All number symbols lists (by numbering system)
     */
    public function getAllNumberSymbols();

    /**
     * Get the number symbols to use for a given numbering system.
     *
     * @param string|null $numberingSystem
     *                                     The numbering system of the wanted symbols set.
     *                                     If null, the default numbering system of this locale will be used.
     *
     * @return NumberSymbolsData
     *                           The wanted number symbols
     *
     * @throws LocalizationException
     *                               When passed $numberingSystem is invalid
     */
    public function getNumberSymbolsByNumberingSystem($numberingSystem = null);

    /**
     * Get the pattern to use when formatting a decimal number (for a given numbering system).
     *
     * @param string|null $numberingSystem
     *                                     The numbering system of the wanted symbols set.
     *                                     If null, the default numbering system of this locale will be used.
     *
     * @return string
     *                The decimal pattern
     *
     * @throws LocalizationException
     *                               When passed numbering system is invalid
     */
    public function getDecimalPattern($numberingSystem = null);

    /**
     * Get the pattern to use when formatting a percentage (for a given numbering system).
     *
     * @param string|null $numberingSystem
     *                                     The numbering system of the wanted symbols set.
     *                                     If null, the default numbering system of this locale will be used.
     *
     * @return string
     *                The percent pattern
     *
     * @throws LocalizationException
     *                               When passed numbering system is invalid
     */
    public function getPercentPattern($numberingSystem = null);

    /**
     * Get the pattern to use when formatting a price (for a given numbering system).
     *
     * @param string|null $numberingSystem
     *                                     The numbering system of the wanted symbols set.
     *                                     If null, the default numbering system of this locale will be used.
     *
     * @return string
     *                The currency pattern
     *
     * @throws LocalizationException
     *                               When passed numbering system is invalid
     */
    public function getCurrencyPattern($numberingSystem = null);

    /**
     * Get a given CLDR Currency.
     *
     * @param string $currencyCode An ISO 4217 currency code
     *
     * @return null|CurrencyInterface The wanted CLDR Currency. Null if this currency is not available for this locale.
     */
    public function getCurrency($currencyCode);

    /**
     * Get CLDR data of a given currency.
     *
     * @param string $currencyCode
     *                             An ISO 4217 currency code
     *
     * @return null|CurrencyData
     *                           The wanted currency data. Null if this currency is not available for this locale.
     */
    public function getCurrencyData($currencyCode);
}
