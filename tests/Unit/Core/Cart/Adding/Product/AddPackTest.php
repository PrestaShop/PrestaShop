<?php
/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Cart\Adding\CartRule;

use Configuration;
use Product;
use Pack;
use StockAvailable;
use Tests\Unit\Core\Cart\AbstractCartTest;

class AddPackTest extends AbstractCartTest
{
    /** @var int */
    const ID_PACK_FIXTURE = 6;

    /** @var int */
    const ID_PRODUCT_IN_PACK_FIXTURE = 5;

    /**
     * Object from test database
     *
     * @var int
     */
    protected $pack;

    /**
     * Object from test database
     *
     * @var int
     */
    protected $productInPack;

    protected $oldConfig = [
        'PS_PACK_STOCK_TYPE' => null,
    ];

    /**
     * Populate pack and product in pack properties from the test database
     */
    public function setUp()
    {
        parent::setUp();
        // store previous config values
        foreach (array_keys($this->oldConfig) as $k) {
            $this->oldConfig[$k] = Configuration::get($k);
        }
        $this->pack          = $this->getProductFromFixtureId(self::ID_PACK_FIXTURE);
        $this->productInPack = $this->getProductFromFixtureId(self::ID_PRODUCT_IN_PACK_FIXTURE);
    }

    public function tearDown()
    {
        // restore previous config values
        foreach (array_keys($this->oldConfig) as $k) {
            Configuration::set($k, $this->oldConfig[$k]);
        }

        parent::tearDown();
    }

    public function testProductStockNumberMatch()
    {
        // Pack type decrement pack only
        Configuration::set('PS_PACK_STOCK_TYPE', Pack::STOCK_TYPE_PACK_ONLY);
        $nbPack    = Product::getQuantity($this->pack->id);
        $nbProduct = Product::getQuantity($this->productInPack->id);
        $this->assertEquals(10, $nbPack);
        $this->assertEquals(50, $nbProduct);

        // Pack type decrement products only
        Configuration::set('PS_PACK_STOCK_TYPE', Pack::STOCK_TYPE_PRODUCTS_ONLY);
        $nbPack    = Product::getQuantity($this->pack->id);
        $nbProduct = Product::getQuantity($this->productInPack->id);
        $this->assertEquals(5, $nbPack);
        $this->assertEquals(50, $nbProduct);

        // Pack type decrement pack and products
        Configuration::set('PS_PACK_STOCK_TYPE', Pack::STOCK_TYPE_PACK_BOTH);
        $nbPack    = Product::getQuantity($this->pack->id);
        $nbProduct = Product::getQuantity($this->productInPack->id);
        $this->assertEquals(5, $nbPack);
        $this->assertEquals(50, $nbProduct);
    }

    public function testPackIsInStock()
    {
        // Pack type decrement pack only
        Configuration::set('PS_PACK_STOCK_TYPE', Pack::STOCK_TYPE_PACK_ONLY);
        $this->assertTrue(Pack::isInStock($this->pack->id, 10));
        $this->assertFalse(Pack::isInStock($this->pack->id, 11));

        // Pack type decrement products only
        Configuration::set('PS_PACK_STOCK_TYPE', Pack::STOCK_TYPE_PRODUCTS_ONLY);
        $this->assertTrue(Pack::isInStock($this->pack->id, 5));
        $this->assertFalse(Pack::isInStock($this->pack->id, 6));

        // Pack type decrement pack and products
        Configuration::set('PS_PACK_STOCK_TYPE', Pack::STOCK_TYPE_PACK_BOTH);
        $this->assertTrue(Pack::isInStock($this->pack->id, 5));
        $this->assertFalse(Pack::isInStock($this->pack->id, 6));
    }

    public function testPackIsPack()
    {
        $this->assertTrue(Pack::isPack($this->pack->id));
    }

    public function testProductsQuantitiesInCart()
    {
        // Pack type decrement pack only
        $this->pack->pack_stock_type = Pack::STOCK_TYPE_PACK_ONLY;
        $this->pack->update();
        $this->calculProductsQuantitiesinCart(2, 2, 30, 30, 8, 20);

        // Pack type decrement product only
        $this->pack->pack_stock_type = Pack::STOCK_TYPE_PRODUCTS_ONLY;
        $this->pack->update();
        $this->calculProductsQuantitiesinCart(2, 2, 30, 50, 0, 0);

        // Pack type decrement pack and product
        $this->pack->pack_stock_type = Pack::STOCK_TYPE_PACK_BOTH;
        $this->pack->update();
        $this->calculProductsQuantitiesinCart(2, 2, 30, 50, 0, 0);
    }

    /**
     * @param int $packQuantity
     * @param int $packDeepQuantity
     * @param int $productQuantity
     * @param int $productDeepQuantity
     * @param int $packLeftExpected
     * @param int $productLeftExpected
     *
     * @return $this
     */
    private function calculProductsQuantitiesIncart(
        $packQuantity,
        $packDeepQuantity,
        $productQuantity,
        $productDeepQuantity,
        $packLeftExpected,
        $productLeftExpected
    ) {
        $this->assertTrue($this->cart->updateQty(2, $this->pack->id));
        $this->assertTrue($this->cart->updateQty(30, $this->productInPack->id));

        $nbPackInCart    = $this->cart->getProductQuantity($this->pack->id);
        $nbProductInCart = $this->cart->getProductQuantity($this->productInPack->id);

        $this->assertEquals($packQuantity, $nbPackInCart['quantity']);
        $this->assertEquals($packDeepQuantity, $nbPackInCart['deep_quantity']);
        $this->assertEquals($productQuantity, $nbProductInCart['quantity']);
        $this->assertEquals($productDeepQuantity, $nbProductInCart['deep_quantity']);

        $cartProducts = $this->cart->getProducts(true);
        $this->assertCount(2, $cartProducts);

        foreach ($cartProducts as $cartProduct) {
            $this->assertContains($cartProduct['id_product'], [$this->pack->id, $this->productInPack->id]);

            if ($cartProduct['id_product'] == $this->pack->id) {
                $this->assertEquals(
                    $packLeftExpected,
                    Product::getQuantity($cartProduct['id_product'], null, null, $this->cart)
                );
            } else {
                $this->assertEquals(
                    $productLeftExpected,
                    Product::getQuantity($cartProduct['id_product'], null, null, $this->cart)
                );
            }
        }
        $this->resetCart();

        return $this;
    }

    public function testAddProductsOutOfStockInCart()
    {
        // Test pack out of stock disabled
        StockAvailable::setProductOutOfStock($this->pack->id, false);
        $this->assertFalse($this->cart->updateQty(11, $this->pack->id));
        $outOfStock = StockAvailable::outOfStock($this->pack->id);
        $this->assertEquals(0, $this->pack->isAvailableWhenOutOfStock($outOfStock));

        // Test pack out of stock enabled
        StockAvailable::setProductOutOfStock($this->pack->id, true);
        $this->assertTrue($this->cart->updateQty(11, $this->pack->id));
        $outOfStock = StockAvailable::outOfStock($this->pack->id);
        $this->assertEquals(1, $this->pack->isAvailableWhenOutOfStock($outOfStock));

        // Test pack out of stock disabled
        StockAvailable::setProductOutOfStock($this->productInPack->id, false);
        $this->assertFalse($this->cart->updateQty(51, $this->productInPack->id));
        $outOfStock = StockAvailable::outOfStock($this->productInPack->id);
        $this->assertEquals(0, $this->pack->isAvailableWhenOutOfStock($outOfStock));

        // Test pack out of stock enabled
        StockAvailable::setProductOutOfStock($this->productInPack->id, true);
        $this->assertTrue($this->cart->updateQty(51, $this->productInPack->id));
        $outOfStock = StockAvailable::outOfStock($this->productInPack->id);
        $this->assertEquals(1, $this->pack->isAvailableWhenOutOfStock($outOfStock));
    }

    public function testUnableToAddPackOutOfStock()
    {
        // Pack type decrement pack only
        $this->pack->pack_stock_type = Pack::STOCK_TYPE_PACK_ONLY;
        $this->pack->update();
        StockAvailable::setProductOutOfStock($this->pack->id, false);
        $this->assertTrue($this->cart->updateQty(9, $this->pack->id));
        $this->assertFalse(Pack::isInStock($this->pack->id, 2, $this->cart));
        $this->assertTrue(Pack::isInStock($this->pack->id, 1, $this->cart));
        $this->resetCart();

        // Pack type decrement product only
        $this->pack->pack_stock_type = Pack::STOCK_TYPE_PRODUCTS_ONLY;
        $this->pack->update();
        StockAvailable::setProductOutOfStock($this->pack->id, false);
        $this->assertTrue($this->cart->updateQty(40, $this->productInPack->id));
        $this->assertFalse(Pack::isInStock($this->pack->id, 2, $this->cart));
        $this->assertTrue(Pack::isInStock($this->pack->id, 1, $this->cart));
        $this->resetCart();

        // Pack type decrement pack and product
        $this->pack->pack_stock_type = Pack::STOCK_TYPE_PACK_BOTH;
        $this->pack->update();
        StockAvailable::setProductOutOfStock($this->pack->id, false);
        $this->assertTrue($this->cart->updateQty(40, $this->productInPack->id));
        $this->assertFalse(Pack::isInStock($this->pack->id, 2, $this->cart));
        $this->assertTrue(Pack::isInStock($this->pack->id, 1, $this->cart));
    }
}
