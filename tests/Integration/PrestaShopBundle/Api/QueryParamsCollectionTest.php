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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Tests\Integration\PrestaShopBundle\Api;

use Exception;
use PrestaShopBundle\Api\QueryParamsCollection;
use Prophecy\Prophet;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group api
 */
class QueryParamsCollectionTest extends WebTestCase
{
    /**
     * @var Prophet
     */
    private $prophet;

    /**
     * @var QueryParamsCollection
     */
    private $queryParams;

    public function setUp()
    {
        $this->prophet = new Prophet();
        $this->queryParams = new QueryParamsCollection();
    }

    /**
     * @dataProvider getInvalidPaginationParams
     *
     * @test
     *
     * @param $pageIndex
     * @param $pageSize
     */
    public function it_should_raise_an_exception_on_invalid_pagination_params($pageIndex, $pageSize)
    {
        try {
            $this->it_should_make_query_params_from_a_request(
                'product',
                $pageIndex,
                $pageSize,
                array()
            );
            $this->fail('Invalid pagination params should raise an exception');
        } catch (Exception $exception) {
            $expectedInstanceOf = '\PrestaShopBundle\Exception\InvalidPaginationParamsException';
            $this->assertInstanceOf(
                $expectedInstanceOf,
                $exception,
                sprintf('It should raise an instance of %s', $expectedInstanceOf)
            );
        }
    }

    /**
     * @return array
     */
    public function getInvalidPaginationParams()
    {
        return array(
            array(
                $pageIndex = 0,
                $pageSize = QueryParamsCollection::DEFAULT_PAGE_SIZE
            ),
            array(
                $pageIndex = 1,
                $pageSize = QueryParamsCollection::DEFAULT_PAGE_SIZE + 1
            ),
        );
    }

    /**
     * @test
     *
     * @param $order
     * @param $pageIndex
     * @param $pageSize
     * @param $expectedSqlClauses
     *
     * @dataProvider getQueryParams
     */
    public function it_should_make_query_params_from_a_request(
        $order,
        $pageIndex,
        $pageSize,
        $expectedSqlClauses
    )
    {
        $requestMock = $this->mockRequest(
            array(
                'order' => $order,
                'page_index' => $pageIndex,
                'page_size' => $pageSize,
                'attributes' => $this->mockAttributes(array())->reveal()
            )
        );

        $this->queryParams = $this->queryParams->fromRequest($requestMock->reveal());

        $sqlParts = array(
            $this->queryParams->getSqlOrder(),
            $this->queryParams->getSqlParams(),
            $this->queryParams->getSqlFilters(),
        );

        $expectedSqlClauses[2] = '';

        $this->assertInternalType('array', $sqlParts);

        $this->assertEquals($expectedSqlClauses, $sqlParts);
    }

    /**
     * @dataProvider getQueryParams
     *
     * @test
     *
     * @param $order
     * @param $pageIndex
     * @param $pageSize
     * @param $expectedSqlClauses
     */
    public function it_should_make_query_params_with_product_filter_from_a_request(
        $order,
        $pageIndex,
        $pageSize,
        $expectedSqlClauses
    )
    {
        $requestMock = $this->mockRequest(
            array(
                'order' => $order,
                'page_index' => $pageIndex,
                'page_size' => $pageSize,
                'attributes' => $this->mockAttributes(array('productId' => 1))->reveal()
            )
        );

        $this->queryParams = $this->queryParams->fromRequest($requestMock->reveal());

        $sqlParts = array(
            $this->queryParams->getSqlOrder(),
            $this->queryParams->getSqlParams(),
            $this->queryParams->getSqlFilters(),
        );

        $expectedSqlClauses[1]['product_id'] = 1;
        $expectedSqlClauses[2] = 'AND {product_id} = :product_id';

        $this->assertInternalType('array', $sqlParts);

        $this->assertEquals($expectedSqlClauses, $sqlParts);
    }

    /**
     * @return array
     */
    public function getQueryParams()
    {
        return array(
            array('product', null, '1', array(
                'ORDER BY {product} ',
                array(
                    'max_results' => 1,
                    'first_result' => 0
                )
            )),
            array('reference DESC', '3', null, array(
                'ORDER BY {reference} DESC ',
                array(
                    'max_results' => 100,
                    'first_result' => 200
                )
            )),
            array('supplier desc', null, null, array(
                'ORDER BY {supplier} DESC ',
                array(
                    'max_results' => 100,
                    'first_result' => 0
                )
            )),
            array('available_quantity DESC', null, null, array(
                'ORDER BY {available_quantity} DESC ',
                array(
                    'max_results' => 100,
                    'first_result' => 0
                )
            )),
            array('available_quantity DESC', '2', '4', array(
                'ORDER BY {available_quantity} DESC ',
                array(
                    'max_results' => 4,
                    'first_result' => 4
                )
            )),
            array('physical_quantity', '3', '3', array(
                'ORDER BY {physical_quantity} ',
                array(
                    'max_results' => 3,
                    'first_result' => 6
                )
            )),
        );
    }

    /**
     * @test
     *
     * @param $params
     * @param $expectedSql
     *
     * @dataProvider getFilterParams
     */
    public function it_should_make_query_params_with_supplier_filter_from_a_request(
        $params,
        $expectedSql
    )
    {
        $requestMock = $this->mockRequest(array_merge(
            $params,
            array('attributes' => $this->mockAttributes(array())->reveal())
        ));
        $this->queryParams = $this->queryParams->fromRequest($requestMock->reveal());
        $this->assertEquals(
            $expectedSql,
            $this->queryParams->getSqlFilters(),
            'It should provide with a SQL clause condition on supplier column'
        );
    }

    /**
     * @return array
     */
    public function getFilterParams()
    {
        return array(
            array(
                array('supplier_id' => 1),
                'AND {supplier_id} = :supplier_id'
            ),
            array(
                array('supplier_id' => array(1, 2)),
                'AND {supplier_id} IN (:supplier_id_0,:supplier_id_1)'
            )
        );
    }

    /**
     * @param array $testedParams
     * @return \Prophecy\Prophecy\ObjectProphecy|\Symfony\Component\HttpFoundation\ParameterBag
     */
    private function mockQuery(array $testedParams)
    {
        $params = array();
        $validQueryParams = array(
            'order',
            'page_index',
            'page_size',
            'supplier_id',
        );

        array_walk($validQueryParams, function ($name) use ($testedParams, &$params) {
            if (array_key_exists($name, $testedParams) && !is_null($testedParams[$name])) {
                $params[$name] = $testedParams[$name];
            }
        });

        /** @var \Symfony\Component\HttpFoundation\ParameterBag $queryMock */
        $queryMock = $this->prophet->prophesize('\Symfony\Component\HttpFoundation\ParameterBag');
        $queryMock->all()->willReturn($params);

        /** @var \Prophecy\Prophecy\ObjectProphecy $queryMock */
        return $queryMock;
    }

    /**
     * @param array $attributes
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    private function mockAttributes(array $attributes)
    {
        $attributesMock = $this->prophet->prophesize('\Symfony\Component\HttpFoundation\ParameterBag');
        $attributesMock->all()->willReturn($attributes);

        return $attributesMock;
    }

    /**
     * @param array $params
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    private function mockRequest(array $params)
    {
        $attributesMock = null;
        if (array_key_exists('attributes', $params)) {
            $attributesMock = $params['attributes'];
        }

        $queryMock = $this->mockQuery($params);
        $requestMock = $this->prophet->prophesize('\Symfony\Component\HttpFoundation\Request');
        $requestMock->query = $queryMock->reveal();

        if (!is_null($attributesMock)) {
            $requestMock->attributes = $attributesMock;
        }

        return $requestMock;
    }
}
