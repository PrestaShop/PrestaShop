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

namespace PrestaShopBundle\EventListener\Context\API;

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Core\Context\ApiClient;
use PrestaShop\PrestaShop\Core\Context\ApiClientContext;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\HttpFoundation\Request;
use Tests\Unit\PrestaShopBundle\EventListener\Context\ContextEventListenerTestCase;

class ShopContextListenerTest extends ContextEventListenerTestCase
{
    private const DEFAULT_SHOP_ID = 42;

    public function testShopContextBasedOnApiClient(): void
    {
        // Create request that mimic a call to external API
        $event = $this->createRequestEvent(new Request([], [], ['_controller' => 'api_platform.action.placeholder']));

        $shopContextBuilder = new ShopContextBuilder(
            $this->mockShopRepository(self::DEFAULT_SHOP_ID),
            $this->mockContextStateManager(),
        );
        $listener = new ShopContextListener(
            $shopContextBuilder,
            $this->mockApiClientContext()
        );
        $listener->onKernelRequest($event);
        $expectedShopConstraint = ShopConstraint::shop(self::DEFAULT_SHOP_ID);
        $this->assertEquals(self::DEFAULT_SHOP_ID, $this->getPrivateField($shopContextBuilder, 'shopId'));
        $this->assertEquals($expectedShopConstraint, $this->getPrivateField($shopContextBuilder, 'shopConstraint'));
        $this->assertEquals($expectedShopConstraint, $event->getRequest()->attributes->get('shopConstraint'));
    }

    public function testListenForRequestNotForApi(): void
    {
        $event = $this->createRequestEvent(new Request());

        $listener = new ShopContextListener(
            $this->mockUnusedBuilder(),
            $this->createMock(ApiClientContext::class)
        );
        $listener->onKernelRequest($event);
    }

    public function testListenButNoClientAvailable(): void
    {
        // Create request that mimic a call to external API
        $event = $this->createRequestEvent(new Request([], [], ['_controller' => 'api_platform.action.placeholder']));

        $listener = new ShopContextListener(
            $this->mockUnusedBuilder(),
            $this->mockEmptyApiClientContext()
        );
        $listener->onKernelRequest($event);
    }

    private function mockUnusedBuilder(): ShopContextBuilder|MockObject
    {
        $builder = $this->createMock(ShopContextBuilder::class);
        $builder->expects($this->never())->method('setShopId');
        $builder->expects($this->never())->method('setShopConstraint');

        return $builder;
    }

    private function mockApiClientContext(): ApiClientContext|MockObject
    {
        $apiClient = $this->createMock(ApiClient::class);
        $apiClient->expects($this->once())->method('getShopId')->willReturn(self::DEFAULT_SHOP_ID);

        $context = $this->createMock(ApiClientContext::class);
        $context->expects($this->once())->method('getApiClient')->willReturn($apiClient);

        return $context;
    }

    private function mockEmptyApiClientContext(): ApiClientContext|MockObject
    {
        $context = $this->createMock(ApiClientContext::class);
        $context->expects($this->once())->method('getApiClient')->willReturn(null);

        return $context;
    }
}
