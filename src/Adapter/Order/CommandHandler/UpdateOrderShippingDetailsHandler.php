<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderShippingDetailsCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\UpdateOrderShippingDetailsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\TransistEmailSendingException;
use Validate;

/**
 * @internal
 */
#[AsCommandHandler]
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

            // update carrier - ONLY if changed - then refresh shipping cost
            $oldCarrierId = (int) $orderCarrier->id_carrier;
            if ($oldCarrierId !== $carrierId) {
                $cart = Cart::getCartByOrderId($order->id);
                $cart->setDeliveryOption(
                    [(int) $cart->id_address_delivery => $this->formatLegacyDeliveryOptionFromCarrierId($carrierId)],
                    true
                );
                $cart->save();

                $orderCarrier->id_carrier = $carrierId;
                $orderCarrier->update();

                $order->id_carrier = $carrierId;
                $this->orderAmountUpdater->update($order, $cart);
            }

            // load fresh order carrier because updated just before
            $orderCarrier = new OrderCarrier((int) $order->getIdOrderCarrier());

            // Update order_carrier
            $orderCarrier->tracking_number = pSQL($trackingNumber);
            if (!$orderCarrier->update()) {
                throw new OrderException('The order carrier cannot be updated.');
            }

            // send mail only if tracking number is different AND not empty
            if (!empty($trackingNumber) && $oldTrackingNumber != $trackingNumber) {
                if (!$orderCarrier->sendInTransitEmail($order)) {
                    throw new TransistEmailSendingException('An error occurred while sending an email to the customer.');
                }

                $customer = new Customer((int) $order->id_customer);
                $carrier = new Carrier((int) $order->id_carrier, (int) $order->getAssociatedLanguage()->getId());

                // Hook called only for the shop concerned
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
