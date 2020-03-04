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
use PrestaShop\PrestaShop\Adapter\Invoice\DTO\InvoiceTotalNumbers;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Adapter\Order\DTO\OrderTotalNumbers;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\AddProductToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\CommandHandler\AddProductToOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use Product;
use Shop;
use SpecificPrice;
use StockAvailable;
use Symfony\Component\Translation\TranslatorInterface;
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

        if ($command->isFreeShipping()) {
            $freeShippingCartRule = $this->createFreeShippingCartRule($cart, $order);
        }

        $this->updateOrderTotals($order, $cart, $command->getOrderInvoiceId());
        $invoice = $this->createNewOrEditExistingInvoice($command, $cart, $order);

        // Create Order detail information
        $orderDetail = new OrderDetail();
        $orderDetail->createList(
            $order,
            $cart,
            $order->getCurrentOrderState(),
            $cart->getProducts(),
            !empty($invoice->id) ? $invoice->id : 0
        );

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

        Hook::exec('actionOrderEdited', ['order' => $order]);

        if (isset($freeShippingCartRule)) {
            $this->createOrderCartRule($freeShippingCartRule, $order, !empty($invoice->id) ? $invoice->id : 0);
        }

        $order->update();
    }

    /**
     * @param CartRule $cartRule
     * @param Order $order
     * @param int $invoiceId
     *
     * @return OrderCartRule
     */
    private function createOrderCartRule(CartRule $cartRule, Order $order, int $invoiceId): OrderCartRule
    {
        $values = [
            'tax_incl' => $cartRule->getContextualValue(true),
            'tax_excl' => $cartRule->getContextualValue(false),
        ];
        $orderCartRule = new OrderCartRule();
        $orderCartRule->id_order = $order->id;
        $orderCartRule->id_cart_rule = $cartRule->id;
        $orderCartRule->id_order_invoice = $invoiceId;
        $orderCartRule->name = $cartRule->name[$this->context->language->id];
        $orderCartRule->value = $values['tax_incl'];
        $orderCartRule->value_tax_excl = $values['tax_excl'];
        $orderCartRule->add();

        return $orderCartRule;
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

            if (!Validate::isLoadedObject($combination)) {
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
            throw new OrderException(sprintf('Product with id "%d" is invalid.', $productId->getValue()));
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
        $customerId = (int) $order->id_customer;
        $productId = (int) $product->id;

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
            $customerId,
            $cart->id,
            $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)}
        );

        if ($command->getProductPriceTaxIncluded() != $initialProductPriceTaxIncl) {
            return $this->createSpecificPrice(
                $customerId,
                $productId,
                $combination ? (int) $combination->id : 0,
                $command->getProductPriceTaxIncluded()
            );
        }

        return null;
    }

    /**
     * @param int $customerId
     * @param int $productId
     * @param int $combinationId
     * @param float $productPriceTaxExcl
     *
     * @return SpecificPrice
     */
    private function createSpecificPrice(
        int $customerId,
        int $productId,
        int $combinationId,
        float $productPriceTaxExcl
    ): SpecificPrice {
        $specificPrice = new SpecificPrice();
        $specificPrice->id_shop = 0;
        $specificPrice->id_shop_group = 0;
        $specificPrice->id_currency = 0;
        $specificPrice->id_country = 0;
        $specificPrice->id_group = 0;
        $specificPrice->id_customer = $customerId;
        $specificPrice->id_product = $productId;
        $specificPrice->id_product_attribute = $combinationId;
        $specificPrice->price = $productPriceTaxExcl;
        $specificPrice->from_quantity = 1;
        $specificPrice->reduction = 0;
        $specificPrice->reduction_type = Reduction::TYPE_AMOUNT;
        $specificPrice->reduction_tax = 0;
        $specificPrice->from = DateTime::NULL_VALUE;
        $specificPrice->to = DateTime::NULL_VALUE;
        $specificPrice->add();

        return $specificPrice;
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
     *
     * @return OrderInvoice|null
     */
    private function createNewOrEditExistingInvoice(
        AddProductToOrderCommand $command,
        Cart $cart,
        Order $order
    ) {
        if ($order->hasInvoice()) {
            return $command->getOrderInvoiceId() ?
                $this->updateExistingInvoice($command->getOrderInvoiceId(), $order) :
                $this->createNewInvoice($order, $cart);
        }

        return null;
    }

    /**
     * @param Order $order
     * @param Cart $cart
     * @param bool $isFreeShipping
     */
    private function createNewInvoice(Order $order, Cart $cart)
    {
        $invoice = new OrderInvoice();

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

        $invoice->total_paid_tax_excl = $order->total_paid_tax_excl;
        $invoice->total_paid_tax_incl = $order->total_paid_tax_incl;
        $invoice->total_products = $order->total_products;
        $invoice->total_products_wt = $order->total_products_wt;
        $invoice->total_shipping_tax_excl = $order->total_shipping_tax_excl;
        $invoice->total_shipping_tax_incl = $order->total_shipping_tax_incl;

        $invoice->total_wrapping_tax_excl = $order->total_wrapping_tax_excl;
        $invoice->total_wrapping_tax_incl = $order->total_shipping_tax_incl;
        $invoice->shipping_tax_computation_method = (int) $taxCalculator->computation_method;

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
     * @param Order $order
     *
     * @return OrderInvoice
     */
    private function updateExistingInvoice($orderInvoiceId, Order $order)
    {
        $invoice = new OrderInvoice($orderInvoiceId);

        $invoiceTotals = InvoiceTotalNumbers::buildFromInvoice($invoice);
        $orderTotals = OrderTotalNumbers::buildFromOrder($order);

        $invoice->total_paid_tax_excl = (float) (string) $invoiceTotals->getTotalPaidTaxExcl()
            ->plus($orderTotals->getTotalPaidTaxExcl())
        ;
        $invoice->total_paid_tax_incl = (float) (string) $invoiceTotals->getTotalPaidTaxIncl()
            ->plus($orderTotals->getTotalPaidTaxIncl())
        ;
        $invoice->total_products = (float) (string) $invoiceTotals->getTotalProducts()
            ->plus($orderTotals->getTotalProducts())
        ;
        $invoice->total_products_wt = (float) (string) $invoiceTotals->getTotalProductsWt()
            ->plus($orderTotals->getTotalProductsWt())
        ;
        $invoice->total_wrapping_tax_excl = (float) (string) $invoiceTotals->getTotalWrappingTaxExcl()
            ->plus($orderTotals->getTotalWrappingTaxExcl())
        ;
        $invoice->total_wrapping_tax_incl = (float) (string) $invoiceTotals->getTotalWrappingTaxIncl()
            ->plus($orderTotals->getTotalWrappingTaxIncl())
        ;
        $invoice->total_discount_tax_excl = (float) (string) $invoiceTotals->getTotalDiscountTaxExcl()
            ->plus($orderTotals->getTotalDiscountTaxExcl())
        ;
        $invoice->total_discount_tax_incl = (float) (string) $invoiceTotals->getTotalDiscountTaxIncl()
            ->plus($orderTotals->getTotalDiscountTaxIncl())
        ;

        $invoice->update();

        return $invoice;
    }

    /**
     * @param Order $order
     * @param Cart $cart
     * @param int|null $invoiceId
     */
    private function updateOrderTotals(Order $order, Cart $cart, ?int $invoiceId): void
    {
        $totalMethod = $invoiceId ? Cart::BOTH_WITHOUT_SHIPPING : Cart::BOTH;
        $orderTotals = OrderTotalNumbers::buildFromOrder($order);

        // update totals amount of order
        $order->total_products = (float) (string) $orderTotals->getTotalProducts()
            ->plus($this->number($cart->getOrderTotal(true, $totalMethod)))
        ;
        $order->total_products_wt = (float) (string) $orderTotals->getTotalProductsWt()
            ->plus($this->number($cart->getOrderTotal(true, Cart::ONLY_PRODUCTS)))
        ;

        $paidWithTaxes = $this->number($cart->getOrderTotal(true, $totalMethod));
        $order->total_paid = (float) (string) $orderTotals->getTotalPaid()->plus($paidWithTaxes);
        $order->total_paid_tax_incl = (float) (string) $orderTotals->getTotalPaidTaxIncl()->plus($paidWithTaxes);
        $order->total_paid_tax_excl = (float) (string) $orderTotals->getTotalPaidTaxExcl()
            ->plus($this->number($cart->getOrderTotal(false, $totalMethod)))
        ;

        // discount
        $discountWithTaxes = $this->number($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
        $order->total_discounts = (float) (string) $orderTotals->getTotalDiscounts()->plus($discountWithTaxes);
        $order->total_discounts_tax_incl = (float) (string) $orderTotals->getTotalDiscountTaxIncl()->plus($discountWithTaxes);
        $order->total_discounts_tax_excl = (float) (string) $orderTotals->getTotalDiscountTaxExcl()
            ->plus($this->number($cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS)))
        ;

        //wrapping
        $wrappingWithTaxes = $this->number($cart->getOrderTotal(true, Cart::ONLY_WRAPPING));
        $order->total_wrapping_tax_incl = (float) (string) $orderTotals->getTotalWrappingTaxIncl()->plus($wrappingWithTaxes);
        $order->total_wrapping = (float) (string) $orderTotals->getTotalWrapping()->plus($wrappingWithTaxes);
        $order->total_wrapping_tax_excl = (float) (string) $orderTotals->getTotalWrappingTaxExcl()
            ->plus($this->number($cart->getOrderTotal(false, Cart::ONLY_WRAPPING)))
        ;

        //shipping
        $order->refreshShippingCost();
    }

    /**
     * @param Cart $cart
     * @param Order $order
     *
     * @return CartRule
     */
    private function createFreeShippingCartRule(Cart $cart, Order $order): CartRule
    {
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

        return $freeShippingCartRule;
    }
}
