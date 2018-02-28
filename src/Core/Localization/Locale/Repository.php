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

namespace PrestaShop\PrestaShop\Core\Localization\Locale;

use PrestaShop\Decimal\Operation\Rounding;
use PrestaShop\PrestaShop\Core\Localization\CLDR\Locale as CldrLocale;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository as CldrLocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\CLDR\NumberSymbolsData;
use PrestaShop\PrestaShop\Core\Localization\Currency;
use PrestaShop\PrestaShop\Core\Localization\Currency\Repository as CurrencyRepository;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Number\Formatter as NumberFormatter;
use PrestaShop\PrestaShop\Core\Localization\Specification\Number as NumberSpecification;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberCollection as PriceSpecificationMap;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberSymbolList;
use PrestaShop\PrestaShop\Core\Localization\Specification\Price as PriceSpecification;

/**
 * Locale repository
 *
 * Used to get locale instances.
 * This repository manages all dependencies needed to create a complete Locale instance
 */
class Repository implements RepositoryInterface
{
    /**
     * Max number of digits to use in the fraction part of a decimal number
     * This is a default value
     */
    const MAX_FRACTION_DIGITS = 3;

    /**
     * Repository used to retrieve low level CLDR locale objects
     *
     * @var CldrLocaleRepository
     */
    protected $cldrLocaleRepository;

    /**
     * Repository used to retrieve Currency objects
     *
     * @var CurrencyRepository
     */
    protected $currencyRepository;

    /**
     * Rounding mode to use when formatting numbers
     * Possible values are listed in PrestaShop\Decimal\Operation\Rounding::ROUND_* constants
     *
     * @var string
     */
    protected $roundingMode;

    /**
     * Numbering system to use when formatting numbers.
     * Default value : "latn"
     *
     * @see http://cldr.unicode.org/translation/numbering-systems
     *
     * @var string
     */
    protected $numberingSystem;

    /**
     * Currency display type
     * Default is "symbol". But sometimes you may want to display the currency code instead.
     * Possible values : PrestaShop\PrestaShop\Core\Localization\Specification\Price::CURRENCY_DISPLAY_*
     *
     * @var string
     */
    protected $currencyDisplayType;

    /**
     * Already instantiated Locale objects
     *
     * @var Locale[]
     */
    protected $locales;

    /**
     * Should we group digits in a number's integer part ?
     *
     * @var bool
     */
    protected $numberGroupingUsed;

    /**
     * Max number of digits to display in a number's decimal part
     *
     * @var int
     */
    protected $maxFractionDigits;

    public function __construct(
        CldrLocaleRepository $cldrLocaleRepository,
        CurrencyRepository $currencyRepository,
        $roundingMode = Rounding::ROUND_HALF_UP,
        $numberingSystem = Locale::NUMBERING_SYSTEM_LATIN,
        $currencyDisplayType = PriceSpecification::CURRENCY_DISPLAY_SYMBOL,
        $groupingUsed = true,
        $maxFractionDigits = self::MAX_FRACTION_DIGITS
    ) {
        $this->cldrLocaleRepository = $cldrLocaleRepository;
        $this->currencyRepository   = $currencyRepository;
        $this->roundingMode         = $roundingMode;
        $this->numberingSystem      = $numberingSystem;
        $this->currencyDisplayType  = $currencyDisplayType;
        $this->numberGroupingUsed   = $groupingUsed;
        $this->maxFractionDigits    = $maxFractionDigits;
    }

    /**
     * @inheritdoc
     */
    public function getLocale($localeCode)
    {
        if (!isset($this->locales[$localeCode])) {
            $this->locales[$localeCode] = new Locale(
                $localeCode,
                $this->getNumberSpecification($localeCode),
                $this->getPriceSpecifications($localeCode),
                new NumberFormatter($this->roundingMode, $this->numberingSystem)
            );
        }

        return $this->locales[$localeCode];
    }

    /**
     * Get the Number specification for a given locale
     *
     * @param string $localeCode
     *  The locale code (simplified IETF tag syntax)
     *  Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     *  eg: fr-FR, en-US
     *
     * @return NumberSpecification
     *  A Number specification
     *
     * @throws LocalizationException
     */
    protected function getNumberSpecification($localeCode)
    {
        $cldrLocale = $this->cldrLocaleRepository->getLocale($localeCode);

        if (null === $cldrLocale) {
            throw new LocalizationException('CLDR locale not found for locale code "' . $localeCode . '"');
        }

        return $this->buildNumberSpecification($cldrLocale);
    }

    /**
     * Get all the Price specifications for a given locale.
     * Each installed currency has its own Price specification
     *
     * @param string $localeCode
     *  The locale code (simplified IETF tag syntax)
     *  Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     *  eg: fr-FR, en-US
     *
     * @return PriceSpecificationMap
     *  All installed currencies' Price specifications
     *
     * @throws LocalizationException
     */
    protected function getPriceSpecifications($localeCode)
    {
        $cldrLocale = $this->cldrLocaleRepository->getLocale($localeCode);
        if (null === $cldrLocale) {
            throw new LocalizationException('CLDR locale not found for locale code "' . $localeCode . '"');
        }

        $currencies = $this->currencyRepository->getInstalledCurrencies();

        $priceSpecifications = new PriceSpecificationMap();
        foreach ($currencies as $currency) {
            $priceSpecification = $this->buildPriceSpecification($cldrLocale, $currency, $localeCode);
            $priceSpecifications->add(
                $priceSpecification->getCurrencyCode(),
                $priceSpecification
            );
        }

        return $priceSpecifications;
    }

    /**
     * Build a Number specification from a CLDR Locale object
     *
     * @param CldrLocale $cldrLocale
     *  This CldrLocale object is a low level data object extracted from CLDR data source
     *
     * @return NumberSpecification
     *
     * @throws LocalizationException
     */
    protected function buildNumberSpecification(CldrLocale $cldrLocale)
    {
        $decimalPattern = $cldrLocale->getDecimalPattern();
        $numbersSymbols = $cldrLocale->getAllNumberSymbols();

        return new NumberSpecification(
            $this->getPositivePattern($decimalPattern),
            $this->getNegativePattern($decimalPattern),
            $this->computeNumberSymbolLists($numbersSymbols),
            $this->maxFractionDigits,
            $this->getMinFractionDigits($decimalPattern),
            $this->numberGroupingUsed,
            $this->getPrimaryGroupSize($decimalPattern),
            $this->getSecondaryGroupSize($decimalPattern)
        );
    }

    /**
     * Build a Price specification from a CLDR Locale object and a Currency object
     *
     * @param CldrLocale $cldrLocale
     *  This CldrLocale object is a low level data object extracted from CLDR data source
     *
     * @param Currency $currency
     *  This Currency object brings missing specification to format a number as a price
     *
     * @param string $localeCode
     *  Some price specs need to be localized (eg : currency symbol)
     *  Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     *  eg: fr-FR, en-US
     *
     * @return PriceSpecification
     *
     * @throws LocalizationException
     */
    protected function buildPriceSpecification(CldrLocale $cldrLocale, Currency $currency, $localeCode)
    {
        $currencyPattern = $cldrLocale->getCurrencyPattern();
        $numbersSymbols  = $cldrLocale->getAllNumberSymbols();

        return new PriceSpecification(
            $this->getPositivePattern($currencyPattern),
            $this->getNegativePattern($currencyPattern),
            $this->computeNumberSymbolLists($numbersSymbols),
            $this->maxFractionDigits,
            $this->getMinFractionDigits($currencyPattern),
            $this->numberGroupingUsed,
            $this->getPrimaryGroupSize($currencyPattern),
            $this->getSecondaryGroupSize($currencyPattern),
            $this->currencyDisplayType,
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
     * Get the max number of fraction digits when displaying a decimal number
     *
     * @return int
     *  The max number of fraction digits to display in the final number
     */
    protected function getMaxFractionDigits()
    {
        return $this->maxFractionDigits;
    }

    /**
     * Should we group digits when displaying a number ?
     *
     * @return bool
     *  True if digits should be grouped
     */
    protected function getNumberGroupingUsed()
    {
        return $this->numberGroupingUsed;
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
