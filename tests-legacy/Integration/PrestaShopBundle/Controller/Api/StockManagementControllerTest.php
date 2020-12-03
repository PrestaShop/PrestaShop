<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace LegacyTests\Integration\PrestaShopBundle\Controller\Api;

use PrestaShopBundle\Api\QueryStockParamsCollection;

/**
 * @group api
 * @group stockmanagement
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class StockManagementControllerTest extends ApiTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $stockMovementRepository = $this->getMockBuilder('PrestaShopBundle\Entity\Repository\StockMovementRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $stockMovementRepository->method('saveStockMvt')->willReturn(true);
        self::$container->set('prestashop.core.api.stock_movement.repository', $stockMovementRepository);

        $this->restoreQuantityEditionFixtures();
    }

    private function restoreMovements()
    {
        $deleteMovements = 'DELETE FROM ps_stock_mvt';
        $statement = self::$kernel->getContainer()->get('doctrine.dbal.default_connection')
            ->prepare($deleteMovements);
        $statement->execute();
    }

    private function restoreQuantityEditionFixtures()
    {
        $updateProductQuantity = '
            UPDATE ps_stock_available
            SET quantity = 8,
            physical_quantity = 10,
            reserved_quantity = 2
            WHERE id_product = 1 AND id_product_attribute = 1';
        $statement = self::$container->get('doctrine.dbal.default_connection')
            ->prepare($updateProductQuantity);
        $statement->execute();
    }

    /**
     * @test
     */
    public function itShouldReturnBadRequestResponseOnInvalidPaginationParams()
    {
        $routes = array(
            $this->router->generate('api_stock_list_products', array()),
            $this->router->generate('api_stock_list_movements', array()),
        );

        foreach ($routes as $route) {
            self::$client->request('GET', $route, array('page_index' => 0));
            $response = self::$client->getResponse();
            $this->assertSame(400, $response->getStatusCode(), 'It should return a response with "Bad Request" Status.');
        }
    }

    /**
     * @dataProvider getProductsStockParams
     * @test
     *
     * @param $params
     * @param $expectedTotalPages
     */
    public function itShouldReturnOkResponseWhenRequestingProductsStock($params, $expectedTotalPages)
    {
        $this->assertOkResponseOnList('api_stock_list_products', $params, $expectedTotalPages);
    }

    /**
     * @return array
     */
    public function getProductsStockParams()
    {
        return array(
            array(
                array(),
                $expectedTotalPages = 1,
            ),
            array(
                array('page_index' => 1, 'page_size' => 2),
                $expectedTotalPages = 24,
            ),
            array(
                array('supplier_id' => 1, 'page_index' => 2, 'page_size' => 2),
                $expectedTotalPages = 0,
            ),
            array(
                array('supplier_id' => array(1, 2), 'page_index' => 2, 'page_size' => 2),
                $expectedTotalPages = 0,
            ),
            array(
                array('category_id' => 5, 'page_index' => 1, 'page_size' => 1),
                $expectedTotalPages = 4,
            ),
            array(
                array('category_id' => array(4, 5), 'page_index' => 1, 'page_size' => 1),
                $expectedTotalPages = 12,
            ),
        );
    }

    /**
     * @dataProvider getProductsCombinationsParams
     * @test
     *
     * @param $params
     * @param $expectedTotalPages
     */
    public function itShouldReturnOkResponseWhenRequestingProductsCombinationsStock(
        $params,
        $expectedTotalPages
    )
    {
        $this->assertOkResponseOnList('api_stock_list_product_combinations', $params, $expectedTotalPages);
    }

    /**
     * @return array
     */
    public function getProductsCombinationsParams()
    {
        return array(
            array(
                array('productId' => 1),
                $expectedTotalPages = 1,
            ),
            array(
                array('productId' => 7, 'page_index' => 1, 'page_size' => 2),
                $expectedTotalPages = 1,
            ),
            array(
                array('productId' => 1, 'category_id' => array(4, 5), 'page_index' => 1, 'page_size' => 1),
                $expectedTotalPages = 8,
            ),
        );
    }

    /**
     * @param $routeName
     * @param array $parameters
     * @param $expectedTotalPages
     */
    private function assertOkResponseOnList(
        $routeName,
        $parameters = array(),
        $expectedTotalPages = null
    ) {
        $route = $this->router->generate($routeName, $parameters);
        self::$client->request('GET', $route);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = self::$client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'It should return a response with "OK" Status.');

        if ($expectedTotalPages) {
            $this->assertResponseHasTotalPages($parameters, $expectedTotalPages);
        }
    }

    /**
     * @param $parameters
     * @param $expectedTotalPages
     */
    private function assertResponseHasTotalPages($parameters, $expectedTotalPages)
    {
        if (null === $expectedTotalPages) {
            return;
        }

        $QueryStockParamsCollection = new QueryStockParamsCollection();
        $pageSize = $QueryStockParamsCollection->getDefaultPageSize();
        if (array_key_exists('page_size', $parameters)) {
            $pageSize = $parameters['page_size'];
        }

        $response = self::$client->getResponse();

        /** @var \Symfony\Component\HttpFoundation\ResponseHeaderBag $headers */
        $headers = $response->headers;
        $this->assertTrue($headers->has('Total-Pages'), 'The response headers should contain the total pages.');
        $this->assertEquals(
            $expectedTotalPages,
            $headers->get('Total-Pages'),
            sprintf(
                'There should be %d page(s) counting %d elements at most.',
                $expectedTotalPages,
                $pageSize
            )
        );
    }

    /**
     * @test
     */
    public function itShouldReturnErrorResponseWhenRequestingProductsStockEdition()
    {
        $this->assertErrorResponseOnEditProduct();
    }

    /**
     * @test
     */
    public function itShouldReturnValidResponseWhenRequestingProductsCombinationsStockEdition()
    {
        $this->assertNotFoundResponseOnEditProductCombination();
        $this->assertOkResponseOnEditProductCombination();
    }

    /**
     * @test
     */
    public function itShouldReturnValidResponseWhenRequestingBulkStockEdition()
    {
        $this->assertErrorResponseOnBulkEditProducts();
        $this->assertOkResponseOnBulkEditProducts();
    }

    /**
     * @return array
     */
    private function assertErrorResponseOnEditProduct()
    {
        $editProductStockRoute = $this->router->generate(
            'api_stock_edit_product',
            array('productId' => 9)
        );

        self::$client->request('POST', $editProductStockRoute);
        $this->assertResponseBodyValidJson(400);

        self::$client->request('POST', $editProductStockRoute, array(), array(), array(), '{}');
        $this->assertResponseBodyValidJson(400);

        self::$client->request('POST', $editProductStockRoute, array('delta' => 1));
        $this->assertResponseBodyValidJson(404);
    }

    /**
     * @return array
     */
    private function assertNotFoundResponseOnEditProductCombination()
    {
        $editProductStockRoute = $this->router->generate(
            'api_stock_edit_product_combination',
            array(
                'productId' => 8,
                'combinationId' => 1,
            )
        );

        self::$client->request('POST', $editProductStockRoute, array('delta' => 1));

        $this->assertResponseBodyValidJson(404);
    }

    private function assertOkResponseOnEditProductCombination()
    {
        $this->restoreMovements();

        $editProductStockRoute = $this->router->generate(
            'api_stock_edit_product_combination',
            array(
                'productId' => 1,
                'combinationId' => 1,
            )
        );

        self::$client->request('POST', $editProductStockRoute, array('delta' => 2));

        $content = $this->assertResponseBodyValidJson(200);

        $this->assertArrayHasKey(
            'product_available_quantity',
            $content,
            'The response body should contain a "product_available_quantity" property.'
        );
        $this->assertArrayHasKey(
            'product_physical_quantity',
            $content,
            'The response body should contain a "product_physical_quantity" property.'
        );
        $this->assertArrayHasKey(
            'product_reserved_quantity',
            $content,
            'The response body should contain a "product_reserved_quantity" property.'
        );
        $this->assertArrayHasKey(
            'product_thumbnail',
            $content,
            'The response body should contain an "image_thumbnail_path" property.'
        );
        $this->assertArrayHasKey(
            'combination_thumbnail',
            $content,
            'The response body should contain an "image_thumbnail_path" property.'
        );

        $this->assertProductQuantity(
            array(
                'available_quantity' => 10,
                'physical_quantity' => 10,
                'reserved_quantity' => 0,
            ),
            $content
        );

        self::$client->request('POST', $editProductStockRoute, array('delta' => -4));
        $content = $this->assertResponseBodyValidJson(200);

        $this->assertProductQuantity(
            array(
                'available_quantity' => 6,
                'physical_quantity' => 6,
                'reserved_quantity' => 0,
            ),
            $content
        );

        self::$client->request('POST', $editProductStockRoute, array(), array(), array(), '{"delta": 0}');
        $this->assertResponseBodyValidJson(200);
    }

    /**
     * @param $expectedQuantities
     * @param $content
     */
    private function assertProductQuantity($expectedQuantities, $content)
    {
        $this->assertSame(
            $expectedQuantities['available_quantity'],
            $content['product_available_quantity'],
            'The response body should contain the newly updated physical quantity.'
        );
        $this->assertSame(
            $expectedQuantities['physical_quantity'],
            $content['product_physical_quantity'],
            'The response body should contain the newly updated quantity.'
        );
        $this->assertSame(
            $expectedQuantities['reserved_quantity'],
            $content['product_reserved_quantity'],
            'The response body should contain the newly updated physical quantity.'
        );
    }

    private function assertErrorResponseOnBulkEditProducts()
    {
        $bulkEditProductsRoute = $this->router->generate('api_stock_bulk_edit_products');

        self::$client->request('POST', $bulkEditProductsRoute);
        $this->assertResponseBodyValidJson(400);

        self::$client->request('POST', $bulkEditProductsRoute, array(), array(), array(), '[{"combination_id": 0}]');
        $this->assertResponseBodyValidJson(400);

        self::$client->request('POST', $bulkEditProductsRoute, array(), array(), array(), '[{"product_id": 1}]');
        $this->assertResponseBodyValidJson(400);

        self::$client->request('POST', $bulkEditProductsRoute, array(), array(), array(), '[{"delta": 0}]');
        $this->assertResponseBodyValidJson(400);

        self::$client->request(
            'POST',
            $bulkEditProductsRoute,
            array(),
            array(),
            array(),
            '[{"product_id": 1, "delta": 0}]'
        );
        $this->assertResponseBodyValidJson(400);
    }

    private function assertOkResponseOnBulkEditProducts()
    {
        $bulkEditProductsRoute = $this->router->generate('api_stock_bulk_edit_products');

        self::$client->request(
            'POST',
            $bulkEditProductsRoute,
            array(),
            array(),
            array(),
            '[{"product_id": 1, "combination_id": 1, "delta": 3},' .
            '{"product_id": 1, "combination_id": 1, "delta": -1}]'
        );
        $content = $this->assertResponseBodyValidJson(200);

        $this->assertArrayHasKey(0, $content, 'The response content should have one item with key #0');

        $this->assertProductQuantity(
            array(
                'available_quantity' => 10,
                'physical_quantity' => 10,
                'reserved_quantity' => 0,
            ),
            $content[1]
        );

        self::$client->request(
            'POST',
            $bulkEditProductsRoute,
            array(),
            array(),
            array(),
            '[{"product_id": 1, "combination_id": 1, "delta": 3},' .
            '{"product_id": 1, "combination_id": 1, "delta": -3}]'
        );
        $content = $this->assertResponseBodyValidJson(200);

        $this->assertArrayHasKey(0, $content, 'The response content should have one item with key #0');

        $this->assertProductQuantity(
            array(
                'available_quantity' => 10,
                'physical_quantity' => 10,
                'reserved_quantity' => 0,
            ),
            $content[1]
        );
    }

    /**
     * @test
     */
    public function itShouldReturnValidResponseWhenRequestingStockSearchResults()
    {
        $listProductsRoute = $this->router->generate('api_stock_list_products');

        self::$client->request(
            'GET',
            $listProductsRoute,
            array('keywords' => array('Chiffon', 'demo_7', 'Size - S'))
        );

        $this->assertResponseBodyValidJson(200);
    }

    /**
     * @test
     */
    public function itShouldReturnValidResponseWhenRequestingStockWithAttributes()
    {
        $listProductsRoute = $this->router->generate('api_stock_list_products');

        self::$client->request(
            'GET',
            $listProductsRoute,
            array('attributes' => array('1:2', '3:14'))
        );

        $this->assertResponseBodyValidJson(200);
    }

    /**
     * @test
     */
    public function itShouldReturnValidResponseWhenRequestingStockWithFeatures()
    {
        $listProductsRoute = $this->router->generate('api_stock_list_products');

        self::$client->request(
            'GET',
            $listProductsRoute,
            array('features' => array('5:1', '6:11'))
        );

        $this->assertResponseBodyValidJson(200);
    }

    /**
     * @dataProvider getMovementsStockParams
     * @test
     *
     * @param $params
     * @param $expectedTotalPages
     */
    public function itShouldReturnOkResponseWhenRequestingMovementsStock($params, $expectedTotalPages)
    {
        $this->assertOkResponseOnList('api_stock_list_movements', $params, $expectedTotalPages);
    }

    /**
     * @return array
     */
    public function getMovementsStockParams()
    {
        return array(
            // @TODO when entity manager can save movements in db
            //            array(
            //                array(),
            //                $expectedTotalPages = 1
            //            ),
            //            array(
            //                array('page_index' => 1, 'page_size' => 5),
            //                $expectedTotalPages = 2
            //            )
            array(
                array('page_index' => 1),
                $expectedTotalPages = 0,
            ),
        );
    }

    /**
     * @test
     */
    public function itShouldReturnOkResponseWhenRequestingMovementsTypes()
    {
        $this->assertOkResponseOnList('api_stock_list_movements_types');
    }

    /**
     * @test
     */
    public function itShouldReturnOkResponseWhenRequestingMovementsEmployees()
    {
        $this->assertOkResponseOnList('api_stock_list_movements_employees');
    }
}
