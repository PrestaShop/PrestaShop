<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\TotMailer;

use Mail;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeTwoFactorException;
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

        $sent = Mail::Send(
            $user->getDefaultLanguage()->getId(),
            'two_factor_auth_code',
            $this->translator->trans('Your authentication code', [], 'Emails.Subject'),
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
        if (false === $sent) {
            throw new EmployeeTwoFactorException('The authentication email could not be sent.', EmployeeTwoFactorException::EMAIL_DELIVERY_FAILED);
        }
    }
}
