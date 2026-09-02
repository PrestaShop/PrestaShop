<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\API\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Core\Context\CurrencyContextBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\PrestaShopBundle\EventListener\ContextEventListenerTestCase;

class CurrencyContextListenerTest extends ContextEventListenerTestCase
{
    private const DEFAULT_CURRENCY_ID = 42;
    private const QUERY_CURRENCY_ID = 51;
    private const SHOP_ID = 69;

    public function testCurrencyContextBasedOnRequestParameter(): void
    {
        // Create request that mimic a call to external API
        $event = $this->createRequestEvent(new Request(['currencyId' => self::QUERY_CURRENCY_ID]));

        $currencyContextBuilder = new CurrencyContextBuilder(
            $this->createMock(CurrencyRepository::class),
            $this->createMock(ContextStateManager::class),
            $this->createMock(LanguageContext::class)
        );

        $listener = new CurrencyContextListener(
            $currencyContextBuilder,
            $this->mockConfiguration(['PS_CURRENCY_DEFAULT' => self::DEFAULT_CURRENCY_ID], ShopConstraint::shop(self::SHOP_ID)),
            $this->mockShopContext()
        );

        $listener->onKernelRequest($event);
        $this->assertEquals(self::QUERY_CURRENCY_ID, $this->getPrivateField($currencyContextBuilder, 'currencyId'));
    }

    public function testCurrencyContextBasedOnShopConfiguration(): void
    {
        // Create request that mimic a call to external API (no currencyId parameter)
        $event = $this->createRequestEvent(new Request());

        $currencyContextBuilder = new CurrencyContextBuilder(
            $this->createMock(CurrencyRepository::class),
            $this->createMock(ContextStateManager::class),
            $this->createMock(LanguageContext::class)
        );

        $listener = new CurrencyContextListener(
            $currencyContextBuilder,
            $this->mockConfiguration(['PS_CURRENCY_DEFAULT' => self::DEFAULT_CURRENCY_ID], ShopConstraint::shop(self::SHOP_ID)),
            $this->mockShopContext()
        );

        $listener->onKernelRequest($event);
        $this->assertEquals(self::DEFAULT_CURRENCY_ID, $this->getPrivateField($currencyContextBuilder, 'currencyId'));
    }

    private function mockShopContext(): ShopContext|MockObject
    {
        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('getId')->willReturn(self::SHOP_ID);

        return $shopContext;
    }
}
