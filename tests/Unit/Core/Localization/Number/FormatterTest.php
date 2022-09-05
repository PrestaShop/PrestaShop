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

namespace Tests\Unit\Core\Localization\Number;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\Operation\Rounding;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Number\Formatter;
use PrestaShop\PrestaShop\Core\Localization\Specification\Number as NumberSpecification;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberInterface as NumberSpecificationInterface;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberSymbolList;
use PrestaShop\PrestaShop\Core\Localization\Specification\Price as PriceSpecification;

class FormatterTest extends TestCase
{
    protected function setUp(): void
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
     * Given a valid number and valid number specification
     * When asking the number formatter to format the said number, following the specification rules
     * Then the expected result should be retrieved
     *
     * @param array $localeParams
     *                            The locale params
     * @param NumberSpecificationInterface $numberSpecification
     *                                                          The number specification
     * @param int|float|string $number
     *                                 The number to be formatted
     * @param string $expectedResult
     *                               The formatted number
     *
     * @dataProvider provideValidNumberFormatSpecs
     *
     * @throws LocalizationException
     */
    public function testFormat($localeParams, $numberSpecification, $number, $expectedResult)
    {
        $formatter = $this->buildFormatter($localeParams);
        $formattedNumber = $formatter->format($number, $numberSpecification);

        $this->assertSame($expectedResult, $formattedNumber);
    }

    protected function buildFormatter($localeParams)
    {
        $rounding = $localeParams['rounding'];
        $numberingSystem = $localeParams['numberingSystem'];

        return new Formatter(
            $rounding,
            $numberingSystem
        );
    }

    public function provideValidNumberFormatSpecs()
    {
        return [
            'French positive number' => [
                'localeParams' => [
                    'rounding' => Rounding::ROUND_HALF_UP,
                    'numberingSystem' => 'latn', // Occidental numbering system
                ],
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
                'number' => 123456.789,
                'expected' => '123 456,789',
            ],
            'French negative number' => [
                'localeParams' => [
                    'rounding' => Rounding::ROUND_HALF_UP,
                    'numberingSystem' => 'latn', // Occidental numbering system
                ],
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
                'number' => -123456.789,
                'expected' => '-123 456,789',
            ],
            'English positive number' => [
                'localeParams' => [
                    'rounding' => Rounding::ROUND_HALF_UP,
                    'numberingSystem' => 'latn', // Occidental numbering system
                ],
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
                'number' => 123456.789,
                'expected' => '123,456.789',
            ],
            'Too much fraction zeroes' => [
                'localeParams' => [
                    'rounding' => Rounding::ROUND_HALF_UP,
                    'numberingSystem' => 'latn', // Occidental numbering system
                ],
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
                'number' => '0.70000',
                'expected' => '0.7',
            ],
            'More fraction zeroes needed' => [
                'localeParams' => [
                    'rounding' => Rounding::ROUND_HALF_UP,
                    'numberingSystem' => 'latn', // Occidental numbering system
                ],
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
                'number' => '0.7',
                'expected' => '0.700',
            ],
            'Rounding needed 1' => [
                'localeParams' => [
                    'rounding' => Rounding::ROUND_HALF_UP,
                    'numberingSystem' => 'latn', // Occidental numbering system
                ],
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
                'number' => 1.2349,
                'expected' => '1.235',
            ],
            'Rounding needed 2' => [
                'localeParams' => [
                    'rounding' => Rounding::ROUND_HALF_UP,
                    'numberingSystem' => 'latn', // Occidental numbering system
                ],
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
                'number' => 1.2344,
                'expected' => '1.234',
            ],
            'French positive price' => [
                'localeParams' => [
                    'rounding' => Rounding::ROUND_HALF_UP,
                    'numberingSystem' => 'latn', // Occidental numbering system
                ],
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
                'number' => 123456.789,
                'expected' => '123 456,79 €',
            ],
            'French negative price' => [
                'localeParams' => [
                    'rounding' => Rounding::ROUND_HALF_UP,
                    'numberingSystem' => 'latn', // Occidental numbering system
                ],
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
                'number' => -123456.781,
                'expected' => '-123 456,78 €',
            ],
            'USA negative price' => [
                'localeParams' => [
                    'rounding' => Rounding::ROUND_HALF_UP,
                    'numberingSystem' => 'latn', // Occidental numbering system
                ],
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
                'number' => -123456.789,
                'expected' => '-$ 123,456.79',
            ],
            'USA positive price with ISO code' => [
                'localeParams' => [
                    'rounding' => Rounding::ROUND_HALF_UP,
                    'numberingSystem' => 'latn', // Occidental numbering system
                ],
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
                'number' => 123456.781,
                'expected' => 'USD 123,456.78',
            ],
        ];
    }
}
