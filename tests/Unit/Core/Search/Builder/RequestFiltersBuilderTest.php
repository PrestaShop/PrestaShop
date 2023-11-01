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

namespace Tests\Unit\Core\Search\Builder;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Search\Builder\RequestFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters;
use Symfony\Component\HttpFoundation\InputBag;
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
        $this->assertEmpty($builtFilters->getFilterId());
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
        $this->assertEmpty($filters->getFilterId());
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
        $this->assertEmpty($builtFilters->getFilterId());
    }

    public function testBuildWithGetRequestAndFilterId()
    {
        $expectedParameters = [
            'limit' => 10,
            'offset' => 10,
            'unknownParameter' => 'plop',
        ];
        $requestMock = $this->buildRequestMock($expectedParameters, 'language');

        $builder = new RequestFiltersBuilder();
        $builder->setConfig(['request' => $requestMock, 'filter_id' => 'language']);
        $filters = $builder->buildFilters();
        $this->assertNotNull($filters);
        unset($expectedParameters['unknownParameter']);
        $this->assertEquals($expectedParameters, $filters->all());
        $this->assertEquals('language', $filters->getFilterId());
    }

    public function testOverrideWithGetRequestAndFilterId()
    {
        $requestParameters = [
            'limit' => 10,
            'offset' => 10,
            'unknownParameter' => 'plop',
        ];
        $requestMock = $this->buildRequestMock($requestParameters, 'alternate_language');

        $filters = new Filters(['limit' => 20, 'sortOrder' => 'ASC'], 'alternate_language');
        $builder = new RequestFiltersBuilder();
        $builder->setConfig(['request' => $requestMock, 'filter_id' => 'language']);
        $builtFilters = $builder->buildFilters($filters);
        $this->assertNotNull($builtFilters);

        $expectedParameters = [
            'limit' => 10,
            'offset' => 10,
            'sortOrder' => 'ASC',
        ];
        $this->assertEquals($expectedParameters, $builtFilters->all());
        $this->assertEquals('alternate_language', $builtFilters->getFilterId());
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
        $this->assertEmpty($filters->getFilterId());
    }

    public function testOverrideWithPostRequestAndFilterId()
    {
        $requestParameters = [
            'limit' => 10,
            'offset' => 10,
            'unknownParameter' => 'plop',
        ];
        $requestMock = $this->buildRequestMock($requestParameters, 'alternate_language', true);

        $filters = new Filters(['limit' => 20, 'sortOrder' => 'ASC'], 'alternate_language');
        $builder = new RequestFiltersBuilder();
        $builder->setConfig(['request' => $requestMock, 'filter_id' => 'language']);
        $filters = $builder->buildFilters($filters);
        $this->assertNotNull($filters);

        $expectedParameters = [
            'limit' => 10,
            'offset' => 10,
            'sortOrder' => 'ASC',
        ];
        $this->assertEquals($expectedParameters, $filters->all());
        $this->assertEquals('alternate_language', $filters->getFilterId());
    }

    /**
     * @param array $parameters
     * @param string $requestScope
     * @param bool $postQuery
     *
     * @return MockObject|Request
     */
    private function buildRequestMock(array $parameters, $requestScope = '', $postQuery = false)
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $parametersBagMock = new InputBag();

        if (!empty($requestScope)) {
            $parameters = [
                $requestScope => $parameters,
            ];
        }

        $parametersBagMock->replace($parameters);

        $emptyParametersBagMock = new InputBag();

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
