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

namespace Tests\Unit\Core\Localization\Number;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\Operation\Rounding;
use PrestaShop\PrestaShop\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Localization\Number\Formatter;
use PrestaShop\PrestaShop\Localization\Specification\Number as NumberSpecification;
use PrestaShop\PrestaShop\Localization\Specification\NumberInterface as NumberSpecificationInterface;
use PrestaShop\PrestaShop\Localization\Specification\NumberSymbolList;
use PrestaShop\PrestaShop\Localization\Specification\Price as PriceSpecification;

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

        $this->assertSame($expectedResult, $formattedNumber);
    }

    protected function buildFormatter($specs)
    {
        /** @var NumberSpecificationInterface $numberSpecification */
        $numberSpecification = $specs['numberSpecification'];
        $rounding            = $specs['rounding'];
        $numberingSystem     = $specs['numberingSystem'];

        return new Formatter(
            $numberSpecification,
            $rounding,
            $numberingSystem
        );
    }

    public function provideValidNumberFormatSpecs()
    {
        return [
            'French positive number'           => [
                'specs'    => [
                    'numberSpecification' => new NumberSpecification(
                        '#,##0.###',
                        '-#,##0.###',
                        ['latn' => new NumberSymbolList(',', ' ', ';', '%', '-', '+', 'E', '^', '‰', '∞', 'NaN')],
                        3,
                        0,
                        true,
                        3,
                        3
                    ),
                    'rounding'            => Rounding::ROUND_HALF_UP,
                    'numberingSystem'     => 'latn', // Occidental numbering system
                ],
                'number'   => 123456.789,
                'expected' => '123 456,789',
            ],
            'French negative number'           => [
                'specs'    => [
                    'numberSpecification' => new NumberSpecification(
                        '#,##0.###',
                        '-#,##0.###',
                        ['latn' => new NumberSymbolList(',', ' ', ';', '%', '-', '+', 'E', '^', '‰', '∞', 'NaN')],
                        3,
                        0,
                        true,
                        3,
                        3
                    ),
                    'rounding'            => Rounding::ROUND_HALF_UP,
                    'numberingSystem'     => 'latn', // Occidental numbering system
                ],
                'number'   => -123456.789,
                'expected' => '-123 456,789',
            ],
            'English positive number'          => [
                'specs'    => [
                    'numberSpecification' => new NumberSpecification(
                        '#,##0.###',
                        '-#,##0.###',
                        ['latn' => new NumberSymbolList('.', ',', ';', '%', '-', '+', 'E', '^', '‰', '∞', 'NaN')],
                        3,
                        0,
                        true,
                        3,
                        3
                    ),
                    'rounding'            => Rounding::ROUND_HALF_UP,
                    'numberingSystem'     => 'latn', // Occidental numbering system
                ],
                'number'   => 123456.789,
                'expected' => '123,456.789',
            ],
            'Too much fraction zeroes'         => [
                'specs'    => [
                    'numberSpecification' => new NumberSpecification(
                        '#,##0.###',
                        '-#,##0.###',
                        ['latn' => new NumberSymbolList('.', ',', ';', '%', '-', '+', 'E', '^', '‰', '∞', 'NaN')],
                        3,
                        0,
                        true,
                        3,
                        3
                    ),
                    'rounding'            => Rounding::ROUND_HALF_UP,
                    'numberingSystem'     => 'latn', // Occidental numbering system
                ],
                'number'   => '0.70000',
                'expected' => '0.7',
            ],
            'More fraction zeroes needed'      => [
                'specs'    => [
                    'numberSpecification' => new NumberSpecification(
                        '#,##0.###',
                        '-#,##0.###',
                        ['latn' => new NumberSymbolList('.', ',', ';', '%', '-', '+', 'E', '^', '‰', '∞', 'NaN')],
                        3,
                        3,
                        true,
                        3,
                        3
                    ),
                    'rounding'            => Rounding::ROUND_HALF_UP,
                    'numberingSystem'     => 'latn', // Occidental numbering system
                ],
                'number'   => '0.7',
                'expected' => '0.700',
            ],
            'Rounding needed 1'                => [
                'specs'    => [
                    'numberSpecification' => new NumberSpecification(
                        '#,##0.###',
                        '-#,##0.###',
                        ['latn' => new NumberSymbolList('.', ',', ';', '%', '-', '+', 'E', '^', '‰', '∞', 'NaN')],
                        3,
                        0,
                        true,
                        3,
                        3
                    ),
                    'rounding'            => Rounding::ROUND_HALF_UP,
                    'numberingSystem'     => 'latn', // Occidental numbering system
                ],
                'number'   => 1.2349,
                'expected' => '1.235',
            ],
            'Rounding needed 2'                => [
                'specs'    => [
                    'numberSpecification' => new NumberSpecification(
                        '#,##0.###',
                        '-#,##0.###',
                        ['latn' => new NumberSymbolList('.', ',', ';', '%', '-', '+', 'E', '^', '‰', '∞', 'NaN')],
                        3,
                        0,
                        true,
                        3,
                        3
                    ),
                    'rounding'            => Rounding::ROUND_HALF_UP,
                    'numberingSystem'     => 'latn', // Occidental numbering system
                ],
                'number'   => 1.2344,
                'expected' => '1.234',
            ],
            'French positive price'            => [
                'specs'    => [
                    'numberSpecification' => new PriceSpecification(
                        '#,##0.## ¤',
                        '-#,##0.## ¤',
                        ['latn' => new NumberSymbolList(',', ' ', ';', '%', '-', '+', 'E', '^', '‰', '∞', 'NaN')],
                        2,
                        2,
                        true,
                        3,
                        3,
                        'symbol',
                        '€',
                        'EUR'
                    ),
                    'rounding'            => Rounding::ROUND_HALF_UP,
                    'numberingSystem'     => 'latn', // Occidental numbering system
                ],
                'number'   => 123456.789,
                'expected' => '123 456,79 €',
            ],
            'French negative price'            => [
                'specs'    => [
                    'numberSpecification' => new PriceSpecification(
                        '#,##0.## ¤',
                        '-#,##0.## ¤',
                        ['latn' => new NumberSymbolList(',', ' ', ';', '%', '-', '+', 'E', '^', '‰', '∞', 'NaN')],
                        2,
                        2,
                        true,
                        3,
                        3,
                        'symbol',
                        '€',
                        'EUR'
                    ),
                    'rounding'            => Rounding::ROUND_HALF_UP,
                    'numberingSystem'     => 'latn', // Occidental numbering system
                ],
                'number'   => -123456.781,
                'expected' => '-123 456,78 €',
            ],
            'USA negative price'               => [
                'specs'    => [
                    'numberSpecification' => new PriceSpecification(
                        '¤ #,##0.##',
                        '-¤ #,##0.##',
                        ['latn' => new NumberSymbolList('.', ',', ';', '%', '-', '+', 'E', '^', '‰', '∞', 'NaN')],
                        2,
                        2,
                        true,
                        3,
                        3,
                        'symbol',
                        '$',
                        'USD'
                    ),
                    'rounding'            => Rounding::ROUND_HALF_UP,
                    'numberingSystem'     => 'latn', // Occidental numbering system
                ],
                'number'   => -123456.789,
                'expected' => '-$ 123,456.79',
            ],
            'USA positive price with ISO code' => [
                'specs'    => [
                    'numberSpecification' => new PriceSpecification(
                        '¤ #,##0.##',
                        '-¤ #,##0.##',
                        ['latn' => new NumberSymbolList('.', ',', ';', '%', '-', '+', 'E', '^', '‰', '∞', 'NaN')],
                        2,
                        2,
                        true,
                        3,
                        3,
                        'code',
                        '$',
                        'USD'
                    ),
                    'rounding'            => Rounding::ROUND_HALF_UP,
                    'numberingSystem'     => 'latn', // Occidental numbering system
                ],
                'number'   => 123456.781,
                'expected' => 'USD 123,456.78',
            ],
        ];
    }
}
