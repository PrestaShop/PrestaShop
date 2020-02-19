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
    public function handle(IssuePartialRefundCommand $command): void
    {
        /** @var Order $order */
        $order = $this->getOrderObject($command->getOrderId());
        if (!$order->hasInvoice()) {
            throw new InvalidOrderStateException(
                InvalidOrderStateException::INVOICE_NOT_FOUND,
                'Can not perform partial refund on order with no invoice'
            );
        }

        /** @var OrderRefundSummary $orderRefundSummary */
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
            $order,
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
