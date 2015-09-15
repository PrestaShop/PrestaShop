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
use Symfony\Component\Filesystem\Filesystem;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;
use Symfony\Component\Routing\Router;
use PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException;
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;
use PrestaShop\PrestaShop\Core\Foundation\Log\MessageStackManager;
use PrestaShop\PrestaShop\Core\Foundation\View\ViewFactory;
use PrestaShop\PrestaShop\Core\Foundation\Exception\ErrorException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * This base Router class is extended for Front and Admin interfaces. The router
 * will cache routes YML files, will scan module directories to add new routes and Controller overrides.
 * The router will find the route, and extended classes will dispatch to the controllers through doDispatch().
 */
abstract class AbstractRouter
{
    /**
     * Used for generateUrl() calls.
     * Generates an absolute path, including route prefixes, but without the part preceding the route path.
     *
     * @see UrlGenerator::ABSOLUTE_PATH
     */
    const ABSOLUTE_ROUTE = 'absolute_route';

    /**
     * @var \Core_Foundation_IoC_Container
     */
    protected $container;

    /**
     * @var \Core_Business_ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var string
     */
    protected $cacheFileName;

    /**
     * @var YamlFileLoader
     */
    private $routeLoader;

    /**
     * @var \Symfony\Component\Routing\Router
     */
    private $sfRouter;

    /**
     * @var array[string]
     */
    private $routingFiles;

    /**
     * @var array[string]
     */
    protected $controllerNamespaces;

    /**
     * @var array[string]
     */
    protected $moduleRouteMapping;

    /**
     * @var ConfigCacheFactoryInterface|null
     */
    private $configCacheFactory;

    /**
     * @var string regex
     */
    private $routingFilePattern;

    /**
     * @var boolean
     */
    private $isSubcalling = false;

    /**
     * @var EventDispatcher
     */
    protected $routingDispatcher;

    protected $triggerCacheGenerationFlag = false;

    /**
     * Instanciate a Router with a set of routes YML files.
     *
     * @param string $routingFilePattern a regex to indicate routes YML files to include.
     */
    protected function __construct($routingFilePattern)
    {
        $this->configuration = $this->container->make('Core_Business_ConfigurationInterface');
        $this->cacheFileName = explode('\\', get_class($this));
        $this->cacheFileName = $this->cacheFileName[count($this->cacheFileName)-1];

        // Yml file loaders
        $locator = new FileLocator(array($this->configuration->get('_PS_ROOT_DIR_')));
        $this->routeLoader = new YamlFileLoader($locator);

        // Register routing/settings extensions (modules)
        $this->routingFilePattern = $routingFilePattern;
        $this->registerSettingFiles();
        
        // Register a RouterService in the container
        RoutingService::registerRoutingService($this, $this->container);
    }

    /**
     * Gets the URL Generator instance, already set with routes.
     * Use it to generate HTML links from route names or parameters array.
     *
     * @return \Symfony\Component\Routing\Generator\UrlGeneratorInterface An URL generator with Router routes loaded. NULL if router did never dispatch before.
     */
    final protected function getUrlGenerator()
    {
        return (!isset($this->sfRouter))? null : $this->sfRouter->getGenerator();
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
    abstract public function generateUrl($name, $parameters = array(), $forceLegacyUrl = false, $referenceType = UrlGeneratorInterface::ABSOLUTE_URL);

    /**
     * Dispatcher entry point. Called in entry point files (index.php).
     *
     * @param boolean $noRoutePassThrough Use True to allow dispatch function to return false if no route found. Else, an exception is raised.
     * @throws ResourceNotFoundException if $noRoutePassThrough is set to False and no route is found for the request.
     * @return boolean false if $noRoutePassThrough is set to true and no route found. Does not returns if action finished successfully (blocked by exit;)
     */
    final public function dispatch($noRoutePassThrough = false)
    {
        // Request
        $request = Request::createFromGlobals();
        $requestContext = new RequestContext();
        $requestContext->fromRequest($request);

        // shutdown registration, to ensure event dispatching
        $that =& $this; // pass by ref!
        register_shutdown_function(array($that, 'registerShutdownFunctionCallback'), $request);

        // Instantiate Sf Router
        $this->sfRouter = new \Symfony\Component\Routing\Router(
            $this->routeLoader,
            (array_key_exists('/', $this->routingFiles)) ? $this->routingFiles['/'] : $this->routingFiles[array_keys($this->routingFiles)[0]],
            array('cache_dir' => $this->configuration->get('_PS_CACHE_DIR_').'routing',
                  'debug' => $this->configuration->get('_PS_MODE_DEV_'),
                  'matcher_cache_class' => $this->cacheFileName.'_url_matcher',
            ),
            $requestContext
        );

        // Add multiple routing files (prefixed or not)
        $this->aggregateRoutingExtensions($this->sfRouter);

        try {
            try {
                // Resolve route
                $parameters = $this->sfRouter->match($requestContext->getPathInfo());
                $request->attributes->add($parameters);
    
                // Call Controller/Action
                list($controllerName, $controllerMethod) = explode('::', $parameters['_controller']);
                $res = $this->doDispatch($controllerName, $controllerMethod, $request);
                $this->routingDispatcher->dispatch('dispatch'.($res?'_succeed':'_failed'), new BaseEvent('Dispatched on '.$parameters['_controller'].'.'));
                $this->exitNow();
            } catch (ResourceNotFoundException $e) {
                $this->routingDispatcher->dispatch('dispatch_failed', new BaseEvent('Failed to resolve route from HTTP request.', $e));
                if ($noRoutePassThrough) {
                    // Allow legacy code to handle request if not found in this dispatcher
                    return false;
                } else {
                    throw $e;
                }
            }
        } catch (\Exception $e) {
            if (php_sapi_name() == "cli") {
                throw $e;
            }
            $this->tryToDisplayExceptions($e);
            return true; // do not bypass now!
        }
    }

    /**
     * This function will call controller and the corresponding action. In this function, all security layers,
     * pre-actions and post-actions, must be called.
     *
     * @param string $controllerName The name of the Controller (partial namespace given, instantiateController() will complete with the first part)
     * @param string $controllerMethod The name of the function to execute. Must accept parameters: Request &$request, Response &$response
     * @param Request $request
     * @throws ResourceNotFoundException if controller action failed (not found)
     * @return boolean True for success, false if the router should pass through for the next Router (legacy Dispatcher).
     */
    abstract protected function doDispatch($controllerName, $controllerMethod, Request &$request);

    /**
     * This method will forward the Router into another Controller/action without any redirection instruction to the browser.
     * The browser will then receive response from a different action with no URL change.
     * Used for example after a POST succeed, and we want to execute another action to display another content.
     *
     * @param Request $oldRequest The request of the action that called this method.
     * @param string $routeName The new route name to forward to.
     * @param array $routeParameters The parameters to override the route defaults parameters
     * @throws DevelopmentErrorException if the forward is made from a subcall action.
     * @return boolean true if a route is found; false if $noRoutePassThrough is set to true and no route found.
     */
    final public function forward(Request &$oldRequest, $routeName, $routeParameters = array())
    {
        if (!$oldRequest || !$this->canForward()) {
            throw new DevelopmentErrorException('You cannot make a forward into a subcall!', null, 1005);
        }

        $attributes =& $oldRequest->attributes;
        $attributes->set('_previous_controller', $attributes->get('_controller'));
        $attributes->set('_previous_route_from_module', $attributes->get('_route_from_module'));
        $attributes->add($routeParameters); // FIXME: needed here?
        $path = $this->generateUrl($routeName, $routeParameters, false, self::ABSOLUTE_ROUTE);
        try {
            // Resolve route
            $parameters = $this->sfRouter->match($path);
            $oldRequest->attributes->add($parameters);

            // Call Controller/Action
            list($controllerName, $controllerMethod) = explode('::', $parameters['_controller']);
            $res = $this->doDispatch($controllerName, $controllerMethod, $oldRequest);
            $this->routingDispatcher->dispatch('forward'.($res?'_succeed':'_failed'), new BaseEvent('Forwarded on '.$parameters['_controller'].'.'));
            $this->exitNow();
        } catch (ResourceNotFoundException $e) {
            $this->routingDispatcher->dispatch('forward_failed', new BaseEvent('Failed to resolve route from forward request.', $e));
            throw new DevelopmentErrorException('A forward failed due to unresolved route.', $oldRequest, 1002, $e);
        }
    }

    /**
     * Check if a forward is possible at the moment.
     * For now, this function checks if a sucball is in progress. If it's the case, then the forward is not possible.
     *
     * @return boolean
     */
    public function canForward()
    {
        return !$this->isSubcalling;
    }

    /**
     * Dispatcher internal entry point. Called by a controller/action to get a content subpart from another controller.
     *
     * @param string $routeName The route unique name/ID
     * @param array $routeParameters The route's parameters (mandatory, and optional, and even more if needed)
     * @throws ResourceNotFoundException if no route is found for the request.
     * @return string data/view returned by matching controller (not sent through output buffer)
     */
    final public function subcall($routeName, $routeParameters = array(), $layoutMode = BaseController::RESPONSE_PARTIAL_VIEW)
    {
        $this->isSubcalling = true;
        $subRequest = new Request();
        $path = $this->generateUrl($routeName, $routeParameters, false, self::ABSOLUTE_ROUTE);

        try {
            // Resolve route
            $parameters = $this->sfRouter->match($path);
            $subRequest->attributes->add($parameters);

            // Override layout mode for subcall
            $subRequest->headers->set('layout-mode', $layoutMode); // this param is prior to 'accept' HTTP data.

            // Call Controller/Action
            list($controllerName, $controllerMethod) = explode('::', $parameters['_controller']);
            $res = $this->doSubcall($controllerName, $controllerMethod, $subRequest);
            $this->routingDispatcher->dispatch('subcall'.($res?'_succeed':'_failed'), new BaseEvent('Subcall done on '.$parameters['_controller'].'.'));
            $this->isSubcalling = false;
            return $res;
        } catch (ResourceNotFoundException $e) {
            $this->routingDispatcher->dispatch('subcall_failed', new BaseEvent('Failed to resolve route from subcall request.', $e));
            $this->isSubcalling = false;
            throw new DevelopmentErrorException('A subcall failed due to unresolved route.', $oldRequest, 1002, $e);
        }
    }

    /**
     * This function will call controller and the corresponding action. In this function, all security layers,
     * pre-actions and post-actions, must be called.
     *
     * @param string $controllerName The name of the Controller (partial namespace given, instantiateController() will complete with the first part)
     * @param string $controllerMethod The name of the function to execute. Must accept parameters: Request &$request, Response &$response
     * @param Request $request
     * @throws ResourceNotFoundException if controller action failed (not found)
     * @return string Data/view returned by matching controller (not sent through output buffer).
     */
    abstract protected function doSubcall($controllerName, $controllerMethod, Request &$request);

    /**
     * The redirect call take a route and its parameters, to send a redirection header to the browser.
     * If the redirect succeed, it does not return because an exit is done after redirection is sent.
     *
     * @param mixed $to Integer, String or Array. See description for specific array format.
     * @param boolean $permanent True to send 'Permanently moved' header code. False to send 'Temporary redirection' header code. Only if $to is not a return code.
     * @throws DevelopmentErrorException if the redirect is made from a subcall action.
     * @return false if headers already sent (cannot redirect, it's too late). Does not returns if redirect succeed.
     */
    final public function redirectToRoute(Request &$oldRequest, $routeName, $routeParameters, $forceLegacyUrl = false, $permanent = false)
    {
        if (!$oldRequest || !$this->canRedirect()) {
            throw new DevelopmentErrorException('You cannot make a redirection into a subcall!', null, 1004);
        }

        if (headers_sent() !== false) {
            $this->routingDispatcher->dispatch('redirection_failed', new BaseEvent('Too late to redirect: headers already sent.'));
            return false; // headers already sent
        }

        $this->doRedirect($routeName, $routeParameters, $forceLegacyUrl, $permanent);
        $this->exitNow();
    }

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
    final public function redirect($to, $permanent = false)
    {
        if (!$this->canRedirect()) {
            throw new DevelopmentErrorException('You cannot make a redirection into a subcall!', null, 1004);
        }
        if (headers_sent() !== false) {
            $this->routingDispatcher->dispatch('redirection_failed', new BaseEvent('Too late to redirect: headers already sent.'));
            return false; // headers already sent
        }
        if (is_string($to)) {
            $this->routingDispatcher->dispatch('redirection_sent', new BaseEvent($to));
            if ($permanent) {
                header('Status: 301 Moved Permanently', false, 301);
            }
            header('Location: '.$to, true);
            $this->exitNow();
        }
        if (is_int($to)) {
            $this->routingDispatcher->dispatch('redirection_sent', new BaseEvent($to));
            http_response_code($to);
            $this->exitNow();
            // TODO: default error page for this code. Howto ? et un cas specific 500 ?
        }

        $e = new DevelopmentErrorException('Bad parameters format given to redirect().');
        $this->routingDispatcher->dispatch('redirection_failed', new BaseEvent($to, $e));
        throw $e;
    }

    /**
     * Check if a redirect is possible at the moment.
     * For now, this function checks if a sucball is in progress. If it's the case, then the redirect is not possible.
     *
     * @return boolean
     */
    public function canRedirect()
    {
        return !$this->isSubcalling;
    }

    /**
     * This function will generate a URL and will send a redirection to it to the browser.
     *
     * @param string $route The route name
     * @param array $parameters The route parameters
     * @param boolean $forceLegacyUrl True to use alternative URL to reach legacy dispatcher.
     * @param boolean $permanent True to send 'Permanently moved' header code. False to send 'Temporary redirection' header code.
     */
    abstract protected function doRedirect($route, $parameters, $forceLegacyUrl = false, $permanent = false);

    final private function registerSettingFiles()
    {
        $triggerCacheGenerationFlag = &$this->triggerCacheGenerationFlag;
        $cache = $this->getConfigCacheFactory()->cache(
            $this->configuration->get('_PS_CACHE_DIR_').'routing/'.$this->cacheFileName.'_setting_list.php',
            function (ConfigCacheInterface $cache) use (&$triggerCacheGenerationFlag) {
                $routingFiles = $routingFilePaths = $routeIds = array();

                // search for Core routes.yml files (base routes.yml is the first, then Core's others)
                $routingFilesFinder = Finder::create()->files()->name('&'.$this->routingFilePattern.'&')->sortByName()->followLinks()
                        ->in($this->configuration->get('_PS_ROOT_DIR_').'/CoreConfig/');
                foreach ($routingFilesFinder as $file) {
                    $path = $file->getRealpath();
                    $matches = array();
                    $prefix = '/';
                    $suffix = '';
                    if (1 === preg_match('&/CoreConfig/'.$this->routingFilePattern.'$&i', $path, $matches) && isset($matches[2])) {
                        $suffix = $matches[2];
                    }
                    $routingFiles[] = '\''.addslashes($prefix.$suffix).'\' => \''.addslashes($path).'\'';
                    $routingFilePaths['/'][$prefix.$suffix] = $path;
                }

                // generate cache
                $phpCode = '<'.'?php
$this->routingFiles = array('.implode(', ', array_reverse($routingFiles)).');
'; // Raw php code inside a string, do not indent please.
                $cache->write($phpCode);
                $triggerCacheGenerationFlag = true;
            }
        );

        include $cache->getPath();
    }

    /**
     * Gets the cache factory to build cache files. Used internally by the Router and its subclasses.
     *
     * @param boolean $forceDebug True to force debug mode (do not keep generated cache for next request).
     * @return ConfigCacheFactoryInterface
     */
    final protected function getConfigCacheFactory($forceDebug = false)
    {
        if (null === $this->configCacheFactory) {
            $this->configCacheFactory = new ConfigCacheFactory($forceDebug || $this->configuration->get('_PS_MODE_DEV_'));
        }
        return $this->configCacheFactory;
    }

    final private function aggregateRoutingExtensions(Router &$sfRouter)
    {
        foreach ($this->routingFiles as $prefix => $routingFile) {
            $collection = $this->routeLoader->load($routingFile);
            $collection->addPrefix($prefix);
            $sfRouter->getRouteCollection()->addCollection($collection);
        }
    }

    /**
     * Get the route parameters from all YAML files (Core and modules).
     *
     * @param string $route The route unique name/ID to retrieve
     * @return \Symfony\Component\Routing\Route The found Route object, or null if not found.
     */
    final public function getRouteParameters($route)
    {
        return $this->sfRouter->getRouteCollection()->get($route);
    }

    /**
     * This method is called at the end of a dispatch() call, if an exception is thrown and not catched.
     * The result of this method is not guaranteed. If it fails, the input exception is just thrown again.
     *
     * @param \Exception $lastException The last exception that stopped the process.
     * @throws \Exception Transmit the input exception in case of failure.
     */
    final protected function tryToDisplayExceptions(\Exception $lastException)
    {
        try {
            $messageStackManager = MessageStackManager::getInstance();
            $viewEngine = null;
            $messages = '';

            if ($messageStackManager->getErrorIterator() && $messageStackManager->getErrorIterator()->count()) {
                if ($viewEngine == null) {
                    $viewEngine = new ViewFactory($this->container, 'smarty');
                }
                $messages .= $viewEngine->view->fetch('Core/system_messages.tpl', array(
                    'exceptions' => $messageStackManager->getErrorIterator(),
                    'color' => 'red'
                ));
            }

            if ($messageStackManager->getWarningIterator() && $messageStackManager->getWarningIterator()->count()) {
                if ($viewEngine == null) {
                    $viewEngine = new ViewFactory($this->container, 'smarty');
                }
                $messages .= $viewEngine->view->fetch('Core/system_messages.tpl', array(
                    'exceptions' => $messageStackManager->getWarningIterator(),
                    'color' => 'orange'
                ));
            }

            // Well, in this case we just have to display $lastException
            if ($messages == '') {
                if ($viewEngine == null) {
                    $viewEngine = new ViewFactory($this->container, 'smarty');
                }
                $messages .= $viewEngine->view->fetch('Core/system_messages.tpl', array(
                    'exceptions' => array($lastException),
                    'color' => 'red'
                ));
            }

            echo '<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script> '.$messages;
            $this->exitNow(1);
        } catch (\Exception $e) {
            // Failure. Don't need $e (templating failure), but should throws $lastException to display anyway.
            throw $lastException;
        }
    }

    /**
     * This method is called by PHP process to register a listener when the process is about to shutdown.
     * This is used to have a last chance of operating a fatal error for example.
     * No need to call it by yourself.
     *
     * @param Request $request
     */
    abstract public function registerShutdownFunctionCallback(Request &$request);

    /**
     * Wrapper method to exit process. This will do an exit($i) except
     * if you override the method. For testing environment.
     *
     * @param number $i
     */
    protected function exitNow($i = 0)
    {
        exit($i);
    }
}
