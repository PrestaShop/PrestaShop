<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Cache;
use Cart;
use Context;
use Order;
use Product;
use StockAvailable;
use Tests\Resources\DatabaseDump;
use Tests\TestCase\SymfonyIntegrationTestCase;

final class ProductGetQuantityCartOrderTest extends SymfonyIntegrationTestCase
{
    public static function tearDownAfterClass(): void
    {
        try {
            DatabaseDump::restoreAllTables();
            Product::resetStaticCache();
            Cart::resetStaticCache();
            Cache::clean('*');
        } finally {
            parent::tearDownAfterClass();
        }
    }

    public function testCartQuantityIsNoLongerSubtractedOnceTheOrderIsAdded(): void
    {
        $product = $this->createProduct();
        StockAvailable::setQuantity((int) $product->id, 0, 10);
        $cart = $this->createCart();
        self::assertTrue($cart->updateQty(3, (int) $product->id));
        Cart::resetStaticCache();

        self::assertSame(7, Product::getQuantity((int) $product->id, 0, null, $cart));
        self::assertSame(7, Product::getQuantity((int) $product->id, 0, null, $cart));

        $this->createOrder($cart);

        self::assertSame(10, Product::getQuantity((int) $product->id, 0, null, $cart));
    }

    private function createProduct(): Product
    {
        $product = new Product(null, false, (int) Context::getContext()->language->id);
        $product->name = 'Cart order quantity';
        $product->link_rewrite = 'cart-order-quantity';
        $product->active = true;
        $product->available_for_order = true;
        $product->price = 10.0;
        self::assertTrue($product->add());

        return $product;
    }

    private function createCart(): Cart
    {
        $context = Context::getContext();
        $cart = new Cart();
        $cart->id_lang = (int) $context->language->id;
        $cart->id_currency = (int) $context->currency->id;
        $cart->id_shop = (int) $context->shop->id;
        $cart->id_shop_group = (int) $context->shop->id_shop_group;
        self::assertTrue($cart->add());

        return $cart;
    }

    private function createOrder(Cart $cart): void
    {
        $order = new Order();
        $order->id_cart = (int) $cart->id;
        $order->id_customer = 1;
        $order->id_address_delivery = 1;
        $order->id_address_invoice = 1;
        $order->id_currency = (int) $cart->id_currency;
        $order->id_lang = (int) $cart->id_lang;
        $order->id_shop = (int) $cart->id_shop;
        $order->id_shop_group = (int) $cart->id_shop_group;
        $order->id_carrier = 0;
        $order->secure_key = md5('product-get-quantity-cart-order');
        $order->payment = 'Test payment';
        $order->module = 'test';
        $order->total_paid = $order->total_paid_real = 0.0;
        $order->total_products = $order->total_products_wt = 0.0;
        $order->conversion_rate = 1.0;
        self::assertTrue($order->add());
    }
}
