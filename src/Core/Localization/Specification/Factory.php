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

namespace PrestaShop\PrestaShop\Core\Localization\Specification;

use PrestaShop\PrestaShop\Core\Localization\CLDR\Locale as CldrLocale;
use PrestaShop\PrestaShop\Core\Localization\CLDR\NumberSymbolsData;
use PrestaShop\PrestaShop\Core\Localization\Currency;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Specification\Number as NumberSpecification;
use PrestaShop\PrestaShop\Core\Localization\Specification\Price as PriceSpecification;

/**
 * Number specification factory.
 * Builds Number, Price or Percentage specifications objects.
 *
 * Uses a CLDR Locale instance to extract relevant data needed to build a specification object
 */
class Factory
{
    /**
     * Build a Number specification from a CLDR Locale object
     *
     * @param CldrLocale $cldrLocale
     *  This CldrLocale object is a low level data object extracted from CLDR data source
     *
     * @param int $maxFractionDigits
     *  Max number of digits to display in a number's decimal part
     *
     * @param bool $numberGroupingUsed
     *  Should we group digits in a number's integer part ?
     *
     * @return NumberSpecification
     *
     * @throws LocalizationException
     */
    public function buildNumberSpecification(CldrLocale $cldrLocale, $maxFractionDigits, $numberGroupingUsed)
    {
        $decimalPattern = $cldrLocale->getDecimalPattern();
        $numbersSymbols = $cldrLocale->getAllNumberSymbols();

        return new NumberSpecification(
            $this->getPositivePattern($decimalPattern),
            $this->getNegativePattern($decimalPattern),
            $this->computeNumberSymbolLists($numbersSymbols),
            $maxFractionDigits,
            $this->getMinFractionDigits($decimalPattern),
            $numberGroupingUsed,
            $this->getPrimaryGroupSize($decimalPattern),
            $this->getSecondaryGroupSize($decimalPattern)
        );
    }

    /**
     * Build a Price specification from a CLDR Locale object and a Currency object
     *
     * @param string $localeCode
     *  The concerned locale
     *
     * @param CldrLocale $cldrLocale
     *  This CldrLocale object is a low level data object extracted from CLDR data source
     *  It contains data about the concerned locale.
     *
     * @param Currency $currency
     *  This Currency object brings missing specification to format a number as a price
     *
     * @param bool $numberGroupingUsed
     *  Should we group digits when formatting prices ?
     *
     * @param $currencyDisplayType
     *  Type of display for currency symbol (symbol or ISO code)
     *
     * @param null|int $maxFractionDigits
     *  The decimal precision of the price
     *
     * @return PriceSpecification
     *
     * @throws LocalizationException
     */
    public function buildPriceSpecification(
        $localeCode,
        CldrLocale $cldrLocale,
        Currency $currency,
        $numberGroupingUsed,
        $currencyDisplayType,
        $maxFractionDigits = null
    ) {
        $currencyPattern = $cldrLocale->getCurrencyPattern();
        $numbersSymbols  = $cldrLocale->getAllNumberSymbols();

        $precision = $maxFractionDigits;
        if (null === $precision) {
            $precision = (int)$currency->getDecimalPrecision();
        }

        return new PriceSpecification(
            $this->getPositivePattern($currencyPattern),
            $this->getNegativePattern($currencyPattern),
            $this->computeNumberSymbolLists($numbersSymbols),
            $precision,
            $this->getMinFractionDigits($currencyPattern),
            $numberGroupingUsed,
            $this->getPrimaryGroupSize($currencyPattern),
            $this->getSecondaryGroupSize($currencyPattern),
            $currencyDisplayType,
            $currency->getSymbol($localeCode),
            $currency->getIsoCode()
        );
    }

    /**
     * Extract the positive pattern from a CLDR formatting pattern
     * Works with any formatting pattern (number, price, percentage)
     *
     * @param string $pattern
     *  The CLDR pattern
     *
     * @return string
     *  The extracted positive pattern
     */
    protected function getPositivePattern($pattern)
    {
        $patterns = explode(';', $pattern);

        return $patterns[0];
    }

    /**
     * Extract the negative pattern from a CLDR formatting pattern
     * Works with any formatting pattern (number, price, percentage)
     *
     * @param string $pattern
     *  The CLDR pattern
     *
     * @return string
     *  The extracted negative pattern
     */
    protected function getNegativePattern($pattern)
    {
        $patterns = explode(';', $pattern);

        return isset($patterns[1])
            ? $patterns[1]
            : '-' . $patterns[0];
    }

    /**
     * Convert a list of CLDR number symbols data into a list of NumberSymbolList objects
     *
     * @param NumberSymbolsData[] $allNumberSymbolsData
     *  All the CLDR number symbols data indexed by numbering system
     *
     * @return NumberSymbolList[]
     *
     * @throws LocalizationException
     *  If passed data is invalid
     */
    protected function computeNumberSymbolLists($allNumberSymbolsData)
    {
        $symbolsLists = [];
        foreach ($allNumberSymbolsData as $numberingSystem => $numberSymbolsData) {
            $symbolsLists[$numberingSystem] = $this->getNumberSymbolList($numberSymbolsData);
        }

        return $symbolsLists;
    }

    /**
     * Get a NumberSymbolList object from a CLDR NumberSymbolsData object
     *
     * @param NumberSymbolsData $symbolsData
     *  Data that will be used to build the NumberSymbolList object
     *
     * @return NumberSymbolList
     *  An immutable NumberSymbolList object
     *
     * @throws LocalizationException
     *  If passed data is invalid
     */
    protected function getNumberSymbolList(NumberSymbolsData $symbolsData)
    {
        return new NumberSymbolList(
            $symbolsData->decimal,
            $symbolsData->group,
            $symbolsData->list,
            $symbolsData->percentSign,
            $symbolsData->minusSign,
            $symbolsData->plusSign,
            $symbolsData->exponential,
            $symbolsData->superscriptingExponent,
            $symbolsData->perMille,
            $symbolsData->infinity,
            $symbolsData->nan
        );
    }

    /**
     * Extract the min number of fraction digits from a number pattern (decimal, currency, percentage)
     *
     * @param string $pattern
     *  The formatting pattern to use for extraction
     *
     * @return int
     *  The min number of fraction digits to display in the final number
     */
    protected function getMinFractionDigits($pattern)
    {
        $dotPos = (int)strpos($pattern, '.');

        return substr_count($pattern, '0', $dotPos);
    }

    /**
     * Get the primary digits group size from a number formatting pattern
     *
     * @param string $pattern
     *  The CLDR number formatting pattern (e.g.: #,##0.###)
     *
     * @return int
     *  The primary group size of the passed pattern
     */
    protected function getPrimaryGroupSize($pattern)
    {
        $parts       = explode('.', $pattern);
        $integerPart = $parts[0];
        $groups      = explode(',', $integerPart);
        $nbGroups    = count($groups);

        return strlen($groups[$nbGroups - 1]);
    }

    /**
     * Get the secondary digits group size from a number formatting pattern
     * e.g.: with    #,##0.### => No secondary group. Will return primary group size.
     * e.g.: with #,##,##0.### => Secondary group size is 2, primary group size is 3.
     *
     * @param string $pattern
     *  The CLDR number formatting pattern
     *
     * @return int
     *  The secondary group size of the passed pattern
     */
    protected function getSecondaryGroupSize($pattern)
    {
        $parts       = explode('.', $pattern);
        $integerPart = $parts[0];
        $groups      = explode(',', $integerPart);
        $nbGroups    = count($groups);

        if ($nbGroups > 2) {
            return strlen($groups[$nbGroups - 2]);
        }

        return strlen($groups[$nbGroups - 1]);
    }
}
