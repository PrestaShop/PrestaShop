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
