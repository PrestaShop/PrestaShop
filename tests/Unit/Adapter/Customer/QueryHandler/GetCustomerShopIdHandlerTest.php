<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Customer\QueryHandler;

use Customer;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Customer\QueryHandler\GetCustomerShopIdHandler;
use PrestaShop\PrestaShop\Adapter\Customer\Repository\CustomerRepository;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerShopId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

class GetCustomerShopIdHandlerTest extends TestCase
{
    public function testItReturnsCustomerShopId(): void
    {
        $customerId = new CustomerId(42);
        $customer = $this->createMock(Customer::class);
        $customer->id_shop = 2;
        $customerRepository = $this->createMock(CustomerRepository::class);
        $customerRepository
            ->expects($this->once())
            ->method('get')
            ->with($customerId)
            ->willReturn($customer);

        $handler = new GetCustomerShopIdHandler($customerRepository);

        $shopId = $handler->handle(new GetCustomerShopId($customerId->getValue()));

        $this->assertSame(2, $shopId->getValue());
    }
}
