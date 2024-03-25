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

namespace Tests\Unit\PrestaShopBundle\EventListener\API\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Controller\Api\OAuth2\AccessTokenController;
use PrestaShopBundle\EventListener\API\Context\ShopContextListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\PrestaShopBundle\EventListener\ContextEventListenerTestCase;

class ShopContextListenerTest extends ContextEventListenerTestCase
{
    private const DEFAULT_SHOP_ID = 42;
    private const QUERY_SHOP_ID = 51;
    private const QUERY_SHOP_GROUP_ID = 69;

    public function testShopContextWhenMultishopDisabled(): void
    {
        // Create request that mimic a call to external API
        $event = $this->createRequestEvent(new Request());

        $shopContextBuilder = new ShopContextBuilder(
            $this->mockShopRepository(self::DEFAULT_SHOP_ID),
            $this->mockContextStateManager(),
        );
        $listener = new ShopContextListener(
            $shopContextBuilder,
            $this->mockMultistoreFeature(false),
            $this->mockConfiguration(['PS_SHOP_DEFAULT' => self::DEFAULT_SHOP_ID])
        );
        $listener->onKernelRequest($event);
        $expectedShopConstraint = ShopConstraint::shop(self::DEFAULT_SHOP_ID);
        $this->assertEquals(self::DEFAULT_SHOP_ID, $this->getPrivateField($shopContextBuilder, 'shopId'));
        $this->assertEquals($expectedShopConstraint, $this->getPrivateField($shopContextBuilder, 'shopConstraint'));
        $this->assertEquals($expectedShopConstraint, $event->getRequest()->attributes->get('shopConstraint'));
    }

    /**
     * @dataProvider getMultishopRequests
     *
     * @param Request $request
     * @param ShopConstraint $expectedShopConstraint
     * @param int $expectedShopId
     */
    public function testListenRequestParametersWhenMultishopIsEnabled(Request $request, ShopConstraint $expectedShopConstraint, int $expectedShopId): void
    {
        // Create request that mimic a call to external API
        $event = $this->createRequestEvent($request);

        $shopContextBuilder = new ShopContextBuilder(
            $this->mockShopRepository(self::DEFAULT_SHOP_ID),
            $this->mockContextStateManager(),
        );
        $listener = new ShopContextListener(
            $shopContextBuilder,
            $this->mockMultistoreFeature(true),
            $this->mockConfiguration(['PS_SHOP_DEFAULT' => self::DEFAULT_SHOP_ID])
        );
        $listener->onKernelRequest($event);

        $this->assertEquals($expectedShopId, $this->getPrivateField($shopContextBuilder, 'shopId'));
        $this->assertEquals($expectedShopConstraint, $this->getPrivateField($shopContextBuilder, 'shopConstraint'));
        $this->assertEquals($expectedShopConstraint, $event->getRequest()->attributes->get('shopConstraint'));
    }

    public function getMultishopRequests(): iterable
    {
        yield 'access token endpoint uses default shop as fallback, even if no shop context parameter is specified' => [
            new Request([], [], ['_controller' => AccessTokenController::class]),
            ShopConstraint::shop(self::DEFAULT_SHOP_ID),
            self::DEFAULT_SHOP_ID,
        ];

        yield 'single shop query parameter' => [
            new Request(['shopId' => self::QUERY_SHOP_ID]),
            ShopConstraint::shop(self::QUERY_SHOP_ID),
            self::QUERY_SHOP_ID,
        ];

        yield 'single shop request parameter' => [
            new Request([], ['shopId' => self::QUERY_SHOP_ID]),
            ShopConstraint::shop(self::QUERY_SHOP_ID),
            self::QUERY_SHOP_ID,
        ];

        yield 'single shop attribute parameter' => [
            new Request([], [], ['shopId' => self::QUERY_SHOP_ID]),
            ShopConstraint::shop(self::QUERY_SHOP_ID),
            self::QUERY_SHOP_ID,
        ];

        yield 'shop group query parameter' => [
            new Request(['shopGroupId' => self::QUERY_SHOP_GROUP_ID]),
            ShopConstraint::shopGroup(self::QUERY_SHOP_GROUP_ID),
            self::DEFAULT_SHOP_ID,
        ];

        yield 'shop group request parameter' => [
            new Request([], ['shopGroupId' => self::QUERY_SHOP_GROUP_ID]),
            ShopConstraint::shopGroup(self::QUERY_SHOP_GROUP_ID),
            self::DEFAULT_SHOP_ID,
        ];

        yield 'shop group attribute parameter' => [
            new Request([], [], ['shopGroupId' => self::QUERY_SHOP_GROUP_ID]),
            ShopConstraint::shopGroup(self::QUERY_SHOP_GROUP_ID),
            self::DEFAULT_SHOP_ID,
        ];

        yield 'all shops query parameter true' => [
            new Request(['allShops' => true]),
            ShopConstraint::allShops(),
            self::DEFAULT_SHOP_ID,
        ];

        yield 'all shops query parameter false' => [
            new Request(['allShops' => false]),
            ShopConstraint::allShops(),
            self::DEFAULT_SHOP_ID,
        ];

        yield 'all shops query parameter presence' => [
            new Request(['allShops' => null]),
            ShopConstraint::allShops(),
            self::DEFAULT_SHOP_ID,
        ];

        yield 'all shops request parameter presence' => [
            new Request([], ['allShops' => null]),
            ShopConstraint::allShops(),
            self::DEFAULT_SHOP_ID,
        ];

        yield 'all shops attributes parameter presence' => [
            new Request([], [], ['allShops' => null]),
            ShopConstraint::allShops(),
            self::DEFAULT_SHOP_ID,
        ];
    }

    public function testMissingRequestParametersWhenMultishopIsEnabled(): void
    {
        // Create request that mimic a call to external API but no shop context parameters is specified
        $event = $this->createRequestEvent(new Request());

        $shopContextBuilder = new ShopContextBuilder(
            $this->mockShopRepository(self::DEFAULT_SHOP_ID),
            $this->mockContextStateManager(),
        );
        $listener = new ShopContextListener(
            $shopContextBuilder,
            $this->mockMultistoreFeature(true),
            $this->mockConfiguration(['PS_SHOP_DEFAULT' => self::DEFAULT_SHOP_ID])
        );
        $listener->onKernelRequest($event);

        // No shop context parameters can be defined
        $this->assertNull($this->getPrivateField($shopContextBuilder, 'shopId'));
        $this->assertNull($this->getPrivateField($shopContextBuilder, 'shopConstraint'));
        $this->assertFalse($event->getRequest()->attributes->has('shopConstraint'));

        $response = $event->getResponse();
        $this->assertNotNull($response);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString('Multi shop is enabled, you must specify a shop context', $response->getContent());
    }

    private function mockMultistoreFeature(bool $isUsed): MultistoreFeature|MockObject
    {
        $feature = $this->createMock(MultistoreFeature::class);
        $feature->method('isUsed')->willReturn($isUsed);

        return $feature;
    }
}
