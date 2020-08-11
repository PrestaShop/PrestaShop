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

namespace PrestaShop\PrestaShop\Adapter\Order;

use Cart;
use Combination;
use Context;
use Currency;
use Customer;
use Group;
use Order;
use OrderDetail;
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;
use PrestaShopException;
use Product;
use SpecificPrice;
use Tools;
use Validate;

/**
 * Reusable methods for Order subdomain command/query handlers.
 */
abstract class AbstractOrderHandler
{
    /**
     * @param OrderId $orderId
     *
     * @return Order
     */
    protected function getOrder(OrderId $orderId)
    {
        try {
            $order = new Order($orderId->getValue());
        } catch (PrestaShopException $e) {
            throw new OrderException(
                sprintf(
                    'Error occured when trying to get order object #%s',
                    $orderId->getValue()
                ),
                0,
                $e
            );
        }

        if ($order->id !== $orderId->getValue()) {
            throw new OrderNotFoundException($orderId, sprintf('Order with id "%d" was not found.', $orderId->getValue()));
        }

        return $order;
    }

    /**
     * @param Order $order
     *
     * @return bool
     */
    protected function isTaxIncludedInOrder(Order $order): bool
    {
        return $this->getOrderTaxCalculationMethod($order) === PS_TAX_INC;
    }

    /**
     * @param Order $order
     *
     * @return int
     */
    protected function getOrderTaxCalculationMethod(Order $order): int
    {
        $customer = new Customer($order->id_customer);

        return Group::getPriceDisplayMethod((int) $customer->id_default_group);
    }

    /**
     * @param Cart $cart
     *
     * @return int
     */
    protected function getPrecisionFromCart(Cart $cart): int
    {
        $computingPrecision = new ComputingPrecision();
        $currency = new Currency((int) $cart->id_currency);

        return $computingPrecision->getPrecision($currency->precision);
    }

    /**
     * @param Order $order
     *
     * @return Cart
     */
    protected function createNewOrEditExistingCart(Order $order)
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
    protected function getCombination($combinationId)
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
    protected function getProduct(ProductId $productId, $langId)
    {
        $product = new Product($productId->getValue(), false, $langId);

        if ($product->id !== $productId->getValue()) {
            throw new OrderException(sprintf('Product with id "%d" is invalid.', $productId->getValue()));
        }

        return $product;
    }

    /**
     * Create a specific price, or update it if it already exists
     *
     * @param Number $priceTaxIncluded
     * @param Number $priceTaxExcluded
     * @param Order $order
     * @param Product $product
     * @param $combination
     *
     * @return SpecificPrice
     *
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    protected function updateSpecificPrice(
        Number $priceTaxIncluded,
        Number $priceTaxExcluded,
        Order $order,
        Product $product,
        $combination
    ): void {
        $existingSpecificPrice = SpecificPrice::getSpecificPrice(
            $product->id,
            0,
            $order->id_currency,
            0,
            0,
            0,
            $combination ? $combination->id : 0,
            $order->id_customer,
            $order->id_cart
        );

        $productOriginalPrice = new Number((string) $product->price);

        if ($productOriginalPrice->equals($priceTaxExcluded)) {
            if (!empty($existingSpecificPrice)) {
                $specificPrice = new SpecificPrice($existingSpecificPrice['id_specific_price']);
                $specificPrice->delete();
            }

            return;
        }

        if (!empty($existingSpecificPrice)) {
            $specificPrice = new SpecificPrice($existingSpecificPrice['id_specific_price']);
            $specificPrice->price = $priceTaxExcluded;
            $specificPrice->update();

            return;
        }

        $specificPrice = new SpecificPrice();
        $specificPrice->id_shop = 0;
        $specificPrice->id_cart = $order->id_cart;
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
    }

    /**
     * Update order details after a specific price has been created or updated
     *
     * @param Order $order
     * @param OrderDetail $updatedOrderDetail
     * @param int $productQuantity
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    protected function updateOrderDetailsWithSameProduct(
        Order $order,
        OrderDetail $updatedOrderDetail,
        int $computingPrecision
    ): void {
        foreach ($order->getOrderDetailList() as $row) {
            $orderDetail = new OrderDetail($row['id_order_detail']);
            if ((int) $orderDetail->product_id !== (int) $updatedOrderDetail->product_id) {
                continue;
            }
            if (!empty($updatedOrderDetail->product_attribute_id) && (int) $updatedOrderDetail->product_attribute_id !== (int) $orderDetail->product_attribute_id) {
                continue;
            }
            if ($updatedOrderDetail->id == $orderDetail->id) {
                continue;
            }
            $orderDetail->unit_price_tax_excl = (float) $updatedOrderDetail->unit_price_tax_excl;
            $orderDetail->unit_price_tax_incl = (float) $updatedOrderDetail->unit_price_tax_incl;
            $orderDetail->total_price_tax_excl = Tools::ps_round((float) $updatedOrderDetail->unit_price_tax_excl * $orderDetail->product_quantity, $computingPrecision);
            $orderDetail->total_price_tax_incl = Tools::ps_round((float) $updatedOrderDetail->unit_price_tax_incl * $orderDetail->product_quantity, $computingPrecision);

            $orderDetail->update();
        }
    }
}
