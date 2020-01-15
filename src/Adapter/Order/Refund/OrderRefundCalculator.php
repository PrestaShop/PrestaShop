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
use Group;
use Order;
use OrderDetail;
use PrestaShopDatabaseException;
use PrestaShopException;
use Tax;
use TaxCalculator;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderDetailRefund;
use PrestaShop\PrestaShop\Core\Domain\Order\VoucherRefundType;

/**
 * Performs all computation for a refund on an Order, returns a OrderRefundDetail
 * object which contains all the refund detail.
 */
class OrderRefundCalculator
{
    public function computeOrderFund(
        Order $order,
        array $orderDetailRefunds,
        float $shippingRefund,
        int $voucherRefundType,
        ?float $chosenVoucherAmount
    ): OrderRefundSummary {
        $isTaxIncluded = $this->isTaxIncludedInOrder($order);

        $orderDetailList = $this->getOrderTailList($orderDetailRefunds);
        $productRefunds = $this->flattenProductRefunds($orderDetailRefunds, $isTaxIncluded, $orderDetailList);
        $refundedAmount = 0;
        foreach ($productRefunds as $orderDetailId => $productRefund) {
            $refundedAmount += $productRefund['amount'];
        }

        $voucherChosen = false;
        $voucherAmount = 0;
        if ($voucherRefundType === VoucherRefundType::PRODUCT_PRICES_EXCLUDING_VOUCHER_REFUND) {
            //@todo: Check if it matches order_discount_price in legacy
            $refundedAmount -= $voucherAmount = (float) $order->total_discounts;
        } elseif ($voucherRefundType === VoucherRefundType::SPECIFIC_AMOUNT_REFUND) {
            $voucherChosen = true;
            $refundedAmount = $voucherAmount = $chosenVoucherAmount;
        }

        $shippingCostAmount = $shippingRefund ?: false;
        if ($shippingCostAmount > 0) {
            if (!$isTaxIncluded) {
                // @todo: use https://github.com/PrestaShop/decimal for price computations
                $taxCalculator = $this->getTaxCalculator($order->carrier_tax_rate);
                $refundedAmount += $taxCalculator->addTaxes($shippingCostAmount);
            } else {
                $refundedAmount += $shippingCostAmount;
            }
        }

        return new OrderRefundSummary(
            $orderDetailList,
            $productRefunds,
            $refundedAmount,
            $shippingCostAmount,
            $voucherAmount,
            $voucherChosen,
            $isTaxIncluded
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
    private function getOrderTailList(array $orderDetailRefunds)
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
     *
     * @return array
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function flattenProductRefunds(array $orderDetailRefunds, bool $isTaxIncluded, array $orderDetails)
    {
        $productRefunds = [];
        /** @var OrderDetailRefund $orderDetailRefund */
        foreach ($orderDetailRefunds as $orderDetailRefund) {
            $orderDetailId = $orderDetailRefund->getOrderDetailId();
            $orderDetail = $orderDetails[$orderDetailId];
            $quantity = $orderDetailRefund->getProductQuantity();

            $productRefunds[$orderDetailId] = [
                'quantity' => $quantity,
                'id_order_detail' => $orderDetailId,
            ];

            if (null === $orderDetailRefund->getRefundedAmount()) {
                $productRefundAmount = $isTaxIncluded ?
                    $orderDetail->unit_price_tax_excl :
                    $orderDetail->unit_price_tax_incl;
                $productRefundAmount *= $quantity;
            } else {
                $productRefundAmount = $orderDetailRefund->getRefundedAmount();
            }

            $productRefunds[$orderDetailId]['amount'] = $productRefundAmount;
            $productRefunds[$orderDetailId]['unit_price'] =
                $productRefunds[$orderDetailId]['amount'] / $productRefunds[$orderDetailId]['quantity'];

            // add missing fields
            $productRefunds[$orderDetailId]['unit_price_tax_excl'] = $orderDetail->unit_price_tax_excl;
            $productRefunds[$orderDetailId]['unit_price_tax_incl'] = $orderDetail->unit_price_tax_incl;
            $productRefunds[$orderDetailId]['total_price_tax_excl'] = $orderDetail->unit_price_tax_excl * $productRefunds[$orderDetailId]['quantity'];
            $productRefunds[$orderDetailId]['total_price_tax_incl'] = $orderDetail->unit_price_tax_incl * $productRefunds[$orderDetailId]['quantity'];
        }

        return $productRefunds;
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
     * @param float $taxRate
     *
     * @return TaxCalculator
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function getTaxCalculator(float $taxRate)
    {
        $tax = new Tax();
        $tax->rate = $taxRate;

        return new TaxCalculator([$tax]);
    }
}
