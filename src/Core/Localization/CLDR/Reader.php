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
use SimpleXMLElement;

class Reader implements ReaderInterface
{
    const CLDR_ROOT         = 'localization/CLDR/';
    const CLDR_MAIN         = 'localization/CLDR/core/common/main/';
    const CLDR_SUPPLEMENTAL = 'localization/CLDR/core/common/supplemental/';

    const CLDR_ROOT_LOCALE = 'root';

    const SUPPL_DATA_CURRENCY       = 'currencyData';
    const SUPPL_DATA_LANGUAGE       = 'languageData';
    const SUPPL_DATA_NUMBERING      = 'numberingSystems';
    const SUPPL_DATA_PARENT_LOCALES = 'parentLocales'; // For specific locales hierarchy

    const DEFAULT_CURRENCY_DIGITS = 2;

    protected $mainXml = [];

    /**
     * Supplemental data for all locales.
     * Contains data about parent locales, currencies, languages...
     *
     * @var SimplexmlElement
     */
    protected $supplementalXml;
    protected $numberingSystemsXml;

    /**
     * Read locale data by locale code
     *
     * @param $localeCode
     *  The locale code (simplified IETF tag syntax)
     *  Combination of ISO 639-1 (2-letters language code) and ISO 3166-2 (2-letters region code)
     *  eg: fr-FR, en-US
     *  The underscore notation is also accepted (fr_FR, en_US...)
     *
     * @return LocaleData
     *  A LocaleData object
     *
     * @throws LocalizationException
     *  When the locale code is unknown or invalid
     */
    public function readLocaleData($localeCode)
    {
        // CLDR filenames use a different notation from IETF.
        $localeCode = str_replace('-', '_', $localeCode);

        $this->validateLocaleCodeForFilenames($localeCode);
        $this->initSupplementalData();

        $finalData = new LocaleData();
        $lookup    = $this->getLookup($localeCode);
        foreach ($lookup as $thisLocaleCode) {
            $partialData = $this->getLocaleData($thisLocaleCode);
            $finalData   = $finalData->overrideWith($partialData);
        }

        return $finalData;
    }

    /**
     * Validate a locale code
     *
     * If the passed code doesn't respect the CLDR files naming style, an exception will be raised
     * eg : "fr_FR" and "en_001" are valid
     *
     * @param $localeCode
     *  Locale code to be validated
     *
     * @throws LocalizationException
     *  When locale code is invalid
     */
    protected function validateLocaleCodeForFilenames($localeCode)
    {
        if (!preg_match('#^[a-zA-Z0-9]+(_[a-zA-Z0-9]+)*$#', $localeCode)) {
            throw new LocalizationException('Invalid locale code');
        }
    }

    /**
     * Initialize supplemental CLDR data
     */
    protected function initSupplementalData()
    {
        // Supplemental data about currencies, languages and parent locales
        if (!isset($this->supplementalXml)) {
            $supplementalPath      = realpath(
                _PS_ROOT_DIR_ . '/'
                . self::CLDR_SUPPLEMENTAL
                . 'supplementalData.xml'
            );
            $this->supplementalXml = simplexml_load_file($supplementalPath);
        }

        // This file contains special digits for non-occidental numbering systems
        if (!isset($this->numberingSystemsXml)) {
            $numberingSystemsPath      = realpath(
                _PS_ROOT_DIR_ . '/'
                . self::CLDR_SUPPLEMENTAL
                . 'numberingSystems.xml'
            );
            $this->numberingSystemsXml = simplexml_load_file($numberingSystemsPath);
        }
    }

    /**
     * Build lookup files stack for a given locale code
     *
     * @param $localeCode
     *  The given locale code (simplified IETF notation)
     *
     * @return array
     *  The lookup
     *  ['root', <intermediate codes>, $localeCode]
     *
     * @throws LocalizationException
     *  When locale code is invalid or unknown
     *
     * @see http://www.unicode.org/reports/tr35/tr35.html#Lookup
     */
    protected function getLookup($localeCode)
    {
        $lookup = [$localeCode];

        while ($localeCode = $this->getParentLocale($localeCode)) {
            array_unshift($lookup, $localeCode);
        };

        return $lookup;
    }

    /**
     * Get the parent locale for a given locale code
     *
     * @param $localeCode
     *  CLDR filenames' style locale code (with underscores)
     *  eg.: en, fr, en_GB, fr_FR...
     *
     * @return string|null
     *  The parent locale code (CLDR filenames' style). Null if no parent.
     *
     * @throws LocalizationException
     */
    protected function getParentLocale($localeCode)
    {
        // root is the... root of all CLDR locales' data. Then no parent.
        if (self::CLDR_ROOT_LOCALE == $localeCode) {
            return null;
        }

        // The special case from supplemental data
        foreach ($this->supplementalXml->parentLocales->parentLocale as $data) {
            $locales = explode(' ', $data['locales']);
            if (in_array($localeCode, $locales)) {
                return $data['parent'];
            }
        }

        // The common case with truncation
        $pos = strrpos($localeCode, '_');
        if (false !== $pos) {
            $parent = substr($localeCode, 0, $pos);
            if (false === $parent) {
                throw new LocalizationException('Invalid locale code : ' . $localeCode);
            }

            return $parent;
        }

        // The "top level" case. When only language code is left in $localeCode : 'en', 'fr'... then parent is "root".
        return self::CLDR_ROOT_LOCALE;
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
        $expl  = explode('-', $localeTag);
        $parts = [
            'language' => $expl[0],
        ];
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
     * @param string $localeCode
     *  The locale code.
     *
     * @return SimplexmlElement
     *  The locale data
     *
     * @throws LocalizationException
     *  If this locale code has no corresponding xml file
     */
    protected function getMainXmlData($localeCode)
    {
        return simplexml_load_file($this->mainPath($localeCode . '.xml'));
    }

    /**
     * Get the real path for CLDR main data folder
     * If a filename is provided, it will be added at the end of the path
     *
     * @param string $filename (Optional) The filename to be added to the path
     *
     * @return string The realpath of CLDR main data folder
     *
     * @throws LocalizationException
     */
    protected function mainPath($filename = '')
    {
        $path = realpath(_PS_ROOT_DIR_ . '/' . self::CLDR_MAIN . ($filename ? $filename : ''));
        if (false === $path) {
            throw new LocalizationException("The file $filename does not exist");
        }

        return $path;
    }

    /**
     * Extracts locale data from CLDR xml data.
     * XML data will be mapped in a LocaleData object
     *
     * @param string $localeTag The wanted locale. Can be either a language code (e.g.: fr) of an IETF tag (e.g.: en-US)
     *
     * @return LocaleData
     * @throws LocalizationException
     */
    protected function getLocaleData($localeTag)
    {
        $xmlData          = $this->getMainXmlData($localeTag);
        $supplementalData = ['digits' => $this->getDigitsData()];

        return $this->mapLocaleData($xmlData, $supplementalData);
    }

    /**
     * Maps locale data from SimplexmlElement to a LocaleData object
     *
     * @param SimplexmlElement $xmlLocaleData
     *  XML locale data
     *
     * @param array $supplementalData
     *  Supplemental locale data
     *
     * @return LocaleData
     *  The mapped locale data
     *
     * @todo use root aliases to fill up missing values (e.g.: missing symbols for exotic numbering systems).
     * @see  http://cldr.unicode.org/development/development-process/design-proposals/resolution-of-cldr-files
     */
    protected function mapLocaleData(SimplexmlElement $xmlLocaleData, $supplementalData)
    {
        $localeData = new LocaleData();
        if (isset($xmlLocaleData->identity->language)) {
            $localeData->localeCode = (string)$xmlLocaleData->identity->language['type'];
        }
        if (isset($xmlLocaleData->identity->territory)) {
            $localeData->localeCode .= '-' . $xmlLocaleData->identity->territory['type'];
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
            foreach ($numbersData->symbols as $symbolsNode) {
                if (isset($symbolsNode->alias)) {
                    // TODO here is the alias pointing to the data to be used for this specific numbering system (xpath)
                    // @see <project root>/localization/CLDR/core/common/main/root.xml
                    continue;
                }
                $symbolsList = new NumberSymbolsData();
                if (isset($symbolsNode->decimal)) {
                    $symbolsList->decimal = (string)$symbolsNode->decimal;
                }
                if (isset($symbolsNode->group)) {
                    $symbolsList->group = (string)$symbolsNode->group;
                }
                if (isset($symbolsNode->list)) {
                    $symbolsList->list = (string)$symbolsNode->list;
                }
                if (isset($symbolsNode->percentSign)) {
                    $symbolsList->percentSign = (string)$symbolsNode->percentSign;
                }
                if (isset($symbolsNode->minusSign)) {
                    $symbolsList->minusSign = (string)$symbolsNode->minusSign;
                }
                if (isset($symbolsNode->plusSign)) {
                    $symbolsList->plusSign = (string)$symbolsNode->plusSign;
                }
                if (isset($symbolsNode->exponential)) {
                    $symbolsList->exponential = (string)$symbolsNode->exponential;
                }
                if (isset($symbolsNode->superscriptingExponent)) {
                    $symbolsList->superscriptingExponent = (string)$symbolsNode->superscriptingExponent;
                }
                if (isset($symbolsNode->perMille)) {
                    $symbolsList->perMille = (string)$symbolsNode->perMille;
                }
                if (isset($symbolsNode->infinity)) {
                    $symbolsList->infinity = (string)$symbolsNode->infinity;
                }
                if (isset($symbolsNode->nan)) {
                    $symbolsList->nan = (string)$symbolsNode->nan;
                }
                if (isset($symbolsNode->timeSeparator)) {
                    $symbolsList->timeSeparator = (string)$symbolsNode->timeSeparator;
                }
                if (isset($symbolsNode->currencyDecimal)) {
                    $symbolsList->currencyDecimal = (string)$symbolsNode->currencyDecimal;
                }
                if (isset($symbolsNode->currencyGroup)) {
                    $symbolsList->currencyGroup = (string)$symbolsNode->currencyGroup;
                }

                $localeData->numberSymbols[(string)$symbolsNode['numberSystem']] = $symbolsList;
            }
        }
        // Decimal patterns (by numbering system)
        if (isset($numbersData->decimalFormats)) {
            /** @var SimplexmlElement $format */
            foreach ($numbersData->decimalFormats as $format) {
                /** @var SimplexmlElement $format */
                $numberSystem  = (string)$format['numberSystem'];
                $patternResult = $format->xpath('decimalFormatLength[not(@type)]/decimalFormat/pattern');
                if (isset($patternResult[0])) {
                    $localeData->decimalPatterns[$numberSystem] = (string)$patternResult[0];
                }
            }
            // Aliases nodes are in root.xml only. They avoid duplicated data.
            // We browse aliases after all regular patterns have been defined, and duplicate data for target number
            // systems.
            foreach ($numbersData->decimalFormats as $format) {
                /** @var SimplexmlElement $format */
                $numberSystem = (string)$format['numberSystem'];
                // If alias is set, we just copy data from another numbering system :
                $alias = $format->alias;
                if ($alias
                    && preg_match(
                        "#^\.\.\/decimalFormats\[@numberSystem='([^)]+)'\]$#",
                        (string)$alias['path'],
                        $matches
                    )
                ) {
                    $aliasNumSys                                = $matches[1];
                    $localeData->decimalPatterns[$numberSystem] = $localeData->decimalPatterns[$aliasNumSys];
                    continue;
                }
            }
        }
        // Percent patterns (by numbering system)
        if (isset($numbersData->percentFormats)) {
            foreach ($numbersData->percentFormats as $format) {
                $numberSystem  = (string)$format['numberSystem'];
                $patternResult = $format->xpath('percentFormatLength/percentFormat/pattern');
                if (isset($patternResult[0])) {
                    $localeData->percentPatterns[$numberSystem] = (string)$patternResult[0];
                }
            }
            // Aliases nodes are in root.xml only. They avoid duplicated data.
            // We browse aliases after all regular patterns have been defined, and duplicate data for target number
            // systems.
            foreach ($numbersData->percentFormats as $format) {
                /** @var SimplexmlElement $format */
                $numberSystem = (string)$format['numberSystem'];
                // If alias is set, we just copy data from another numbering system :
                $alias = $format->alias;
                if ($alias
                    && preg_match(
                        "#^\.\.\/percentFormats\[@numberSystem='([^)]+)'\]$#",
                        (string)$alias['path'],
                        $matches
                    )
                ) {
                    $aliasNumSys                                = $matches[1];
                    $localeData->percentPatterns[$numberSystem] = $localeData->percentPatterns[$aliasNumSys];
                    continue;
                }
            }
        }
        // Currency patterns (by numbering system)
        if (isset($numbersData->currencyFormats)) {
            foreach ($numbersData->currencyFormats as $format) {
                /** @var SimplexmlElement $format */
                $numberSystem  = (string)$format['numberSystem'];
                $patternResult = $format->xpath('currencyFormatLength/currencyFormat[@type="standard"]/pattern');
                if (isset($patternResult[0])) {
                    $localeData->currencyPatterns[$numberSystem] = (string)$patternResult[0];
                }
            }
            // Aliases nodes are in root.xml only. They avoid duplicated data.
            // We browse aliases after all regular patterns have been defined, and duplicate data for target number
            // systems.
            foreach ($numbersData->currencyFormats as $format) {
                /** @var SimplexmlElement $format */
                $numberSystem = (string)$format['numberSystem'];
                // If alias is set, we just copy data from another numbering system :
                $alias = $format->alias;
                if ($alias
                    && preg_match(
                        "#^\.\.\/currencyFormats\[@numberSystem='([^)]+)'\]$#",
                        (string)$alias['path'],
                        $matches
                    )
                ) {
                    $aliasNumSys                                 = $matches[1];
                    $localeData->currencyPatterns[$numberSystem] = $localeData->currencyPatterns[$aliasNumSys];
                    continue;
                }
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
        $xmlData      = $this->getMainXmlData($localeTag);
        $currencyData = $xmlData->xpath("/ldml/numbers/currencies/currency[@type='$currencyCode']");
        if (empty($currencyData)) {
            return [];
        }
        $supplementalXmlData = $this->readSupplementalData(self::SUPPL_DATA_CURRENCY);
        $supplementalData    = $this->extractCurrencySupplementalData(
            $supplementalXmlData,
            $currencyCode
        );

        return $this->mapCurrencyData($currencyData[0], $supplementalData);
    }

    /**
     * Maps currency data from SimplexmlElement to a multidimensional array
     *
     * @param SimplexmlElement $xmlCurrencyData
     *  XML currency data
     *
     * @param array $supplementalData
     *  Supplemental currency data
     *
     * @return array
     *  The mapped data
     */
    protected function mapCurrencyData(SimplexmlElement $xmlCurrencyData, $supplementalData)
    {
        // ISO 4217 currency code is carried by "type" attribute of <currency> tag
        // It also actually identifies the tag among others
        $numericIsoCode = isset($supplementalData['numericIsoCode'])
            ? (int)$supplementalData['numericIsoCode']
            : null;
        $decimalDigits  = isset($supplementalData['decimalDigits'])
            ? (int)$supplementalData['decimalDigits']
            : self::DEFAULT_CURRENCY_DIGITS;
        $currencyArray  = [
            'isoCode'        => (string)$xmlCurrencyData['type'],
            'numericIsoCode' => $numericIsoCode,
            'decimalDigits'  => $decimalDigits,
        ];
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

        return $currencyArray;
    }

    /**
     * Extract currency supplemental data from passed XML
     *
     * Returned supplemental data is an indexed array.
     * Example :
     * [
     *     'numericIsoCode' => 123,
     *     'decimalDigits'  => 2,
     * ]
     *
     * @param SimplexmlElement $supplementalXmlData XML to be searched for supplemental data
     * @param string $currencyCode                  The target currency
     *
     * @return array The supplemental data
     *
     * @throws InvalidArgumentException
     */
    protected function extractCurrencySupplementalData(SimplexmlElement $supplementalXmlData, $currencyCode)
    {
        $numericIsoCode = null;
        $decimalDigits  = self::DEFAULT_CURRENCY_DIGITS;
        $codesMapping   = $supplementalXmlData->supplementalData->xpath(
            '//codeMappings/currencyCodes[@type="' . $currencyCode . '"]'
        );
        if (!empty($codesMapping)) {
            /** @var SimplexmlElement $codesMapping */
            $codesMapping   = $codesMapping[0];
            $numericIsoCode = (int)(string)$codesMapping->attributes()->numeric;
        }
        $fractionsData = $supplementalXmlData->supplementalData->xpath(
            '//currencyData/fractions/info[@iso4217="' . $currencyCode . '"]'
        );
        if (!empty($fractionsData)) {
            /** @var SimplexmlElement $fractionsData */
            $fractionsData = $fractionsData[0];
            $decimalDigits = (int)(string)$fractionsData->attributes()->digits;
        }

        return compact('numericIsoCode', 'decimalDigits');
    }

    /**
     * Extract parent locale code
     *
     * @param SimplexmlElement $parentLocaleXmlData
     * @param string $localeTag
     *
     * @return mixed|string
     */
    protected function extractParentLocale(SimplexmlElement $parentLocaleXmlData, $localeTag)
    {
        if (self::CLDR_ROOT_LOCALE === $localeTag) {
            return null;
        }
        $parts = $this->getLocaleParts($localeTag);
        if (empty($parts['region'])) {
            return self::CLDR_ROOT_LOCALE;
        }
        $code    = $parts['language'] . '_' . $parts['region'];
        $results = $parentLocaleXmlData->xpath("//parentLocale[contains(@locales, '$code')]");
        if (empty($results)) {
            return $parts['language'];
        }
        $node = $results[0];

        return (string)$node['parent'];
    }

    /**
     * Extract all existing digits sets from supplemental xml data
     *
     * @return array
     *  eg.:
     *  [
     *      'latn'     => '0123456789',
     *      'arab'     => '٠١٢٣٤٥٦٧٨٩',
     *      'fullwide' => '０１２３４５６７８９',
     *  ]
     */
    protected function getDigitsData()
    {
        $digitsSets = [];
        $results    = $this->numberingSystemsXml->numberingSystems->xpath('//numberingSystem[@type="numeric"]');
        foreach ($results as $numberingSystem) {
            $systemId              = (string)$numberingSystem['id'];
            $digits                = (string)$numberingSystem['digits'];
            $digitsSets[$systemId] = $digits;
        }

        return $digitsSets;
    }
}
