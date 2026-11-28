<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\OrdersPerCustomerKpiRefreshProvider;

class OrdersPerCustomerKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    /**
     * @dataProvider provideCounters
     */
    public function testGetValueDividesValidOrdersByActiveCustomers(int $customers, int $orders, string $expectedValue): void
    {
        $this->statsRepository->method('countActiveCustomers')->willReturn($customers);
        $this->statsRepository->method('countValidOrders')->willReturn($orders);

        $provider = new OrdersPerCustomerKpiRefreshProvider($this->statsRepository, $this->shopContext, $this->kpiConfiguration);

        $this->assertSame($expectedValue, $provider->getValue()->getValue());
    }

    public static function provideCounters(): iterable
    {
        yield 'half' => [4, 10, '2.5'];
        yield 'rounded to two decimals' => [3, 10, '3.33'];
        yield 'no customer' => [0, 10, '0'];
    }

    public function testGetValueWithoutCustomersDoesNotCountOrders(): void
    {
        $this->statsRepository->method('countActiveCustomers')->willReturn(0);
        $this->statsRepository->expects($this->never())->method('countValidOrders');

        (new OrdersPerCustomerKpiRefreshProvider($this->statsRepository, $this->shopContext, $this->kpiConfiguration))->getValue();

        $this->assertCachedValue('ORDERS_PER_CUSTOMER', 0, '+1 day');
    }
}
