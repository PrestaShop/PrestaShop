<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\Adapter\Hook;

use PrestaShop\PrestaShop\Adapter\Hook\HookDispatcher;
use PrestaShopBundle\Service\Hook\HookEvent;
use PrestaShopBundle\Service\Hook\RenderingHookEvent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\Event;

class HookDispatcherTest extends KernelTestCase
{
    /** @var bool */
    private $testedListenerCallbackCalled = false;

    private function getHookDispatcher(): HookDispatcher
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        return $kernel->getContainer()->get('prestashop.hook.dispatcher');
    }

    /**
     * Test a simple dispatch.
     */
    public function testDispatch(): void
    {
        $hookDispatcher = $this->getHookDispatcher();
        $this->assertInstanceOf(HookEvent::class, $hookDispatcher->dispatch('unknown_hook_name'));
        $this->assertInstanceOf(HookEvent::class, $hookDispatcher->dispatch('unknown_hook_name', new HookEvent()));
        $this->assertInstanceOf(HookEvent::class, $hookDispatcher->dispatch('unknown_hook_name', new RenderingHookEvent()));
    }

    /**
     * Test a simple dispatch with a custom listener.
     */
    public function testAddListener(): void
    {
        $hookDispatcher = $this->getHookDispatcher();

        $hookDispatcher->addListener('test_test', [$this, 'listenerCallback']);
        $hookDispatcher->dispatch('unknown_hook_name');
        $this->assertFalse($this->testedListenerCallbackCalled);
        $hookDispatcher->dispatch('test_test');
        $this->assertTrue($this->testedListenerCallbackCalled);
    }

    /**
     * Test a simple dispatch with a custom listener that renders response.
     */
    public function testAddListenerRendering(): void
    {
        $hookDispatcher = $this->getHookDispatcher();

        $hookDispatcher->addListener('test_test_2', [$this, 'listenerCallback2']);
        $hookDispatcher->addListener('test_test_2', [$this, 'listenerCallback2b']);
        /** @var RenderingHookEvent $event */
        $event = $hookDispatcher->dispatch('test_test_2', new RenderingHookEvent());

        $subset = [
            'listenerCallback2' => ['result_test_2'],
            'overriden_listener_name' => ['result_test_2b'],
        ];
        $array = $event->getContent();

        foreach ($subset as $key => $value) {
            $this->assertArrayHasKey($key, $array);
            $this->assertEquals($value, $subset[$key]);
        }
    }

    public function listenerCallback(Event $event, string $eventName): void
    {
        $this->assertEquals('test_test', $eventName);
        $this->testedListenerCallbackCalled = true;
    }

    public function listenerCallback2(RenderingHookEvent $event, string $eventName): void
    {
        $this->assertEquals('test_test_2', $eventName);
        $event->setContent(['result_test_2']);
    }

    public function listenerCallback2b(RenderingHookEvent $event, string $eventName): void
    {
        $this->assertEquals('test_test_2', $eventName);
        $event->setContent(['result_test_2b'], 'overriden_listener_name');
    }
}
