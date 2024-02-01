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

namespace PrestaShopBundle\Routing\Linter;

use PrestaShopBundle\Routing\Linter\Exception\LinterException;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
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
     * @throws \ReflectionException
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
