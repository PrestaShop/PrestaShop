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

use Address;
use Cart;
use Combination;
use Configuration;
use Context;
use Currency;
use Customer;
use Group;
use Order;
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Adapter\Number\RoundModeConverter;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;
use PrestaShopDatabaseException;
use PrestaShopException;
use Product;
use SpecificPrice;
use TaxManagerFactory;
use Validate;

/**
 * Reusable methods for Order subdomain command/query handlers.
 */
abstract class AbstractOrderHandler
{
    const COMPARISON_PRECISION = 6;

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
     * @param \PrestaShop\Decimal\Number $priceTaxIncluded
     * @param \PrestaShop\Decimal\Number $priceTaxExcluded
     * @param Order $order
     * @param Product $product
     * @param Combination|null $combination
     *
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     */
    protected function updateSpecificPrice(
        Number $priceTaxIncluded,
        Number $priceTaxExcluded,
        Order $order,
        Product $product,
        ?Combination $combination
    ): void {
        $productSpecificPrice = $this->getProductSpecificPriceInOrder($product, $order, $combination);
        $productOriginalPrice = $this->getProductRegularPrice($product, $order, $combination);

        // If provided price is equal to catalog price no need to have specific price
        if ($productOriginalPrice->equals($priceTaxExcluded)) {
            // Product specific price is not useful any more we can delete it
            if (null !== $productSpecificPrice) {
                $productSpecificPrice->delete();
            }

            return;
        }

        // If price tax excluded and price tax included don't match exactly, we use the price tax included as a base to recompute
        // the price tax excluded, this gives us more precision and decimals which avoids offset in later computing (totals)
        $precisePriceTaxExcluded = $this->getPrecisePriceTaxExcluded($priceTaxIncluded, $priceTaxExcluded, $order, $product);
        if (null !== $productSpecificPrice) {
            $productSpecificPrice->price = (float) (string) $precisePriceTaxExcluded;
            $productSpecificPrice->update();

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
        $specificPrice->price = (float) (string) $precisePriceTaxExcluded;
        $specificPrice->from_quantity = SpecificPrice::ORDER_DEFAULT_FROM_QUANTITY;
        $specificPrice->reduction = 0;
        $specificPrice->reduction_type = 'amount';
        $specificPrice->reduction_tax = !$priceTaxIncluded->equals($priceTaxExcluded);
        $specificPrice->from = SpecificPrice::ORDER_DEFAULT_DATE;
        $specificPrice->to = SpecificPrice::ORDER_DEFAULT_DATE;
        $specificPrice->add();
    }

    /**
     * @param \PrestaShop\Decimal\Number $priceTaxIncluded
     * @param \PrestaShop\Decimal\Number $priceTaxExcluded
     * @param Order $order
     * @param Product $product
     *
     * @return \PrestaShop\Decimal\Number
     */
    private function getPrecisePriceTaxExcluded(
        Number $priceTaxIncluded,
        Number $priceTaxExcluded,
        Order $order,
        Product $product
    ): Number {
        $taxAddress = new Address($order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)});
        $taxManager = TaxManagerFactory::getManager($taxAddress, Product::getIdTaxRulesGroupByIdProduct((int) $product->id, Context::getContext()));
        $productTaxCalculator = $taxManager->getTaxCalculator();
        $taxFactor = new Number((string) (1 + ($productTaxCalculator->getTotalRate() / 100)));

        $computedPriceTaxIncluded = $priceTaxExcluded->times($taxFactor);
        if ($computedPriceTaxIncluded->equals($priceTaxIncluded)) {
            return $priceTaxExcluded;
        }

        // When price tax included is computed based on price tax excluded there is a difference
        // so we recompute the price tax excluded based on the tax rate to have more precision
        return $priceTaxIncluded->dividedBy($taxFactor);
    }

    /**
     * @param Product $product
     * @param Order $order
     * @param Combination|null $combination
     *
     * @return SpecificPrice|null
     */
    protected function getProductSpecificPriceInOrder(
        Product $product,
        Order $order,
        ?Combination $combination
    ): ?SpecificPrice {
        // WARNING: DO NOT use SpecificPrice::getSpecificPrice as it filters out fields that are not in database
        // hence it ignores the customer or cart restriction and results are biased
        $existingSpecificPriceId = $order->getProductSpecificPriceId($product->id, $combination ? $combination->id : 0);

        if (empty($existingSpecificPriceId)) {
            return null;
        }

        return new SpecificPrice($existingSpecificPriceId);
    }

    /**
     * @param Product $product
     * @param Order $order
     * @param Combination|null $combination
     *
     * @return \PrestaShop\Decimal\Number
     */
    protected function getProductRegularPrice(
        Product $product,
        Order $order,
        ?Combination $combination
    ): Number {
        // Get price via getPriceStatic so that the catalog price rules are applied

        return new Number((string) Product::getPriceStatic(
            $product->id,
            false,
            null !== $combination ? $combination->id : 0,
            self::COMPARISON_PRECISION,
            null,
            false,
            true,
            1,
            false,
            $order->id_customer, // We still use the customer ID in case this customer has some special prices
            null, // But we keep the cart null as we don't want this order overridden price
            $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)}
        ));
    }

    /**
     * @return string
     */
    protected function getNumberRoundMode(): string
    {
        return RoundModeConverter::getNumberRoundMode((int) Configuration::get('PS_PRICE_ROUND_MODE'));
    }

    /**
     * Delivery option consists of deliveryAddress and carrierId.
     *
     * Legacy multishipping feature used comma separated carriers in delivery option (e.g. {'1':'6,7'}
     * Now that multishipping is gone - delivery option should consist of one carrier and one address.
     *
     * However the structure of deliveryOptions is still used with comma in legacy, so
     * this method provides assurance for deliveryOption structure until major refactoring
     *
     * @param int $carrierId
     *
     * @return string
     */
    protected function formatLegacyDeliveryOptionFromCarrierId(int $carrierId): string
    {
        return sprintf('%d,', $carrierId);
    }
}
