<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Routing\Linter;

use PrestaShopBundle\Routing\Linter\Exception\LinterException;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\Routing\Route;

/**
 * Checks if SecurityAnnotation is configured for route's controller action
 */
final class SecurityAttributeLinter implements RouteLinterInterface
{
    /**
     * @param Route $route
     *
     * @return AdminSecurity[]
     *
     * @throws ReflectionException
     * @throws LinterException
     */
    public function getRouteSecurityAttributes(Route $route)
    {
        $controllerAndMethod = $this->extractControllerAndMethodNamesFromRoute($route);

        if ($controllerAndMethod === null) {
            throw new LinterException(sprintf('"%s" cannot be parsed', $route->getDefault('_controller')));
        }

        $reflection = new ReflectionMethod(
            $controllerAndMethod['controller'],
            $controllerAndMethod['method']
        );

        $attributes = $reflection->getAttributes(AdminSecurity::class);

        if (count($attributes) == 0) {
            throw new LinterException(sprintf('"%s:%s" does not have AdminSecurity attribute configured', $controllerAndMethod['controller'], $controllerAndMethod['method']));
        }

        return array_map(fn ($value): AdminSecurity => $value->newInstance(), $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function lint($routeName, Route $route)
    {
        $this->getRouteSecurityAttributes($route);
    }

    /**
     * @param Route $route
     *
     * @return array|null
     */
    private function extractControllerAndMethodNamesFromRoute(Route $route)
    {
        $controller = $route->getDefault('_controller');

        if (!str_contains($controller, '::')) {
            return null;
        }

        [$controller, $method] = explode('::', $controller, 2);

        return [
            'controller' => $controller,
            'method' => $method,
        ];
    }
}
