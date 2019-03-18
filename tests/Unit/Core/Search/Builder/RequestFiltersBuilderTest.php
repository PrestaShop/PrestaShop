<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Search\Builder;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Search\Builder\RequestFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class RequestFiltersBuilderTest extends TestCase
{
    public function testBuildWithoutRequest()
    {
        $builder = new RequestFiltersBuilder();
        $filters = $builder->buildFilters();
        $this->assertNull($filters);
    }

    public function testOverrideWithoutRequest()
    {
        $builder = new RequestFiltersBuilder();
        $filters = new Filters(['limit' => 10]);
        $builtFilters = $builder->buildFilters($filters);
        $this->assertNotNull($builtFilters);
        $this->assertEquals($filters->all(), $builtFilters->all());
        $this->assertEmpty($builtFilters->getUuid());
    }

    public function testBuildWithGetRequest()
    {
        $expectedParameters = [
            'limit' => 10,
            'offset' => 10,
            'unknownParameter' => 'plop',
        ];
        $requestMock = $this->buildRequestMock($expectedParameters);

        $builder = new RequestFiltersBuilder();
        $builder->setConfig(['request' => $requestMock]);
        $filters = $builder->buildFilters();
        $this->assertNotNull($filters);
        unset($expectedParameters['unknownParameter']);
        $this->assertEquals($expectedParameters, $filters->all());
        $this->assertEmpty($filters->getUuid());
    }

    public function testOverrideWithGetRequest()
    {
        $requestParameters = [
            'limit' => 10,
            'offset' => 10,
            'unknownParameter' => 'plop',
        ];
        $requestMock = $this->buildRequestMock($requestParameters);

        $filters = new Filters(['limit' => 20, 'orderBy' => 'language_id']);
        $builder = new RequestFiltersBuilder();
        $builder->setConfig(['request' => $requestMock]);
        $builtFilters = $builder->buildFilters($filters);
        $this->assertNotNull($builtFilters);
        $expectedParameters = [
            'limit' => 10,
            'offset' => 10,
            'orderBy' => 'language_id',
        ];
        $this->assertEquals($expectedParameters, $filters->all());
        $this->assertEmpty($builtFilters->getUuid());
    }

    public function testBuildWithGetRequestAndFiltersUuid()
    {
        $expectedParameters = [
            'limit' => 10,
            'offset' => 10,
            'unknownParameter' => 'plop',
        ];
        $requestMock = $this->buildRequestMock($expectedParameters, 'language');

        $builder = new RequestFiltersBuilder();
        $builder->setConfig(['request' => $requestMock, 'filters_uuid' => 'language']);
        $filters = $builder->buildFilters();
        $this->assertNotNull($filters);
        unset($expectedParameters['unknownParameter']);
        $this->assertEquals($expectedParameters, $filters->all());
        $this->assertEquals('language', $filters->getUuid());
    }

    public function testOverrideWithGetRequestAndFiltersUuid()
    {
        $requestParameters = [
            'limit' => 10,
            'offset' => 10,
            'unknownParameter' => 'plop',
        ];
        $requestMock = $this->buildRequestMock($requestParameters, 'language');

        $filters = new Filters(['limit' => 20, 'sortOrder' => 'ASC'], 'alternate_language');
        $builder = new RequestFiltersBuilder();
        $builder->setConfig(['request' => $requestMock, 'filters_uuid' => 'language']);
        $filters = $builder->buildFilters($filters);
        $this->assertNotNull($filters);

        $expectedParameters = [
            'limit' => 10,
            'offset' => 10,
            'sortOrder' => 'ASC',
        ];
        $this->assertEquals($expectedParameters, $filters->all());
        $this->assertEquals('alternate_language', $filters->getUuid());
    }

    public function testBuildWithPostRequest()
    {
        $expectedParameters = [
            'limit' => 10,
            'offset' => 10,
            'unknownParameter' => 'plop',
        ];
        $requestMock = $this->buildRequestMock($expectedParameters, '', true);

        $builder = new RequestFiltersBuilder();
        $builder->setConfig(['request' => $requestMock]);
        $filters = $builder->buildFilters();
        $this->assertNotNull($filters);
        unset($expectedParameters['unknownParameter']);
        $this->assertEquals($expectedParameters, $filters->all());
        $this->assertEmpty($filters->getUuid());
    }

    public function testOverrideWithPostRequestAndFiltersUuid()
    {
        $requestParameters = [
            'limit' => 10,
            'offset' => 10,
            'unknownParameter' => 'plop',
        ];
        $requestMock = $this->buildRequestMock($requestParameters, 'language', true);

        $filters = new Filters(['limit' => 20, 'sortOrder' => 'ASC'], 'alternate_language');
        $builder = new RequestFiltersBuilder();
        $builder->setConfig(['request' => $requestMock, 'filters_uuid' => 'language']);
        $filters = $builder->buildFilters($filters);
        $this->assertNotNull($filters);

        $expectedParameters = [
            'limit' => 10,
            'offset' => 10,
            'sortOrder' => 'ASC',
        ];
        $this->assertEquals($expectedParameters, $filters->all());
        $this->assertEquals('alternate_language', $filters->getUuid());
    }

    /**
     * @param array $parameters
     * @param string $requestScope
     * @param bool $postQuery
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Request
     */
    private function buildRequestMock(array $parameters, $requestScope = '', $postQuery = false)
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $parametersBagMock = $this->getMockBuilder(ParameterBag::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        if (!empty($requestScope)) {
            $parameters = [
                $requestScope => $parameters,
            ];
        }

        $parametersBagMock
            ->expects($this->once())
            ->method('all')
            ->willReturn($parameters)
        ;

        $emptyParametersBagMock = $this->getMockBuilder(ParameterBag::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $emptyParametersBagMock
            ->expects($this->once())
            ->method('all')
            ->willReturn([])
        ;

        if ($postQuery) {
            $requestMock->request = $parametersBagMock;
            $requestMock->query = $emptyParametersBagMock;
        } else {
            $requestMock->query = $parametersBagMock;
            $requestMock->request = $emptyParametersBagMock;
        }

        return $requestMock;
    }
}
