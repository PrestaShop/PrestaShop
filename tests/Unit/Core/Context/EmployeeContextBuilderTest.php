<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Core\Context;

use Employee;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Employee\EmployeeRepository;
use PrestaShop\PrestaShop\Core\Context\EmployeeContextBuilder;

class EmployeeContextBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $employee = $this->mockEmployee();
        $builder = new EmployeeContextBuilder(
            $this->mockEmployeeRepository($employee)
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
            $this->createMock(EmployeeRepository::class)
        );

        $employeeContext = $builder->build();
        $this->assertNull($employeeContext->getEmployee());
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

    private function mockEmployeeRepository(Employee|MockObject $employee = null): EmployeeRepository|MockObject
    {
        $repository = $this->createMock(EmployeeRepository::class);
        $repository
            ->method('get')
            ->willReturn($employee ?: $this->mockEmployee())
        ;

        return $repository;
    }
}
