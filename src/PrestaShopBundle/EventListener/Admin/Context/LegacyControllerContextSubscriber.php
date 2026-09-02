<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\Admin\Context;

use PrestaShop\PrestaShop\Core\Context\LegacyControllerContextBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Listener dedicated to set up LegacyController context for the Back-Office/Admin application.
 *
 * The LegacyControllerContext is a context used for backward compatible reasons, we want to get rid
 * of the legacy Context singleton dependency, but many hooks and code still depend on it, so we propose
 * this alternative dedicated context that is mostly useful for legacy pages.
 */
class LegacyControllerContextSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly LegacyControllerContextBuilder $legacyControllerContextBuilder,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest'],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $controllerName = $this->getControllerName($event->getRequest());
        $this->legacyControllerContextBuilder->setControllerName($controllerName);

        // Optional redirection url
        if ($event->getRequest()->query->has('back')) {
            $this->legacyControllerContextBuilder->setRedirectionUrl($event->getRequest()->query->get('back'));
        }
    }

    private function getControllerName(?Request $request): string
    {
        $controllerName = 'AdminController';

        if ($request->attributes->has('_legacy_controller')) {
            $controllerName = $request->attributes->get('_legacy_controller');
        } elseif ($request->query->has('controller')) {
            $controllerName = $request->query->get('controller');
        }

        return $controllerName;
    }
}
