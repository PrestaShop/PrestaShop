<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin;

use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Email\Generator\CodeGenerator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class TwoFactorResendController extends PrestaShopAdminController
{
    public function __construct(
        private readonly Security $security,
        #[Autowire(service: 'scheb_two_factor.security.email.code_generator')]
        private readonly CodeGenerator $codeGenerator,
    ) {
    }

    public function resend(Request $request): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('2fa_resend', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->security->getUser();
        if (!$user instanceof TwoFactorInterface) {
            throw $this->createAccessDeniedException($this->trans('Email 2FA is not enabled for this user.', [], 'Admin.TwoFactor.Login'));
        }

        $this->codeGenerator->reSend($user);

        $this->addFlash('success', $this->trans('Code resent. Please check your spam folder as well.', [], 'Admin.TwoFactor.Login'));

        return $this->redirectToRoute('2fa_login');
    }
}
