<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\CurrencyByIdChoiceProvider;

class CurrencyByIdChoiceProviderTest extends ChoiceProviderTestCase
{
    private const ACTIVE_CURRENCIES = [
        ['id_currency' => 1, 'name' => 'Euro', 'iso_code' => 'EUR', 'symbol' => '€'],
        ['id_currency' => 2, 'name' => 'US Dollar', 'iso_code' => 'USD', 'symbol' => '$'],
    ];

    private const ALL_CURRENCIES = [
        ['id_currency' => 1, 'name' => 'Euro', 'iso_code' => 'EUR', 'symbol' => '€'],
        ['id_currency' => 2, 'name' => 'US Dollar', 'iso_code' => 'USD', 'symbol' => '$'],
        ['id_currency' => 3, 'name' => 'British Pound', 'iso_code' => 'GBP', 'symbol' => '£'],
    ];

    public function testGetChoicesReturnsOnlyActiveCurrenciesByDefault(): void
    {
        $provider = new CurrencyByIdChoiceProvider(
            $this->buildDataProviderMock(true, self::ACTIVE_CURRENCIES)
        );

        $this->assertSame(
            ['Euro (EUR)' => 1, 'US Dollar (USD)' => 2],
            $provider->getChoices()
        );
    }

    public function testGetChoicesIncludesInactiveCurrenciesWhenActiveOnlyIsFalse(): void
    {
        $provider = new CurrencyByIdChoiceProvider(
            $this->buildDataProviderMock(false, self::ALL_CURRENCIES),
            false
        );

        $this->assertSame(
            ['Euro (EUR)' => 1, 'US Dollar (USD)' => 2, 'British Pound (GBP)' => 3],
            $provider->getChoices()
        );
    }

    public function testGetChoicesAttributesReturnsSymbolsForActiveCurrencies(): void
    {
        $provider = new CurrencyByIdChoiceProvider(
            $this->buildDataProviderMock(true, self::ACTIVE_CURRENCIES)
        );

        $this->assertSame(
            ['Euro (EUR)' => ['symbol' => '€'], 'US Dollar (USD)' => ['symbol' => '$']],
            $provider->getChoicesAttributes()
        );
    }

    public function testGetChoicesAttributesReturnsSymbolsIncludingInactiveCurrencies(): void
    {
        $provider = new CurrencyByIdChoiceProvider(
            $this->buildDataProviderMock(false, self::ALL_CURRENCIES),
            false
        );

        $this->assertSame(
            [
                'Euro (EUR)' => ['symbol' => '€'],
                'US Dollar (USD)' => ['symbol' => '$'],
                'British Pound (GBP)' => ['symbol' => '£'],
            ],
            $provider->getChoicesAttributes()
        );
    }

    private function buildDataProviderMock(bool $activeOnly, array $returnedCurrencies): CurrencyDataProviderInterface
    {
        $mock = $this->createMock(CurrencyDataProviderInterface::class);
        $mock->expects($this->once())
            ->method('getCurrencies')
            ->with(false, $activeOnly, true)
            ->willReturn($returnedCurrencies);

        return $mock;
    }
}
