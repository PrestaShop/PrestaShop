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

use Address;
use Attribute;
use Carrier;
use Cart;
use CartRule;
use Combination;
use Configuration;
use Context;
use Customer;
use Hook;
use Order;
use OrderCarrier;
use OrderCartRule;
use OrderDetail;
use OrderInvoice;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\AddProductToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\CommandHandler\AddProductToOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use Product;
use Shop;
use SpecificPrice;
use StockAvailable;
use Symfony\Component\Translation\TranslatorInterface;
use Tools;
use Validate;

/**
 * Handles adding product to an existing order using legacy object model classes.
 *
 * @internal
 */
final class AddProductToOrderHandler extends AbstractOrderHandler implements AddProductToOrderHandlerInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Context
     */
    private $context;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->context = Context::getContext();
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddProductToOrderCommand $command)
    {
        $order = $this->getOrderObject($command->getOrderId());

        if ($this->context->cart !== null) {
            $oldCartRules = $this->context->cart->getCartRules();
        } else {
            $oldCartRules = [];
        }

        $this->assertOrderWasNotShipped($order);

        $product = $this->getProductObject($command->getProductId(), (int) $order->id_lang);
        $combination = $this->getCombination($command->getCombinationId());

        $cart = $this->createNewCart($order);

        $specificPrice = $this->createSpecificPriceIfNeeded(
            $command,
            $order,
            $cart,
            $product,
            $combination
        );

        $this->addProductToCart($cart, $product, $combination, $command->getProductQuantity());

        $invoice = $this->createNewOrEditExistingInvoice(
            $command,
            $order,
            $cart
        );

        $totalMethod = $command->getOrderInvoiceId() ? Cart::BOTH_WITHOUT_SHIPPING : Cart::BOTH;

        // Create Order detail information
        $orderDetail = new OrderDetail();
        $orderDetail->createList(
            $order,
            $cart,
            $order->getCurrentOrderState(),
            $cart->getProducts(),
            $command->getOrderInvoiceId() ?: 0
        );

        // update totals amount of order
        // @todo: use https://github.com/PrestaShop/decimal for prices computations
        $order->total_products += (float) $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        $order->total_products_wt += (float) $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);

        $order->total_paid += Tools::ps_round((float) $cart->getOrderTotal(true, $totalMethod), 2);
        $order->total_paid_tax_excl += Tools::ps_round((float) $cart->getOrderTotal(false, $totalMethod), 2);
        $order->total_paid_tax_incl += Tools::ps_round((float) $cart->getOrderTotal(true, $totalMethod), 2);

        if (null !== $invoice && Validate::isLoadedObject($invoice)) {
            $order->total_shipping = $invoice->total_shipping_tax_incl;
            $order->total_shipping_tax_incl = $invoice->total_shipping_tax_incl;
            $order->total_shipping_tax_excl = $invoice->total_shipping_tax_excl;
        }

        // discount
        $order->total_discounts += (float) abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
        $order->total_discounts_tax_excl += (float) abs($cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS));
        $order->total_discounts_tax_incl += (float) abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));

        // Save changes of order
        $order->update();

        StockAvailable::synchronize($product->id);

        // Update weight SUM
        $orderCarrier = new OrderCarrier((int) $order->getIdOrderCarrier());
        if (Validate::isLoadedObject($orderCarrier)) {
            $orderCarrier->weight = (float) $order->getTotalWeight();
            if ($orderCarrier->update()) {
                $order->weight = sprintf('%.3f ' . Configuration::get('PS_WEIGHT_UNIT'), $orderCarrier->weight);
            }
        }

        // Update Tax lines
        $orderDetail->updateTaxAmount($order);

        // Delete specific price if exists
        if (null !== $specificPrice) {
            $specificPrice->delete();
        }

        $order = $order->refreshShippingCost();

        Hook::exec('actionOrderEdited', ['order' => $order]);

        $newCartRules = $this->context->cart->getCartRules();

        sort($oldCartRules);
        sort($newCartRules);

        if (!empty($newCartRules) && !empty($oldCartRules)) {
            $result = array_diff($newCartRules, $oldCartRules);
        } else {
            $result = [];
        }

        foreach ($result as $cartRule) {
            // Create OrderCartRule
            $rule = new CartRule($cartRule['id_cart_rule']);
            $values = array(
                'tax_incl' => $rule->getContextualValue(true),
                'tax_excl' => $rule->getContextualValue(false),
            );
            $orderCartRule = new OrderCartRule();
            $orderCartRule->id_order = $order->id;
            $orderCartRule->id_cart_rule = $cartRule['id_cart_rule'];
            $orderCartRule->id_order_invoice = $invoice->id;
            $orderCartRule->name = $cartRule['name'];
            $orderCartRule->value = $values['tax_incl'];
            $orderCartRule->value_tax_excl = $values['tax_excl'];
            $orderCartRule->add();

            // @todo: use https://github.com/PrestaShop/decimal
            $order->total_discounts += $orderCartRule->value;
            $order->total_discounts_tax_incl += $orderCartRule->value;
            $order->total_discounts_tax_excl += $orderCartRule->value_tax_excl;
            $order->total_paid -= $orderCartRule->value;
            $order->total_paid_tax_incl -= $orderCartRule->value;
            $order->total_paid_tax_excl -= $orderCartRule->value_tax_excl;
        }

        $order->update();
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
     *
     * @return Cart
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

        $this->context->cart = $cart;
        $this->context->customer = new Customer($order->id_customer);

        return $cart;
    }

    /**
     * @param AddProductToOrderCommand $command
     * @param Order $order
     * @param Cart $cart
     * @param Product $product
     * @param Combination|null $combination
     *
     * @return SpecificPrice|null
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
            // @todo: use private method to create specific price object
            $specificPrice = new SpecificPrice();
            $specificPrice->id_shop = 0;
            $specificPrice->id_shop_group = 0;
            $specificPrice->id_currency = 0;
            $specificPrice->id_country = 0;
            $specificPrice->id_group = 0;
            $specificPrice->id_customer = $order->id_customer;
            $specificPrice->id_product = $product->id;
            $specificPrice->id_product_attribute = $combination ? $combination->id : 0;
            $specificPrice->price = $command->getProductPriceTaxExcluded();
            $specificPrice->from_quantity = 1;
            $specificPrice->reduction = 0;
            $specificPrice->reduction_type = 'amount';
            $specificPrice->reduction_tax = 0;
            $specificPrice->from = '0000-00-00 00:00:00';
            $specificPrice->to = '0000-00-00 00:00:00';
            $specificPrice->add();

            return $specificPrice;
        }

        return null;
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
     *
     * @return OrderInvoice|null
     */
    private function createNewOrEditExistingInvoice(
        AddProductToOrderCommand $command,
        Order $order,
        Cart $cart
    ) {
        if ($order->hasInvoice()) {
            return $command->getOrderInvoiceId() ?
                $this->updateExistingInvoice($command->getOrderInvoiceId(), $cart) :
                $this->createNewInvoice($order, $cart, $command->isFreeShipping());
        }

        return null;
    }

    /**
     * @param Order $order
     * @param Cart $cart
     * @param bool $isFreeShipping
     */
    private function createNewInvoice(Order $order, Cart $cart, $isFreeShipping)
    {
        $invoice = new OrderInvoice();

        // If we create a new invoice, we calculate shipping cost
        $totalMethod = Cart::BOTH;

        // Create Cart rule in order to make free shipping
        if ($isFreeShipping) {
            // @todo: use private method to create cart rule
            $freeShippingCartRule = new CartRule();
            $freeShippingCartRule->id_customer = $order->id_customer;
            $freeShippingCartRule->name = [
                Configuration::get('PS_LANG_DEFAULT') => $this->translator->trans(
                    '[Generated] CartRule for Free Shipping',
                    [],
                    'Admin.Orderscustomers.Notification'
                ),
            ];
            $freeShippingCartRule->date_from = date('Y-m-d H:i:s');
            $freeShippingCartRule->date_to = date('Y-m-d H:i:s', time() + 24 * 3600);
            $freeShippingCartRule->quantity = 1;
            $freeShippingCartRule->quantity_per_user = 1;
            $freeShippingCartRule->minimum_amount_currency = $order->id_currency;
            $freeShippingCartRule->reduction_currency = $order->id_currency;
            $freeShippingCartRule->free_shipping = true;
            $freeShippingCartRule->active = 1;
            $freeShippingCartRule->add();

            // Add cart rule to cart and in order
            $cart->addCartRule($freeShippingCartRule->id);
            $values = [
                'tax_incl' => $freeShippingCartRule->getContextualValue(true),
                'tax_excl' => $freeShippingCartRule->getContextualValue(false),
            ];

            $order->addCartRule(
                $freeShippingCartRule->id,
                $freeShippingCartRule->name[Configuration::get('PS_LANG_DEFAULT')],
                $values
            );
        }

        $invoice->id_order = $order->id;
        if ($invoice->number) {
            Configuration::updateValue('PS_INVOICE_START_NUMBER', false, false, null, $order->id_shop);
        } else {
            $invoice->number = Order::getLastInvoiceNumber() + 1;
        }

        $invoice_address = new Address(
            (int) $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)}
        );
        $carrier = new Carrier((int) $order->id_carrier);
        $taxCalculator = $carrier->getTaxCalculator($invoice_address);

        // @todo: use https://github.com/PrestaShop/decimal to compute prices and taxes
        $invoice->total_paid_tax_excl = Tools::ps_round((float) $cart->getOrderTotal(false, $totalMethod), 2);
        $invoice->total_paid_tax_incl = Tools::ps_round((float) $cart->getOrderTotal(true, $totalMethod), 2);
        $invoice->total_products = (float) $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        $invoice->total_products_wt = (float) $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $invoice->total_shipping_tax_excl = (float) $cart->getTotalShippingCost(null, false);
        $invoice->total_shipping_tax_incl = (float) $cart->getTotalShippingCost();

        $invoice->total_wrapping_tax_excl = abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING));
        $invoice->total_wrapping_tax_incl = abs($cart->getOrderTotal(true, Cart::ONLY_WRAPPING));
        $invoice->shipping_tax_computation_method = (int) $taxCalculator->computation_method;

        // Update current order field, only shipping because other field is updated later
        $order->total_shipping += $invoice->total_shipping_tax_incl;
        $order->total_shipping_tax_excl += $invoice->total_shipping_tax_excl;
        $order->total_shipping_tax_incl += $invoice->total_shipping_tax_incl;

        $order->total_wrapping += abs($cart->getOrderTotal(true, Cart::ONLY_WRAPPING));
        $order->total_wrapping_tax_excl += abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING));
        $order->total_wrapping_tax_incl += abs($cart->getOrderTotal(true, Cart::ONLY_WRAPPING));
        $invoice->add();

        $invoice->saveCarrierTaxCalculator($taxCalculator->getTaxesAmount($invoice->total_shipping_tax_excl));

        $orderCarrier = new OrderCarrier();
        $orderCarrier->id_order = (int) $order->id;
        $orderCarrier->id_carrier = (int) $order->id_carrier;
        $orderCarrier->id_order_invoice = (int) $invoice->id;
        $orderCarrier->weight = (float) $cart->getTotalWeight();
        $orderCarrier->shipping_cost_tax_excl = (float) $invoice->total_shipping_tax_excl;
        $orderCarrier->shipping_cost_tax_incl = (float) $invoice->total_shipping_tax_incl;
        $orderCarrier->add();

        return $invoice;
    }

    /**
     * @param int $orderInvoiceId
     * @param Cart $cart
     *
     * @return OrderInvoice
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

        return $invoice;
    }
}
