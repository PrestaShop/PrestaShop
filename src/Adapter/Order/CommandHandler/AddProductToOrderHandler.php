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

declare(strict_types=1);

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
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DuplicateProductInOrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\AddProductToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\CommandHandler\AddProductToOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductOutOfStockException;
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
     * @var int
     */
    private $computingPrecision;

    /**
     * @param TranslatorInterface $translator
     * @param ContextStateManager $contextStateManager
     * @param OrderAmountUpdater $orderAmountUpdater
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
        $order = $this->getOrder($command->getOrderId());

        $this->contextStateManager
            ->setCurrency(new Currency($order->id_currency))
            ->setCustomer(new Customer($order->id_customer));

        // Get context precision just in case
        $this->computingPrecision = $this->context->getComputingPrecision();
        try {
            $this->assertOrderWasNotShipped($order);
            $this->assertProductDuplicate($order, $command);

            $product = $this->getProduct($command->getProductId(), (int) $order->id_lang);
            $combination = null !== $command->getCombinationId() ? $this->getCombination($command->getCombinationId()->getValue()) : null;

            $this->checkProductInStock($product, $command);

            $cart = Cart::getCartByOrderId($order->id);
            if (!($cart instanceof Cart)) {
                throw new OrderException('Cart linked to the order cannot be found.');
            }
            $this->contextStateManager->setCart($cart);
            // Cart precision is more adapted
            $this->computingPrecision = $this->getPrecisionFromCart($cart);

            $this->updateSpecificPrice(
                $command->getProductPriceTaxIncluded(),
                $command->getProductPriceTaxExcluded(),
                $command->getProductQuantity(),
                $order,
                $product,
                $combination
            );

            $this->addProductToCart($cart, $product, $combination, $command->getProductQuantity());

            // Fetch Cart Product
            $productCart = $this->getCartProductData($cart, $product, $combination, $command->getProductQuantity());

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

            // update order details
            $this->updateOrderDetails(
                $order,
                $orderDetail,
                $product,
                $command->getCombinationId() ? $command->getCombinationId()->getValue() : null,
                $command->getProductQuantity(),
                $command->getProductPriceTaxIncluded(),
                $command->getProductPriceTaxExcluded()
            );

            $this->updateOrderInvoices($order);

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
        } catch (Exception $e) {
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
     * @return SpecificPrice[]
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function restoreOrderProductsSpecificPrices(Order $order, Cart $cart): array
    {
        $specificPrices = [];
        foreach ($order->getOrderDetailList() as $row) {
            $orderDetail = new OrderDetail($row['id_order_detail']);
            $product = new Product((int) $orderDetail->product_id);

            $specificPrice = $this->createSpecificPriceIfNeeded(
                new Number((string) $orderDetail->unit_price_tax_incl),
                new Number((string) $orderDetail->unit_price_tax_excl),
                $order,
                $cart,
                $product,
                new Combination($orderDetail->product_attribute_id)
            );

            if (null !== $specificPrice) {
                $specificPrices[] = $specificPrice;
            }
        }

        return $specificPrices;
    }

    /**
     * This function extracts the newly added product from the cart and reformat the data in order to create a
     * dedicated OrderDetail with appropriate amounts
     *
     * @param Cart $cart
     * @param Product $product
     * @param Combination|null $combination
     * @param int $quantity
     *
     * @return array
     */
    private function getCartProductData(Cart $cart, Product $product, ?Combination $combination, int $quantity): array
    {
        $productItem = array_reduce($cart->getProducts(), function ($carry, $item) use ($product, $combination) {
            if (null !== $carry) {
                return $carry;
            }

            $productMatch = $item['id_product'] == $product->id;
            $combinationMatch = $combination === null || $item['id_product_attribute'] == $combination->id;

            return $productMatch && $combinationMatch ? $item : null;
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
        /**
         * Here we update product and customization in the cart.
         *
         * The last argument "skip quantity check" is set to true because
         * 1) the quantity has already been checked,
         * 2) (main reason) when the cart checks the availability ; it substracts
         * its own quantity from available stock.
         *
         * This is because a product in a cart is not really out of the stock, because it is not checked out yet.
         *
         * Here we are editing an order, not a cart, so what has been ordered
         * has already been substracted from the stock.
         */
        $result = $cart->updateQty(
            $quantity,
            $product->id,
            $combination ? $combination->id : null,
            false,
            'up',
            0,
            new Shop($cart->id_shop),
            true,
            true
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
        //check if product is available in stock
        if (!Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock($command->getProductId()->getValue()))) {
            $combinationId = null !== $command->getCombinationId() ? $command->getCombinationId()->getValue() : 0;
            $availableQuantity = StockAvailable::getQuantityAvailableByProduct($command->getProductId()->getValue(), $combinationId);

            if ($availableQuantity < $command->getProductQuantity()) {
                throw new ProductOutOfStockException(sprintf('Product with id "%s" is out of stock, thus cannot be added to cart', $product->id));
            }
        }
    }

    /**
     * @param Order $order
     * @param AddProductToOrderCommand $command
     *
     * @throws DuplicateProductInOrderException
     */
    private function assertProductDuplicate(Order $order, AddProductToOrderCommand $command): void
    {
        $invoicesContainingProduct = [];
        foreach ($order->getOrderDetailList() as $orderDetail) {
            if ($command->getProductId()->getValue() !== (int) $orderDetail['product_id']) {
                continue;
            }
            if (!empty($command->getCombinationId()) && $command->getCombinationId() !== (int) $orderDetail['product_attribute_id']) {
                continue;
            }
            $invoicesContainingProduct[] = (int) $orderDetail['id_order_invoice'];
        }

        if (empty($invoicesContainingProduct)) {
            return;
        }

        // If it's a new invoice (or no invoice), the ID is null, so we check if the Order has invoice (in which case
        // a new one is going to be created) If it doesn't have invoices we do'nt allow adding duplicate OrderDetail
        if (empty($command->getOrderInvoiceId()) && !$order->hasInvoice()) {
            throw new DuplicateProductInOrderException('You cannot add this product in the order has it is already present');
        }

        // If we are targeting a specific invoice check that the ID has not been found in the OrderDetail list
        if (!empty($command->getOrderInvoiceId()) && in_array((int) $command->getOrderInvoiceId(), $invoicesContainingProduct)) {
            throw new DuplicateProductInOrderException('You cannot add this product in the order has it is already present');
        }
    }

    /**
     * Update order details after a specific price has been created or updated
     *
     * @param Order $order
     * @param OrderDetail $updatedOrderDetail
     * @param Product $product
     * @param int|null $combinationId
     * @param int $productQuantity
     * @param Number $priceTaxIncluded
     * @param Number $priceTaxExcluded
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function updateOrderDetails(
        Order $order,
        OrderDetail $updatedOrderDetail,
        Product $product,
        ?int $combinationId,
        int $productQuantity,
        Number $priceTaxIncluded,
        Number $priceTaxExcluded
    ): void {
        foreach ($order->getOrderDetailList() as $row) {
            $orderDetail = new OrderDetail($row['id_order_detail']);
            if ((int) $orderDetail->product_id !== (int) $product->id) {
                continue;
            }
            if (!empty($combinationId) && (int) $combinationId !== (int) $orderDetail->product_attribute_id) {
                continue;
            }
            if ($updatedOrderDetail->id == $orderDetail->id) {
                continue;
            }
            $orderDetail->unit_price_tax_excl = (float) (string) $priceTaxExcluded;
            $orderDetail->unit_price_tax_incl = (float) (string) $priceTaxIncluded;
            $orderDetail->total_price_tax_excl = Tools::ps_round((float) (string) $priceTaxExcluded * $orderDetail->product_quantity, $this->computingPrecision);
            $orderDetail->total_price_tax_incl = Tools::ps_round((float) (string) $priceTaxIncluded * $orderDetail->product_quantity, $this->computingPrecision);

            $orderDetail->update();
        }
    }

    /**
     * This method takes care of multi-invoices, all invoices are updated
     *
     * @param Order $order
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function updateOrderInvoices(Order $order): void
    {
        $orderInvoices = OrderInvoice::getInvoicesByOrderId($order->id);

        foreach ($orderInvoices as $invoice) {
            $totalProductsTaxExcluded = 0;
            $totalProductsTaxIncluded = 0;
            foreach ($invoice->getProducts() as $product) {
                $totalProductsTaxExcluded += (float) $product['total_price_tax_excl'];
                $totalProductsTaxIncluded += (float) $product['total_price_tax_incl'];
            }
            $invoice->total_products_wt = (float) Tools::ps_round($totalProductsTaxExcluded, $this->computingPrecision);
            $invoice->total_products = (float) Tools::ps_round($totalProductsTaxIncluded, $this->computingPrecision);

            $invoice->total_paid_tax_excl = (float) Tools::ps_round($totalProductsTaxExcluded + $invoice->total_shipping_tax_excl, $this->computingPrecision);
            $invoice->total_paid_tax_incl = (float) Tools::ps_round($totalProductsTaxIncluded + $invoice->total_shipping_tax_incl, $this->computingPrecision);

            $invoice->update();
        }
    }
}
