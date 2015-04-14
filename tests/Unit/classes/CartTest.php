<?php

namespace PrestaShop\PrestaShop\Tests\Unit\Classes;

use Exception;

use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;

use Adapter_ProductPriceCalculator;
use Adapter_AddressFactory;
use Core_Business_Configuration;

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

    public function test_getOrderTotal_Round_Line()
    {
        $this->setConfiguration(array(
            '_PS_PRICE_COMPUTE_PRECISION_' => 2,
            'PS_TAX_ADDRESS_TYPE' => 0,
            'PS_USE_ECOTAX' => 0,
            'PS_ROUND_TYPE' => Order::ROUND_LINE,
            'PS_ECOTAX_TAX_RULES_GROUP_ID' => 0
        ));

        $this->productPriceCalculator->addFakeProduct(new FakeProduct(1, 10.125));
        $this->productPriceCalculator->addFakeProduct(new FakeProduct(1, 10.125));

        $orderTotal = $this->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS, $this->productPriceCalculator->getProducts());
        $this->assertEquals(20.26, $orderTotal);
    }
}
