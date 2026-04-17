<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Routing\Linter;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Get all configured routes for Back Office
 */
final class AdminRouteProvider
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return Route[] As routeName => route
     */
    public function getRoutes()
    {
        $adminRoutes = [];

        foreach ($this->router->getRouteCollection() as $routeName => $route) {
            if (!str_starts_with($routeName, 'admin_')) {
                continue;
            }

            $adminRoutes[$routeName] = $route;
        }

        return $adminRoutes;
    }
}
