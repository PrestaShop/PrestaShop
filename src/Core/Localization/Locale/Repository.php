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
use PrestaShop\PrestaShop\Core\Localization\CLDR\Number as CldNumber;
use PrestaShop\PrestaShop\Core\Localization\CLDR\NumberRepository as CldrNumberRepository;
use PrestaShop\PrestaShop\Core\Localization\Currency;
use PrestaShop\PrestaShop\Core\Localization\Currency\Repository as CurrencyRepository;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Number\Formatter as NumberFormatter;
use PrestaShop\PrestaShop\Core\Localization\Specification\Number as NumberSpecification;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberCollection as PriceSpecificationMap;
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
     * Repository used to retrieve low level CLDR number data bag
     *
     * @var CldrNumberRepository
     */
    protected $cldrNumberRepository;

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

    public function __construct(
        CldrNumberRepository $cldrNumberRepository,
        CurrencyRepository $currencyRepository,
        $roundingMode = Rounding::ROUND_HALF_UP,
        $numberingSystem = 'latn',
        $currencyDisplayType = PriceSpecification::CURRENCY_DISPLAY_SYMBOL
    ) {
        $this->cldrNumberRepository = $cldrNumberRepository;
        $this->currencyRepository   = $currencyRepository;
        $this->roundingMode         = $roundingMode;
        $this->numberingSystem      = $numberingSystem;
        $this->currencyDisplayType  = $currencyDisplayType;
    }

    /**
     * @inheritdoc
     */
    public function getLocale($localeCode)
    {
        if (!isset($this->locales[$localeCode])) {
            $this->locales[$localeCode] = new Locale(
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
        $cldrNumber = $this->cldrNumberRepository->getNumber($localeCode);

        if (null === $cldrNumber) {
            throw new LocalizationException('CLDR Number data not found for locale "' . $localeCode . '""');
        }

        return $this->buildNumberSpecification($cldrNumber);
    }

    /**
     * Get all the Price specifications for a given locale.
     * Each installed currency for this locale has its own Price specification
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
        $cldrNumber = $this->cldrNumberRepository->getNumber($localeCode);
        if (null === $cldrNumber) {
            throw new LocalizationException('CLDR Number data not found for locale "' . $localeCode . '""');
        }

        $currencies = $this->currencyRepository->getInstalledCurrencies();

        $priceSpecifications = new PriceSpecificationMap();
        foreach ($currencies as $currency) {
            $priceSpecification = $this->buildPriceSpecification($cldrNumber, $currency, $localeCode);
            $priceSpecifications->add(
                $priceSpecification->getCurrencyCode(),
                $priceSpecification
            );
        }

        return $priceSpecifications;
    }

    /**
     * Build a Number specification from a CLDR Number object
     *
     * @param CldNumber $cldrNumber
     *  CldrNumber objects are low level data object extracted from CLDR data files
     *
     * @return NumberSpecification
     *
     * @throws LocalizationException
     */
    protected function buildNumberSpecification($cldrNumber)
    {
        return new NumberSpecification(
            $cldrNumber->getPositivePattern(),
            $cldrNumber->getNegativePattern(),
            $cldrNumber->getSymbols(),
            $cldrNumber->getMaxFractionDigits(),
            $cldrNumber->getMinFractionDigits(),
            $cldrNumber->getGroupingUsed(),
            $cldrNumber->getPrimaryGroupSize(),
            $cldrNumber->getSecondaryGroupSize()
        );
    }

    /**
     * Build a Price specification from a CLDR Number object and a Currency object
     *
     * @param CldNumber $cldrNumber
     *  This CldrNumber object is a low level data bag extracted from CLDR data source
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
    protected function buildPriceSpecification(CldNumber $cldrNumber, Currency $currency, $localeCode)
    {
        return new PriceSpecification(
            $cldrNumber->getPositivePattern(),
            $cldrNumber->getNegativePattern(),
            $cldrNumber->getSymbols(),
            $cldrNumber->getMaxFractionDigits(),
            $cldrNumber->getMinFractionDigits(),
            $cldrNumber->getGroupingUsed(),
            $cldrNumber->getPrimaryGroupSize(),
            $cldrNumber->getSecondaryGroupSize(),
            $this->currencyDisplayType,
            $currency->getSymbol($localeCode),
            $currency->getIsoCode()
        );
    }
}
