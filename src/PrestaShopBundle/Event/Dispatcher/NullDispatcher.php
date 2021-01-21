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

namespace PrestaShopBundle\Event\Dispatcher;

use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Hook\HookInterface;
use PrestaShop\PrestaShop\Core\Hook\RenderedHookInterface;
use Symfony\Component\EventDispatcher\Event;
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
     * @param string $eventName
     * @param Event|null $event
     *
     * @return Event|void
     */
    public function dispatch($eventName, Event $event = null)
    {
    }

    /**
     * @param null $eventName
     *
     * @return array|void
     */
    public function getListeners($eventName = null)
    {
    }

    /**
     * @param null $eventName
     *
     * @return bool|void
     */
    public function hasListeners($eventName = null)
    {
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
     *
     * @return int|void|null
     */
    public function getListenerPriority($eventName, $listener)
    {
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
