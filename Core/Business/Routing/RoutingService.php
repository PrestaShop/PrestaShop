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

use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;

/**
 * This Router wrapper will offers public services that will be available from the main IoC Container provider.
 *
 * To retrieve this service you should call:
 * $container->make('CoreFoundation:RoutingService'); or $container->make('Routing');
 *
 * From legacy code, $container is a global. From the new architecture, you must have access to the
 * application container (from a Controller/action, use $this->container).
 *
 */
class RoutingService
{
    /**
     * @var Router
     */
    private $router;

    final private function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @var boolean
     */
    private static $instanciated = false;

    /**
     * Called by Router during start of the PHP process, to register this service in the Container.
     * Do not call it by yourself.
     *
     * @param Router $router
     * @param Container $container
     */
    final public static function registerRoutingService(Router $router, Container $container)
    {
        if (self::$instanciated !== false) {
            return;
        }
        $service = new self($router);
        $container->bind('PrestaShop\\PrestaShop\\Core\\Business\\Routing\\RoutingService', $service, true);
        $container->bind('Routing', $service, true);
        self::$instanciated = true;
    }

    /**
     * Generates a URL or path for a specific route based on the given parameters.
     *
     * This is a Wrapper for the Symfony method:
     * @see \Symfony\Component\Routing\Generator\UrlGeneratorInterface::generate()
     * but also adds a legacy URL generation support.
     *
     * @param string      $name             The name of the route
     * @param mixed       $parameters       An array of parameters (to use in route matching, or to add as GET values if $forceLegacyUrl is True)
     * @param bool        $forceLegacyUrl   True to use alternative URL to reach another dispatcher.
     *                                      You must override the method in a Controller subclass in order to use this option.
     * @param bool|string $referenceType The type of reference to be generated (one of the constants)
     *
     * @return string The generated URL
     *
     * @throws RouteNotFoundException              If the named route doesn't exist
     * @throws MissingMandatoryParametersException When some parameters are missing that are mandatory for the route
     * @throws InvalidParameterException           When a parameter value for a placeholder is not correct because
     *                                             it does not match the requirement
     * @throws DevelopmentErrorException           If $forceLegacyUrl True, without proper method override.
     */
    final public function generateUrl($name, $parameters = array(), $forceLegacyUrl = false, $referenceType = UrlGeneratorInterface::ABSOLUTE_URL)
    {
        return $this->router->generateUrl($name, $parameters, $forceLegacyUrl, $referenceType);
    }

    /**
     * Sets the URL/code to use if a forbidden redirection is called through setForbiddenRedirection().
     *
     * @see AbstractRouter::redirect()
     *
     * @param mixed $redirection Integer or String
     */
    final public function setForbiddenRedirection($redirection)
    {
        $this->router->setForbiddenRedirection($redirection);
    }
}
