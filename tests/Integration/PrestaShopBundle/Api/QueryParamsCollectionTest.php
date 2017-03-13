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

    public function setUp()
    {
        $this->prophet = new Prophet();
    }

    /**
     * @param $filterQuery
     * @param $pageIndex
     * @param $pageSize
     * @param $expectedSqlClauses
     *
     * @dataProvider getQueryParams
     */
    public function testFromRequest(
        $filterQuery,
        $pageIndex,
        $pageSize,
        $expectedSqlClauses
    )
    {
        $queryParams = new QueryParamsCollection();
        $requestMock = $this->mockRequest($filterQuery, $pageIndex, $pageSize);

        $queryParams = $queryParams->fromRequest($requestMock->reveal());

        $sqlClauses = $queryParams->toSqlClauses();

        $this->assertInternalType('array', $sqlClauses);
        $this->assertArrayHasKey('order', $sqlClauses);
        $this->assertArrayHasKey('limit', $sqlClauses);
        $this->assertArrayHasKey('limit_params', $sqlClauses);
        $this->assertEquals($expectedSqlClauses, $sqlClauses);
    }

    public function getQueryParams()
    {
        $filterQueries = array(
            array('product', null, '1', array(
                'order' => 'ORDER BY {product} ',
                'limit' => 'LIMIT :first_result,:max_result',
                'limit_params' => array(
                    'max_result' => 1,
                    'first_result' => 0
                )
            )),
            array('reference DESC', '3', null, array(
                'order' => 'ORDER BY {reference} DESC ',
                'limit' => 'LIMIT :first_result,:max_result',
                'limit_params' => array(
                    'max_result' => 100,
                    'first_result' => 200
                )
            )),
            array('supplier desc', null, null, array(
                'order' => 'ORDER BY {supplier} DESC ',
                'limit' => 'LIMIT :first_result,:max_result',
                'limit_params' => array(
                    'max_result' => 100,
                    'first_result' => 0
                )
            )),
            array('available_quantity DESC', null, null, array(
                'order' => 'ORDER BY {available_quantity} DESC ',
                'limit' => 'LIMIT :first_result,:max_result',
                'limit_params' => array(
                    'max_result' => 100,
                    'first_result' => 0
                )
            )),
            array('available_quantity DESC', '2', '4', array(
                'order' => 'ORDER BY {available_quantity} DESC ',
                'limit' => 'LIMIT :first_result,:max_result',
                'limit_params' => array(
                    'max_result' => 4,
                    'first_result' => 4
                )
            )),
        );

        return $filterQueries;
    }

    /**
     * @param $filterQuery
     * @param $pageIndex
     * @param $pageSize
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    private function mockQuery($filterQuery, $pageIndex, $pageSize)
    {
        $queryMock = $this->prophet->prophesize('\Symfony\Component\HttpFoundation\ParameterBag');

        $params = array('filter' => $filterQuery);

        if (!is_null($pageIndex)) {
            $params['page_index'] = $pageIndex;
        }

        if (!is_null($pageSize)) {
            $params['page_size'] = $pageSize;
        }
        $queryMock->all()->willReturn($params);

        return $queryMock;
    }

    /**
     * @param $filterQuery
     * @param $pageIndex
     * @param $pageSize
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    private function mockRequest($filterQuery, $pageIndex, $pageSize)
    {
        $queryMock = $this->mockQuery($filterQuery, $pageIndex, $pageSize);
        $requestMock = $this->prophet->prophesize('\Symfony\Component\HttpFoundation\Request');
        $requestMock->query = $queryMock->reveal();

        return $requestMock;
    }
}
