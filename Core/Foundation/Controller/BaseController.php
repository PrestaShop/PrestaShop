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

use Symfony\Component\Routing\RequestContext;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Foundation\Log\MessageStackManager;
use PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;
use PrestaShop\PrestaShop\Core\Foundation\Exception\ErrorException;

abstract class BaseController
{
    const RESPONSE_LAYOUT_HTML = 'layout_html'; // encaspulate with a layout and call templating engine to format response.
    const RESPONSE_NUDE_HTML = 'nude_html'; // same as LAYOUT_HTML but with empty layout (<html>,<head>,<title>,<body>, only).
    const RESPONSE_AJAX_HTML = 'none_html'; // no layout and call templating engine to format response.
    const RESPONSE_PARTIAL_VIEW = 'none_html'; // no layout and call templating engine to format response.
    const RESPONSE_RAW_TEXT = 'none_raw'; // no layout, no templating, no data transformation, direct controller action output
    const RESPONSE_XML = 'none_xml'; // no layout, no templating, transform response from array to XML
    const RESPONSE_JSON = 'none_json'; // no layout, transform response from array to json format
    const RESPONSE_NONE = 'none_none'; // no auto response output: case when action want to dump a file for example

    /**
     * @var AbstractRouter
     */
    protected $router;

    
    /**
     * @var \Core_Foundation_IoC_Container
     */
    protected $container;

    /**
     * Instantiate the Controller. Often made from a Router.
     *
     * @param AbstractRouter $router The Router instance that instantiated the Controller.
     */
    public function __construct(AbstractRouter &$router, \Core_Foundation_IoC_Container &$container)
    {
        $this->router =& $router;
        $this->container =& $container;
    }

    /**
     * This function will transform the resulting controller action content into various formats.
     * If you need a new one, you can override this function in your extended class. Don't forget to call
     * parent::formatResponse() in your own switch/default case.
     *
     * @param string $format
     * @param Response $response
     * @throws DevelopmentErrorException
     */
    public function formatResponse($format, Response &$response)
    {
        switch ($format) {
            case 'html':
                $this->formatHtmlResponse($response);
                break;
            case 'json':
                $this->formatJsonResponse($response);
                break;
            case 'xml':
                throw new DevelopmentErrorException('Not yet supported!');
            case 'raw':
                return;
            case 'none':
                $this->router->exitNow(); // Break PHP process! Controller action should have already sent its result by itself (file, binary, etc...)
            default:
                throw new DevelopmentErrorException('Unknown format.');
        }
    }

    /**
     * This function will format data in an HTML result. Most of the time, you should use a template engine to render
     * the response. The data given by the controller action is available in $response->getContentData(), and once
     * you rendered the HTML content, you should put it in $response->setContent().
     *
     * @param Response $response
     */
    abstract protected function formatHtmlResponse(Response &$response);

    /**
     * This will format data from $response->getContentData() into JSON format.
     *
     * @param Response $response
     */
    final protected function formatJsonResponse(Response &$response)
    {
        $content = $response->getContentData();
        $configuration = $this->container->make('Core_Business_ConfigurationInterface');
        $response->setContent(json_encode($content, $configuration->get('_PS_MODE_DEV_') ? JSON_PRETTY_PRINT : 0));
    }

    /**
     * This will choose the encapsulation function to execute.
     * If you need a new one, you can override this function in your extended class. Don't forget to call
     * parent::encapsulateResponse() in your own switch/default case.
     *
     * @param string $encapsulation
     * @param Response $response
     * @throws DevelopmentErrorException
     */
    public function encapsulateResponse($encapsulation, Response &$response)
    {
        switch ($encapsulation) {
            case 'layout':
                $this->encapsulateLayout($response);
                break;
            case 'nude':
                $this->encapsulateNudeHtml($response);
                break;
            case 'none':
                return;
            default:
                throw new DevelopmentErrorException('Unknown encapsulation.');
        }
    }

    /**
     * This function will encapsulate an HTML content into a very smart HTML layout,
     * with the minimum required to be valid HTML document.
     * If you need more HTML stuff in this mode, override this function in your extended class.
     *
     * @param Response $response
     */
    protected function encapsulateNudeHtml(Response &$response)
    {
        $content = $response->getContent();
        $content = '<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="robots" content="index, follow, all" />
        <title>Carrément a voir !</title>
    </head>
    <body>
'.$content.'
    </body>
</html>';
        $response->setContent($content);
    }

    /**
     * This function should encapsulate the content to display into an HTML layout (menu, headers, footers, etc...)
     * Implements it and use $response->getContent() to retrieve the main content.
     * Once you encapsulated the content in the layout, use $response->setContent() to store the result.
     *
     * @param Response $response
     */
    abstract protected function encapsulateLayout(Response &$response);

    /**
     * Get error(s) to the controller, to be displayed in the screen.
     * This is a wrapper method for MessageStackManager::getInstance()->getErrorIterator()
     *
     * @return SplQueue The Error queue to dequeue messages.
     */
    final public function getErrorIterator()
    {
        return MessageStackManager::getInstance()->getErrorIterator();
    }

    /**
     * get warning(s) to the controller, to be displayed in the screen.
     * This warnings are generally important malfunction of the software that must
     * be fixed. But these warnings will not throw an error and stop execution to let the user
     * fix settings in the admin interface.
     * This is a wrapper method for MessageStackManager::getInstance()->getWarningIterator()
     *
     * @return SplQueue The Warning queue to dequeue messages.
     */
    final public function getWarningIterator()
    {
        return MessageStackManager::getInstance()->getWarningIterator();
    }

    /**
     * Get info(s) to the controller, to be displayed in the screen.
     * This is a wrapper method for MessageStackManager::getInstance()->getInfoIterator()
     *
     * @return SplQueue The Info queue to dequeue messages.
     */
    final public function getInfoIterator()
    {
        return MessageStackManager::getInstance()->getInfoIterator();
    }

    /**
     * Get success(es) to the controller, to be displayed in the screen.
     * This is a wrapper method for MessageStackManager::getInstance()->getSuccessIterator()
     *
     * @return SplQueue The Success queue to dequeue messages.
     */
    final public function getSuccessIterator()
    {
        return MessageStackManager::getInstance()->getSuccessIterator();
    }

    /**
     * Gets the Router singleton that has instantiated the Controller.
     *
     * @return \PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter The router to use to forward/redirect/subcall/...
     */
    final public function getRouter()
    {
        return $this->router;
    }

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
    final public function generateUrl($name, $parameters = array(), $forceLegacyUrl = false, $referenceType = UrlGeneratorInterface::ABSOLUTE_URL)
    {
        return $this->getRouter()->generateUrl($name, $parameters, $forceLegacyUrl, $referenceType);
    }

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
     * @param string $layoutMode The output mode overriden (partial view by default, means HTML content whithout any encapsulation).
     * @param boolean $fullResponse True to return full Response object instead of just the rendered content.
     * @throws DevelopmentErrorException If the route is not found.
     * @return string|Response The action result, after template/transformations depending on $layoutMode and $fullResponse values.
     */
    final public function subcall($name, $parameters = array(), $layoutMode = BaseController::RESPONSE_PARTIAL_VIEW, $fullResponse = false)
    {
        return $this->getRouter()->subcall($name, $parameters, $layoutMode, $fullResponse);
    }

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
    final public function forward(Request &$oldRequest, $routeName, $routeParameters = array())
    {
        return $this->getRouter()->forward($oldRequest, $routeName, $routeParameters);
    }

    /**
     * The redirect call take a route and its parameters, to send a redirection header to the browser.
     * If the redirect succeed, it does not return because an exit is done after redirection is sent.
     *
     * This is a Wrapper for the method:
     * @see \PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter::redirectToRoute()
     *
     * @param mixed $to Integer, String or Array. See description for specific array format.
     * @param boolean $permanent True to send 'Permanently moved' header code. False to send 'Temporary redirection' header code. Only if $to is not a return code.
     * @throws DevelopmentErrorException if the redirect is made from a subcall action.
     * @return false if headers already sent (cannot redirect, it's too late). Does not returns if redirect succeed.
     */
    final public function redirectToRoute(Request &$oldRequest, $routeName, $routeParameters, $forceLegacyUrl = false, $permanent = false)
    {
        $this->getRouter()->redirectToRoute($oldRequest, $routeName, $routeParameters, $forceLegacyUrl, $permanent);
    }

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
    final public function redirect($to, $permanent = false)
    {
        $this->getRouter()->redirect($to, $permanent);
    }
}
