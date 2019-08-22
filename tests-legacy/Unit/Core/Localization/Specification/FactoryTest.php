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

namespace LegacyTests\Unit\Core\Localization\Specification;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\Currency;
use PrestaShop\PrestaShop\Core\Localization\CLDR\Locale;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\NumberSymbolsData;
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
