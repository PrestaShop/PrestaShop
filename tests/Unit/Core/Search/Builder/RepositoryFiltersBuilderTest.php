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
use PrestaShop\PrestaShop\Core\Search\Builder\RepositoryFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShopBundle\Entity\AdminFilter;
use PrestaShopBundle\Entity\Repository\AdminFilterRepository;

class RepositoryFiltersBuilderTest extends TestCase
{
    public function testBuildWithoutParameters()
    {
        /** @var AdminFilterRepository $repositoryMock */
        $repositoryMock = $this->getMockBuilder(AdminFilterRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $builder = new RepositoryFiltersBuilder($repositoryMock);
        $filters = $builder->buildFilters();
        $this->assertNull($filters);
    }

    public function testBuildWithFiltersUuid()
    {
        $expectedFilters = [
            'limit' => 10,
            'offset' => 10,
        ];
        $builder = new RepositoryFiltersBuilder($this->buildRepositoryByUuidMock($expectedFilters));
        $builder->setConfig([
            'filters_uuid' => 'language',
            'employee_id' => 1,
            'shop_id' => 1,
        ]);
        $filters = $builder->buildFilters();
        $this->assertNotNull($filters);
        $this->assertEquals($expectedFilters, $filters->all());
        $this->assertEquals('language', $filters->getUuid());
    }

    public function testOverrideWithFiltersUuid()
    {
        $repositoryFilters = [
            'limit' => 10,
            'offset' => 10,
        ];
        $builder = new RepositoryFiltersBuilder($this->buildRepositoryByUuidMock($repositoryFilters));
        $builder->setConfig([
            'filters_uuid' => 'language',
            'employee_id' => 1,
            'shop_id' => 1,
        ]);
        $filters = new Filters(['limit' => 20, 'orderBy' => 'language_id'], 'alternate_language');
        $builtFilters = $builder->buildFilters($filters);
        $this->assertNotNull($builtFilters);
        $expectedFilters = [
            'limit' => 10,
            'offset' => 10,
            'orderBy' => 'language_id',
        ];
        $this->assertEquals($expectedFilters, $builtFilters->all());
        $this->assertEquals('alternate_language', $filters->getUuid());
    }

    public function testBuildWithController()
    {
        $repositoryFilters = [
            'limit' => 10,
            'offset' => 10,
        ];
        $builder = new RepositoryFiltersBuilder($this->buildRepositoryByRouteMock($repositoryFilters));
        $builder->setConfig([
            'controller' => 'language',
            'action' => 'index',
            'employee_id' => 1,
            'shop_id' => 1,
        ]);
        $filters = new Filters(['limit' => 20, 'orderBy' => 'language_id'], 'alternate_language');
        $builtFilters = $builder->buildFilters($filters);
        $this->assertNotNull($builtFilters);
        $expectedFilters = [
            'limit' => 10,
            'offset' => 10,
            'orderBy' => 'language_id',
        ];
        $this->assertEquals($expectedFilters, $filters->all());
        $this->assertEquals('alternate_language', $filters->getUuid());
    }

    public function testOverrideWithController()
    {
        $expectedFilters = [
            'limit' => 10,
            'offset' => 10,
        ];
        $builder = new RepositoryFiltersBuilder($this->buildRepositoryByRouteMock($expectedFilters));
        $builder->setConfig([
            'controller' => 'language',
            'action' => 'index',
            'employee_id' => 1,
            'shop_id' => 1,
        ]);
        $filters = $builder->buildFilters();
        $this->assertNotNull($filters);
        $this->assertEquals($expectedFilters, $filters->all());
        $this->assertEmpty($filters->getUuid());
    }

    /**
     * @param array|null $filters
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|AdminFilterRepository
     */
    private function buildRepositoryByUuidMock(array $filters = null)
    {
        $repositoryMock = $this->getMockBuilder(AdminFilterRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        if (null !== $filters) {
            $adminFilterMock = $this->buildAdminFilterMock($filters);

            $repositoryMock
                ->expects($this->once())
                ->method('findByEmployeeAndUuid')
                ->willReturn($adminFilterMock)
            ;
        }

        return $repositoryMock;
    }

    /**
     * @param array|null $filters
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|AdminFilterRepository
     */
    private function buildRepositoryByRouteMock(array $filters = null)
    {
        $repositoryMock = $this->getMockBuilder(AdminFilterRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        if (null !== $filters) {
            $adminFilterMock = $this->buildAdminFilterMock($filters);

            $repositoryMock
                ->expects($this->once())
                ->method('findByEmployeeAndRouteParams')
                ->willReturn($adminFilterMock)
            ;
        }

        return $repositoryMock;
    }

    /**
     * @param array $filters
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|AdminFilter
     */
    private function buildAdminFilterMock(array $filters)
    {
        $adminFilterMock = $this->getMockBuilder(AdminFilter::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $adminFilterMock
            ->expects($this->once())
            ->method('getFilter')
            ->willReturn(json_encode($filters))
        ;

        return $adminFilterMock;
    }
}
