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

namespace Tests\Unit\PrestaShopBundle\EventListener\Admin\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\EventListener\Admin\Context\ShopContextListener;
use PrestaShopBundle\Security\Admin\TokenAttributes;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Tests\Unit\PrestaShopBundle\EventListener\ContextEventListenerTestCase;

class ShopContextListenerTest extends ContextEventListenerTestCase
{
    private const PS_SSL_ENABLED = 1;
    private const DEFAULT_SHOP_ID = 42;
    private const EMPLOYEE_DEFAULT_SHOP_ID = 51;

    public function testSingleShop(): void
    {
        $event = $this->createRequestEvent(new Request());

        $shopContextBuilder = new ShopContextBuilder(
            $this->mockShopRepository(self::DEFAULT_SHOP_ID),
            $this->mockContextStateManager(),
        );

        $listener = new ShopContextListener(
            $shopContextBuilder,
            $this->mockEmployeeContext(),
            $this->mockConfiguration(['PS_SHOP_DEFAULT' => self::DEFAULT_SHOP_ID, 'PS_SSL_ENABLED' => self::PS_SSL_ENABLED]),
            $this->mockMultistoreFeature(false),
            $this->mockRouter(),
            $this->mockSecurity(),
        );
        $listener->initShopContext($event);

        $expectedShopConstraint = ShopConstraint::shop(self::DEFAULT_SHOP_ID);
        $this->assertEquals(self::DEFAULT_SHOP_ID, $this->getPrivateField($shopContextBuilder, 'shopId'));
        $this->assertEquals($expectedShopConstraint, $this->getPrivateField($shopContextBuilder, 'shopConstraint'));
        $this->assertEquals($expectedShopConstraint, $event->getRequest()->attributes->get('shopConstraint'));
    }

    /**
     * @dataProvider getMultiShopValues
     *
     * @param ?ShopConstraint $tokenShopConstraint
     * @param ?array $employeeData
     * @param ShopConstraint $expectedShopConstraint
     * @param int $expectedShopId
     */
    public function testMultiShop(?ShopConstraint $tokenShopConstraint, ?array $employeeData, ShopConstraint $expectedShopConstraint, int $expectedShopId): void
    {
        $event = $this->createRequestEvent(new Request());

        $shopContextBuilder = new ShopContextBuilder(
            $this->mockShopRepository($expectedShopId),
            $this->mockContextStateManager(),
        );

        $listener = new ShopContextListener(
            $shopContextBuilder,
            $this->mockEmployeeContext($employeeData),
            $this->mockConfiguration(['PS_SHOP_DEFAULT' => self::DEFAULT_SHOP_ID, 'PS_SSL_ENABLED' => self::PS_SSL_ENABLED]),
            $this->mockMultistoreFeature(true),
            $this->mockRouter(),
            $this->mockSecurity($expectedShopConstraint),
        );
        $listener->initShopContext($event);

        $this->assertEquals($expectedShopId, $this->getPrivateField($shopContextBuilder, 'shopId'));
        $this->assertEquals($expectedShopConstraint, $this->getPrivateField($shopContextBuilder, 'shopConstraint'));
        $this->assertEquals($expectedShopConstraint, $event->getRequest()->attributes->get('shopConstraint'));
    }

    public function getMultiShopValues(): iterable
    {
        yield 'single shop, employee has all permissions' => [
            ShopConstraint::shop(1),
            [],
            ShopConstraint::shop(1),
            1,
        ];
        yield 'shop group, employee has all permissions' => [
            ShopConstraint::shopGroup(2),
            [],
            ShopConstraint::shopGroup(2),
            self::DEFAULT_SHOP_ID,
        ];
        yield 'all shops, employee has all permissions' => [
            ShopConstraint::allShops(),
            [],
            ShopConstraint::allShops(),
            self::DEFAULT_SHOP_ID,
        ];
        yield 'no token attribute means all shops, employee has all permissions' => [
            null,
            [],
            ShopConstraint::allShops(),
            self::DEFAULT_SHOP_ID,
        ];
        yield 'single shop, employee has permission' => [
            ShopConstraint::shop(3),
            ['authorizeShops' => [3]],
            ShopConstraint::shop(3),
            3,
        ];
        yield 'single shop, employee has no permission for it so fallback on its default shop' => [
            ShopConstraint::shop(3),
            ['authorizedShops' => [1]],
            ShopConstraint::shop(self::EMPLOYEE_DEFAULT_SHOP_ID),
            self::EMPLOYEE_DEFAULT_SHOP_ID,
        ];
        yield 'shop group, employee has no permission for it so fallback on its default shop' => [
            ShopConstraint::shopGroup(3),
            ['authorizedShopGroups' => [1]],
            ShopConstraint::shop(self::EMPLOYEE_DEFAULT_SHOP_ID),
            self::EMPLOYEE_DEFAULT_SHOP_ID,
        ];
        yield 'single shop, no employee' => [
            ShopConstraint::shop(3),
            null,
            ShopConstraint::shop(self::DEFAULT_SHOP_ID),
            self::DEFAULT_SHOP_ID,
        ];
        yield 'shop group, no employee' => [
            ShopConstraint::shopGroup(3),
            null,
            ShopConstraint::allShops(),
            self::DEFAULT_SHOP_ID,
        ];
    }

    /**
     * @dataProvider getRedirectionValues
     *
     * @param string $switchParameterValue
     * @param ShopConstraint|null $originalTokenShopConstraint
     * @param bool $redirectionExpected
     * @param ShopConstraint|null $expectedTokenShopConstraint
     */
    public function testMultiShopRedirection(string $switchParameterValue, ?ShopConstraint $originalTokenShopConstraint, bool $redirectionExpected, ?ShopConstraint $expectedTokenShopConstraint): void
    {
        $request = new Request(
            ['setShopContext' => $switchParameterValue],
            [], [], [], [],
            [
                'HTTP_HOST' => 'localhost',
                'BASE' => '/admin-dev',
                'PHP_SELF' => '/admin-dev/index.php',
                'SCRIPT_NAME' => '/admin-dev/index.php',
            ]
        );
        $event = $this->createRequestEvent($request);

        $shopContextBuilder = new ShopContextBuilder(
            $this->mockShopRepository(self::DEFAULT_SHOP_ID),
            $this->mockContextStateManager(),
        );

        $security = $this->mockSecurity($originalTokenShopConstraint);
        $listener = new ShopContextListener(
            $shopContextBuilder,
            $this->mockEmployeeContext(),
            $this->mockConfiguration(['PS_SHOP_DEFAULT' => self::DEFAULT_SHOP_ID, 'PS_SSL_ENABLED' => self::PS_SSL_ENABLED]),
            $this->mockMultistoreFeature(true),
            $this->mockRouter(),
            $security,
        );

        // Check the initial state of the token attribute
        if (null !== $originalTokenShopConstraint) {
            $this->assertEquals($originalTokenShopConstraint, $security->getToken()->getAttribute(TokenAttributes::SHOP_CONSTRAINT));
        } else {
            $this->assertFalse($security->getToken()->hasAttribute(TokenAttributes::SHOP_CONSTRAINT));
        }
        $listener->initShopContext($event);

        $this->assertEquals($redirectionExpected, $event->getResponse() instanceof RedirectResponse);

        // Check the updated state of the token attribute
        if (null !== $expectedTokenShopConstraint) {
            $this->assertEquals($expectedTokenShopConstraint, $security->getToken()->getAttribute(TokenAttributes::SHOP_CONSTRAINT));
        } else {
            $this->assertFalse($security->getToken()->hasAttribute(TokenAttributes::SHOP_CONSTRAINT));
        }
    }

    public function getRedirectionValues(): iterable
    {
        yield 'initially all shops, redirect to shop' => [
            // Passed parameter
            's-1',
            // Initial token shop constraint
            ShopConstraint::allShops(),
            // Redirection expected
            true,
            // Expected token attribute after handled executed
            ShopConstraint::shop(1),
        ];

        yield 'initially null, redirect to shop' => [
            // Passed parameter
            's-1',
            // Initial token shop constraint
            null,
            // Redirection expected
            true,
            // Expected token attribute after handled executed
            ShopConstraint::shop(1),
        ];

        yield 'initially single shop, redirect to other shop' => [
            's-1',
            ShopConstraint::shop(2),
            true,
            ShopConstraint::shop(1),
        ];

        yield 'initially group shop, redirect to shop' => [
            's-1',
            ShopConstraint::shopGroup(1),
            true,
            ShopConstraint::shop(1),
        ];

        yield 'initially single, redirect to shop group' => [
            'g-1',
            ShopConstraint::shop(1),
            true,
            ShopConstraint::shopGroup(1),
        ];

        yield 'initially group shop, redirect to other shop group' => [
            'g-1',
            ShopConstraint::shopGroup(3),
            true,
            ShopConstraint::shopGroup(1),
        ];

        yield 'initially single shop, redirect to all shops' => [
            '',
            ShopConstraint::shop(1),
            true,
            ShopConstraint::allShops(),
        ];

        yield 'initially group shop, redirect to all shops' => [
            '',
            ShopConstraint::shopGroup(1),
            true,
            ShopConstraint::allShops(),
        ];

        yield 'initially all shops, redirect to shop group' => [
            'g-1',
            ShopConstraint::allShops(),
            true,
            ShopConstraint::shopGroup(1),
        ];

        yield 'stay on same shop no redirection' => [
            's-1',
            ShopConstraint::shop(1),
            false,
            ShopConstraint::shop(1),
        ];

        yield 'stay on same shop group no redirection' => [
            'g-1',
            ShopConstraint::shopGroup(1),
            false,
            ShopConstraint::shopGroup(1),
        ];

        yield 'stay on all shops no redirection' => [
            '',
            ShopConstraint::allShops(),
            false,
            ShopConstraint::allShops(),
        ];
    }

    private function mockRouter(): RouterInterface|MockObject
    {
        $router = $this->createMock(RouterInterface::class);
        $router
            ->method('match')
            ->willThrowException(new NoConfigurationException())
        ;

        return $router;
    }

    private function mockMultistoreFeature(bool $multiShopEnabled): MultistoreFeature|MockObject
    {
        $multistore = $this->createMock(MultistoreFeature::class);
        $multistore
            ->method('isUsed')
            ->willReturn($multiShopEnabled)
        ;

        return $multistore;
    }

    private function mockSecurity(?ShopConstraint $shopConstraint = null): Security
    {
        $securityMock = $this->createMock(Security::class);
        $userMock = $this->createMock(UserInterface::class);
        $token = new UsernamePasswordToken($userMock, 'main', []);
        if (null !== $shopConstraint) {
            $token->setAttribute(TokenAttributes::SHOP_CONSTRAINT, $shopConstraint);
        }
        $securityMock->method('getToken')->willReturn($token);

        return $securityMock;
    }

    private function mockEmployeeContext(?array $employeeData = []): EmployeeContext|MockObject
    {
        $employeeContext = $this->createMock(EmployeeContext::class);

        if (null === $employeeData) {
            $employeeContext
                ->method('hasAuthorizationOnShopGroup')
                ->willReturn(false)
            ;
            $employeeContext
                ->method('hasAuthorizationOnShop')
                ->willReturn(false)
            ;
            $employeeContext
                ->method('getDefaultShopId')
                ->willReturn(0)
            ;
        } else {
            if (!empty($employeeData['authorizedShopGroups'])) {
                $employeeContext
                    ->method('hasAuthorizationOnShopGroup')
                    ->will($this->returnCallback(function ($shopGroupId) use ($employeeData) {
                        return in_array($shopGroupId, $employeeData['authorizedShopGroups']);
                    }))
                ;
            } else {
                $employeeContext
                    ->method('hasAuthorizationOnShopGroup')
                    ->willReturn(true)
                ;
            }

            if (!empty($employeeData['authorizedShops'])) {
                $employeeContext
                    ->method('hasAuthorizationOnShop')
                    ->will($this->returnCallback(function ($shopId) use ($employeeData) {
                        return in_array($shopId, $employeeData['authorizedShops']);
                    }))
                ;
            } else {
                $employeeContext
                    ->method('hasAuthorizationOnShop')
                    ->willReturn(true)
                ;
            }

            $employeeContext
                ->method('getDefaultShopId')
                ->willReturn(self::EMPLOYEE_DEFAULT_SHOP_ID)
            ;
        }

        return $employeeContext;
    }
}
