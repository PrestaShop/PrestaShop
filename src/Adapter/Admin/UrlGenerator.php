<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Admin;

use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Symfony\Component\Process\Exception\LogicException;
use ReflectionClass;

/**
 * This UrlGeneratorInterface implementation (in a Sf service) will provides Legacy URLs.
 *
 * To be used by Symfony controllers, to generate a link to a Legacy page.
 * Call an instance of it through the Symfony container:
 * $container->get('prestashop.core.admin.url_generator_legacy');
 * Or via the UrlGeneratorFactory (as Sf service):
 * $container->get('prestashop.core.admin.url_generator_factory')->forLegacy();
 */
class UrlGenerator implements UrlGeneratorInterface
{
    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @var Router
     */
    private $router;

    /**
     * Constructor.
     *
     * @param LegacyContext $legacyContext
     * @param Router
     */
    public function __construct(LegacyContext $legacyContext, Router $router)
    {
        $this->legacyContext = $legacyContext;
        $this->router = $router;
    }

    /* (non-PHPdoc)
     * @see \Symfony\Component\Routing\Generator\UrlGeneratorInterface::generate()
     */
    public function generate($name, $parameters = array(), $referenceType = self::ABSOLUTE_PATH)
    {
        // By default, consider given parameters in legacy format (no mapping if route not found).
        $legacyController = $name;
        $legacyParameters = $parameters;

        // resolve route & legacy mapping
        list($legacyController, $legacyParameters) = $this->getLegacyOptions($name, $parameters);

        return $this->legacyContext->getAdminLink($legacyController, true, $legacyParameters);
    }

    /**
     * Try to get controller & parameters with mapping options.
     *
     * If failed to find options, then return the input values.
     *
     * @param string   $routeName
     * @param string[] $parameters The route parameters to convert
     *
     * @return array[] An array with: the legacy controller name, then the parameters array
     */
    final public function getLegacyOptions($routeName, $parameters = array())
    {
        $legacyController = $routeName;
        $legacyParameters = $parameters;

        $route = $this->router->getRouteCollection()->get($routeName);
        if ($route) {
            if ($route->hasDefault('_legacy_controller')) {
                $legacyController = $route->getDefault('_legacy_controller');
                if ($route->hasDefault('_legacy_param_mapper_class') && $route->hasDefault('_legacy_param_mapper_method')) {
                    $class = $route->getDefault('_legacy_param_mapper_class');
                    $method = $route->getDefault('_legacy_param_mapper_method');
                    $method = (new ReflectionClass('\\'.$class))->getMethod($method);
                    $legacyParameters = $method->invoke(($method->isStatic()) ? null : $method->getDeclaringClass()->newInstance(), $parameters);
                }
            }
        }

        return array($legacyController, $legacyParameters);
    }

    /* (non-PHPdoc)
     * @see \Symfony\Component\Routing\RequestContextAwareInterface::setContext()
     */
    public function setContext(RequestContext $context)
    {
        throw new LogicException('Cannot use this UrlGeneratorInterface implementation with a Symfony context. Please call AdminUrlGeneratorFactory::forLegacy() to reach the right instance.');
    }

    /* (non-PHPdoc)
     * @see \Symfony\Component\Routing\RequestContextAwareInterface::getContext()
     */
    public function getContext()
    {
        throw new LogicException('Cannot use this UrlGeneratorInterface implementation with a Symfony context. Please call AdminUrlGeneratorFactory::forLegacy() to reach the right instance.');
    }
}
