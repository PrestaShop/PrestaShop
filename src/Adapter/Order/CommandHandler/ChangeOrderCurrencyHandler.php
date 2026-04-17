<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Cart;
use Currency;
use ObjectModel;
use Order;
use OrderCarrier;
use OrderCartRule;
use OrderDetail;
use OrderInvoice;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ChangeOrderCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\ChangeOrderCurrencyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShopCollection;
use PrestaShopException;
use Tools;
use Validate;

/**
 * @internal
 */
#[AsCommandHandler]
final class ChangeOrderCurrencyHandler extends AbstractOrderHandler implements ChangeOrderCurrencyHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(ChangeOrderCurrencyCommand $command)
    {
        $order = $this->getOrder($command->getOrderId());

        if ($command->getNewCurrencyId()->getValue() === (int) $order->id_currency || $order->valid) {
            throw new OrderException('You cannot change the currency.');
        }

        try {
            $oldCurrency = new Currency($order->id_currency);
            $newCurrency = new Currency($command->getNewCurrencyId()->getValue());

            if (!Validate::isLoadedObject($oldCurrency) || !Validate::isLoadedObject($newCurrency)) {
                throw new OrderException('Can\'t load Currency object');
            }

            $this->updateOrderDetail($order, $oldCurrency, $newCurrency);
            $this->updateOrderCarrier((int) $order->getIdOrderCarrier(), $oldCurrency, $newCurrency);
            $this->updateInvoices($order->getInvoicesCollection(), $oldCurrency, $newCurrency);
            $this->updateCart($order->id_cart, $newCurrency);
            $this->updateOrder($order, $oldCurrency, $newCurrency);
            $this->updateOrderDiscounts($order->getCartRules(), $oldCurrency, $newCurrency);
        } catch (PrestaShopException $e) {
            throw new OrderException(
                sprintf(
                    'Error occurred when trying to change currency for order #%s',
                    $order->id
                ),
                0,
                $e
            );
        }
    }

    /**
     * @param int $orderCarrierId
     * @param Currency $oldCurrency
     * @param Currency $newCurrency
     */
    private function updateOrderCarrier(int $orderCarrierId, Currency $oldCurrency, Currency $newCurrency): void
    {
        if (!$orderCarrierId) {
            return;
        }

        $order_carrier = new OrderCarrier($orderCarrierId);
        $order_carrier->shipping_cost_tax_excl = (float) Tools::convertPriceFull(
            $order_carrier->shipping_cost_tax_excl,
            $oldCurrency,
            $newCurrency
        );
        $order_carrier->shipping_cost_tax_incl = (float) Tools::convertPriceFull(
            $order_carrier->shipping_cost_tax_incl,
            $oldCurrency,
            $newCurrency
        );
        $order_carrier->update();
    }

    /**
     * @param Order $order
     * @param Currency $oldCurrency
     * @param Currency $newCurrency
     */
    private function updateOrderDetail(Order $order, Currency $oldCurrency, Currency $newCurrency): void
    {
        foreach ($order->getOrderDetailList() as $orderDetailItem) {
            $orderDetail = new OrderDetail($orderDetailItem['id_order_detail']);
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

            $this->convertPriceFields($orderDetail, $fields, $oldCurrency, $newCurrency);

            $orderDetail->update();
            $orderDetail->updateTaxAmount($order);
        }
    }

    /**
     * @param PrestaShopCollection $invoices
     * @param Currency $oldCurrency
     * @param Currency $newCurrency
     */
    private function updateInvoices(PrestaShopCollection $invoices, Currency $oldCurrency, Currency $newCurrency): void
    {
        if (!$invoices->count()) {
            return;
        }

        foreach ($invoices as $invoice) {
            $this->convertPriceFields($invoice, $this->getSharedAmountFields(), $oldCurrency, $newCurrency);
            $invoice->save();
        }
    }

    protected function updateOrderDiscounts(array $orderCartRules, Currency $oldCurrency, Currency $newCurrency): void
    {
        foreach ($orderCartRules as $orderCartRule) {
            $orderCartRule = new OrderCartRule($orderCartRule['id_order_cart_rule']);
            $this->convertPriceFields($orderCartRule, ['value', 'value_tax_excl'], $oldCurrency, $newCurrency);
            $orderCartRule->save();
        }
    }

    /**
     * @param Order $order
     * @param Currency $oldCurrency
     * @param Currency $newCurrency
     */
    private function updateOrder(Order $order, Currency $oldCurrency, Currency $newCurrency): void
    {
        $this->convertPriceFields($order, $this->getSharedAmountFields(), $oldCurrency, $newCurrency);

        $order->id_currency = $newCurrency->id;
        $order->conversion_rate = (float) $newCurrency->conversion_rate;
        $order->update();
    }

    /**
     * @param int $cartId
     * @param Currency $newCurrency
     */
    private function updateCart(int $cartId, Currency $newCurrency): void
    {
        $cart = new Cart($cartId);

        $cart->id_currency = $newCurrency->id;
        $cart->update();
    }

    /**
     * Provides fields for Order and OrderInvoice amounts update
     *
     * @return array
     */
    private function getSharedAmountFields(): array
    {
        return [
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
    }

    /**
     * @param ObjectModel $object
     * @param array $fields
     * @param Currency $oldCurrency
     * @param Currency $newCurrency
     */
    private function convertPriceFields(
        ObjectModel $object,
        array $fields,
        Currency $oldCurrency,
        Currency $newCurrency
    ) {
        foreach ($fields as $field) {
            if (isset($object->$field)) {
                $object->{$field} = Tools::convertPriceFull($object->{$field}, $oldCurrency, $newCurrency);
            }
        }
    }
}
