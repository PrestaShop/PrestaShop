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
use Customization;
use Product;
use Pack;
use StockAvailable;
use Tests\Unit\Core\Cart\AbstractCartTest;

class AddCustomizationTest extends AbstractCartTest
{

    protected $customizations = [];

    public function tearDown()
    {
        foreach ($this->customizations as $customization) {
            $customization->delete();
        }
        parent::tearDown();
    }

    protected function addCustomization(Product $product)
    {

        $customization                       = new Customization;
        $customization->id_product           = $product->id;
        $customization->id_product_attribute = 0;
        $customization->id_address_delivery  = 0;
        $customization->quantity             = 0;
        $customization->quantity_refunded    = 0;
        $customization->quantity_returned    = 0;
        $customization->in_cart              = 0;
        $customization->id_cart              = $this->cart->id;
        $customization->add();

        $this->customizations[] = $customization;

        return $customization;
    }

    public function testCustomizationCanBeAddedInCartIfAvailable()
    {
        $customizationField = $this->getCustomizationFieldFromFixtureName('a');
        $product            = new Product($customizationField->id_product);
        $customization      = $this->addCustomization($product);

        $nbProduct = Product::getQuantity($product->id, null, null, $this->cart, null);
        $this->assertEquals(30, $nbProduct);

        $result = $this->cart->updateQty(11, $product->id, null, $customization->id);
        $this->assertTrue($result);
        $qty = $this->cart->getProductQuantity($product->id, null, $customization->id);
        $this->assertEquals(11, $qty['quantity']);
        $qty = $this->cart->getProductQuantity($product->id, null, null);
        $this->assertEquals(0, $qty['quantity']);
        $nbProduct = Product::getQuantity($product->id, null, null, $this->cart, $customization->id);
        $this->assertEquals(19, $nbProduct);
    }

    public function testCustomizationCannotBeAddedInCartIfMoreThanStock()
    {
        $customizationField = $this->getCustomizationFieldFromFixtureName('a');
        $product            = new Product($customizationField->id_product);
        $customization      = $this->addCustomization($product);

        $result = $this->cart->updateQty(41, $product->id, null, $customization->id);
        $this->assertFalse($result);
        $qty = $this->cart->getProductQuantity($product->id, null, $customization->id);
        $this->assertEquals(0, $qty['quantity']);
        $nbProduct = Product::getQuantity($product->id, null, null, $this->cart, $customization->id);
        $this->assertEquals(30, $nbProduct);
    }

    public function testCustomizationCanBeAddedInCartIfMoreThanStockButAvailableWhenOutOfStock()
    {
        $customizationField = $this->getCustomizationFieldFromFixtureName('a');
        $product            = new Product($customizationField->id_product);
        $customization      = $this->addCustomization($product);

        $oldOrderOutOfStock = Configuration::get('PS_ORDER_OUT_OF_STOCK');
        Configuration::set('PS_ORDER_OUT_OF_STOCK', 1);
        $product->out_of_stock = 1;
        $product->save();

        $result = $this->cart->updateQty(41, $product->id, null, $customization->id);
        $this->assertTrue($result);
        $qty = $this->cart->getProductQuantity($product->id, null, $customization->id);
        $this->assertEquals(41, $qty['quantity']);
        $nbProduct = Product::getQuantity($product->id, null, null, $this->cart, $customization->id);
        $this->assertEquals(-11, $nbProduct);

        Configuration::set('PS_ORDER_OUT_OF_STOCK', $oldOrderOutOfStock);
    }

}
