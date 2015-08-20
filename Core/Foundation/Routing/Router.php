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

/**
 * This base Router class is extended for Front and Admin interfaces. The router
 * will cache routes YML files, will scan module directories to add new routes and Controller overrides.
 * The router will find the route, and extended classes will dispatch to the controllers through doDispatch().
 */
abstract class Router
{
    /**
     * @var \Core_Business_ConfigurationInterface
     */
    private $configuration;

    /**
     * @var string
     */
    private $cacheFileName;

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
    private $controllerNamespaces;

    /**
     * @var ConfigCacheFactoryInterface|null
     */
    private $configCacheFactory;

    /**
     * @var string regex
     */
    private $routingFilePattern;
    

    /**
     * Instanciate a Router with a set of routes YML files.
     *
     * @param string $routingFilePattern a regex to indicate routes YML files to include.
     */
    public final function __construct($routingFilePattern)
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
    }

    /**
     * Dispatcher entry point. Called in entry point files (index.php).
     *
     * @param boolean $noRoutePassThrough Use True to allow dispatch function to return false if no route found. Else, an exception is raised.
     * @throws ResourceNotFoundException if $noRoutePassThrough is set to False and no route is found for the request.
     * @return boolean true if a route is found; false if $noRoutePassThrough is set to true and no route found.
     */
    public final function dispatch($noRoutePassThrough = false)
    {
        // Request & void response
        $request = Request::createFromGlobals();
        $requestContext = new RequestContext();
        $requestContext->fromRequest($request);

        // Instantiate Sf Router
        $this->sfRouter = new \Symfony\Component\Routing\Router(
            $this->routeLoader,
            $this->routingFiles['/'],
            array('cache_dir' => $this->configuration->get('_PS_CACHE_DIR_').'routing',
                  'debug' => $this->configuration->get('_PS_MODE_DEV_'),
                  'matcher_cache_class' => $this->cacheFileName.'_url_matcher',
            ),
            $requestContext
        );

        // Add modules' routing files
        $this->aggregateRoutingExtensions();

        // Resolve route, and call Controller
        try {
            $parameters = $this->sfRouter->match($requestContext->getPathInfo());
            $request->attributes->add($parameters);
            list($controllerName, $controllerMethod) = explode('::', $parameters['_controller']);

            return $this->doDispatch($controllerName, $controllerMethod, $request);
        } catch (ResourceNotFoundException $e) {
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
    public final function forward()
    {
        // TODO
    }

    private final function registerSettingFiles()
    {
        $cache = $this->getConfigCacheFactory()->cache(
            $this->configuration->get('_PS_CACHE_DIR_').'routing/'.$this->cacheFileName.'_setting_list.php',
            function (ConfigCacheInterface $cache) {
                // search for routes.yml files
                $routingFilesFinder = Finder::create()->files()->name('&'.$this->routingFilePattern.'&')->sortByName()->followLinks()
                    ->in($this->configuration->get('_PS_MODULE_DIR_').'*/CoreConfig/') // modules routes first (but will be prefixed)
                    ->in($this->configuration->get('_PS_ROOT_DIR_').'/CoreConfig/'); // then default Core routes
                foreach($routingFilesFinder as $file) {
                    $path = $file->getRealpath();
                    $matches = array();
                    if (1 === preg_match('&modules/([^/]+)/CoreConfig/'.$this->routingFilePattern.'$&i', $path, $matches) && isset($matches[1])) {
                        $prefix = '/'.$matches[1];
                        $suffix = isset($matches[2]) ? $matches[2] : '';
                    } else {
                        $matches = array();
                        $prefix = '/';
                        $suffix = '';
                        if (1 === preg_match('&/CoreConfig/'.$this->routingFilePattern.'$&i', $path, $matches) && isset($matches[2])) {
                            $suffix = $matches[2];
                        }
                    }
                    $routingFiles[] = '\''.addslashes($prefix.$suffix).'\' => \''.addslashes($path).'\'';
                }
                
                // search for controller override namespaces
                $settingsFilesFinder = Finder::create()->files()->name('settings.yml')->sortByName()->followLinks()
                    ->in($this->configuration->get('_PS_MODULE_DIR_').'*/CoreConfig/') // first Modules (for override)
                    ->in($this->configuration->get('_PS_ROOT_DIR_').'/CoreConfig/'); // then Core default
                foreach($settingsFilesFinder as $file) {
                    try {
                        $settings = Yaml::parse(file_get_contents($file->getRealpath()));
                        if (isset($settings['controllers']) && isset($settings['controllers']['override_namespace'])) {
                            $namespace = $settings['controllers']['override_namespace'];
                            $namespaces[] = '\''.addslashes($namespace).'\'';
                        }
                    } catch (\Exception $e) {
                        // TODO : log, parse error
                    }
                }

                // generate cache
                $phpCode = '<'.'?php
$this->routingFiles = array('.implode(', ', $routingFiles).');
$this->controllerNamespaces = array('.implode(', ', $namespaces).');
'; // Raw php code inside a string, do not indent please.
                $cache->write($phpCode); // FIXME : add cache freshness option, how ?
            }
        );

        include $cache->getPath();
    }

    private final function getConfigCacheFactory()
    {
        if (null === $this->configCacheFactory) {
            $this->configCacheFactory = new ConfigCacheFactory($this->configuration->get('_PS_MODE_DEV_'));
        }

        return $this->configCacheFactory;
    }

    private final function aggregateRoutingExtensions()
    {
        foreach($this->routingFiles as $prefix => $routingFile) {
            if ($prefix == '/') {
                continue;
            }
            $collection = $this->routeLoader->load($routingFile);
            $collection->addPrefix($prefix);
            $this->sfRouter->getRouteCollection()->addCollection($collection);
        }
    }

    /**
     * Will scan modules to find an override of the Core controller.
     * If not, use the Core controller (most of the cases).
     * If more than one controller found, the conflict is rejected, and the default Core controller is used.
     *
     * @param string $controllerName
     * @throws \ErrorException If no default controller found in the Core. The routes YML file is incorrect.
     * @return BaseController The controller instance
     */
    protected final function instantiateController($controllerName)
    {
        $foundOverrides = array();
        foreach($this->controllerNamespaces as $namespace) {
            $className = '\\'.$namespace.'\\'.$controllerName;
            if (!class_exists($className)) {
                continue;
            }
            $foundOverrides[] = $className;
        }

        // One override found, use it.
        if (count($foundOverrides) === 1) {
            $class = new \ReflectionClass($foundOverrides[0]);
            $controller = $class->newInstance();
            return $controller;
        }

        // More overrides found: problem! do not use it but Warn!
        if (count($foundOverrides) > 1) {
            // TODO : faire Warning/log, avec le detail: On a plus de 2 overrides qui se battent, donc on fallback sur le controller du Core avec le warning.
        }

        // fallback on default Core controller (most of the time).
        $className = '\\PrestaShop\\PrestaShop\\Core\\Business\\Controller\\'.$controllerName;
        if (!class_exists($className)) {
            throw new \ErrorException('Default Controller is not found for: '.$className);
        }
        $class = new \ReflectionClass($className);
        $controller = $class->newInstance();
        return $controller;
    }

}
