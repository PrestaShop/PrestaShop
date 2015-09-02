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
use PrestaShop\PrestaShop\Core\Business\Routing\Router;
use PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException;
use PrestaShop\PrestaShop\Core\Business\Controller\FrontController;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\EventDispatcher;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;

class FakeRouter extends Router
{
    private static $instance = null;
    final public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self('fake_controllers_routes(_(.*))?\.yml');
        }
        return self::$instance;
    }
    
    public $calledcheckControllerAuthority = null;
    
    protected function checkControllerAuthority(\ReflectionClass $class)
    {
        $this->calledcheckControllerAuthority = $class->name;
        if ($class->name == 'PrestaShop\\PrestaShop\\Tests\\RouterTest\\Test\\RouterTestControllerError') {
            throw new \ErrorException('FakeControllerError stops!');
        }
        if ($class->name == 'PrestaShop\\PrestaShop\\Tests\\RouterTest\\Test\\RouterTestControllerWarning') {
            throw new WarningException('FakeControllerWarning does not stop!', 'alternateText');
        }
    }
}

class FakeControllerError extends FrontController
{
}
class FakeControllerWarning extends FrontController
{
}

class RouterTest extends UnitTestCase
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

    private $warningReceived = false;

    public function warningListenerEvent(BaseEvent $e)
    {
        $this->warningReceived = ($e->getException()->alternative == 'alternateText');
    }

    public function test_router_unknown_route()
    {
        $this->setup_env();
        $router = FakeRouter::getInstance();

        // push request into PHP globals (simulate a request) and resolve through dispatch().
        $fakeRequest = Request::create('/unknown'); // unknown route, return false!
        $fakeRequest->overrideGlobals();
        $found = $router->dispatch(true);
        $this->assertFalse($found, 'Unknown route should return false through dispatch().');
    }

    public function test_router_module_routes()
    {
        $this->setup_env();
        $router = FakeRouter::getInstance();

        // load from a module! Controller & Action OK case.
        $fakeRequest = Request::create('/routerTest/a'); // route to existing controller in a module, action OK.
        $fakeRequest->overrideGlobals();
        $found = $router->dispatch(true);
        $this->assertTrue($found, '/routerTest/a should be found.');

        // load from a module! Controller Error case (bad parent class checked)
        $fakeRequest = Request::create('/routerTest/b');
        $fakeRequest->overrideGlobals();
        try {
            $found = $router->dispatch(true);
            $this->fail('/routerTest/b should be found but must trigger an error.');
        } catch (\ErrorException $ee) {
            $this->assertEquals('FakeControllerError stops!', $ee->getMessage());
        }

        // load from a module! Controller Warning case (bad parent class checked)
        $fakeRequest = Request::create('/routerTest/c');
        $fakeRequest->overrideGlobals();
        EventDispatcher::getInstance('message')->addListener('warning_message', array($this, 'warningListenerEvent'));
        try {
            $found = $router->dispatch(true);
            $this->assertTrue($found, '/routerTest/c should be found even with a warning exception.');
            $this->assertAttributeEquals(true, 'warningReceived', $this);
        } catch (\Exception $e) {
            $this->fail('/routerTest/c should not trigger another exception.');
        }
    }

    public function test_subcall()
    {
        // TODO
    }

    public function test_forward()
    {
        // TODO
    }

    public function test_url_generation()
    {
        // TODO
    }

    // Because redirect will trigger exit;, this test will stops phpunit!
//     public function test_redirect()
//     {
//         $this->setup_env();
//         $router = FakeRouter::getInstance();

//         $fakeRequest = Request::create('/routerTest/redirect');
//         $fakeRequest->overrideGlobals();
//         $router->dispatch();
//     }
}
