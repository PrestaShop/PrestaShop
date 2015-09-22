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
namespace PrestaShop\PrestaShop\Core\Foundation\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Config\ConfigCacheFactory;
use Symfony\Component\Config\ConfigCacheInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;
use Symfony\Component\Routing\Router;
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;
use PrestaShop\PrestaShop\Core\Foundation\Log\MessageStackManager;
use PrestaShop\PrestaShop\Core\Foundation\View\ViewFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * This interface gives minimal compliance for new Architecture Router/Controller/Actions mechanism.
 *
 * The interfaced methods will be implemented by each Router to let Controllers' actions call them.
 *
 * Since the Router is not a public singleton nor a global var accessible from everywhere,
 * if you have no access to the application router instance, then you can call a RoutingService instead,
 * from the application container: $container->make('CoreFoundation:Routing')
 */
interface RouterInterface
{
    /**
     * Generates a URL or path for a specific route based on the given parameters.
     *
     * You can use this to generate a URL from everywhere if you have access to a Router instance.
     * In the other case, the application Container should contains a service called 'Routing' in which you can call the same method.
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
    public function generateUrl($name, $parameters = array(), $forceLegacyUrl = false, $referenceType = UrlGeneratorInterface::ABSOLUTE_URL);

    /**
     * Dispatcher entry point. Called in entry point files (index.php).
     *
     * @param boolean $noRoutePassThrough Use True to allow dispatch function to return false if no route found. Else, an exception is raised.
     * @throws ResourceNotFoundException if $noRoutePassThrough is set to False and no route is found for the request.
     * @return boolean false if $noRoutePassThrough is set to true and no route found. Does not returns if action finished successfully (blocked by exit;)
     */
    public function dispatch($noRoutePassThrough = false);

    /**
     * This method will forward the Router into another Controller/action without any redirection instruction to the browser.
     *
     * The browser will then receive response from a different action with no URL change.
     * Used for example after a POST succeed, and we want to execute another action to display another content.
     *
     * @param Request $oldRequest The request of the action that called this method.
     * @param string $routeName The new route name to forward to.
     * @param array $routeParameters The parameters to override the route defaults parameters
     * @throws DevelopmentErrorException if the forward is made from a subcall action.
     * @return boolean true if a route is found; false if $noRoutePassThrough is set to true and no route found.
     */
    public function forward(Request &$oldRequest, $routeName, $routeParameters = array());

    /**
     * Check if a forward is possible at the moment.
     *
     * @return boolean
     */
    public function canForward();

    /**
     * Dispatcher internal entry point.
     *
     * Called by a controller/action to get a content subpart from another controller.
     *
     * @param string $routeName The route unique name/ID
     * @param array $routeParameters The route's parameters (mandatory, and optional, and even more if needed)
     * @param string $layoutMode The output mode overriden (partial view by default, means HTML content whithout any encapsulation).
     * @param boolean $fullResponse True to return full Response object instead of just the rendered content.
     * @throws ResourceNotFoundException if no route is found for the request.
     * @return string|Response data/view returned by matching controller (not sent through output buffer)
     */
    public function subcall($routeName, $routeParameters = array(), $layoutMode = BaseController::RESPONSE_PARTIAL_VIEW, $fullResponse = false);

    /**
     * The redirect call take a route and its parameters, to send a redirection header to the browser.
     *
     * If the redirect succeed, it does not return because an exit is done after redirection is sent.
     *
     * @param Request $oldRequest The previous Request instance (to take route parameters if needed)
     * @param string $routeName The name of the route (see route YML files)
     * @param array $routeParameters The route parameters to use for this route (see route YML parameters settings).
     * @param boolean $forceLegacyUrl True to use the legacy URL generation (redirects to legacy controllers)
     * @param boolean $permanent True to send 'Permanently moved' header code. False to send 'Temporary redirection' header code.
     * @throws DevelopmentErrorException if the redirect is made from a subcall action.
     * @return false if headers already sent (cannot redirect, it's too late). Does not returns if redirect succeed.
     */
    public function redirectToRoute(Request &$oldRequest, $routeName, $routeParameters, $forceLegacyUrl = false, $permanent = false);

    /**
     * The redirect call take several values in parameter:
     * - Integer value to return a specific HTTP return code and its default message (500, 404, etc...),
     * - String to indicate the URL to redirect to
     *
     * If the redirect succeed, it does not return because an exit is done after redirection sent.
     *
     * @throws DevelopmentErrorException if argument is not properly set.
     *
     * @param mixed $to Integer or String. See description for specific array format.
     * @param boolean $permanent True to send 'Permanently moved' header code. False to send 'Temporary redirection' header code. Only if $to is an URL.
     * @throws DevelopmentErrorException if the redirect is made from a subcall action.
     * @return false if headers already sent (cannot redirect, it's too late).
     */
    public function redirect($to, $permanent = false);

    /**
     * Check if a redirect is possible at the moment.
     *
     * @return boolean
     */
    public function canRedirect();

    /**
     * Get the route parameters from all YAML files (Core and modules).
     *
     * @param string $route The route unique name/ID to retrieve
     * @return \Symfony\Component\Routing\Route The found Route object, or null if not found.
     */
    public function getRouteParameters($route);

    /**
     * This method is called by PHP process to register a listener when the process is about to shutdown.
     *
     * This is used to have a last chance of operating a fatal error for example.
     * No need to call it by yourself.
     *
     * @param Request $request
     */
    public function registerShutdownFunctionCallback(Request &$request);
}
