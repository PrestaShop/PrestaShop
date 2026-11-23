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
}
