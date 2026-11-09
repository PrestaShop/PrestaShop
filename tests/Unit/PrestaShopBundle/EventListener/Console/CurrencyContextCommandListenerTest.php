<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener\Console;

use Context;
use Currency;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\CurrencyContextBuilder;
use PrestaShopBundle\EventListener\Console\CurrencyContextCommandListener;
use Shop;

class CurrencyContextCommandListenerTest extends TestCase
{
    public function testTheDefaultCurrencyIsSetForACommand(): void
    {
        $builder = $this->createMock(CurrencyContextBuilder::class);
        $builder->expects($this->once())->method('setCurrencyId')->with(3);
        $builder->expects($this->once())->method('buildLegacyContext');

        $this->createListener($builder, $this->createContext(1, null), 3)->onConsoleCommand();
    }

    public function testNothingIsSetOnAShopThatIsNotInstalled(): void
    {
        $builder = $this->createMock(CurrencyContextBuilder::class);
        $builder->expects($this->never())->method('buildLegacyContext');
        $configuration = $this->createMock(ConfigurationInterface::class);
        // Reading the configuration would query a database that does not exist yet
        $configuration->expects($this->never())->method('get');
        $legacyContext = $this->createMock(LegacyContext::class);
        $legacyContext->method('getContext')->willReturn($this->createContext(null, null));

        (new CurrencyContextCommandListener($builder, $configuration, $legacyContext))->onConsoleCommand();
    }

    public function testACurrencyAlreadySetIsKept(): void
    {
        $builder = $this->createMock(CurrencyContextBuilder::class);
        $builder->expects($this->never())->method('buildLegacyContext');

        $this->createListener($builder, $this->createContext(1, $this->createMock(Currency::class)), 3)->onConsoleCommand();
    }

    public function testNothingIsSetWithoutADefaultCurrency(): void
    {
        $builder = $this->createMock(CurrencyContextBuilder::class);
        $builder->expects($this->never())->method('buildLegacyContext');

        $this->createListener($builder, $this->createContext(1, null), 0)->onConsoleCommand();
    }

    private function createListener(CurrencyContextBuilder $builder, Context $context, int $defaultCurrencyId): CurrencyContextCommandListener
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->with('PS_CURRENCY_DEFAULT')->willReturn((string) $defaultCurrencyId);
        $legacyContext = $this->createMock(LegacyContext::class);
        $legacyContext->method('getContext')->willReturn($context);

        return new CurrencyContextCommandListener($builder, $configuration, $legacyContext);
    }

    private function createContext(?int $shopId, ?Currency $currency): Context
    {
        $context = $this->createMock(Context::class);
        if (null !== $shopId) {
            $context->shop = $this->createMock(Shop::class);
            $context->shop->id = $shopId;
        }
        $context->currency = $currency;

        return $context;
    }
}
