<?php
/**
 * 2007-2017 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Tests\Integration\PrestaShopBundle\Controller\Api;

use Prophecy\Prophet;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Context;
use Shop;

/**
 * @group api
 */
class StockControllerTest extends WebTestCase
{
    /**
     * @var \Prophecy\Prophet
     */
    private $prophet;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \Symfony\Component\BrowserKit\Client
     */
    private $client;

    public function setUp()
    {
        parent::setUp();

        $this->prophet = new Prophet();
        $this->client = $this->createClient();

        $container = self::$kernel->getContainer();

        $legacyContextMock = $this->mockContextAdapter();
        $container->set('prestashop.adapter.legacy.context', $legacyContextMock->reveal());

        $this->router = $container->get('router');
    }

    public function tearDown()
    {
        $this->prophet->checkPredictions();

        parent::tearDown();
    }

    public function testListProductsAction()
    {
        $this->assertOkResponseOnListProducts('api_stock_list_products');
    }

    public function testListProductCombinationsAction()
    {
        $this->assertOkResponseOnListProducts('api_stock_list_product_combinations', array('productId' => 1));
    }

    /**
     * @param $route
     * @param array $parameters
     */
    private function assertOkResponseOnListProducts($route, $parameters = array())
    {
        $route = $this->router->generate($route, $parameters);

        $this->client->request('GET', $route);

        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), 'It should return a response with "OK" Status.');
    }

    public function testEditProductAction()
    {
        $this->assertNotFoundResponseOnEditProductStockQuantity();
    }

    public function testEditProductCombinationAction()
    {
        $this->assertNotFoundResponseOnEditProductCombinationStockQuantity();
        $this->assertOkResponseOnEditProductCombinationQuantity();
    }

    /**
     * @return array
     */
    private function assertNotFoundResponseOnEditProductStockQuantity()
    {
        $editProductStockRoute = $this->router->generate('api_stock_edit_product', array(
            'productId' => 9,
        ));

        $this->client->request('POST', $editProductStockRoute, array('quantity' => 1));

        $response = $this->client->getResponse();

        $this->assertEquals(404, $response->getStatusCode(), 'It should return a response with "Not Found" Status.');

        $this->assertResponseBodyValidJson($response);
    }

    /**
     * @return array
     */
    private function assertNotFoundResponseOnEditProductCombinationStockQuantity()
    {
        $editProductStockRoute = $this->router->generate('api_stock_edit_product_combination', array(
            'productId' => 8,
            'productAttributeId' => 1
        ));

        $this->client->request('POST', $editProductStockRoute, array('quantity' => 1));

        $response = $this->client->getResponse();

        $this->assertEquals(404, $response->getStatusCode(), 'It should return a response with "Not Found" Status.');

        $this->assertResponseBodyValidJson($response);
    }

    /**
     * @param $response
     * @return mixed
     */
    private function assertResponseBodyValidJson($response)
    {
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(JSON_ERROR_NONE, json_last_error(), 'The response body should be a valid json document');

        return $content;
    }

    private function assertOkResponseOnEditProductCombinationQuantity()
    {
        $editProductStockRoute = $this->router->generate('api_stock_edit_product_combination', array(
            'productId' => 1,
            'productAttributeId' => 1,
        ));

        $expectedAvailableQuantity = 10;
        $expectedPhysicalQuantity = 12;
        $expectedReservedQuantity = 2;

        $this->client->request('POST', $editProductStockRoute, array('quantity' => $expectedAvailableQuantity));

        /**
         * @var \Symfony\Component\HttpFoundation\JsonResponse $response
         */
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), 'It should return a response with "OK" Status.');

        $content = $this->assertResponseBodyValidJson($response);

        $this->assertArrayHasKey('product_available_quantity', $content,
            'The response body should contain a "product_available_quantity" property.'
        );
        $this->assertEquals($expectedAvailableQuantity, $content['product_available_quantity'],
            'The response body should contain the newly updated physical quantity.'
        );

        $this->assertArrayHasKey('product_physical_quantity', $content,
            'The response body should contain a "product_physical_quantity" property.'
        );
        $this->assertEquals($expectedPhysicalQuantity, $content['product_physical_quantity'],
            'The response body should contain the newly updated quantity.'
        );

        $this->assertArrayHasKey('product_reserved_quantity', $content,
            'The response body should contain a "product_reserved_quantity" property.'
        );
        $this->assertEquals($expectedReservedQuantity, $content['product_reserved_quantity'],
            'The response body should contain the newly updated physical quantity.'
        );
    }

    /**
     * @return \PrestaShop\PrestaShop\Adapter\LegacyContext
     */
    private function mockContextAdapter()
    {
        /** @var \PrestaShop\PrestaShop\Adapter\LegacyContext $legacyContextMock */
        $legacyContextMock = $this->prophet->prophesize('\PrestaShop\PrestaShop\Adapter\LegacyContext');

        $contextMock = $this->mockContext();
        $legacyContextMock->getContext()->willReturn($contextMock->reveal());

        $legacyContextMock->getEmployeeLanguageIso()->willReturn(null);

        return $legacyContextMock;
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    private function mockContext()
    {
        $contextMock = $this->prophet->prophesize('\Context');

        $employeeMock = $this->mockEmployee();
        $contextMock->employee = $employeeMock->reveal();

        $languageMock = $this->mockLanguage();
        $contextMock->language = $languageMock->reveal();

        $shopMock = $this->mockShop();
        $contextMock->shop = $shopMock->reveal();

        $controllerMock = $this->mockController();
        $contextMock->controller = $controllerMock->reveal();

        Context::setInstanceForTesting($contextMock->reveal());

        return $contextMock;
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    private function mockEmployee()
    {
        $employeeMock = $this->prophet->prophesize('\Employee');
        $employeeMock->id_lang = 1;

        return $employeeMock;
    }

    /**
     * @return object
     */
    private function mockLanguage()
    {
        $languageMock = $this->prophet->prophesize('\Language');
        $languageMock->iso_code = 'en-US';

        return $languageMock;
    }

    private function mockShop()
    {
        /** @var \Shop $shopMock */
        $shopMock = $this->prophet->prophesize('\Shop');
        $shopMock->getContextualShopId()->willReturn(1);

        $shopMock->getContextType()->willReturn(Shop::CONTEXT_SHOP);

        return $shopMock;
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    private function mockController()
    {
        $controller = $this->prophesize('\AdminController');
        $controller->controller_type = 'admin';

        return $controller;
    }
}
