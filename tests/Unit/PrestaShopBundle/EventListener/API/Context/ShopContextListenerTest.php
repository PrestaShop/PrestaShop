<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener\API\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use PrestaShopBundle\Controller\Api\OAuth2\AccessTokenController;
use PrestaShopBundle\EventListener\API\Context\ShopContextListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\PrestaShopBundle\EventListener\ContextEventListenerTestCase;

class ShopContextListenerTest extends ContextEventListenerTestCase
{
    private const DEFAULT_SHOP_ID = 42;
    private const QUERY_SHOP_ID = 51;
    private const QUERY_SECOND_SHOP_ID = 99;
    private const QUERY_SHOP_GROUP_ID = 69;
    private const OTHER_SHOP_GROUP_ID = 70;

    /**
     * Stubbed installation for the representative-shop rule: the default shop (42) and
     * shop 51 belong to group 69; shop 99 is alone in group 70.
     */
    private const SHOPS_BY_GROUP = [
        self::QUERY_SHOP_GROUP_ID => [self::DEFAULT_SHOP_ID, self::QUERY_SHOP_ID],
        self::OTHER_SHOP_GROUP_ID => [self::QUERY_SECOND_SHOP_ID],
    ];

    public function testShopContextWhenMultishopDisabled(): void
    {
        // Create request that mimic a call to external API
        $event = $this->createRequestEvent(new Request());

        $shopContextBuilder = new ShopContextBuilder(
            $this->mockShopRepository(self::DEFAULT_SHOP_ID),
            $this->mockContextStateManager(),
            $this->mockMultistoreFeature(false)
        );
        $listener = new ShopContextListener(
            $shopContextBuilder,
            $this->mockMultistoreFeature(false),
            $this->mockConfiguration(['PS_SHOP_DEFAULT' => self::DEFAULT_SHOP_ID]),
            $this->mockShopListResolver()
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
            $this->mockMultistoreFeature(true)
        );
        $listener = new ShopContextListener(
            $shopContextBuilder,
            $this->mockMultistoreFeature(true),
            $this->mockConfiguration(['PS_SHOP_DEFAULT' => self::DEFAULT_SHOP_ID]),
            $this->mockShopListResolver()
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

        yield 'shop group without the default shop resolves an in-scope shop' => [
            new Request(['shopGroupId' => self::OTHER_SHOP_GROUP_ID]),
            ShopConstraint::shopGroup(self::OTHER_SHOP_GROUP_ID),
            self::QUERY_SECOND_SHOP_ID,
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

        yield 'shop collection query parameter string list' => [
            new Request(['shopIds' => self::QUERY_SHOP_ID . ',' . self::QUERY_SECOND_SHOP_ID]),
            ShopCollection::shops([self::QUERY_SHOP_ID, self::QUERY_SECOND_SHOP_ID]),
            self::QUERY_SHOP_ID,
        ];

        yield 'shop collection request parameter string list' => [
            new Request([], ['shopIds' => self::QUERY_SECOND_SHOP_ID . ',' . self::QUERY_SHOP_ID]),
            ShopCollection::shops([self::QUERY_SECOND_SHOP_ID, self::QUERY_SHOP_ID]),
            self::QUERY_SHOP_ID,
        ];

        yield 'shop collection attribute parameter string list' => [
            new Request([], [], ['shopIds' => self::QUERY_SECOND_SHOP_ID . ', ' . self::QUERY_SHOP_ID]),
            ShopCollection::shops([self::QUERY_SECOND_SHOP_ID, self::QUERY_SHOP_ID]),
            self::QUERY_SHOP_ID,
        ];

        yield 'shop collection query parameter array' => [
            new Request(['shopIds' => [self::QUERY_SHOP_ID, self::QUERY_SECOND_SHOP_ID]]),
            ShopCollection::shops([self::QUERY_SHOP_ID, self::QUERY_SECOND_SHOP_ID]),
            self::QUERY_SHOP_ID,
        ];

        yield 'shop collection request parameter array' => [
            new Request([], ['shopIds' => [self::QUERY_SECOND_SHOP_ID, self::QUERY_SHOP_ID]]),
            ShopCollection::shops([self::QUERY_SECOND_SHOP_ID, self::QUERY_SHOP_ID]),
            self::QUERY_SHOP_ID,
        ];

        yield 'shop collection attribute parameter array' => [
            new Request([], [], ['shopIds' => [self::QUERY_SECOND_SHOP_ID, self::QUERY_SHOP_ID]]),
            ShopCollection::shops([self::QUERY_SECOND_SHOP_ID, self::QUERY_SHOP_ID]),
            self::QUERY_SHOP_ID,
        ];
    }

    public function testMissingRequestParametersWhenMultishopIsEnabled(): void
    {
        // Create request that mimic a call to external API but no shop context parameters is specified
        $event = $this->createRequestEvent(new Request());

        $shopContextBuilder = new ShopContextBuilder(
            $this->mockShopRepository(self::DEFAULT_SHOP_ID),
            $this->mockContextStateManager(),
            $this->mockMultistoreFeature(true)
        );
        $listener = new ShopContextListener(
            $shopContextBuilder,
            $this->mockMultistoreFeature(true),
            $this->mockConfiguration(['PS_SHOP_DEFAULT' => self::DEFAULT_SHOP_ID]),
            $this->mockShopListResolver()
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

    /**
     * Mirrors ShopListResolver's representative rule over the stubbed SHOPS_BY_GROUP
     * installation: the default shop when it belongs to the scope, else the lowest shop id.
     */
    private function mockShopListResolver(): ShopListResolverInterface|MockObject
    {
        $resolver = $this->createMock(ShopListResolverInterface::class);
        $resolver->method('resolveRepresentativeShopId')->willReturnCallback(
            static function (ShopConstraint $shopConstraint): int {
                if (null !== $shopConstraint->getShopId()) {
                    return $shopConstraint->getShopId()->getValue();
                }
                if ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds()) {
                    $shopIds = array_map(static fn (ShopId $shopId): int => $shopId->getValue(), $shopConstraint->getShopIds());
                } elseif (null !== $shopConstraint->getShopGroupId()) {
                    $shopIds = self::SHOPS_BY_GROUP[$shopConstraint->getShopGroupId()->getValue()] ?? [];
                } else {
                    $shopIds = array_merge(...array_values(self::SHOPS_BY_GROUP));
                }
                if ([] === $shopIds) {
                    return 0;
                }

                return in_array(self::DEFAULT_SHOP_ID, $shopIds, true) ? self::DEFAULT_SHOP_ID : min($shopIds);
            }
        );

        return $resolver;
    }

    private function mockMultistoreFeature(bool $isUsed): MultistoreFeature|MockObject
    {
        $feature = $this->createMock(MultistoreFeature::class);
        $feature->method('isUsed')->willReturn($isUsed);

        return $feature;
    }
}
