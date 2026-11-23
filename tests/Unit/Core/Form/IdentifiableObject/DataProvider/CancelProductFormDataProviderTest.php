<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\DataProvider;

use Currency;
use Order;
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

class CancelProductFormDataProviderTest extends TestCase
{
    public function testOnlyTheOrderProductsAreQueriedNotTheWholeOrderView(): void
    {
        $order = $this->createMock(Order::class);
        $order->id = 42;
        $order->id_currency = 2;

        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo(new OrderId(42)))
            ->willReturn($order)
        ;

        $products = [$this->createMock(OrderProductForViewing::class)];
        $queryBus = $this->createMock(CommandBusInterface::class);
        $queryBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->equalTo(GetOrderProductsForViewing::all(42)))
            ->willReturn(new OrderProductsForViewing($products))
        ;

        $currency = $this->createMock(Currency::class);
        $currency->precision = 3;
        $currencyDataProvider = $this->createMock(CurrencyDataProviderInterface::class);
        $currencyDataProvider
            ->expects($this->once())
            ->method('getCurrencyById')
            ->with(2)
            ->willReturn($currency)
        ;

        $orderTaxMethodProvider = $this->createMock(OrderTaxMethodProvider::class);
        $orderTaxMethodProvider
            ->expects($this->once())
            ->method('getTaxMethod')
            ->with($order)
            ->willReturn('Tax included')
        ;

        $provider = new CancelProductFormDataProvider(
            $queryBus,
            $currencyDataProvider,
            $orderRepository,
            $orderTaxMethodProvider
        );

        $this->assertSame(
            [
                'products' => $products,
                'taxMethod' => 'Tax included',
                'precision' => 3,
            ],
            $provider->getData(42)
        );
    }

    public function testAPreloadedOrderViewIsUsedOnceWithoutAnyQuery(): void
    {
        $products = [$this->createMock(OrderProductForViewing::class)];
        $orderForViewing = $this->createMock(OrderForViewing::class);
        $orderForViewing->method('getId')->willReturn(42);
        $orderForViewing->method('getCurrencyId')->willReturn(2);
        $orderForViewing->method('getTaxMethod')->willReturn('Tax excluded');
        $orderForViewing->method('getProducts')->willReturn(new OrderProductsForViewing($products));

        $order = $this->createMock(Order::class);
        $order->id = 42;
        $order->id_currency = 2;
        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository->expects($this->once())->method('get')->willReturn($order);

        $queryBus = $this->createMock(CommandBusInterface::class);
        $queryBus
            ->expects($this->once())
            ->method('handle')
            ->willReturn(new OrderProductsForViewing([]))
        ;

        $currency = $this->createMock(Currency::class);
        $currency->precision = 2;
        $currencyDataProvider = $this->createMock(CurrencyDataProviderInterface::class);
        $currencyDataProvider->method('getCurrencyById')->with(2)->willReturn($currency);

        $orderTaxMethodProvider = $this->createMock(OrderTaxMethodProvider::class);
        $orderTaxMethodProvider->expects($this->once())->method('getTaxMethod')->willReturn('Tax included');

        $provider = new CancelProductFormDataProvider(
            $queryBus,
            $currencyDataProvider,
            $orderRepository,
            $orderTaxMethodProvider
        );
        $provider->preloadOrderForViewing($orderForViewing);

        $this->assertSame(
            [
                'products' => $products,
                'taxMethod' => 'Tax excluded',
                'precision' => 2,
            ],
            $provider->getData(42)
        );
        // The preloaded view is dropped after its first use: the order is read again.
        $this->assertSame(
            [
                'products' => [],
                'taxMethod' => 'Tax included',
                'precision' => 2,
            ],
            $provider->getData(42)
        );
    }

    public function testPreloadedProductsKeepTheSortingOfTheProductsQuery(): void
    {
        $products = [];
        foreach (['demo_3' => 'c', 'demo_12' => 'b', 'demo_1' => 'a'] as $reference => $key) {
            $products[$key] = $this->createMock(OrderProductForViewing::class);
            $products[$key]->method('getReference')->willReturn($reference);
        }
        $orderForViewing = $this->createMock(OrderForViewing::class);
        $orderForViewing->method('getId')->willReturn(42);
        $orderForViewing->method('getCurrencyId')->willReturn(2);
        $orderForViewing->method('getProducts')->willReturn(new OrderProductsForViewing(array_values($products)));

        $currency = $this->createMock(Currency::class);
        $currency->precision = 2;
        $currencyDataProvider = $this->createMock(CurrencyDataProviderInterface::class);
        $currencyDataProvider->method('getCurrencyById')->willReturn($currency);

        $provider = new CancelProductFormDataProvider(
            $this->createMock(CommandBusInterface::class),
            $currencyDataProvider,
            $this->createMock(OrderRepository::class),
            $this->createMock(OrderTaxMethodProvider::class)
        );
        $provider->preloadOrderForViewing($orderForViewing);

        $this->assertSame(
            // Same as Sorter::natural() with the default sorting of GetOrderProductsForViewing::all()
            [$products['b'], $products['c'], $products['a']],
            $provider->getData(42)['products']
        );
    }

    public function testAPreloadedViewOfAnotherOrderIsIgnored(): void
    {
        $orderForViewing = $this->createMock(OrderForViewing::class);
        $orderForViewing->method('getId')->willReturn(7);
        $orderForViewing->expects($this->never())->method('getProducts');

        $order = $this->createMock(Order::class);
        $order->id = 42;
        $order->id_currency = 2;
        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository->expects($this->once())->method('get')->willReturn($order);

        $queryBus = $this->createMock(CommandBusInterface::class);
        $queryBus->expects($this->once())->method('handle')->willReturn(new OrderProductsForViewing([]));

        $currency = $this->createMock(Currency::class);
        $currency->precision = 2;
        $currencyDataProvider = $this->createMock(CurrencyDataProviderInterface::class);
        $currencyDataProvider->method('getCurrencyById')->willReturn($currency);

        $orderTaxMethodProvider = $this->createMock(OrderTaxMethodProvider::class);
        $orderTaxMethodProvider->method('getTaxMethod')->willReturn('Tax included');

        $provider = new CancelProductFormDataProvider(
            $queryBus,
            $currencyDataProvider,
            $orderRepository,
            $orderTaxMethodProvider
        );
        $provider->preloadOrderForViewing($orderForViewing);

        $this->assertSame([], $provider->getData(42)['products']);
    }
}
