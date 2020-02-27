<?php
/**
 * 2007-2020 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Context;
use Order;
use OrderCarrier;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderRefundCalculator;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderRefundSummary;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderRefundUpdater;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderSlipCreator;
use PrestaShop\PrestaShop\Adapter\Order\Refund\VoucherGenerator;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssueStandardRefundCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\IssueStandardRefundHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidOrderStateException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\ReturnProductDisabledException;
use Validate;

/**
 * @internal
 */
class IssueStandardRefundHandler extends AbstractOrderCommandHandler implements IssueStandardRefundHandlerInterface
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
     * @param ConfigurationInterface $configuration
     * @param OrderRefundCalculator $orderRefundCalculator
     * @param OrderSlipCreator $orderSlipCreator
     * @param VoucherGenerator $voucherGenerator
     * @param OrderRefundUpdater $refundUpdater
     */
    public function __construct(
        ConfigurationInterface $configuration,
        OrderRefundCalculator $orderRefundCalculator,
        OrderSlipCreator $orderSlipCreator,
        VoucherGenerator $voucherGenerator,
        OrderRefundUpdater $refundUpdater
    ) {
        $this->configuration = $configuration;
        $this->orderRefundCalculator = $orderRefundCalculator;
        $this->orderSlipCreator = $orderSlipCreator;
        $this->voucherGenerator = $voucherGenerator;
        $this->refundUpdater = $refundUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(IssueStandardRefundCommand $command): void
    {
        if ((int) $this->configuration->get('PS_ORDER_RETURN') <= 0) {
            throw new ReturnProductDisabledException();
        }

        /** @var Order $order */
        $order = $this->getOrderObject($command->getOrderId());
        if (!$order->hasInvoice() || $order->hasBeenDelivered()) {
            throw new InvalidOrderStateException('Can not perform standard refund on order with no invoice, or already delivered');
        }

        $shippingRefundAmount = $command->refundShippingCost() ? $order->total_shipping_tax_incl : 0;
        /** @var OrderRefundSummary $orderRefundSummary */
        $orderRefundSummary = $this->orderRefundCalculator->computeOrderRefund(
            $order,
            $command->getOrderDetailRefunds(),
            $shippingRefundAmount,
            $command->getVoucherRefundType(),
            $command->getVoucherRefundAmount()
        );

        // Update order details and reinject quantities
        foreach ($orderRefundSummary->getProductRefunds() as $orderDetailId => $productRefund) {
            $orderDetail = $orderRefundSummary->getOrderDetailById($orderDetailId);
            // For standard refund the order is necessarily NOT delivered yet, so reinjection is automatic
            $this->reinjectQuantity($orderDetail, $productRefund['quantity']);
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

        // Update refund details (standard refund only happen for an order not delivered, so it can't return products)
        $this->refundUpdater->updateRefundData($order, $orderRefundSummary, false);

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
