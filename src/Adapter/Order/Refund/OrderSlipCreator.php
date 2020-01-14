<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Order\Refund;

use Customer;
use Hook;
use Language;
use Mail;
use Order;
use OrderSlip;
use OrderDetail;
use StockAvailable;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\EmptyRefundAmountException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class OrderSlipCreator is responsible of creating an OrderSlip for a refund
 */
class OrderSlipCreator
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param ConfigurationInterface $configuration
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ConfigurationInterface $configuration,
        TranslatorInterface $translator
    ) {
        $this->configuration = $configuration;
        $this->translator = $translator;
    }

    public function createOrderSlip(
        Order $order,
        OrderRefundDetail $orderRefundDetail
    ) {
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
                if ($this->configuration->get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                    StockAvailable::synchronize($orderDetail->product_id);
                }
            }
        } else {
            throw new EmptyRefundAmountException();
        }
    }
}
