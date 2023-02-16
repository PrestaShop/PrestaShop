<?php

namespace PrestaShopBundle\Hook;

use PrestaShop\PrestaShop\Core\Hook\Hook;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Hook\HookInterface;
use PrestaShop\PrestaShop\Core\Hook\RenderedHook;
use PrestaShop\PrestaShop\Core\Version;
use PrestaShopBundle\Service\Hook\RenderingHookEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SymfonyEventHookDispatcher implements HookDispatcherInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(EventDispatcherInterface $eventDispatcher, RequestStack $requestStack)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
    }

    public function dispatchHook(HookInterface $hook)
    {
        $event = new HookEvent($hook);
        $this->eventDispatcher->dispatch($event, $hook->getName());
    }

    public function dispatchWithParameters($hookName, array $hookParameters = [])
    {
        $this->dispatchHook(new Hook($hookName, $hookParameters));
    }

    public function dispatchRendering(HookInterface $hook)
    {
        $event = $this->renderForParameters(
            $hook->getName(),
            $hook->getParameters()
        );

        return new RenderedHook($hook, $event->getContent());
    }

    public function dispatchRenderingWithParameters($hookName, array $hookParameters = [])
    {
        return $this->dispatchRendering(new Hook($hookName, $hookParameters));
    }


    public function addListener($eventName, $listener, $priority = 0)
    {
        throw new \RuntimeException();
    }

    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        throw new \RuntimeException();
    }

    public function removeListener($eventName, $listener)
    {
        throw new \RuntimeException();
    }

    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        throw new \RuntimeException();
    }

    public function getListeners($eventName = null)
    {
        throw new \RuntimeException();
    }

    public function dispatch($event)
    {
        throw new \RuntimeException();
    }

    public function getListenerPriority($eventName, $listener)
    {
        throw new \RuntimeException();
    }

    public function hasListeners($eventName = null)
    {
        throw new \RuntimeException();
    }

    private function renderForParameters($eventName, array $parameters = [])
    {
        $event = new RenderingHookEvent($this->getHookEventContextParameters());
        $event->setHookParameters($parameters);

        /** @var RenderingHookEvent $eventDispatched */
        $eventDispatched = $this->eventDispatcher->dispatch($event, $eventName);

        return $eventDispatched;
    }

    /**
     * @return array
     *
     * Returns context parameters that will be injected into the new HookEvent
     *
     * Note: _ps_version contains PrestaShop version, and is here only if the Hook is triggered by Symfony architecture
     */
    private function getHookEventContextParameters(): array
    {
        $globalParameters = ['_ps_version' => Version::VERSION];

        if (null === $this->requestStack) {
            return $globalParameters;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $globalParameters;
        }

        $globalParameters['request'] = $request;
        $globalParameters['route'] = $request->attributes->get('_route');

        return $globalParameters;
    }
}
