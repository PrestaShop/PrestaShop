<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\DataProvider;

use Currency;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Order\OrderTaxMethodProvider;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderProductsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\CancelProductFormDataProvider;
use Symfony\Contracts\Translation\TranslatorInterface;

class CancelProductFormDataProviderTest extends TestCase
{
    private const ORDER_ID = 42;
    private const CURRENCY_ID = 2;

    private CommandBusInterface&MockObject $queryBus;
    private OrderRepository&MockObject $orderRepository;
    private CancelProductFormDataProvider $provider;

    protected function setUp(): void
    {
        $this->queryBus = $this->createMock(CommandBusInterface::class);
        $this->orderRepository = $this->createMock(OrderRepository::class);

        $currency = $this->createMock(Currency::class);
        $currency->precision = 3;
        $currencyDataProvider = $this->createMock(CurrencyDataProviderInterface::class);
        $currencyDataProvider->method('getCurrencyById')->with(self::CURRENCY_ID)->willReturn($currency);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        $this->provider = new CancelProductFormDataProvider(
            $this->queryBus,
            $currencyDataProvider,
            $this->orderRepository,
            new OrderTaxMethodProvider($translator)
        );
    }

    public function testOnlyTheOrderProductsAreQueriedNotTheWholeOrderView(): void
    {
        $products = [$this->createMock(OrderProductForViewing::class)];
        $this->expectOrderIsRead($products, true);

        $this->assertSame(
            ['products' => $products, 'taxMethod' => 'Tax included', 'precision' => 3],
            $this->provider->getData(self::ORDER_ID)
        );
    }

    public function testAPreloadedOrderViewIsUsedOnceWithoutAnyQuery(): void
    {
        $products = [$this->createMock(OrderProductForViewing::class)];
        $this->provider->preloadOrderForViewing($this->mockOrderForViewing(self::ORDER_ID, $products));
        $this->expectOrderIsRead([], false);

        $this->assertSame(
            ['products' => $products, 'taxMethod' => 'Tax label of the view', 'precision' => 3],
            $this->provider->getData(self::ORDER_ID)
        );
        // The preloaded view is dropped after its first use: the order is read again.
        $this->assertSame(
            ['products' => [], 'taxMethod' => 'Tax excluded', 'precision' => 3],
            $this->provider->getData(self::ORDER_ID)
        );
    }

    public function testPreloadedProductsKeepTheSortingOfTheProductsQuery(): void
    {
        $products = [];
        foreach (['demo_3' => 'c', 'demo_12' => 'b', 'demo_1' => 'a'] as $reference => $key) {
            $products[$key] = $this->createMock(OrderProductForViewing::class);
            $products[$key]->method('getReference')->willReturn($reference);
        }
        $this->provider->preloadOrderForViewing($this->mockOrderForViewing(self::ORDER_ID, array_values($products)));

        $this->assertSame(
            // Same as Sorter::natural() with the default sorting of GetOrderProductsForViewing::all()
            [$products['b'], $products['c'], $products['a']],
            $this->provider->getData(self::ORDER_ID)['products']
        );
    }

    public function testAPreloadedViewOfAnotherOrderIsIgnored(): void
    {
        $orderForViewing = $this->mockOrderForViewing(7, [$this->createMock(OrderProductForViewing::class)]);
        $orderForViewing->expects($this->never())->method('getProducts');
        $this->provider->preloadOrderForViewing($orderForViewing);
        $this->expectOrderIsRead([], true);

        $this->assertSame(
            ['products' => [], 'taxMethod' => 'Tax included', 'precision' => 3],
            $this->provider->getData(self::ORDER_ID)
        );
    }

    private function expectOrderIsRead(array $products, bool $isTaxIncluded): void
    {
        $this->orderRepository
            ->expects($this->once())
            ->method('getPriceDisplaySettings')
            ->with($this->equalTo(new OrderId(self::ORDER_ID)))
            ->willReturn(['currency_id' => self::CURRENCY_ID, 'is_tax_included' => $isTaxIncluded])
        ;
        $this->queryBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->equalTo(GetOrderProductsForViewing::all(self::ORDER_ID)))
            ->willReturn(new OrderProductsForViewing($products))
        ;
    }

    private function mockOrderForViewing(int $orderId, array $products): OrderForViewing&MockObject
    {
        $orderForViewing = $this->createMock(OrderForViewing::class);
        $orderForViewing->method('getId')->willReturn($orderId);
        $orderForViewing->method('getCurrencyId')->willReturn(self::CURRENCY_ID);
        $orderForViewing->method('getTaxMethod')->willReturn('Tax label of the view');
        $orderForViewing->method('getProducts')->willReturn(new OrderProductsForViewing($products));

        return $orderForViewing;
    }
}
