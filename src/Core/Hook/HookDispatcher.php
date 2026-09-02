<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Hook;

use PrestaShop\PrestaShop\Adapter\Hook\HookDispatcher as HookDispatcherAdapter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class HookDispatcher is responsible for dispatching hooks.
 */
final class HookDispatcher implements HookDispatcherInterface
{
    /**
     * @var HookDispatcherAdapter
     */
    private $hookDispatcherAdapter;

    /**
     * @param HookDispatcherAdapter $hookDispatcherAdapter
     */
    public function __construct(HookDispatcherAdapter $hookDispatcherAdapter)
    {
        $this->hookDispatcherAdapter = $hookDispatcherAdapter;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchHook(HookInterface $hook)
    {
        $this->hookDispatcherAdapter->dispatchForParameters(
            $hook->getName(),
            $hook->getParameters()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchWithParameters($hookName, array $hookParameters = [])
    {
        $this->dispatchHook(new Hook($hookName, $hookParameters));
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchRendering(HookInterface $hook)
    {
        $event = $this->hookDispatcherAdapter->renderForParameters(
            $hook->getName(),
            $hook->getParameters()
        );

        $content = $event->getContent();
        array_walk($content, function (&$partialContent) {
            $partialContent = empty($partialContent) ? '' : current($partialContent);
        });

        return new RenderedHook($hook, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchRenderingWithParameters($hookName, array $hookParameters = [])
    {
        return $this->dispatchRendering(new Hook($hookName, $hookParameters));
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(object $event, ?string $eventName = null): object
    {
        return $this->hookDispatcherAdapter->dispatch($event, $eventName);
    }

    /**
     * {@inheritdoc}
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->hookDispatcherAdapter->addListener($eventName, $listener, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->hookDispatcherAdapter->addSubscriber($subscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function removeListener($eventName, $listener)
    {
        $this->hookDispatcherAdapter->removeListener($eventName, $listener);
    }

    /**
     * {@inheritdoc}
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->hookDispatcherAdapter->removeSubscriber($subscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners($eventName = null)
    {
        return $this->hookDispatcherAdapter->getListeners($eventName);
    }

    /**
     * {@inheritdoc}
     */
    public function getListenerPriority($eventName, $listener)
    {
        return $this->hookDispatcherAdapter->getListenerPriority($eventName, $listener);
    }

    /**
     * {@inheritdoc}
     */
    public function hasListeners($eventName = null)
    {
        return $this->hookDispatcherAdapter->hasListeners($eventName);
    }
}
