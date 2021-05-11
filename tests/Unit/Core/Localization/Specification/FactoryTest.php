<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Localization\Specification;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\Locale;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\NumberSymbolsData;
use PrestaShop\PrestaShop\Core\Localization\Currency;
use PrestaShop\PrestaShop\Core\Localization\Specification\Factory as FactorySpecification;

class FactoryTest extends TestCase
{
    /**
     * @var FactorySpecification
     */
    protected $factory;

    protected function setUp()
    {
        $this->factory = new FactorySpecification();
    }

    /**
     * Given a valid CLDR locale
     * Given a Max Fractions digts to display in a number's decimal
     * Given a boolean to define if we should group digits in a number's integer part
     * Then calling buildNumberSpecification() should return an NumberSpecification
     *
     * @dataProvider getNumberData
     */
    public function testBuildNumberSpecification($data, $expected)
    {
        $specification = $this->factory->buildNumberSpecification(
            $this->createLocale(
                ...$data
            ),
            3,
            true
        );
        $this->assertEquals(
            $expected,
            $specification->toArray()
        );
    }

    public function getNumberData()
    {
        return [
            [
                [
                    'nl-NL',
                    '¤ #,##0.00;¤ -#,##0.00',
                    '#,##0%',
                    '#,##0.###',
                ],
                [
                    'positivePattern' => '#,##0.###',
                    'negativePattern' => '-#,##0.###',
                    'maxFractionDigits' => 3,
                    'minFractionDigits' => 0,
                    'groupingUsed' => true,
                    'primaryGroupSize' => 3,
                    'secondaryGroupSize' => 3,
                    'numberSymbols' => [
                        ',',
                        '.',
                        ';',
                        '%',
                        '-',
                        '+',
                        'E',
                        "\u{00d7}",
                        "\u{2030}",
                        "\u{221e}",
                        'NaN',
                    ],
                ],
            ],
            [
                [
                    'fr-FR',
                    '#,##0.00 ¤',
                    '#,##0 %',
                    '#,##0.###',
                ],
                [
                    'positivePattern' => '#,##0.###',
                    'negativePattern' => '-#,##0.###',
                    'maxFractionDigits' => 3,
                    'minFractionDigits' => 0,
                    'groupingUsed' => true,
                    'primaryGroupSize' => 3,
                    'secondaryGroupSize' => 3,
                    'numberSymbols' => [
                        ',',
                        '.',
                        ';',
                        '%',
                        '-',
                        '+',
                        'E',
                        "\u{00d7}",
                        "\u{2030}",
                        "\u{221e}",
                        'NaN',
                    ],
                ],
            ],
        ];
    }

    /**
     * Given a valid CLDR locale
     * Given a Max Fractions digts to display in a number's decimal
     * Given a boolean to define if we should group digits in a number's integer part
     * Then calling buildPriceSpecification() should return an NumberSpecification
     *
     * @dataProvider getPriceData
     */
    public function testBuildPriceSpecification($data, $expected)
    {
        $specification = $this->factory->buildPriceSpecification(
            $data[0],
            $this->createLocale(
                ...$data
            ),
            new Currency(
                null,
                null,
                'EUR',
                '978',
                [
                    $data[0] => '€',
                ],
                '2',
                [
                    $data[0] => 'Euro',
                ]
            ),
            3,
            true
        );
        $this->assertEquals(
            $expected,
            $specification->toArray()
        );
        $this->assertEquals($specification->getMaxFractionDigits(), $expected['maxFractionDigits']);
    }

    /**
     * Given a valid CLDR locale
     * Given a Max Fractions digits to display in a number's decimal
     * Given a boolean to define if we should group digits in a number's integer part
     * Given an integer to specify max fraction digits
     * Then calling buildPriceSpecification() should return an NumberSpecification
     *
     * @dataProvider getPriceData
     */
    public function testBuildPriceSpecificationWithMax($data, $expected)
    {
        $maxFractionDigits = 3;
        $specification = $this->factory->buildPriceSpecification(
            $data[0],
            $this->createLocale(
                ...$data
            ),
            new Currency(
                null,
                null,
                'EUR',
                '978',
                [
                    $data[0] => '€',
                ],
                '2',
                [
                    $data[0] => 'Euro',
                ]
            ),
            3,
            true,
            $maxFractionDigits
        );
        $expected['maxFractionDigits'] = $maxFractionDigits;
        $this->assertEquals(
            $expected,
            $specification->toArray()
        );
        $this->assertEquals($specification->getMaxFractionDigits(), $maxFractionDigits);
    }

    /**
     * Given a valid CLDR locale
     * Given a Max Fractions digits to display in a number's decimal
     * Given a boolean to define if we should group digits in a number's integer part
     * Given an integer to specify max fraction digits
     * Then calling buildPriceSpecification() should return an NumberSpecification
     *
     * @dataProvider getPriceData
     */
    public function testBuildPriceSpecificationWithPrecisionFallback(array $data, array $expected): void
    {
        // if maxFractionDigits < minFractionDigits, minFractionDigits = maxFractionDigits
        // see PrestaShop\PrestaShop\Core\Localization\Specification\Number
        // The dataProvider provides minFractionDigits === 2
        $precisions = [
            [
                'currencyPrecision' => 6,
                'maxFractionDigits' => 3,
                'expectedMinFractionDigits' => 3,
            ], //minFractionDigits will be equal to 3
            [
                'currencyPrecision' => 6,
                'maxFractionDigits' => 6,
                'expectedMinFractionDigits' => 6,
            ], //minFractionDigits will be equal to 6
            [
                'currencyPrecision' => 1,
                'maxFractionDigits' => 3,
                'expectedMinFractionDigits' => 1,
            ], //minFractionDigits will be equal to 1
            [
                'currencyPrecision' => '4',
                'maxFractionDigits' => 6,
                'expectedMinFractionDigits' => 2,
            ], //minFractionDigits will be equal to 2
            [
                'currencyPrecision' => null,
                'maxFractionDigits' => 6,
                'expectedMinFractionDigits' => 2,
            ], //minFractionDigits will be equal to 2
            [
                'currencyPrecision' => [],
                'maxFractionDigits' => 6,
                'expectedMinFractionDigits' => 2,
            ], //minFractionDigits will be equal to 2
        ];

        foreach ($precisions as $expectedPrecisions) {
            $currencyPrecision = $expectedPrecisions['currencyPrecision'];
            $maxFractionDigits = $expectedPrecisions['maxFractionDigits'];
            $expectedMinFractionDigits = $expectedPrecisions['expectedMinFractionDigits'];

            $specification = $this->factory->buildPriceSpecification(
                $data[0],
                $this->createLocale(
                    ...$data
                ),
                new Currency(
                    null,
                    null,
                    'EUR',
                    '978',
                    [
                        $data[0] => '€',
                    ],
                    $currencyPrecision,
                    [
                        $data[0] => 'Euro',
                    ]
                ),
                3,
                true,
                $maxFractionDigits
            );
            $expected['maxFractionDigits'] = $maxFractionDigits;
            $expected['minFractionDigits'] = $expectedMinFractionDigits;
            self::assertEquals(
                $expected,
                $specification->toArray()
            );
            $this->assertEquals($specification->getMaxFractionDigits(), $maxFractionDigits);
        }
    }

    public function getPriceData()
    {
        return [
            [
                [
                    'nl-NL',
                    '¤ #,##0.00;¤ -#,##0.00',
                    '#,##0%',
                    '#,##0.###',
                ],
                [
                    'positivePattern' => '¤ #,##0.00',
                    'negativePattern' => '¤ -#,##0.00',
                    'maxFractionDigits' => 2,
                    'minFractionDigits' => 2,
                    'groupingUsed' => true,
                    'primaryGroupSize' => 3,
                    'secondaryGroupSize' => 3,
                    'currencyCode' => 'EUR',
                    'currencySymbol' => '€',
                    'numberSymbols' => [
                        ',',
                        '.',
                        ';',
                        '%',
                        '-',
                        '+',
                        'E',
                        "\u{00d7}",
                        "\u{2030}",
                        "\u{221e}",
                        'NaN',
                    ],
                ],
            ],
            [
                [
                    'fr-FR',
                    '#,##0.00 ¤',
                    '#,##0 %',
                    '#,##0.###',
                ],
                [
                    'positivePattern' => '#,##0.00 ¤',
                    'negativePattern' => '-#,##0.00 ¤',
                    'maxFractionDigits' => 5,
                    'minFractionDigits' => 2,
                    'groupingUsed' => true,
                    'primaryGroupSize' => 3,
                    'secondaryGroupSize' => 3,
                    'currencyCode' => 'EUR',
                    'currencySymbol' => '€',
                    'numberSymbols' => [
                        ',',
                        '.',
                        ';',
                        '%',
                        '-',
                        '+',
                        'E',
                        "\u{00d7}",
                        "\u{2030}",
                        "\u{221e}",
                        'NaN',
                    ],
                ],
            ],
        ];
    }

    /**
     * Create LocaleData
     *
     * @param string $code
     * @param string $currencyPattern
     * @param string $percentPattern
     * @param string $decimalPattern
     *
     * @return LocaleData
     */
    private function createLocale(
        $code,
        $currencyPattern,
        $percentPattern,
        $decimalPattern
    ) {
        $localeData = new LocaleData();
        $localeData->setLocaleCode($code);
        $localeData->setDefaultNumberingSystem('latn');
        $localeData->setNumberingSystems(['native' => 'latn']);
        $localeData->setCurrencyPatterns(['latn' => $currencyPattern]);
        $localeData->setPercentPatterns(['latn' => $percentPattern]);
        $localeData->setDecimalPatterns(['latn' => $decimalPattern]);

        $symbolData = new NumberSymbolsData();
        $symbolData->setDecimal(',');
        $symbolData->setGroup('.');
        $symbolData->setList(';');
        $symbolData->setPercentSign('%');
        $symbolData->setMinusSign('-');
        $symbolData->setPlusSign('+');
        $symbolData->setExponential('E');
        $symbolData->setSuperscriptingExponent('×');
        $symbolData->setPerMille('‰');
        $symbolData->setInfinity('∞');
        $symbolData->setNan('NaN');
        $symbolData->setTimeSeparator(':');
        $localeData->setNumberSymbols(['latn' => $symbolData]);

        return new Locale($localeData);
    }
}
