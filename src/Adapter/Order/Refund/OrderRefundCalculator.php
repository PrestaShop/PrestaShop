<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Order\Refund;

use Configuration;
use Currency;
use Customer;
use Group;
use Order;
use OrderDetail;
use OrderSlip;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidCancelProductException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderDetailRefund;
use PrestaShop\PrestaShop\Core\Domain\Order\VoucherRefundType;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;
use PrestaShopDatabaseException;
use PrestaShopException;
use Tools;

/**
 * Performs all computation for a refund on an Order, returns a OrderRefundDetail
 * object which contains all the refund detail.
 */
class OrderRefundCalculator
{
    /**
     * @param Order $order
     * @param array $orderDetailRefunds
     * @param DecimalNumber $shippingRefund
     * @param int $voucherRefundType
     * @param DecimalNumber|null $chosenVoucherAmount
     *
     * @return OrderRefundSummary
     *
     * @throws InvalidCancelProductException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function computeOrderRefund(
        Order $order,
        array $orderDetailRefunds,
        DecimalNumber $shippingRefund,
        int $voucherRefundType,
        ?DecimalNumber $chosenVoucherAmount
    ): OrderRefundSummary {
        $isTaxIncluded = $this->isTaxIncludedInOrder($order);
        $precision = $this->getPrecision($order);

        $orderDetailList = $this->getOrderDetailList($orderDetailRefunds);
        $productRefunds = $this->flattenCheckedProductRefunds(
            $order,
            $orderDetailRefunds,
            $isTaxIncluded,
            $orderDetailList,
            $precision
        );

        $numberZero = new DecimalNumber('0');

        $refundedAmount = $numberZero;
        foreach ($productRefunds as $productRefund) {
            $refundedAmount = $refundedAmount->plus(new DecimalNumber((string) $productRefund['amount']));
        }

        $voucherChosen = false;
        $voucherAmount = $numberZero;
        if ($voucherRefundType === VoucherRefundType::PRODUCT_PRICES_EXCLUDING_VOUCHER_REFUND) {
            $voucherAmount = new DecimalNumber((string) $order->total_discounts);
            $refundedAmount = $refundedAmount->minus($voucherAmount);
        } elseif ($voucherRefundType === VoucherRefundType::SPECIFIC_AMOUNT_REFUND) {
            $voucherChosen = true;
            $refundedAmount = $voucherAmount = $chosenVoucherAmount;
        }

        $shippingCostAmount = $shippingRefund;
        if ($shippingCostAmount->isPositive()) {
            $shippingMaxRefund = new DecimalNumber(
                $isTaxIncluded ?
                    (string) $order->total_shipping_tax_incl :
                    (string) $order->total_shipping_tax_excl
            );

            $shippingSlipResume = OrderSlip::getShippingSlipResume($order->id);
            $shippingSlipTotalTaxIncl = new DecimalNumber((string) ($shippingSlipResume['total_shipping_tax_incl'] ?? 0));
            $shippingMaxRefund = $shippingMaxRefund->minus($shippingSlipTotalTaxIncl);

            if ($shippingCostAmount->isGreaterThan($shippingMaxRefund)) {
                $shippingCostAmount = $shippingMaxRefund;
            }
            // Previously taxes were computed but then some values are mixed with and without taxes
            // They all should be in the same state since OrderRefundSummary contains $isTaxIncluded
            $refundedAmount = $refundedAmount->plus($shippingCostAmount);
        }

        // Something has to be refunded (check refunds count instead of the sum in case a voucher is implied)
        if (count($productRefunds) <= 0 && $refundedAmount->isLowerOrEqualThanZero()) {
            throw new InvalidCancelProductException(InvalidCancelProductException::NO_REFUNDS);
        }

        return new OrderRefundSummary(
            $orderDetailList,
            $productRefunds,
            (float) (string) $refundedAmount,
            (float) (string) $shippingCostAmount,
            (float) (string) $voucherAmount,
            $voucherChosen,
            $isTaxIncluded,
            $precision
        );
    }

    /**
     * @param array $orderDetailRefunds
     *
     * @return OrderDetail[]
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function getOrderDetailList(array $orderDetailRefunds)
    {
        $orderDetailList = [];
        /** @var OrderDetailRefund $orderDetailRefund */
        foreach ($orderDetailRefunds as $orderDetailRefund) {
            $orderDetailList[$orderDetailRefund->getOrderDetailId()] = new OrderDetail($orderDetailRefund->getOrderDetailId());
        }

        return $orderDetailList;
    }

    /**
     * @param Order $order
     * @param array $orderDetailRefunds
     * @param bool $isTaxIncluded
     * @param array $orderDetails
     * @param int $precision
     *
     * @return array
     *
     * @throws InvalidCancelProductException
     */
    private function flattenCheckedProductRefunds(
        Order $order,
        array $orderDetailRefunds,
        bool $isTaxIncluded,
        array $orderDetails,
        int $precision
    ) {
        $productRefunds = [];
        /** @var OrderDetailRefund $orderDetailRefund */
        foreach ($orderDetailRefunds as $orderDetailRefund) {
            $orderDetailId = $orderDetailRefund->getOrderDetailId();
            /** @var OrderDetail $orderDetail */
            $orderDetail = $orderDetails[$orderDetailId];
            $quantity = $orderDetailRefund->getProductQuantity();
            $quantityLeft = (int) $orderDetail->product_quantity - (int) $orderDetail->product_quantity_refunded - (int) $orderDetail->product_quantity_return;
            if ($quantity > $quantityLeft) {
                throw new InvalidCancelProductException(InvalidCancelProductException::QUANTITY_TOO_HIGH, $quantityLeft);
            }

            $productRefunds[$orderDetailId] = [
                'quantity' => $quantity,
                'id_order_detail' => $orderDetailId,
            ];

            // Compute max refund by product: what was paid for this quantity, rounded like the order, without exceeding what is left to refund
            $leftTaxIncl = max(0.0, (float) $orderDetail->total_price_tax_incl - (float) $orderDetail->total_refunded_tax_incl);
            $leftTaxExcl = max(0.0, (float) $orderDetail->total_price_tax_excl - (float) $orderDetail->total_refunded_tax_excl);
            $paidTaxIncl = min($this->getPaidAmount($order, (float) $orderDetail->unit_price_tax_incl, $quantity, $precision), $leftTaxIncl);
            $paidTaxExcl = min($this->getPaidAmount($order, (float) $orderDetail->unit_price_tax_excl, $quantity, $precision), $leftTaxExcl);
            $productMaxRefund = $isTaxIncluded ? $paidTaxIncl : $paidTaxExcl;

            // If refunded amount is null it means the whole product is refunded (used for standard refund, and return product)
            if (null === $orderDetailRefund->getRefundedAmount()) {
                $productRefundAmount = (float) (string) $productMaxRefund;
            } else {
                $productRefundAmount = (float) (string) $orderDetailRefund->getRefundedAmount() <= $productMaxRefund ?
                    (float) (string) $orderDetailRefund->getRefundedAmount() : $productMaxRefund;
            }

            $productRefunds[$orderDetailId]['amount'] = $productRefundAmount;
            $productRefunds[$orderDetailId]['unit_price'] =
                $productRefunds[$orderDetailId]['amount'] / $productRefunds[$orderDetailId]['quantity'];

            // We get the tax calculator from the OrderDetail which will make it use the tax rate at the moment the order was placed
            $taxCalculator = $orderDetail->getTaxCalculator();

            // Add data for OrderDetail updates, it's important to round because too many decimals will fail in Validate::isPrice
            $roundMode = (int) $order->round_mode;
            if (null === $orderDetailRefund->getRefundedAmount()) {
                $productRefunds[$orderDetailId]['total_refunded_tax_incl'] = Tools::ps_round($paidTaxIncl, $precision, $roundMode);
                $productRefunds[$orderDetailId]['total_refunded_tax_excl'] = Tools::ps_round($paidTaxExcl, $precision, $roundMode);
            } elseif ($isTaxIncluded) {
                $productRefunds[$orderDetailId]['total_refunded_tax_incl'] = Tools::ps_round($productRefundAmount, $precision, $roundMode);
                $productRefunds[$orderDetailId]['total_refunded_tax_excl'] = Tools::ps_round(min($taxCalculator->removeTaxes($productRefundAmount), $leftTaxExcl), $precision, $roundMode);
            } else {
                $productRefunds[$orderDetailId]['total_refunded_tax_excl'] = Tools::ps_round($productRefundAmount, $precision, $roundMode);
                $productRefunds[$orderDetailId]['total_refunded_tax_incl'] = Tools::ps_round(min($taxCalculator->addTaxes($productRefundAmount), $leftTaxIncl), $precision, $roundMode);
            }

            // Add missing fields
            $productRefunds[$orderDetailId]['unit_price_tax_excl'] = (float) $orderDetail->unit_price_tax_excl;
            $productRefunds[$orderDetailId]['unit_price_tax_incl'] = (float) $orderDetail->unit_price_tax_incl;
            $productRefunds[$orderDetailId]['total_price_tax_excl'] = (float) $orderDetail->unit_price_tax_excl * $productRefunds[$orderDetailId]['quantity'];
            $productRefunds[$orderDetailId]['total_price_tax_incl'] = (float) $orderDetail->unit_price_tax_incl * $productRefunds[$orderDetailId]['quantity'];
        }

        return $productRefunds;
    }

    /**
     * Amount paid for a quantity of a product line, rounded with the rounding type and mode of the order
     */
    private function getPaidAmount(Order $order, float $unitPrice, int $quantity, int $precision): float
    {
        $roundMode = (int) $order->round_mode;

        switch ((int) $order->round_type ?: (int) Configuration::get('PS_ROUND_TYPE')) {
            case Order::ROUND_ITEM:
                return Tools::ps_round($unitPrice, $precision, $roundMode) * $quantity;
            case Order::ROUND_LINE:
                return Tools::ps_round($unitPrice * $quantity, $precision, $roundMode);
            default:
                return $unitPrice * $quantity;
        }
    }

    /**
     * @param Order $order
     *
     * @return bool
     */
    private function isTaxIncludedInOrder(Order $order): bool
    {
        $customer = new Customer($order->id_customer);

        $taxCalculationMethod = Group::getPriceDisplayMethod((int) $customer->id_default_group);

        return $taxCalculationMethod === PS_TAX_INC;
    }

    /**
     * @param Order $order
     *
     * @return int
     */
    private function getPrecision(Order $order): int
    {
        $currency = new Currency($order->id_currency);
        $computingPrecision = new ComputingPrecision();

        return $computingPrecision->getPrecision($currency->precision);
    }
}
