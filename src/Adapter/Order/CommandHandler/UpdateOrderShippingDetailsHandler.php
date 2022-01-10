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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Carrier;
use Cart;
use Customer;
use Hook;
use Language;
use OrderCarrier;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Adapter\Order\OrderAmountUpdater;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderShippingDetailsCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\UpdateOrderShippingDetailsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\TransistEmailSendingException;
use Validate;

/**
 * @internal
 */
final class UpdateOrderShippingDetailsHandler extends AbstractOrderHandler implements UpdateOrderShippingDetailsHandlerInterface
{
    /**
     * @var OrderAmountUpdater
     */
    private $orderAmountUpdater;
    /**
     * @var ContextStateManager
     */
    private $contextStateManager;

    /**
     * @param OrderAmountUpdater $orderAmountUpdater
     */
    public function __construct(OrderAmountUpdater $orderAmountUpdater, ContextStateManager $contextStateManager)
    {
        $this->orderAmountUpdater = $orderAmountUpdater;
        $this->contextStateManager = $contextStateManager;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateOrderShippingDetailsCommand $command)
    {
        $order = $this->getOrder($command->getOrderId());

        $trackingNumber = $command->getShippingTrackingNumber();
        $carrierId = $command->getNewCarrierId();
        $oldTrackingNumber = $order->getShippingNumber();

        $this->contextStateManager
            ->setLanguage(new Language($order->id_lang));

        try {
            $orderCarrier = new OrderCarrier($command->getCurrentOrderCarrierId());
            if (!Validate::isLoadedObject($orderCarrier)) {
                throw new OrderException('The order carrier ID is invalid.');
            }

            if (!empty($trackingNumber) && !Validate::isTrackingNumber($trackingNumber)) {
                throw new OrderException('The tracking number is incorrect.');
            }

            //update carrier - ONLY if changed - then refresh shipping cost
            $oldCarrierId = (int) $orderCarrier->id_carrier;
            if ($oldCarrierId !== $carrierId) {
                $cart = Cart::getCartByOrderId($order->id);
                $cart->setDeliveryOption([
                    (int) $cart->id_address_delivery => $this->formatLegacyDeliveryOptionFromCarrierId($carrierId),
                ]);
                $cart->save();

                $orderCarrier->id_carrier = $carrierId;
                $orderCarrier->update();

                $order->id_carrier = $carrierId;
                $this->orderAmountUpdater->update($order, $cart);
            }

            //load fresh order carrier because updated just before
            $orderCarrier = new OrderCarrier((int) $order->getIdOrderCarrier());

            // Update order_carrier
            $orderCarrier->tracking_number = pSQL($trackingNumber);
            if (!$orderCarrier->update()) {
                throw new OrderException('The order carrier cannot be updated.');
            }

            //send mail only if tracking number is different AND not empty
            if (!empty($trackingNumber) && $oldTrackingNumber != $trackingNumber) {
                if (!$orderCarrier->sendInTransitEmail($order)) {
                    throw new TransistEmailSendingException('An error occurred while sending an email to the customer.');
                }

                $customer = new Customer((int) $order->id_customer);
                $carrier = new Carrier((int) $order->id_carrier, (int) $order->getAssociatedLanguage()->getId());

                Hook::exec('actionAdminOrdersTrackingNumberUpdate', [
                    'order' => $order,
                    'customer' => $customer,
                    'carrier' => $carrier,
                ], null, false, true, false, $order->id_shop);
            }
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }
    }
}
