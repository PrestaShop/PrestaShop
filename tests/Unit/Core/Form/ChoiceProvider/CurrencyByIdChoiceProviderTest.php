<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\ChoiceProvider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\CurrencyByIdChoiceProvider;

class CurrencyByIdChoiceProviderTest extends TestCase
{
    /**
     * Everywhere a currency is picked for immediate use - an order, a product price - only the
     * enabled ones belong in the list.
     */
    public function testItAsksForEnabledCurrenciesByDefault(): void
    {
        $provider = new CurrencyByIdChoiceProvider($this->createDataProviderExpecting(true));

        self::assertSame(['Euro (EUR)' => 1], $provider->getChoices());
    }

    /**
     * Payment restrictions are configured before a currency is switched on, so that screen has to
     * offer the disabled ones too.
     */
    public function testItAsksForEveryCurrencyWhenDisabledOnesAreIncluded(): void
    {
        $provider = new CurrencyByIdChoiceProvider($this->createDataProviderExpecting(false), true);

        self::assertSame(['Euro (EUR)' => 1, 'US Dollar (USD)' => 99], $provider->getChoices());
    }

    /**
     * The grouping argument is not decoration: in multistore the shop association join returns a
     * currency once per shop, and without it the same currency is offered several times.
     */
    public function testItAlwaysGroupsByCurrency(): void
    {
        $dataProvider = $this->createMock(CurrencyDataProviderInterface::class);
        $dataProvider
            ->expects(self::once())
            ->method('getCurrencies')
            ->with(false, self::anything(), true)
            ->willReturn([]);

        (new CurrencyByIdChoiceProvider($dataProvider))->getChoices();
    }

    /**
     * @param bool $expectedActiveOnly the value the provider must pass as the "active only" argument
     */
    private function createDataProviderExpecting(bool $expectedActiveOnly): CurrencyDataProviderInterface
    {
        $currencies = [
            ['id_currency' => 1, 'name' => 'Euro', 'iso_code' => 'EUR', 'symbol' => '€'],
        ];

        if (!$expectedActiveOnly) {
            $currencies[] = ['id_currency' => 99, 'name' => 'US Dollar', 'iso_code' => 'USD', 'symbol' => '$'];
        }

        $dataProvider = $this->createMock(CurrencyDataProviderInterface::class);
        $dataProvider
            ->expects(self::once())
            ->method('getCurrencies')
            ->with(false, $expectedActiveOnly, true)
            ->willReturn($currencies);

        return $dataProvider;
    }
}
