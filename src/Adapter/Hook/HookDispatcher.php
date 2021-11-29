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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Hook;

use PrestaShop\PrestaShop\Core\Hook\Hook;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Hook\HookInterface;
use PrestaShop\PrestaShop\Core\Hook\RenderedHook;
use PrestaShopBundle\Service\Hook\HookEvent;
use PrestaShopBundle\Service\Hook\RenderingHookEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This dispatcher is used to trigger hook listeners.
 *
 * The dispatch process cannot be stopped like a common EventDispatcher.
 *
 * If the event is a RenderingHookEvent, then the final result is
 * an array of contents accessed from $event->getContent().
 */
class HookDispatcher extends EventDispatcher implements HookDispatcherInterface
{
    /**
     * @var array
     */
    private $renderingContent = [];

    /**
     * @var bool|callable
     */
    private $propagationStoppedCalledBy = false;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack (nullable to preserve backward compatibility)
     */
    public function __construct(RequestStack $requestStack = null)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     * This override will check if $event is an instance of HookEvent.
     *
     * @throws \Exception if the Event is not HookEvent or a subclass
     */
    public function dispatch($eventName, Event $event = null)
    {
        if ($event === null) {
            $event = new HookEvent($this->getHookEventContextParameters());
        }

        if (!$event instanceof HookEvent) {
            throw new \Exception('HookDispatcher must dispatch a HookEvent subclass only. ' . get_class($event) . ' given.');
        }

        if ($listeners = $this->getListeners(strtolower($eventName))) {
            $this->doDispatch($listeners, $eventName, $event);
        }

        return $event;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchHook(HookInterface $hook)
    {
        return $this->dispatchForParameters(
            $hook->getName(),
            $hook->getParameters()
        );
    }

    /**
     * Calls multiple hooks with the same parameter set.
     *
     * Each event is independent for each hook call. Parameter set is duplicated.
     *
     * @param array $eventNames the hooks to dispatch to
     * @param array $eventParameters the parameters set to insert in each HookEvent instance
     *
     * @throws \Exception if the Event is not HookEvent or a subclass
     */
    public function dispatchMultiple(array $eventNames, array $eventParameters)
    {
        foreach ($eventNames as $name) {
            $this->dispatch(
                $name,
                (new HookEvent($this->getHookEventContextParameters()))->setHookParameters($eventParameters)
            );
        }
    }

    /**
     * {@inheritdoc}
     * This override will avoid PropagationStopped to break the dispatching process.
     * After dispatch, in case of RenderingHookEvent, the final content array will be set in event.
     */
    protected function doDispatch($listeners, $eventName, Event $event)
    {
        $this->propagationStoppedCalledBy = false;
        foreach ($listeners as $listener) {
            // removes $this to parameters. Hooks should not have access to dispatcher
            ob_start();
            $listener($event, $eventName, null);
            $obContent = ob_get_clean();

            if ($event instanceof RenderingHookEvent) {
                $listenerName = $event->popListener() ?: $listener[1];

                $this->renderingContent[$listenerName] = $event->popContent();
            }
            if ($event->isPropagationStopped()) {
                $this->propagationStoppedCalledBy = $listener;
            }
        }
        if ($event instanceof RenderingHookEvent) {
            $event->setContent($this->renderingContent);
            $this->renderingContent = [];
        }
    }

    /**
     * Creates a HookEvent, sets its parameters, and dispatches it.
     *
     * @param string $eventName The hook name
     * @param array $parameters Hook parameters
     *
     * @return Event the event that has been passed to each listener
     *
     * @throws \Exception
     */
    public function dispatchForParameters($eventName, array $parameters = [])
    {
        $event = new HookEvent($this->getHookEventContextParameters());
        $event->setHookParameters($parameters);

        return $this->dispatch($eventName, $event);
    }

    /**
     * Creates a RenderingHookEvent, sets its parameters, and dispatches it. Returns the event with the response(s).
     *
     * @param string $eventName the hook name
     * @param array $parameters Hook parameters
     *
     * @return RenderingHookEvent The event that has been passed to each listener. Contains the responses.
     *
     * @throws \Exception
     */
    public function renderForParameters($eventName, array $parameters = [])
    {
        $event = new RenderingHookEvent($this->getHookEventContextParameters());
        $event->setHookParameters($parameters);

        /** @var RenderingHookEvent $eventDispatched */
        $eventDispatched = $this->dispatch($eventName, $event);

        return $eventDispatched;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchWithParameters($hookName, array $hookParameters = [])
    {
        $this->dispatch(new Hook($hookName, $hookParameters));
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchRendering(HookInterface $hook)
    {
        $event = $this->renderForParameters(
            $hook->getName(),
            $hook->getParameters()
        );

        return new RenderedHook($hook, $event->getContent());
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchRenderingWithParameters($hookName, array $hookParameters = [])
    {
        return $this->dispatchRendering(new Hook($hookName, $hookParameters));
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
        $globalParameters = ['_ps_version' => \AppKernel::VERSION];

        if (null === $this->requestStack) {
            return $globalParameters;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $globalParameters;
        }

        $globalParameters['request'] = $request;
        $globalParameters['route'] = $request->get('_route');

        return $globalParameters;
    }
}
