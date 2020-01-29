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

        $discountValue = (float) str_replace(',', '.', $command->getDiscountValue());
        $cartRules = $this->buildCartRulesByType($command, $order, $discountValue, $orderInvoice);

        $result = true;

        foreach ($cartRules as &$cartRule) {
            $cartRuleObj = $this->createCartRule($command, $order, $cartRule, $discountValue);

            if ($result = $cartRuleObj->add()) {
                $cartRule['id'] = $cartRuleObj->id;
            } else {
                break;
            }
        }

        if ($result) {
            foreach ($cartRules as $orderInvoiceId => $cartRule) {
                $orderCartRule = $this->createOrderCartRule($command, $order, $orderInvoiceId, $cartRule);
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
     * @param OrderInvoice $orderInvoice
     * @param float $valueTaxIncl
     * @param float $valueTaxExcl
     */
    protected function applyDiscountOnInvoice(OrderInvoice $orderInvoice, $valueTaxIncl, $valueTaxExcl)
    {
        $orderInvoice->total_discount_tax_incl += $valueTaxIncl;
        $orderInvoice->total_discount_tax_excl += $valueTaxExcl;
        $orderInvoice->total_paid_tax_incl -= $valueTaxIncl;
        $orderInvoice->total_paid_tax_excl -= $valueTaxExcl;
        $orderInvoice->update();
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
     * @param AddCartRuleToOrderCommand $command
     * @param Order $order
     * @param float $discountValue
     * @param OrderInvoice|null $orderInvoice
     */
    private function buildCartRulesByType(
        AddCartRuleToOrderCommand $command,
        Order $order,
        float $discountValue,
        ?OrderInvoice $orderInvoice
    ): array {
        $cartRules = [];

        switch ($command->getCartRuleType()) {
            case OrderDiscountType::DISCOUNT_PERCENT:
                $this->buildPercentTypeCartRules($cartRules, $order, $discountValue, $orderInvoice);

                break;
            case OrderDiscountType::DISCOUNT_AMOUNT:
                $this->buildAmountTypeCartRules($cartRules, $order, $discountValue, $orderInvoice);

                break;
            case OrderDiscountType::FREE_SHIPPING:
                $this->buildFreeShippingTypeCartRules($cartRules, $order, $orderInvoice);

                break;
            default:
                throw new OrderException('The discount type is invalid.');
        }

        return $cartRules;
    }

    /**
     * @param array $cartRules
     * @param Order $order
     * @param float $discountValue
     * @param OrderInvoice|null $orderInvoice
     */
    private function buildPercentTypeCartRules(array &$cartRules, Order $order, float $discountValue, ?OrderInvoice $orderInvoice): void
    {
        if ($discountValue < PercentageDiscount::MAX_PERCENTAGE) {
            if (isset($orderInvoice)) {
                $cartRules[$orderInvoice->id]['value_tax_incl'] = Tools::ps_round(
                    $orderInvoice->total_paid_tax_incl * $discountValue / 100,
                    2
                );
                $cartRules[$orderInvoice->id]['value_tax_excl'] = Tools::ps_round(
                    $orderInvoice->total_paid_tax_excl * $discountValue / 100,
                    2
                );

                // Update OrderInvoice
                $this->applyDiscountOnInvoice(
                    $orderInvoice,
                    $cartRules[$orderInvoice->id]['value_tax_incl'],
                    $cartRules[$orderInvoice->id]['value_tax_excl']
                );
            } elseif ($order->hasInvoice()) {
                $orderInvoices = $order->getInvoicesCollection();
                foreach ($orderInvoices as $orderInvoice) {
                    /* @var OrderInvoice $orderInvoice */
                    $cartRules[$orderInvoice->id]['value_tax_incl'] = Tools::ps_round(
                        $orderInvoice->total_paid_tax_incl * $discountValue / 100,
                        2
                    );
                    $cartRules[$orderInvoice->id]['value_tax_excl'] = Tools::ps_round(
                        $orderInvoice->total_paid_tax_excl * $discountValue / 100,
                        2
                    );

                    // Update OrderInvoice
                    $this->applyDiscountOnInvoice(
                        $orderInvoice,
                        $cartRules[$orderInvoice->id]['value_tax_incl'],
                        $cartRules[$orderInvoice->id]['value_tax_excl']
                    );
                }
            } else {
                $cartRules[0]['value_tax_incl'] = Tools::ps_round(
                    $order->total_paid_tax_incl * $discountValue / 100,
                    2
                );
                $cartRules[0]['value_tax_excl'] = Tools::ps_round(
                    $order->total_paid_tax_excl * $discountValue / 100,
                    2
                );
            }
        } else {
            throw new OrderException('Percentage discount value cannot be higher than 100%.');
        }
    }

    /**
     * @param array $cartRules
     * @param Order $order
     * @param float $discountValue
     * @param OrderInvoice|null $orderInvoice
     */
    private function buildAmountTypeCartRules(array &$cartRules, Order $order, float $discountValue, ?OrderInvoice $orderInvoice): void
    {
        if ($orderInvoice) {
            if ($discountValue > $orderInvoice->total_paid_tax_incl) {
                throw new OrderException('The discount value is greater than the order invoice total.');
            }

            $cartRules[$orderInvoice->id]['value_tax_incl'] = Tools::ps_round($discountValue, 2);
            $cartRules[$orderInvoice->id]['value_tax_excl'] = Tools::ps_round(
                $discountValue / (1 + ($order->getTaxesAverageUsed() / 100)),
                2
            );

            // Update OrderInvoice
            $this->applyDiscountOnInvoice(
                $orderInvoice,
                $cartRules[$orderInvoice->id]['value_tax_incl'],
                $cartRules[$orderInvoice->id]['value_tax_excl']
            );
        } elseif ($order->hasInvoice()) {
            $orderInvoices = $order->getInvoicesCollection();
            foreach ($orderInvoices as $orderInvoice) {
                /** @var OrderInvoice $orderInvoice */
                if ($discountValue > $orderInvoice->total_paid_tax_incl) {
                    throw new OrderException('The discount value is greater than the order invoice total.');
                }

                $cartRules[$orderInvoice->id]['value_tax_incl'] = Tools::ps_round($discountValue, 2);
                $cartRules[$orderInvoice->id]['value_tax_excl'] = Tools::ps_round(
                    $discountValue / (1 + ($order->getTaxesAverageUsed() / 100)),
                    2
                );

                // Update OrderInvoice
                $this->applyDiscountOnInvoice(
                    $orderInvoice,
                    $cartRules[$orderInvoice->id]['value_tax_incl'],
                    $cartRules[$orderInvoice->id]['value_tax_excl']
                );
            }
        } else {
            if ($discountValue > $order->total_paid_tax_incl) {
                throw new OrderException('The discount value is greater than the order total.');
            }

            $cartRules[0]['value_tax_incl'] = Tools::ps_round($discountValue, 2);
            $cartRules[0]['value_tax_excl'] = Tools::ps_round(
                $discountValue / (1 + ($order->getTaxesAverageUsed() / 100)),
                2
            );
        }
    }

    /**
     * @param array $cartRules
     * @param Order $order
     * @param OrderInvoice|null $orderInvoice
     */
    private function buildFreeShippingTypeCartRules(array &$cartRules, Order $order, ?OrderInvoice $orderInvoice): void
    {
        if ($orderInvoice) {
            if ($orderInvoice->total_shipping_tax_incl > 0) {
                $cartRules[$orderInvoice->id]['value_tax_incl'] = $orderInvoice->total_shipping_tax_incl;
                $cartRules[$orderInvoice->id]['value_tax_excl'] = $orderInvoice->total_shipping_tax_excl;

                // Update OrderInvoice
                $this->applyDiscountOnInvoice(
                    $orderInvoice,
                    $cartRules[$orderInvoice->id]['value_tax_incl'],
                    $cartRules[$orderInvoice->id]['value_tax_excl']
                );
            }
        } elseif ($order->hasInvoice()) {
            $orderInvoices = $order->getInvoicesCollection();
            foreach ($orderInvoices as $orderInvoice) {
                /** @var OrderInvoice $orderInvoice */
                if ($orderInvoice->total_shipping_tax_incl <= 0) {
                    continue;
                }
                $cartRules[$orderInvoice->id]['value_tax_incl'] = $orderInvoice->total_shipping_tax_incl;
                $cartRules[$orderInvoice->id]['value_tax_excl'] = $orderInvoice->total_shipping_tax_excl;

                // Update OrderInvoice
                $this->applyDiscountOnInvoice(
                    $orderInvoice,
                    $cartRules[$orderInvoice->id]['value_tax_incl'],
                    $cartRules[$orderInvoice->id]['value_tax_excl']
                );
            }
        } else {
            $cartRules[0]['value_tax_incl'] = $order->total_shipping_tax_incl;
            $cartRules[0]['value_tax_excl'] = $order->total_shipping_tax_excl;
        }
    }

    /**
     * @param AddCartRuleToOrderCommand $command
     * @param Order $order
     * @param array $cartRule
     * @param float $discountValue
     *
     * @return CartRule
     */
    private function createCartRule(
        AddCartRuleToOrderCommand $command,
        Order $order,
        array $cartRule,
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
            $cartRuleObj->reduction_amount = $cartRule['value_tax_excl'];
        } elseif ($command->getCartRuleType() === OrderDiscountType::FREE_SHIPPING) {
            $cartRuleObj->free_shipping = 1;
        }

        $cartRuleObj->active = 0;

        return $cartRuleObj;
    }

    /**
     * @param AddCartRuleToOrderCommand $command
     * @param Order $order
     * @param int $orderInvoiceId
     * @param array $cartRule
     *
     * @return OrderCartRule
     */
    private function createOrderCartRule(AddCartRuleToOrderCommand $command, Order $order, int $orderInvoiceId, array $cartRule): OrderCartRule
    {
        $orderCartRule = new OrderCartRule();
        $orderCartRule->id_order = $order->id;
        $orderCartRule->id_cart_rule = $cartRule['id'];
        $orderCartRule->id_order_invoice = $orderInvoiceId;
        $orderCartRule->name = $command->getCartRuleName();
        $orderCartRule->value = $cartRule['value_tax_incl'];
        $orderCartRule->value_tax_excl = $cartRule['value_tax_excl'];

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
}
