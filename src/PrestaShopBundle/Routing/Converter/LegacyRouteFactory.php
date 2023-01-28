<?php

namespace PrestaShopBundle\Routing\Converter;

use PrestaShopBundle\Entity\Repository\FeatureFlagRepository;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class LegacyRouteFactory
{
    /**
     * @var FeatureFlagRepository
     */
    private $featureFlagRepository;

    public function __construct(FeatureFlagRepository $featureFlagRepository)
    {
        $this->featureFlagRepository = $featureFlagRepository;
    }

    public function buildFromCollection(RouteCollection $routeCollection): array
    {
        $legacyRoutes = [];

        foreach ($routeCollection as $routeName => $route) {
            if ($this->isLegacyRoute($route)) {
                $legacyRoutes[$routeName] = LegacyRoute::buildLegacyRoute($routeName, $route->getDefaults());
            }
        }

        return $legacyRoutes;
    }

    private function isLegacyRoute(Route $route): bool
    {
        $routeDefaults = $route->getDefaults();

        if (isset($routeDefaults[RouterProvider::LEGACY_LINK_ROUTE_ATTRIBUTE])) {
            if (isset($routeDefaults[RouterProvider::FEATURE_FLAG_NAME])) {
                return $this->featureFlagRepository->isEnabled($routeDefaults[RouterProvider::FEATURE_FLAG_NAME]);
            }

            return true;
        }

        return false;
    }
}
