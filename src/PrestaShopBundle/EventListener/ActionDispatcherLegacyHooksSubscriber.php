<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\EventListener;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Service\Hook\HookDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use PrestaShopBundle\Service\Hook\HookEvent;

class ActionDispatcherLegacyHooksSubscriber implements EventSubscriberInterface
{
    const DISPATCHER_BEFORE_ACTION = 'actionDispatcherBefore';
    const DISPATCHER_AFTER_ACTION = 'actionDispacterAfter';

    /**
     * List of available front controllers types
     */
    const FRONT_OFFICE_CONTROLLER = 1;
    const BACK_OFFICE_CONTROLLER = 2;
    const MODULE_CONTROLLER = 3;
    const NA_CONTROLLER = 0;

    /**
     * @var HookDispatcher
     */
    private $hookDispacher;

    public function __construct(HookDispatcher $hookDispatcher)
    {
        $this->hookDispacher = $hookDispatcher;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array (
                'callActionDispatcherBeforeHook',
            ),
            KernelEvents::RESPONSE => array(
                'callActionDispatcherAfterHook',
            ),
        );
    }

    public function callActionDispatcherBeforeHook(FilterControllerEvent $event)
    {
        $controllerType = self::NA_CONTROLLER;
        $controller = $event->getController()[0];

        if($controller instanceof FrameworkBundleAdminController) {
            $controllerType = self::BACK_OFFICE_CONTROLLER;
        }
        $hookEvent = new HookEvent();
        $hookEvent->setHookParameters(array(
            'controller_type' => $controllerType
        ));

        $this->hookDispacher->dispatch(self::DISPATCHER_BEFORE_ACTION, $hookEvent);
    }

    public function callActionDispatcherAfterHook(FilterResponseEvent $event)
    {

    }
}
