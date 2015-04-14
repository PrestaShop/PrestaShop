<?php

namespace PrestaShop\PrestaShop\Tests\Unit\Classes;

use Exception;

use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;

use Core_Foundation_IoC_Container;
use Adapter_ProductPriceCalculator;
use Adapter_ServiceLocator;
use Adapter_AddressFactory;
use Core_Business_Configuration;
use Core_Foundation_IoC_ContainerBuilder;

use Address;
use Cart;
use Order;
use Tools;

class FakeConfiguration implements Core_Business_Configuration
{
    private $keys;

    public function __construct(array $keys)
    {
        $this->keys = $keys;
    }

    public function get($key)
    {
        if (!array_key_exists($key, $this->keys)) {
            throw new Exception("Key $key does not exist in the fake configuration.");
        }
        return $this->keys[$key];
    }
}

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
    private $container;

    public function setup()
    {
        $this->setUpCommonStaticMocks();
        $this->container = new Core_Foundation_IoC_Container;
        Adapter_ServiceLocator::setServiceContainerInstance($this->container);

        $this->productPriceCalculator = new FakeProductPriceCalculator;
        $this->container->bind('Adapter_ProductPriceCalculator', function () {
            return $this->productPriceCalculator;
        });

        $addressFactory = $this->getMockBuilder('Adapter_AddressFactory')->getMock();
        $address = new Address;
        $address->id = 1;
        $addressFactory->method('findOrCreate')->willReturn($address);

        $this->container->bind('Adapter_AddressFactory', function () use ($addressFactory) {
            return $addressFactory;
        });

        $this->cart = new Cart;
        $this->cart->id = 1;
    }

    public function teardown()
    {
        $this->tearDownCommonStaticMocks();
        $container_builder = new Core_Foundation_IoC_ContainerBuilder;
        $container = $container_builder->build();
        Adapter_ServiceLocator::setServiceContainerInstance($container);
        Tools::$round_mode = null;
    }

    public function setConfiguration(array $keys)
    {
        $mockConfiguration = new FakeConfiguration($keys);

        $this->container->bind('Core_Business_Configuration', function () use ($mockConfiguration) {
            return $mockConfiguration;
        });
    }

    public function test_price_calculator_adapter_is_loaded()
    {
        new Adapter_ProductPriceCalculator;
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
