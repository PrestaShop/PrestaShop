<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\WithdrawalRequest;

use Configuration;
use Context;
use Language;
use Mail;
use PrestaShopBundle\Entity\WithdrawalRequest;

/**
 * Sends the emails of the withdrawal function:
 *  - the acknowledgement of receipt on a durable medium, required by Directive 2011/83/EU, article 11a.3: it holds
 *    the content of the withdrawal statement and the date and time it was submitted;
 *  - the notification of the merchant.
 */
final class WithdrawalRequestNotifier
{
    public const ACKNOWLEDGEMENT_TEMPLATE = 'withdrawal_acknowledgement';
    public const MERCHANT_NOTIFICATION_TEMPLATE = 'withdrawal_request';

    /**
     * @return bool whether the acknowledgement was accepted for delivery
     */
    public function sendAcknowledgement(WithdrawalRequest $withdrawalRequest, int $languageId): bool
    {
        $language = new Language($languageId);
        if (!$language->id) {
            $languageId = (int) Configuration::get('PS_LANG_DEFAULT', null, null, $withdrawalRequest->getShopId());
            $language = new Language($languageId);
        }

        $subject = Context::getContext()->getTranslator()->trans(
            'Acknowledgement of receipt of your withdrawal',
            [],
            'Emails.Subject',
            $language->locale
        );

        return (bool) Mail::send(
            $languageId,
            self::ACKNOWLEDGEMENT_TEMPLATE,
            $subject,
            $this->getTemplateVars($withdrawalRequest, $language),
            $withdrawalRequest->getEmail(),
            $withdrawalRequest->getFirstname() . ' ' . $withdrawalRequest->getLastname(),
            null,
            null,
            null,
            null,
            _PS_MAIL_DIR_,
            false,
            $withdrawalRequest->getShopId()
        );
    }

    /**
     * @return bool whether the notification was accepted for delivery
     */
    public function notifyMerchant(WithdrawalRequest $withdrawalRequest): bool
    {
        $shopId = $withdrawalRequest->getShopId();
        $merchantEmail = (string) Configuration::get('PS_SHOP_EMAIL', null, null, $shopId);
        if ('' === $merchantEmail) {
            return false;
        }

        $languageId = (int) Configuration::get('PS_LANG_DEFAULT', null, null, $shopId);
        $language = new Language($languageId);

        $subject = Context::getContext()->getTranslator()->trans(
            'New withdrawal request for order %reference%',
            ['%reference%' => $withdrawalRequest->getOrderReference()],
            'Emails.Subject',
            $language->locale
        );

        return (bool) Mail::send(
            $languageId,
            self::MERCHANT_NOTIFICATION_TEMPLATE,
            $subject,
            $this->getTemplateVars($withdrawalRequest, $language),
            $merchantEmail,
            (string) Configuration::get('PS_SHOP_NAME', null, null, $shopId),
            null,
            null,
            null,
            null,
            _PS_MAIL_DIR_,
            false,
            $shopId,
            null,
            $withdrawalRequest->getEmail(),
            $withdrawalRequest->getFirstname() . ' ' . $withdrawalRequest->getLastname()
        );
    }

    /**
     * @return array<string, string>
     */
    private function getTemplateVars(WithdrawalRequest $withdrawalRequest, Language $language): array
    {
        $dateFormat = $language->date_format_full ?: 'Y-m-d H:i:s';

        return [
            '{firstname}' => $withdrawalRequest->getFirstname(),
            '{lastname}' => $withdrawalRequest->getLastname(),
            '{email}' => $withdrawalRequest->getEmail(),
            '{order_name}' => $withdrawalRequest->getOrderReference(),
            '{id_order}' => (string) $withdrawalRequest->getOrderId(),
            '{withdrawal_date}' => $withdrawalRequest->getSubmittedAt()->format($dateFormat),
            '{withdrawal_timezone}' => $withdrawalRequest->getSubmittedAt()->format('T'),
            '{message}' => (string) $withdrawalRequest->getMessage(),
        ];
    }
}
