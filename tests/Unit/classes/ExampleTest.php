<?php

use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;

/**
 * Class ExampleTest
 *
 * This is a dummy test as it does not actually check anything in getOrderTotal.
 * It is here to give an example of unit testing setup using mocks.
 *
 */
class ExampleTest extends UnitTestCase
{
    /**
     * @var Cart
     */
    private $cart;

    public function setUp()
    {
        $this->setUpCommonStaticMocks();

        $this->cart = new Cart(null, null, $this->context);
    }

    public function tearDown()
    {
        $this->tearDownCommonStaticMocks();
    }

    public function test_getOrderTotal_ShouldReturnZero_WhenIdIsNotDefined()
    {
        //When
        $result = $this->cart->getOrderTotal();
        //Then
        $this->assertEquals(0, $result);
    }
}
