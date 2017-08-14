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

namespace PrestaShopBundle\Tests\Localization;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Localization\CLDRDataReader;

class CLDRDataReaderTest extends TestCase
{
    /**
     * @var CLDRDataReader
     */
    protected $reader;

    public function setUp()
    {
        $this->reader = new CLDRDataReader();
    }

    /**
     * Given a valid locale code
     * When reading this locale data
     * Retrieved data should match the expectation
     *
     * @param $localeCode
     * @param $expectedData
     *
     * @dataProvider provideValidLocaleData
     */
    public function testItReadsLocaleData($localeCode, $expectedData)
    {
        $localeData = $this->reader->getLocaleByCode($localeCode);

        foreach ($expectedData as $property => $value) {
            if ($property === 'numberSymbols') {
                // Assertions on number symbols lists (array of CLDRNumberSymbolList objects)
                $expectedSymbolsSets = $value;
                foreach ($expectedSymbolsSets as $expectedNumberingSystem => $expectedSymbolsSet) {
                    foreach ($expectedSymbolsSet as $symbolName => $symbolValue) {
                        $this->assertSame(
                            $symbolValue,
                            $localeData->numberSymbols[$expectedNumberingSystem]->$symbolName,
                            'For "' . $localeCode . '" locale, "' . $expectedNumberingSystem . '" numbering system, "'
                            . $symbolName . '" value is not the same as expected'
                        );
                    }
                }
            } else {
                // Assertions on other properties
                $this->assertSame(
                    $value,
                    $localeData->$property,
                    'For ' . $localeCode . ' locale, ' . $property . ' value is not the same as expected'
                );
            }
        }
    }

    /**
     * Given an invalid locale code
     * When trying to read the locale data
     * An InvalidArgumentException should be thrown
     *
     * @expectedException \InvalidArgumentException
     */
    public function testItFailsReadingUnknownLocaleData()
    {
        $this->reader->getLocaleByCode('foo');
    }

    /**
     * Given a valid locale code and currency code,
     * When reading this currency data in this locale language
     * Retrieved data should match the expectation
     *
     * @param string $localeCode
     * @param string $currencyCode
     * @param array  $expectedData
     *
     * @dataProvider provideValidCurrencyData
     */
    public function testItReadsCurrencyData($localeCode, $currencyCode, $expectedData)
    {
        $currencyData = $this->reader->getCurrencyByIsoCode($currencyCode, $localeCode);

        foreach ($expectedData as $property => $value) {
            $this->assertSame($value, $currencyData[$property]);
        }
    }

    public function provideValidLocaleData()
    {
        return array(
            'fr-FR' => array(
                'localeCode'   => 'fr-FR',
                'expectedData' => array(
                    'numberingSystems'      => array(
                        'default' => 'latn',
                        'native'  => 'latn',
                    ),
                    'minimumGroupingDigits' => 1,
                    'numberSymbols'         => array(
                        'arab'    => array(
                            'decimal'                => '٫',
                            'group'                  => '٬',
                            'list'                   => '؛',
                            'percentSign'            => '٪',
                            'plusSign'               => null,
                            'minusSign'              => '‏−',
                            'exponential'            => 'اس',
                            'superscriptingExponent' => '×',
                            'perMille'               => '؉',
                            'infinity'               => '∞',
                            'nan'                    => 'NaN',
                            'timeSeparator'          => ':',
                            'currencyDecimal'        => null, // Optional
                            'currencyGroup'          => null, // Optional
                        ),
                        'arabext' => array(
                            'decimal'                => '٫',
                            'group'                  => '٬',
                            'list'                   => '؛',
                            'percentSign'            => '٪',
                            'plusSign'               => '‎+',
                            'minusSign'              => '‎−',
                            'exponential'            => '×۱۰^',
                            'superscriptingExponent' => '×',
                            'perMille'               => '؉',
                            'infinity'               => '∞',
                            'nan'                    => 'NaN',
                            'timeSeparator'          => null,
                            'currencyDecimal'        => null, // Optional
                            'currencyGroup'          => null, // Optional
                        ),
                        'latn'    => array(
                            'decimal'                => ',',
                            'group'                  => ' ',
                            'list'                   => ';',
                            'percentSign'            => '%',
                            'plusSign'               => '+',
                            'minusSign'              => '-',
                            'exponential'            => 'E',
                            'superscriptingExponent' => '×',
                            'perMille'               => '‰',
                            'infinity'               => '∞',
                            'nan'                    => 'NaN',
                            'timeSeparator'          => ':',
                            'currencyDecimal'        => null, // Optional
                            'currencyGroup'          => null, // Optional
                        ),
                    ),
                    'decimalPatterns'       => array(
                        'latn' => '#,##0.###',
                    ),
                    'percentPatterns'       => array(
                        'latn' => '#,##0 %',
                    ),
                    'currencyPatterns'      => array(
                        'latn' => "#,##0.00 ¤",
                    ),
                ),
            ),
            'fr-CH' => array(
                'localeCode'   => 'fr-CH',
                'expectedData' => array(
                    'numberingSystems'      => array(
                        'default' => 'latn',
                        'native'  => 'latn',
                    ),
                    'minimumGroupingDigits' => 1,
                    'numberSymbols'         => array(
                        'arab'    => array(
                            'decimal'                => '٫',
                            'group'                  => '٬',
                            'list'                   => '؛',
                            'percentSign'            => '٪',
                            'plusSign'               => null,
                            'minusSign'              => '‏−',
                            'exponential'            => 'اس',
                            'superscriptingExponent' => '×',
                            'perMille'               => '؉',
                            'infinity'               => '∞',
                            'nan'                    => 'NaN',
                            'timeSeparator'          => ':',
                            'currencyDecimal'        => null, // Optional
                            'currencyGroup'          => null, // Optional
                        ),
                        'arabext' => array(
                            'decimal'                => '٫',
                            'group'                  => '٬',
                            'list'                   => '؛',
                            'percentSign'            => '٪',
                            'plusSign'               => '‎+',
                            'minusSign'              => '‎−',
                            'exponential'            => '×۱۰^',
                            'superscriptingExponent' => '×',
                            'perMille'               => '؉',
                            'infinity'               => '∞',
                            'nan'                    => 'NaN',
                            'timeSeparator'          => null,
                            'currencyDecimal'        => null, // Optional
                            'currencyGroup'          => null, // Optional
                        ),
                        'latn'    => array(
                            'decimal'                => ',',
                            'group'                  => ' ',
                            'list'                   => ';',
                            'percentSign'            => '%',
                            'plusSign'               => '+',
                            'minusSign'              => '-',
                            'exponential'            => 'E',
                            'superscriptingExponent' => '×',
                            'perMille'               => '‰',
                            'infinity'               => '∞',
                            'nan'                    => 'NaN',
                            'timeSeparator'          => ':',
                            'currencyDecimal'        => '.', // Optional
                            'currencyGroup'          => null, // Optional
                        ),
                    ),
                    'decimalPatterns'       => array(
                        'latn' => '#,##0.###',
                    ),
                    'percentPatterns'       => array(
                        'latn' => '#,##0%',
                    ),
                    'currencyPatterns'      => array(
                        'latn' => "#,##0.00 ¤",
                    ),
                ),
            ),
            'de'    => array(
                'localeCode'   => 'de', // No localization
                'expectedData' => array(
                    'numberingSystems'      => array(
                        'default' => 'latn',
                        'native'  => 'latn',
                    ),
                    'minimumGroupingDigits' => 1,
                    'numberSymbols'         => array(
                        'latn' => array(
                            'decimal'                => ',',
                            'group'                  => '.',
                            'list'                   => ';',
                            'percentSign'            => '%',
                            'plusSign'               => '+',
                            'minusSign'              => '-',
                            'exponential'            => 'E',
                            'superscriptingExponent' => '·',
                            'perMille'               => '‰',
                            'infinity'               => '∞',
                            'nan'                    => 'NaN',
                            'timeSeparator'          => ':',
                            'currencyDecimal'        => null, // Optional
                            'currencyGroup'          => null, // Optional
                        ),
                    ),
                    'decimalPatterns'       => array(
                        'latn' => '#,##0.###',
                    ),
                    'percentPatterns'       => array(
                        'latn' => '#,##0 %',
                    ),
                    'currencyPatterns'      => array(
                        'latn' => "#,##0.00 ¤",
                    ),
                ),
            ),
            'de-AT' => array(
                'localeCode'   => 'de-AT',
                'expectedData' => array(
                    'numberingSystems'      => array(
                        'default' => 'latn',
                        'native'  => 'latn',
                    ),
                    'minimumGroupingDigits' => 1,
                    'numberSymbols'         => array(
                        'latn' => array(
                            'decimal'                => ',',
                            'group'                  => ' ', // Overrides 'de' symbol
                            'list'                   => ';',
                            'percentSign'            => '%',
                            'plusSign'               => '+',
                            'minusSign'              => '-',
                            'exponential'            => 'E',
                            'superscriptingExponent' => '·',
                            'perMille'               => '‰',
                            'infinity'               => '∞',
                            'nan'                    => 'NaN',
                            'timeSeparator'          => ':',
                            'currencyDecimal'        => null, // Optional
                            'currencyGroup'          => '.', // Optional (completes 'de' data)
                        ),
                    ),
                    'decimalPatterns'       => array(
                        'latn' => '#,##0.###',
                    ),
                    'percentPatterns'       => array(
                        'latn' => '#,##0 %',
                    ),
                    'currencyPatterns'      => array(
                        'latn' => "¤ #,##0.00", // Overrides 'de' pattern
                    ),
                ),
            ),
            'fa-IR' => array(
                'localeCode'   => 'fa-IR',
                'expectedData' => array(
                    'numberingSystems'      => array(
                        'default' => 'arabext',
                        'native'  => 'arabext',
                    ),
                    'minimumGroupingDigits' => 1,
                    'numberSymbols'         => array(
                        'arab'    => array(
                            'decimal'                => '٫',
                            'group'                  => '٬',
                            'list'                   => '؛', // RTL
                            'percentSign'            => '٪',
                            'plusSign'               => null,
                            'minusSign'              => null,
                            'exponential'            => null,
                            'superscriptingExponent' => '×',
                            'perMille'               => '؉',
                            'infinity'               => '∞',
                            'nan'                    => null,
                            'timeSeparator'          => ':',
                            'currencyDecimal'        => null, // Optional
                            'currencyGroup'          => null, // Optional
                        ),
                        'arabext' => array(
                            'decimal'                => '٫',
                            'group'                  => '٬',
                            'list'                   => '؛', // RTL
                            'percentSign'            => '‎٪',
                            'plusSign'               => '‎+',
                            'minusSign'              => '‎−',
                            'exponential'            => '×۱۰^',
                            'superscriptingExponent' => '×',
                            'perMille'               => '؉',
                            'infinity'               => '∞',
                            'nan'                    => 'ناعدد', // RTL
                            'timeSeparator'          => ':',
                            'currencyDecimal'        => null, // Optional
                            'currencyGroup'          => null, // Optional
                        ),
                        'latn'    => array(
                            'decimal'                => '.',
                            'group'                  => ',',
                            'list'                   => ';',
                            'percentSign'            => '%',
                            'plusSign'               => '‎+',
                            'minusSign'              => '‎−',
                            'exponential'            => 'E',
                            'superscriptingExponent' => '×',
                            'perMille'               => '‰',
                            'infinity'               => '∞',
                            'nan'                    => 'ناعدد', // RTL
                            'timeSeparator'          => ':',
                            'currencyDecimal'        => null, // Optional
                            'currencyGroup'          => null, // Optional
                        ),
                    ),
                    'decimalPatterns'       => array(
                        'arabext' => '#,##0.###',
                        'latn'    => '#,##0.###',
                    ),
                    'percentPatterns'       => array(
                        'arab'    => '#,##0%',
                        'arabext' => '% #,##0;% -#,##0',
                        'latn'    => '#,##0%',
                    ),
                    'currencyPatterns'      => array(
                        'arab'    => "‎¤#,##0.00",
                        'arabext' => "#,##0.00 ؜¤;؜-#,##0.00 ؜¤", // RTL
                        'latn'    => "‎¤ #,##0.00",
                    ),
                ),
            ),
        );
    }

    public function provideValidCurrencyData()
    {
        return array(
            // Here, no currency data in fr_FR.xml. Everything comes from fr.xml
            'fr-FR - EUR' => array(
                'localeCode'   => 'fr-FR',
                'currencyCode' => 'EUR',
                'expectedData' => array(
                    'isoCode'     => 'EUR',
                    'displayName' => array(
                        'default' => 'euro',
                        'one'     => 'euro',
                        'other'   => 'euros',
                    ),
                    'symbol'      => array(
                        'default' => '€',
                        'narrow'  => '€',
                    ),
                ),
            ),
            // Here, data comes from both en.xml and en-DK.xml files (no overriding)
            'en-DK - DKK' => array(
                'localeCode'   => 'en-DK',
                'currencyCode' => 'DKK',
                'expectedData' => array(
                    'isoCode'     => 'DKK',
                    'displayName' => array( // from en.xml
                        'default' => 'Danish Krone',
                        'one'     => 'Danish krone',
                        'other'   => 'Danish kroner',
                    ),
                    'symbol'      => array( // from en-DK.xml
                        'default' => 'kr.',
                    ),
                ),
            ),
            // In this one, default symbol from fo.xml ("kr") is overridden by fo-DK.xml ("kr.")
            // Narrow symbol stays unchanged from fo.xml
            'fo-DK - DKK' => array(
                'localeCode'   => 'fo-DK',
                'currencyCode' => 'DKK',
                'expectedData' => array(
                    'isoCode'     => 'DKK',
                    'displayName' => array( // from en.xml
                        'default' => 'donsk króna',
                        'one'     => 'donsk króna',
                        'other'   => 'danskar krónur',
                    ),
                    'symbol'      => array( // from en-DK.xml
                        'default' => 'kr.',
                        'narrow'  => 'kr',
                    ),
                ),
            ),
        );
    }
}
