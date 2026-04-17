<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Context;

use Currency;
use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Core\Context\CurrencyContextBuilder;
use Tests\Unit\Core\Configuration\MockConfigurationTrait;

class CurrencyContextBuilderTest extends ContextBuilderTestCase
{
    use MockConfigurationTrait;

    private const EN_ID = 3;
    private const FR_ID = 5;
    private const NON_EXISTENT_LANGUAGE_ID = 42;

    /**
     * @dataProvider getCurrencyValues
     *
     * @param int $languageId
     * @param string $expectedName
     * @param string $expectedSymbol
     * @param string $expectedPattern
     */
    public function testBuild(int $languageId, string $expectedName, string $expectedSymbol, string $expectedPattern): void
    {
        $currency = $this->mockCurrency();
        $builder = new CurrencyContextBuilder(
            $this->mockCurrencyRepository($currency),
            $this->createMock(ContextStateManager::class),
            $this->mockLanguageContext($languageId)
        );
        $builder->setCurrencyId($currency->id);

        $currencyContext = $builder->build();
        $this->assertEquals($currency->id, $currencyContext->getId());
        $this->assertEquals($expectedName, $currencyContext->getName());
        $this->assertEquals($currency->getLocalizedNames(), $currencyContext->getLocalizedNames());
        $this->assertEquals($currency->iso_code, $currencyContext->getIsoCode());
        $this->assertEquals($currency->numeric_iso_code, $currencyContext->getNumericIsoCode());
        $this->assertEquals(new DecimalNumber((string) $currency->getConversionRate()), $currencyContext->getConversionRate());
        $this->assertEquals($expectedSymbol, $currencyContext->getSymbol());
        $this->assertEquals($currency->getLocalizedSymbols(), $currencyContext->getLocalizedSymbols());
        $this->assertEquals($currency->precision, $currencyContext->getPrecision());
        $this->assertEquals($expectedPattern, $currencyContext->getPattern());
        $this->assertEquals($currency->getLocalizedPatterns(), $currencyContext->getLocalizedPatterns());
    }

    public function getCurrencyValues(): iterable
    {
        yield 'english values' => [
            self::EN_ID,
            'Dollar',
            '$',
            '#,##0.00\u{00A0}¤',
        ];

        yield 'french values' => [
            self::FR_ID,
            'Dollars',
            '€',
            '#,##0.00 ¤',
        ];

        yield 'fallback values are first language' => [
            self::NON_EXISTENT_LANGUAGE_ID,
            'Dollar',
            '$',
            '#,##0.00\u{00A0}¤',
        ];
    }

    private function mockCurrency(): Currency|MockObject
    {
        $currency = $this->createMock(Currency::class);
        $currency->id = 42;
        $currency->iso_code = 'USD';
        $currency->iso_code_num = '069';
        $currency->numeric_iso_code = '427';
        $currency->deleted = true;
        $currency->unofficial = true;
        $currency->modified = false;
        $currency->format = '#,##0.00 ¤';
        $currency->blank = 1;
        $currency->decimals = 2;
        $currency->precision = 2;
        $currency->pattern = '#,##0.00 ¤';

        $currency
            ->method('getLocalizedNames')
            ->willReturn([
                self::EN_ID => 'Dollar',
                self::FR_ID => 'Dollars',
            ])
        ;
        $currency
            ->method('getConversionRate')
            ->willReturn(1.1)
        ;
        $currency
            ->method('getLocalizedSymbols')
            ->willReturn([
                self::EN_ID => '$',
                self::FR_ID => '€',
            ])
        ;
        $currency
            ->method('getLocalizedPatterns')
            ->willReturn([
                self::EN_ID => '#,##0.00\u{00A0}¤',
                self::FR_ID => '#,##0.00 ¤',
            ])
        ;

        return $currency;
    }

    private function mockCurrencyRepository(Currency|MockObject $currency): CurrencyRepository|MockObject
    {
        $repository = $this->createMock(CurrencyRepository::class);
        $repository
            ->method('get')
            ->willReturn($currency)
        ;

        return $repository;
    }
}
