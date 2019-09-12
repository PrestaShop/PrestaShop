<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Unit\Core\Cart\Adding\CartRule;

use Configuration;
use LegacyTests\Unit\Core\Cart\AbstractCartTest;
use Product;

/**
 * behat equivalent : Scenarii/Cart/Adding/Product/add_combination.feature
 */
class AddCombinationTest extends AbstractCartTest
{
    public function testCombinationCanBeAddedInCartIfAvailable()
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

    public function testCombinationCannotBeAddedInCartIfMoreThanStock()
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

    public function testCombinationCanBeAddedInCartIfMoreThanStockButAvailableWhenOutOfStock()
    {
        $combination = $this->getCombinationFromFixtureName('a');
        $product     = new Product($combination->id_product);

        $oldOrderOutOfStock = Configuration::get('PS_ORDER_OUT_OF_STOCK');
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
