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

use Currency;
use OrderCarrier;
use OrderDetail;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ChangeOrderCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\ChangeOrderCurrencyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use Tools;
use Validate;

/**
 * @internal
 */
final class ChangeOrderCurrencyHandler extends AbstractOrderHandler implements ChangeOrderCurrencyHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(ChangeOrderCurrencyCommand $command)
    {
        $order = $this->getOrderObject($command->getOrderId());

        if ($command->getNewCurrencyId()->getValue() === (int) $order->id_currency || $order->valid) {
            throw new OrderException('You cannot change the currency.');
        }

        $oldCurrency = new Currency($order->id_currency);
        $currency = new Currency($command->getNewCurrencyId()->getValue());

        if (!Validate::isLoadedObject($currency)) {
            throw new OrderException('Can\'t load Currency object');
        }

        // Update order detail amount
        foreach ($order->getOrderDetailList() as $orderDetail) {
            $order_detail = new OrderDetail($orderDetail['id_order_detail']);
            // @todo: use private method to handle this
            $fields = [
                'ecotax',
                'product_price',
                'reduction_amount',
                'total_shipping_price_tax_excl',
                'total_shipping_price_tax_incl',
                'total_price_tax_incl',
                'total_price_tax_excl',
                'product_quantity_discount',
                'purchase_supplier_price',
                'reduction_amount',
                'reduction_amount_tax_incl',
                'reduction_amount_tax_excl',
                'unit_price_tax_incl',
                'unit_price_tax_excl',
                'original_product_price',
            ];

            foreach ($fields as $field) {
                $order_detail->{$field} = Tools::convertPriceFull($order_detail->{$field}, $oldCurrency, $currency);
            }

            $order_detail->update();
            $order_detail->updateTaxAmount($order);
        }

        $id_order_carrier = (int) $order->getIdOrderCarrier();

        if ($id_order_carrier) {
            $order_carrier = new OrderCarrier((int) $order->getIdOrderCarrier());
            $order_carrier->shipping_cost_tax_excl = (float) Tools::convertPriceFull(
                $order_carrier->shipping_cost_tax_excl,
                $oldCurrency,
                $currency
            );
            $order_carrier->shipping_cost_tax_incl = (float) Tools::convertPriceFull(
                $order_carrier->shipping_cost_tax_incl,
                $oldCurrency,
                $currency
            );
            $order_carrier->update();
        }

        // Update order && order_invoice amount
        // @todo: use private method to handle this
        $fields = [
            'total_discounts',
            'total_discounts_tax_incl',
            'total_discounts_tax_excl',
            'total_discount_tax_excl',
            'total_discount_tax_incl',
            'total_paid',
            'total_paid_tax_incl',
            'total_paid_tax_excl',
            'total_paid_real',
            'total_products',
            'total_products_wt',
            'total_shipping',
            'total_shipping_tax_incl',
            'total_shipping_tax_excl',
            'total_wrapping',
            'total_wrapping_tax_incl',
            'total_wrapping_tax_excl',
        ];

        $invoices = $order->getInvoicesCollection();

        if ($invoices) {
            foreach ($invoices as $invoice) {
                foreach ($fields as $field) {
                    if (isset($invoice->$field)) {
                        $invoice->{$field} = Tools::convertPriceFull($invoice->{$field}, $oldCurrency, $currency);
                    }
                }
                $invoice->save();
            }
        }

        foreach ($fields as $field) {
            if (isset($order->$field)) {
                $order->{$field} = Tools::convertPriceFull($order->{$field}, $oldCurrency, $currency);
            }
        }

        // Update currency in order
        $order->id_currency = $currency->id;
        // Update exchange rate
        $order->conversion_rate = (float) $currency->conversion_rate;
        $order->update();
    }
}
