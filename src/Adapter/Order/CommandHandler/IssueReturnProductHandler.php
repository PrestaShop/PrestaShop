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
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderRefundCalculator;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderRefundSummary;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderRefundUpdater;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderSlipCreator;
use PrestaShop\PrestaShop\Adapter\Order\Refund\VoucherGenerator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\CancellationActionType;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssueReturnProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\IssueReturnProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidOrderStateException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\ReturnProductDisabledException;
use Validate;

/**
 * @internal
 */
#[AsCommandHandler]
class IssueReturnProductHandler extends AbstractOrderCommandHandler implements IssueReturnProductHandlerInterface
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
    public function handle(IssueReturnProductCommand $command): void
    {
        if ((int) $this->configuration->get('PS_ORDER_RETURN') <= 0) {
            throw new ReturnProductDisabledException();
        }

        $order = $this->getOrder($command->getOrderId());
        if (!$order->hasBeenDelivered()) {
            throw new InvalidOrderStateException(
                InvalidOrderStateException::DELIVERY_NOT_FOUND,
                'Can not perform return product on order with not delivered yet'
            );
        }
        $this->setOrderContext($this->contextStateManager, $order);

        try {
            $this->issueReturn($command, $order);
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }
    }

    private function issueReturn(IssueReturnProductCommand $command, Order $order): void
    {
        $shippingRefundAmount = new DecimalNumber((string) ($command->refundShippingCost() ? $order->total_shipping_tax_incl : 0));
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
            if ($command->restockRefundedProducts()) {
                $this->reinjectQuantity($orderDetail, $productRefund['quantity']);
            }
            // Hook called only for the shop concerned
            Hook::exec('actionProductCancel', ['order' => $order, 'id_order_detail' => (int) $orderDetailId, 'cancel_quantity' => $productRefund['quantity'], 'action' => CancellationActionType::RETURN_PRODUCT], null, false, true, false, $order->id_shop);
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

        // Update refund details (by definition it returns products)
        $this->refundUpdater->updateRefundData(
            $orderRefundSummary,
            true,
            $command->restockRefundedProducts()
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
