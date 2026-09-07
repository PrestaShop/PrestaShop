<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Order\Refund;

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
            $voucherAmount = $this->getRefundedShareOfOrderDiscount($order, $productRefunds);
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

            // Compute max refund by product (based on quantity left and already refunded amount)
            $productUnitPrice = $isTaxIncluded ? (float) $orderDetail->unit_price_tax_incl : (float) $orderDetail->unit_price_tax_excl;
            $productMaxRefund = (int) $quantity * $productUnitPrice;

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
            if ($isTaxIncluded) {
                $productRefunds[$orderDetailId]['total_refunded_tax_incl'] = Tools::ps_round($productRefundAmount, $precision);
                $productRefunds[$orderDetailId]['total_refunded_tax_excl'] = Tools::ps_round($taxCalculator->removeTaxes($productRefundAmount), $precision);
            } else {
                $productRefunds[$orderDetailId]['total_refunded_tax_excl'] = Tools::ps_round($productRefundAmount, $precision);
                $productRefunds[$orderDetailId]['total_refunded_tax_incl'] = Tools::ps_round($taxCalculator->addTaxes($productRefundAmount), $precision);
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
     * The order discount applies to the whole cart, so a refund may only take back the share of it
     * that the refunded quantities carried. Deducting all of it from a partial refund short-changes
     * the customer: on a 210 order discounted by 42, refunding the 150 product returned 150 - 42 = 108
     * where that product's own share is 150 - 30 = 120.
     *
     * Refunding every product gives a ratio of one, so a full refund still deducts the whole discount.
     *
     * Known limitation: a cart rule that also grants free shipping stores the shipping part inside the
     * same value, so that part is shared across the products here rather than charged to shipping.
     *
     * @param Order $order
     * @param array $productRefunds
     *
     * @return DecimalNumber
     */
    private function getRefundedShareOfOrderDiscount(Order $order, array $productRefunds): DecimalNumber
    {
        $orderDiscount = new DecimalNumber((string) $order->total_discounts);
        $orderProductsTotal = new DecimalNumber((string) $order->total_products_wt);

        // Nothing to apportion the discount against, keep deducting it whole.
        if (!$orderProductsTotal->isGreaterThanZero()) {
            return $orderDiscount;
        }

        // The amount actually being refunded, not the products' list price: the merchant may refund
        // less than a line is worth, and that smaller amount carries a correspondingly smaller share.
        $refundedProductsTotal = new DecimalNumber('0');
        foreach ($productRefunds as $productRefund) {
            $refundedProductsTotal = $refundedProductsTotal->plus(
                new DecimalNumber((string) $productRefund['total_refunded_tax_incl'])
            );
        }

        if ($refundedProductsTotal->isGreaterOrEqualThan($orderProductsTotal)) {
            return $orderDiscount;
        }

        return $orderDiscount->times($refundedProductsTotal)->dividedBy($orderProductsTotal);
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
