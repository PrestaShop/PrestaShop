<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener\Admin\Context;

use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Employee\EmployeeRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Context\EmployeeContextBuilder;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\EventListener\Admin\Context\EmployeeContextSubscriber;
use PrestaShopBundle\Security\Admin\SessionEmployeeProvider;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\PrestaShopBundle\EventListener\ContextEventListenerTestCase;

class EmployeeContextSubscriberTest extends ContextEventListenerTestCase
{
    public function testEmployeeNotFound(): void
    {
        $event = $this->createRequestEvent(new Request());
        $employeeBuilder = new EmployeeContextBuilder(
            $this->createMock(EmployeeRepository::class),
            $this->createMock(ContextStateManager::class),
            $this->createMock(ShopRepository::class),
        );
        $listener = new EmployeeContextSubscriber(
            $employeeBuilder,
            $this->createMock(Security::class),
            $this->createMock(SessionEmployeeProvider::class),
        );
        $listener->onKernelRequest($event);
        $this->assertEquals(null, $this->getPrivateField($employeeBuilder, 'employeeId'));
    }

    public function testEmployeeFromSymfonySecurity(): void
    {
        $employeeBuilder = new EmployeeContextBuilder(
            $this->createMock(EmployeeRepository::class),
            $this->createMock(ContextStateManager::class),
            $this->createMock(ShopRepository::class),
        );
        $employeeMock = $this->createMock(Employee::class);
        $employeeMock->method('getId')->willReturn(51);
        $securityMock = $this->createMock(Security::class);
        $securityMock->method('getUser')->willReturn($employeeMock);
        $listener = new EmployeeContextSubscriber(
            $employeeBuilder,
            $securityMock,
            $this->createMock(SessionEmployeeProvider::class),
        );

        $event = $this->createRequestEvent(new Request());
        $listener->onKernelRequest($event);
        $this->assertEquals(51, $this->getPrivateField($employeeBuilder, 'employeeId'));
    }

    public function testEmployeeFromSessionEmployeeProvider(): void
    {
        $employeeBuilder = new EmployeeContextBuilder(
            $this->createMock(EmployeeRepository::class),
            $this->createMock(ContextStateManager::class),
            $this->createMock(ShopRepository::class),
        );
        $employeeMock = $this->createMock(Employee::class);
        $employeeMock->method('getId')->willReturn(51);

        $sessionEmployeeProvider = $this->createMock(SessionEmployeeProvider::class);
        $sessionEmployeeProvider->method('getEmployeeFromSession')->willReturn($employeeMock);

        $listener = new EmployeeContextSubscriber(
            $employeeBuilder,
            $this->createMock(Security::class),
            $sessionEmployeeProvider,
        );

        $event = $this->createRequestEvent(new Request());
        $listener->onKernelRequest($event);
        $this->assertEquals(51, $this->getPrivateField($employeeBuilder, 'employeeId'));
    }
}
