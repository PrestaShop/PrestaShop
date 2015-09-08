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
use PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter;
use PrestaShop\PrestaShop\Core\Foundation\Routing\ModuleRouterOverrideException;
use PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PrestaShop\PrestaShop\Core\Foundation\Exception\ErrorException;

abstract class Router extends AbstractRouter
{
    /**
     * @var Request
     */
    private static $lastRouterRequestInstance = null;

    public static function getLastRouterRequestInstance()
    {
        return self::$lastRouterRequestInstance;
    }

    /**
     * An URL, or an array of elements to generate an URL for HTTP 500 code.
     * @var array|string
     */
    private $forbiddenRedirection = null;

    /**
     * Because getTraits() from ReflectionClass does not return traits from ancestors classes, we must use recursive scan.
     *
     * @param \ReflectionClass $class The class to scan
     * @return array:ReflectionClass All traits from the givben class, even from the ancestors classes.
     */
    final private function getAllTraits(\ReflectionClass $class)
    {
        $traits = array();
        
        $classes = array($class);
        $parent = $class;
        while ($parent = $parent->getParentClass()) {
            $classes[] = $parent;
        }

        foreach (array_reverse($classes) as $c) {
            foreach ($c->getTraits() as $trait) {
                $traits[$trait->name] = $trait; // unicity check :) Children prior
            }
        }
        return array_values($traits);
    }

    /**
     * Check and filter trait methods. The result will be put in cache.
     *
     * @param array:\ReflectioClass $allTraits The traits used in the controller
     * @param string $startsWith Filter methods names that starts with this parameter.
     * @throws DevelopmentErrorException If a method is not compliant (parameters)
     * @return array:string Methods names that matches the filter and belongs to the controller.
     */
    final private function filterTraits($allTraits, $startsWith)
    {
        $traitFunctions = array();
        foreach ($allTraits as $trait) {
            $methods = $trait->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                $parameters = $method->getParameters();
                if (count($parameters) < 2) {
                    throw new DevelopmentErrorException('A trait method should always accept at least 2 parameters (&$request, &$response). The Trait method '
                        .$method->name.' wants '.$method->getNumberOfParameters());
                }
                if (!$parameters[0]->isPassedByReference() || !$parameters[1]->isPassedByReference()) {
                    throw new DevelopmentErrorException('A trait method should always accept both first parameters by reference only (&$request, &$response).');
                }
                for ($i = 2; $i < count($parameters); $i++) {
                    if (!$parameters[$i]->isDefaultValueAvailable()) {
                        throw new DevelopmentErrorException('A trait method can accept more than 2 parameters for specific cases, but always optional & with default values.');
                    }
                }
                if (strpos($method->name, $startsWith) === 0) {
                    $traitFunctions[] = $method->name;
                }
            }
        }
        return $traitFunctions;
    }

    /**
     * Will scan modules to find an override of the Core controller.
     * If not, use the Core controller (most of the cases).
     * If more than one controller found, the conflict is rejected, and the default Core controller is used.
     * The result of this function is used to generate a cache.
     *
     * @param string $controllerName
     * @throws DevelopmentErrorException If no default controller found in the Core. The routes YML file is incorrect.
     * @throws ModuleRouterOverrideException IF more than one module want to override the same Controller.
     * @return string The controller class name (with right namespace)
     */
    final protected function getControllerClass($controllerName)
    {
        $foundModules = array();
        $foundOverrides = array();
        foreach ($this->controllerNamespaces as $module => $namespace) {
            $className = '\\'.$namespace.'\\'.$controllerName;
            if (!class_exists($className)) {
                continue;
            }
            $foundOverrides[] = $className;
            $foundModules[] = $module;
        }

        // One override found, use it.
        if (count($foundOverrides) === 1) {
            return array($foundOverrides[0], $foundModules[0]);
        }

        // fallback on default Core controller (most of the time).
        $className = '\\PrestaShop\\PrestaShop\\Core\\Business\\Controller\\'.$controllerName;
        if (!class_exists($className)) {
            throw new DevelopmentErrorException('Default Controller is not found for: '.$className);
        }

        // More overrides found: problem! do not use it but Warn!
        if (count($foundOverrides) > 1) {
            throw new ModuleRouterOverrideException(
                'More than one module want to override '.$controllerName.'. You must uninstall one of them: '.implode(', ', $foundModules),
                $className,
                array('Found modules: ' => $foundModules, 'Found overrides' => $foundOverrides),
                1001
            );
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

    /**
     * This function will call controller and the corresponding action. In this function, all security layers,
     * pre-actions and post-actions, must be called. The function will generate a cache function to be executed quickly.
     *
     * @param string $controllerName The name of the Controller (partial namespace given, to complete)
     * @param string $controllerMethod The name of the function to execute. Must accept parameters: Request &$request, Response &$response
     * @param Request $request
     * @throws ResourceNotFoundException if controller action failed (not found)
     * @return boolean True for success, false if the router should pass through for the next Router (legacy Dispatcher).
     */
    final protected function doDispatch($controllerName, $controllerMethod, Request &$request)
    {
        self::$lastRouterRequestInstance = $request;
        return $this->doCall($controllerName, $controllerMethod, $request, false, true);
    }

    /**
     * This function will call controller and the corresponding action. In this function, all security layers,
     * pre-actions and post-actions, must be called. The function will generate a cache function to be executed quickly.
     *
     * @param string $controllerName The name of the Controller (partial namespace given, instantiateController() will complete with the first part)
     * @param string $controllerMethod The name of the function to execute. Must accept parameters: Request &$request, Response &$response
     * @param Request $request
     * @throws ResourceNotFoundException if controller action failed (not found)
     * @return string Data/view returned by matching controller (not sent through output buffer).
     */
    final protected function doSubcall($controllerName, $controllerMethod, Request &$request)
    {
        return $this->doCall($controllerName, $controllerMethod, $request, true);
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
    final private function doCall($controllerName, $controllerMethod, Request &$request, $returnView = false, $pinResponse = false)
    {
        $warnings = 0;

        // Find right Controller and check security on it
        try {
            list($controllerClass, $module) = $this->getControllerClass($controllerName);
            $request->attributes->set('_controller_from_module', $module);
        } catch (WarningException $we) {
            // degraded mode, many module overrides canceled.
            $controllerClass = $we->alternative;
            $request->attributes->set('_controller_from_module', '/');
            $warnings++;
        }

        $class = new \ReflectionClass($controllerClass);
        try {
            $this->checkControllerAuthority($class);
        } catch (WarningException $we) {
            $warnings++;
            /* Event dispatcher 'message' has already been triggered with event 'warning_message'. */
        }
        $method = $class->getMethod($controllerMethod);
        
        // backup _controller value for PS Router way of work, and override original value by sf way of work.
        $request->attributes->set('_controller_short', $request->attributes->get('_controller'));
        $request->attributes->set('_controller', $controllerClass.'::'.$controllerMethod);

        $routingDispatcher = $this->routingDispatcher;
        $cacheFullName = $this->cacheFileName.'_'.str_replace('\\', '_', $controllerName).'_'.$controllerMethod.($returnView?'_subcall':'').($pinResponse?'_pinned':'');
        $cache = $this->getConfigCacheFactory($warnings > 0)->cache(// force debug mode if warnings (to avoid keeping cache file)
            $this->configuration->get('_PS_CACHE_DIR_').'routing/'.$cacheFullName.'.php',
            function (ConfigCacheInterface $cache) use ($class, $controllerClass, $controllerMethod, &$routingDispatcher, &$request, $returnView, $cacheFullName, $pinResponse) {

                // find traits, classify them
                $traits = $this->getAllTraits($class);
                $initTraits = $this->filterTraits($traits, 'initAction');
                $beforeActionTraits = $this->filterTraits($traits, 'beforeAction');
                $controllerResolverTrait = $this->filterTraits($traits, 'controllerResolver'); // only 1 allowed!
                if (count($controllerResolverTrait) > 1) {
                    throw new DevelopmentErrorException('A controller cannot use multiple traits that define a controllerResolver function. Please choose one of them.');
                }
                if (count($controllerResolverTrait) === 1) {
                    $controllerResolverTrait = $controllerResolverTrait[0];
                } else {
                    $controllerResolverTrait = null;
                }
                $afterActionTraits = $this->filterTraits($traits, 'afterAction');
                $closeActionTraits = $this->filterTraits($traits, 'closeAction');

                // generate cache
                $phpCode = '<'.'?php

use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController;
use PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter;
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;

function doDispatchCached'.$cacheFullName.'(\ReflectionMethod $method, Request &$request, AbstractRouter &$router)
{
    $response = new Response();
    '.($pinResponse? '$response->pinAsLastRouterResponseInstance();':'').'
    $response->setResponseFormat(BaseController::'.($returnView?'RESPONSE_PARTIAL_VIEW':'RESPONSE_LAYOUT_HTML').');
    $actionAllowed = true;

    $controllerInstance = new '.$controllerClass.'($router);
';
                foreach ($initTraits as $initTrait) {
                    $phpCode .= '
    $actionAllowed = $actionAllowed & $controllerInstance->'.$initTrait.'($request, $response);';
                }
                foreach ($beforeActionTraits as $beforeActionTrait) {
                    $phpCode .= '
    $actionAllowed = $actionAllowed & $controllerInstance->'.$beforeActionTrait.'($request, $response);';
                }
                if ($controllerResolverTrait) {
                    $phpCode .= '

    if ($actionAllowed) {
        $controllerResolver = $controllerInstance->'.$controllerResolverTrait.'($request, $response, $router);
        if ($controllerResolver) {
            $responseFormat = $controllerResolver($controllerInstance, $method);
            if ($responseFormat) {
                $response->setResponseFormat($responseFormat);
            }
        } else {
            throw new DevelopmentErrorException(\'The controller uses a Trait controllerResolver that failed to return a controllerResolver!\');
        }
    }
';
                } else {
                    $phpCode .= '

    if ($actionAllowed) {
        $responseFormat = $controllerInstance->'.$controllerMethod.'($request, $response);
        if ($responseFormat) {
            $response->setResponseFormat($responseFormat);
        }
    }
';
                }
                foreach ($afterActionTraits as $afterActionTrait) {
                    $phpCode .= '
    $actionAllowed = $actionAllowed & $controllerInstance->'.$afterActionTrait.'($request, $response);';
                }
                foreach ($closeActionTraits as $closeActionTrait) {
                    $phpCode .= '
    $actionAllowed = $actionAllowed & $controllerInstance->'.$closeActionTrait.'($request, $response);';
                }
                
                $phpCode .= '

    if ($actionAllowed && ($responseFormat = $response->getResponseFormat())) {
        list($encapsulation, $format) = explode(\'_\', $responseFormat);
        if ($format) {
            $controllerInstance->formatResponse($format, $response);
        }
        if ($encapsulation) {
            $controllerInstance->encapsulateResponse($encapsulation, $response);
        }
';
                if ($returnView) {
                    $phpCode .= '
        // Do not use send (no output buffer tricks)
        return $response->getContent();
    }

    if (!$actionAllowed) {
        throw new \Core_Foundation_Exception_Exception(\'Action forbidden.\');
    }
}
'; // Raw php code inside a string, do not indent please.
                } else {
                    $phpCode .= '
        // Send response to output buffer
        $response->send();
    }

    if (!$actionAllowed) {
        $router = '.get_class($this).'::getInstance();
        $router->redirectToForbidden();
    }
    return true;
}
'; // Raw php code inside a string, do not indent please.
                }

                $cache->write($phpCode);
                $routingDispatcher->dispatch('cache_generation', (new BaseEvent())->setRequest($request));
            }
        );

        include_once $cache->getPath();
        $functionName = 'doDispatchCached'.$cacheFullName;
        return $functionName($method, $request, $this);
    }

    public function setForbiddenRedirection($redirection)
    {
        $this->forbiddenRedirection = $redirection;
    }

    public function redirectToForbidden()
    {
        if ($this->forbiddenRedirection === null) {
            $this->redirect(500);
        }
        $this->redirect($this->forbiddenRedirection); // HTTP code 200 (Login page for example)
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
    public function generateUrl($name, $parameters = array(), $forceLegacyUrl = false, $referenceType = UrlGeneratorInterface::ABSOLUTE_URL)
    {
        if ($forceLegacyUrl == true) {
            // This feature is made in AdminController and FrontController subclasses only.
            throw new DevelopmentErrorException('You cannot ask for legacy URL without overriding the generateUrl() method.');
        }
        try {
            if ($referenceType === self::ABSOLUTE_ROUTE) {
                $urlGenerator = $this->getUrlGenerator();
                $baseUrl = $urlGenerator->getContext()->getBaseUrl();

                $url = $urlGenerator->generate($name, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH);
                $path = parse_url($url)['path'];

                // remove base URL (for '(/xxx)?/admin-xxx/index.php' if present in the URL)
                if (strlen($baseUrl) > 0 && strpos($path, $baseUrl) === 0) {
                    $path = substr($path, strlen($baseUrl));
                }
                
                return $path;
            }
            return $this->getUrlGenerator()->generate($name, $parameters, $referenceType);
        } catch (RouteNotFoundException $rnfe) {
            return false;
        }
    }

    /**
     * This function will generate a URL and will send a redirection to it to the browser.
     *
     * @param string $route The route name
     * @param array $parameters The route parameters
     * @param boolean $forceLegacyUrl True to use alternative URL to reach legacy dispatcher.
     * @param boolean $permanent True to send 'Permanently moved' header code. False to send 'Temporary redirection' header code.
     */
    final protected function doRedirect($route, $parameters, $forceLegacyUrl = false, $permanent = false)
    {
        $url = $this->generateUrl($route, $parameters, $forceLegacyUrl, UrlGeneratorInterface::ABSOLUTE_URL);
        $this->routingDispatcher->dispatch('redirection_sent', new BaseEvent($url));
        if ($permanent) {
            header('Status: 301 Moved Permanently', false, 301);
        }
        header('Location: '.$url, true);
        $this->exitNow();
    }
}
