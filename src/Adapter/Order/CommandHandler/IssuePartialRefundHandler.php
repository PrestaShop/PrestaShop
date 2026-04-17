<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Context;
use Hook;
use Order;
use OrderCarrier;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderRefundCalculator;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderRefundUpdater;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderSlipCreator;
use PrestaShop\PrestaShop\Adapter\Order\Refund\VoucherGenerator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\CancellationActionType;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssuePartialRefundCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\IssuePartialRefundHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidOrderStateException;
use Validate;

/**
 * @internal
 */
#[AsCommandHandler]
final class IssuePartialRefundHandler extends AbstractOrderCommandHandler implements IssuePartialRefundHandlerInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var OrderRefundCalculator
     */
    private $orderRefundCalculator;

    /**
     * @var OrderSlipCreator
     */
    private $orderSlipCreator;

    /**
     * @var VoucherGenerator
     */
    private $voucherGenerator;

    /**
     * @var OrderRefundUpdater
     */
    private $refundUpdater;

    /**
     * @var ContextStateManager
     */
    private $contextStateManager;

    /**
     * @param ConfigurationInterface $configuration
     * @param OrderRefundCalculator $orderRefundCalculator
     * @param OrderSlipCreator $orderSlipCreator
     * @param VoucherGenerator $voucherGenerator
     * @param OrderRefundUpdater $refundUpdater
     * @param ContextStateManager $contextStateManager
     */
    public function __construct(
        ConfigurationInterface $configuration,
        OrderRefundCalculator $orderRefundCalculator,
        OrderSlipCreator $orderSlipCreator,
        VoucherGenerator $voucherGenerator,
        OrderRefundUpdater $refundUpdater,
        ContextStateManager $contextStateManager
    ) {
        $this->configuration = $configuration;
        $this->orderRefundCalculator = $orderRefundCalculator;
        $this->orderSlipCreator = $orderSlipCreator;
        $this->voucherGenerator = $voucherGenerator;
        $this->refundUpdater = $refundUpdater;
        $this->contextStateManager = $contextStateManager;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(IssuePartialRefundCommand $command): void
    {
        /** @var Order $order */
        $order = $this->getOrder($command->getOrderId());
        if (!$order->hasBeenPaid() && !$order->hasPayments()) {
            throw new InvalidOrderStateException(
                InvalidOrderStateException::NOT_PAID,
                'Can not perform partial refund on an order which is not paid'
            );
        }

        $this->setOrderContext($this->contextStateManager, $order);

        try {
            $this->issuePartialRefund($command, $order);
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }
    }

    /**
     * @param IssuePartialRefundCommand $command
     * @param Order $order
     */
    private function issuePartialRefund(IssuePartialRefundCommand $command, Order $order): void
    {
        $orderRefundSummary = $this->orderRefundCalculator->computeOrderRefund(
            $order,
            $command->getOrderDetailRefunds(),
            $command->getShippingCostRefundAmount(),
            $command->getVoucherRefundType(),
            $command->getVoucherRefundAmount()
        );

        // @todo This part should probably be in a share abstract class as it will probably be common with other handlers
        // Update order details and reinject quantities
        $shouldReinjectProducts = !$order->hasBeenDelivered() || $command->restockRefundedProducts();
        foreach ($orderRefundSummary->getProductRefunds() as $orderDetailId => $productRefund) {
            $orderDetail = $orderRefundSummary->getOrderDetailById($orderDetailId);
            if ($shouldReinjectProducts) {
                $this->reinjectQuantity($orderDetail, $productRefund['quantity']);
            }
            // Hook called only for the shop concerned
            Hook::exec('actionProductCancel', ['order' => $order, 'id_order_detail' => (int) $orderDetailId, 'cancel_quantity' => $productRefund['quantity'], 'cancel_amount' => $productRefund['amount'], 'action' => CancellationActionType::PARTIAL_REFUND], null, false, true, false, $order->id_shop);
        }

        // Update order carrier weight
        $orderCarrier = new OrderCarrier((int) $order->getIdOrderCarrier());
        if (Validate::isLoadedObject($orderCarrier)) {
            $orderCarrier->weight = (float) $order->getTotalWeight();
            if ($orderCarrier->update()) {
                $order->weight = sprintf('%.3f %s', $orderCarrier->weight, $this->configuration->get('PS_WEIGHT_UNIT'));
            }
        }

        // Create order slip
        if ($command->generateCreditSlip()) {
            $this->orderSlipCreator->create($order, $orderRefundSummary);
        }

        // Update refund details
        $productsReturned = (int) $this->configuration->get('PS_ORDER_RETURN') === 1 && $order->hasBeenDelivered();
        $this->refundUpdater->updateRefundData(
            $orderRefundSummary,
            $productsReturned,
            $shouldReinjectProducts
        );

        // Generate voucher if needed
        if ($command->generateVoucher() && $orderRefundSummary->getRefundedAmount() > 0) {
            $this->voucherGenerator->generateVoucher(
                $order,
                $orderRefundSummary->getRefundedAmount(),
                Context::getContext()->currency->iso_code,
                $orderRefundSummary->isTaxIncluded()
            );
        }
    }
}
