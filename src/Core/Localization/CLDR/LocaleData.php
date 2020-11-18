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

/**
 * The LocaleData class is the exact representation of Locale's data structure inside CLDR xml data files.
 *
 * This class is only used internally, it is mutable and overridable until fully built. It can then be used as
 * an intermediary data bag to build a real CLDR Locale (immutable) object.
 */
class LocaleData
{
    /**
     * The locale code for this data (either language code or IETF tag).
     * e.G.: 'fr', 'fr-FR'...
     *
     * @var string
     */
    protected $localeCode;

    /**
     * List of available numbering systems
     * Array of strings (codes).
     *
     * @var string[]
     */
    protected $numberingSystems;

    /**
     * Default numbering system.
     *
     * @var string
     */
    protected $defaultNumberingSystem;

    /**
     * Used to suppress groupings below a certain value.
     *
     * 1 -> grouping starts at 4 figures integers (1,000 and more)
     * 2 -> grouping starts at 5 figures integers (10,000 and more)
     *
     * @var int
     */
    protected $minimumGroupingDigits;

    /**
     * Collection of all available symbols list (by numbering system).
     *
     * @var NumberSymbolsData[]
     */
    protected $numberSymbols;

    /**
     * Collection of all available decimal patterns (by numbering system)
     * Array of strings (patterns).
     *
     * @var string[]
     */
    protected $decimalPatterns;

    /**
     * Collection of all available percent patterns (by numbering system)
     * Array of strings (patterns).
     *
     * @var string[]
     */
    protected $percentPatterns;

    /**
     * Collection of all available currency patterns (by numbering system)
     * Array of strings (patterns).
     *
     * @var string[]
     */
    protected $currencyPatterns;

    /**
     * All currencies, by ISO code.
     *
     * @var CurrencyData[]
     */
    protected $currencies;

    /**
     * Override this object's data with another LocaleData object.
     *
     * @param LocaleData $localeData Locale data to use for the override
     *
     * @return $this Fluent interface
     */
    public function overrideWith(LocaleData $localeData)
    {
        if (null !== $localeData->getLocaleCode()) {
            $this->setLocaleCode($localeData->getLocaleCode());
        }

        if (null !== $localeData->getNumberingSystems()) {
            if (null === $this->numberingSystems) {
                $this->numberingSystems = [];
            }
            $this->numberingSystems = array_merge($this->numberingSystems, $localeData->getNumberingSystems());
        }

        if (null !== $localeData->getDefaultNumberingSystem()) {
            $this->setDefaultNumberingSystem($localeData->getDefaultNumberingSystem());
        }

        if (null !== $localeData->getMinimumGroupingDigits()) {
            $this->setMinimumGroupingDigits($localeData->getMinimumGroupingDigits());
        }

        if (null !== $localeData->getNumberSymbols()) {
            foreach ($localeData->getNumberSymbols() as $numberingSystem => $symbolsData) {
                if (!isset($this->numberSymbols[$numberingSystem])) {
                    $this->numberSymbols[$numberingSystem] = $symbolsData;

                    continue;
                }
                $this->numberSymbols[$numberingSystem]->overrideWith($symbolsData);
            }
        }

        if (null !== $localeData->getDecimalPatterns()) {
            $this->setDecimalPatterns($localeData->getDecimalPatterns());
        }

        if (null !== $localeData->getPercentPatterns()) {
            $this->setPercentPatterns($localeData->getPercentPatterns());
        }

        if (null !== $localeData->getCurrencyPatterns()) {
            if (null === $this->currencyPatterns) {
                $this->currencyPatterns = [];
            }
            $this->currencyPatterns = array_merge($this->currencyPatterns, $localeData->getCurrencyPatterns());
        }

        if (null !== $localeData->getCurrencies()) {
            foreach ($localeData->getCurrencies() as $code => $currencyData) {
                if (!isset($this->currencies[$code])) {
                    $this->currencies[$code] = $currencyData;
                    continue;
                }
                $this->currencies[$code]->overrideWith($currencyData);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->localeCode;
    }

    /**
     * @param string $localeCode
     *
     * @return LocaleData
     */
    public function setLocaleCode($localeCode)
    {
        $this->localeCode = $localeCode;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getNumberingSystems()
    {
        return $this->numberingSystems;
    }

    /**
     * @param string[] $numberingSystems
     *
     * @return LocaleData
     */
    public function setNumberingSystems($numberingSystems)
    {
        $this->numberingSystems = $numberingSystems;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultNumberingSystem()
    {
        return $this->defaultNumberingSystem;
    }

    /**
     * @param string $defaultNumberingSystem
     *
     * @return LocaleData
     */
    public function setDefaultNumberingSystem($defaultNumberingSystem)
    {
        $this->defaultNumberingSystem = $defaultNumberingSystem;

        return $this;
    }

    /**
     * @return int
     */
    public function getMinimumGroupingDigits()
    {
        return $this->minimumGroupingDigits;
    }

    /**
     * @param int $minimumGroupingDigits
     *
     * @return LocaleData
     */
    public function setMinimumGroupingDigits($minimumGroupingDigits)
    {
        $this->minimumGroupingDigits = $minimumGroupingDigits;

        return $this;
    }

    /**
     * @return \PrestaShop\PrestaShop\Core\Localization\CLDR\NumberSymbolsData[]
     */
    public function getNumberSymbols()
    {
        return $this->numberSymbols;
    }

    /**
     * @param \PrestaShop\PrestaShop\Core\Localization\CLDR\NumberSymbolsData[] $numberSymbols
     *
     * @return LocaleData
     */
    public function setNumberSymbols($numberSymbols)
    {
        $this->numberSymbols = $numberSymbols;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getDecimalPatterns()
    {
        return $this->decimalPatterns;
    }

    /**
     * @param string[] $decimalPatterns
     *
     * @return LocaleData
     */
    public function setDecimalPatterns($decimalPatterns)
    {
        $this->decimalPatterns = $decimalPatterns;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getPercentPatterns()
    {
        return $this->percentPatterns;
    }

    /**
     * @param string[] $percentPatterns
     *
     * @return LocaleData
     */
    public function setPercentPatterns($percentPatterns)
    {
        $this->percentPatterns = $percentPatterns;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getCurrencyPatterns()
    {
        return $this->currencyPatterns;
    }

    /**
     * @param string[] $currencyPatterns
     *
     * @return LocaleData
     */
    public function setCurrencyPatterns($currencyPatterns)
    {
        $this->currencyPatterns = $currencyPatterns;

        return $this;
    }

    /**
     * @return \PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyData[]
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }

    /**
     * @param \PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyData[] $currencies
     *
     * @return LocaleData
     */
    public function setCurrencies($currencies)
    {
        $this->currencies = $currencies;

        return $this;
    }
}
