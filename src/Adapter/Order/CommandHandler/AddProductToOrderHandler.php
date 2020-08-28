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
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Adapter\Order\OrderAmountUpdater;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DuplicateProductInOrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DuplicateProductInOrderInvoiceException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\AddProductToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\CommandHandler\AddProductToOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductOutOfStockException;
use PrestaShopException;
use Product;
use Shop;
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
                $order,
                $product,
                $combination
            );

            $this->addProductToCart($cart, $product, $combination, $command->getProductQuantity());

            // Fetch Cart Product
            $productCart = $this->getCartProductData($cart, $product, $combination, $command->getProductQuantity());

            $newInvoice = null;
            if ($order->hasInvoice()) {
                if (empty($command->getOrderInvoiceId())) {
                    $newInvoice = $this->createNewInvoice($order, $cart, [$productCart], $command->withFreeShipping());
                } else {
                    $newInvoice = new OrderInvoice($command->getOrderInvoiceId());
                }
            }

            // Create Order detail information
            $orderDetail = $this->createOrderDetail(
                $order,
                $newInvoice,
                $cart,
                [$productCart]
            );

            // update order details
            $this->updateOrderDetailsWithSameProduct(
                $order,
                $orderDetail,
                $this->computingPrecision
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

            // Update totals amount of order
            $this->orderAmountUpdater->update($order, $cart);
            Hook::exec('actionOrderEdited', ['order' => $order]);
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
     * @param Order $order
     * @param Cart $cart
     * @param array $newProducts
     * @param bool|null $withFreeShipping
     */
    private function createNewInvoice(Order $order, Cart $cart, array $newProducts, ?bool $withFreeShipping)
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
        $invoice->shipping_tax_computation_method = (int) $taxCalculator->computation_method;
        // We don't need to compute the invoice totals because the OrderAmountUpdater will take care of it afterwards
        $invoice->add();

        $invoice->saveCarrierTaxCalculator($taxCalculator->getTaxesAmount($invoice->total_shipping_tax_excl));

        if (true === $withFreeShipping) {
            $this->addFreeShippingDiscount($order, $cart, $invoice);
        }

        return $invoice;
    }

    /**
     * @param Order $order
     * @param Cart $cart
     * @param OrderInvoice $orderInvoice
     *
     * @throws OrderException
     */
    private function addFreeShippingDiscount(Order $order, Cart $cart, OrderInvoice $orderInvoice): void
    {
        $defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT');
        $cartRuleObj = new CartRule();
        $cartRuleObj->id_customer = $order->id_customer;
        $cartRuleObj->date_from = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($order->date_add)));
        $cartRuleObj->date_to = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $cartRuleObj->name = [
            $defaultLangId => $this->translator->trans(
                'Free Shipping discount for invoice %s',
                ['%s' => $orderInvoice->getInvoiceNumberFormatted($defaultLangId, $order->id_shop)],
                'Admin.Orderscustomers.Notification'
            ),
        ];
        // This a one time cart rule, for a specific user that can only be used once
        $cartRuleObj->quantity = 1;
        $cartRuleObj->quantity_per_user = 1;
        $cartRuleObj->active = 0;
        $cartRuleObj->highlight = 0;
        $cartRuleObj->id_order_invoice = $orderInvoice->id;
        $cartRuleObj->free_shipping = 1;

        try {
            if (!$cartRuleObj->add()) {
                throw new OrderException('An error occurred during the CartRule creation');
            }
        } catch (PrestaShopException $e) {
            throw new OrderException('An error occurred during the CartRule creation', 0, $e);
        }

        try {
            // It's important to add the cart rule to the cart Or it will be ignored when cart performs AutoRemove AddAdd
            if (!$cart->addCartRule($cartRuleObj->id)) {
                throw new OrderException('An error occurred while adding CartRule to cart');
            }
        } catch (PrestaShopException $e) {
            throw new OrderException('An error occurred while adding CartRule to cart', 0, $e);
        }
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
     * @throws DuplicateProductInOrderInvoiceException
     */
    private function assertProductDuplicate(Order $order, AddProductToOrderCommand $command): void
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
