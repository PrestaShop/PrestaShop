<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Localization\CLDR;

use InvalidArgumentException;
use SimplexmlElement;

class DataReader implements DataReaderInterface
{
    const CLDR_ROOT = 'localization/CLDR/';
    const CLDR_MAIN = 'localization/CLDR/core/common/main/';

    /**
     * Get locale data by code (either language code or EITF locale tag)
     *
     * @param string $localeCode The wanted locale code
     *
     * @return LocaleData The locale data object
     */
    public function getLocaleByCode($localeCode)
    {
        $parts      = $this->getLocaleParts($localeCode);
        $commonData = $this->readLocaleData($parts['language']);

        if (empty($parts['region'])) {
            return $commonData;
        }

        $regionalisedData = $this->readLocaleData($localeCode);

        return $commonData->merge($regionalisedData);
    }

    /**
     * Get currency data by ISO 4217 code
     *
     * @param string $isoCode    The currency code
     * @param string $localeCode The output locale code (in which language do you want the currency data ?)
     *
     * @return array The currency data
     */
    public function getCurrencyByIsoCode($isoCode, $localeCode)
    {
        $parts      = $this->getLocaleParts($localeCode);
        $commonData = $this->readCurrencyData($isoCode, $parts['language']);

        if ($localeCode === $parts['language']) {
            return $commonData;
        }

        $regionalisedData = $this->readCurrencyData($isoCode, $localeCode);

        return array_replace_recursive($commonData, $regionalisedData);
    }

    /**
     * Extracts the relevant parts of an IETF locale tag
     *
     * @param string $localeTag The locale tag (e.g.: fr-FR, en-US...)
     *
     * @return array The indexed locale parts (e.g.: ['langage' => 'en', 'region' => 'US'])
     */
    protected function getLocaleParts($localeTag)
    {
        $expl = explode('-', $localeTag);

        $parts = array(
            'language' => $expl[0],
        );

        if (!empty($expl[1])) {
            $parts['region'] = $expl[1];
        }

        return $parts;
    }

    /**
     * Get CLDR official xml data for a given locale tag
     *
     * The locale tag can be either an IETF tag (en-GB) or a simple language code (en)
     *
     * @param string $localeTag The locale tag.
     *
     * @return SimplexmlElement The locale data
     */
    protected function readMainData($localeTag)
    {
        $parts      = $this->getLocaleParts($localeTag);
        $langCode   = $parts['language'];
        $regionCode = isset($parts['region']) ? $parts['region'] : null;
        $filename   = $this->getMainDataFilePath($langCode, $regionCode);

        return simplexml_load_file($filename);
    }

    /**
     * Get the main data file path for a given language.
     * If the optional region code is provided, the regionalised data file path will be returned instead.
     *
     * @param string $langCode   The language code (e.g.: fr, en, de...)
     * @param string $regionCode (Optional) The region code (e.g.: FR, GB, US...)
     *
     * @return string
     */
    protected function getMainDataFilePath($langCode, $regionCode = null)
    {
        $filename = $langCode;

        if ($regionCode) {
            $filename .= '_' . $regionCode;
        }

        $filename = preg_replace('#[^_a-z-A-Z0-9]#', '', $filename);
        $filename .= '.xml';

        return $this->mainPath($filename);
    }

    /**
     * Get the real path for CLDR main data folder
     * If a filename is provided, it will be added at the end of the path
     *
     * @param string $filename (Optional) The filename to be added to the path
     *
     * @return string The realpath of CLDR main data folder
     *
     * @throws InvalidArgumentException
     */
    protected function mainPath($filename = '')
    {
        $path = realpath(_PS_ROOT_DIR_ . '/' . self::CLDR_MAIN . ($filename ? $filename : ''));
        if (false === $path) {
            throw new InvalidArgumentException("The file $filename does not exist");
        }

        return $path;
    }

    /**
     * Extracts locale data from CLDR xml data.
     * XML data will be mapped in a LocaleData object
     *
     * @param string $localeTag The wanted locale. Can be either a language code (e.g.: fr) of an EITF tag (e.g.: en-US)
     *
     * @return LocaleData
     */
    protected function readLocaleData($localeTag)
    {
        $xmlData = $this->readMainData($localeTag);

        return $this->mapLocaleData($xmlData);
    }

    /**
     * Maps locale data from SimplexmlElement to a multidimensional array
     *
     * @param SimplexmlElement $xmlLocaleData XML locale data
     *
     * @return LocaleData The mapped data
     */
    protected function mapLocaleData(SimplexmlElement $xmlLocaleData)
    {
        $localeData = new LocaleData();

        if (isset($xmlLocaleData->identity->language)) {
            $localeData->localeCode = (string)$xmlLocaleData->identity->language['type'];
        }
        if (isset($xmlLocaleData->identity->territory)) {
            $localeData->localeCode .= '-' . $xmlLocaleData->identity->territory['type'];
        } elseif (isset($xmlLocaleData->numbers->symbols)) {
            $localeData->defaultNumberingSystem = (string)$xmlLocaleData->numbers->symbols[0]['numberSystem'];
        }

        $numbersData = $xmlLocaleData->numbers;

        // Default numbering system.
        if (isset($numbersData->defaultNumberingSystem)) {
            $localeData->defaultNumberingSystem = (string)$numbersData->defaultNumberingSystem;
        }

        // Minimum grouping digits value defines when we should start grouping digits.
        // 1 => we start grouping at 4 figures numbers (1,000+) (most frequent)
        // 2 => we start grouping at 5 figures numbers (10,000+)
        if (isset($numbersData->minimumGroupingDigits)) {
            $localeData->minimumGroupingDigits = (int)$numbersData->minimumGroupingDigits;
        }

        // Complete numbering systems list with the "others" available for this locale.
        // Possible other systems are "native", "traditional" and "finance".
        // @see http://www.unicode.org/reports/tr35/tr35-numbers.html#otherNumberingSystems
        if (isset($numbersData->otherNumberingSystems)) {
            foreach ($numbersData->otherNumberingSystems->children() as $system) {
                /** @var $system SimplexmlElement */
                $localeData->numberingSystems[$system->getName()] = (string)$system;
            }
        }

        // Symbols (by numbering system)
        if (isset($numbersData->symbols)) {
            foreach ($numbersData->symbols as $symbol) {
                $symbolsList = new NumberSymbolList();
                if (isset($symbol->decimal)) {
                    $symbolsList->decimal = (string)$symbol->decimal;
                }
                if (isset($symbol->group)) {
                    $symbolsList->group = (string)$symbol->group;
                }
                if (isset($symbol->list)) {
                    $symbolsList->list = (string)$symbol->list;
                }
                if (isset($symbol->percentSign)) {
                    $symbolsList->percentSign = (string)$symbol->percentSign;
                }
                if (isset($symbol->minusSign)) {
                    $symbolsList->minusSign = (string)$symbol->minusSign;
                }
                if (isset($symbol->plusSign)) {
                    $symbolsList->plusSign = (string)$symbol->plusSign;
                }
                if (isset($symbol->exponential)) {
                    $symbolsList->exponential = (string)$symbol->exponential;
                }
                if (isset($symbol->superscriptingExponent)) {
                    $symbolsList->superscriptingExponent = (string)$symbol->superscriptingExponent;
                }
                if (isset($symbol->perMille)) {
                    $symbolsList->perMille = (string)$symbol->perMille;
                }
                if (isset($symbol->infinity)) {
                    $symbolsList->infinity = (string)$symbol->infinity;
                }
                if (isset($symbol->nan)) {
                    $symbolsList->nan = (string)$symbol->nan;
                }
                if (isset($symbol->timeSeparator)) {
                    $symbolsList->timeSeparator = (string)$symbol->timeSeparator;
                }
                if (isset($symbol->currencyDecimal)) {
                    $symbolsList->currencyDecimal = (string)$symbol->currencyDecimal;
                }
                if (isset($symbol->currencyGroup)) {
                    $symbolsList->currencyGroup = (string)$symbol->currencyGroup;
                }

                $localeData->numberSymbols[(string)$symbol['numberSystem']] = $symbolsList;
            }
        }

        // Decimal patterns (by numbering system)
        if (isset($numbersData->decimalFormats)) {
            foreach ($numbersData->decimalFormats as $format) {
                /** @var SimplexmlElement $format */
                $numberSystem  = (string)$format['numberSystem'];
                $patternResult = $format->xpath('decimalFormatLength[not(@type)]/decimalFormat/pattern');

                $localeData->decimalPatterns[$numberSystem] = (string)$patternResult[0];
            }
        }

        // Percent patterns (by numbering system)
        if (isset($numbersData->percentFormats)) {
            foreach ($numbersData->percentFormats as $format) {
                $numberSystem = (string)$format['numberSystem'];
                $pattern      = $format->percentFormatLength->percentFormat->pattern;

                $localeData->percentPatterns[$numberSystem] = (string)$pattern;
            }
        }

        // Currency patterns (by numbering system)
        if (isset($numbersData->currencyFormats)) {
            foreach ($numbersData->currencyFormats as $format) {
                /** @var SimplexmlElement $format */
                $numberSystem  = (string)$format['numberSystem'];
                $patternResult = $format->xpath('currencyFormatLength/currencyFormat[@type="standard"]/pattern');

                $localeData->currencyPatterns[$numberSystem] = (string)$patternResult[0];
            }
        }

        return $localeData;
    }

    /**
     * Extracts currency data from CLDR xml data.
     * XML data will be mapped to a multidimensional array
     *
     * Example output :
     * [
     *     'isoCode'     => 'EUR',
     *     'displayName' => [
     *         'default' => 'euro',
     *         'one'     => 'euro',
     *         'other'   => 'euros',
     *     ],
     *     'symbol'      => [
     *         'default' => '€',
     *         'narrow'  => '€',
     *     ],
     * ]
     *
     * @param string $currencyCode The wanted currency
     * @param string $localeTag    The output locale tag (in which language do you want the currency data ?)
     *
     * @return array
     */
    protected function readCurrencyData($currencyCode, $localeTag)
    {
        $xmlData      = $this->readMainData($localeTag);
        $currencyData = $xmlData->xpath("/ldml/numbers/currencies/currency[@type='$currencyCode']");
        if (empty($currencyData)) {
            return array();
        }

        return $this->mapCurrencyData($currencyData[0]);
    }

    /**
     * Maps currency data from SimplexmlElement to a multidimensional array
     *
     * @param SimplexmlElement $xmlCurrencyData XML currency data
     *
     * @return array The mapped data
     */
    protected function mapCurrencyData(SimplexmlElement $xmlCurrencyData)
    {
        // ISO 4217 currency code is carried by "type" attribute of <currency> tag
        // It also actually identifies the tag among others
        $currencyArray = array(
            'isoCode' => (string)$xmlCurrencyData['type'],
        );

        // Display names (depending on count)
        foreach ($xmlCurrencyData->displayName as $displayName) {
            $displayNameCount = 'default';
            if (!empty($displayName['count'])) {
                $displayNameCount = (string)$displayName['count'];
            }
            $currencyArray['displayName'][$displayNameCount] = (string)$displayName;
        }

        // Symbol (full, shortened...)
        foreach ($xmlCurrencyData->symbol as $symbol) {
            $symbolType = 'default';
            if (!empty($symbol['alt'])) {
                $symbolType = (string)$symbol['alt'];
            }
            $currencyArray['symbol'][$symbolType] = (string)$symbol;
        }

        // If no symbol at all, use ISO code.
        if (empty($currencyArray['symbol'])) {
            $currencyArray['symbol']['default'] = $currencyArray['isoCode'];
        }

        return ($currencyArray);
    }
}
