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
use PrestaShopBundle\EventListener\Admin\Context\ShopContextSubscriber;
use PrestaShopBundle\Routing\LegacyControllerConstants;
use PrestaShopBundle\Security\Admin\TokenAttributes;
use Shop;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tests\Unit\PrestaShopBundle\EventListener\ContextEventListenerTestCase;

class ShopContextSubscriberTest extends ContextEventListenerTestCase
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
            $this->mockMultistoreFeature(false),
        );

        $listener = new ShopContextSubscriber(
            $shopContextBuilder,
            $this->mockEmployeeContext(),
            $this->mockConfiguration(['PS_SHOP_DEFAULT' => self::DEFAULT_SHOP_ID, 'PS_SSL_ENABLED' => self::PS_SSL_ENABLED]),
            $this->mockMultistoreFeature(false),
            $this->mockRouter(),
            $this->mockSecurity(),
            $this->mockLegacyContext(),
            $this->createMock(TranslatorInterface::class),
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
            $this->mockMultistoreFeature(true),
        );

        $listener = new ShopContextSubscriber(
            $shopContextBuilder,
            $this->mockEmployeeContext($employeeData),
            $this->mockConfiguration(['PS_SHOP_DEFAULT' => self::DEFAULT_SHOP_ID, 'PS_SSL_ENABLED' => self::PS_SSL_ENABLED]),
            $this->mockMultistoreFeature(true),
            $this->mockRouter(),
            $this->mockSecurity($expectedShopConstraint),
            $this->mockLegacyContext(),
            $this->createMock(TranslatorInterface::class),
        );
        $listener->initShopContext($event);

        $this->assertEquals($expectedShopId, $this->getPrivateField($shopContextBuilder, 'shopId'));
        $this->assertEquals($expectedShopConstraint, $this->getPrivateField($shopContextBuilder, 'shopConstraint'));
        $this->assertEquals($expectedShopConstraint, $event->getRequest()->attributes->get('shopConstraint'));
    }

    public function testLegacyControllerForceMultiShop(): void
    {
        $request = new Request();
        $request->attributes->set(LegacyControllerConstants::MULTISHOP_CONTEXT_ATTRIBUTE, Shop::CONTEXT_ALL);
        $event = $this->createRequestEvent($request);

        $shopContextBuilder = new ShopContextBuilder(
            $this->mockShopRepository(self::DEFAULT_SHOP_ID),
            $this->mockContextStateManager(),
            $this->mockMultistoreFeature(false),
        );

        $listener = new ShopContextSubscriber(
            $shopContextBuilder,
            $this->mockEmployeeContext(),
            $this->mockConfiguration(['PS_SHOP_DEFAULT' => self::DEFAULT_SHOP_ID, 'PS_SSL_ENABLED' => self::PS_SSL_ENABLED]),
            $this->mockMultistoreFeature(false),
            $this->mockRouter(),
            $this->mockSecurity(),
            $this->mockLegacyContext(),
            $this->createMock(TranslatorInterface::class),
        );
        $listener->initShopContext($event);

        $expectedShopConstraint = ShopConstraint::allShops();
        $this->assertEquals(self::DEFAULT_SHOP_ID, $this->getPrivateField($shopContextBuilder, 'shopId'));
        $this->assertEquals($expectedShopConstraint, $this->getPrivateField($shopContextBuilder, 'shopConstraint'));
        $this->assertEquals($expectedShopConstraint, $event->getRequest()->attributes->get('shopConstraint'));
    }

    public static function getMultiShopValues(): iterable
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
            ['authorizedShops' => [3]],
            ShopConstraint::shop(3),
            3,
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
     * @param string|null $switchParameterValue
     * @param ShopConstraint|null $originalTokenShopConstraint
     * @param bool $redirectionExpected
     * @param string $expectedCookieValue
     * @param ShopConstraint $expectedTokenShopConstraint
     * @param array $employeeData
     */
    public function testMultiShopRedirection(?string $switchParameterValue, ?ShopConstraint $originalTokenShopConstraint, bool $redirectionExpected, string $expectedCookieValue, ShopConstraint $expectedTokenShopConstraint, array $employeeData = []): void
    {
        $requestParameters = null !== $switchParameterValue ? ['setShopContext' => $switchParameterValue] : [];
        $request = new Request(
            $requestParameters,
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
            $this->mockMultistoreFeature(true),
        );

        $security = $this->mockSecurity($originalTokenShopConstraint);
        $legacyContext = $this->mockLegacyContext();
        $listener = new ShopContextSubscriber(
            $shopContextBuilder,
            $this->mockEmployeeContext($employeeData),
            $this->mockConfiguration(['PS_SHOP_DEFAULT' => self::DEFAULT_SHOP_ID, 'PS_SSL_ENABLED' => self::PS_SSL_ENABLED]),
            $this->mockMultistoreFeature(true),
            $this->mockRouter(),
            $security,
            $legacyContext,
            $this->createMock(TranslatorInterface::class),
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
        $this->assertEquals($expectedTokenShopConstraint, $security->getToken()->getAttribute(TokenAttributes::SHOP_CONSTRAINT));

        // This value is only updated when a valid redirection occurs, otherwise it remains empty
        $this->assertEquals($expectedCookieValue, $legacyContext->getContext()->cookie->shopContext);
    }

    public static function getRedirectionValues(): iterable
    {
        // Valid redirections since everything is allowed
        yield 'initially all shops, redirect to shop' => [
            // Passed parameter
            's-1',
            // Initial token shop constraint
            ShopConstraint::allShops(),
            // Redirection expected
            true,
            // Expected cookie value
            's-1',
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
            // Expected cookie value
            's-1',
            // Expected token attribute after handled executed
            ShopConstraint::shop(1),
        ];

        yield 'initially single shop, redirect to other shop' => [
            's-1',
            ShopConstraint::shop(2),
            true,
            's-1',
            ShopConstraint::shop(1),
        ];

        yield 'initially group shop, redirect to shop' => [
            's-1',
            ShopConstraint::shopGroup(1),
            true,
            's-1',
            ShopConstraint::shop(1),
        ];

        yield 'initially single, redirect to shop group' => [
            'g-1',
            ShopConstraint::shop(1),
            true,
            'g-1',
            ShopConstraint::shopGroup(1),
        ];

        yield 'initially group shop, redirect to other shop group' => [
            'g-1',
            ShopConstraint::shopGroup(3),
            true,
            'g-1',
            ShopConstraint::shopGroup(1),
        ];

        yield 'initially single shop, redirect to all shops' => [
            '',
            ShopConstraint::shop(1),
            true,
            '',
            ShopConstraint::allShops(),
        ];

        yield 'initially group shop, redirect to all shops' => [
            '',
            ShopConstraint::shopGroup(1),
            true,
            '',
            ShopConstraint::allShops(),
        ];

        yield 'initially all shops, redirect to shop group' => [
            'g-1',
            ShopConstraint::allShops(),
            true,
            'g-1',
            ShopConstraint::shopGroup(1),
        ];

        // No redirection since we are on the same constraint (and everything is allowed)
        yield 'stay on same shop no redirection' => [
            's-1',
            ShopConstraint::shop(1),
            false,
            '',
            ShopConstraint::shop(1),
        ];

        yield 'stay on same shop group no redirection' => [
            'g-1',
            ShopConstraint::shopGroup(1),
            false,
            '',
            ShopConstraint::shopGroup(1),
        ];

        yield 'stay on all shops no redirection' => [
            '',
            ShopConstraint::allShops(),
            false,
            '',
            ShopConstraint::allShops(),
        ];

        // Now test with explicit data, switch to allowed contexts
        yield 'initially single shop, redirect to other shop allowed' => [
            's-1',
            ShopConstraint::shop(2),
            true,
            's-1',
            ShopConstraint::shop(1),
            [
                'authorizedShops' => [1, 2],
                'defaultShopId' => 2,
            ],
        ];

        yield 'initially single shop, redirect to shop group allowed' => [
            'g-1',
            ShopConstraint::shop(2),
            true,
            'g-1',
            ShopConstraint::shopGroup(1),
            [
                'authorizedShops' => [2],
                'defaultShopId' => 2,
                'authorizedShopGroups' => [1, 2],
            ],
        ];

        yield 'initially single shop, redirect to all shops allowed' => [
            '',
            ShopConstraint::shop(2),
            true,
            '',
            ShopConstraint::allShops(),
            [
                'authorizedShops' => [2],
                'defaultShopId' => 2,
                'authorizedForAllShops' => true,
            ],
        ];

        // Now test with explicit data, the requested context is not allowed so we are forced back to the original context
        yield 'initially single shop, redirect to other shop not allowed' => [
            's-1',
            ShopConstraint::shop(2),
            true,
            's-2',
            ShopConstraint::shop(2),
            [
                'authorizedShops' => [2],
                'defaultShopId' => 2,
            ],
        ];

        yield 'initially single shop, redirect to shop group not allowed' => [
            'g-1',
            ShopConstraint::shop(2),
            true,
            's-2',
            ShopConstraint::shop(2),
            [
                'authorizedShops' => [2],
                'defaultShopId' => 2,
                'authorizedShopGroups' => [2],
            ],
        ];

        yield 'initially single shop, redirect to all shops not allowed' => [
            '',
            ShopConstraint::shop(2),
            true,
            's-2',
            ShopConstraint::shop(2),
            [
                'authorizedShops' => [2],
                'defaultShopId' => 2,
                'authorizedForAllShops' => false,
            ],
        ];

        // Now we don't change the context, but the original one is invalid so we are redirected to the default shop
        yield 'initially single shop not allowed, redirect to default shop' => [
            null,
            ShopConstraint::shop(1),
            true,
            's-2',
            ShopConstraint::shop(2),
            [
                'authorizedShops' => [2],
                'defaultShopId' => 2,
            ],
        ];

        yield 'initially shop group not allowed, redirect to default shop' => [
            null,
            ShopConstraint::shopGroup(1),
            true,
            's-2',
            ShopConstraint::shop(2),
            [
                'authorizedShops' => [2],
                'defaultShopId' => 2,
                'authorizedShopGroups' => [2],
            ],
        ];

        yield 'initially all shops not allowed, redirect to default shop' => [
            null,
            ShopConstraint::allShops(),
            true,
            's-1',
            ShopConstraint::shop(1),
            [
                'authorizedShops' => [1],
                'defaultShopId' => 1,
                'authorizedForAllShops' => false,
            ],
        ];
    }

    /**
     * @dataProvider provideLoginShopContext
     */
    public function testInitShopContextOnLogin(array $employeeData, ?ShopConstraint $expectedShopConstraint): void
    {
        $token = $this->createMock(TokenInterface::class);

        // The test occurs here, the mock validates that the subscriber correctly sets the appropriate attribute on the token
        if ($expectedShopConstraint) {
            $token->expects($this->atLeastOnce())->method('setAttribute')->with(TokenAttributes::SHOP_CONSTRAINT, $expectedShopConstraint);
        } else {
            $token->expects($this->never())->method('setAttribute');
        }

        $shopContextBuilder = new ShopContextBuilder(
            $this->mockShopRepository(self::DEFAULT_SHOP_ID),
            $this->mockContextStateManager(),
            $this->mockMultistoreFeature(false),
        );

        $listener = new ShopContextSubscriber(
            $shopContextBuilder,
            $this->mockEmployeeContext($employeeData),
            $this->mockConfiguration(),
            $this->mockMultistoreFeature(true),
            $this->mockRouter(),
            $this->mockSecurity(),
            $this->mockLegacyContext(),
            $this->createMock(TranslatorInterface::class),
        );

        $event = new AuthenticationSuccessEvent($token);
        $listener->initShopContextOnLogin($event);
    }

    public static function provideLoginShopContext(): iterable
    {
        yield 'employee has authorization for all shops' => [
            [
                'authorizedForAllShops' => true,
            ],
            ShopConstraint::allShops(),
        ];

        yield 'employee not authorize for all shops, but has default shop ID' => [
            [
                'authorizedForAllShops' => false,
                'defaultShopId' => 2,
            ],
            ShopConstraint::shop(2),
        ];

        yield 'employee not authorize for all shops, and no default shop ID' => [
            [
                'authorizedForAllShops' => false,
                'defaultShopId' => 0,
            ],
            null,
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

        // By default, authorized for everything
        if (empty($employeeData)) {
            $employeeContext
                ->method('hasAuthorizationOnShopGroup')
                ->willReturn(true)
            ;
            $employeeContext
                ->method('hasAuthorizationOnShop')
                ->willReturn(true)
            ;
            $employeeContext
                ->method('hasAuthorizationForAllShops')
                ->willReturn(true)
            ;
            $employeeContext
                ->method('getDefaultShopId')
                ->willReturn(self::EMPLOYEE_DEFAULT_SHOP_ID)
            ;
        } else {
            if (isset($employeeData['authorizedShopGroups'])) {
                $employeeContext
                    ->method('hasAuthorizationOnShopGroup')
                    ->will($this->returnCallback(function ($shopGroupId) use ($employeeData) {
                        return in_array($shopGroupId, $employeeData['authorizedShopGroups']);
                    }))
                ;
            } else {
                $employeeContext
                    ->method('hasAuthorizationOnShopGroup')
                    ->willReturn(false)
                ;
            }

            if (isset($employeeData['authorizedShops'])) {
                $employeeContext
                    ->method('hasAuthorizationOnShop')
                    ->will($this->returnCallback(function ($shopId) use ($employeeData) {
                        return in_array($shopId, $employeeData['authorizedShops']);
                    }))
                ;
            } else {
                $employeeContext
                    ->method('hasAuthorizationOnShop')
                    ->willReturn(false)
                ;
            }

            if (isset($employeeData['authorizedForAllShops'])) {
                $employeeContext
                    ->method('hasAuthorizationForAllShops')
                    ->willReturn($employeeData['authorizedForAllShops'])
                ;
            } else {
                $employeeContext
                    ->method('hasAuthorizationForAllShops')
                    ->willReturn(false)
                ;
            }

            if (isset($employeeData['defaultShopId'])) {
                $employeeContext
                    ->method('getDefaultShopId')
                    ->willReturn($employeeData['defaultShopId'])
                ;
            } else {
                $employeeContext
                    ->method('getDefaultShopId')
                    ->willReturn(self::EMPLOYEE_DEFAULT_SHOP_ID)
                ;
            }
        }

        return $employeeContext;
    }
}
