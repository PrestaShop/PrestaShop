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
namespace PrestaShop\PrestaShop\Core\Business\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\ConfigCacheFactory;
use Symfony\Component\Config\ConfigCacheInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use PrestaShop\PrestaShop\Core\Business\Routing\Router;
use PrestaShop\PrestaShop\Core\Business\Controller\FrontController;
use PrestaShop\PrestaShop\Core\Business\Controller\AdminController;
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;
use PrestaShop\PrestaShop\Core\Business\Context;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FrontRouter extends Router
{
    /**
     * @var FrontRouter
     */
    private static $instance = null;

    /**
     * Get current instance of router (singleton), and affect the Container singleton as well.
     *
     * @return FrontRouter
     */
    final public static function getInstance(\Core_Foundation_IoC_Container &$container = null)
    {
        if (!self::$instance) {
            if ($container != null) {
                self::$container = $container;
            }
            self::$instance = new self('front_routes(_(.*))?\.yml');
        }
        return self::$instance;
    }

    final protected function checkControllerAuthority(\ReflectionClass $class)
    {
        if (!$class->isSubclassOf('PrestaShop\\PrestaShop\\Core\\Business\\Controller\\FrontController')
            || $class->isSubclassOf('PrestaShop\\PrestaShop\\Core\\Business\\Controller\\AdminController')) {
            throw new DevelopmentErrorException('Front router tried to call a non-front controller ('.$class->name.'). Please verify your routes Settings, and controllers.', null, 1004);
        }
    }

    /**
     * Generates a URL or path for a specific Front route based on the given parameters.
     *
     * This is a Wrapper for the Symfony method:
     * @see \Symfony\Component\Routing\Generator\UrlGeneratorInterface::generate()
     * but also adds a legacy URL generation support for Front interface.
     *
     * @param string      $name             The name of the route
     * @param mixed       $parameters       An array of parameters (to use in route matching, or to add as GET values if $forceLegacyUrl is True)
     * @param bool        $forceLegacyUrl   True to use alternative URL to reach legacy dispatcher.
     * @param bool|string $referenceType The type of reference to be generated (one of the constants)
     *
     * @return string The generated URL
     *
     * @throws RouteNotFoundException              If the named route doesn't exist
     * @throws MissingMandatoryParametersException When some parameters are missing that are mandatory for the route
     * @throws InvalidParameterException           When a parameter value for a placeholder is not correct because
     *                                             it does not match the requirement
     */
    final public function generateUrl($name, $parameters = array(), $forceLegacyUrl = false, $referenceType = UrlGeneratorInterface::ABSOLUTE_URL)
    {
        if (($routeParams = $this->getRouteParameters($name)) &&
            ($defaultParams = $routeParams->getDefaults()) &&
            ($forceLegacyUrl == true || (isset($defaultParams['_legacy_force']) && $defaultParams['_legacy_force'] === true)) &&
            isset($defaultParams['_legacy_path']) &&
            ($link = Context::getInstance()->link)) { // For legacy case!
            $legacyPath = $defaultParams['_legacy_path'];

            $legacyContext = self::$container->make('Adapter_LegacyContext');
            return $legacyContext->getFrontUrl($legacyPath);
        }
        return parent::generateUrl($name, $parameters, false, $referenceType);
    }
}
