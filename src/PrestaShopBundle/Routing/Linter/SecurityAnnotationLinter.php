<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Routing\Linter;

use Doctrine\Common\Annotations\Reader;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use ReflectionMethod;
use Symfony\Component\Routing\Route;

/**
 * Checks if SecurityAnnotation is configured for route's controller action
 */
final class SecurityAnnotationLinter implements RouteLinterInterface
{
    /**
     * @var Reader
     */
    private $annoationReader;

    /**
     * @param Reader $annoationReader
     */
    public function __construct(Reader $annoationReader)
    {
        $this->annoationReader = $annoationReader;
    }

    /**
     * {@inheritdoc}
     */
    public function lint(Route $route)
    {
        $controllerAndMethod = $this->extractControllerAndMethodNamesFromRoute($route);

        $reflection = new ReflectionMethod(
            $controllerAndMethod['controller'],
            $controllerAndMethod['method']
        );

        $annotation = $this->annoationReader->getMethodAnnotation($reflection, AdminSecurity::class);

        if (null === $annotation) {
            throw new LinterException(sprintf(
                '"%s:%s" does not have AdminSecurity annotation configured',
                 $controllerAndMethod['controller'],
                 $controllerAndMethod['method']
            ));
        }
    }

    /**
     * @param Route $route
     *
     * @return array
     */
    private function extractControllerAndMethodNamesFromRoute(Route $route)
    {
        // @todo: parsing needs to be improved & refactored into separate service

        list($controller, $method) = explode(':', $route->getDefault('_controller'));

        return [
            'controller' => $controller,
            'method' => $method,
        ];
    }
}
