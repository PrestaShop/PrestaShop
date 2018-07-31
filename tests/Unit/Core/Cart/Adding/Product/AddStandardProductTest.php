<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Cart\Adding\CartRule;

use Configuration;
use Product;
use Pack;
use StockAvailable;
use Tests\Unit\Core\Cart\AbstractCartTest;

class AddStandardProductTest extends AbstractCartTest
{
    public function testProductCanBeAddedInCartIfAvailable()
    {
        $product = $this->getProductFromFixtureId(1);

        $nbProduct = Product::getQuantity($product->id, null, null, $this->cart, null);
        $this->assertEquals(1000, $nbProduct);

        $result = $this->cart->updateQty(11, $product->id);
        $this->assertTrue($result);
        $qty = $this->cart->getProductQuantity($product->id, null, null);
        $this->assertEquals(11, $qty['quantity']);
        $nbProduct = Product::getQuantity($product->id, null, null, $this->cart, null);
        $this->assertEquals(989, $nbProduct);
    }

    public function testProductCannotBeAddedInCartIfMoreThanStock()
    {
        $product = $this->getProductFromFixtureId(1);

        $result = $this->cart->updateQty(1100, $product->id);
        $this->assertFalse($result);
        $qty = $this->cart->getProductQuantity($product->id, null, null);
        $this->assertEquals(0, $qty['quantity']);
        $nbProduct = Product::getQuantity($product->id, null, null, $this->cart, null);
        $this->assertEquals(1000, $nbProduct);
    }

    public function testProductCanBeAddedInCartIfMoreThanStockButAvailableWhenOutOfStock()
    {
        $product = $this->getProductFromFixtureId(1);

        $oldOrderOutOfStock = Configuration::get('PS_ORDER_OUT_OF_STOCK');
        Configuration::set('PS_ORDER_OUT_OF_STOCK', 1);
        $product->out_of_stock = 1;
        $product->save();

        $result = $this->cart->updateQty(1100, $product->id);
        $this->assertTrue($result);
        $qty = $this->cart->getProductQuantity($product->id, null, null);
        $this->assertEquals(1100, $qty['quantity']);
        $nbProduct = Product::getQuantity($product->id, null, null, $this->cart, null);
        $this->assertEquals(-100, $nbProduct);

        Configuration::set('PS_ORDER_OUT_OF_STOCK', $oldOrderOutOfStock);
    }

}
