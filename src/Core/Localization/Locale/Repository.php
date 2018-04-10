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
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository as CldrLocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\Currency\Repository as CurrencyRepository;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Number\Formatter as NumberFormatter;
use PrestaShop\PrestaShop\Core\Localization\Specification\Factory as SpecificationFactory;
use PrestaShop\PrestaShop\Core\Localization\Specification\Number as NumberSpecification;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberCollection as PriceSpecificationMap;
use PrestaShop\PrestaShop\Core\Localization\Specification\Price as PriceSpecification;

/**
 * Locale repository
 *
 * Used to get locale instances.
 * This repository manages all dependencies needed to create a complete Localization/Locale instance
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
     * Default value: "latn"
     *
     * @see http://cldr.unicode.org/translation/numbering-systems
     *
     * @var string
     */
    protected $numberingSystem;

    /**
     * Currency display type
     * Default is "symbol". But sometimes you may want to display the currency code instead.
     * Possible values: PrestaShop\PrestaShop\Core\Localization\Specification\Price::CURRENCY_DISPLAY_*
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

        return (new SpecificationFactory)->buildNumberSpecification(
            $cldrLocale,
            $this->maxFractionDigits,
            $this->numberGroupingUsed
        );
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

        $currencies = $this->currencyRepository->getAvailableCurrencies($localeCode);

        $priceSpecifications = new PriceSpecificationMap();
        foreach ($currencies as $currency) {
            // Build the spec
            $thisPriceSpecification = (new SpecificationFactory)->buildPriceSpecification(
                $localeCode,
                $cldrLocale,
                $currency,
                $this->numberGroupingUsed,
                $this->currencyDisplayType,
                null // TODO : replace here with custom currency precision
            );

            // Add the spec to the collection
            $priceSpecifications->add(
                $thisPriceSpecification->getCurrencyCode(),
                $thisPriceSpecification
            );
        }

        return $priceSpecifications;
    }
}
