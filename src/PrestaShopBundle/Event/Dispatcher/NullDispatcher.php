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

namespace PrestaShopBundle\Event\Dispatcher;

use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Hook\HookInterface;
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

    public function dispatch($eventName, \Symfony\Component\EventDispatcher\Event $event = null)
    {
    }

    public function getListeners($eventName = null)
    {
    }

    public function hasListeners($eventName = null)
    {
    }

    public function removeListener($eventName, $listener)
    {
    }

    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
    }

    public function getListenerPriority($eventName, $listener)
    {
    }

    public function dispatchHook(HookInterface $hook)
    {
    }

    public function dispatchWithParameters($hookName, array $hookParameters = [])
    {
    }

    public function dispatchRendering(HookInterface $hook)
    {
    }

    public function dispatchRenderingWithParameters($hookName, array $hookParameters = [])
    {
    }
}
