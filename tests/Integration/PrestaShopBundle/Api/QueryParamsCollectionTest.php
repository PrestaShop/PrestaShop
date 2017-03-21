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
     * @param $orderQuery
     * @param $pageIndex
     * @param $pageSize
     * @param $expectedSqlClauses
     *
     * @dataProvider getQueryParams
     */
    public function it_should_make_query_params_from_a_request(
        $orderQuery,
        $pageIndex,
        $pageSize,
        $expectedSqlClauses
    ) {
        $attributesMock = $this->mockAttributes(array());
        $requestMock = $this->mockRequest($orderQuery, $pageIndex, $pageSize, $attributesMock);

        $this->queryParams = $this->queryParams->fromRequest($requestMock->reveal());

        $sqlParts = array(
            $this->queryParams->getSqlOrder(),
            $this->queryParams->getSqlParams(),
            $this->queryParams->getSqlFilter(),
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
     * @param $orderQuery
     * @param $pageIndex
     * @param $pageSize
     * @param $expectedSqlClauses
     */
    public function it_should_make_query_params_with_filter_from_a_request(
        $orderQuery,
        $pageIndex,
        $pageSize,
        $expectedSqlClauses
    ) {
        $attributesMock = $this->mockAttributes(array('productId' => 1));
        $requestMock = $this->mockRequest($orderQuery, $pageIndex, $pageSize, $attributesMock->reveal());

        $this->queryParams = $this->queryParams->fromRequest($requestMock->reveal());

        $sqlParts = array(
            $this->queryParams->getSqlOrder(),
            $this->queryParams->getSqlParams(),
            $this->queryParams->getSqlFilter(),
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
                    'max_result' => 1,
                    'first_result' => 0
                )
            )),
            array('reference DESC', '3', null, array(
                'ORDER BY {reference} DESC ',
                array(
                    'max_result' => 100,
                    'first_result' => 200
                )
            )),
            array('supplier desc', null, null, array(
                'ORDER BY {supplier} DESC ',
                array(
                    'max_result' => 100,
                    'first_result' => 0
                )
            )),
            array('available_quantity DESC', null, null, array(
                'ORDER BY {available_quantity} DESC ',
                array(
                    'max_result' => 100,
                    'first_result' => 0
                )
            )),
            array('available_quantity DESC', '2', '4', array(
                'ORDER BY {available_quantity} DESC ',
                array(
                    'max_result' => 4,
                    'first_result' => 4
                )
            )),
            array('physical_quantity', '3', '3', array(
                'ORDER BY {physical_quantity} ',
                array(
                    'max_result' => 3,
                    'first_result' => 6
                )
            )),
        );
    }

    /**
     * @param $orderQuery
     * @param $pageIndex
     * @param $pageSize
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    private function mockQuery($orderQuery, $pageIndex, $pageSize)
    {
        /** @var \Symfony\Component\HttpFoundation\ParameterBag $queryMock */
        $queryMock = $this->prophet->prophesize('\Symfony\Component\HttpFoundation\ParameterBag');

        $params = array('order' => $orderQuery);

        if (!is_null($pageIndex)) {
            $params['page_index'] = $pageIndex;
        }

        if (!is_null($pageSize)) {
            $params['page_size'] = $pageSize;
        }
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
        $queryMock = $this->prophet->prophesize('\Symfony\Component\HttpFoundation\ParameterBag');
        $queryMock->all()->willReturn($attributes);

        return $queryMock;
    }

    /**
     * @param $orderQuery
     * @param $pageIndex
     * @param $pageSize
     * @param null $attributesMock
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    private function mockRequest($orderQuery, $pageIndex, $pageSize, $attributesMock = null)
    {
        $queryMock = $this->mockQuery($orderQuery, $pageIndex, $pageSize);
        $requestMock = $this->prophet->prophesize('\Symfony\Component\HttpFoundation\Request');
        $requestMock->query = $queryMock->reveal();

        if (!is_null($attributesMock)) {
            $requestMock->attributes = $attributesMock;
        }

        return $requestMock;
    }
}
