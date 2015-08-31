<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Tests\Unit\Classes;

use Exception;
use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use Adapter_ProductPriceCalculator;
use Adapter_AddressFactory;
use Core_Business_ConfigurationInterface;
use Address;
use Cart;
use Order;
use Tools;
use Phake;

class FakeProduct
{
    public function __construct($quantity, $price)
    {
        $this->quantity = $quantity;
        $this->price = $price;
    }

    public $price;
    public $quantity;
}

class FakeProductPriceCalculator
{
    private $products = array();

    public function addFakeProduct(FakeProduct $product)
    {
        $id = count($this->products) + 1;

        $this->products[$id] = array(
            'id_shop' => null,
            'id_product' => $id,
            'id_product_attribute' => $id,
            'cart_quantity' => $product->quantity,
            'price' => $product->price
        );
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function getProductPrice($id)
    {
        return $this->products[$id]['price'];
    }
}

class CartTest extends UnitTestCase
{
    public function setup()
    {
        parent::setup();

        $this->productPriceCalculator = new FakeProductPriceCalculator;
        $this->container->bind('Adapter_ProductPriceCalculator', $this->productPriceCalculator);

        $addressFactory = Phake::mock('Adapter_AddressFactory');
        $address = new Address;
        $address->id = 1;

        Phake::when($addressFactory)->findOrCreate()->thenReturn($address);
        $this->container->bind('Adapter_AddressFactory', $addressFactory);

        $this->cart = new Cart;
        $this->cart->id = 1;
    }

    public function teardown()
    {
        parent::teardown();
        Tools::$round_mode = null;
    }

    private function setRoundType($type)
    {
        $this->setConfiguration(array(
            '_PS_PRICE_COMPUTE_PRECISION_' => 2,
            'PS_TAX_ADDRESS_TYPE' => 0,
            'PS_USE_ECOTAX' => 0,
            'PS_ROUND_TYPE' => $type,
            'PS_ECOTAX_TAX_RULES_GROUP_ID' => 0,
            'PS_ATCP_SHIPWRAP' => false
        ));
    }

    public function test_getOrderTotal_Round_Line_When_No_Tax()
    {
        $this->setRoundType(Order::ROUND_LINE);

        $this->productPriceCalculator->addFakeProduct(new FakeProduct(3, 10.125));
        $this->productPriceCalculator->addFakeProduct(new FakeProduct(1, 10.125));

        $orderTotal = $this->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS, $this->productPriceCalculator->getProducts());
        $this->assertEquals(40.51, $orderTotal);
    }

    public function test_getOrderTotal_Round_Total_When_No_Tax()
    {
        $this->setRoundType(Order::ROUND_TOTAL);

        $this->productPriceCalculator->addFakeProduct(new FakeProduct(3, 10.125));
        $this->productPriceCalculator->addFakeProduct(new FakeProduct(1, 10.125));

        $orderTotal = $this->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS, $this->productPriceCalculator->getProducts());
        $this->assertEquals(40.5, $orderTotal);
    }

    public function test_getOrderTotal_Round_Item_When_No_Tax()
    {
        $this->setRoundType(Order::ROUND_ITEM);

        $this->productPriceCalculator->addFakeProduct(new FakeProduct(3, 10.125));
        $this->productPriceCalculator->addFakeProduct(new FakeProduct(1, 10.125));

        $orderTotal = $this->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS, $this->productPriceCalculator->getProducts());
        $this->assertEquals(40.52, $orderTotal);
    }
}
