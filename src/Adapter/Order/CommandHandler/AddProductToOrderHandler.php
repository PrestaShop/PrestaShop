<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

use Address;
use Attribute;
use Carrier;
use Cart;
use CartRule;
use Combination;
use Configuration;
use Context;
use Customer;
use Order;
use OrderCarrier;
use OrderInvoice;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddProductToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\AddProductToOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use Product;
use Shop;
use SpecificPrice;
use Tools;
use Validate;

/**
 * Handles adding product to an existing order using legacy object model classes.
 */
final class AddProductToOrderHandler extends AbstractOrderHandler implements AddProductToOrderHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddProductToOrderCommand $command)
    {
        $order = $this->getOrderObject($command->getOrderId());

        $this->assertOrderWasNotShipped($order);

        $product = $this->getProductObject($command->getProductId(), (int) $order->id_lang);
        $combination = $this->getCombination($command->getCombinationId());

        $cart = $this->createNewCart($order);

        $this->createSpecificPriceIfNeeded(
            $command,
            $order,
            $cart,
            $product,
            $combination
        );

        $this->addProductToCart($cart, $product, $combination, $command->getProductQuantity());

        $this->createNewOrEditExistingInvoice(
            $command,
            $order,
            $cart
        );
    }

    /**
     * @param Order $order
     *
     * @throws OrderException
     */
    private function assertOrderWasNotShipped(Order $order)
    {
        if ($order->hasBeenShipped()) {
            throw new OrderException('Cannot add product to shipped order.');
        }
    }

    /**
     * @param int $combinationId
     *
     * @return Combination|null
     */
    private function getCombination($combinationId)
    {
        $combination = null;

        if (0 !== $combinationId) {
            $combination = new Combination($combinationId);

            if (Validate::isLoadedObject($combination)) {
                throw new OrderException('Product combination not found.');
            }
        }

        return $combination;
    }

    /**
     * @param ProductId $productId
     * @param int $langId
     *
     * @return Product
     */
    private function getProductObject(ProductId $productId, $langId)
    {
        $product = new Product($productId->getValue(), false, $langId);

        if ($product->id !== $productId->getValue()) {
            throw new OrderException(
                sprintf('Product with id "%d" is invalid.', $productId->getValue())
            );
        }

        return $product;
    }

    /**
     * @param Order $order
     */
    private function createNewCart(Order $order)
    {
        $cart = new Cart();
        $cart->id_shop_group = $order->id_shop_group;
        $cart->id_shop = $order->id_shop;
        $cart->id_customer = $order->id_customer;
        $cart->id_carrier = $order->id_carrier;
        $cart->id_address_delivery = $order->id_address_delivery;
        $cart->id_address_invoice = $order->id_address_invoice;
        $cart->id_currency = $order->id_currency;
        $cart->id_lang = $order->id_lang;
        $cart->secure_key = $order->secure_key;

        $cart->add();

        Context::getContext()->cart =  $cart;
        Context::getContext()->customer = new Customer($order->id_customer);

        return $cart;
    }

    /**
     * @param AddProductToOrderCommand $command
     * @param Order $order
     * @param Cart $cart
     * @param Product $product
     * @param Combination|null $combination
     */
    private function createSpecificPriceIfNeeded(
        AddProductToOrderCommand $command,
        Order $order,
        Cart $cart,
        Product $product,
        $combination
    ) {
        $initialProductPriceTaxIncl = Product::getPriceStatic(
            $product->id,
            true,
            $combination ? $combination->id : null,
            2,
            null,
            false,
            true,
            1,
            false,
            $order->id_customer,
            $cart->id,
            $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)}
        );

        if ($command->getProductPriceTaxIncluded() != $initialProductPriceTaxIncl) {
            $specific_price = new SpecificPrice();
            $specific_price->id_shop = 0;
            $specific_price->id_shop_group = 0;
            $specific_price->id_currency = 0;
            $specific_price->id_country = 0;
            $specific_price->id_group = 0;
            $specific_price->id_customer = $order->id_customer;
            $specific_price->id_product = $product->id;
            $specific_price->id_product_attribute = $combination ? $combination->id : 0;
            $specific_price->price = $command->getProductPriceTaxExcluded();
            $specific_price->from_quantity = 1;
            $specific_price->reduction = 0;
            $specific_price->reduction_type = 'amount';
            $specific_price->reduction_tax = 0;
            $specific_price->from = '0000-00-00 00:00:00';
            $specific_price->to = '0000-00-00 00:00:00';
            $specific_price->add();
        }
    }

    /**
     * @param Cart $cart
     * @param Product $product
     * @param Combination|null $combination
     * @param int $quantity
     */
    private function addProductToCart(Cart $cart, Product $product, $combination, $quantity)
    {
        $result = $cart->updateQty(
            $quantity,
            $product->id,
            $combination ? $combination->id : null,
            false,
            'up',
            0,
            new Shop($cart->id_shop)
        );

        if ($result < 0) {
            // If product has attribute, minimal quantity is set with minimal quantity of attribute
            $minimalQuantity = $combination
                ? Attribute::getAttributeMinimalQty($combination->id) :
                $product->minimal_quantity
            ;

            throw new OrderException(sprintf('Minimum quantity of "%d" must be added', $minimalQuantity));
        }

        if (!$result) {
            throw new OrderException(sprintf('Product with id "%s" is out of stock.', $product->id));
        }
    }

    /**
     * @param AddProductToOrderCommand $command
     * @param Order $order
     * @param Cart $cart
     */
    private function createNewOrEditExistingInvoice(
        AddProductToOrderCommand $command,
        Order $order,
        Cart $cart
    ) {
        if ($order->hasInvoice()) {
            $command->getOrderInvoiceId() ?
                $this->updateExistingInvoice($command->getOrderInvoiceId(), $cart) :
                $this->createNewInvoice();
        }
    }

    private function createNewInvoice(Order $order, $isFreeShipping)
    {
        $invoice = new OrderInvoice();

        // If we create a new invoice, we calculate shipping cost
        $totalMethod = Cart::BOTH;

        // Create Cart rule in order to make free shipping
        if ($isFreeShipping) {
            $cart_rule = new CartRule();
            $cart_rule->id_customer = $order->id_customer;
            $cart_rule->name = array(
                Configuration::get('PS_LANG_DEFAULT') => $this->trans('[Generated] CartRule for Free Shipping', array(), 'Admin.Orderscustomers.Notification'),
            );
            $cart_rule->date_from = date('Y-m-d H:i:s', time());
            $cart_rule->date_to = date('Y-m-d H:i:s', time() + 24 * 3600);
            $cart_rule->quantity = 1;
            $cart_rule->quantity_per_user = 1;
            $cart_rule->minimum_amount_currency = $order->id_currency;
            $cart_rule->reduction_currency = $order->id_currency;
            $cart_rule->free_shipping = true;
            $cart_rule->active = 1;
            $cart_rule->add();

            // Add cart rule to cart and in order
            $cart->addCartRule($cart_rule->id);
            $values = array(
                'tax_incl' => $cart_rule->getContextualValue(true),
                'tax_excl' => $cart_rule->getContextualValue(false),
            );
            $order->addCartRule($cart_rule->id, $cart_rule->name[Configuration::get('PS_LANG_DEFAULT')], $values);
        }

        $invoice->id_order = $order->id;
        if ($invoice->number) {
            Configuration::updateValue('PS_INVOICE_START_NUMBER', false, false, null, $order->id_shop);
        } else {
            $invoice->number = Order::getLastInvoiceNumber() + 1;
        }

        $invoice_address = new Address((int) $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)});
        $carrier = new Carrier((int) $order->id_carrier);
        $tax_calculator = $carrier->getTaxCalculator($invoice_address);

        $invoice->total_paid_tax_excl = Tools::ps_round((float) $cart->getOrderTotal(false, $totalMethod), 2);
        $invoice->total_paid_tax_incl = Tools::ps_round((float) $cart->getOrderTotal($use_taxes, $totalMethod), 2);
        $invoice->total_products = (float) $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        $invoice->total_products_wt = (float) $cart->getOrderTotal($use_taxes, Cart::ONLY_PRODUCTS);
        $invoice->total_shipping_tax_excl = (float) $cart->getTotalShippingCost(null, false);
        $invoice->total_shipping_tax_incl = (float) $cart->getTotalShippingCost();

        $invoice->total_wrapping_tax_excl = abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING));
        $invoice->total_wrapping_tax_incl = abs($cart->getOrderTotal($use_taxes, Cart::ONLY_WRAPPING));
        $invoice->shipping_tax_computation_method = (int) $tax_calculator->computation_method;

        // Update current order field, only shipping because other field is updated later
        $order->total_shipping += $invoice->total_shipping_tax_incl;
        $order->total_shipping_tax_excl += $invoice->total_shipping_tax_excl;
        $order->total_shipping_tax_incl += ($use_taxes) ? $invoice->total_shipping_tax_incl : $invoice->total_shipping_tax_excl;

        $order->total_wrapping += abs($cart->getOrderTotal($use_taxes, Cart::ONLY_WRAPPING));
        $order->total_wrapping_tax_excl += abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING));
        $order->total_wrapping_tax_incl += abs($cart->getOrderTotal($use_taxes, Cart::ONLY_WRAPPING));
        $invoice->add();

        $invoice->saveCarrierTaxCalculator($tax_calculator->getTaxesAmount($invoice->total_shipping_tax_excl));

        $order_carrier = new OrderCarrier();
        $order_carrier->id_order = (int) $order->id;
        $order_carrier->id_carrier = (int) $order->id_carrier;
        $order_carrier->id_order_invoice = (int) $invoice->id;
        $order_carrier->weight = (float) $cart->getTotalWeight();
        $order_carrier->shipping_cost_tax_excl = (float) $invoice->total_shipping_tax_excl;
        $order_carrier->shipping_cost_tax_incl = ($use_taxes) ? (float) $invoice->total_shipping_tax_incl : (float) $invoice->total_shipping_tax_excl;
        $order_carrier->add();
    }

    /**
     * @param int $orderInvoiceId
     * @param Cart $cart
     */
    private function updateExistingInvoice($orderInvoiceId, Cart $cart)
    {
        $invoice = new OrderInvoice($orderInvoiceId);

        $invoice->total_paid_tax_excl += Tools::ps_round(
            (float) $cart->getOrderTotal(false, Cart::BOTH_WITHOUT_SHIPPING),
            2
        );
        $invoice->total_paid_tax_incl += Tools::ps_round(
            (float) $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING),
            2
        );
        $invoice->total_products += (float) $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        $invoice->total_products_wt += (float) $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);

        $invoice->update();
    }
}
