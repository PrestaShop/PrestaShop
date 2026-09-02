<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Localization;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\Reader;

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
     * @param string $localeCode
     * @param array $expectedData
     */
    public function testReadLocaleData(string $localeCode, array $expectedData): void
    {
        $localeData = $this->reader->readLocaleData($localeCode);

        $this->assertInstanceOf(LocaleData::class, $localeData);

        $dns = $localeData->getDefaultNumberingSystem();

        $this->assertEquals(
            $expectedData['defaultNumberingSystem'],
            $dns,
            'Wrong group separator'
        );
        $this->assertEquals(
            $expectedData['digitsGroupSeparator'],
            $localeData->getNumberSymbols()[$dns]->getGroup(),
            'Wrong group separator'
        );
        $this->assertEquals(
            $expectedData['decimalSeparator'],
            $localeData->getNumberSymbols()[$dns]->getDecimal(),
            'Wrong decimal separator'
        );
        $this->assertEquals(
            $expectedData['decimalPattern'],
            $localeData->getDecimalPatterns()[$dns],
            'Wrong decimal pattern'
        );
        $this->assertEquals(
            $expectedData['currencyPattern'],
            $localeData->getCurrencyPatterns()[$dns],
            'Wrong currency pattern'
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
                ],
            ],
            'fr' => [
                'localeCode' => 'fr',
                'expectedData' => [
                    'defaultNumberingSystem' => 'latn',
                    'digitsGroupSeparator' => hex2bin('e280af'),
                    'decimalSeparator' => ',',
                    'decimalPattern' => '#,##0.###',
                    'currencyPattern' => "#,##0.00\u{a0}¤",
                ],
            ],
            'fr-FR' => [
                'localeCode' => 'fr-FR',
                'expectedData' => [
                    'defaultNumberingSystem' => 'latn',
                    'digitsGroupSeparator' => hex2bin('e280af'),
                    'decimalSeparator' => ',',
                    'decimalPattern' => '#,##0.###',
                    'currencyPattern' => "#,##0.00\u{a0}¤",
                ],
            ],
            'fr-CH' => [
                'localeCode' => 'fr-CH',
                'expectedData' => [
                    'defaultNumberingSystem' => 'latn',
                    'digitsGroupSeparator' => hex2bin('e280af'),
                    'decimalSeparator' => ',',
                    'decimalPattern' => '#,##0.###',
                    'currencyPattern' => "#,##0.00\u{a0}¤",
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
                ],
            ],
        ];
    }
}
