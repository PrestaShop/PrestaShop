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

use Context;
use Hook;
use Order;
use OrderCarrier;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderRefundCalculator;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderRefundUpdater;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderSlipCreator;
use PrestaShop\PrestaShop\Adapter\Order\Refund\VoucherGenerator;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\CancellationActionType;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssuePartialRefundCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\IssuePartialRefundHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidOrderStateException;
use Validate;

/**
 * @internal
 */
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
