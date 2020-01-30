<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
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

        $discountValue = (float) $command->getDiscountValue()->toPrecision(CommonAbstractType::PRESTASHOP_DECIMALS);
        $reducedValuesByInvoiceId = $this->getReducedValuesByInvoiceId($command->getCartRuleType(), $order, $discountValue, $orderInvoices);
        $this->updateInvoicesDiscount($orderInvoices, $reducedValuesByInvoiceId);

        $result = true;

        foreach ($reducedValuesByInvoiceId as &$reducedValues) {
            $cartRuleObj = $this->createCartRule($command, $order, $reducedValues, $discountValue);

            if ($result = $cartRuleObj->add()) {
                $reducedValues['cart_rule_id'] = $cartRuleObj->id;
            } else {
                break;
            }
        }

        if ($result) {
            foreach ($reducedValuesByInvoiceId as $orderInvoiceId => $reducedValues) {
                $orderCartRule = $this->createOrderCartRule($command->getCartRuleName(), $order, $orderInvoiceId, $reducedValues);
                $result &= $orderCartRule->add();

                $this->fillOrderWithDiscountValues($order, $orderCartRule);
            }

            // Update Order
            $result &= $order->update();
        }

        if (!$result) {
            throw new OrderException('An error occurred during the OrderCartRule creation');
        }
    }

    /**
     * @param Order $order
     * @param OrderCartRule $orderCartRule
     */
    private function fillOrderWithDiscountValues(Order $order, OrderCartRule $orderCartRule): void
    {
        $order->total_discounts += $orderCartRule->value;
        $order->total_discounts_tax_incl += $orderCartRule->value;
        $order->total_discounts_tax_excl += $orderCartRule->value_tax_excl;
        $order->total_paid -= $orderCartRule->value;
        $order->total_paid_tax_incl -= $orderCartRule->value;
        $order->total_paid_tax_excl -= $orderCartRule->value_tax_excl;
    }

    /**
     * @param string $cartRuleType
     * @param Order $order
     * @param float $discountValue
     * @param array $orderInvoices
     *
     * @return array
     *
     * @throws OrderException
     */
    private function getReducedValuesByInvoiceId(
        string $cartRuleType,
        Order $order,
        float $discountValue,
        array $orderInvoices
    ): array {
        switch ($cartRuleType) {
            case OrderDiscountType::DISCOUNT_PERCENT:
                return $this->calculatePercentReducedValues($order, $discountValue, $orderInvoices);

                break;
            case OrderDiscountType::DISCOUNT_AMOUNT:
                return $this->calculateAmountReducedValues($order, $discountValue, $orderInvoices);

                break;
            case OrderDiscountType::FREE_SHIPPING:
                return $this->calculateFreeShippingReducedValues($order, $orderInvoices);

                break;
            default:
                throw new OrderException('The discount type is invalid.');
        }
    }

    /**
     * @param Order $order
     * @param float $discountValue
     * @param array $orderInvoices
     *
     * @return array
     *
     * @throws OrderException
     */
    private function calculatePercentReducedValues(Order $order, float $discountValue, array $orderInvoices): array
    {
        $reducedValuesByInvoiceId = [];

        if ($discountValue > PercentageDiscount::MAX_PERCENTAGE) {
            throw new OrderException('Percentage discount value cannot be higher than 100%.');
        }

        if (empty($orderInvoices)) {
            return $this->buildReducedValuesByInvoiceId(
                0,
                Tools::ps_round($order->total_paid_tax_incl * $discountValue / 100, 2),
                Tools::ps_round($order->total_paid_tax_excl * $discountValue / 100, 2)
            );
        } else {
            foreach ($orderInvoices as $orderInvoice) {
                $reducedValuesByInvoiceId = $this->buildReducedValuesByInvoiceId(
                    $orderInvoice->id,
                    Tools::ps_round($orderInvoice->total_paid_tax_incl * $discountValue / 100, 2),
                    Tools::ps_round($orderInvoice->total_paid_tax_excl * $discountValue / 100, 2)
                );
            }
        }

        return $reducedValuesByInvoiceId;
    }

    /**
     * @param Order $order
     * @param float $discountValue
     * @param array $orderInvoices
     *
     * @return array
     *
     * @throws OrderException
     */
    private function calculateAmountReducedValues(Order $order, float $discountValue, array $orderInvoices): array
    {
        $reducedValuesByInvoiceId = [];

        if (empty($orderInvoices)) {
            if ($discountValue > $order->total_paid_tax_incl) {
                throw new OrderException('The discount value is greater than the order total.');
            }

            return $this->buildReducedValuesByInvoiceId(
                0,
                Tools::ps_round($discountValue, 2),
                Tools::ps_round($discountValue / (1 + ($order->getTaxesAverageUsed() / 100)), 2)
            );
        } else {
            foreach ($orderInvoices as $orderInvoice) {
                /** @var OrderInvoice $orderInvoice */
                if ($discountValue > $orderInvoice->total_paid_tax_incl) {
                    throw new OrderException('The discount value is greater than the order invoice total.');
                }

                $reducedValuesByInvoiceId = $this->buildReducedValuesByInvoiceId(
                    $orderInvoice->id,
                    Tools::ps_round($discountValue, 2),
                    Tools::ps_round($discountValue / (1 + ($order->getTaxesAverageUsed() / 100)), 2)
                );
            }
        }

        return $reducedValuesByInvoiceId;
    }

    /**
     * @param Order $order
     * @param array $orderInvoices
     *
     * @return array
     */
    private function calculateFreeShippingReducedValues(Order $order, array $orderInvoices): array
    {
        $reducedValuesByInvoiceId = [];

        if (empty($orderInvoices)) {
            return $this->buildReducedValuesByInvoiceId(0, $order->total_shipping_tax_incl, $order->total_shipping_tax_excl);
        } else {
            foreach ($orderInvoices as $orderInvoice) {
                /** @var OrderInvoice $orderInvoice */
                if ($orderInvoice->total_shipping_tax_incl <= 0) {
                    continue;
                }

                $reducedValuesByInvoiceId = $this->buildReducedValuesByInvoiceId(
                    $orderInvoice->id,
                    $orderInvoice->total_shipping_tax_incl,
                    $orderInvoice->total_shipping_tax_excl
                );
            }
        }

        return $reducedValuesByInvoiceId;
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
        float $discountValue
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
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
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
     * @param int $invoiceId
     * @param float $valueTaxIncl
     * @param float $valueTaxExcl
     *
     * @return array
     */
    private function buildReducedValuesByInvoiceId(int $invoiceId, float $valueTaxIncl, float $valueTaxExcl): array
    {
        return [
            $invoiceId => [
                'value_tax_incl' => $valueTaxIncl,
                'value_tax_excl' => $valueTaxExcl,
            ],
        ];
    }
}
