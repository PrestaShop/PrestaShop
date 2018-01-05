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

namespace Tests\PrestaShopBundle\Localization\Number;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Localization\Exception\LocalizationException;
use PrestaShopBundle\Localization\Number\Formatter;
use PrestaShopBundle\Localization\Specification\NumberInterface as NumberSpecification;
use PrestaShopBundle\Localization\Specification\NumberSymbolList;

class FormatterTest extends TestCase
{
    protected function setUp()
    {
        if (!defined('PS_ROUND_UP')) {
            define('PS_ROUND_UP', 0);
        }
        if (!defined('PS_ROUND_DOWN')) {
            define('PS_ROUND_DOWN', 1);
        }
        if (!defined('PS_ROUND_HALF_UP')) {
            define('PS_ROUND_HALF_UP', 2);
        }
        if (!defined('PS_ROUND_HALF_DOWN')) {
            define('PS_ROUND_HALF_DOWN', 3);
        }
        if (!defined('PS_ROUND_HALF_EVEN')) {
            define('PS_ROUND_HALF_EVEN', 4);
        }
        if (!defined('PS_ROUND_HALF_ODD')) {
            define('PS_ROUND_HALF_ODD', 5);
        }
    }

    /**
     * @dataProvider provideValidNumberFormatSpecs
     *
     * @param $specs
     * @param $number
     * @param $expectedResult
     *
     * @throws LocalizationException
     */
    public function testFormat($specs, $number, $expectedResult)
    {
        $formatter       = $this->buildFormatter($specs);
        $formattedNumber = $formatter->format($number);

        $this->assertEquals($expectedResult, $formattedNumber);
    }

    protected function buildFormatter($specs)
    {
        // Replace raw symbols data with mocked symbols lists
        $specs['numberSpecification']['symbols'] = $this->mockNumberSymbolsLists(
            $specs['numberSpecification']['symbols']
        );

        /** @var NumberSpecification $numberSpecification */
        $numberSpecification = $this->mockNumberSpecification($specs['numberSpecification']);
        $rounding            = $specs['rounding'];
        $numberingSystem     = $specs['numberingSystem'];

        return new Formatter(
            $numberSpecification,
            $rounding,
            $numberingSystem
        );
    }

    protected function mockNumberSymbolsLists($symbolsData)
    {
        $symbolsLists = [];
        foreach ($symbolsData as $numberingSystem => $listData) {
            $symbolsLists[$numberingSystem] = $this->mockNumberSymbolsSingleList($listData);
        }

        return $symbolsLists;
    }

    protected function mockNumberSymbolsSingleList($symbolsData)
    {
        // Init defaults
        $decimal                = '';
        $group                  = '';
        $list                   = '';
        $percentSign            = '';
        $minusSign              = '';
        $plusSign               = '';
        $exponential            = '';
        $superscriptingExponent = '';
        $perMille               = '';
        $infinity               = '';
        $nan                    = '';

        // Get the real values
        extract($symbolsData, EXTR_IF_EXISTS);

        $mocked = $this->getMockBuilder(NumberSymbolList::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mocked->method('getDecimal')
            ->willReturn($decimal);
        $mocked->method('getGroup')
            ->willReturn($group);
        $mocked->method('getList')
            ->willReturn($list);
        $mocked->method('getPercentSign')
            ->willReturn($percentSign);
        $mocked->method('getMinusSign')
            ->willReturn($minusSign);
        $mocked->method('getPlusSign')
            ->willReturn($plusSign);
        $mocked->method('getExponential')
            ->willReturn($exponential);
        $mocked->method('getSuperscriptingExponent')
            ->willReturn($superscriptingExponent);
        $mocked->method('getPerMille')
            ->willReturn($perMille);
        $mocked->method('getInfinity')
            ->willReturn($infinity);
        $mocked->method('getNan')
            ->willReturn($nan);

        return $mocked;
    }

    protected function mockNumberSpecification($numberSpecData)
    {
        // Init defaults
        $positivePattern    = '';
        $negativePattern    = '';
        $symbols            = [];
        $maxFractionDigits  = '';
        $minFractionDigits  = '';
        $groupingUsed       = true;
        $primaryGroupSize   = '';
        $secondaryGroupSize = '';

        // Get the real values
        extract($numberSpecData, EXTR_IF_EXISTS);

        $mocked = $this->getMockBuilder(NumberSpecification::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mocked->method('getPositivePattern')
            ->willReturn($positivePattern);
        $mocked->method('getNegativePattern')
            ->willReturn($negativePattern);
        $mocked->method('getMaxFractionDigits')
            ->willReturn($maxFractionDigits);
        $mocked->method('getMinFractionDigits')
            ->willReturn($minFractionDigits);
        $mocked->method('isGroupingUsed')
            ->willReturn($groupingUsed);
        $mocked->method('getPrimaryGroupSize')
            ->willReturn($primaryGroupSize);
        $mocked->method('getSecondaryGroupSize')
            ->willReturn($secondaryGroupSize);
        $mocked->method('getSymbolsByNumberingSystem')
            ->willReturnMap(
                [
                    ['latn', $symbols['latn']],
                ]
            );

        return $mocked;
    }

    public function provideValidNumberFormatSpecs()
    {
        return [
            'French positive number'   => [
                'specs'    => [
                    'numberSpecification' => [
                        'positivePattern'    => '#,##0.###',
                        'negativePattern'    => '-#,##0.###',
                        'symbols'            => [
                            'latn' => [
                                'decimal'                => ',',
                                'group'                  => ' ',
                                'list'                   => ';',
                                'percentSign'            => '%',
                                'minusSign'              => '-',
                                'plusSign'               => '+',
                                'exponential'            => 'E',
                                'superscriptingExponent' => '^',
                            ],
                        ],
                        'maxFractionDigits'  => 3,
                        'minFractionDigits'  => 0,
                        'groupingUsed'       => true,
                        'primaryGroupSize'   => 3,
                        'secondaryGroupSize' => 3,
                    ],
                    'rounding'            => 2,      // Half Up (PS_ROUND_HALF_UP)
                    'numberingSystem'     => 'latn', // Occidental numbering system
                ],
                'number'   => 123456.789,
                'expected' => '123 456,789',
            ],
            'French negative number'   => [
                'specs'    => [
                    'numberSpecification' => [
                        'positivePattern'    => '#,##0.###',
                        'negativePattern'    => '-#,##0.###',
                        'symbols'            => [
                            'latn' => [
                                'decimal'                => ',',
                                'group'                  => ' ',
                                'list'                   => ';',
                                'percentSign'            => '%',
                                'minusSign'              => '-',
                                'plusSign'               => '+',
                                'exponential'            => 'E',
                                'superscriptingExponent' => '^',
                            ],
                        ],
                        'maxFractionDigits'  => 3,
                        'minFractionDigits'  => 0,
                        'groupingUsed'       => true,
                        'primaryGroupSize'   => 3,
                        'secondaryGroupSize' => 3,
                    ],
                    'rounding'            => 2,      // Half Up (PS_ROUND_HALF_UP)
                    'numberingSystem'     => 'latn', // Occidental numbering system
                ],
                'number'   => -123456.789,
                'expected' => '-123 456,789',
            ],
            'English positive number'  => [
                'specs'    => [
                    'numberSpecification' => [
                        'positivePattern'    => '#,##0.###',
                        'negativePattern'    => '-#,##0.###',
                        'symbols'            => [
                            'latn' => [
                                'decimal'                => '.',
                                'group'                  => ',',
                                'list'                   => ';',
                                'percentSign'            => '%',
                                'minusSign'              => '-',
                                'plusSign'               => '+',
                                'exponential'            => 'E',
                                'superscriptingExponent' => '^',
                            ],
                        ],
                        'maxFractionDigits'  => 3,
                        'minFractionDigits'  => 0,
                        'groupingUsed'       => true,
                        'primaryGroupSize'   => 3,
                        'secondaryGroupSize' => 3,
                    ],
                    'rounding'            => 2,      // Half Up (PS_ROUND_HALF_UP)
                    'numberingSystem'     => 'latn', // Occidental numbering system
                ],
                'number'   => 123456.789,
                'expected' => '123,456.789',
            ],
            'Too much fraction zeroes' => [
                'specs'    => [
                    'numberSpecification' => [
                        'positivePattern'    => '#,##0.###',
                        'negativePattern'    => '-#,##0.###',
                        'symbols'            => [
                            'latn' => [
                                'decimal'                => '.',
                                'group'                  => ',',
                                'list'                   => ';',
                                'percentSign'            => '%',
                                'minusSign'              => '-',
                                'plusSign'               => '+',
                                'exponential'            => 'E',
                                'superscriptingExponent' => '^',
                            ],
                        ],
                        'maxFractionDigits'  => 3,
                        'minFractionDigits'  => 0,
                        'groupingUsed'       => true,
                        'primaryGroupSize'   => 3,
                        'secondaryGroupSize' => 3,
                    ],
                    'rounding'            => 2,      // Half Up (PS_ROUND_HALF_UP)
                    'numberingSystem'     => 'latn', // Occidental numbering system
                ],
                'number'   => '0.70000',
                'expected' => '0.7',
            ],
            'Rounding needed 1'        => [
                'specs'    => [
                    'numberSpecification' => [
                        'positivePattern'    => '#,##0.###',
                        'negativePattern'    => '-#,##0.###',
                        'symbols'            => [
                            'latn' => [
                                'decimal'                => '.',
                                'group'                  => ',',
                                'list'                   => ';',
                                'percentSign'            => '%',
                                'minusSign'              => '-',
                                'plusSign'               => '+',
                                'exponential'            => 'E',
                                'superscriptingExponent' => '^',
                            ],
                        ],
                        'maxFractionDigits'  => 3,
                        'minFractionDigits'  => 0,
                        'groupingUsed'       => true,
                        'primaryGroupSize'   => 3,
                        'secondaryGroupSize' => 3,
                    ],
                    'rounding'            => 2,      // Half Up (PS_ROUND_HALF_UP)
                    'numberingSystem'     => 'latn', // Occidental numbering system
                ],
                'number'   => 1.2349,
                'expected' => '1.235',
            ],
            'Rounding needed 2'        => [
                'specs'    => [
                    'numberSpecification' => [
                        'positivePattern'    => '#,##0.###',
                        'negativePattern'    => '-#,##0.###',
                        'symbols'            => [
                            'latn' => [
                                'decimal'                => '.',
                                'group'                  => ',',
                                'list'                   => ';',
                                'percentSign'            => '%',
                                'minusSign'              => '-',
                                'plusSign'               => '+',
                                'exponential'            => 'E',
                                'superscriptingExponent' => '^',
                            ],
                        ],
                        'maxFractionDigits'  => 3,
                        'minFractionDigits'  => 0,
                        'groupingUsed'       => true,
                        'primaryGroupSize'   => 3,
                        'secondaryGroupSize' => 3,
                    ],
                    'rounding'            => 2,      // Half Up (PS_ROUND_HALF_UP)
                    'numberingSystem'     => 'latn', // Occidental numbering system
                ],
                'number'   => 1.2344,
                'expected' => '1.234',
            ],
        ];
    }
}
