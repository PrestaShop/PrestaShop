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

namespace Tests\Unit\Core\Context;

use Doctrine\ORM\NoResultException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\Employee;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\LegacyControllerContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Entity\Repository\TabRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Tests\Unit\Core\Configuration\MockConfigurationTrait;
use Twig\Environment;

class LegacyControllerContextBuilderTest extends TestCase
{
    use MockConfigurationTrait;

    /**
     * @dataProvider getControllerValues
     *
     * @param string $controllerName
     * @param string $expectedControllerName
     * @param ?string $className
     * @param int $multishopContext
     * @param string $expectedCurrentIndex
     * @param ?string $redirectionUrl
     */
    public function testBuild(string $controllerName, string $expectedControllerName, ?string $className, int $multishopContext, string $expectedCurrentIndex, string $expectedTable, ?string $redirectionUrl = null): void
    {
        $builder = new LegacyControllerContextBuilder(
            $this->mockEmployeeContext(),
            ['AdminCarts'],
            $this->mockTabRepository(),
            $this->createMock(ContainerInterface::class),
            $this->mockConfiguration(),
            $this->mockRequestStack(true),
            $this->createMock(ShopContext::class),
            $this->createMock(LanguageContext::class),
            'admin-dev',
            '9.0.0',
            $this->createMock(Environment::class),
        );

        $builder->setControllerName($controllerName);
        if (null !== $redirectionUrl) {
            $builder->setRedirectionUrl($redirectionUrl);
        }
        $legacyController = $builder->build();

        $this->assertEquals($className, $legacyController->className);
        $this->assertEquals('admin', $legacyController->controller_type);
        $this->assertEquals($expectedControllerName, $legacyController->php_self);
        $this->assertEquals($expectedControllerName, $legacyController->controller_name);
        $this->assertEquals(10, $legacyController->id);
        $this->assertEquals($multishopContext, $legacyController->multishop_context);
        $this->assertEquals($expectedCurrentIndex, $legacyController->currentIndex);
        $this->assertEquals($expectedTable, $legacyController->table);
        $this->assertTrue($legacyController->ajax);
    }

    public function getControllerValues(): iterable
    {
        yield 'AdminCarts default generic behaviour for all controllers' => [
            'AdminCarts',
            'AdminCarts',
            'Cart',
            ShopConstraint::ALL_SHOPS,
            'index.php?controller=AdminCarts',
            'cart',
        ];

        yield 'AdminCarts default behaviour with redirection url' => [
            'AdminCarts',
            'AdminCarts',
            'Cart',
            ShopConstraint::ALL_SHOPS,
            'index.php?controller=AdminCarts&back=index.php%3Fcontroller%3DAdminOrder',
            'cart',
            'index.php?controller=AdminOrder',
        ];

        yield 'AdminAccess special classname' => [
            'AdminAccess',
            'AdminAccess',
            'Profile',
            ShopConstraint::ALL_SHOPS | ShopConstraint::SHOP_GROUP | ShopConstraint::SHOP,
            'index.php?controller=AdminAccess',
            'access',
        ];

        yield 'AdminCarrierWizard special classname' => [
            'AdminCarrierWizard',
            'AdminCarrierWizard',
            'Carrier',
            ShopConstraint::ALL_SHOPS | ShopConstraint::SHOP_GROUP | ShopConstraint::SHOP,
            'index.php?controller=AdminCarrierWizard',
            'carrier',
        ];

        yield 'AdminImages special classname' => [
            'AdminImages',
            'AdminImages',
            'ImageType',
            ShopConstraint::ALL_SHOPS | ShopConstraint::SHOP_GROUP | ShopConstraint::SHOP,
            'index.php?controller=AdminImages',
            'image_type',
        ];

        yield 'AdminReturn special classname' => [
            'AdminReturn',
            'AdminReturn',
            'OrderReturn',
            ShopConstraint::ALL_SHOPS | ShopConstraint::SHOP_GROUP | ShopConstraint::SHOP,
            'index.php?controller=AdminReturn',
            'order_return',
        ];

        yield 'AdminSearchConf special classname' => [
            'AdminSearchConfController',
            'AdminSearchConf',
            'Alias',
            ShopConstraint::ALL_SHOPS | ShopConstraint::SHOP_GROUP | ShopConstraint::SHOP,
            'index.php?controller=AdminSearchConf',
            'alias',
        ];

        yield 'AdminConfigureFaviconBo special classname' => [
            'AdminConfigureFaviconBo',
            'AdminConfigureFaviconBo',
            'Configuration',
            ShopConstraint::ALL_SHOPS | ShopConstraint::SHOP_GROUP | ShopConstraint::SHOP,
            'index.php?controller=AdminConfigureFaviconBo',
            'configuration',
        ];

        yield 'AdminController default fallback for symfony routes without associated legacy controller' => [
            'AdminController',
            'Admin',
            null,
            ShopConstraint::ALL_SHOPS | ShopConstraint::SHOP_GROUP | ShopConstraint::SHOP,
            'index.php?controller=Admin',
            'configuration',
        ];

        yield 'AdminCartsController with Controller prefix' => [
            'AdminCartsController',
            'AdminCarts',
            'Cart',
            ShopConstraint::ALL_SHOPS,
            'index.php?controller=AdminCarts',
            'cart',
        ];

        yield 'AdminCartsControllerOverride with ControllerOverride prefix' => [
            'AdminCartsControllerOverride',
            'AdminCarts',
            'Cart',
            ShopConstraint::ALL_SHOPS,
            'index.php?controller=AdminCarts',
            'cart',
        ];
    }

    public function testNoControllerName(): void
    {
        $tabRepository = $this->createMock(TabRepository::class);
        $tabRepository
            ->method('getIdByClassName')
            ->willThrowException(new NoResultException())
        ;
        $builder = new LegacyControllerContextBuilder(
            $this->mockEmployeeContext(),
            ['AdminCarts'],
            $tabRepository,
            $this->createMock(ContainerInterface::class),
            $this->mockConfiguration(),
            $this->mockRequestStack(false),
            $this->createMock(ShopContext::class),
            $this->createMock(LanguageContext::class),
            'admin-dev',
            '9.0.0',
            $this->createMock(Environment::class),
        );

        // We don't call setControllerName so the builder falls back on AdminNotFound
        $legacyController = $builder->build();

        $this->assertEquals('NotFound', $legacyController->className);
        $this->assertEquals('admin', $legacyController->controller_type);
        $this->assertEquals('AdminNotFound', $legacyController->php_self);
        $this->assertEquals('AdminNotFound', $legacyController->controller_name);
        $this->assertEquals(-1, $legacyController->id);
        $this->assertEquals(ShopConstraint::ALL_SHOPS | ShopConstraint::SHOP_GROUP | ShopConstraint::SHOP, $legacyController->multishop_context);
        $this->assertEquals('index.php', $legacyController->currentIndex);
        $this->assertEquals('configuration', $legacyController->table);
        $this->assertFalse($legacyController->ajax);
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

    private function mockRequestStack(bool $ajax): RequestStack|MockObject
    {
        $request = $this->createMock(Request::class);
        $request->method('get')->willReturn($ajax);

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getCurrentRequest')->willReturn($request);

        return $requestStack;
    }
}
