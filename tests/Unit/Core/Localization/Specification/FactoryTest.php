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
use PrestaShop\PrestaShop\Core\Localization\Specification\Price;

class FactoryTest extends TestCase
{
    /**
     * @var FactorySpecification
     */
    protected $factory;

    protected function setUp(): void
    {
        parent::setUp();
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
    public function testBuildNumberSpecification(array $data, array $expected): void
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

    public function getNumberData(): array
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
     * @dataProvider getPriceDataWithPrecisions
     */
    public function testBuildPriceSpecification(array $data, array $expected): void
    {
        $specification = $this->factory->buildPriceSpecification(
            $data[0],
            $this->createLocale(
                ...$data
            ),
            new Currency(
                true,
                1,
                'EUR',
                978,
                [
                    $data[0] => '€',
                ],
                2,
                [
                    $data[0] => 'Euro',
                ]
            ),
            true,
            Price::CURRENCY_DISPLAY_CODE
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
     * @dataProvider getPriceDataWithPrecisions
     */
    public function testBuildPriceSpecificationWithMax(array $data, array $expected): void
    {
        $maxFractionDigits = 3;
        $specification = $this->factory->buildPriceSpecification(
            $data[0],
            $this->createLocale(
                ...$data
            ),
            new Currency(
                true,
                1,
                'EUR',
                978,
                [
                    $data[0] => '€',
                ],
                2,
                [
                    $data[0] => 'Euro',
                ]
            ),
            true,
            Price::CURRENCY_DISPLAY_CODE,
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
     * @dataProvider getPriceDataWithPrecisions
     */
    public function testBuildPriceSpecificationWithPrecisionFallback(array $data, array $expected): void
    {
        $currencyPrecision = $data[4]['currencyPrecision'];
        $maxFractionDigits = $data[4]['maxFractionDigits'];
        $expectedMinFractionDigits = $data[4]['expectedMinFractionDigits'];

        $specification = $this->factory->buildPriceSpecification(
            $data[0],
            $this->createLocale(
                ...$data
            ),
            new Currency(
                true,
                1,
                'EUR',
                978,
                [
                    $data[0] => '€',
                ],
                $currencyPrecision,
                [
                    $data[0] => 'Euro',
                ]
            ),
            true,
            Price::CURRENCY_DISPLAY_CODE,
            $maxFractionDigits
        );
        $expected['maxFractionDigits'] = $maxFractionDigits;
        $expected['minFractionDigits'] = $expectedMinFractionDigits;
        self::assertEquals(
            $expected,
            $specification->toArray()
        );
        self::assertEquals($specification->getMaxFractionDigits(), $maxFractionDigits);
    }

    public function getPriceDataWithPrecisions(): array
    {
        // if maxFractionDigits < minFractionDigits, minFractionDigits = maxFractionDigits
        // see PrestaShop\PrestaShop\Core\Localization\Specification\Number
        // The dataProvider provides minFractionDigits === 2
        return [
            [
                [
                    'nl-NL',
                    '¤ #,##0.00;¤ -#,##0.00',
                    '#,##0%',
                    '#,##0.###',
                    [
                        'currencyPrecision' => 6,
                        'maxFractionDigits' => 3,
                        'expectedMinFractionDigits' => 3,
                    ],
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
                    [
                        'currencyPrecision' => 6,
                        'maxFractionDigits' => 3,
                        'expectedMinFractionDigits' => 3,
                    ],
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
            [
                [
                    'nl-NL',
                    '¤ #,##0.00;¤ -#,##0.00',
                    '#,##0%',
                    '#,##0.###',
                    [
                        'currencyPrecision' => 6,
                        'maxFractionDigits' => 6,
                        'expectedMinFractionDigits' => 6,
                    ],
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
                    [
                        'currencyPrecision' => 6,
                        'maxFractionDigits' => 6,
                        'expectedMinFractionDigits' => 6,
                    ],
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
            [
                [
                    'nl-NL',
                    '¤ #,##0.00;¤ -#,##0.00',
                    '#,##0%',
                    '#,##0.###',
                    [
                        'currencyPrecision' => 1,
                        'maxFractionDigits' => 3,
                        'expectedMinFractionDigits' => 1,
                    ],
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
                    [
                        'currencyPrecision' => 1,
                        'maxFractionDigits' => 3,
                        'expectedMinFractionDigits' => 1,
                    ],
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
            [
                [
                    'nl-NL',
                    '¤ #,##0.00;¤ -#,##0.00',
                    '#,##0%',
                    '#,##0.###',
                    [
                        'currencyPrecision' => '4',
                        'maxFractionDigits' => 6,
                        'expectedMinFractionDigits' => 4,
                    ],
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
                    [
                        'currencyPrecision' => '4',
                        'maxFractionDigits' => 6,
                        'expectedMinFractionDigits' => 4,
                    ],
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
            [
                [
                    'nl-NL',
                    '¤ #,##0.00;¤ -#,##0.00',
                    '#,##0%',
                    '#,##0.###',
                    [
                        'currencyPrecision' => null,
                        'maxFractionDigits' => 6,
                        'expectedMinFractionDigits' => 2,
                    ],
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
                    [
                        'currencyPrecision' => null,
                        'maxFractionDigits' => 6,
                        'expectedMinFractionDigits' => 2,
                    ],
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
            [
                [
                    'nl-NL',
                    '¤ #,##0.00;¤ -#,##0.00',
                    '#,##0%',
                    '#,##0.###',
                    [
                        'currencyPrecision' => [],
                        'maxFractionDigits' => 6,
                        'expectedMinFractionDigits' => 2,
                    ],
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
                    [
                        'currencyPrecision' => [],
                        'maxFractionDigits' => 6,
                        'expectedMinFractionDigits' => 2,
                    ],
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
     * @return Locale
     */
    private function createLocale(
        string $code,
        string $currencyPattern,
        string $percentPattern,
        string $decimalPattern
    ): Locale {
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
