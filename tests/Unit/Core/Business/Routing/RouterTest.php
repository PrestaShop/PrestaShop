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
namespace PrestaShop\PrestaShop\Tests\Unit\Core\Business\Routing;

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
    final public static function getInstance(\Core_Foundation_IoC_Container $container)
    {
        if (!self::$instance) {
            self::$container = $container;
            self::$instance = new self('fake_test_routes(_(.*))?\.yml');
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

    public static $calledExitNow = false;
    public function exitNow($i = 0)
    {
        self::$calledExitNow = true;
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

    public function test_router_instance()
    {
        $this->setup_env();
        $router = FakeRouter::getInstance($this->container);

        // push request into PHP globals (simulate a request) and resolve through dispatch().
        $fakeRequest = Request::create('/unknown'); // unknown route, return false!
        $fakeRequest->overrideGlobals();
        $found = $router->dispatch(true);
        $this->assertFalse($found, 'Unknown route should return false through dispatch().');
    }
}
