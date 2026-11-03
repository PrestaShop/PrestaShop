<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer;

use Customer;
use Link;
use Mail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Sends the customer an email with a link to set up the password of their account.
 */
final class PasswordSetupLinkMailer
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        #[Autowire(expression: 'service("prestashop.adapter.legacy.context").getContext().link')]
        private readonly Link $link,
    ) {
    }

    public function send(Customer $customer): bool
    {
        $language = $customer->getAssociatedLanguage();
        $langId = (int) $language->id;
        $shopId = (int) $customer->id_shop;

        $url = $this->link->getPageLink('password', true, $langId, [
            'token' => $customer->secure_key,
            'id_customer' => (int) $customer->id,
            'reset_token' => $customer->reset_password_token,
        ], idShop: $shopId);

        return (bool) Mail::send(
            $langId,
            'backoffice_account',
            $this->translator->trans('Set up your password', [], 'Emails.Subject', $language->locale),
            [
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
                '{url}' => $url,
            ],
            $customer->email,
            $customer->firstname . ' ' . $customer->lastname,
            idShop: $shopId,
        );
    }
}
