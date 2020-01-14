<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use CartRule;
use Configuration;
use Context;
use Customer;
use Hook;
use Language;
use Mail;
use OrderCarrier;
use OrderDetail;
use OrderSlip;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderRefundCalculator;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderRefundDetail;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssuePartialRefundCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\IssuePartialRefundHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\EmptyRefundAmountException;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use StockAvailable;
use Symfony\Component\Translation\TranslatorInterface;
use Validate;

/**
 * @internal
 */
final class IssuePartialRefundHandler extends AbstractOrderCommandHandler implements IssuePartialRefundHandlerInterface
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
     * @var OrderRefundCalculator
     */
    private $orderRefundCalculator;

    /**
     * @param Locale $locale
     * @param TranslatorInterface $translator
     * @param OrderRefundCalculator $orderRefundCalculator
     */
    public function __construct(
        Locale $locale,
        TranslatorInterface $translator,
        OrderRefundCalculator $orderRefundCalculator
    ) {
        $this->locale = $locale;
        $this->translator = $translator;
        $this->orderRefundCalculator = $orderRefundCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(IssuePartialRefundCommand $command)
    {
        $order = $this->getOrderObject($command->getOrderId());
        /** @var OrderRefundDetail $orderRefundDetail */
        $orderRefundDetail = $this->orderRefundCalculator->computeOrderFund(
            $order,
            $command->getOrderDetailRefunds(),
            $command->getShippingCostRefundAmount(),
            $command->getVoucherRefundType(),
            $command->getVoucherRefundAmount()
        );

        // Reinject quantity
        if (!$order->hasBeenDelivered() || $command->restockRefundedProducts()) {
            foreach ($orderRefundDetail->getProductRefunds() as $orderDetailId => $productRefund) {
                $this->reinjectQuantity($orderRefundDetail->getOrderDetailById($orderDetailId), $productRefund['quantity']);
            }
        }

        // Update order carrier weight
        $orderCarrier = new OrderCarrier((int) $order->getIdOrderCarrier());
        if (Validate::isLoadedObject($orderCarrier)) {
            $orderCarrier->weight = (float) $order->getTotalWeight();
            if ($orderCarrier->update()) {
                $order->weight = sprintf('%.3f %s', $orderCarrier->weight, Configuration::get('PS_WEIGHT_UNIT'));
            }
        }

        if ($orderRefundDetail->getRefundedAmount() > 0) {
            $orderSlipCreated = OrderSlip::create(
                $order,
                $orderRefundDetail->getProductRefunds(),
                $orderRefundDetail->getRefundedShipping(),
                $orderRefundDetail->getVoucherAmount(),
                $orderRefundDetail->isVoucherChosen(),
                !$orderRefundDetail->isTaxIncluded()
            );

            if (!$orderSlipCreated) {
                throw new OrderException('You cannot generate a partial credit slip.');
            }

            $fullQuantityList = array_map(function ($orderDetail) { return $orderDetail['quantity']; }, $orderRefundDetail->getProductRefunds());
            Hook::exec('actionOrderSlipAdd', [
                'order' => $order,
                'productList' => $orderRefundDetail->getProductRefunds(),
                'qtyList' => $fullQuantityList,
            ], null, false, true, false, $order->id_shop);

            $customer = new Customer((int) $order->id_customer);

            // @todo: use private method to send mail
            $params = [
                '{lastname}' => $customer->lastname,
                '{firstname}' => $customer->firstname,
                '{id_order}' => $order->id,
                '{order_name}' => $order->getUniqReference(),
            ];

            $orderLanguage = new Language((int) $order->id_lang);

            // @todo: use a dedicated Mail class (see #13945)
            // @todo: remove this @and have a proper error handling
            @Mail::Send(
                (int) $order->id_lang,
                'credit_slip',
                $this->translator->trans(
                    'New credit slip regarding your order',
                    [],
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

            /** @var OrderDetail $orderDetail */
            foreach ($orderRefundDetail->getOrderDetails() as $orderDetail) {
                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                    StockAvailable::synchronize($orderDetail->product_id);
                }
            }
        } else {
            throw new EmptyRefundAmountException();
        }

        if ($command->generateVoucher() && $orderRefundDetail->getRefundedAmount() > 0) {
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
            $cartRule->partial_use = 1;
            $cartRule->active = 1;

            $cartRule->reduction_amount = $orderRefundDetail->getRefundedAmount();
            $cartRule->reduction_tax = $order->getTaxCalculationMethod() != PS_TAX_EXC;
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

            $currency = Context::getContext()->currency;
            $customer = new Customer((int) ($order->id_customer));

            $params = [
                '{lastname}' => $customer->lastname,
                '{firstname}' => $customer->firstname,
                '{id_order}' => $order->id,
                '{order_name}' => $order->getUniqReference(),
                '{voucher_amount}' => $this->locale->formatPrice($cartRule->reduction_amount, $currency->iso_code),
                '{voucher_num}' => $cartRule->code,
            ];

            // @todo: use private method to send mail and later a decoupled mail sender
            $orderLanguage = new Language((int) $order->id_lang);

            @Mail::Send(
                (int) $order->id_lang,
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
}
