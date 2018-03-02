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

use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;

class Locale
{
    /**
     * The locale code for this data (either language code or IETF tag)
     *
     * @var string
     */
    public $localeCode;

    /**
     * List of available numbering systems
     * Array of strings (codes)
     *
     * @var string[]
     */
    public $numberingSystems;

    /**
     * Default numbering system
     *
     * @var string
     */
    public $defaultNumberingSystem;

    /**
     * Used to suppress groupings below a certain value
     *
     * 1 -> grouping starts at 4 figures integers (1,000 and more)
     * 2 -> grouping starts at 5 figures integers (10,000 and more)
     *
     * @var int
     */
    public $minimumGroupingDigits;

    /**
     * Collection of all available symbols list (by numbering system)
     *
     * @var NumberSymbolsData[]
     */
    public $numberSymbols;

    /**
     * Collection of all available decimal patterns (by numbering system)
     * Array of strings (patterns)
     *
     * @var string[]
     */
    public $decimalPatterns;

    /**
     * Collection of all available percent patterns (by numbering system)
     * Array of strings (patterns)
     *
     * @var string[]
     */
    public $percentPatterns;

    /**
     * Collection of all available currency patterns (by numbering system)
     * Array of strings (patterns)
     *
     * @var string[]
     */
    public $currencyPatterns;

    /**
     * All currencies, by ISO code
     *
     * @var CurrencyData[]
     */
    public $currencies;

    public function __construct(LocaleData $localeData)
    {
        $this->localeCode             = $localeData->localeCode;
        $this->numberingSystems       = $localeData->numberingSystems;
        $this->defaultNumberingSystem = $localeData->defaultNumberingSystem;
        $this->minimumGroupingDigits  = $localeData->minimumGroupingDigits;
        $this->numberSymbols          = $localeData->numberSymbols;
        $this->decimalPatterns        = $localeData->decimalPatterns;
        $this->percentPatterns        = $localeData->percentPatterns;
        $this->currencyPatterns       = $localeData->currencyPatterns;
        $this->currencies             = $localeData->currencies;
    }

    /**
     * Get the code of this Locale (simplified IETF notation)
     *
     * @return string
     *  The locale code
     */
    public function getLocaleCode()
    {
        return $this->localeCode;
    }

    /**
     * Get all available numbering systems for this locale
     *
     * @return string[]
     */
    public function getNumberingSystems()
    {
        return $this->numberingSystems;
    }

    /**
     * Get the default numbering system for this locale
     *
     * @return string
     */
    public function getDefaultNumberingSystem()
    {
        return $this->defaultNumberingSystem;
    }

    /**
     * Get the minimum grouping digits number when formatting numbers for this locale
     *
     * @return int
     */
    public function getMinimumGroupingDigits()
    {
        return $this->minimumGroupingDigits;
    }

    /**
     * Get all available number symbols lists, by numbering system
     *
     * @return NumberSymbolsData[]
     *  All number symbols lists (by numbering system)
     */
    public function getAllNumberSymbols()
    {
        return $this->numberSymbols;
    }

    /**
     * Get the number symbols to use for a given numbering system.
     *
     * @param string|null $numberingSystem
     *  The numbering system of the wanted symbols set.
     *  If null, the default numbering system of this locale will be used.
     *
     * @return NumberSymbolsData
     *  The wanted number symbols
     *
     * @throws LocalizationException
     *  When passed $numberingSystem is invalid
     */
    public function getNumberSymbolsByNumberingSystem($numberingSystem = null)
    {
        if (null === $numberingSystem) {
            $numberingSystem = $this->getDefaultNumberingSystem();
        }
        if (!isset($this->numberSymbols[$numberingSystem])) {
            throw new LocalizationException('Invalid numbering system : ' . $numberingSystem);
        }

        return $this->numberSymbols[$numberingSystem];
    }

    /**
     * Get the pattern to use when formatting a decimal number (for a given numbering system).
     *
     * @param string|null $numberingSystem
     *  The numbering system of the wanted symbols set.
     *  If null, the default numbering system of this locale will be used.
     *
     * @return string
     *  The decimal pattern
     *
     * @throws LocalizationException
     *  When passed numbering system is invalid
     */
    public function getDecimalPattern($numberingSystem = null)
    {
        if (null === $numberingSystem) {
            $numberingSystem = $this->getDefaultNumberingSystem();
        }
        if (!isset($this->decimalPatterns[$numberingSystem])) {
            throw new LocalizationException('No decimal pattern found for numbering system : ' . $numberingSystem);
        }

        return $this->decimalPatterns[$numberingSystem];
    }

    /**
     * Get the pattern to use when formatting a percentage (for a given numbering system).
     *
     * @param string|null $numberingSystem
     *  The numbering system of the wanted symbols set.
     *  If null, the default numbering system of this locale will be used.
     *
     * @return string
     *  The percent pattern
     *
     * @throws LocalizationException
     *  When passed numbering system is invalid
     */
    public function getPercentPattern($numberingSystem = null)
    {
        if (null === $numberingSystem) {
            $numberingSystem = $this->getDefaultNumberingSystem();
        }
        if (!isset($this->percentPatterns[$numberingSystem])) {
            throw new LocalizationException('No percent pattern found for numbering system : ' . $numberingSystem);
        }

        return $this->percentPatterns[$numberingSystem];
    }

    /**
     * Get the pattern to use when formatting a price (for a given numbering system).
     *
     * @param string|null $numberingSystem
     *  The numbering system of the wanted symbols set.
     *  If null, the default numbering system of this locale will be used.
     *
     * @return string
     *  The currency pattern
     *
     * @throws LocalizationException
     *  When passed numbering system is invalid
     */
    public function getCurrencyPattern($numberingSystem = null)
    {
        if (null === $numberingSystem) {
            $numberingSystem = $this->getDefaultNumberingSystem();
        }
        if (!isset($this->currencyPatterns[$numberingSystem])) {
            throw new LocalizationException('No currency pattern found for numbering system : ' . $numberingSystem);
        }

        return $this->currencyPatterns[$numberingSystem];
    }

    /**
     * Get a given CLDR Currency
     *
     * @param string $currencyCode
     *  An ISO 4217 currency code
     *
     * @return null|Currency
     *  The wanted CLDR Currency. Null if this currency is not available for this locale.
     */
    public function getCurrency($currencyCode)
    {
        if (!empty($this->currencies[$currencyCode])) {
            return new Currency($this->currencies[$currencyCode]);
        }

        return null;
    }

    /**
     * Get CLDR data of a given currency
     *
     * @param string $currencyCode
     *  An ISO 4217 currency code
     *
     * @return null|CurrencyData
     *  The wanted currency data. Null if this currency is not available for this locale.
     */
    public function getCurrencyData($currencyCode)
    {
        if (!empty($this->currencies[$currencyCode])) {
            return $this->currencies[$currencyCode];
        }

        return null;
    }
}
