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
use PrestaShopBundle\Install\SimplexmlElement;

class Reader implements ReaderInterface
{
    const CLDR_ROOT         = 'localization/CLDR/';
    const CLDR_MAIN         = 'localization/CLDR/core/common/main/';
    const CLDR_SUPPLEMENTAL = 'localization/CLDR/core/common/supplemental/';

    const SUPPL_DATA_CURRENCY       = 'currencyData';
    const SUPPL_DATA_LANGUAGE       = 'languageData';
    const SUPPL_DATA_NUMBERING      = 'numberingSystems';
    const SUPPL_DATA_PARENT_LOCALES = 'parentLocales'; // For specific locales hierarchy

    const DEFAULT_CURRENCY_DIGITS = 2;

    protected $mainXml = [];
    /**
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
     *
     * @return LocaleData
     *  A LocaleData object
     */
    public function readLocaleData($localeCode)
    {
        $this->initSupplementalData();
        $lookup = $this->buildLookup($localeCode);
        // TODO : to be continued
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
     * @see http://www.unicode.org/reports/tr35/tr35.html#Lookup
     */
    protected function buildLookup($localeCode)
    {
        // CLDR filenames use a different notation from IETF.
        $localeCode = str_replace('-', '_', $localeCode);
        $lookup     = [$localeCode];

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
        // root is the... root of all CLDR locales' data
        if ('root' == $localeCode) {
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

        // The "top level" case (when only language code is left : 'en', 'fr'... parent is "root")
        return 'root';
    }

    /**
     * Get locale data by code (either language code or IETF locale tag)
     *
     * @param string $localeCode The wanted locale code
     *
     * @return LocaleData The locale data object
     */
    public function getLocaleByCode($localeCode)
    {
        $localeData = $this->getLocaleData($localeCode);
        while ($localeData->parentLocale) {
            $localeData->fill($this->getLocaleByCode($localeData->parentLocale));
        }

        return $localeData;
    }

    /**
     * Get currency data by ISO 4217 code
     *
     * @param string $isoCode The currency code
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
    protected function getMainXmlData($localeTag)
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
     * @param string $langCode The language code (e.g.: fr, en, de...)
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
     * Get supplemental data from CLDR "supplemental" data files
     *
     * @param string $dataType Type of needed data
     *
     * @return SimpleXMLElement The supplemental CLDR data
     */
    protected function readSupplementalData($dataType)
    {
        $filename = $this->getSupplementalDataFilePath($dataType);
        if (!isset($this->supplementalXml[$filename])) {
            $this->supplementalXml[$filename] = simplexml_load_file($filename);
        }

        return $this->supplementalXml[$filename]->$dataType;
    }

    /**
     * Get path of the CLDR file containing $dataType data
     *
     * @param string $dataType Type of the needed data
     *
     * @return string Path to the appropriate CLDR supplemental data.
     *
     * @throws InvalidArgumentException
     */
    public function getSupplementalDataFilePath($dataType)
    {
        switch ($dataType) {
            case self::SUPPL_DATA_CURRENCY:
            case self::SUPPL_DATA_LANGUAGE:
            case self::SUPPL_DATA_PARENT_LOCALES:
                $filename = 'supplementalData.xml';
                break;
            case self::SUPPL_DATA_NUMBERING:
                $filename = 'numberingSystems.xml';
                break;
            default:
                throw new InvalidArgumentException('Unknown supplemental data type : ' . $dataType);
                break;
        }
        $path = realpath(_PS_ROOT_DIR_ . '/' . self::CLDR_SUPPLEMENTAL . $filename);
        if (false === $path) {
            throw new InvalidArgumentException("The file $filename does not exist");
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
     */
    protected function getLocaleData($localeTag)
    {
        $xmlData                 = $this->getMainXmlData($localeTag);
        $parentLocaleXmlData     = $this->readSupplementalData(self::SUPPL_DATA_PARENT_LOCALES);
        $numberingSystemsXmlData = $this->readSupplementalData(self::SUPPL_DATA_NUMBERING);
        $parentLocale            = $this->extractParentLocale($parentLocaleXmlData, $localeTag);
        $digits                  = $this->extractDigits($numberingSystemsXmlData);
        $supplementalData        = array(
            'parentLocale' => $parentLocale,
            'digits'       => $digits,
        );

        return $this->mapLocaleData($xmlData, $supplementalData);
    }

    /**
     * Maps locale data from SimplexmlElement to a multidimensional array
     *
     * @param SimplexmlElement $xmlLocaleData XML locale data
     * @param array $supplementalData Supplemental locale data
     *
     * @return LocaleData The mapped data
     */
    protected function mapLocaleData(SimplexmlElement $xmlLocaleData, $supplementalData)
    {
        $localeData = new LocaleData();
        if (isset($supplementalData['parentLocale'])) {
            $localeData->parentLocale = $supplementalData['parentLocale'];
        }
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
                if ($alias = $format->alias) {
                    if (preg_match(
                        "#^\.\.\/decimalFormats\[@numberSystem='([^)]+)'\]$#",
                        (string)$alias['path'],
                        $matches
                    )) {
                        $aliasNumSys                                = $matches[1];
                        $localeData->decimalPatterns[$numberSystem] = $localeData->decimalPatterns[$aliasNumSys];
                        continue;
                    }
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
            // @see comments about aliases above
            foreach ($numbersData->percentFormats as $format) {
                /** @var SimplexmlElement $format */
                $numberSystem = (string)$format['numberSystem'];
                // If alias is set, we just copy data from another numbering system :
                if ($alias = $format->alias) {
                    if (preg_match(
                        "#^\.\.\/percentFormats\[@numberSystem='([^)]+)'\]$#",
                        (string)$alias['path'],
                        $matches
                    )) {
                        $aliasNumSys                                = $matches[1];
                        $localeData->percentPatterns[$numberSystem] = $localeData->percentPatterns[$aliasNumSys];
                        continue;
                    }
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
            // @see comments about aliases above
            foreach ($numbersData->currencyFormats as $format) {
                /** @var SimplexmlElement $format */
                $numberSystem = (string)$format['numberSystem'];
                // If alias is set, we just copy data from another numbering system :
                if ($alias = $format->alias) {
                    if (preg_match(
                        "#^\.\.\/currencyFormats\[@numberSystem='([^)]+)'\]$#",
                        (string)$alias['path'],
                        $matches
                    )) {
                        $aliasNumSys                                 = $matches[1];
                        $localeData->currencyPatterns[$numberSystem] = $localeData->currencyPatterns[$aliasNumSys];
                        continue;
                    }
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
     * @param string $localeTag The output locale tag (in which language do you want the currency data ?)
     *
     * @return array
     */
    protected function readCurrencyData($currencyCode, $localeTag)
    {
        $xmlData      = $this->getMainXmlData($localeTag);
        $currencyData = $xmlData->xpath("/ldml/numbers/currencies/currency[@type='$currencyCode']");
        if (empty($currencyData)) {
            return array();
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
     * @param SimplexmlElement $xmlCurrencyData XML currency data
     * @param array $supplementalData Supplemental currency data
     *
     * @return array The mapped data
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
        $currencyArray  = array(
            'isoCode'        => (string)$xmlCurrencyData['type'],
            'numericIsoCode' => $numericIsoCode,
            'decimalDigits'  => $decimalDigits,
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
     * @param string $currencyCode The target currency
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
        if ('root' === $localeTag) {
            return null;
        }
        $parts = $this->getLocaleParts($localeTag);
        if (empty($parts['region'])) {
            return 'root';
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
     * @param SimplexmlElement $numberingSystemsXmlData
     *
     * @return array|null
     */
    protected function extractDigits(SimplexmlElement $numberingSystemsXmlData)
    {
        $digitsSets = array();
        $results    = $numberingSystemsXmlData->xpath('//numberingSystem[@type="numeric"]');
        foreach ($results as $numberingSystem) {
            $systemId              = (string)$numberingSystem['id'];
            $digits                = (string)$numberingSystem['digits'];
            $digitsSets[$systemId] = $digits;
        }

        return $digitsSets;
    }
}
