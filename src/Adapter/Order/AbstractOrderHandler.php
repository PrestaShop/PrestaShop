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
use Configuration;
use Context;
use Currency;
use Customer;
use Group;
use Order;
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use PrestaShopException;
use Product;
use SpecificPrice;
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
     * @param Number $priceTaxIncluded
     * @param Number $priceTaxExcluded
     * @param Order $order
     * @param Cart $cart
     * @param Product $product
     * @param Combination|null $combination
     *
     * @return SpecificPrice|void
     */
    protected function createSpecificPriceIfNeeded(
        Number $priceTaxIncluded,
        Number $priceTaxExcluded,
        Order $order,
        Cart $cart,
        Product $product,
        $combination
    ): ?SpecificPrice {
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
            return null;
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
            return null;
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

        return $specificPrice;
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
     * Clean all the specific prices that were created but this handler
     *
     * @param SpecificPrice[] $temporarySpecificPrices
     *
     * @throws \PrestaShopException
     */
    protected function clearTemporarySpecificPrices(array $temporarySpecificPrices): void
    {
        if (empty($temporarySpecificPrices)) {
            return;
        }

        foreach ($temporarySpecificPrices as $specificPrice) {
            $specificPrice->delete();
        }
    }

    /**
     * Create a specific price, or update it if it already exists
     *
     * @param Number $priceTaxIncluded
     * @param Number $priceTaxExcluded
     * @param int $productQuantity
     * @param Order $order
     * @param Product $product
     * @param $combination
     *
     * @return SpecificPrice
     *
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    protected function createOrUpdateSpecificPrice(
        Number $priceTaxIncluded,
        Number $priceTaxExcluded,
        int $productQuantity,
        Order $order,
        Product $product,
        $combination
    ): SpecificPrice {
        $existingSpecificPrice = SpecificPrice::getSpecificPrice(
            $product->id,
            0,
            $order->id_currency,
            0,
            0,
            $productQuantity,
            $combination ? $combination->id : 0,
            $order->id_customer
        );

        if (!empty($existingSpecificPrice)) {
            $specificPrice = new SpecificPrice($existingSpecificPrice['id_specific_price']);

            $specificPrice->price = $priceTaxExcluded;
            $specificPrice->update();

            return $specificPrice;
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

        return $specificPrice;
    }
}
