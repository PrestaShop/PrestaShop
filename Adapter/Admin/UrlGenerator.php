<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter\Admin;

use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PrestaShop\PrestaShop\Adapter\Adapter_LegacyContext;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Symfony\Component\Routing\RequestContext;

/**
 * TODO !1 : PHPDoc
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

        // Base path contains admin directory ('admin-dev' or random 'admin-xxxx')
        $basePath = $this->legacyContext->getAdminBaseUrl();

        // Try to get controller & parameters with mapping options
        $route = $this->router->getRouteCollection()->get($name);
        if ($route) {
            if ($route->hasOption('_legacy_controller')) {
                $legacyController = $route->getOption('_legacy_controller');
                if ($route->hasOption('_legacy_param_mapper_class') && $route->hasOption('_legacy_param_mapper_method')) {
                    $class = $route->getOption('_legacy_param_mapper_class');
                    $method = $route->getOption('_legacy_param_mapper_method');
                    $method = (new \ReflectionClass('\\'.$class))->getMethod($method);
                    $legacyParameters = $method->invoke(($method->isStatic())?null:$method->getDeclaringClass()->newInstance(), $parameters);
                }
            }
        }

        return $basePath.$this->legacyContext->getAdminLink($legacyController, true, $legacyParameters);
    }
    
    // TODO !4: TU

    /* (non-PHPdoc)
     * @see \Symfony\Component\Routing\RequestContextAwareInterface::setContext()
     */
    public function setContext(RequestContext $context)
    {
        throw new \BadMethodCallException('Cannot use this UrlGeneratorInterface implementation with a Symfony context. Please call AdminUrlGeneratorFactory::forLegacy() to reach the right instance.');
    }

    /* (non-PHPdoc)
     * @see \Symfony\Component\Routing\RequestContextAwareInterface::getContext()
     */
    public function getContext()
    {
        throw new \BadMethodCallException('Cannot use this UrlGeneratorInterface implementation with a Symfony context. Please call AdminUrlGeneratorFactory::forLegacy() to reach the right instance.');
    }
}
