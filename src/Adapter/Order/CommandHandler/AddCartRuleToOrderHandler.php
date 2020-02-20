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
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\PercentageDiscount;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddCartRuleToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\AddCartRuleToOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\OrderDiscountType;
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Tools;
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
        $orderInvoice = $this->createSingleOrderInvoice($order, $command);

        if (null !== $orderInvoice) {
            $orderInvoices = [$orderInvoice];
        } else {
            $orderInvoices = $order->getInvoicesCollection()->getResults();
        }

        $discountValue = $command->getDiscountValue() ?
            (float) $command->getDiscountValue()->toPrecision(CommonAbstractType::PRESTASHOP_DECIMALS) :
            null
        ;

        $reductionValues = $this->getReductionValues($command->getCartRuleType(), $order, $discountValue);
        $reductionValuesByInvoiceId = $this->getReductionValuesByInvoiceId(
            $reductionValues,
            $command->getCartRuleType(),
            $orderInvoices,
            $discountValue
        );
        $this->updateInvoicesDiscount($orderInvoices, $reductionValuesByInvoiceId);

        $result = true;

        foreach ($reductionValuesByInvoiceId as &$reducedValues) {
            $cartRuleObj = $this->createCartRule($command, $order, $reducedValues, $discountValue);

            if ($result = $cartRuleObj->add()) {
                $reducedValues['cart_rule_id'] = $cartRuleObj->id;
            } else {
                break;
            }
        }

        if ($result) {
            foreach ($reductionValuesByInvoiceId as $orderInvoiceId => $reducedValues) {
                $orderCartRule = $this->createOrderCartRule($command->getCartRuleName(), $order, $orderInvoiceId, $reducedValues);
                $result &= $orderCartRule->add();
            }

            $result &= $this->applyReductionToOrder($order, $reductionValues);
        }

        if (!$result) {
            throw new OrderException('An error occurred during the OrderCartRule creation');
        }
    }

    /**
     * @param array $reductionValues
     * @param string $cartRuleType
     * @param array $orderInvoices
     * @param float $discountValue
     *
     * @return array
     */
    private function getReductionValuesByInvoiceId(
        array $reductionValues,
        string $cartRuleType,
        array $orderInvoices,
        ?float $discountValue
    ): array {
        $reducedValuesByInvoiceId = [];

        if (empty($orderInvoices)) {
            return [$reductionValues];
        } else {
            foreach ($orderInvoices as $orderInvoice) {
                $isAlreadyFreeShipping = OrderDiscountType::FREE_SHIPPING === $cartRuleType && $orderInvoice->total_shipping_tax_incl <= 0;
                $discountAmountIsTooBig = OrderDiscountType::DISCOUNT_AMOUNT === $cartRuleType && $discountValue > $orderInvoice->total_paid_tax_incl;

                if ($isAlreadyFreeShipping) {
                    continue;
                } elseif ($discountAmountIsTooBig) {
                    throw new OrderException('The discount value is greater than the order invoice total.');
                }

                $reducedValuesByInvoiceId[$orderInvoice->id] = $reductionValues;
            }
        }

        return $reducedValuesByInvoiceId;
    }

    private function getReductionValues(string $cartRuleType, Order $order, ?float $discountValue): array
    {
        $totalPaidTaxIncl = (float) $order->total_paid_tax_incl;
        $totalPaidTaxExcl = (float) $order->total_paid_tax_excl;

        switch ($cartRuleType) {
            case OrderDiscountType::DISCOUNT_PERCENT:
                if ($discountValue > PercentageDiscount::MAX_PERCENTAGE) {
                    throw new OrderException('Percentage discount value cannot be higher than 100%.');
                }
                $reductionValues = $this->calculatePercentReduction($discountValue, $totalPaidTaxIncl, $totalPaidTaxExcl);

                break;
            case OrderDiscountType::DISCOUNT_AMOUNT:
                if ($discountValue > $totalPaidTaxIncl) {
                    throw new OrderException('The discount value is greater than the order total.');
                }
                $reductionValues = $this->calculateAmountReduction($discountValue, $order->getTaxesAverageUsed());

                break;
            case OrderDiscountType::FREE_SHIPPING:
                $reductionValues = $this->calculateFreeShippingReduction(
                    $order->total_shipping_tax_incl,
                    $order->total_shipping_tax_excl
                );

                break;
            default:
                throw new OrderException('The discount type is invalid.');
        }

        return $reductionValues;
    }

    /**
     * @param array $orderInvoices
     * @param array $reducedValuesByInvoiceId
     */
    private function updateInvoicesDiscount(array $orderInvoices, array $reducedValuesByInvoiceId): void
    {
        if (empty($orderInvoices)) {
            return;
        }

        foreach ($orderInvoices as $orderInvoice) {
            $valueTaxIncl = $reducedValuesByInvoiceId[$orderInvoice->id]['value_tax_incl'];
            $valueTaxExcl = $reducedValuesByInvoiceId[$orderInvoice->id]['value_tax_excl'];

            $orderInvoice->total_discount_tax_incl += $valueTaxIncl;
            $orderInvoice->total_discount_tax_excl += $valueTaxExcl;
            $orderInvoice->total_paid_tax_incl -= $valueTaxIncl;
            $orderInvoice->total_paid_tax_excl -= $valueTaxExcl;

            $orderInvoice->update();
        }
    }

    /**
     * @param AddCartRuleToOrderCommand $command
     * @param Order $order
     * @param array $reducedValues
     * @param float $discountValue
     *
     * @return CartRule
     */
    private function createCartRule(
        AddCartRuleToOrderCommand $command,
        Order $order,
        array $reducedValues,
        ?float $discountValue
    ): CartRule {
        $cartRuleObj = new CartRule();
        $cartRuleObj->date_from = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($order->date_add)));
        $cartRuleObj->date_to = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $cartRuleObj->name[Configuration::get('PS_LANG_DEFAULT')] = $command->getCartRuleName();
        $cartRuleObj->quantity = 0;
        $cartRuleObj->quantity_per_user = 1;

        if ($command->getCartRuleType() === OrderDiscountType::DISCOUNT_PERCENT) {
            $cartRuleObj->reduction_percent = $discountValue;
        } elseif ($command->getCartRuleType() === OrderDiscountType::DISCOUNT_AMOUNT) {
            $cartRuleObj->reduction_amount = $reducedValues['value_tax_excl'];
        } elseif ($command->getCartRuleType() === OrderDiscountType::FREE_SHIPPING) {
            $cartRuleObj->free_shipping = 1;
        }

        $cartRuleObj->active = 0;

        return $cartRuleObj;
    }

    /**
     * @param string $cartRuleName
     * @param Order $order
     * @param int $orderInvoiceId
     * @param array $reducedValues
     *
     * @return OrderCartRule
     */
    private function createOrderCartRule(string $cartRuleName, Order $order, int $orderInvoiceId, array $reducedValues): OrderCartRule
    {
        $orderCartRule = new OrderCartRule();
        $orderCartRule->id_order = $order->id;
        $orderCartRule->id_cart_rule = $reducedValues['cart_rule_id'];
        $orderCartRule->id_order_invoice = $orderInvoiceId;
        $orderCartRule->name = $cartRuleName;
        $orderCartRule->value = $reducedValues['value_tax_incl'];
        $orderCartRule->value_tax_excl = $reducedValues['value_tax_excl'];

        return $orderCartRule;
    }

    /**
     * @param Order $order
     * @param AddCartRuleToOrderCommand $command
     *
     * @return OrderInvoice|null
     */
    private function createSingleOrderInvoice(Order $order, AddCartRuleToOrderCommand $command): ?OrderInvoice
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
     * @param float $discountValue
     * @param float $totalPaidTaxIncl
     * @param float $totalPaidTaxExcl
     *
     * @return array
     */
    private function calculatePercentReduction(
        float $discountValue,
        float $totalPaidTaxIncl,
        float $totalPaidTaxExcl
    ): array {
        return $this->buildReducedValues(
            Tools::ps_round($totalPaidTaxIncl * $discountValue / 100, 2),
            Tools::ps_round($totalPaidTaxExcl * $discountValue / 100, 2)
        );
    }

    /**
     * @param float $discountValue
     * @param float $taxesAverageUsed
     *
     * @return array
     */
    private function calculateAmountReduction(
        float $discountValue,
        float $taxesAverageUsed
    ) {
        return $this->buildReducedValues(
            Tools::ps_round($discountValue, 2),
            Tools::ps_round($discountValue / (1 + ($taxesAverageUsed / 100)), 2)
        );
    }

    /**
     * @param float $totalShippingTaxIncl
     * @param float $totalShippingTaxExcl
     *
     * @return array
     */
    private function calculateFreeShippingReduction(float $totalShippingTaxIncl, float $totalShippingTaxExcl)
    {
        return $this->buildReducedValues(
            $totalShippingTaxIncl,
            $totalShippingTaxExcl
        );
    }

    /**
     * @param float $valueTaxIncl
     * @param float $valueTaxExcl
     *
     * @return array
     */
    private function buildReducedValues(float $valueTaxIncl, float $valueTaxExcl): array
    {
        return [
            'value_tax_incl' => $valueTaxIncl,
            'value_tax_excl' => $valueTaxExcl,
        ];
    }

    /**
     * @param Order $order
     * @param array $reductionValues
     *
     * @return bool
     */
    private function applyReductionToOrder(Order $order, array $reductionValues): bool
    {
        $order->total_discounts += $reductionValues['value_tax_incl'];
        $order->total_discounts_tax_incl += $reductionValues['value_tax_incl'];
        $order->total_discounts_tax_excl += $reductionValues['value_tax_excl'];
        $order->total_paid -= $reductionValues['value_tax_incl'];
        $order->total_paid_tax_incl -= $reductionValues['value_tax_incl'];
        $order->total_paid_tax_excl -= $reductionValues['value_tax_excl'];

        return $order->update();
    }
}
