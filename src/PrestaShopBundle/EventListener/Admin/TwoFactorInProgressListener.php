<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\Admin;

use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

final class TwoFactorInProgressListener
{
    use TargetPathTrait;

    private const ALLOWED_ROUTES = [
        '2fa_login',
        '2fa_login_check',
        '2fa_resend',
        'admin_logout',
    ];

    public function __construct(
        private readonly Security $security,
        private readonly RouterInterface $router,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $token = $this->security->getToken();
        if (!$token instanceof TwoFactorTokenInterface) {
            return;
        }

        $route = (string) $event->getRequest()->attributes->get('_route');
        if (in_array($route, self::ALLOWED_ROUTES, true)) {
            return;
        }

        $request = $event->getRequest();
        if ($request->hasSession() && $request->isMethodSafe() && !$request->isXmlHttpRequest()) {
            $this->saveTargetPath($request->getSession(), 'main', $request->getUri());
        }

        $event->setResponse(new RedirectResponse($this->router->generate('2fa_login')));
        $event->stopPropagation();
    }
}
