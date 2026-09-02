<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Hook;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Hook\HookDispatcher as HookDispatcherAdapter;
use PrestaShop\PrestaShop\Core\Hook\Hook;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcher;
use PrestaShop\PrestaShop\Core\Hook\HookInterface;
use PrestaShop\PrestaShop\Core\Hook\RenderedHook;
use PrestaShopBundle\Service\Hook\RenderingHookEvent;

class HookDispatcherTest extends TestCase
{
    /**
     * @var HookDispatcherAdapter|MockObject
     */
    private $hookDispatcherAdapter;

    /**
     * @var HookDispatcher
     */
    private $hookDispatcher;

    protected function setUp(): void
    {
        $this->hookDispatcherAdapter = $this->createMock(HookDispatcherAdapter::class);
        $this->hookDispatcher = new HookDispatcher($this->hookDispatcherAdapter);
    }

    public function testDispatchHook()
    {
        // stub hook behavior
        $hook = $this->createHook();

        // stub adapter expectations
        $this->hookDispatcherAdapter
            ->expects($this->once())
            ->method('dispatchForParameters')
            ->with($this->equalTo('hookName'), $this->equalTo([]));

        $this->hookDispatcher->dispatchHook($hook);
    }

    public function testDispatchWithParameters()
    {
        // stub adapter expectations
        $this->hookDispatcherAdapter
            ->expects($this->once())
            ->method('dispatchForParameters')
            ->with($this->equalTo('fooHook'), $this->equalTo(['bar' => 'Bar']));

        $this->hookDispatcher->dispatchWithParameters('fooHook', ['bar' => 'Bar']);
    }

    public function testDispatchRendering()
    {
        $hook = new Hook('hookName', []);

        // stub rendering hook event behavior
        $hookEvent = $this->createRenderingHookEvent($hook->getParameters());

        // stub adapter expectations
        $this->hookDispatcherAdapter
            ->expects($this->once())
            ->method('renderForParameters')
            ->with(
                $hook->getName(),
                $hook->getParameters()
            )
            ->willReturn($hookEvent);

        $renderedHook = $this->hookDispatcher->dispatchRendering($hook);

        $this->assertInstanceOf(RenderedHook::class, $renderedHook);
        $this->assertEquals($hook, $renderedHook->getHook());
        $this->assertEquals([], $renderedHook->getContent());
    }

    public function testDispatchRenderingWithParameters()
    {
        $hook = new Hook('Baz', ['hello' => 'World']);

        // stub rendering hook event behavior
        $hookEvent = $this->createRenderingHookEvent($hook->getParameters());

        // stub adapter expectations
        $this->hookDispatcherAdapter
            ->expects($this->once())
            ->method('renderForParameters')
            ->with(
                $hook->getName(),
                $hook->getParameters()
            )
            ->willReturn($hookEvent);

        $renderedHook = $this->hookDispatcher->dispatchRenderingWithParameters('Baz', ['hello' => 'World']);

        $this->assertInstanceOf(RenderedHook::class, $renderedHook);
        $this->assertEquals($hook, $renderedHook->getHook());
        $this->assertEquals(['hello' => 'World'], $renderedHook->getContent());
    }

    private function createHook(string $name = 'hookName', array $parameters = []): HookInterface
    {
        $hookStub = $this->createMock(HookInterface::class);

        $hookStub->method('getName')->willReturn($name);
        $hookStub->method('getParameters')->willReturn($parameters);

        return $hookStub;
    }

    /**
     * The event dispatcher puts every parameter dispatched in an array.
     *
     * @param array $parameters
     */
    private function createRenderingHookEvent(array $parameters = []): RenderingHookEvent
    {
        $parametersDispatched = [];
        foreach ($parameters as $key => $parameter) {
            $parametersDispatched[$key] = empty($parameter) ? [] : [$parameter];
        }

        $renderingHookEventStub = $this->createMock(RenderingHookEvent::class);
        $renderingHookEventStub->method('getContent')->willReturn($parametersDispatched);

        return $renderingHookEventStub;
    }
}
