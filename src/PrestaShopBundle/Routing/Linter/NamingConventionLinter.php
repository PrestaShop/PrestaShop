<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Routing\Linter;

use PrestaShop\PrestaShop\Core\Util\Inflector;
use PrestaShopBundle\Routing\Linter\Exception\ControllerNotFoundException;
use PrestaShopBundle\Routing\Linter\Exception\NamingConventionException;
use PrestaShopBundle\Routing\Linter\Exception\SymfonyControllerConventionException;
use Symfony\Component\Routing\Route;

/**
 * Checks that route and contoller follows naming conventions
 */
final class NamingConventionLinter implements RouteLinterInterface
{
    /**
     * {@inheritdoc}
     */
    public function lint($routeName, Route $route)
    {
        $controllerAndMethodName = $this->getControllerAndMethodName($route);

        $pluralizedController = Inflector::getInflector()->tableize(
            Inflector::getInflector()->pluralize($controllerAndMethodName['controller'])
        );

        $expectedRouteName = strtr('admin_{resources}_{action}', [
            '{resources}' => $pluralizedController,
            '{action}' => Inflector::getInflector()->tableize($controllerAndMethodName['method']),
        ]);

        if ($routeName !== $expectedRouteName) {
            throw new NamingConventionException(
                sprintf('Route "%s" does not follow naming convention.', $routeName),
                0,
                null,
                $expectedRouteName
            );
        }
    }

    /**
     * @param Route $route
     *
     * @return array
     */
    private function getControllerAndMethodName(Route $route)
    {
        $defaultController = $route->getDefault('_controller');
        if (!str_contains($defaultController, '::')) {
            throw new SymfonyControllerConventionException(
                sprintf('Controller "%s" does not follow symfony convention.', $defaultController),
                $defaultController
            );
        }

        [$controller, $method] = explode('::', $defaultController, 2);
        if (!method_exists($controller, $method)) {
            throw new ControllerNotFoundException(
                sprintf('Controller "%s" does not exist.', $defaultController),
                $defaultController
            );
        }

        $controllerParts = explode('\\', $controller);
        $controller = preg_replace('/Controller$/', '', end($controllerParts));

        $method = preg_replace('/Action$/', '', $method);

        return [
            'controller' => $controller,
            'method' => $method,
        ];
    }
}
