<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
use Currency;
use Customer;
use Exception;
use Hook;
use Order;
use OrderCarrier;
use OrderDetail;
use OrderInvoice;
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Adapter\Order\OrderAmountUpdater;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\AddProductToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\CommandHandler\AddProductToOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductOutOfStockException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
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
     * @var Context
     */
    private $context;

    /**
     * @var ContextStateManager
     */
    private $contextStateManager;

    /**
     * @var OrderAmountUpdater
     */
    private $orderAmountUpdater;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $temporarySpecificPrices;

    /**
     * @var int
     */
    private $computingPrecision;

    /**
     * @param TranslatorInterface $translator
     * @param ContextStateManager $contextStateManager
     */
    public function __construct(
        TranslatorInterface $translator,
        ContextStateManager $contextStateManager,
        OrderAmountUpdater $orderAmountUpdater
    ) {
        $this->context = Context::getContext();
        $this->translator = $translator;
        $this->contextStateManager = $contextStateManager;
        $this->orderAmountUpdater = $orderAmountUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddProductToOrderCommand $command)
    {
        $order = $this->getOrderObject($command->getOrderId());

        $this->contextStateManager
            ->setCurrency(new Currency($order->id_currency))
            ->setCustomer(new Customer($order->id_customer));

        // Get context precision just in case
        $this->computingPrecision = $this->context->getComputingPrecision();
        $this->temporarySpecificPrices = [];
        try {
            $this->assertOrderWasNotShipped($order);

            $product = $this->getProductObject($command->getProductId(), (int) $order->id_lang);
            $combination = $this->getCombination($command->getCombinationId());

            $this->checkProductInStock($product, $command);

            $cart = $this->createNewOrEditExistingCart($order);
            // Cart precision is more adapted
            $this->computingPrecision = $this->getPrecisionFromCart($cart);

            // Restore any specific prices for the products in the order
            $this->restoreOrderProductsSpecificPrices(
                $order,
                $cart
            );

            // Add specific price for the product being added
            $this->createSpecificPriceIfNeeded(
                $command->getProductPriceTaxIncluded(),
                $command->getProductPriceTaxExcluded(),
                $order,
                $cart,
                $product,
                $combination
            );

            $this->addProductToCart($cart, $product, $combination, $command->getProductQuantity());

            // Fetch Cart Product
            $productCart = $this->getCartProductData($cart, $product, $command->getProductQuantity());

            $invoice = $this->createNewOrEditExistingInvoice(
                $command,
                $order,
                $cart,
                [$productCart]
            );

            // Create Order detail information
            $orderDetail = $this->createOrderDetail(
                $order,
                $invoice,
                $cart,
                [$productCart]
            );

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

            $order = $order->refreshShippingCost();

            Hook::exec('actionOrderEdited', ['order' => $order]);

            // Update totals amount of order
            $this->orderAmountUpdater->update($order, $cart, (int) $orderDetail->id_order_invoice);

            // Delete temporary specific prices
            $this->clearTemporarySpecificPrices();
        } catch (Exception $e) {
            $this->clearTemporarySpecificPrices();
            $this->contextStateManager->restoreContext();
            throw $e;
        }

        $this->contextStateManager->restoreContext();
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
    private function createNewOrEditExistingCart(Order $order)
    {
        $cartId = Cart::getCartIdByOrderId($order->id);
        if ($cartId) {
            $cart = new Cart($cartId);
        } else {
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
        }

        $this->context->cart = $cart;

        return $cart;
    }

    /**
     * @param Order $order
     * @param OrderInvoice|null $invoice
     * @param Cart $cart
     * @param array $productCart
     *
     * @return OrderDetail
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function createOrderDetail(Order $order, ?OrderInvoice $invoice, Cart $cart, array $productCart): OrderDetail
    {
        $orderDetail = new OrderDetail();
        $orderDetail->createList(
            $order,
            $cart,
            $order->getCurrentOrderState(),
            $productCart,
            !empty($invoice->id) ? $invoice->id : 0
        );

        return $orderDetail;
    }

    /**
     * @param Order $order
     * @param Cart $cart
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function restoreOrderProductsSpecificPrices(Order $order, Cart $cart): void
    {
        foreach ($order->getOrderDetailList() as $row) {
            $orderDetail = new OrderDetail($row['id_order_detail']);
            $product = new Product((int) $orderDetail->product_id);

            $this->createSpecificPriceIfNeeded(
                new Number((string) $orderDetail->unit_price_tax_incl),
                new Number((string) $orderDetail->unit_price_tax_excl),
                $order,
                $cart,
                $product,
                new Combination($orderDetail->product_attribute_id)
            );
        }
    }

    /**
     * Clean all the specific prices that were created but this handler
     *
     * @throws \PrestaShopException
     */
    private function clearTemporarySpecificPrices(): void
    {
        if (empty($this->temporarySpecificPrices)) {
            return;
        }

        /** @var SpecificPrice $specificPrice */
        foreach ($this->temporarySpecificPrices as $specificPrice) {
            $specificPrice->delete();
        }
        $this->temporarySpecificPrices = [];
    }

    /**
     * @param Number $priceTaxIncluded
     * @param Number $priceTaxExcluded
     * @param Order $order
     * @param Cart $cart
     * @param Product $product
     * @param Combination|null $combination
     */
    private function createSpecificPriceIfNeeded(
        Number $priceTaxIncluded,
        Number $priceTaxExcluded,
        Order $order,
        Cart $cart,
        Product $product,
        $combination
    ): void {
        // Check it the SpecificPrice has already been added by restoreOrderProductsSpecificPrices, if yes ignore new
        // price because the first one is kept
        if (SpecificPrice::exists(
            $product->id,
            $combination ? $combination->id : 0,
            0,
            0,
            0,
            $order->id_currency,
            $order->id_customer,
            1,
            DateTime::NULL_VALUE,
            DateTime::NULL_VALUE
        )) {
            return;
        }

        $initialProductPriceTaxExcl = Product::getPriceStatic(
            $product->id,
            false,
            $combination ? $combination->id : null,
            $this->computingPrecision,
            null,
            false,
            true,
            1,
            false,
            $order->id_customer,
            $cart->id,
            $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)}
        );

        // Better check with price tax excluded since it's the one saved in database, if the price matches
        // the product's one no need for specific price
        if ($priceTaxExcluded->equals(new Number((string) $initialProductPriceTaxExcl))) {
            return;
        }

        $specificPrice = new SpecificPrice();
        $specificPrice->id_shop = 0;
        $specificPrice->id_cart = 0;
        $specificPrice->id_shop_group = 0;
        $specificPrice->id_currency = $order->id_currency;
        $specificPrice->id_country = 0;
        $specificPrice->id_group = 0;
        $specificPrice->id_customer = $order->id_customer;
        $specificPrice->id_product = $product->id;
        $specificPrice->id_product_attribute = $combination ? $combination->id : 0;
        $specificPrice->price = (float) (string) $priceTaxExcluded;
        $specificPrice->from_quantity = 1;
        $specificPrice->reduction = 0;
        $specificPrice->reduction_type = 'amount';
        $specificPrice->reduction_tax = !$priceTaxIncluded->equals($priceTaxExcluded);
        $specificPrice->from = '0000-00-00 00:00:00';
        $specificPrice->to = '0000-00-00 00:00:00';
        $specificPrice->add();
        $this->temporarySpecificPrices[] = $specificPrice;
    }

    /**
     * This function extracts the newly added product from the cart and reformat the data in order to create a
     * dedicated OrderDetail with appropriate amounts
     *
     * @param Cart $cart
     * @param Product $product
     * @param int $quantity
     *
     * @return array
     */
    private function getCartProductData(Cart $cart, Product $product, int $quantity): array
    {
        $productItem = array_reduce($cart->getProducts(), function ($carry, $item) use ($product) {
            if (null !== $carry) {
                return $carry;
            }

            return $item['id_product'] == $product->id ? $item : null;
        });
        $productItem['cart_quantity'] = $quantity;

        switch (Configuration::get('PS_ROUND_TYPE')) {
            case Order::ROUND_TOTAL:
                $productItem['total'] = $productItem['price_with_reduction_without_tax'] * $quantity;
                $productItem['total_wt'] = $productItem['price_with_reduction'] * $quantity;

                break;
            case Order::ROUND_LINE:
                $productItem['total'] = Tools::ps_round(
                    $productItem['price_with_reduction_without_tax'] * $quantity,
                    $this->computingPrecision
                );
                $productItem['total_wt'] = Tools::ps_round(
                    $productItem['price_with_reduction'] * $quantity,
                    $this->computingPrecision
                );

                break;

            case Order::ROUND_ITEM:
            default:
                $productItem['total'] = Tools::ps_round(
                        $productItem['price_with_reduction_without_tax'],
                        $this->computingPrecision
                    ) * $quantity;
                $productItem['total_wt'] = Tools::ps_round(
                        $productItem['price_with_reduction'],
                        $this->computingPrecision
                    ) * $quantity;

                break;
        }

        return $productItem;
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
     * @param array $products
     *
     * @return OrderInvoice|null
     */
    private function createNewOrEditExistingInvoice(
        AddProductToOrderCommand $command,
        Order $order,
        Cart $cart,
        array $products
    ) {
        if ($order->hasInvoice()) {
            return $command->getOrderInvoiceId() ?
                $this->updateExistingInvoice($command->getOrderInvoiceId(), $cart, $products) :
                $this->createNewInvoice($order, $cart, $command->isFreeShipping(), $products);
        }

        return null;
    }

    /**
     * @param Order $order
     * @param Cart $cart
     * @param bool $isFreeShipping
     * @param array $newProducts
     * @param
     */
    private function createNewInvoice(Order $order, Cart $cart, $isFreeShipping, array $newProducts)
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

        $invoice->total_paid_tax_excl = Tools::ps_round(
            (float) $cart->getOrderTotal(false, $totalMethod, $newProducts),
            $this->computingPrecision
        );
        $invoice->total_paid_tax_incl = Tools::ps_round(
            (float) $cart->getOrderTotal(true, $totalMethod, $newProducts),
            $this->computingPrecision
        );
        $invoice->total_products = (float) $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS, $newProducts);
        $invoice->total_products_wt = (float) $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS, $newProducts);
        $invoice->total_shipping_tax_excl = (float) $cart->getTotalShippingCost(null, false);
        $invoice->total_shipping_tax_incl = (float) $cart->getTotalShippingCost();

        $invoice->total_wrapping_tax_excl = abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING, $newProducts));
        $invoice->total_wrapping_tax_incl = abs($cart->getOrderTotal(true, Cart::ONLY_WRAPPING, $newProducts));
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
     * @param Cart $cart
     * @param array $newProducts
     *
     * @return OrderInvoice
     */
    private function updateExistingInvoice($orderInvoiceId, Cart $cart, array $newProducts)
    {
        $invoice = new OrderInvoice($orderInvoiceId);

        $invoice->total_paid_tax_excl += Tools::ps_round(
            (float) $cart->getOrderTotal(false, Cart::BOTH_WITHOUT_SHIPPING, $newProducts),
            $this->computingPrecision
        );
        $invoice->total_paid_tax_incl += Tools::ps_round(
            (float) $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING, $newProducts),
            $this->computingPrecision
        );
        $invoice->total_products += (float) $cart->getOrderTotal(
            false,
            Cart::ONLY_PRODUCTS,
            $newProducts
        );
        $invoice->total_products_wt += (float) $cart->getOrderTotal(
            true,
            Cart::ONLY_PRODUCTS,
            $newProducts
        );

        $invoice->update();

        return $invoice;
    }

    /**
     * @param Product $product
     * @param AddProductToOrderCommand $command
     *
     * @throws ProductOutOfStockException
     */
    private function checkProductInStock(Product $product, AddProductToOrderCommand $command): void
    {
        if ($command->getCombinationId() > 0) {
            $isAvailableWhenOutOfStock = Product::isAvailableWhenOutOfStock($product->out_of_stock);
            $isEnoughQuantity = Attribute::checkAttributeQty(
                $command->getCombinationId(),
                $command->getProductQuantity()
            );

            if (!$isAvailableWhenOutOfStock && !$isEnoughQuantity) {
                throw new ProductOutOfStockException(sprintf('Product with id "%s" is out of stock, thus cannot be added to cart', $product->id));
            }

            return;
        }

        if (!$product->checkQty($command->getProductQuantity())) {
            throw new ProductOutOfStockException(sprintf('Product with id "%s" is out of stock, thus cannot be added to cart', $product->id));
        }
    }
}
