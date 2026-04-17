<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\EventListener;

use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
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

    public function callActionDispatcherBeforeHook(ControllerEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $requestAttributes = $event->getRequest()->attributes;
        $controllerType = self::NA_CONTROLLER;
        $controller = is_array($event->getController())
            ? $event->getController()[0]
            : $event->getController()
        ;

        if ($controller instanceof FrameworkBundleAdminController || $controller instanceof PrestaShopAdminController) {
            $controllerType = self::BACK_OFFICE_CONTROLLER;
        }

        $this->hookDispatcher->dispatchWithParameters(self::DISPATCHER_BEFORE_ACTION, [
            'controller_type' => $controllerType,
        ]);

        $requestAttributes->set('controller_type', $controllerType);
        $requestAttributes->set('controller_name', $controller::class);
    }

    public function callActionDispatcherAfterHook(ResponseEvent $event)
    {
        if (!$event->isMainRequest()) {
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
