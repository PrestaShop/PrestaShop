<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Adapter\Hook;

use PrestaShop\PrestaShop\Adapter\Hook\HookDispatcher;
use PrestaShopBundle\Service\Hook\HookEvent;
use PrestaShopBundle\Service\Hook\RenderingHookEvent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\EventDispatcher\Event;

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
        $this->assertInstanceOf(HookEvent::class, $hookDispatcher->dispatch(new HookEvent()));
        $this->assertInstanceOf(HookEvent::class, $hookDispatcher->dispatch(new HookEvent(), 'unknown_hook_name'));
        $this->assertInstanceOf(HookEvent::class, $hookDispatcher->dispatch(new RenderingHookEvent(), 'unknown_hook_name'));
    }

    /**
     * Test a simple dispatch with a custom listener.
     */
    public function testAddListener(): void
    {
        $hookDispatcher = $this->getHookDispatcher();

        $hookDispatcher->addListener('test_test', [$this, 'listenerCallback']);
        $hookDispatcher->dispatch(new HookEvent(), 'unknown_hook_name');
        $this->assertFalse($this->testedListenerCallbackCalled);
        $hookDispatcher->dispatch(new HookEvent(), 'test_test');
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
        $event = $hookDispatcher->dispatch(new RenderingHookEvent(), 'test_test_2');

        $subset = [
            'listenerCallback2' => ['result_test_2'],
            'overriden_listener_name' => ['result_test_2b'],
        ];
        $array = $event->getContent();

        foreach ($subset as $key => $value) {
            $this->assertArrayHasKey($key, $array);
            $this->assertEquals($value, $array[$key]);
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
