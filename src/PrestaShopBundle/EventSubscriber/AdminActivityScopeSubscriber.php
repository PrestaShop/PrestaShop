<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventSubscriber;

use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Service\Log\AdminActivityScope;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Marks Back Office requests so activity logging can be enabled only for BO operations.
 */
class AdminActivityScopeSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $controller = $event->getController();
        $controllerInstance = is_array($controller) ? $controller[0] : $controller;

        // Activity logging is enabled only for main requests handled by a Back Office controller.
        if (!$controllerInstance instanceof PrestaShopAdminController) {
            return;
        }

        $event->getRequest()->attributes->set(
            AdminActivityScope::REQUEST_ATTRIBUTE,
            true
        );
    }
}
