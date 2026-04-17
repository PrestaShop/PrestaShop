<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Routing\Converter;

use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class LegacyRouteFactory
{
    public function __construct(
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
    ) {
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
                return $this->featureFlagStateChecker->isEnabled($routeDefaults[RouterProvider::FEATURE_FLAG_NAME]);
            }

            return true;
        }

        return false;
    }
}
