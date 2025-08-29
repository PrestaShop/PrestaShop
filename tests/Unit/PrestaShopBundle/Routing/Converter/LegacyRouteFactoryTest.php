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

namespace Tests\Unit\PrestaShopBundle\Routing\Converter;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagManager;
use PrestaShopBundle\Routing\Converter\LegacyRoute;
use PrestaShopBundle\Routing\Converter\LegacyRouteFactory;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class LegacyRouteFactoryTest extends TestCase
{
    /**
     * @dataProvider getRouteDataProviderFeatureFlag
     */
    public function testRouteEnabledWithEnabledFeatureFlags(Route $route): void
    {
        $factory = new LegacyRouteFactory(
            $featureFlagManager = $this->createMock(FeatureFlagManager::class)
        );

        $featureFlagManager
            ->method('isEnabled')
            ->willReturn(true)
        ;

        $collection = new RouteCollection();
        $collection->add('symfony_route', new Route('/', [
            'route_name' => 'test',
        ]));
        $collection->add('feature_flag_route_name', $route);
        $routes = $factory->buildFromCollection($collection);

        self::assertIsArray($routes);
        self::assertCount(1, $routes);
        self::assertContainsOnlyInstancesOf(LegacyRoute::class, $routes);
    }

    public function getRouteDataProviderFeatureFlag(): iterable
    {
        yield [new Route('/', [
            '_legacy_link' => 'AdminController',
            '_legacy_feature_flag' => 'AdminControllerV2',
        ])];
    }

    /**
     * @dataProvider getRoutes
     */
    public function testRouteWithoutFeatureFlagShouldNeverCallRepository(
        Route $route
    ): void {
        $factory = new LegacyRouteFactory(
            $featureFlagManager = $this->createMock(FeatureFlagManager::class)
        );

        // assert no calls to repository
        $featureFlagManager
            ->expects(self::never())
            ->method('isEnabled')
        ;

        $collection = new RouteCollection();
        $collection->add('test', $route);
        $routes = $factory->buildFromCollection($collection);
        self::assertIsArray($routes);
        self::assertContainsOnlyInstancesOf(LegacyRoute::class, $routes);
    }

    public function getRoutes(): iterable
    {
        yield [new Route('/')];
        yield [new Route('/', [
            '_legacy_link' => 'AdminController',
        ])];
    }
}
