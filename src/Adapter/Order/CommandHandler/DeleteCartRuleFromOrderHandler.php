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

use OrderCartRule;
use OrderInvoice;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\DeleteCartRuleFromOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\DeleteCartRuleFromOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use Validate;

/**
 * @internal
 */
final class DeleteCartRuleFromOrderHandler extends AbstractOrderHandler implements DeleteCartRuleFromOrderHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(DeleteCartRuleFromOrderCommand $command)
    {
        $order = $this->getOrderObject($command->getOrderId());
        $orderCartRule = new OrderCartRule($command->getOrderCartRuleId());

        if (!Validate::isLoadedObject($orderCartRule) || $orderCartRule->id_order != $order->id) {
            throw new OrderException('Invalid order cart rule provided.');
        }

        if ($orderCartRule->id_order_invoice) {
            $orderInvoice = new OrderInvoice($orderCartRule->id_order_invoice);
            if (!Validate::isLoadedObject($orderInvoice)) {
                throw new OrderException('Can\'t load Order Invoice object');
            }

            // Update amounts of Order Invoice
            $orderInvoice->total_discount_tax_excl -= $orderCartRule->value_tax_excl;
            $orderInvoice->total_discount_tax_incl -= $orderCartRule->value;

            $orderInvoice->total_paid_tax_excl += $orderCartRule->value_tax_excl;
            $orderInvoice->total_paid_tax_incl += $orderCartRule->value;

            // Update Order Invoice
            $orderInvoice->update();
        }

        // Update amounts of order
        $order->total_discounts -= $orderCartRule->value;
        $order->total_discounts_tax_incl -= $orderCartRule->value;
        $order->total_discounts_tax_excl -= $orderCartRule->value_tax_excl;

        $order->total_paid += $orderCartRule->value;
        $order->total_paid_tax_incl += $orderCartRule->value;
        $order->total_paid_tax_excl += $orderCartRule->value_tax_excl;

        // Delete Order Cart Rule and update Order
        $orderCartRule->delete();
        $order->update();
    }
}
