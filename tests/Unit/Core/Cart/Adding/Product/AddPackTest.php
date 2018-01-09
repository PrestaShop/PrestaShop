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

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Cart\Adding\CartRule;

use PrestaShop\PrestaShop\Tests\Unit\Core\Cart\AbstractCartTest;

class AddPackTest extends AbstractCartTest
{
    public function testProductQuantity()
    {
        $idProductInPackFixture = 5;
        $idPackFixture = 6;
        $pack = $this->getProductFromFixtureId($idPackFixture);
        $productPack = $this->getProductFromFixtureId($idProductInPackFixture);
        $idPack = $pack->id;
        $idProductInPack = $productPack->id;
        $nbPack = \Product::getQuantity($idPack);
        $nbProduct = \Product::getQuantity($idProductInPack);
        $this->assertEquals(10, $nbPack);
        $this->assertEquals(50, $nbProduct);
        $this->assertTrue(\Pack::isInStock($idPack));
        $this->assertTrue(\Pack::isInStock($idProductInPack));
    }

    public function testPackInCart()
    {
        $idProductInPackFixture = 5;
        $idPackFixture = 6;
        $pack = $this->getProductFromFixtureId($idPackFixture);
        $productPack = $this->getProductFromFixtureId($idProductInPackFixture);
        $idPack = $pack->id;
        $idProductInPack = $productPack->id;

        // Simple tests
        $this->assertTrue(\Pack::isPack($idPack));
        $this->assertTrue($this->cart->updateQty(3, $idPack));
        $this->assertTrue($this->cart->updateQty(30, $idProductInPack));

        $nbPackInCart = $this->cart->getProductQuantity($idPack);
        $nbProductInCart = $this->cart->getProductQuantity($idProductInPack);
        $cartProducts = $this->cart->getProducts(true);

        $this->assertCount(2, $cartProducts);
        $this->assertEquals(3, $nbPackInCart['quantity']);
        $this->assertEquals(30, $nbProductInCart['quantity']);

        foreach ($cartProducts as $cartProduct) {
            $this->assertContains($cartProduct['id_product'], array($idPack, $idProductInPack));
        }

        // Unable to add more than in stock
        $this->resetCart();

        if (!$pack->isAvailableWhenOutOfStock((int) $pack->out_of_stock)) {
            $this->assertFalse($this->cart->updateQty(11, $idPack));
        } else {
            $this->assertTrue($this->cart->updateQty(11, $idPack));
        }

        if (!$productPack->isAvailableWhenOutOfStock((int) $productPack->out_of_stock)) {
            $this->assertFalse($this->cart->updateQty(60, $idProductInPack));
        } else {
            $this->assertTrue($this->cart->updateQty(60, $idProductInPack));
        }

        // Unable to add pack with product out of stock
        $this->resetCart();
        $this->assertTrue($this->cart->updateQty(40, $idProductInPack));
        $this->assertFalse(\Pack::isInStock($idPack, 2));
        $this->assertTrue(\Pack::isInStock($idPack, 1));
    }
}
