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

use Cart;
use Combination;
use Configuration;
use Context;
use Customization;
use Hook;
use Order;
use OrderDetail;
use OrderInvoice;
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Adapter\Order\OrderProductQuantityUpdater;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\CannotEditDeliveredOrderProductException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\UpdateProductInOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\CommandHandler\UpdateProductInOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductOutOfStockException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use Product;
use SpecificPrice;
use StockAvailable;
use Validate;

/**
 * @internal
 */
final class UpdateProductInOrderHandler extends AbstractOrderHandler implements UpdateProductInOrderHandlerInterface
{
    /**
     * @var OrderProductQuantityUpdater
     */
    private $orderProductQuantityUpdater;

    /**
     * @var array
     */
    private $temporarySpecificPrices;

    public function __construct(OrderProductQuantityUpdater $orderProductQuantityUpdater)
    {
        $this->orderProductQuantityUpdater = $orderProductQuantityUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductInOrderCommand $command)
    {
        // Return value
        $res = true;

        $order = $this->getOrderObject($command->getOrderId());
        $orderDetail = new OrderDetail($command->getOrderDetailId());
        $orderInvoice = null;
        if (!empty($command->getOrderInvoiceId())) {
            $orderInvoice = new OrderInvoice($command->getOrderInvoiceId());
        }

        // Check fields validity
        $this->assertProductCanBeUpdated($command, $orderDetail, $order, $orderInvoice);

        if (0 < $orderDetail->id_customization) {
            $customization = new Customization($orderDetail->id_customization);
            $customization->quantity = $command->getQuantity();
            $customization->save();
        }
        $product_quantity = $command->getQuantity();

        // @todo: use https://github.com/PrestaShop/decimal for price computations
        $product_price_tax_incl = (float) $command->getPriceTaxIncluded()->round(2);
        $product_price_tax_excl = (float) $command->getPriceTaxExcluded()->round(2);
        $total_products_tax_incl = $product_price_tax_incl * $product_quantity;
        $total_products_tax_excl = $product_price_tax_excl * $product_quantity;

        // Calculate differences of price (Before / After)
        $diff_price_tax_incl = $total_products_tax_incl - $orderDetail->total_price_tax_incl;
        $diff_price_tax_excl = $total_products_tax_excl - $orderDetail->total_price_tax_excl;
        if ($diff_price_tax_incl != 0 && $diff_price_tax_excl != 0) {
            $orderDetail->unit_price_tax_excl = $product_price_tax_excl;
            $orderDetail->unit_price_tax_incl = $product_price_tax_incl;

            $orderDetail->total_price_tax_incl += $diff_price_tax_incl;
            $orderDetail->total_price_tax_excl += $diff_price_tax_excl;

            $cart = $this->createNewOrEditExistingCart($order);
            $product = $this->getProductObject(new ProductId((int) $orderDetail->product_id), (int) $order->id_lang);
            $combination = $this->getCombination((int) $orderDetail->product_attribute_id);

            // Add specific price for the product being added
            $this->createSpecificPriceIfNeeded(
                $command->getPriceTaxIncluded(),
                $command->getPriceTaxExcluded(),
                $order,
                $cart,
                $product,
                $combination
            );

            // Apply changes on Order
            $order = new Order($orderDetail->id_order);
            $order->total_products += $diff_price_tax_excl;
            $order->total_products_wt += $diff_price_tax_incl;

            $order->total_paid += $diff_price_tax_incl;
            $order->total_paid_tax_excl += $diff_price_tax_excl;
            $order->total_paid_tax_incl += $diff_price_tax_incl;

            $res &= $order->update();
        }

        // Update quantity and amounts
        $order = $this->orderProductQuantityUpdater->update($order, $orderDetail, $product_quantity, $orderInvoice);

        // Delete temporary specific prices
        $this->clearTemporarySpecificPrices();

        if (!$res) {
            throw new OrderException('An error occurred while editing the product line.');
        }

        Hook::exec('actionOrderEdited', ['order' => $order]);
    }

    /**
     * @param UpdateProductInOrderCommand $command
     * @param OrderDetail $orderDetail
     * @param Order $order
     * @param OrderInvoice|null $orderInvoice
     *
     * @throws OrderException
     */
    private function assertProductCanBeUpdated(
        UpdateProductInOrderCommand $command,
        OrderDetail $orderDetail,
        Order $order,
        OrderInvoice $orderInvoice = null
    ) {
        if (!Validate::isLoadedObject($orderDetail)) {
            throw new OrderException('The Order Detail object could not be loaded.');
        }

        if (null !== $orderInvoice && !Validate::isLoadedObject($orderInvoice)) {
            throw new OrderException('The invoice object cannot be loaded.');
        }

        if (!Validate::isLoadedObject($order)) {
            throw new OrderException('The order object cannot be loaded.');
        }

        if ($orderDetail->id_order != $order->id) {
            throw new OrderException('You cannot edit the order detail for this order.');
        }

        // We can't edit a delivered order
        if ($order->hasBeenDelivered()) {
            throw new CannotEditDeliveredOrderProductException('You cannot edit a delivered order.');
        }

        if (null !== $orderInvoice && $orderInvoice->id_order != $order->id) {
            throw new OrderException('You cannot use this invoice for the order');
        }

        if ($command->getPriceTaxIncluded()->isNegative() || $command->getPriceTaxExcluded()->isNegative()) {
            throw new OrderException('Invalid price');
        }

        if (!is_array($command->getQuantity())
            && !Validate::isUnsignedInt($command->getQuantity())
        ) {
            throw new OrderException('Invalid quantity');
        }

        // @todo: check if quantity can be array
//        if (is_array($command->getQuantity())) {
//            foreach ($command->getQuantity() as $qty) {
//                if (!Validate::isUnsignedInt($qty)) {
//                    throw new OrderException('Invalid quantity');
//                }
//            }
//        }

        //check if product is available in stock
        if (!\Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock($orderDetail->product_id))) {
            $availableQuantity = StockAvailable::getQuantityAvailableByProduct($orderDetail->product_id, $orderDetail->product_attribute_id);

            if ($availableQuantity < $command->getQuantity()) {
                throw new ProductOutOfStockException('Not enough products in stock');
            }
        }
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
            $this->getPrecisionFromCart($cart),
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

        Context::getContext()->cart = $cart;

        return $cart;
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
}
