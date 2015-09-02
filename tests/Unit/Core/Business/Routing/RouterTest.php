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

class FakeRouter extends Router
{
    public $calledcheckControllerAuthority = null;
    
    protected function checkControllerAuthority(\ReflectionClass $class)
    {
        $this->calledcheckControllerAuthority = $class->name;
        if ($class->name == 'FakeControllerError') // FIXME: name contient tout le namespace ?
        {
            throw new \ErrorException('FakeControllerError stops!');
        }
        if ($class->name == 'FakeControllerWarning') // FIXME: name contient tout le namespace ?
        {
            throw new WarningException('FakeControllerWarning does not stop!');
        }
    }
}

class FakeControllerError extends FrontController { }
class FakeControllerWarning extends FrontController { }

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

    public function test_router_unknown_route()
    {
        $this->setup_env();

        $router = new FakeRouter('fake_controllers_routes(_(.*))?\.yml');

        // push request into PHP globals (simulate a request) and resolve through dispatch().
        $fakeRequest = Request::create('/unknown'); // unknown route, return false!
        $fakeRequest->overrideGlobals();
        $found = $router->dispatch(true);
        $this->assertFalse($found, 'Unknown route should return false through dispatch().');
        
        // TODO : verifier la generation des caches: pas de cache pour une route inconnue
    }

    public function test_router_module_routes()
    {
        $this->setup_env();

        $router = new FakeRouter('fake_controllers_routes(_(.*))?\.yml');

        // load from a module!
        $fakeRequest = Request::create('/routerTest/a'); // route to existing controller in a module, action OK.
        $fakeRequest->overrideGlobals();
        //$found = $router->dispatch(true); // FIXME : devrait fonctionner ! Vois pk l'autoload ne le prends pas... voir avec Luke !
        new \PrestaShop\PrestaShop\Tests\RouterTest\Test\RouterTestController();
        
        // TODO : dispatch vers 3 routes : un bon controller, un controller FakeControllerError, et un FakeControllerWarning
        
        // TODO : verifier la generation des caches: présence et nom de fichier, non vide.
    }
}
