<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Context;

use Employee;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Employee\EmployeeRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Context\EmployeeContextBuilder;

class EmployeeContextBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $employee = $this->mockEmployee();
        $builder = new EmployeeContextBuilder(
            $this->mockEmployeeRepository($employee),
            $this->createMock(ContextStateManager::class),
            $this->mockShopRepository(),
        );
        $builder->setEmployeeId(42);

        $employeeContext = $builder->build();
        $this->assertNotNull($employeeContext->getEmployee());
        $this->assertEquals($employee->id, $employeeContext->getEmployee()->getId());
        $this->assertEquals($employee->id_profile, $employeeContext->getEmployee()->getProfileId());
        $this->assertEquals($employee->id_lang, $employeeContext->getEmployee()->getLanguageId());
        $this->assertEquals($employee->firstname, $employeeContext->getEmployee()->getFirstName());
        $this->assertEquals($employee->lastname, $employeeContext->getEmployee()->getLastName());
        $this->assertEquals($employee->email, $employeeContext->getEmployee()->getEmail());
        $this->assertEquals($employee->passwd, $employeeContext->getEmployee()->getPassword());
        $this->assertEquals($employee->getImage(), $employeeContext->getEmployee()->getImageUrl());
        $this->assertEquals($employee->default_tab, $employeeContext->getEmployee()->getDefaultTabId());
        $this->assertEquals($employee->getDefaultShopID(), $employeeContext->getEmployee()->getDefaultShopId());
        $this->assertEquals($employee->getAssociatedShopIds(), $employeeContext->getEmployee()->getAssociatedShopIds());
        $this->assertEquals($employee->getAssociatedShopGroupIds(), $employeeContext->getEmployee()->getAssociatedShopGroupIds());
    }

    public function testBuildNoEmployee(): void
    {
        $builder = new EmployeeContextBuilder(
            $this->createMock(EmployeeRepository::class),
            $this->createMock(ContextStateManager::class),
            $this->mockShopRepository(),
        );

        $employeeContext = $builder->build();
        $this->assertNull($employeeContext->getEmployee());
    }

    public function testBuildLegacyContext(): void
    {
        $contextManagerMock = $this->createMock(ContextStateManager::class);
        $employee = $this->mockEmployee();
        $builder = new EmployeeContextBuilder(
            $this->mockEmployeeRepository($employee),
            $contextManagerMock,
            $this->mockShopRepository(),
        );
        $builder->setEmployeeId(42);

        $contextManagerMock->expects(static::once())->method('setEmployee')->with($employee);
        $builder->buildLegacyContext();
    }

    private function mockEmployee(): Employee|MockObject
    {
        $employee = $this->createMock(Employee::class);
        $employee->id = 42;
        $employee->id_profile = 51;
        $employee->id_lang = 69;
        $employee->firstname = 'Luck';
        $employee->lastname = 'Skywalker';
        $employee->email = 'luck.skywalker@galaxy.faraway';
        $employee->passwd = 'may4th';
        $employee->default_tab = 99;
        $employee
            ->method('getImage')
            ->willReturn('r2d2.png')
        ;
        $employee
            ->method('getDefaultShopID')
            ->willReturn(12)
        ;
        $employee
            ->method('getAssociatedShopIds')
            ->willReturn([2, 5])
        ;
        $employee
            ->method('getAssociatedShopGroupIds')
            ->willReturn([4, 7])
        ;

        return $employee;
    }

    private function mockEmployeeRepository(Employee|MockObject|null $employee = null): EmployeeRepository|MockObject
    {
        $repository = $this->createMock(EmployeeRepository::class);
        $repository
            ->method('get')
            ->willReturn($employee ?: $this->mockEmployee())
        ;

        return $repository;
    }

    private function mockShopRepository(): ShopRepository|MockObject
    {
        $repository = $this->createMock(ShopRepository::class);
        $repository
            ->method('getAllShopIds')
            ->willReturn([1, 2, 3, 4])
        ;

        return $repository;
    }
}
