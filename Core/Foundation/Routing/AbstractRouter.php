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

/**
 * This base Router class is extended for Front and Admin interfaces. The router
 * will cache routes YML files, will scan module directories to add new routes and Controller overrides.
 * The router will find the route, and extended classes will dispatch to the controllers through doDispatch().
 */
abstract class AbstractRouter
{
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
     * @var ConfigCacheFactoryInterface|null
     */
    private $configCacheFactory;

    /**
     * @var string regex
     */
    private $routingFilePattern;
    
    /**
     * @var EventDispatcher
     */
    protected $routingDispatcher;

    private $triggerCacheGenerationFlag = false;

    /**
     * Instanciate a Router with a set of routes YML files.
     *
     * @param string $routingFilePattern a regex to indicate routes YML files to include.
     */
    final public function __construct($routingFilePattern)
    {
        $this->configuration = \Adapter_ServiceLocator::get('Core_Business_ConfigurationInterface');
        $this->cacheFileName = explode('\\', get_class($this));
        $this->cacheFileName = $this->cacheFileName[count($this->cacheFileName)-1];

        // Yml file loaders
        $locator = new FileLocator(array($this->configuration->get('_PS_ROOT_DIR_')));
        $this->routeLoader = new YamlFileLoader($locator);

        // Register routing/settings extensions (modules)
        $this->routingFilePattern = $routingFilePattern;
        $this->registerSettingFiles();
        
        // EventDispatcher init
        EventDispatcher::initDispatchers();
        $this->routingDispatcher = EventDispatcher::getInstance('routing');
        if ($this->triggerCacheGenerationFlag) {
            $this->routingDispatcher->dispatch('cache_generation', new BaseEvent());
        }
    }

    /**
     * Gets the URL Generator instance, already set with routes.
     * Use it to generate HTML links from route names or parameters array.
     *
     * @return \Symfony\Component\Routing\Generator\UrlGeneratorInterface An URL generator with Router routes loaded. NULL if router did never dispatch before.
     */
    final public function getUrlGenerator()
    {
        return (!isset($this->sfRouter))? null : $this->sfRouter->getGenerator();
    }

    /**
     * Dispatcher entry point. Called in entry point files (index.php).
     *
     * @param boolean $noRoutePassThrough Use True to allow dispatch function to return false if no route found. Else, an exception is raised.
     * @throws ResourceNotFoundException if $noRoutePassThrough is set to False and no route is found for the request.
     * @return boolean true if a route is found; false if $noRoutePassThrough is set to true and no route found.
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
            $this->routingFiles[array_keys($this->routingFiles)[0]],
            array('cache_dir' => $this->configuration->get('_PS_CACHE_DIR_').'routing',
                  'debug' => $this->configuration->get('_PS_MODE_DEV_'),
                  'matcher_cache_class' => $this->cacheFileName.'_url_matcher',
            ),
            $requestContext
        );

        // Add modules' routing files
        $this->aggregateRoutingExtensions($this->sfRouter);

        // Resolve route, and call Controller
        try {
            $parameters = $this->sfRouter->match($requestContext->getPathInfo());
            $request->attributes->add($parameters);
            list($controllerName, $controllerMethod) = explode('::', $parameters['_controller']);

            $res = $this->doDispatch($controllerName, $controllerMethod, $request);
            $this->routingDispatcher->dispatch('dispatch'.($res?'_succeed':'_failed'), new BaseEvent('Dispatched on '.$parameters['_controller'].'.'));
            return $res;
        } catch (ResourceNotFoundException $e) {
            $this->routingDispatcher->dispatch('dispatch_failed', new BaseEvent('Failed to resolve route from HTTP request.', $e));
            if ($noRoutePassThrough) {
                // Allow legacy code to handle request if not found in this dispatcher
                return false;
            } else {
                throw $e;
            }
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
     * TODO
     */
    final public function forward(Request &$request, $routeName, $routeParameters = array())
    {
        // TODO: backup $request->attributes-> _controller and others, to _previous_controller and others.

        $requestContext = new RequestContext();
        $requestContext->fromRequest($request);
        
        $subRouter = new \Symfony\Component\Routing\Router(
            $this->routeLoader,
            $this->routingFiles[array_keys($this->routingFiles)[0]],
            array('cache_dir' => $this->configuration->get('_PS_CACHE_DIR_').'routing',
                  'debug' => $this->configuration->get('_PS_MODE_DEV_'),
                  'matcher_cache_class' => $this->cacheFileName.'_url_matcher',
            ),
            $requestContext
        );

        // Add modules' routing files
        $this->aggregateRoutingExtensions($subRouter);

        // Resolve route, and call Controller

        // TODO: redispatch: resolve given route (and not one from requestContext) and then doDispatch on it.
//         $this->routingDispatcher->dispatch('forward_succeed', new BaseEvent());
//         $this->routingDispatcher->dispatch('forward_failed', new BaseEvent());
    }

    /**
     * Dispatcher internal entry point. Called by a controller/action to get a content subpart from another controller.
     *
     * @param Request $subRequest A request made manually (not from real HTTP request) to match the needed subpart.
     * @throws ResourceNotFoundException if no route is found for the request.
     * @return string data/view returned by matching controller (not sent through output buffer)
     */
    final public function subcall(Request &$subRequest, $layoutMode = BaseController::RESPONSE_PARTIAL_VIEW)
    {
        $requestContext = new RequestContext();
        $requestContext->fromRequest($subRequest);
        
        $subRouter = new \Symfony\Component\Routing\Router(
            $this->routeLoader,
            $this->routingFiles[array_keys($this->routingFiles)[0]],
            array('cache_dir' => $this->configuration->get('_PS_CACHE_DIR_').'routing',
                  'debug' => $this->configuration->get('_PS_MODE_DEV_'),
                  'matcher_cache_class' => $this->cacheFileName.'_url_matcher',
            ),
            $requestContext
        );

        // Add modules' routing files
        $this->aggregateRoutingExtensions($subRouter);

        // Resolve route, and call Controller
        $parameters = $subRouter->match($requestContext->getPathInfo());

        $subRequest->attributes->add($parameters);
        $subRequest->headers->set('layout-mode', $layoutMode); // this param is prior to 'accept' HTTP data.

        list($controllerName, $controllerMethod) = explode('::', $parameters['_controller']);

        $res = $this->doSubcall($controllerName, $controllerMethod, $subRequest);
        $this->routingDispatcher->dispatch('subcall'.($res?'_succeed':'_failed'), new BaseEvent('Subcall done on '.$parameters['_controller'].'.'));
        return $res;
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
     * The redirect call take several values in parameter:
     * - Integer value to return a specific HTTP return code and its default message (500, 404, etc...),
     * - String to indicate the URL to redirect to,
     * - Array to indicate a route to generate a URL: [route name; [parameters]]
     *
     * If the redirect succeed, it does not return because an exit is done after redirection sent.
     *
     * If the argument is an array containing a route and its parameters, then the method could
     * throws exceptions due to URL generation errors (@see PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException::generate())
     *
     * @throws \Core_Foundation_Exception_Exception if argument is not properly set.
     *
     * @param mixed $to Integer, String or Array. See description for specific array format.
     * @return false if headers already sent (cannot redirect, it's too late).
     */
    final public function redirect($to)
    {
        if (headers_sent() !== false) {
            $this->routingDispatcher->dispatch('redirection_failed', new BaseEvent('Too late to redirect: headers already sent.'));
            return false; // headers already sent
        }
        if (is_string($to)) {
            $this->routingDispatcher->dispatch('redirection_sent', new BaseEvent($to));
            header('Location: '.$to, true);
            exit;
        }
        if (is_int($to)) {
            $this->routingDispatcher->dispatch('redirection_sent', new BaseEvent($to));
            http_response_code($to);
            exit;
            // TODO: default error page for this code. Howto ?
        }
        if (is_array($to) && count($to) == 2 && is_array($to[1])) {
            $route = $to[0];
            $parameters = $to[1];
            $url = $this->getUrlGenerator()->generate($route, $parameters);
            $this->routingDispatcher->dispatch('redirection_sent', new BaseEvent($url));
            header('Location: '.$url, true);
            exit;
        }
        
        $e = new \Core_Foundation_Exception_Exception('Bad parameters format given to redirect().');
        $this->routingDispatcher->dispatch('redirection_failed', new BaseEvent($to, $e));
        throw $e;
    }

    final private function registerSettingFiles()
    {
        $triggerCacheGenerationFlag = &$this->triggerCacheGenerationFlag;
        $cache = $this->getConfigCacheFactory()->cache(
            $this->configuration->get('_PS_CACHE_DIR_').'routing/'.$this->cacheFileName.'_setting_list.php',
            function (ConfigCacheInterface $cache) use (&$triggerCacheGenerationFlag) {
                $moduleCoreConfigExists = (count(glob($this->configuration->get('_PS_MODULE_DIR_').'*/CoreConfig/')) > 0);
                $routingFiles = $routingFilePaths = array();

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
                    $routingFilePaths[$prefix.$suffix] = $path;
                }

                // test if at least one module will brings a setup file before to include path into search.
                if ($moduleCoreConfigExists) {
                    $routingFilesFinder = Finder::create()->files()->name('&'.$this->routingFilePattern.'&')->sortByName()->followLinks()
                            ->in($this->configuration->get('_PS_MODULE_DIR_').'*/CoreConfig/');
                    foreach ($routingFilesFinder as $file) {
                        $path = $file->getRealpath();
                        $matches = array();
                        if (1 === preg_match('&'.$this->configuration->get('_PS_MODULE_DIR_').'([^/]+)/CoreConfig/'.$this->routingFilePattern.'$&i', $path, $matches) &&
                            isset($matches[1])) {
                            $prefix = '/'.$matches[1];
                            $suffix = isset($matches[2]) ? $matches[2] : '';
                            $routingFiles[] = '\''.addslashes($prefix.$suffix).'\' => \''.addslashes($path).'\'';
                            $routingFilePaths[$prefix.$suffix] = $path;
                        }
                    }
                }

                // search for controller override namespaces
                $settingsFilesFinder = Finder::create()->files()->name('settings.yml')->sortByName()->followLinks();
                if ($moduleCoreConfigExists) {
                    $settingsFilesFinder->in($this->configuration->get('_PS_MODULE_DIR_').'*/CoreConfig/'); // first Modules (for override priority)
                }
                $settingsFilesFinder->in($this->configuration->get('_PS_ROOT_DIR_').'/CoreConfig/'); // then default Core routes
                $namespaces = array();

                // Check for error cases
                $routeIds = array();
                foreach ($routingFilePaths as $prefix => $path) {
                    $content = Yaml::parse(file_get_contents($path));
                    $ids = array_keys($content);
                    foreach ($ids as $id) {
                        if (in_array($id, $routeIds)) {
                            throw new \ErrorException('A modules\' route identifier is duplicated. Route IDs must be Unique (route ID: '.$id.', prefix: '.$prefix.')');
                        }
                        $routeIds[] = $id;
                    }
                }
                foreach ($settingsFilesFinder as $file) {
                    try {
                        $settings = Yaml::parse(file_get_contents($file->getRealpath()));
                        if (isset($settings['controllers']) && isset($settings['controllers']['override_namespace'])) {
                            $namespace = $settings['controllers']['override_namespace'];
                            $namespaces[] = '\''.addslashes($namespace).'\'';
                        }
                    } catch (\Exception $e) {
                        throw new \ErrorException('The following settings file is not well structured: '.$file->getRealPath(), $e->getCode());
                    }
                }

                // generate cache
                $phpCode = '<'.'?php
$this->routingFiles = array('.implode(', ', array_reverse($routingFiles)).');
$this->controllerNamespaces = array('.implode(', ', $namespaces).');
'; // Raw php code inside a string, do not indent please.
                $cache->write($phpCode);
                $triggerCacheGenerationFlag = true;
            }
        );

        include $cache->getPath();
    }

    final protected function getConfigCacheFactory($forceDebug = false)
    {
        if (null === $this->configCacheFactory) {
            $this->configCacheFactory = new ConfigCacheFactory($forceDebug || $this->configuration->get('_PS_MODE_DEV_'));
        }

        return $this->configCacheFactory;
    }

    final private function aggregateRoutingExtensions($sfRouter)
    {
        foreach ($this->routingFiles as $prefix => $routingFile) {
            $collection = $this->routeLoader->load($routingFile);
            $collection->addPrefix($prefix);
            $sfRouter->getRouteCollection()->addCollection($collection);
        }
    }

    final public function registerShutdownFunctionCallback(Request &$request)
    {
        $null = null;
        EventDispatcher::getInstance('routing')->dispatch('shutdown', (new BaseEvent())->setRequest($request));
    }
}
