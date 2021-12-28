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

namespace Tests\Unit\Core\Localization\CLDR;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\Reader;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;

class ReaderTest extends TestCase
{
    /**
     * CLDR Reader to be tested
     *
     * @var Reader
     */
    protected $reader;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->reader = new Reader();
    }

    /**
     * @dataProvider provideLocaleData
     *
     * Given a valid CLDR Reader
     * When asking for a specific locale to this reader
     * Then the expected Locale data should be retrieved
     *
     * @param string $localeCode
     * @param array $expectedData
     *
     * @throws LocalizationException
     */
    public function testReadLocaleData(string $localeCode, array $expectedData): void
    {
        $localeData = $this->reader->readLocaleData($localeCode);

        $this->assertInstanceOf(LocaleData::class, $localeData);

        $dns = $localeData->getDefaultNumberingSystem();

        $this->assertSame(
            $expectedData['defaultNumberingSystem'],
            $dns,
            'Wrong group separator'
        );
        $this->assertSame(
            $expectedData['digitsGroupSeparator'],
            $localeData->getNumberSymbols()[$dns]->getGroup(),
            'Wrong group separator'
        );
        $this->assertSame(
            $expectedData['decimalSeparator'],
            $localeData->getNumberSymbols()[$dns]->getDecimal(),
            'Wrong decimal separator'
        );
        $this->assertSame(
            $expectedData['decimalPattern'],
            $localeData->getDecimalPatterns()[$dns],
            'Wrong decimal pattern'
        );
        $this->assertSame(
            $expectedData['currencyPattern'],
            $localeData->getCurrencyPatterns()[$dns],
            'Wrong currency pattern'
        );
        $displayNames = $localeData->getCurrencies()['EUR']->getDisplayNames();
        $this->assertSame(
            $expectedData['euroName'],
            is_array($displayNames) ? $displayNames['default'] : null,
            'Wrong name for Euro'
        );
        $this->assertSame(
            $expectedData['euroNarrowSymbol'],
            $localeData->getCurrencies()['EUR']->getSymbols()['narrow'],
            'Wrong narrow symbol for euro'
        );
        $displayNames = $localeData->getCurrencies()['USD']->getDisplayNames();
        $this->assertSame(
            $expectedData['dollarName'],
            is_array($displayNames) ? $displayNames['default'] : null,
            'Wrong name for US Dollar'
        );
        $this->assertSame(
            $expectedData['dollarDefaultSymbol'],
            $localeData->getCurrencies()['USD']->getSymbols()['default'],
            'Wrong default symbol for dollar'
        );
        $this->assertSame(
            $expectedData['dollarNarrowSymbol'],
            $localeData->getCurrencies()['USD']->getSymbols()['narrow'],
            'Wrong narrow symbol for dollar'
        );
    }

    public function provideLocaleData(): array
    {
        return [
            'root' => [
                'localeCode' => 'root',
                'expectedData' => [
                    'defaultNumberingSystem' => 'latn',
                    'digitsGroupSeparator' => ',',
                    'decimalSeparator' => '.',
                    'decimalPattern' => '#,##0.###',
                    'currencyPattern' => "¤\u{a0}#,##0.00",
                    'euroName' => null,
                    'euroNarrowSymbol' => '€',
                    'dollarName' => null,
                    'dollarDefaultSymbol' => 'US$',
                    'dollarNarrowSymbol' => '$',
                ],
            ],
            'fr' => [
                'localeCode' => 'fr',
                'expectedData' => [
                    'defaultNumberingSystem' => 'latn',
                    'digitsGroupSeparator' => "\u{202f}",
                    'decimalSeparator' => ',',
                    'decimalPattern' => '#,##0.###',
                    'currencyPattern' => "#,##0.00\u{a0}¤",
                    'euroName' => 'euro',
                    'euroNarrowSymbol' => '€',
                    'dollarName' => 'dollar des États-Unis',
                    'dollarDefaultSymbol' => '$US',
                    'dollarNarrowSymbol' => '$',
                ],
            ],
            'fr-FR' => [
                'localeCode' => 'fr-FR',
                'expectedData' => [
                    'defaultNumberingSystem' => 'latn',
                    'digitsGroupSeparator' => "\u{202f}",
                    'decimalSeparator' => ',',
                    'decimalPattern' => '#,##0.###',
                    'currencyPattern' => "#,##0.00\u{a0}¤",
                    'euroName' => 'euro',
                    'euroNarrowSymbol' => '€',
                    'dollarName' => 'dollar des États-Unis',
                    'dollarDefaultSymbol' => '$US',
                    'dollarNarrowSymbol' => '$',
                ],
            ],
            'fr-CH' => [
                'localeCode' => 'fr-CH',
                'expectedData' => [
                    'defaultNumberingSystem' => 'latn',
                    'digitsGroupSeparator' => "\u{202f}",
                    'decimalSeparator' => ',',
                    'decimalPattern' => '#,##0.###',
                    'currencyPattern' => "#,##0.00\u{a0}¤",
                    'euroName' => 'euro',
                    'euroNarrowSymbol' => '€',
                    'dollarName' => 'dollar des États-Unis',
                    'dollarDefaultSymbol' => '$US',
                    'dollarNarrowSymbol' => '$',
                ],
            ],
            'en-GB' => [
                'localeCode' => 'en-GB',
                'expectedData' => [
                    'defaultNumberingSystem' => 'latn',
                    'digitsGroupSeparator' => ',',
                    'decimalSeparator' => '.',
                    'decimalPattern' => '#,##0.###',
                    'currencyPattern' => '¤#,##0.00',
                    'euroName' => 'Euro',
                    'euroNarrowSymbol' => '€',
                    'dollarName' => 'US Dollar',
                    'dollarDefaultSymbol' => 'US$',
                    'dollarNarrowSymbol' => '$',
                ],
            ],
        ];
    }
}
