<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace Tests\PrestaShopBundle\Service\Hook;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PrestaShopBundle\Service\Hook\HookEvent;
use PrestaShopBundle\Service\Hook\RenderingHookEvent;
use Symfony\Component\EventDispatcher\Event;

/**
 * @group sf
 * Tests about admin CommonController and its actions.
 *
 */
class HookDispatcherTest extends KernelTestCase
{
    /**
     * Test a simple dispatch.
     */
    public function testDispatch()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $hookDisptacher = $kernel->getContainer()->get('prestashop.hook.dispatcher');

        $hookDisptacher->dispatch('unknown_hook_name');
        $hookDisptacher->dispatch('unknown_hook_name', new HookEvent());
        $hookDisptacher->dispatch('unknown_hook_name', new RenderingHookEvent());
    }

    /**
     * Test a simple dispatch with a custom listener.
     */
    public function testAddListener()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $hookDisptacher = $kernel->getContainer()->get('prestashop.hook.dispatcher');

        $hookDisptacher->addListener('test_test', array($this, 'listenerCallback'));
        $hookDisptacher->dispatch('unknown_hook_name');
        $this->assertFalse($this->testedListenerCallbackCalled);
        $hookDisptacher->dispatch('test_test');
        $this->assertTrue($this->testedListenerCallbackCalled);
    }
    private $testedListenerCallbackCalled = false;
    public function listenerCallback(Event $event, $eventName)
    {
        $this->assertEquals('test_test', $eventName);
        $this->testedListenerCallbackCalled = true;
    }

    /**
     * Test a simple dispatch with a custom listener that renders response.
     */
    public function testAddListenerRendering()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $hookDisptacher = $kernel->getContainer()->get('prestashop.hook.dispatcher');

        $hookDisptacher->addListener('test_test_2', array($this, 'listenerCallback2'));
        $hookDisptacher->addListener('test_test_2', array($this, 'listenerCallback2b'));
        $event = $hookDisptacher->dispatch('test_test_2', new RenderingHookEvent());
        $this->assertArraySubset(array(
            'listenerCallback2' => "result_test_2",
            'overriden_listener_name' => "result_test_2b"
        ), $event->getContent());
    }
    public function listenerCallback2(RenderingHookEvent $event, $eventName)
    {
        $this->assertEquals('test_test_2', $eventName);
        $event->setContent(['result_test_2']);
    }
    public function listenerCallback2b(RenderingHookEvent $event, $eventName)
    {
        $this->assertEquals('test_test_2', $eventName);
        $event->setContent(['result_test_2b'], 'overriden_listener_name');
    }
}
