<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\DataProvider;

use Currency;
use Generator;
use Order;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Currency\CurrencyDataProvider;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\AddProductFormDataProvider;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\EditProductFormDataProvider;

class OrderProductFormDataProviderTest extends TestCase
{
    /**
     * @dataProvider getProviderClasses
     */
    public function testCurrencyIsReadFromTheOrderWithoutBuildingTheOrderView(string $providerClass): void
    {
        $order = $this->createMock(Order::class);
        $order->id_currency = 2;

        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo(new OrderId(42)))
            ->willReturn($order)
        ;

        $queryBus = $this->createMock(CommandBusInterface::class);
        $queryBus->expects($this->never())->method('handle');

        $currency = $this->createMock(Currency::class);
        $currencyDataProvider = $this->createMock(CurrencyDataProvider::class);
        $currencyDataProvider
            ->expects($this->once())
            ->method('getCurrencyById')
            ->with(2)
            ->willReturn($currency)
        ;

        $provider = new $providerClass(
            $queryBus,
            $currencyDataProvider,
            $this->createMock(FeatureFlagStateCheckerInterface::class),
            $orderRepository
        );
        $data = $provider->getData(42);

        $this->assertSame(42, $data['order_id']);
        $this->assertSame($currency, $data['currency']);
    }

    public static function getProviderClasses(): Generator
    {
        yield [AddProductFormDataProvider::class];
        yield [EditProductFormDataProvider::class];
    }
}
