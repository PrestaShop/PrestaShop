<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Routing;

use PrestaShopBundle\Security\Admin\RequestAttributes;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * This service provides the list of anonymous routes (identified via their _anonymous_controller attribute),
 * since the getRouteCollection method is very heavy to call it includes an internal cache system to reduce the
 * const of this check.
 */
class AnonymousRouteProvider implements CacheWarmerInterface
{
    public function __construct(
        private readonly Router $router,
        private readonly CacheInterface $cache,
    ) {
    }

    public function getAnonymousRoutes(): array
    {
        return $this->cache->get('anonymous_routes', function () {
            $anonymousRoutes = [];
            $routeCollection = $this->router->getRouteCollection();
            foreach ($routeCollection as $name => $route) {
                if ($route->getDefault(RequestAttributes::ANONYMOUS_CONTROLLER_ATTRIBUTE) === true) {
                    $anonymousRoutes[$name] = $route;
                }
            }

            return $anonymousRoutes;
        });
    }

    public function isRouteAnonymous(string $routeName): bool
    {
        return array_key_exists($routeName, $this->getAnonymousRoutes());
    }

    public function warmUp(string $cacheDir): array
    {
        // Simply call the method so that its result is put in the cache
        $this->getAnonymousRoutes();

        return [
            self::class,
        ];
    }

    public function isOptional(): bool
    {
        return true;
    }
}
