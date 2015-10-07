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
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use PrestaShop\PrestaShop\Core\Foundation\Controller\AbstractController;
use PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter;
use PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PrestaShop\PrestaShop\Core\Business\Dispatcher\BaseEventDispatcher;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\EventDispatcher;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use PrestaShop\PrestaShop\Adapter\Translator;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use PrestaShop\PrestaShop\Core\Foundation\Controller\ControllerResolver;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\EventFactory;

/**
 * Second layer of the Router classes structure, to add Business specific behaviors (but common for Front/Admin).
 *
 * - Avoid double instantiation of a Router class,
 * - Generates the action execution sequence (with Traits methods),
 * - Implements dispatch, subcall, redirect,
 * - Exposes new abstract functions to be implemented specifically on Admin/Front subclasses.
 */
abstract class Router extends AbstractRouter
{
    /**
     * If true, a new instance will fail to construct. Avoid multiple Router instances in the same PHP process.
     * @var boolean
     */
    private static $instantiated = false;

    /**
     * This stack is filled during dispatch, forwards, subcalls.
     * @var Request[]
     */
    private static $requestStack = array();

    /**
     * This stack is filled during dispatch, forwards, subcalls.
     * @var Response[]
     */
    private static $responseStack = array();

    /**
     * An URL, or an array of elements to generate an URL for HTTP 500 code.
     * @var array|string
     */
    private $forbiddenRedirection = null;

    /**
     * @var \Core_Business_ConfigurationInterface
     */
    protected $configuration;

    /**
     * Instanciate a Router with a set of routes YML files.
     *
     * @throws DevelopmentErrorException If the Router has already been instantiated.
     * @param Container $container The application Container instance
     * @param string $routingFilePattern a regex to indicate routes YML files to include.
     */
    protected function __construct(Container $container, $routingFilePattern)
    {
        if (self::$instantiated !== false) {
            throw new DevelopmentErrorException('You should never instantiate the Router twice in the same process.', get_class($this), 2006);
        }
        try {
            $this->container = $container;
            $this->configuration = $this->container->make('Core_Business_ConfigurationInterface');
            parent::__construct(
                $routingFilePattern,
                $this->configuration->get('_PS_ROOT_DIR_'),
                $this->configuration->get('_PS_CACHE_DIR_'),
                $this->configuration->get('_PS_MODE_DEV_')
            );

            // Register a RouterService in the container, not the Router itself.
            RoutingService::registerRoutingService($this, $this->container);
        
            // EventDispatcher init
            // FIXME: should be a private dispatcher?
            BaseEventDispatcher::initBaseDispatchers($this->container);
            $this->routingDispatcher = $this->container->make('final:EventDispatcher/routing');
            if ($this->triggerCacheGenerationFlag) {
                $this->routingDispatcher->dispatch('cache_generation', new BaseEvent());
            }

            // Exception dispatching handling
            \Core_Foundation_Exception_Exception::setMessageDispatcher($this->container->make('final:EventDispatcher/message'));

            // Restore MessageStack values from persistence
            $this->container->make('MessageStack')->restoreQueues();

            // Translator service init
            $translator = new Translator($this->container->make('CoreBusiness:Context'));
            $this->container->bind(get_class($translator), $translator, true);
            $this->container->bind('TranslatorInterface', $translator, true);
            $this->container->bind('Translator', $translator);
        } catch (\Exception $e) {
            if (php_sapi_name() == "cli") {
                throw $e;
            }
            // At this point every exception is blocking, even WarningException (because MessageStackManager is not yet instantiated).
            $this->tryToDisplayExceptions($e);
            exit(1);
        }
        self::$instantiated = true; // avoid another instance during process (from third part code).
    }

    /**
     * Will find the finally used controller.
     * The result of this function is used to generate a cache.
     *
     * TODO: Support prefixed routes from modules.
     * For now, only Core base controllers are looked for when route is from the Core (means no possible override by a module).
     *
     * @param string $controllerName
     * @throws DevelopmentErrorException If no default controller found in the Core. The routes YML file is incorrect.
     * @return string The controller class name (with right namespace) and the module name (or Core for '/') that responded.
     */
    final protected function getControllerClass($controllerName)
    {
        $className = '\\PrestaShop\\PrestaShop\\Core\\Business\\Controller\\'.$controllerName;
        if (!class_exists($className)) {
            throw new DevelopmentErrorException('Default Controller is not found for: '.$className, null, 2007);
        }

        return array($className, '/');
    }

    /**
     * This method should be overriden to check if Controller is allowed to be executed in this Router instance.
     *
     * @param \ReflectionClass $class
     * @throws DevelopmentErrorException if the given controller violates the Router policy. This will stops execution.
     * @throws \PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException to raise a major exception without stopping execution.
     */
    abstract protected function checkControllerAuthority(\ReflectionClass $class);

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter::doDispatch()
     */
    final protected function doDispatch($controllerName, $controllerMethod, Request $request)
    {
        self::$requestStack['dispatch'] = $request;

        $result =  $this->doCall($controllerName, $controllerMethod, $request, false, true);

        array_pop(self::$requestStack);
        return $result;
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter::doSubcall()
     */
    final protected function doSubcall($controllerName, $controllerMethod, Request $request)
    {
        self::$requestStack['subcall_'.count(self::$requestStack)] = $request;

        // merge query and request subparts from caller's Request
        $dispatchRequest = self::$requestStack['dispatch'];
        $request->query = $dispatchRequest->query;
        $request->request = $dispatchRequest->request;
        // FIXME: maybe more? (files, cookies, headers parameterBags)

        $result = $this->doCall($controllerName, $controllerMethod, $request, true);

        array_pop(self::$requestStack);
        return $result;
    }

    /**
     * This function will call controller and the corresponding action. In this function, all security layers,
     * pre-actions and post-actions, must be called. The function will generate a cache function to be executed quickly.
     *
     * @param string $controllerName The name of the Controller (partial namespace given, to complete)
     * @param string $controllerMethod The name of the function to execute. Must accept parameters: Request &$request, Response &$response
     * @param Request $request
     * @param boolean True to return content instead of sending it through output buffer. False by default.
     * @param boolean True to pin the Response object instance in its $lastRouterResponseInstance singleton. Used by dispatch() only!
     * @throws ResourceNotFoundException if controller action failed (not found)
     * @return string|boolean True for success, false if fail, or the resulting content if $returnView is true.
     */
    final private function doCall($controllerName, $controllerMethod, Request $request, $returnView = false, $pinResponse = false)
    {
        // Find right Controller and check security on it
        try {
            list($controllerClass, $module) = $this->getControllerClass($controllerName);
            $request->attributes->set('_controller_from_module', $module);
        } catch (WarningException $we) {
            // degraded mode, many module overrides canceled.
            $controllerClass = $we->alternative;
            $request->attributes->set('_controller_from_module', '/');
        }
        $class = new \ReflectionClass($controllerClass);
        try {
            $this->checkControllerAuthority($class);
        } catch (WarningException $we) {
            /* Event dispatcher 'message' has already been triggered with event 'warning_message'. */
        }
        $method = $class->getMethod($controllerMethod);

        // backup _controller value for PS Router way of work, and override original value by sf way of work.
        $request->attributes->set('_controller_short', $request->attributes->get('_controller'));
        $request->attributes->set('_controller', $controllerClass.'::'.$controllerMethod);

        // instantiate Controller, check construction integrity
        if (!$this->container->knows($controllerClass)) {
            $this->container->bind($controllerClass, $controllerClass, false, array($this, $this->container));
        }
        $controllerInstance = $this->container->make($controllerClass);
        if (!$controllerInstance->isConstructionStrategyChecked()) {
            throw new DevelopmentErrorException($controllerClass.'::__construct() did not call its parent __construct(). You must call it at first step of the construction.', null, 2008);
        }

        // New response
        $response = new Response();
        self::$responseStack[] = $response;
        $response->setResponseFormat($returnView? AbstractController::RESPONSE_PARTIAL_VIEW : AbstractController::RESPONSE_LAYOUT_HTML);

        // Execution sequence starts here
        $routingDispatcher = $this->routingDispatcher;
        $eventFactory = new EventFactory();

        // init_action dispatch
        $initEvent = $eventFactory->getRoutingEvent($request, $response);
        $routingDispatcher->dispatch('init_action', $initEvent);
        $actionAllowed = !$initEvent->isPropagationStopped();

        // before_action dispatch
        if ($actionAllowed) {
            $beforeEvent = $eventFactory->getRoutingEvent($request, $response);
            $routingDispatcher->dispatch('before_action', $beforeEvent);
            $actionAllowed = $actionAllowed & !$beforeEvent->isPropagationStopped();
        }

        // Resolve controller action method injections
        $resolver = new ControllerResolver(); // Prestashop resolver, not sf!
        $resolver->setContainer($this->container); // inject Container for Controller instantiation.
        $resolver->setRouter($this); // inject Router for Controller instantiation.
        $resolver->setResponse($response); // inject response for its contentData array (scanned for injections)
        $actionArguments = $resolver->getArguments($request, array($controllerInstance, $controllerMethod));

        // ACTION CALL
        if ($actionAllowed) {
            $responseFormat = $method->invokeArgs($controllerInstance, $actionArguments);
            if ($responseFormat) {
                $response->setResponseFormat($responseFormat);
            }
        }

        // after_action dispatch
        if ($actionAllowed) {
            $afterEvent = $eventFactory->getRoutingEvent($request, $response);
            $routingDispatcher->dispatch('after_action', $afterEvent);
            $actionAllowed = $actionAllowed & !$afterEvent->isPropagationStopped();
        }

        // close_action dispatch
        if ($actionAllowed) {
            $closeEvent = $eventFactory->getRoutingEvent($request, $response);
            $routingDispatcher->dispatch('close_action', $closeEvent);
            $actionAllowed = $actionAllowed & !$closeEvent->isPropagationStopped();
        }

        // Format and encapsulate response
        if ($actionAllowed && ($responseFormat = $response->getResponseFormat())) {
            list($encapsulation, $format) = explode('_', $responseFormat);
            if ($format) {
                $controllerInstance->formatResponse($format, $response);
            }
            if ($encapsulation) {
                $controllerInstance->encapsulateResponse($encapsulation, $response);
            }

            if ($returnView) {
                // Do not use send (no output buffer tricks)
                return array_pop(self::$responseStack);
            } else {
                array_pop(self::$responseStack);
                // Send response to output buffer
                $response->send();
            }
        }

        // Forbidden case.
        if (!$actionAllowed) {
            if ($returnView) {
                throw new \Core_Foundation_Exception_Exception('Action forbidden.', 2009);
            }
            $this->redirectToForbidden();
        }
        return true;
    }

    /**
     * Sets the URL/code to use if a forbidden redirection is called through setForbiddenRedirection().
     *
     * @see AbstractRouter::redirect()
     *
     * @param mixed $redirection Integer or String
     */
    public function setForbiddenRedirection($redirection)
    {
        $this->forbiddenRedirection = $redirection;
    }

    /**
     * Call this to redirect to the forbidden page.
     */
    public function redirectToForbidden()
    {
        if ($this->forbiddenRedirection === null) {
            $this->redirect(500);
        }
        $this->redirect($this->forbiddenRedirection); // HTTP code 200 (Login page for example)
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter::generateUrl()
     */
    public function generateUrl($name, array $parameters = array(), $forceLegacyUrl = false, $referenceType = UrlGeneratorInterface::ABSOLUTE_URL)
    {
        if ($forceLegacyUrl == true) {
            // This feature is made in AdminController and FrontController subclasses only.
            throw new DevelopmentErrorException('You cannot ask for legacy URL without overriding the generateUrl() method.', $name, 2010);
        }
        try {
            $urlGenerator = $this->getUrlGenerator();
            if ($referenceType === self::ABSOLUTE_ROUTE) {
                $baseUrl = $urlGenerator->getContext()->getBaseUrl();

                $url = $urlGenerator->generate($name, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH);
                $path = parse_url($url)['path'];

                // remove base URL (for '(/xxx)?/admin-xxx/index.php' if present in the URL)
                if (strlen($baseUrl) > 0 && strpos($path, $baseUrl) === 0) {
                    $path = substr($path, strlen($baseUrl));
                }
                
                return $path;
            }
            return $urlGenerator->generate($name, $parameters, $referenceType);
        } catch (RouteNotFoundException $rnfe) {
            return false;
        }
    }
    

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Router\RouterInterface::getInitialRequest()
     */
    final public static function getInitialRequest()
    {
        if (!isset(self::$requestStack['dispatch'])) {
            return null;
        }
        return self::$requestStack['dispatch'];
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Router\RouterInterface::getCurrentRequest()
     */
    final public function getCurrentRequest()
    {
        // last element in the stack.
        if (count(self::$requestStack) == 0) {
            return null;
        }
        return self::$requestStack[count(self::$requestStack) - 1];
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Router\RouterInterface::getCurrentResponse()
     */
    final public function getCurrentResponse()
    {
        // last element in the stack.
        if (count(self::$responseStack) == 0) {
            return null;
        }
        return self::$responseStack[count(self::$responseStack) - 1];
    }

    /* (non-PHPdoc)
     * @see \PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter::doRedirect()
     */
    final protected function doRedirect($route, array $parameters, $forceLegacyUrl = false, $permanent = false)
    {
        $url = $this->generateUrl($route, $parameters, $forceLegacyUrl, UrlGeneratorInterface::ABSOLUTE_URL);
        $this->routingDispatcher->dispatch('redirection_sent', new BaseEvent($url));
        if ($permanent) {
            header('Status: 301 Moved Permanently', false, 301);
        }
        header('Location: '.$url, true);
        $this->exitNow();
    }

    /**
     * This method is called by PHP process to register a listener when the process is about to shutdown.
     * This is used to have a last chance of operating a fatal error for example.
     * This listener will then dispatch an Event in the 'routing' EventDispatcher, with event name 'shutdown'.
     * If you want to listen to the shutdown event, please use:
     * $container->make('final:EventDispatcher/routing')->addListener('shutdown', <your_listener>).
     *
     * No need to call it by yourself.
     *
     * @param Request $request
     */
    final public function registerShutdownFunctionCallback(Request $request)
    {
        $this->container->make('final:EventDispatcher/routing')->dispatch('shutdown', (new BaseEvent())->setRequest($request));
    }
}
