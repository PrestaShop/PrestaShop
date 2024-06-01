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
use Carrier;
use Cart;
use CartRule;
use Combination;
use Configuration;
use Context;
use Currency;
use Customer;
use Hook;
use Order;
use OrderCarrier;
use OrderDetail;
use OrderInvoice;
use PrestaShop\PrestaShop\Adapter\Cart\Comparator\CartProductsComparator;
use PrestaShop\PrestaShop\Adapter\Cart\Comparator\CartProductUpdate;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Adapter\Order\OrderAmountUpdater;
use PrestaShop\PrestaShop\Adapter\Order\OrderDetailUpdater;
use PrestaShop\PrestaShop\Adapter\Order\OrderProductQuantityUpdater;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DuplicateProductInOrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DuplicateProductInOrderInvoiceException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\AddProductToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\CommandHandler\AddProductToOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductOutOfStockException;
use Product;
use ProductAttribute;
use Shop;
use StockAvailable;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;

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
     * @var OrderProductQuantityUpdater
     */
    private $orderProductQuantityUpdater;

    /**
     * @var OrderDetailUpdater
     */
    private $orderDetailUpdater;

    /**
     * @param TranslatorInterface $translator
     * @param ContextStateManager $contextStateManager
     * @param OrderAmountUpdater $orderAmountUpdater
     * @param OrderProductQuantityUpdater $orderProductQuantityUpdater
     * @param OrderDetailUpdater $orderDetailUpdater
     */
    public function __construct(
        TranslatorInterface $translator,
        ContextStateManager $contextStateManager,
        OrderAmountUpdater $orderAmountUpdater,
        OrderProductQuantityUpdater $orderProductQuantityUpdater,
        OrderDetailUpdater $orderDetailUpdater
    ) {
        $this->context = Context::getContext();
        $this->translator = $translator;
        $this->contextStateManager = $contextStateManager;
        $this->orderAmountUpdater = $orderAmountUpdater;
        $this->orderProductQuantityUpdater = $orderProductQuantityUpdater;
        $this->orderDetailUpdater = $orderDetailUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddProductToOrderCommand $command)
    {
        $order = $this->getOrder($command->getOrderId());

        $this->assertOrderWasNotShipped($order);
        $this->assertProductNotDuplicate($order, $command);

        $cart = Cart::getCartByOrderId($order->id);
        if (!($cart instanceof Cart)) {
            throw new OrderException('Cart linked to the order cannot be found.');
        }

        $product = $this->getProduct($command->getProductId(), (int) $order->getAssociatedLanguage()->getId());
        $combination = null !== $command->getCombinationId() ? $this->getCombination($command->getCombinationId()->getValue()) : null;
        $combinationId = null !== $combination ? (int) $combination->id : 0;

        $this->contextStateManager
            ->setCurrency(new Currency($order->id_currency))
            ->setCustomer(new Customer($order->id_customer))
            ->setCart($cart)
            ->setShop(new Shop($order->id_shop))
        ;

        $this->computingPrecision = $this->getPrecisionFromCart($cart);
        try {
            $this->checkProductInStock($product, $command, (int) $order->id_shop);

            $cartComparator = new CartProductsComparator($cart);
            $this->addProductToCart($cart, $product, $combination, $command->getProductQuantity());
            $updatedCartProducts = $cart->getProducts(true);

            // We get all the modification (creation AND updates) and then filter them
            $productModifications = $cartComparator->getModifiedProducts();
            $creationModifications = [];
            $updateModifications = [];
            foreach ($productModifications as $cartProductUpdate) {
                // Real creation is naturally a creation update
                if ($cartProductUpdate->isCreated()) {
                    $creationModifications[] = $cartProductUpdate;
                    continue;
                }

                // Now we check if the update is about the currently added product This is important for multi invoice orders, in case
                // the added product was already in previous invoices
                $cartCombinationId = null !== $cartProductUpdate->getCombinationId() ? $cartProductUpdate->getCombinationId()->getValue() : 0;
                if ($cartProductUpdate->getProductId()->getValue() === (int) $product->id && $cartCombinationId === $combinationId) {
                    $creationModifications[] = $cartProductUpdate;
                } else {
                    $updateModifications[] = $cartProductUpdate;
                }
            }

            $createdProducts = $this->getCreatedCartProducts(
                $creationModifications,
                $updatedCartProducts
            );

            $invoice = $this->createNewOrEditExistingInvoice(
                $command,
                $order,
                $cart,
                $createdProducts
            );

            // Create Order detail information
            $this->createOrderDetails(
                $order,
                $invoice,
                $cart,
                $createdProducts
            );

            // Once OrderDetail has been created we update it (and identical ones) with the correct price
            $this->orderDetailUpdater->updateOrderDetailsForProduct(
                $order,
                $command->getProductId()->getValue(),
                null !== $command->getCombinationId() ? $command->getCombinationId()->getValue() : 0,
                $command->getProductPriceTaxExcluded(),
                $command->getProductPriceTaxIncluded(),
                0
            );
            StockAvailable::synchronize($product->id);

            $this->updateAffectedOrderDetails(
                $order,
                $updateModifications
            );

            // Update totals amount of order
            $this->orderAmountUpdater->update($order, $cart, null !== $invoice ? (int) $invoice->id : null);
            Hook::exec('actionOrderEdited', ['order' => $order]);
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }
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
     * @param array $cartProducts
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function createOrderDetails(Order $order, ?OrderInvoice $invoice, Cart $cart, array $cartProducts): void
    {
        $orderDetail = new OrderDetail();
        $orderDetail->createList(
            $order,
            $cart,
            $order->getCurrentState(),
            $cartProducts,
            !empty($invoice->id) ? $invoice->id : 0
        );
    }

    /**
     * When a product is added some other products may be affected (because of CartRule that would
     * add some gift for example) So we update products which have been modified in the cart and
     * update the related OrderDetail
     *
     * @param Order $order
     * @param CartProductUpdate[] $updatedProducts
     *
     * @throws OrderException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function updateAffectedOrderDetails(
        Order $order,
        array $updatedProducts
    ): void {
        $orderDetails = $order->getOrderDetailList();
        foreach ($updatedProducts as $updatedProduct) {
            $updatedCombinationId = null !== $updatedProduct->getCombinationId() ? $updatedProduct->getCombinationId()->getValue() : 0;
            $affectedOrderDetail = null;
            foreach ($orderDetails as $orderDetailData) {
                if ((int) $orderDetailData['product_id'] === $updatedProduct->getProductId()->getValue()
                    && (int) $orderDetailData['product_attribute_id'] === $updatedCombinationId) {
                    $affectedOrderDetail = new OrderDetail($orderDetailData['id_order_detail']);
                    break;
                }
            }

            $newUpdatedQuantity = (int) $affectedOrderDetail->product_quantity + $updatedProduct->getDeltaQuantity();
            $orderInvoice = (int) $affectedOrderDetail->id_order_invoice !== 0 ? new OrderInvoice($affectedOrderDetail->id_order_invoice) : null;
            $this->orderProductQuantityUpdater->update(
                $order,
                $affectedOrderDetail,
                $newUpdatedQuantity,
                $orderInvoice,
                false
            );
        }
    }

    /**
     * @param CartProductUpdate[] $creationUpdates
     * @param CartProductUpdate[] $cartProducts
     *
     * @return array
     */
    private function getCreatedCartProducts(
        array $creationUpdates,
        array $cartProducts
    ): array {
        $additionalProducts = [];
        foreach ($creationUpdates as $additionalUpdate) {
            $updateProductId = $additionalUpdate->getProductId()->getValue();
            $updateCombinationId = null !== $additionalUpdate->getCombinationId() ? $additionalUpdate->getCombinationId()->getValue() : 0;
            $updateCustomizationId = null !== $additionalUpdate->getCustomizationId() ? $additionalUpdate->getCustomizationId()->getValue() : 0;
            $cartProduct = $this->getMatchingProduct($cartProducts, [
                'id_product' => $updateProductId,
                'id_product_attribute' => $updateCombinationId,
                'id_customization' => $updateCustomizationId,
            ]);
            $cartProduct['cart_quantity'] = $additionalUpdate->getDeltaQuantity();
            $additionalProducts[] = $cartProduct;
        }

        return $additionalProducts;
    }

    /**
     * Find a specific product among the product array
     *
     * @param array $productList
     * @param array $searchedProduct
     *
     * @return array|null
     */
    private function getMatchingProduct(array $productList, array $searchedProduct): ?array
    {
        return array_reduce($productList, function ($carry, $item) use ($searchedProduct) {
            if (null !== $carry) {
                return $carry;
            }

            $productMatch = $item['id_product'] == $searchedProduct['id_product'];
            $combinationMatch = $item['id_product_attribute'] == $searchedProduct['id_product_attribute'];
            $customizationMatch = $item['id_customization'] == $searchedProduct['id_customization'];

            return $productMatch && $combinationMatch && $customizationMatch ? $item : null;
        });
    }

    /**
     * @param Cart $cart
     * @param Product $product
     * @param Combination|null $combination
     * @param int $quantity
     */
    private function addProductToCart(Cart $cart, Product $product, $combination, $quantity): void
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
                ? ProductAttribute::getAttributeMinimalQty($combination->id) :
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
                $this->createNewInvoice($order, $cart, $command->hasFreeShipping(), $products);
        }

        return null;
    }

    /**
     * @todo: Most of this method can be simplified, since OrderAmountUpdater computes everything
     *        the invoice computation here should be removable, as well as $order->addCartRule
     *
     * @param Order $order
     * @param Cart $cart
     * @param bool $isFreeShipping
     * @param array $newProducts
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
            $freeShippingCartRule->active = true;
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
     * @todo: this whole method should be removable as well since ALL invoices are updated by OrderAmountUpdater
     *
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
     * @param int $shopId
     *
     * @throws ProductOutOfStockException
     */
    private function checkProductInStock(Product $product, AddProductToOrderCommand $command, int $shopId): void
    {
        //check if product is available in stock
        if (!Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock($command->getProductId()->getValue()))) {
            $combinationId = null !== $command->getCombinationId() ? $command->getCombinationId()->getValue() : 0;
            $availableQuantity = StockAvailable::getQuantityAvailableByProduct(
                $command->getProductId()->getValue(),
                $combinationId,
                $shopId
            );

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
     * @throws DuplicateProductInOrderInvoiceException
     */
    private function assertProductNotDuplicate(Order $order, AddProductToOrderCommand $command): void
    {
        $invoicesContainingProduct = [];
        foreach ($order->getOrderDetailList() as $orderDetail) {
            if ($command->getProductId()->getValue() !== (int) $orderDetail['product_id']) {
                continue;
            }
            if (!empty($command->getCombinationId()) && $command->getCombinationId()->getValue() !== (int) $orderDetail['product_attribute_id']) {
                continue;
            }
            $invoicesContainingProduct[] = (int) $orderDetail['id_order_invoice'];
        }

        if (empty($invoicesContainingProduct)) {
            return;
        }

        // If it's a new invoice (or no invoice), the ID is null, so we check if the Order has invoice (in which case
        // a new one is going to be created) If it doesn't have invoices we don't allow adding duplicate OrderDetail
        if (empty($command->getOrderInvoiceId()) && !$order->hasInvoice()) {
            throw new DuplicateProductInOrderException('You cannot add this product in the order as it is already present');
        }

        // If we are targeting a specific invoice check that the ID has not been found in the OrderDetail list
        if (!empty($command->getOrderInvoiceId()) && in_array((int) $command->getOrderInvoiceId(), $invoicesContainingProduct)) {
            $orderInvoice = new OrderInvoice($command->getOrderInvoiceId());
            $invoiceNumber = $orderInvoice->getInvoiceNumberFormatted((int) Configuration::get('PS_LANG_DEFAULT'), $order->id_shop);
            throw new DuplicateProductInOrderInvoiceException($invoiceNumber, 'You cannot add this product in this invoice as it is already present');
        }
    }
}
