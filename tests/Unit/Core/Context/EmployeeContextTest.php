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

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Model\EmployeeInterface;

class EmployeeContextTest extends TestCase
{
    public function testIsSuperAdmin(): void
    {
        $employeeMock = $this->createMock(EmployeeInterface::class);
        $employeeMock
            ->method('getProfileId')
            ->willReturn(EmployeeContext::SUPER_ADMIN_PROFILE_ID)
        ;
        $employeeContext = new EmployeeContext($employeeMock);
        $this->assertTrue($employeeContext->isSuperAdmin());
    }

    public function testIsNotSuperAdmin(): void
    {
        $employeeMock = $this->createMock(EmployeeInterface::class);
        $employeeMock
            ->method('getProfileId')
            ->willReturn(EmployeeContext::SUPER_ADMIN_PROFILE_ID + 1)
        ;
        $employeeContext = new EmployeeContext($employeeMock);
        $this->assertFalse($employeeContext->isSuperAdmin());
    }

    public function testGetDefaultShopId(): void
    {
        $employeeMock = $this->createMock(EmployeeInterface::class);
        $employeeMock
            ->method('getDefaultShopId')
            ->willReturn(42)
        ;
        $employeeContext = new EmployeeContext($employeeMock);
        $this->assertEquals(42, $employeeContext->getDefaultShopId());
    }

    /**
     * @dataProvider provideShops
     */
    public function testHasAuthorizationOnShop(bool $isSuperAdmin, array $shopIds, int $testedShopId, bool $expectedAuthorization): void
    {
        $employeeMock = $this->createMock(EmployeeInterface::class);
        $employeeMock
            ->method('getProfileId')
            ->willReturn($isSuperAdmin ? EmployeeContext::SUPER_ADMIN_PROFILE_ID : 42)
        ;
        $employeeMock
            ->method('getAssociatedShopIds')
            ->willReturn($shopIds)
        ;
        $employeeContext = new EmployeeContext($employeeMock);

        $this->assertEquals($expectedAuthorization, $employeeContext->hasAuthorizationOnShop($testedShopId));
    }

    public function provideShops(): iterable
    {
        yield 'super admin with no shops is autorized' => [
            true,
            [],
            42,
            true,
        ];

        yield 'super admin with matching shop is authorized' => [
            true,
            [42, 51],
            42,
            true,
        ];

        yield 'not super admin with matching shop is authorized' => [
            false,
            [42],
            42,
            true,
        ];

        yield 'not super admin with no matching shop is not authorized' => [
            false,
            [51],
            42,
            false,
        ];
    }

    /**
     * @dataProvider provideShopGroups
     */
    public function testHasAuthorizationOnShopGroup(bool $isSuperAdmin, array $groupIds, int $testedGroupId, bool $expectedAuthorization): void
    {
        $employeeMock = $this->createMock(EmployeeInterface::class);
        $employeeMock
            ->method('getProfileId')
            ->willReturn($isSuperAdmin ? EmployeeContext::SUPER_ADMIN_PROFILE_ID : 42)
        ;
        $employeeMock
            ->method('getAssociatedShopGroupIds')
            ->willReturn($groupIds)
        ;
        $employeeContext = new EmployeeContext($employeeMock);

        $this->assertEquals($expectedAuthorization, $employeeContext->hasAuthorizationOnShopGroup($testedGroupId));
    }

    public function provideShopGroups(): iterable
    {
        yield 'super admin with no groups is autorized' => [
            true,
            [],
            42,
            true,
        ];

        yield 'super admin with matching group is authorized' => [
            true,
            [42, 51],
            42,
            true,
        ];

        yield 'not super admin with matching group is authorized' => [
            false,
            [42],
            42,
            true,
        ];

        yield 'not super admin with no matching group is not authorized' => [
            false,
            [51],
            42,
            false,
        ];
    }
}
