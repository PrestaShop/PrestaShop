<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Order\Refund;

use CartRule;
use Customer;
use Language;
use Mail;
use Order;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShopDatabaseException;
use PrestaShopException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class VoucherGenerator is responsible of generating a voucher for a customer
 * for an order refund.
 */
class VoucherGenerator
{
    /**
     * @var Locale
     */
    private $locale;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param Locale $locale
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Locale $locale,
        TranslatorInterface $translator
    ) {
        $this->locale = $locale;
        $this->translator = $translator;
    }

    /**
     * @param Order $order
     * @param float $voucherAmount
     * @param string $currencyIsoCode
     * @param bool $isTaxIncluded
     *
     * @throws OrderException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws LocalizationException
     */
    public function generateVoucher(
        Order $order,
        float $voucherAmount,
        string $currencyIsoCode,
        bool $isTaxIncluded
    ) {
        $cartRule = new CartRule();
        $cartRule->description = $this->translator->trans(
            'Credit slip for order #%d',
            ['#%d' => $order->id],
            'Admin.Orderscustomers.Feature'
        );

        $langIds = Language::getIDs(false);
        foreach ($langIds as $langId) {
            // Define a temporary name
            $cartRule->name[$langId] = sprintf('V0C%1$dO%2$d', $order->id_customer, $order->id);
        }

        // Define a temporary code
        $cartRule->code = sprintf('V0C%1$dO%2$d', $order->id_customer, $order->id);
        $cartRule->quantity = 1;
        $cartRule->quantity_per_user = 1;

        // Specific to the customer
        $cartRule->id_customer = $order->id_customer;
        $now = time();
        $cartRule->date_from = date('Y-m-d H:i:s', $now);
        $cartRule->date_to = date('Y-m-d H:i:s', strtotime('+1 year'));
        $cartRule->partial_use = true;
        $cartRule->active = true;

        $cartRule->reduction_amount = $voucherAmount;
        $cartRule->reduction_tax = $isTaxIncluded;
        $cartRule->minimum_amount_currency = $order->id_currency;
        $cartRule->reduction_currency = $order->id_currency;

        if (!$cartRule->add()) {
            throw new OrderException('You cannot generate a voucher.');
        }

        // Update the voucher code and name
        foreach ($langIds as $langId) {
            $cartRule->name[$langId] = sprintf('V%1$dC%2$dO%3$d', $cartRule->id, $order->id_customer, $order->id);
        }

        $cartRule->code = sprintf('V%1$dC%2$dO%3$d', $cartRule->id, $order->id_customer, $order->id);

        if (!$cartRule->update()) {
            throw new OrderException('You cannot generate a voucher.');
        }

        $customer = new Customer((int) ($order->id_customer));

        $params = [
            '{lastname}' => $customer->lastname,
            '{firstname}' => $customer->firstname,
            '{id_order}' => $order->id,
            '{order_name}' => $order->getUniqReference(),
            '{voucher_amount}' => $this->locale->formatPrice($cartRule->reduction_amount, $currencyIsoCode),
            '{voucher_num}' => $cartRule->code,
        ];

        // @todo: use private method to send mail and later a decoupled mail sender
        $orderLanguage = $order->getAssociatedLanguage();

        @Mail::Send(
            (int) $orderLanguage->getId(),
            'voucher',
            $this->translator->trans(
                'New voucher for your order #%s',
                [$order->reference],
                'Emails.Subject',
                $orderLanguage->locale
            ),
            $params,
            $customer->email,
            $customer->firstname . ' ' . $customer->lastname,
            null,
            null,
            null,
            null,
            _PS_MAIL_DIR_,
            true,
            (int) $order->id_shop
        );
    }
}
