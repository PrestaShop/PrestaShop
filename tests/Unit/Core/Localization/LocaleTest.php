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

namespace Tests\Unit\Core\Localization;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\Operation\Rounding;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Number\Formatter;
use PrestaShop\PrestaShop\Core\Localization\Specification\Number as NumberSpecification;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberCollection;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberSymbolList;
use PrestaShop\PrestaShop\Core\Localization\Specification\Price as PriceSpecification;

class LocaleTest extends TestCase
{
    /**
     * @var Locale
     */
    protected $cldrLocale;

    /**
     * Setting up the locale to be tested.
     *
     * The passed specifications are french (french number formatting and Euro currency)
     *
     * For more formatting cases, @see \Tests\Unit\Core\Localization\Number\FormatterTest
     */
    protected function setUp()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $numberSpecification = new NumberSpecification(
            '#,##0.###',
            '-#,##0.###',
            ['latn' => new NumberSymbolList(',', ' ', ';', '%', '-', '+', 'E', '^', '‰', '∞', 'NaN')],
            3,
            2,
            true,
            3,
            3
        );
        /** noinspection end */

        /** @noinspection PhpUnhandledExceptionInspection */
        $priceSpecsCollection = (new NumberCollection())
            ->add(
                'EUR',
                new PriceSpecification(
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
                )
            );
        /** @noinspection end */

        $formatter = new Formatter(
            Rounding::ROUND_HALF_UP,
            'latn'
        );

        // $this->locale was already taken by TestCase class
        $this->cldrLocale = new Locale(
            'fr-FR',
            $numberSpecification,
            $priceSpecsCollection,
            $formatter
        );
    }

    /**
     * Given a valid number (numeric)
     * When asking the locale to format this number
     * Then the expected formatted number should be retrieved
     *
     * @param int|float $number
     *  The number to be formatted
     *
     * @param string $expected
     *  The formatted number
     *
     * @dataProvider provideValidNumbers
     *
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public function testFormatNumber($number, $expected)
    {
        $formattedNumber = $this->cldrLocale->formatNumber($number);

        $this->assertSame($expected, $formattedNumber);
    }

    /**
     * Given an invalid number (not numeric)
     * When asking the locale to format it
     * Then an exception should be raised
     *
     * For more formatting cases, @see \Tests\Unit\Core\Localization\Number\FormatterTest
     *
     * @expectedException \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public function testFormatNumberWithInvalidRawNumber()
    {
        $this->cldrLocale->formatNumber('foobar');
    }

    /**
     * Provide valid numbers data (number + expected formatting result)
     *
     * @return array
     * [
     *     [<raw number>, <expected formatted number>],
     *     [...],
     * ]
     */
    public function provideValidNumbers()
    {
        return [
            [123456.789, '123 456,789'],
            [-123456.789, '-123 456,789'],
            ['0.7', '0,70'],
            [1.2349, '1,235'],
            [1.2343, '1,234'],
        ];
    }

    /**
     * Given a valid number (numeric) and a valid currency code
     * When asking the locale to format this number as a price of this currency
     * Then the expected formatted price should be retrieved
     *
     * For more formatting cases, @see \Tests\Unit\Core\Localization\Number\FormatterTest
     *
     * @param int|float|string $number
     *  The number to be formatted
     *
     * @param string $currencyCode
     *  The currency code
     *
     * @param string $expected
     *  The formatted number
     *
     * @dataProvider provideValidPriceData
     *
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public function testFormatPrice($number, $currencyCode, $expected)
    {
        $price = $this->cldrLocale->formatPrice($number, $currencyCode);

        $this->assertSame($expected, $price);
    }

    /**
     * Provide valid price data (number + currency code + expected formatted price)
     *
     * @return array
     * [
     *     [<raw number>, <currency code>, <expected formatted price>],
     *     [...],
     * ]
     */
    public function provideValidPriceData()
    {
        return [
            [123456.789, 'EUR', '123 456,79 €'],
            [-123456.781, 'EUR', '-123 456,78 €'],
        ];
    }

    /**
     * Given an invalid number (not numeric) or invalid currency
     * When asking the locale to format the number as a price
     * Then an exception should be raised
     *
     * @param mixed $number
     *  Potentially invalid number
     *
     * @param mixed $currency
     *  Potentially invalid currency
     *
     * @dataProvider provideInvalidPriceData
     *
     * @expectedException \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public function testFormatNumberWithInvalidPriceData($number, $currency)
    {
        $this->cldrLocale->formatPrice($number, $currency);
    }

    public function provideInvalidPriceData()
    {
        return [
            'Invalid number'   => ['foobar', 'EUR'],
            'Unknown currency' => [123456.789, 'USD'],
            'Invalid currency' => [123456.789, 123],
        ];
    }
}
