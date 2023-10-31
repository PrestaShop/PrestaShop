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

namespace Core\Context;

use Currency;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Core\Context\CurrencyContextBuilder;
use Tests\Unit\Core\Configuration\MockConfigurationTrait;

class CurrencyContextBuilderTest extends TestCase
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
            $this->mockConfiguration(['PS_LANG_DEFAULT' => $languageId])
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
