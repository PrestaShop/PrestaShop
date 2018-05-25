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

class AddCombinationTest extends AbstractCartTest
{
    public function testProductCanBeAddedInCartIfAvailable()
    {
        $combination = $this->getCombinationFromFixtureName('a');
        $product     = new Product($combination->id_product);

        $nbProduct = Product::getQuantity($product->id, $combination->id, null, $this->cart, null);
        $this->assertEquals(500, $nbProduct);

        $result = $this->cart->updateQty(11, $product->id, $combination->id, null);
        $this->assertTrue($result);
        $qty = $this->cart->getProductQuantity($product->id, $combination->id, null);
        $this->assertEquals(11, $qty['quantity']);
        $nbProduct = Product::getQuantity($product->id, $combination->id, null, $this->cart, null);
        $this->assertEquals(489, $nbProduct);
    }

    public function testProductCannotBeAddedInCartIfMoreThanStock()
    {
        $combination = $this->getCombinationFromFixtureName('a');
        $product     = new Product($combination->id_product);

        $result = $this->cart->updateQty(600, $product->id, $combination->id);
        $this->assertFalse($result);
        $qty = $this->cart->getProductQuantity($product->id, $combination->id, null);
        $this->assertEquals(0, $qty['quantity']);
        $nbProduct = Product::getQuantity($product->id, $combination->id, null, $this->cart, null);
        $this->assertEquals(500, $nbProduct);
    }

    public function testProductCanBeAddedInCartIfMoreThanStockButAvailableWhenOutOfStock()
    {
        $combination = $this->getCombinationFromFixtureName('a');
        $product     = new Product($combination->id_product);

        $oldOrderOutOfStock = Configuration::get('PS_PACK_STOCK_TYPE');
        Configuration::set('PS_ORDER_OUT_OF_STOCK', 1);
        $product->out_of_stock = 1;
        $product->save();

        $result = $this->cart->updateQty(600, $product->id, $combination->id);
        $this->assertTrue($result);
        $qty = $this->cart->getProductQuantity($product->id, $combination->id, null);
        $this->assertEquals(600, $qty['quantity']);
        $nbProduct = Product::getQuantity($product->id, $combination->id, null, $this->cart, null);
        $this->assertEquals(-100, $nbProduct);

        Configuration::set('PS_ORDER_OUT_OF_STOCK', $oldOrderOutOfStock);
    }

}
