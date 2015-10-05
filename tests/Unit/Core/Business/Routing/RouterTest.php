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
use PrestaShop\PrestaShop\Core\Foundation\Controller\AbstractController;
use PrestaShop\PrestaShop\Core\Foundation\Routing\AbstractRouter;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Business\Routing\Router;
use PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException;
use PrestaShop\PrestaShop\Core\Business\Controller\FrontController;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\EventDispatcher;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;
use PrestaShop\PrestaShop\Core\Business\Context;
use PrestaShop\PrestaShop\Core\Business\Dispatcher\BaseEventDispatcher;
use PrestaShop\PrestaShop\Core\Business\Dispatcher\HookEvent;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use PrestaShop\PrestaShop\Tests\Fake\FakeRouter;

class Fake_Adapter_HookManager extends \Adapter_HookManager
{
    public function exec(
        $hook_name,
        $hook_args = array(),
        $id_module = null,
        $array_return = false,
        $check_exceptions = true,
        $use_push = false,
        $id_shop = null
    ) {
        return $hook_name;
    }
}

class RouterTest extends UnitTestCase
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
        $router = new FakeRouter($this->container);

        // push request into PHP globals (simulate a request) and resolve through dispatch().
        $fakeRequest = Request::create('/unknown'); // unknown route, return false!
        $fakeRequest->overrideGlobals();
        $found = $router->dispatch(true);
        $this->assertFalse($found, 'Unknown route should return false through dispatch().');

        // hook listener
        $hookDispatcher = $this->container->make('final:EventDispatcher/hook'); // FIXME
        // check there is listeners registered at Router instantiation.
        $count = count($hookDispatcher->getListeners());
        $this->assertGreaterThan(0, $count);
        // add hook listener
        $hookDispatcher->addListener('legacy_TestLegacyHook', array($this, 'listenHook'), 255);
        $this->assertEquals($count+1, count($hookDispatcher->getListeners()));
        $result = $hookDispatcher->hook('legacy_TestLegacyHook', array('k1' => 'v1'));
        // add legacy hook listener
        $hookDispatcher->addListener('legacy_TestLegacyHook', array(new Fake_Adapter_HookManager(), 'onHook'), 128);
        $result = $hookDispatcher->hook('legacy_TestLegacyHook', array('k1' => 'v1'));
        $this->assertContains('TestLegacyHook', $result); // 'legacy_' prefix is removed for legacy HOOKS!
    }

    public function listenHook(HookEvent $event, $eventName)
    {
        $this->assertEquals('legacy_TestLegacyHook', $eventName, 'Event name should not be modified here.');
        $this->assertInstanceOf('PrestaShop\\PrestaShop\\Core\\Business\\Dispatcher\\HookEvent', $event, 'Event should be instance of HookEvent.');
        $params = $event->getHookParameters();
        $this->assertArrayHasKey('k1', $params, 'Failed to transfer hook params.');
        $event->appendHookResult('result1');
    }
}
