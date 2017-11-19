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

namespace Tests\Unit\PrestaShopBundle\Api;

use Exception;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Api\QueryParamsCollection;
use PrestaShopBundle\Api\QueryStockParamsCollection;
use Prophecy\Prophet;

/**
 * @group api
 */
class QueryParamsCollectionTest extends TestCase
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
        $this->queryParams = new QueryStockParamsCollection();
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
                $pageSize = 100
            ),
            array(
                $pageIndex = 1,
                $pageSize = 100 + 1
            ),
        );
    }

    /**
     * @dataProvider getQueryParams
     * @test
     *
     * @param $order
     * @param $pageIndex
     * @param $pageSize
     * @param $expectedSqlClauses
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
                '_attributes' => $this->mockAttributes(array())->reveal()
            )
        );

        $this->queryParams = $this->queryParams->fromRequest($requestMock->reveal());

        $sqlParts = array(
            $this->queryParams->getSqlOrder(),
            $this->queryParams->getSqlParams(),
            $this->queryParams->getSqlFilters(),
        );

        $expectedSqlClauses[2] = array(QueryParamsCollection::SQL_CLAUSE_WHERE => '');

        $this->assertInternalType('array', $sqlParts);

        $this->assertEquals($expectedSqlClauses, $sqlParts);
    }

    /**
     * @dataProvider getQueryParams
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
                '_attributes' => $this->mockAttributes(array('productId' => 1))->reveal()
            )
        );

        $this->queryParams = $this->queryParams->fromRequest($requestMock->reveal());

        $sqlParts = array(
            $this->queryParams->getSqlOrder(),
            $this->queryParams->getSqlParams(),
            $this->queryParams->getSqlFilters(),
        );

        $expectedSqlClauses[1]['product_id'] = 1;
        $expectedSqlClauses[2] = array(QueryParamsCollection::SQL_CLAUSE_WHERE => 'AND {product_id} = :product_id');

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
     * @dataProvider getFilterParams
     * @test
     *
     * @param $params
     * @param $expectedSql
     * @param $message
     */
    public function it_should_make_query_params_with_filter_from_a_request(
        $params,
        $expectedSql,
        $message
    )
    {
        $requestMock = $this->mockRequest(array_merge(
            $params,
            array('_attributes' => $this->mockAttributes(array())->reveal())
        ));
        $this->queryParams = $this->queryParams->fromRequest($requestMock->reveal());
        $this->assertEquals(
            $expectedSql,
            $this->queryParams->getSqlFilters(),
            $message
        );
    }

    /**
     * @return array
     */
    public function getFilterParams()
    {
        $supplierFilterMessage = 'It should provide with a SQL condition clause on supplier';
        $categoryFilterMessage = 'It should provide with a SQL condition clause on category';
        $keywordsFilterMessage =
            'It should provide with SQL conditions clauses on product references, names and supplier names'
        ;
        $attributesFilterMessage = 'It should provide with SQL conditions clauses on product attributes';
        $featuresFilterMessage = 'It should provide with SQL conditions clauses on product features';

        return array(
            array(
                array('supplier_id' => 1),
                array(QueryParamsCollection::SQL_CLAUSE_WHERE => 'AND {supplier_id} = :supplier_id'),
                $supplierFilterMessage
            ),
            array(
                array('supplier_id' => array(1, 2)),
                array(QueryParamsCollection::SQL_CLAUSE_WHERE => 'AND {supplier_id} IN (:supplier_id_0,:supplier_id_1)'),
                $supplierFilterMessage
            ),
            array(
                array('category_id' => 1),
                array(QueryParamsCollection::SQL_CLAUSE_WHERE => 'AND FIND_IN_SET({category_id}, :categories_ids)'),
                $categoryFilterMessage
            ),
            array(
                array('category_id' => array(1, 2)),
                array(QueryParamsCollection::SQL_CLAUSE_WHERE => 'AND FIND_IN_SET({category_id}, :categories_ids)'),
                $categoryFilterMessage
            ),
            array(
                array('keywords' => 'Fashion'),
                array(
                    QueryParamsCollection::SQL_CLAUSE_WHERE => '',
                    QueryParamsCollection::SQL_CLAUSE_HAVING => 'AND (' .
                        '{supplier_name} LIKE :keyword_0 OR ' .
                        '{product_reference} LIKE :keyword_0 OR ' .
                        '{product_name} LIKE :keyword_0 OR ' .
                        '{combination_name} LIKE :keyword_0' .
                        ')'
                ),
                $keywordsFilterMessage
            ),
            array(
                array('keywords' => 'Chiffon'),
                array(
                    QueryParamsCollection::SQL_CLAUSE_WHERE => '',
                    QueryParamsCollection::SQL_CLAUSE_HAVING => 'AND (' .
                        '{supplier_name} LIKE :keyword_0 OR ' .
                        '{product_reference} LIKE :keyword_0 OR ' .
                        '{product_name} LIKE :keyword_0 OR ' .
                        '{combination_name} LIKE :keyword_0' .
                        ')',
                ),
                $keywordsFilterMessage
            ),
            array(
                array('keywords' => array('Chiffon', 'demo_7', 'Size - S')),
                array(
                    QueryParamsCollection::SQL_CLAUSE_WHERE => '',
                    QueryParamsCollection::SQL_CLAUSE_HAVING => 'AND (' .
                        '{supplier_name} LIKE :keyword_0 OR ' .
                        '{product_reference} LIKE :keyword_0 OR ' .
                        '{product_name} LIKE :keyword_0 OR ' .
                        '{combination_name} LIKE :keyword_0' .
                        ')' . "\n" .
                        'AND (' .
                        '{supplier_name} LIKE :keyword_1 OR ' .
                        '{product_reference} LIKE :keyword_1 OR ' .
                        '{product_name} LIKE :keyword_1 OR ' .
                        '{combination_name} LIKE :keyword_1' .
                        ')' . "\n" .
                        'AND (' .
                        '{supplier_name} LIKE :keyword_2 OR ' .
                        '{product_reference} LIKE :keyword_2 OR ' .
                        '{product_name} LIKE :keyword_2 OR ' .
                        '{combination_name} LIKE :keyword_2' .
                        ')'
                ),
                $keywordsFilterMessage
            ),
            array(
                array('attributes' => '1:2'),
                array(
                    QueryParamsCollection::SQL_CLAUSE_WHERE => 'AND '.
                        'FIND_IN_SET(:attribute_0, {attributes})'
                ),
                $attributesFilterMessage
            ),
            array(
                array('attributes' => array('1:2', '3:14')),
                array(
                    QueryParamsCollection::SQL_CLAUSE_WHERE => 'AND ' .
                        'FIND_IN_SET(:attribute_0, {attributes})' . "\n" .
                        'AND FIND_IN_SET(:attribute_1, {attributes})'
                ),
                $attributesFilterMessage
            ),
            array(
                array('features' => '5:1'),
                array(
                    QueryParamsCollection::SQL_CLAUSE_WHERE => 'AND ' .
                        'FIND_IN_SET(:feature_0, {features})'
                ),
                $featuresFilterMessage
            ),
            array(
                array('features' => array('5:1', '6:11')),
                array(
                    QueryParamsCollection::SQL_CLAUSE_WHERE => 'AND ' .
                        'FIND_IN_SET(:feature_0, {features})' . "\n" .
                        'AND FIND_IN_SET(:feature_1, {features})'
                ),
                $featuresFilterMessage
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
            'category_id',
            'keywords',
            'attributes',
            'features',
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
        if (array_key_exists('_attributes', $params)) {
            $attributesMock = $params['_attributes'];
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
