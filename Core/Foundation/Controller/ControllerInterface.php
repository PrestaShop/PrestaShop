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
namespace PrestaShop\PrestaShop\Core\Foundation\Controller;

use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Foundation\Log\MessageStackManager;
use PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;

/**
 * Interface for every Controller that the Router can execute.
 * This layer describe the format transformation protocole, encapsulation options, and some wrapper methods to implement.
 */
interface ControllerInterface
{
    // These constants are parseable, with '_' as separator, and are used in YML files, so do not change them into integers.
    const RESPONSE_LAYOUT_HTML = 'layout_html'; // encaspulate with a layout and call templating engine to format response.
    const RESPONSE_NUDE_HTML = 'nude_html'; // same as LAYOUT_HTML but with empty layout (<html>,<head>,<title>,<body>, only).
    const RESPONSE_AJAX_HTML = 'none_html'; // no layout and call templating engine to format response.
    const RESPONSE_PARTIAL_VIEW = 'none_html'; // no layout and call templating engine to format response.
    const RESPONSE_RAW_TEXT = 'none_raw'; // no layout, no templating, no data transformation, direct controller action output
    const RESPONSE_XML = 'none_xml'; // no layout, no templating, transform response from array to XML
    const RESPONSE_JSON = 'none_json'; // no layout, transform response from array to json format
    const RESPONSE_NONE = 'none_none'; // no auto response output: case when action want to dump a file for example

    /**
     * Ensures the Controller constructor called its parent constructor.
     * If not, the Router will throw an Exception to avoid using a controller that shortcuts
     * the parent construction (for security reasons).
     *
     * Sub extended classes can finalize their override of this method to fix the minimal level of strategy checking.
     *
     * @return boolean True if the Controller has been constructed using all the parents' __construct methods.
     */
    public function isConstructionStrategyChecked();

    /**
     * This function will transform the resulting controller action content into various formats.
     * If you need a new one, you can override this function in your extended class. Don't forget to call
     * parent::formatResponse() in your own switch/default case.
     *
     * @param string $format The format method to manage ('html', 'json', 'xml', etc...)
     * @param Response $response
     * @throws DevelopmentErrorException in case of unknown formatting method.
     */
    public function formatResponse($format, Response &$response);

    /**
     * This will choose the encapsulation function to execute.
     * If you need a new one, you can override this function in your extended class. Don't forget to call
     * parent::encapsulateResponse() in your own switch/default case.
     *
     * @param string $encapsulation
     * @param Response $response
     * @throws DevelopmentErrorException in case of unknown encapsulation method.
     */
    public function encapsulateResponse($encapsulation, Response &$response);

    /**
     * Get error(s) to the controller, to be displayed in the screen.
     * This is a wrapper method for MessageStackManager->getErrorIterator()
     *
     * @return SplQueue The Error queue to dequeue messages.
     */
    public function getErrorIterator();

    /**
     * get warning(s) to the controller, to be displayed in the screen.
     * This warnings are generally important malfunction of the software that must
     * be fixed. But these warnings will not throw an error and stop execution to let the user
     * fix settings in the admin interface.
     * This is a wrapper method for MessageStackManager->getWarningIterator()
     *
     * @return SplQueue The Warning queue to dequeue messages.
     */
    public function getWarningIterator();

    /**
     * Get info(s) to the controller, to be displayed in the screen.
     * This is a wrapper method for MessageStackManager->getInfoIterator()
     *
     * @return SplQueue The Info queue to dequeue messages.
     */
    public function getInfoIterator();

    /**
     * Get success(es) to the controller, to be displayed in the screen.
     * This is a wrapper method for MessageStackManager->getSuccessIterator()
     *
     * @return SplQueue The Success queue to dequeue messages.
     */
    public function getSuccessIterator();

    /**
     * Gets the Router singleton that has instantiated the Controller.
     *
     * @return \PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter The router to use to forward/redirect/subcall/...
     */
    public function getRouter();

    /**
     * Generates a URL or path for a specific route based on the given parameters.
     *
     * This is a Wrapper for the method:
     * @see \PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter::generateUrl()
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
    public function generateUrl($name, array $parameters = array(), $forceLegacyUrl = false, $referenceType = UrlGeneratorInterface::ABSOLUTE_URL);

    /**
     * You can call a Controller/action directly from another Controller/action by calling this.
     * The route will be resolved, all pre-action and post-action Traits will be played, like a classical Router->dispatch action,
     * BUT the output behavior will be overriden to return the result instead of print it in the output buffer, throught $layoutMode.
     *
     * This is a Wrapper for the method:
     * @see \PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter::subcall()
     *
     * @param string $name The route unique name/ID
     * @param array $parameters The route's parameters (mandatory, and optional, and even more if needed)
     * @param integer $layoutMode The output mode overriden (partial view by default, means HTML content whithout any encapsulation).
     * @param boolean $fullResponse True to return full Response object instead of just the rendered content.
     * @throws DevelopmentErrorException If the route is not found.
     * @return string|Response The action result, after template/transformations depending on $layoutMode and $fullResponse values.
     */
    public function subcall($name, array $parameters = array(), $layoutMode = BaseController::RESPONSE_PARTIAL_VIEW, $fullResponse = false);

    /**
     * This method will forward the Router into another Controller/action without any redirection instruction to the browser.
     * The browser will then receive response from a different action with no URL change.
     * Used for example after a POST succeed, and we want to execute another action to display another content.
     *
     * This is a Wrapper for the method:
     * @see \PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter::forward()
     *
     * @param Request $oldRequest The request of the action that called this method.
     * @param string $routeName The new route name to forward to.
     * @param array $routeParameters The parameters to override the route defaults parameters
     * @throws DevelopmentErrorException if the forward is made from a subcall action.
     * @return boolean true if a route is found; false if $noRoutePassThrough is set to true and no route found.
     */
    public function forward(Request &$oldRequest, $routeName, array $routeParameters = array());

    /**
     * The redirect call take a route and its parameters, to send a redirection header to the browser.
     * If the redirect succeed, it does not return because an exit is done after redirection is sent.
     *
     * This is a Wrapper for the method:
     * @see \PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter::redirectToRoute()
     *
     * @param Request $oldRequest The previous Request instance (to take route parameters if needed)
     * @param string $routeName The name of the route (see route YML files)
     * @param array $routeParameters The route parameters to use for this route (see route YML parameters settings).
     * @param boolean $forceLegacyUrl True to use the legacy URL generation (redirects to legacy controllers)
     * @param boolean $permanent True to send 'Permanently moved' header code. False to send 'Temporary redirection' header code.
     * @throws DevelopmentErrorException if the redirect is made from a subcall action.
     * @return false if headers already sent (cannot redirect, it's too late). Does not returns if redirect succeed.
     */
    public function redirectToRoute(Request &$oldRequest, $routeName, array $routeParameters, $forceLegacyUrl = false, $permanent = false);

    /**
     * The redirect call take several values in parameter:
     * - Integer value to return a specific HTTP return code and its default message (500, 404, etc...),
     * - String to indicate the URL to redirect to
     *
     * If the redirect succeed, it does not return because an exit is done after redirection sent.
     *
     * This is a Wrapper for the method:
     * @see \PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter::redirect()
     *
     * @throws DevelopmentErrorException if argument is not properly set.
     *
     * @param mixed $to Integer or String. See description for specific array format.
     * @param boolean $permanent True to send 'Permanently moved' header code. False to send 'Temporary redirection' header code. Only if $to is an URL.
     * @return false if headers already sent (cannot redirect, it's too late).
     */
    public function redirect($to, $permanent = false);
}
