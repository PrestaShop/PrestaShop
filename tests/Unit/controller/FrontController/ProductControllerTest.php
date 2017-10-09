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

namespace Tests\Unit\Controller\FrontController;

use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use Tests\TestCase\IntegrationTestCase;
use Tests\Unit\ContextMocker;

class ProductControllerTest extends IntegrationTestCase
{

    /**
     * @var ContextMocker
     */
    protected $contextMocker;

    private $controller;

    public function setUp()
    {
        parent::setUp();
        $this->contextMocker = new ContextMocker();
        $this->contextMocker->mockContext();
        $this->controller = new \ProductControllerCore();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->contextMocker->resetContext();
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object $object     Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod($object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method     = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * here we test that for a given dataset of specific prices, currency, ecotax... we get the correct discount result
     *
     * @dataProvider specificPricesProvider
     *
     * @param $price
     * @param $taxRate
     * @param $ecotaxAmount
     * @param $currencyData
     * @param $specificPrices
     * @param $expected
     */
    public function testFormatQuantityDiscounts(
        $price,
        $taxRate,
        $ecotaxAmount,
        $currencyData,
        $specificPrices,
        $expected
    ) {
        $class    = new \ReflectionClass(get_class($this->controller));
        $property = $class->getProperty("context");
        $property->setAccessible(true);

        $currency                  = new \Currency;
        $currency->active          = true;
        $currency->conversion_rate = $currencyData['conversion_rate'];
        $currency->sign            = $currencyData['sign'];
        $currency->iso_code        = $currencyData['code'];

        /** @var \Context $context */
        $context            = $property->getValue($this->controller);
        $context->currency  = $currency;
        $language           = new \Language();
        $language->iso_code = 'EN';
        $context->language  = $language;
        $result             = $this->invokeMethod(
            $this->controller,
            'formatQuantityDiscounts',
            array(
                $specificPrices,
                $price,
                $taxRate,
                $ecotaxAmount,
            )
        );

        $priceFormatter = new PriceFormatter();

        foreach ($expected as $expectedLevel => $expectedValues) {
            $this->assertArrayHasKey($expectedLevel, $result);
            foreach ($expectedValues as $expectedKey => $expectedValue) {
                $this->assertArrayHasKey($expectedKey, $result[$expectedLevel]);
                $this->assertEquals($priceFormatter->format($expectedValue), $result[$expectedLevel][$expectedKey]);
            }
        }
    }

    public function specificPricesProvider()
    {

        $specificPrices = array(
            0 => array(
                'id_specific_price'      => '9',
                'id_specific_price_rule' => '0',
                'id_cart'                => '0',
                'id_product'             => '10',
                'id_shop'                => '1',
                'id_shop_group'          => '0',
                'id_currency'            => '0',
                'id_country'             => '0',
                'id_group'               => '0',
                'id_customer'            => '0',
                'id_product_attribute'   => '0',
                'price'                  => '15.000000',
                'from_quantity'          => '15',
                'reduction'              => 0,
                'reduction_tax'          => '1',
                'reduction_type'         => 'amount',
                'from'                   => '0000-00-00 00:00:00',
                'to'                     => '0000-00-00 00:00:00',
                'score'                  => '48',
                'quantity'               => '15',
                'reduction_with_tax'     => 0,
                'nextQuantity'           => -1,
            ),
        );
        $currencyEur    = array(
            'conversion_rate' => 1.0,
            'sign'            => '€',
            'code'            => 'EUR',
        );
        $currencyDol    = array(
            'conversion_rate' => 1.3,
            'sign'            => '$',
            'code'            => 'USD',
        );

        return array(
            'EUR to USD, without ecotax' => array(
                'price'           => 31.2,
                'tax_rate'        => 20,
                'ecotax_amount'   => 0,
                'currency'        => $currencyDol,
                'specific_prices' => $specificPrices,
                'expected'        => array(
                    array(
                        'discount' => 7.80,
                        'save'     => 117.00,
                    ),
                ),
            ),
            'EUR to EUR, without ecotax' => array(
                'price'           => 24,
                'tax_rate'        => 20,
                'ecotax_amount'   => 0,
                'currency'        => $currencyEur,
                'specific_prices' => $specificPrices,
                'expected'        => array(
                    array(
                        'discount' => 6.00,
                        'save'     => 90.00,
                    ),
                ),
            ),
            'EUR to USD, with ecotax'    => array(
                'price'           => 31.2,
                'tax_rate'        => 20,
                'ecotax_amount'   => 0.9,
                'currency'        => $currencyDol,
                'specific_prices' => $specificPrices,
                'expected'        => array(
                    array(
                        'discount' => 6.63,
                        'save'     => 99.45,
                    ),
                ),
            ),
            'EUR to EUR, with ecotax'    => array(
                'price'           => 24,
                'tax_rate'        => 20,
                'ecotax_amount'   => 0.9,
                'currency'        => $currencyEur,
                'specific_prices' => $specificPrices,
                'expected'        => array(
                    array(
                        'discount' => 5.10,
                        'save'     => 76.50,
                    ),
                ),
            ),
        );
    }
}
