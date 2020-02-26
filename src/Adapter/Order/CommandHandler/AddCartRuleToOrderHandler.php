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

use CartRule;
use Configuration;
use Order;
use OrderCartRule;
use OrderInvoice;
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\PercentageDiscount;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddCartRuleToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\AddCartRuleToOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\OrderDiscountType;
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Validate;

/**
 * @internal
 */
final class AddCartRuleToOrderHandler extends AbstractOrderHandler implements AddCartRuleToOrderHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddCartRuleToOrderCommand $command): void
    {
        $order = $this->getOrderObject($command->getOrderId());

        $discountValue = $command->getDiscountValue();

        $cartRuleType = $command->getCartRuleType();
        $reductionValues = $this->getReductionValues($cartRuleType, $order, $discountValue);

        $invoiceId = 0;
        $orderInvoice = $this->getInvoiceForUpdate($order, $command);

        if (null !== $orderInvoice) {
            $invoiceId = (int) $orderInvoice->id;
            $this->updateInvoiceDiscount($orderInvoice, $cartRuleType, $reductionValues);
        }

        $cartRule = $this->addCartRule($command, $order, $reductionValues, $discountValue);
        $this->addOrderCartRule($order->id, $command->getCartRuleName(), $cartRule->id, $invoiceId, $reductionValues);

        $this->applyReductionToOrder($order, $reductionValues);
    }

    /**
     * @param string $cartRuleType
     * @param Order $order
     * @param Number|null $discountValue
     *
     * @return array
     *
     * @throws OrderException
     */
    private function getReductionValues(string $cartRuleType, Order $order, ?Number $discountValue): array
    {
        $totalPaidTaxIncl = new Number((string) $order->total_paid_tax_incl);
        $totalPaidTaxExcl = new Number((string) $order->total_paid_tax_excl);

        switch ($cartRuleType) {
            case OrderDiscountType::DISCOUNT_PERCENT:
                if ($discountValue->isGreaterThan(new Number((string) PercentageDiscount::MAX_PERCENTAGE))) {
                    throw new OrderException('Percentage discount value cannot be higher than 100%.');
                }
                $reductionValues = $this->calculatePercentReduction($discountValue, $totalPaidTaxIncl, $totalPaidTaxExcl);

                break;
            case OrderDiscountType::DISCOUNT_AMOUNT:
                if ($discountValue->isGreaterThan($totalPaidTaxIncl)) {
                    throw new OrderException('The discount value is greater than the order total.');
                }
                $reductionValues = $this->calculateAmountReduction($discountValue, new Number((string) $order->getTaxesAverageUsed()));

                break;
            case OrderDiscountType::FREE_SHIPPING:
                $reductionValues = $this->calculateFreeShippingReduction(
                    new Number((string) $order->total_shipping_tax_incl),
                    new Number((string) $order->total_shipping_tax_excl)
                );

                break;
            default:
                throw new OrderException('The discount type is invalid.');
        }

        return $reductionValues;
    }

    /**
     * @param OrderInvoice $orderInvoice
     * @param string $cartRuleType
     * @param Number|null $discountValue
     * @param Number[] $reductionValues
     */
    private function updateInvoiceDiscount(OrderInvoice $orderInvoice, string $cartRuleType, array $reductionValues): void
    {
        $orderTotalPaidTaxExcl = new Number((string) $orderInvoice->total_paid_tax_excl);

        $isAlreadyFreeShipping = OrderDiscountType::FREE_SHIPPING === $cartRuleType && $orderInvoice->total_shipping_tax_incl <= 0;
        $discountAmountIsTooBig = OrderDiscountType::DISCOUNT_AMOUNT === $cartRuleType &&
            $reductionValues['value_tax_incl']->isGreaterThan($orderTotalPaidTaxExcl)
        ;

        if ($isAlreadyFreeShipping) {
            return;
        } elseif ($discountAmountIsTooBig) {
            throw new OrderException('The discount value is greater than the order invoice total.');
        }

        $orderTotalDiscountTaxIncl = new Number((string) $orderInvoice->total_discount_tax_incl);
        $orderTotalDiscountTaxExcl = new Number((string) $orderInvoice->total_discount_tax_excl);
        $orderTotalPaidTaxIncl = new Number((string) $orderInvoice->total_paid_tax_incl);

        $valueTaxIncl = $reductionValues['value_tax_incl'];
        $valueTaxExcl = $reductionValues['value_tax_excl'];

        $orderInvoice->total_discount_tax_incl = (float) $orderTotalDiscountTaxIncl
            ->plus($valueTaxIncl)
            ->toPrecision(CommonAbstractType::PRESTASHOP_DECIMALS)
        ;
        $orderInvoice->total_discount_tax_excl = (float) $orderTotalDiscountTaxExcl
            ->plus($valueTaxExcl)
            ->toPrecision(CommonAbstractType::PRESTASHOP_DECIMALS)
        ;
        $orderInvoice->total_paid_tax_incl = (float) $orderTotalPaidTaxIncl
            ->minus($valueTaxIncl)
            ->toPrecision(CommonAbstractType::PRESTASHOP_DECIMALS)
        ;
        $orderInvoice->total_paid_tax_excl = (float) $orderTotalPaidTaxExcl
            ->minus($valueTaxExcl)
            ->toPrecision(CommonAbstractType::PRESTASHOP_DECIMALS)
        ;

        $orderInvoice->update();
    }

    /**
     * @param AddCartRuleToOrderCommand $command
     * @param Order $order
     * @param Number[] $reducedValues
     * @param float $discountValue
     *
     * @return CartRule
     */
    private function addCartRule(
        AddCartRuleToOrderCommand $command,
        Order $order,
        array $reducedValues,
        ?Number $discountValue
    ): CartRule {
        $cartRuleObj = new CartRule();
        $cartRuleObj->date_from = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($order->date_add)));
        $cartRuleObj->date_to = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $cartRuleObj->name[Configuration::get('PS_LANG_DEFAULT')] = $command->getCartRuleName();
        $cartRuleObj->quantity = 0;
        $cartRuleObj->quantity_per_user = 1;

        if ($command->getCartRuleType() === OrderDiscountType::DISCOUNT_PERCENT) {
            $cartRuleObj->reduction_percent = (float) $discountValue->toPrecision(CommonAbstractType::PRESTASHOP_DECIMALS);
        } elseif ($command->getCartRuleType() === OrderDiscountType::DISCOUNT_AMOUNT) {
            $cartRuleObj->reduction_amount = (float) $reducedValues['value_tax_excl']->toPrecision(CommonAbstractType::PRESTASHOP_DECIMALS);
        } elseif ($command->getCartRuleType() === OrderDiscountType::FREE_SHIPPING) {
            $cartRuleObj->free_shipping = 1;
        }

        $cartRuleObj->active = 0;

        if (false === $cartRuleObj->add()) {
            throw new OrderException('An error occurred during the CartRule creation');
        }

        return $cartRuleObj;
    }

    /**
     * @param int $orderId
     * @param string $cartRuleName
     * @param int $cartRuleId
     * @param int $invoiceId
     * @param Number[] $reductionValues
     */
    private function addOrderCartRule(
        int $orderId,
        string $cartRuleName,
        int $cartRuleId,
        int $invoiceId,
        array $reductionValues
    ): void {
        $orderCartRule = new OrderCartRule();
        $orderCartRule->id_order = $orderId;
        $orderCartRule->id_cart_rule = $cartRuleId;
        $orderCartRule->id_order_invoice = $invoiceId;
        $orderCartRule->name = $cartRuleName;
        $orderCartRule->value = (float) $reductionValues['value_tax_incl']->toPrecision(CommonAbstractType::PRESTASHOP_DECIMALS);
        $orderCartRule->value_tax_excl = (float) $reductionValues['value_tax_excl']->toPrecision(CommonAbstractType::PRESTASHOP_DECIMALS);

        if (false === $orderCartRule->add()) {
            throw new OrderException('An error occurred during the OrderCartRule creation');
        }
    }

    /**
     * @param Order $order
     * @param AddCartRuleToOrderCommand $command
     *
     * @return OrderInvoice|null
     */
    private function getInvoiceForUpdate(Order $order, AddCartRuleToOrderCommand $command): ?OrderInvoice
    {
        // If the discount is for only one invoice
        if ($order->hasInvoice() && null !== $command->getOrderInvoiceId()) {
            $orderInvoice = new OrderInvoice($command->getOrderInvoiceId()->getValue());
            if (!Validate::isLoadedObject($orderInvoice)) {
                throw new OrderException('Can\'t load Order Invoice object');
            }

            return $orderInvoice;
        }

        return null;
    }

    /**
     * @param Number $discountValue
     * @param Number $totalPaidTaxIncl
     * @param Number $totalPaidTaxExcl
     *
     * @return array
     */
    private function calculatePercentReduction(
        Number $discountValue,
        Number $totalPaidTaxIncl,
        Number $totalPaidTaxExcl
    ): array {
        $hundredPercent = new Number('100');

        $valueTaxIncl = $discountValue
            ->times($totalPaidTaxIncl)
            ->dividedBy($hundredPercent)
        ;

        $valueTaxExcl = $discountValue
            ->times($totalPaidTaxExcl)
            ->dividedBy($hundredPercent)
        ;

        return $this->buildReducedValues(
            $valueTaxIncl,
            $valueTaxExcl
        );
    }

    /**
     * @param Number $discountValue
     * @param Number $taxesAverageUsed
     *
     * @return array
     */
    private function calculateAmountReduction(
        Number $discountValue,
        Number $taxesAverageUsed
    ) {
        $hundredPercent = new Number('100');
        $avgTax = (new Number('1'))->plus($taxesAverageUsed->dividedBy($hundredPercent));

        $totalTaxExcl = $discountValue
            ->dividedBy($avgTax)
        ;

        return $this->buildReducedValues(
            $discountValue,
            $totalTaxExcl
        );
    }

    /**
     * @param Number $totalShippingTaxIncl
     * @param Number $totalShippingTaxExcl
     *
     * @return array
     */
    private function calculateFreeShippingReduction(Number $totalShippingTaxIncl, Number $totalShippingTaxExcl)
    {
        return $this->buildReducedValues(
            $totalShippingTaxIncl,
            $totalShippingTaxExcl
        );
    }

    /**
     * @param Number $valueTaxIncl
     * @param Number $valueTaxExcl
     *
     * @return array
     */
    private function buildReducedValues(Number $valueTaxIncl, Number $valueTaxExcl): array
    {
        return [
            'value_tax_incl' => $valueTaxIncl,
            'value_tax_excl' => $valueTaxExcl,
        ];
    }

    /**
     * @param Order $order
     * @param Number[] $reductionValues
     */
    private function applyReductionToOrder(Order $order, array $reductionValues): void
    {
        $orderTotalDiscounts = new Number((string) $order->total_discounts);
        $orderTotalDiscountsTaxExcl = new Number((string) $order->total_discounts_tax_excl);
        $orderTotalDiscountsTaxIncl = new Number((string) $order->total_discounts_tax_incl);
        $orderTotalPaid = new Number((string) $order->total_paid);
        $orderTotalPaidTaxIncl = new Number((string) $order->total_paid_tax_incl);
        $orderTotalPaidTaxExcl = new Number((string) $order->total_paid_tax_excl);

        $order->total_discounts = (float) $orderTotalDiscounts
            ->plus($reductionValues['value_tax_incl'])
            ->toPrecision(CommonAbstractType::PRESTASHOP_DECIMALS)
        ;
        $order->total_discounts_tax_incl = (float) $orderTotalDiscountsTaxIncl
            ->plus($reductionValues['value_tax_incl'])
            ->toPrecision(CommonAbstractType::PRESTASHOP_DECIMALS)
        ;
        $order->total_discounts_tax_excl = (float) $orderTotalDiscountsTaxExcl
            ->plus($reductionValues['value_tax_excl'])
            ->toPrecision(CommonAbstractType::PRESTASHOP_DECIMALS)
        ;
        $order->total_paid = (float) $orderTotalPaid
            ->minus($reductionValues['value_tax_incl'])
            ->toPrecision(CommonAbstractType::PRESTASHOP_DECIMALS)
        ;
        $order->total_paid_tax_incl = (float) $orderTotalPaidTaxIncl
            ->minus($reductionValues['value_tax_incl'])
            ->toPrecision(CommonAbstractType::PRESTASHOP_DECIMALS)
        ;
        $order->total_paid_tax_excl = (float) $orderTotalPaidTaxExcl
            ->minus($reductionValues['value_tax_excl'])
            ->toPrecision(CommonAbstractType::PRESTASHOP_DECIMALS)
        ;

        if (false === $order->update()) {
            throw new OrderException('An error occurred trying to apply cart rule to order');
        }
    }
}
