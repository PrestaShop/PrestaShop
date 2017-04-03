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

use PrestaShopBundle\Api\QueryParamsCollection;

/**
 * @group api
 */
class StockMovementControllerTest extends ApiTestCase
{
    public function setUp()
    {
        parent::setUp();

        $legacyContextMock = $this->mockContextAdapter();

        $container = self::$kernel->getContainer();
        $container->set('prestashop.adapter.legacy.context', $legacyContextMock->reveal());
    }

    /**
     * @test
     */
    public function it_should_return_bad_request_response_on_invalid_pagination_params()
    {
        $route = $this->router->generate('api_stock_list_movements', array());

        $this->client->request('GET', $route, array('page_index' => 0));
        $response = $this->client->getResponse();

        $this->assertEquals(400, $response->getStatusCode(), 'It should return a response with "Bad Request" Status.');
    }

    /**
     * @dataProvider getMovementsStockParams
     * @test
     *
     * @param $params
     * @param $expectedTotalPages
     */
    public function it_should_return_ok_response_when_requesting_movements_list($params, $expectedTotalPages)
    {
        $this->assertOkResponseOnListMovements('api_stock_list_movements', $params, $expectedTotalPages);
    }

    /**
     * @return array
     */
    public function getMovementsStockParams()
    {
        return array(
            array(
                array(),
                $expectedTotalPages = 1
            )
        );
    }

    /**
     * @param $routeName
     * @param array $parameters
     * @param $expectedTotalPages
     */
    private function assertOkResponseOnListMovements(
        $routeName,
        $parameters = array(),
        $expectedTotalPages = null
    )
    {
        $route = $this->router->generate($routeName, $parameters);
        $this->client->request('GET', $route);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'It should return a response with "OK" Status.');

        $this->assertResponseHasTotalPages($parameters, $expectedTotalPages);
    }

    /**
     * @param $parameters
     * @param $expectedTotalPages
     */
    private function assertResponseHasTotalPages($parameters, $expectedTotalPages)
    {
        if (is_null($expectedTotalPages)) {
            return;
        }

        $pageSize = QueryParamsCollection::DEFAULT_PAGE_SIZE;
        if (array_key_exists('page_size', $parameters)) {
            $pageSize = $parameters['page_size'];
        }

        $response = $this->client->getResponse();

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
}
