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

namespace Tests\Unit\Core\Grid\DataProvider;

use Doctrine\DBAL\Query\QueryBuilder;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\DataProvider\DoctrineGridDataProvider;
use PrestaShop\PrestaShop\Core\Grid\DataProvider\GridDataInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineQueryBuilderInterface;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

class DoctrineGridDataProviderTest extends TestCase
{
    /**
     * @var DoctrineGridDataProvider
     */
    private $doctrineDataProvider;

    public function setUp()
    {
        $this->doctrineDataProvider = new DoctrineGridDataProvider($this->createDoctrineQueryBuilderMock());
    }

    public function testItProvidesGridData()
    {
        $criteria = $this->createMock(SearchCriteriaInterface::class);

        $data = $this->doctrineDataProvider->getData($criteria);

        $this->assertInstanceOf(GridDataInterface::class, $data);
        $this->assertInstanceOf(RecordCollectionInterface::class, $data->getRecords());

        $this->assertEquals(4, $data->getRecordsTotal());
        $this->assertCount(2, $data->getRecords());
        $this->assertEquals('SELECT * FROM ps_test', $data->getQuery());
    }

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
            ->willReturn('SELECT * FROM ps_test');

        $doctrineQueryBuilder = $this->createMock(DoctrineQueryBuilderInterface::class);
        $doctrineQueryBuilder->method('getSearchQueryBuilder')
            ->willReturn($qb);
        $doctrineQueryBuilder->method('getCountQueryBuilder')
            ->willReturn($qb);

        return $doctrineQueryBuilder;
    }
}
