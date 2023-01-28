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

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Api;

use Cache;
use Doctrine\DBAL\Connection;
use PrestaShopBundle\Api\QueryStockParamsCollection;
use Tests\Resources\DatabaseDump;

class StockManagementControllerTest extends ApiTestCase
{
    /**
     * @var Connection
     */
    private $connection;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::restoreDatabase();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::restoreDatabase();
    }

    protected static function restoreDatabase(): void
    {
        DatabaseDump::restoreTables([
            'product',
            'product_attribute',
            'stock_available',
            'stock_mvt',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = self::$kernel->getContainer()->get('doctrine.dbal.default_connection');

        $stockMovementRepository = $this->getMockBuilder('PrestaShopBundle\Entity\Repository\StockMovementRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $stockMovementRepository->method('saveStockMvt')->willReturn(true);
        self::$container->set('prestashop.core.api.stock_movement.repository', $stockMovementRepository);

        $this->restoreQuantityEditionFixtures();
    }

    public function testItShouldReturnBadRequestResponseOnInvalidPaginationParams(): void
    {
        $routes = [
            $this->router->generate('api_stock_list_products', []),
            $this->router->generate('api_stock_list_movements', []),
        ];

        foreach ($routes as $route) {
            self::$client->request('GET', $route, ['page_index' => 0]);
            $response = self::$client->getResponse();
            $this->assertSame(400, $response->getStatusCode(), 'It should return a response with "Bad Request" Status.');
        }
    }

    /**
     * @dataProvider getProductsStockParams
     *
     * @param array $params
     * @param int $expectedTotalPages
     */
    public function testItShouldReturnOkResponseWhenRequestingProductsStock(array $params, int $expectedTotalPages): void
    {
        $this->assertOkResponseOnList('api_stock_list_products', $params, $expectedTotalPages);
    }

    /**
     * @return array
     */
    public function getProductsStockParams(): array
    {
        return [
            [
                [],
                1,
            ],
            [
                ['page_index' => 1, 'page_size' => 2],
                24,
            ],
            [
                ['supplier_id' => 1, 'page_index' => 2, 'page_size' => 2],
                0,
            ],
            [
                ['supplier_id' => [1, 2], 'page_index' => 2, 'page_size' => 2],
                0,
            ],
            [
                ['category_id' => 5, 'page_index' => 1, 'page_size' => 1],
                4,
            ],
            [
                ['category_id' => [4, 5], 'page_index' => 1, 'page_size' => 1],
                12,
            ],
        ];
    }

    /**
     * @dataProvider getProductsCombinationsParams
     *
     * @param array $params
     * @param int $expectedTotalPages
     */
    public function testItShouldReturnOkResponseWhenRequestingProductsCombinationsStock(array $params, int $expectedTotalPages): void
    {
        $this->assertOkResponseOnList('api_stock_list_product_combinations', $params, $expectedTotalPages);
    }

    /**
     * @return array
     */
    public function getProductsCombinationsParams(): array
    {
        return [
            [
                ['productId' => 1],
                1,
            ],
            [
                ['productId' => 7, 'page_index' => 1, 'page_size' => 2],
                1,
            ],
            [
                ['productId' => 1, 'category_id' => [4, 5], 'page_index' => 1, 'page_size' => 1],
                8,
            ],
        ];
    }

    /**
     * @param string $routeName
     * @param array $parameters
     * @param int|null $expectedTotalPages
     */
    private function assertOkResponseOnList(
        string $routeName,
        array $parameters = [],
        int $expectedTotalPages = null
    ): void {
        $route = $this->router->generate($routeName, $parameters);
        self::$client->request('GET', $route);

        $response = self::$client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'It should return a response with "OK" Status.');

        if ($expectedTotalPages) {
            $this->assertResponseHasTotalPages($parameters, $expectedTotalPages);
        }
    }

    /**
     * @param array $parameters
     * @param int $expectedTotalPages
     */
    private function assertResponseHasTotalPages(array $parameters, int $expectedTotalPages): void
    {
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

    public function testItShouldReturnErrorResponseWhenRequestingProductsStockEdition(): void
    {
        $this->assertErrorResponseOnEditProduct();
    }

    public function testItShouldReturnValidResponseWhenRequestingProductsCombinationsStockEdition(): void
    {
        $this->assertNotFoundResponseOnEditProductCombination();
        $this->assertOkResponseOnEditProductCombination();
    }

    public function testItShouldReturnValidResponseWhenRequestingBulkStockEdition(): void
    {
        $this->assertErrorResponseOnBulkEditProducts();
        $this->assertOkResponseOnBulkEditProducts();
    }

    private function assertErrorResponseOnEditProduct(): void
    {
        $editProductStockRoute = $this->router->generate(
            'api_stock_edit_product',
            ['productId' => 9]
        );

        self::$client->request('POST', $editProductStockRoute);
        $this->assertResponseBodyValidJson(400);

        self::$client->request('POST', $editProductStockRoute, [], [], [], '{}');
        $this->assertResponseBodyValidJson(400);

        self::$client->request('POST', $editProductStockRoute, ['delta' => 1]);
        $this->assertResponseBodyValidJson(404);
    }

    private function assertNotFoundResponseOnEditProductCombination(): void
    {
        $editProductStockRoute = $this->router->generate(
            'api_stock_edit_product_combination',
            [
                'productId' => 8,
                'combinationId' => 1,
            ]
        );

        self::$client->request('POST', $editProductStockRoute, ['delta' => 1]);

        $this->assertResponseBodyValidJson(404);
    }

    private function assertOkResponseOnEditProductCombination(): void
    {
        $this->restoreMovements();

        $editProductStockRoute = $this->router->generate(
            'api_stock_edit_product_combination',
            [
                'productId' => 1,
                'combinationId' => 1,
            ]
        );

        self::$client->request('POST', $editProductStockRoute, ['delta' => 2]);

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
            [
                'available_quantity' => 10,
                'physical_quantity' => 10,
                'reserved_quantity' => 0,
            ],
            $content
        );

        self::$client->request('POST', $editProductStockRoute, ['delta' => -4]);
        $content = $this->assertResponseBodyValidJson(200);

        $this->assertProductQuantity(
            [
                'available_quantity' => 6,
                'physical_quantity' => 6,
                'reserved_quantity' => 0,
            ],
            $content
        );

        self::$client->request('POST', $editProductStockRoute, [], [], [], '{"delta": 0}');
        $this->assertResponseBodyValidJson(200);
    }

    /**
     * @param array $expectedQuantities
     * @param array $content
     */
    private function assertProductQuantity(array $expectedQuantities, array $content): void
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

    private function assertErrorResponseOnBulkEditProducts(): void
    {
        $bulkEditProductsRoute = $this->router->generate('api_stock_bulk_edit_products');

        self::$client->request('POST', $bulkEditProductsRoute);
        $content = $this->assertResponseBodyValidJson(400);
        $this->assertEquals([
            'error' => 'Invalid JSON content (The request body should contain a JSON-encoded array of product identifiers and deltas)',
        ], $content);

        self::$client->request('POST', $bulkEditProductsRoute, [], [], [], '[{"combination_id": 0}]');
        $content = $this->assertResponseBodyValidJson(400);
        $this->assertEquals([
            'error' => 'Each item of JSON-encoded array in the request body should contain a product id ("product_id"), a quantity delta ("delta"). The item of index #0 is invalid.',
        ], $content);

        self::$client->request('POST', $bulkEditProductsRoute, [], [], [], '[{"product_id": 1}]');
        $content = $this->assertResponseBodyValidJson(400);
        $this->assertEquals([
            'error' => 'Each item of JSON-encoded array in the request body should contain a product id ("product_id"), a quantity delta ("delta"). The item of index #0 is invalid.',
        ], $content);

        self::$client->request('POST', $bulkEditProductsRoute, [], [], [], '[{"delta": 0}]');
        $content = $this->assertResponseBodyValidJson(400);
        $this->assertEquals([
            'error' => 'Each item of JSON-encoded array in the request body should contain a product id ("product_id"), a quantity delta ("delta"). The item of index #0 is invalid.',
        ], $content);

        self::$client->request(
            'POST',
            $bulkEditProductsRoute,
            [],
            [],
            [],
            '[{"product_id": 1, "delta": 0}]'
        );
        $content = $this->assertResponseBodyValidJson(400);
        $this->assertEquals([
            'error' => 'Value cannot be 0.',
        ], $content);
    }

    private function assertOkResponseOnBulkEditProducts(): void
    {
        $bulkEditProductsRoute = $this->router->generate('api_stock_bulk_edit_products');

        self::$client->request(
            'POST',
            $bulkEditProductsRoute,
            [],
            [],
            [],
            '[{"product_id": 1, "combination_id": 1, "delta": 3},' .
            '{"product_id": 1, "combination_id": 1, "delta": -1}]'
        );
        $content = $this->assertResponseBodyValidJson(200);

        $this->assertArrayHasKey(0, $content, 'The response content should have one item with key #0');

        $this->assertProductQuantity(
            [
                'available_quantity' => 10,
                'physical_quantity' => 10,
                'reserved_quantity' => 0,
            ],
            $content[1]
        );

        self::$client->request(
            'POST',
            $bulkEditProductsRoute,
            [],
            [],
            [],
            '[{"product_id": 1, "combination_id": 1, "delta": 3},' .
            '{"product_id": 1, "combination_id": 1, "delta": -3}]'
        );
        $content = $this->assertResponseBodyValidJson(200);

        $this->assertArrayHasKey(0, $content, 'The response content should have one item with key #0');

        $this->assertProductQuantity(
            [
                'available_quantity' => 10,
                'physical_quantity' => 10,
                'reserved_quantity' => 0,
            ],
            $content[1]
        );
    }

    public function testItShouldReturnValidResponseWhenRequestingStockSearchResults(): void
    {
        $listProductsRoute = $this->router->generate('api_stock_list_products');

        self::$client->request(
            'GET',
            $listProductsRoute,
            ['keywords' => ['Chiffon', 'demo_7', 'Size - S']]
        );

        $this->assertResponseBodyValidJson(200);
    }

    public function testItShouldReturnValidResponseWhenRequestingStockWithAttributes(): void
    {
        $listProductsRoute = $this->router->generate('api_stock_list_products');

        self::$client->request(
            'GET',
            $listProductsRoute,
            ['attributes' => ['1:2', '3:14']]
        );

        $this->assertResponseBodyValidJson(200);
    }

    public function testItShouldReturnValidResponseWhenRequestingStockWithFeatures(): void
    {
        $listProductsRoute = $this->router->generate('api_stock_list_products');

        self::$client->request(
            'GET',
            $listProductsRoute,
            ['features' => ['5:1', '6:11']]
        );

        $this->assertResponseBodyValidJson(200);
    }

    /**
     * @dataProvider getMovementsStockParams
     *
     * @param array $params
     * @param int $expectedTotalPages
     */
    public function itShouldReturnOkResponseWhenRequestingMovementsStock(array $params, int $expectedTotalPages): void
    {
        $this->assertOkResponseOnList('api_stock_list_movements', $params, $expectedTotalPages);
    }

    public function getMovementsStockParams(): array
    {
        return [
            // @TODO when entity manager can save movements in db
            //            array(
            //                array(),
            //                1
            //            ),
            //            array(
            //                array('page_index' => 1, 'page_size' => 5),
            //                2
            //            )
            [
                ['page_index' => 1],
                0,
            ],
        ];
    }

    public function testItShouldReturnOkResponseWhenRequestingMovementsTypes(): void
    {
        $this->assertOkResponseOnList('api_stock_list_movements_types');
    }

    public function testItShouldReturnOkResponseWhenRequestingMovementsEmployees(): void
    {
        $this->assertOkResponseOnList('api_stock_list_movements_employees');
    }

    private function restoreMovements(): void
    {
        $deleteMovements = sprintf('DELETE FROM %sstock_mvt', _DB_PREFIX_);
        $statement = $this->connection->prepare($deleteMovements);
        $statement->executeStatement();
    }

    private function restoreQuantityEditionFixtures(): void
    {
        $updateProductQuantity = sprintf(
            'UPDATE %sstock_available SET quantity = 8, physical_quantity = 10, reserved_quantity = 2 WHERE id_product = 1 AND id_product_attribute = 1',
            _DB_PREFIX_
        );
        $statement = $this->connection->prepare($updateProductQuantity);
        $statement->executeStatement();
        // Clear cache for entity manager to fetch the new updated values
        Cache::clean('objectmodel_StockAvailable_*');
    }
}
