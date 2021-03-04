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

use Doctrine\Common\Inflector\Inflector;
use PrestaShopBundle\Routing\Linter\Exception\NamingConventionException;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Symfony\Component\Routing\Route;

/**
 * Checks that route and contoller follows naming conventions
 */
final class NamingConventionLinter implements RouteLinterInterface
{
    /**
     * @var ControllerNameParser
     */
    private $controllerNameParser;

    /**
     * @param ControllerNameParser $controllerNameParser
     */
    public function __construct(ControllerNameParser $controllerNameParser)
    {
        $this->controllerNameParser = $controllerNameParser;
    }

    /**
     * {@inheritdoc}
     */
    public function lint($routeName, Route $route)
    {
        $controllerAndMethodName = $this->getControllerAndMethodName($route);

        $pluralizedController = Inflector::tableize(
            Inflector::pluralize($controllerAndMethodName['controller'])
        );

        $expectedRouteName = strtr('admin_{resources}_{action}', [
            '{resources}' => $pluralizedController,
            '{action}' => Inflector::tableize($controllerAndMethodName['method']),
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
        $controller = $route->getDefault('_controller');

        if (strpos($controller, '::') === false) {
            $controller = $this->controllerNameParser->parse($controller);
        }

        list($controller, $method) = explode('::', $controller, 2);

        $controllerParts = explode('\\', $controller);
        $controller = preg_replace('/Controller$/', '', end($controllerParts));

        $method = preg_replace('/Action$/', '', $method);

        return [
            'controller' => $controller,
            'method' => $method,
        ];
    }
}
