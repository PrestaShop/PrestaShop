<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin;

use DateTimeImmutable;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Email\Generator\CodeGeneratorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class TwoFactorResendController extends PrestaShopAdminController
{
    public function __construct(
        private readonly Security $security,
        #[Autowire(service: 'limiter.two_factor_resend')]
        private readonly RateLimiterFactory $twoFactorResendLimiter,
        #[Autowire(service: 'scheb_two_factor.security.email.code_generator')]
        private readonly ?CodeGeneratorInterface $codeGenerator = null,
    ) {
    }

    public function resend(Request $request): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('2fa_resend', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->security->getUser();
        if (!$user instanceof TwoFactorInterface || null === $this->codeGenerator) {
            throw $this->createAccessDeniedException($this->trans('Email 2FA is not enabled for this user.', [], 'Admin.TwoFactor.Login'));
        }

        $limit = $this->twoFactorResendLimiter->create($this->getRateLimitKey($request, $user))->consume();
        if (!$limit->isAccepted()) {
            $retryAfterInSeconds = max(1, $limit->getRetryAfter()->getTimestamp() - (new DateTimeImmutable())->getTimestamp());

            $this->addFlash(
                'error',
                $this->trans(
                    'Too many requests for a new authentication code. Please wait %seconds% seconds before trying again.',
                    ['%seconds%' => $retryAfterInSeconds],
                    'Admin.TwoFactor.Login'
                )
            );

            return $this->redirectToRoute('2fa_login');
        }

        $this->codeGenerator->generateAndSend($user);

        $this->addFlash('success', $this->trans('Code resent. Please check your spam folder as well.', [], 'Admin.TwoFactor.Login'));

        return $this->redirectToRoute('2fa_login');
    }

    private function getRateLimitKey(Request $request, TwoFactorInterface $user): string
    {
        $userId = method_exists($user, 'getId') ? (string) $user->getId() : 'unknown-user';
        $ipAddress = $request->getClientIp() ?? 'unknown-ip';

        return $userId . ':' . $ipAddress;
    }
}
