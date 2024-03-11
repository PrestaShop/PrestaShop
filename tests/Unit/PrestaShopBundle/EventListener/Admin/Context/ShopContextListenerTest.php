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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
            $this->mockLegacyContext(['shopContext' => '']),
            $this->mockMultistoreFeature(false)
        );
        $listener->onKernelRequest($event);

        $expectedShopConstraint = ShopConstraint::shop(self::DEFAULT_SHOP_ID);
        $this->assertEquals(self::DEFAULT_SHOP_ID, $this->getPrivateField($shopContextBuilder, 'shopId'));
        $this->assertEquals($expectedShopConstraint, $this->getPrivateField($shopContextBuilder, 'shopConstraint'));
        $this->assertEquals($expectedShopConstraint, $event->getRequest()->attributes->get('shopConstraint'));
    }

    /**
     * @dataProvider getMultiShopValues
     *
     * @param string $cookieValue
     * @param ?array $employeeData
     * @param ShopConstraint $expectedShopConstraint
     * @param int $expectedShopId
     */
    public function testMultiShop(string $cookieValue, ?array $employeeData, ShopConstraint $expectedShopConstraint, int $expectedShopId): void
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
            $this->mockLegacyContext(['shopContext' => $cookieValue]),
            $this->mockMultistoreFeature(true)
        );
        $listener->onKernelRequest($event);

        $this->assertEquals($expectedShopId, $this->getPrivateField($shopContextBuilder, 'shopId'));
        $this->assertEquals($expectedShopConstraint, $this->getPrivateField($shopContextBuilder, 'shopConstraint'));
        $this->assertEquals($expectedShopConstraint, $event->getRequest()->attributes->get('shopConstraint'));
    }

    public function getMultiShopValues(): iterable
    {
        yield 'single shop, employee has all permissions' => ['s-1', [], ShopConstraint::shop(1), 1];
        yield 'shop group, employee has all permissions' => ['g-2', [], ShopConstraint::shopGroup(2), self::DEFAULT_SHOP_ID];
        yield 'all shops, employee has all permissions' => ['', [], ShopConstraint::allShops(), self::DEFAULT_SHOP_ID];
        yield 'single shop, employee has permission' => ['s-3', ['authorizeShops' => [3]], ShopConstraint::shop(3), 3];
        yield 'single shop, employee has no permission for it so fallback on its default shop' => [
            's-3',
            ['authorizedShops' => [1]],
            ShopConstraint::shop(self::EMPLOYEE_DEFAULT_SHOP_ID),
            self::EMPLOYEE_DEFAULT_SHOP_ID,
        ];
        yield 'shop group, employee has no permission for it so fallback on its default shop' => [
            'g-3',
            ['authorizedShopGroups' => [1]],
            ShopConstraint::shop(self::EMPLOYEE_DEFAULT_SHOP_ID),
            self::EMPLOYEE_DEFAULT_SHOP_ID,
        ];
        yield 'single shop, no employee' => [
            's-3',
            null,
            ShopConstraint::shop(self::DEFAULT_SHOP_ID),
            self::DEFAULT_SHOP_ID,
        ];
        yield 'shop group, no employee' => [
            'g-3',
            null,
            ShopConstraint::allShops(),
            self::DEFAULT_SHOP_ID,
        ];
    }

    /**
     * @dataProvider getRedirectionValues
     *
     * @param string $switchParameterValue
     * @param string|null $originalCookieValue
     * @param bool $redirectionExpected
     * @param string|null $expectedCookieValue
     */
    public function testMultiShopRedirection(string $switchParameterValue, ?string $originalCookieValue, bool $redirectionExpected, ?string $expectedCookieValue): void
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

        $mockContext = $this->mockLegacyContext(['shopContext' => $originalCookieValue]);
        $listener = new ShopContextListener(
            $shopContextBuilder,
            $this->mockEmployeeContext(),
            $this->mockConfiguration(['PS_SHOP_DEFAULT' => self::DEFAULT_SHOP_ID, 'PS_SSL_ENABLED' => self::PS_SSL_ENABLED]),
            $mockContext,
            $this->mockMultistoreFeature(true)
        );

        // Check that initially the cookie has a null value
        $this->assertEquals($originalCookieValue, $mockContext->getContext()->cookie->shopContext);
        $listener->onKernelRequest($event);

        $this->assertEquals($redirectionExpected, $event->getResponse() instanceof RedirectResponse);
        $this->assertEquals($expectedCookieValue, $mockContext->getContext()->cookie->shopContext);
    }

    public function getRedirectionValues(): iterable
    {
        yield 'initially all shops, redirect to shop' => [
            // Passed parameter
            's-1',
            // Initial cookie value
            '',
            // Redirection expected
            true,
            // Cookie value after handler executed
            's-1',
        ];

        yield 'initially single shop, redirect to other shop' => [
            's-1',
            's-2',
            true,
            's-1',
        ];

        yield 'initially group shop, redirect to shop' => [
            's-1',
            'g-1',
            true,
            's-1',
        ];

        yield 'initially single, redirect to shop group' => [
            'g-1',
            's-1',
            true,
            'g-1',
        ];

        yield 'initially group shop, redirect to other shop group' => [
            'g-1',
            'g-3',
            true,
            'g-1',
        ];

        yield 'initially single shop, redirect to all shops' => [
            '',
            's-1',
            true,
            '',
        ];

        yield 'initially group shop, redirect to all shops' => [
            '',
            'g-1',
            true,
            '',
        ];

        yield 'initially all shops, redirect to shop group' => [
            'g-1',
            '',
            true,
            'g-1',
        ];

        yield 'stay on same shop no redirection' => [
            's-1',
            's-1',
            false,
            's-1',
        ];

        yield 'stay on same shop group no redirection' => [
            'g-1',
            'g-1',
            false,
            'g-1',
        ];

        yield 'stay on all shops no redirection' => [
            '',
            '',
            false,
            '',
        ];
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
