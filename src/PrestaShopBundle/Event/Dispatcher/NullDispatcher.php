<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Event\Dispatcher;

use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Hook\HookInterface;
use PrestaShop\PrestaShop\Core\Hook\RenderedHookInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NullDispatcher implements EventDispatcherInterface, HookDispatcherInterface
{
    public function addListener($eventName, $listener, $priority = 0)
    {
    }

    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
    }

    /**
     * @param object $event
     * @param string|null $eventName
     *
     * @return object
     */
    public function dispatch(object $event, ?string $eventName = null): object
    {
        return $event;
    }

    /**
     * @param null $eventName
     */
    public function getListeners($eventName = null): array
    {
        return [];
    }

    /**
     * @param null $eventName
     */
    public function hasListeners($eventName = null): bool
    {
        return false;
    }

    public function removeListener($eventName, $listener)
    {
    }

    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
    }

    /**
     * @param string $eventName
     * @param callable $listener
     */
    public function getListenerPriority($eventName, $listener): ?int
    {
        return null;
    }

    public function dispatchHook(HookInterface $hook)
    {
    }

    public function dispatchWithParameters($hookName, array $hookParameters = [])
    {
    }

    /**
     * @param HookInterface $hook
     *
     * @return RenderedHookInterface|void
     */
    public function dispatchRendering(HookInterface $hook)
    {
    }

    /**
     * @param string $hookName
     * @param array $hookParameters
     *
     * @return RenderedHookInterface|void
     */
    public function dispatchRenderingWithParameters($hookName, array $hookParameters = [])
    {
    }
}
