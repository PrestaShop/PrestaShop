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

class FakeAbstractRouterNotAbstract extends AbstractRouter
{
    public $calledControllerName;
    public $calledControllerMethod;
    public $calledWithResquest;
    
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
}

class AbstractRouterTest extends UnitTestCase
{
    private function setup_env()
    {
        $fakeRoot = dirname(dirname(dirname(dirname(__DIR__)))); // to tests folder
        $this->assertEquals('tests', substr($fakeRoot, -5));

        $this->setConfiguration(array(
            '_PS_ROOT_DIR_' => $fakeRoot,
            '_PS_CACHE_DIR_' => $fakeRoot.'/cache/',
            '_PS_MODULE_DIR_' => $fakeRoot.'/resources/module/',
            '_PS_MODE_DEV_' => true
        ));
        
    }

    public function test_router_instantiation()
    {
        $this->setup_env();

        $router = new FakeAbstractRouterNotAbstract('fake_test_routes(_(.*))?\.yml');
        $routingFiles = $this->getObjectAttribute($router, 'routingFiles');
        $this->assertCount(2, $routingFiles, 'Two configuration files should be scaned.');
        $this->assertArrayHasKey('/', $routingFiles);
        $this->assertArrayHasKey('/abstractRouterTest', $routingFiles);

        $controllerNamespaces = $this->getObjectAttribute($router, 'controllerNamespaces');
        $this->assertCount(3, $controllerNamespaces, 'Tree configuration files should be scaned.');

        $this->assertContains('PrestaShop\\PrestaShop\\Tests\\Unit\\Core\\Business\\ControllerFakeModule', $controllerNamespaces);
        $this->assertContains('PrestaShop\\PrestaShop\\Tests\\Unit\\Core\\Business\\Controller', $controllerNamespaces);
    }

    public function test_router_resolution()
    {
        $this->setup_env();
        $router = new FakeAbstractRouterNotAbstract('fake_test_routes(_(.*))?\.yml');

        // push request into PHP globals (simulate a request) and resolve through dispatch().
        $fakeRequest = Request::create('/a');
        $fakeRequest->overrideGlobals();
        $router->dispatch();

        $this->assertEquals('Test\\TestController', $router->calledControllerName, 'Bad controller resolution');
        $this->assertEquals('aAction', $router->calledControllerMethod, 'Bad method resolution');
        $this->assertEquals('fake_test_route1', $router->calledWithResquest->attributes->get('_route'));
    }

    public function test_router_resolution_conflict()
    {
        $this->setup_env();
        try {
            $router = new FakeAbstractRouterNotAbstract('fake_test_conflict_routes(_(.*))?\.yml');
            $this->fail('This instanciation should throw ErrorException!');
        } catch (\ErrorException $ee) {
            $this->assertContains('route ID: fake_test_route_module1, prefix: /abstractRouterTest', $ee->getMessage());
        }
    }

    public function test_router_resolution_priority()
    {
        $this->setup_env();
        $router = new FakeAbstractRouterNotAbstract('fake_test_routes(_(.*))?\.yml');

        // push request into PHP globals (simulate a request) and resolve through dispatch().
        $fakeRequest = Request::create('/abstractRouterTest/b/1'); // module must take priority, not Core
        $fakeRequest->overrideGlobals();
        $router->dispatch();

        $this->assertEquals('Test\\TestController', $router->calledControllerName, 'Bad controller resolution');
        $this->assertEquals('bModuleAction', $router->calledControllerMethod, 'Bad method resolution');
        $this->assertEquals('fake_test_route_module2', $router->calledWithResquest->attributes->get('_route'));
    }

    public function test_subcall()
    {
        // TODO
    }

    public function test_forward()
    {
        // TODO
    }

    public function test_redirect()
    {
        // TODO
    }

}
