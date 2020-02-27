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

use Address;
use Carrier;
use Currency;
use Customer;
use Customization;
use Group;
use Order;
use OrderDetail;
use OrderSlip;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidCancelProductException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderDetailRefund;
use PrestaShop\PrestaShop\Core\Domain\Order\VoucherRefundType;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;
use PrestaShopDatabaseException;
use PrestaShopException;
use TaxCalculator;
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
     * @param float $shippingRefund
     * @param int $voucherRefundType
     * @param float|null $chosenVoucherAmount
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
        float $shippingRefund,
        int $voucherRefundType,
        ?float $chosenVoucherAmount
    ): OrderRefundSummary {
        $isTaxIncluded = $this->isTaxIncludedInOrder($order);
        $precision = $this->getPrecision($order);

        $orderDetailList = $this->getOrderDetailList($orderDetailRefunds);
        $taxCalculator = $this->getOrderTaxCalculator($order);
        $productRefunds = $this->flattenCheckedProductRefunds(
            $orderDetailRefunds,
            $isTaxIncluded,
            $orderDetailList,
            $taxCalculator,
            $precision
        );
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
            $shippingMaxRefund = $isTaxIncluded ? $order->total_shipping_tax_incl : $order->total_shipping_tax_excl;
            $shippingSlipResume = OrderSlip::getShippingSlipResume($order->id);
            $shippingMaxRefund -= $shippingSlipResume['total_shipping_tax_incl'] ?? 0;
            if ($shippingCostAmount > $shippingMaxRefund) {
                $shippingCostAmount = $shippingMaxRefund;
            }
            if (!$isTaxIncluded) {
                // @todo: use https://github.com/PrestaShop/decimal for price computations
                $refundedAmount += $taxCalculator->addTaxes($shippingCostAmount);
            } else {
                $refundedAmount += $shippingCostAmount;
            }
        }

        // Something has to be refunded (check refunds count instead of the sum in case a voucher is implied)
        if (count($productRefunds) <= 0 && $shippingCostAmount <= 0) {
            throw new InvalidCancelProductException(InvalidCancelProductException::NO_REFUNDS);
        }

        return new OrderRefundSummary(
            $orderDetailList,
            $productRefunds,
            $refundedAmount,
            $shippingCostAmount,
            $voucherAmount,
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
     * @param TaxCalculator $taxCalculator
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
        TaxCalculator $taxCalculator,
        int $precision
    ) {
        $productRefunds = [];
        /** @var OrderDetailRefund $orderDetailRefund */
        foreach ($orderDetailRefunds as $orderDetailRefund) {
            $orderDetailId = $orderDetailRefund->getOrderDetailId();
            /** @var OrderDetail $orderDetail */
            $orderDetail = $orderDetails[$orderDetailId];
            $quantity = $orderDetailRefund->getProductQuantity();
            if ($orderDetail->id_customization) {
                $customization = new Customization($orderDetail->id_customization);
                $quantityLeft = (int) $customization->quantity - (int) $customization->quantity_refunded - $customization->quantity_returned;
            } else {
                $quantityLeft = (int) $orderDetail->product_quantity - (int) $orderDetail->product_quantity_refunded - (int) $orderDetail->product_quantity_return;
            }
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
                $productRefundAmount = $productUnitPrice * $quantity <= $productMaxRefund ?
                    $productUnitPrice * $quantity : $productMaxRefund;
            } else {
                $productRefundAmount = $orderDetailRefund->getRefundedAmount() <= $productMaxRefund ?
                    $orderDetailRefund->getRefundedAmount() : $productMaxRefund;
            }

            $productRefunds[$orderDetailId]['amount'] = $productRefundAmount;
            $productRefunds[$orderDetailId]['unit_price'] =
                $productRefunds[$orderDetailId]['amount'] / $productRefunds[$orderDetailId]['quantity'];

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
     * @return TaxCalculator
     *
     * @throws PrestaShopException
     */
    private function getOrderTaxCalculator(Order $order): TaxCalculator
    {
        $carrier = new Carrier((int) $order->id_carrier);
        // @todo: define if we use invoice or delivery address, or we use configuration PS_TAX_ADDRESS_TYPE
        $address = Address::initialize($order->id_address_delivery, false);

        return $carrier->getTaxCalculator($address);
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
