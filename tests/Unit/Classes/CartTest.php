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

namespace Tests\Unit\Classes;

use Tests\TestCase\UnitTestCase;
use Address;
use Cart;
use Order;
use Tests\Unit\ContextMocker;
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
            'id_customization' => 0,
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
    /**
     * @var FakeProductPriceCalculator
     */
    private $productPriceCalculator;

    /**
     * @var ContextMocker
     */
    protected $contextMocker;

    public function setUp()
    {
        parent::setUp();

        $this->initLanguage();

        $this->contextMocker = new ContextMocker();
        $this->contextMocker->mockContext();

        $this->productPriceCalculator = new FakeProductPriceCalculator();
        $this->container->bind('\\PrestaShop\\PrestaShop\\Adapter\\Product\\PriceCalculator', $this->productPriceCalculator);

        $addressFactory = Phake::mock('\\PrestaShop\\PrestaShop\\Adapter\\AddressFactory');
        $address = new Address;
        $address->id = 1;

        Phake::when($addressFactory)->findOrCreate()->thenReturn($address);
        $this->container->bind('\\PrestaShop\\PrestaShop\\Adapter\\AddressFactory', $addressFactory);
    }

    public function teardown()
    {
        parent::teardown();
        Tools::$round_mode = null;
    }

    /**
     * @throws \ReflectionException
     */
    private function initLanguage()
    {
        // We need to mock loaded languages because ContextMocker instantiates a Currency with language id = 1,
        // and Currency needs to have that language loaded, or else it will fail

        $reflectedLanguage = new \ReflectionClass(\Language::class);
        $languageList = $reflectedLanguage->getProperty('_LANGUAGES');
        $languageList->setAccessible(true);
        if (empty($languageList->getValue())) {
            $languageList->setValue(array(
                1 => array(
                    'id_lang'          => 1,
                    'name'             => 'English (English)',
                    'active'           => 1,
                    'language_code'    => 'en-us',
                    'locale'           => 'en-US',
                    'date_format_lite' => 'm/d/Y',
                    'date_format_full' => 'm/d/Y H:i:s',
                    'is_rtl'           => 0,
                    'id_shop'          => 1,
                    'shops'            => array(1 => true),
                )
            ));
        }
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

        $this->setUpCart();

        $this->productPriceCalculator->addFakeProduct(new FakeProduct(3, 10.125));
        $this->productPriceCalculator->addFakeProduct(new FakeProduct(1, 10.125));

        $orderTotal = $this->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS,
            $this->productPriceCalculator->getProducts());

        $this->assertEquals(40.51, $orderTotal);
    }

    public function test_getOrderTotal_Round_Total_When_No_Tax()
    {
        $this->setRoundType(Order::ROUND_TOTAL);

        $this->setUpCart();

        $this->productPriceCalculator->addFakeProduct(new FakeProduct(3, 10.125));
        $this->productPriceCalculator->addFakeProduct(new FakeProduct(1, 10.125));

        $orderTotal = $this->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS, $this->productPriceCalculator->getProducts());
        $this->assertEquals(40.5, $orderTotal);
    }

    public function test_getOrderTotal_Round_Item_When_No_Tax()
    {
        $this->setRoundType(Order::ROUND_ITEM);

        $this->setUpCart();

        $this->productPriceCalculator->addFakeProduct(new FakeProduct(3, 10.125));
        $this->productPriceCalculator->addFakeProduct(new FakeProduct(1, 10.125));

        $orderTotal = $this->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS, $this->productPriceCalculator->getProducts());
        $this->assertEquals(40.52, $orderTotal);
    }

    protected function setUpCart()
    {
        $this->cart = new Cart;
        $this->cart->id = 1;
    }
}
