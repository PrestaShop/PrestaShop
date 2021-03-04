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

namespace PrestaShopBundle\EventListener;

use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @todo Extract logic outside of EventSubscriber
 */
class ActionDispatcherLegacyHooksSubscriber implements EventSubscriberInterface
{
    public const DISPATCHER_BEFORE_ACTION = 'actionDispatcherBefore';
    public const DISPATCHER_AFTER_ACTION = 'actionDispatcherAfter';

    /**
     * List of available front controllers types.
     */
    public const FRONT_OFFICE_CONTROLLER = 1;
    public const BACK_OFFICE_CONTROLLER = 2;
    public const MODULE_CONTROLLER = 3;
    public const NA_CONTROLLER = 0;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    public function __construct(HookDispatcherInterface $hookDispatcher)
    {
        $this->hookDispatcher = $hookDispatcher;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => [
                ['callActionDispatcherBeforeHook', 100],
            ],
            KernelEvents::RESPONSE => [
                ['callActionDispatcherAfterHook', 255],
            ],
        ];
    }

    public function callActionDispatcherBeforeHook(FilterControllerEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $requestAttributes = $event->getRequest()->attributes;
        $controllerType = self::NA_CONTROLLER;
        $controller = $event->getController()[0];

        if ($controller instanceof FrameworkBundleAdminController) {
            $controllerType = self::BACK_OFFICE_CONTROLLER;
        }

        $this->hookDispatcher->dispatchWithParameters(self::DISPATCHER_BEFORE_ACTION, [
            'controller_type' => $controllerType,
        ]);

        $requestAttributes->set('controller_type', $controllerType);
        $requestAttributes->set('controller_name', get_class($controller));
    }

    public function callActionDispatcherAfterHook(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $requestAttributes = $event->getRequest()->attributes;

        if ($requestAttributes->has('controller_type') && $requestAttributes->has('controller_name')) {
            $this->hookDispatcher->dispatchWithParameters(self::DISPATCHER_AFTER_ACTION, [
                'controller_type' => $requestAttributes->get('controller_type'),
                'controller_class' => $requestAttributes->get('controller_name'),
                'is_module' => 0,
            ]);
        }
    }
}
