<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\Factory\DoctrineGridDataFactory;
use PrestaShop\PrestaShop\Core\Grid\Data\GridDataInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineQueryBuilderInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\QueryParserInterface;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PDOStatement;

class DoctrineGridDataFactoryTest extends TestCase
{
    public function testItProvidesGridData()
    {
        $hookDispatcher = $this->createHookDispatcherMock();
        $hookDispatcher->expects($this->once())
            ->method('dispatchWithParameters')
        ;

        $queryParser = $this->createQueryParserMock();

        $doctrineGridDataFactory = new DoctrineGridDataFactory(
            $this->createDoctrineQueryBuilderMock(),
            $hookDispatcher,
            $queryParser,
            'test_grid_id'
        );

        $criteria = $this->createMock(SearchCriteriaInterface::class);

        $data = $doctrineGridDataFactory->getData($criteria);

        $this->assertInstanceOf(GridDataInterface::class, $data);
        $this->assertInstanceOf(RecordCollectionInterface::class, $data->getRecords());


        $this->assertEquals(4, $data->getRecordsTotal());
        $this->assertCount(2, $data->getRecords());
        $this->assertEquals('SELECT * FROM ps_test WHERE id = 1', $data->getQuery());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createDoctrineQueryBuilderMock()
    {
        $statement = $this->createMock(PDOStatement::class);
        $statement->method('fetchAll')
            ->willReturn([
                [
                    'id' => 1,
                    'name' => 'Test name 1',
                ],
                [
                    'id' => 2,
                    'name' => 'Test name 2',
                ],
            ]);
        $statement->method('fetch')
            ->willReturn(4);

        $qb = $this->createMock(QueryBuilder::class);
        $qb->method('execute')
            ->willReturn($statement);
        $qb->method('getSQL')
            ->willReturn('SELECT * FROM ps_test WHERE id = :id');
        $qb->method('getParameters')
            ->willReturn([
                'id' => 1,
            ])
        ;

        $doctrineQueryBuilder = $this->createMock(DoctrineQueryBuilderInterface::class);
        $doctrineQueryBuilder->method('getSearchQueryBuilder')
            ->willReturn($qb);
        $doctrineQueryBuilder->method('getCountQueryBuilder')
            ->willReturn($qb);

        return $doctrineQueryBuilder;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createHookDispatcherMock()
    {
        $hookDispatcher = $this->createMock(HookDispatcherInterface::class);
        $hookDispatcher->method('dispatchWithParameters')
            ->willReturn(null);

        return $hookDispatcher;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createQueryParserMock()
    {
        $queryParser = $this->getMockBuilder(QueryParserInterface::class)
            ->setMethods(['parse'])
            ->getMockForAbstractClass();

        $queryParser->method('parse')->willReturn('SELECT * FROM ps_test WHERE id = 1');

        return $queryParser;
    }
}
