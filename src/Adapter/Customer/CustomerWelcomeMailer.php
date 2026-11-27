<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer;

use Customer;
use InvalidArgumentException;
use Link;
use LogicException;
use Mail;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Customer\CustomerAccountOrigin;
use PrestaShopException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Sends the welcome email to the customer.
 */
class CustomerWelcomeMailer
{
    private readonly Link $link;

    /**
     * @throws InvalidArgumentException if link is not set in the given context
     */
    public function __construct(
        private readonly TranslatorInterface $translator,
        LegacyContext $context,
    ) {
        if (null === $link = $context->getContext()->link) {
            throw new InvalidArgumentException('Expected link to be set in the context.');
        }

        $this->link = $link;
    }

    /**
     * @throws PrestaShopException
     * @throws LogicException if the given origin is not supported
     */
    public function send(Customer $customer, CustomerAccountOrigin $origin): bool
    {
        $language = $customer->getAssociatedLanguage();

        return (bool) Mail::send(
            (int) $language->id,
            $this->getTemplate($origin),
            $this->getSubject($origin, $language->locale),
            $this->getTemplateVariables($customer, $origin),
            $customer->email,
            $customer->firstname . ' ' . $customer->lastname,
            idShop: (int) $customer->id_shop,
        );
    }

    /**
     * @throws LogicException
     */
    private function getTemplate(CustomerAccountOrigin $origin): string
    {
        return match ($origin) {
            CustomerAccountOrigin::BACK_OFFICE => 'backoffice_account',
            default => throw new LogicException('Not implemented'),
        };
    }

    /**
     * @throws LogicException
     */
    private function getSubject(CustomerAccountOrigin $origin, ?string $locale): string
    {
        return match ($origin) {
            CustomerAccountOrigin::BACK_OFFICE => $this->translator->trans('Set up your password', [], 'Emails.Subject', $locale),
            default => throw new LogicException('Not implemented'),
        };
    }

    /**
     * @throws PrestaShopException
     */
    private function getTemplateVariables(Customer $customer, CustomerAccountOrigin $origin): array
    {
        $variables = [
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
            '{email}' => $customer->email,
        ];

        if ($origin->wasPasswordSetByCustomer()) {
            return $variables;
        }

        $langId = (int) $customer->getAssociatedLanguage()->id;
        $variables['{url}'] = $this->link->getPageLink('password', true, $langId, [
            'token' => $customer->secure_key,
            'id_customer' => (int) $customer->id,
            'reset_token' => $customer->reset_password_token,
        ], idShop: (int) $customer->id_shop);

        return $variables;
    }
}
