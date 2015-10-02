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
namespace PrestaShop\PrestaShop\tests\Unit\Core\Foundation\Dispatcher;

use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\EventDispatcher;
use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use PrestaShop\PrestaShop\Core\Foundation\Dispatcher\BaseEvent;
use PrestaShop\PrestaShop\Core\Business\Dispatcher\BaseEventDispatcher;
use PrestaShop\PrestaShop\Core\Business\Dispatcher\HookEvent;

class BaseEventDispatcherTest extends UnitTestCase
{
    private function setup_env()
    {
        $fakeRoot = dirname(dirname(dirname(dirname(__DIR__)))); // to tests folder
        $this->assertEquals('tests', substr($fakeRoot, -5));

        return $this->setConfiguration(array(
            '_PS_ROOT_DIR_' => $fakeRoot,
            '_PS_CACHE_DIR_' => $fakeRoot.'/cache/',
            '_PS_MODULE_DIR_' => $fakeRoot.'/resources/module/',
            '_PS_MODE_DEV_' => true
        ));
    }

    public function test_event_methods()
    {
        $conf = $this->setup_env();
        $event = new HookEvent('thé', new \Core_Foundation_Exception_Exception('exception_test'));

        // string concat mode
        $event->setHookResult('string');
        $event->appendHookResult('-string');
        $this->assertEquals('string-string', $event->getHookResult());

        // array append mode
        $event->setHookResult(array('k1' => 'v1'));
        $event->appendHookResult(array('k2' => 'v2'));
        $event->appendHookResult('v3');
        $this->assertArrayHasKey('k1', $event->getHookResult());
        $this->assertArrayHasKey('k2', $event->getHookResult());
        $this->assertArrayHasKey('0', $event->getHookResult());
    }
}
