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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\Routing;

use Exception;
use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use Core_Business_Stock_StockManager;
use PrestaShop\PrestaShop\Core\Foundation\Routing\Response;
use PrestaShop\PrestaShop\Core\Foundation\Controller\BaseController;
use PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;
use PrestaShop\PrestaShop\Core\Business\Dispatcher\BaseEventDispatcher;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\EventDispatcher;

class FakeAbstractRouterNotAbstract extends AbstractRouter
{
    public $calledControllerName;
    public $calledControllerMethod;
    public $calledWithResquest;

    public function __construct($container, $routingFilePattern, $conf)
    {
        $this->container = $container;
        parent::__construct($routingFilePattern);
        // EventDispatcher init
        BaseEventDispatcher::initDispatchers(
            $container,
            $conf->get('_PS_ROOT_DIR_'),
            $conf->get('_PS_CACHE_DIR_'),
            $conf->get('_PS_MODULE_DIR_'));
        $this->routingDispatcher = $this->container->make('final:EventDispatcher/routing');
    }

    protected function doDispatch($controllerName, $controllerMethod, Request &$request)
    {
        $this->calledControllerName = $controllerName;
        $this->calledControllerMethod = $controllerMethod;
        $this->calledWithResquest = $request;
    }

    protected function doSubcall($controllerName, $controllerMethod, Request &$request)
    {
        $this->calledControllerName = $controllerName;
        $this->calledControllerMethod = $controllerMethod;
        $this->calledWithResquest = $request;
    }

    protected function doRedirect($route, $parameters, $forceLegacyUrl = false, $permanent = false)
    {
    }

    public function generateUrl($name, $parameters = array(), $forceLegacyUrl = false, $referenceType = UrlGeneratorInterface::ABSOLUTE_URL)
    {
    }
    
    public function registerShutdownFunctionCallback(Request &$request)
    {
    }

    public function exitNow($i = 0)
    {
    }
}

class AbstractRouterTest extends UnitTestCase
{
    private function setup_env()
    {
        $fakeRoot = dirname(dirname(dirname(dirname(__DIR__)))); // to tests folder
        $this->assertEquals('tests', substr($fakeRoot, -5));

        // Router instance clean
        $routerClass = new \ReflectionClass('PrestaShop\\PrestaShop\\Core\\Business\\Routing\\Router');
        $instantiated = $routerClass->getProperty('instantiated');
        $instantiated->setAccessible(true);
        $instantiated->setValue(null, false);

        // Dispatcher clean
        $dispatcherClass = new \ReflectionClass('PrestaShop\\PrestaShop\\Core\\Foundation\\Dispatcher\\EventDispatcher');
        $instances = $dispatcherClass->getProperty('instances');
        $instances->setAccessible(true);
        $instances->setValue(null, array());

        return $this->setConfiguration(array(
            '_PS_ROOT_DIR_' => $fakeRoot,
            '_PS_CACHE_DIR_' => $fakeRoot.'/cache/',
            '_PS_MODULE_DIR_' => $fakeRoot.'/resources/module/',
            '_PS_MODE_DEV_' => true
        ));
    }

    public function test_router_instantiation()
    {
        $conf = $this->setup_env();

        $router = new FakeAbstractRouterNotAbstract($this->container, 'fake_test_routes(_(.*))?\.yml', $conf);
        $routingFiles = $this->getObjectAttribute($router, 'routingFiles');
        $this->assertCount(1, $routingFiles, 'One configuration file should be scaned.');
        $this->assertArrayHasKey('/', $routingFiles);
    }

    public function test_router_resolution()
    {
        $conf = $this->setup_env();
        $router = new FakeAbstractRouterNotAbstract($this->container, 'fake_test_routes(_(.*))?\.yml', $conf);

        // push request into PHP globals (simulate a request) and resolve through dispatch().
        $fakeRequest = Request::create('/a');
        $fakeRequest->overrideGlobals();
        $router->dispatch();

        $this->assertEquals('Test\\TestController', $router->calledControllerName, 'Bad controller resolution');
        $this->assertEquals('aAction', $router->calledControllerMethod, 'Bad method resolution');
        $this->assertEquals('fake_test_route1', $router->calledWithResquest->attributes->get('_route'));
    }
}
