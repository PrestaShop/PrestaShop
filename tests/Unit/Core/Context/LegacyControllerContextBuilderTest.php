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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Core\Context\Employee;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\LegacyControllerContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShopBundle\Entity\Repository\TabRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Unit\Core\Configuration\MockConfigurationTrait;

class LegacyControllerContextBuilderTest extends TestCase
{
    use MockConfigurationTrait;

    /**
     * @dataProvider getControllerValues
     *
     * @param string $controllerName
     * @param ?string $className
     * @param int $multishopContext
     */
    public function testBuild(string $controllerName, ?string $className, int $multishopContext): void
    {
        $builder = new LegacyControllerContextBuilder(
            $this->mockEmployeeContext(),
            $this->createMock(ContextStateManager::class),
            ['AdminCartsController'],
            $this->mockTabRepository(),
            $this->createMock(ContainerInterface::class),
        );

        $builder->setControllerName($controllerName);
        $legacyController = $builder->build();

        $this->assertEquals($className, $legacyController->className);
        $this->assertEquals('admin', $legacyController->controller_type);
        $this->assertEquals($controllerName, $legacyController->php_self);
        $this->assertEquals($controllerName, $legacyController->controller_name);
        $this->assertEquals(10, $legacyController->id);
        $this->assertEquals($multishopContext, $legacyController->multishop_context);
    }

    public function getControllerValues(): iterable
    {
        yield 'AdminCartsController' => [
            'AdminCartsController',
            'Cart',
            ShopConstraint::ALL_SHOPS,
        ];

        yield 'AdminAccessController' => [
            'AdminAccessController',
            'Profile',
            7,
        ];

        yield 'AdminController' => [
            'AdminController',
            null,
            7,
        ];
    }

    public function testNoControllerName(): void
    {
        $builder = new LegacyControllerContextBuilder(
            $this->mockEmployeeContext(),
            $this->createMock(ContextStateManager::class),
            ['AdminCartsController'],
            $this->mockTabRepository(),
            $this->createMock(ContainerInterface::class),
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Cannot build Controller context as no controllerName has been defined/');
        $builder->build();
    }

    private function mockTabRepository(): TabRepository|MockObject
    {
        $repository = $this->createMock(TabRepository::class);
        $repository
            ->method('getIdByClassName')
            ->willReturn(10);

        return $repository;
    }

    private function mockEmployeeContext(): EmployeeContext|MockObject
    {
        $employee = $this->createMock(Employee::class);
        $employee
            ->method('getId')
            ->willReturn(20);

        $employeeContext = $this->createMock(EmployeeContext::class);
        $employeeContext
            ->method('getEmployee')
            ->willReturn($employee);

        return $employeeContext;
    }
}
