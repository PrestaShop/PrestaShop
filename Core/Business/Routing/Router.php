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

abstract class Router extends AbstractRouter
{

    private final function filterTraits($allTraits, $startsWith)
    {
        $traitFunctions = array();
        foreach($allTraits as $trait) {
            $methods = $trait->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach($methods as $method) {
                if ($method->getNumberOfParameters() != 2) {
                    continue; // TODO : more secure, and throw a log/warning/ErrorException ?
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
     *
     * @param string $controllerName
     * @throws \ErrorException If no default controller found in the Core. The routes YML file is incorrect.
     * @return string The controller class name (with right namespace)
     */
    protected final function getControllerClass($controllerName)
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
            // Pire que ca : ce warning doit être poussé dans la génération du cache :)
        }

        // fallback on default Core controller (most of the time).
        $className = '\\PrestaShop\\PrestaShop\\Core\\Business\\Controller\\'.$controllerName;
        if (!class_exists($className)) {
            throw new \ErrorException('Default Controller is not found for: '.$className);
        }
        return $className;
    }

    /**
     * TODO : php doc here !
     * @param \ReflectionClass $class
     */
    abstract protected function checkControllerAuthority(\ReflectionClass $class);

    /**
     * This function will call controller and the corresponding action. In this function, all security layers,
     * pre-actions and post-actions, must be called. The function will generate a cache function to be executed quickly.
     *
     * @param string $controllerName The name of the Controller (partial namespace given, instantiateController() will complete with the first part)
     * @param string $controllerMethod The name of the function to execute. Must accept parameters: Request &$request, Response &$response
     * @param Request $request
     * @throws ResourceNotFoundException if controller action failed (not found)
     * @return boolean True for success, false if the router should pass through for the next Router (legacy Dispatcher).
     */
    protected function doDispatch($controllerName, $controllerMethod, Request &$request)
    {
        // Find right Controller and check security on it
        $controllerClass = $this->getControllerClass($controllerName);
        $class = new \ReflectionClass($controllerClass);
        $this->checkControllerAuthority($class);
        $method = $class->getMethod($controllerMethod);
        // backup _controller value for PS Router way of work, and override original value by sf way of work.
        $request->attributes->set('_controller_short', $request->attributes->get('_controller'));
        $request->attributes->set('_controller', $controllerClass.'::'.$controllerMethod);

        $cache = $this->getConfigCacheFactory()->cache(
            $this->configuration->get('_PS_CACHE_DIR_').'routing/'.$this->cacheFileName.'_'.str_replace('\\', '_', $controllerName).'_'.$controllerMethod.'.php',
            function (ConfigCacheInterface $cache)
            use($class, $controllerClass, $controllerMethod) {

                // find traits, classify them
                $traits = $class->getTraits();
                $initTraits = $this->filterTraits($traits, 'initAction');
                $beforeActionTraits = $this->filterTraits($traits, 'beforeAction');
                $controllerResolverTrait = $this->filterTraits($traits, 'controllerResolver'); // only 1 allowed!
                if (count($controllerResolverTrait) > 1) {
                    throw new \ErrorException('A controller cannot use multiple traits that define a controllerResolver function. Please choose one of them.');
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

function doDispatchCached(\ReflectionMethod $method, Request &$request)
{
    $response = new Response();
    $response->setResponseFormat(BaseController::RESPONSE_LAYOUT_HTML);
    $actionAllowed = true;

    $controllerInstance = new '.$controllerClass.'();
';
                foreach($initTraits as $initTrait) {
                    $phpCode .= '
    $actionAllowed = $actionAllowed & $controllerInstance->'.$initTrait.'($request, $response);';
                }
                foreach($beforeActionTraits as $beforeActionTrait) {
                    $phpCode .= '
    $actionAllowed = $actionAllowed & $controllerInstance->'.$beforeActionTrait.'($request, $response);';
                }
                if ($controllerResolverTrait) {
                    $phpCode .= '

    $controllerResolver = $controllerInstance->'.$controllerResolverTrait.'($request, $response);
    if ($controllerResolver && $actionAllowed) {
        $responseFormat = $controllerResolver($controllerInstance, $method);
        if ($responseFormat) {
            $response->setResponseFormat($responseFormat);
        }
    } else {
        throw new \ErrorException(\'The controller uses a Trait controllerResolver that failed to return a controllerResolver!\');
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
                foreach($afterActionTraits as $afterActionTrait) {
                    $phpCode .= '
    $actionAllowed = $actionAllowed & $controllerInstance->'.$afterActionTrait.'($request, $response);';
                }
                foreach($closeActionTraits as $closeActionTrait) {
                    $phpCode .= '
    $actionAllowed = $actionAllowed & $controllerInstance->'.$closeActionTrait.'($request, $response);';
                }
                
                $phpCode .= '

    if ($actionAllowed && ($responseFormat = $response->getResponseFormat())) {
        list($encapsulation, $format) = explode(\'/\', $responseFormat);
        if ($format) {
            $controllerInstance->formatResponse($format, $response);
        }
        if ($encapsulation) {
            $controllerInstance->encapsulateResponse($encapsulation, $response);
        }

        // Send response to output buffer
        $response->send();
    }

    if (!$actionAllowed) {
        // TODO : forbiden forward !
    }
    return true;
}
'; // Raw php code inside a string, do not indent please.
                $cache->write($phpCode);
            }
        );

        include $cache->getPath();
        return doDispatchCached($method, $request);
    }

}
