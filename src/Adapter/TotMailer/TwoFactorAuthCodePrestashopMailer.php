<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\TotMailer;

use Mail;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Translation\TranslatorInterface;
use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;

final class TwoFactorAuthCodePrestashopMailer implements AuthCodeMailerInterface
{
    public function __construct(
        private readonly ShopContext $shopContext,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @param Employee $user
     */
    public function sendAuthCode(TwoFactorInterface $user): void
    {
        $templateVars = [
            '{auth_code}' => $user->getEmailAuthCode(),
            '{firstname}' => $user->getFirstName(),
            '{lastname}' => $user->getLastName(),
        ];

        Mail::Send(
            $user->getDefaultLanguage()->getId(),
            'two_factor_auth_code',
            $this->translator->trans('Your authentication code', [], 'Admin.Advparameters.Feature'),
            $templateVars,
            $user->getEmailAuthRecipient(),
            null,
            null,
            null,
            null,
            null,
            _PS_MAIL_DIR_,
            false,
            $this->shopContext->getId(),
            null,
            null
        );
    }
}
