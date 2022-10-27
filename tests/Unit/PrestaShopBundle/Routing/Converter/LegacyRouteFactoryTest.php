<?php

namespace Tests\Unit\PrestaShopBundle\Routing\Converter;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Entity\Repository\FeatureFlagRepository;
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
            $featureFlagRepository = $this->createMock(FeatureFlagRepository::class)
        );

        $featureFlagRepository
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
            $featureFlagRepo = $this->createMock(FeatureFlagRepository::class)
        );

        // assert no calls to repository
        $featureFlagRepo
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
