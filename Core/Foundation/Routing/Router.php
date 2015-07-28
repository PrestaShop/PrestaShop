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

class Router
{
    /**
     * @var Router
     */
    public static $instance = null;
    
    public $routeLoader;
    
    /**
     * Get current instance of router (singleton)
     *
     * @return Router
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct()
    {
        $locator = new FileLocator(array(__DIR__.'/../../..'));
        $this->routeLoader = new YamlFileLoader($locator);
    }
    
    public function dispatch($noRoutePassThrough = false)
    {
        $requestContext = new RequestContext();
        $requestContext->fromRequest(Request::createFromGlobals());
        
        $router = new \Symfony\Component\Routing\Router(
            $this->routeLoader,
            'config/yml/routes.yml',
            array('cache_dir' => __DIR__.'/../../../cache/routing'),
            $requestContext
        );
        
        try {
            $parameters = $router->match($requestContext->getPathInfo());
            list($controllerName, $controllerMethod) = explode('::', $parameters['_controller']);
            
            // Find right Controller
            $controller = $this->initController($controllerName);
            
            // Execute action
            $controller->$controllerMethod($requestContext);
            
            return true;
        }catch (ResourceNotFoundException $e) {
            if ($noRoutePassThrough) {
                // Allow legacy code to handle request if not found in this dispatcher
                return false;
            } else {
                throw $e;
            }
        }
    }
    
    private function initController($controllerName)
    {
        $namespace = 'PrestaShop\\PrestaShop\\Core\\Controller'; // TODO: ici on pourra tester plusieurs namespaces priorisés, pour un system d'overrides ?
        
        $class = new \ReflectionClass('\\'.$namespace.'\\'.$controllerName);
        $controller = $class->newInstance();
        return $controller;
    }
    
}
