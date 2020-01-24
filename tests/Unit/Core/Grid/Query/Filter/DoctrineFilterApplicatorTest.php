<?php

namespace Tests\Unit\Core\Grid\Query\Filter;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\DoctrineFilterApplicator;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\SqlFilters;

class DoctrineFilterApplicatorTest extends TestCase
{
    /**
     * @var Connection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connectionMock;

    protected function setUp()
    {
        parent::setUp();

        $this->connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    public function testItCreatesFilterByMinMaxTypeWhenBothValuesAreAvailable()
    {
        $qb = new QueryBuilder($this->connectionMock);
        $filterName = 'id_product';

        $sqlFilters = (new SqlFilters())
            ->addFilter(
                $filterName,
                'p.`id_product`',
                SqlFilters::MIN_MAX
            )
        ;

        $filterApplicator = new DoctrineFilterApplicator();

        $filterValues = [
            $filterName => [
                'min_field' => 1,
                'max_field' => 5,
            ],
        ];

        $filterApplicator->apply($qb, $sqlFilters, $filterValues);

        $this->assertContains(
            'p.`id_product` >= :id_product_min AND p.`id_product` <= :id_product_max',
            $qb->getSQL()
        );
    }

    public function testItCreatesFilterByMinMaxTypeWhenMinValueIsAvailable()
    {
        $qb = new QueryBuilder($this->connectionMock);
        $filterName = 'id_product';

        $sqlFilters = (new SqlFilters())
            ->addFilter(
                $filterName,
                'p.`id_product`',
                SqlFilters::MIN_MAX
            )
        ;

        $filterApplicator = new DoctrineFilterApplicator();

        $filterValues = [
            $filterName => [
                'min_field' => 1,
            ],
        ];

        $filterApplicator->apply($qb, $sqlFilters, $filterValues);

        $this->assertContains(
            'p.`id_product` >= :id_product_min',
            $qb->getSQL()
        );
    }

    public function testItCreatesFilterByMinMaxTypeWhenMaxValueIsAvailable()
    {
        $qb = new QueryBuilder($this->connectionMock);
        $filterName = 'id_product';

        $sqlFilters = (new SqlFilters())
            ->addFilter(
                $filterName,
                'p.`id_product`',
                SqlFilters::MIN_MAX
            )
        ;

        $filterApplicator = new DoctrineFilterApplicator();

        $filterValues = [
            $filterName => [
                'max_field' => 1,
            ],
        ];

        $filterApplicator->apply($qb, $sqlFilters, $filterValues);

        $this->assertContains(
            'p.`id_product` <= :id_product_max',
            $qb->getSQL()
        );
    }

    public function testItCreatesFilterByMinMaxTypeWhenBothValuesAreEqual()
    {
        $qb = new QueryBuilder($this->connectionMock);
        $filterName = 'id_product';

        $sqlFilters = (new SqlFilters())
            ->addFilter(
                $filterName,
                'p.`id_product`',
                SqlFilters::MIN_MAX
            )
        ;

        $filterApplicator = new DoctrineFilterApplicator();

        $filterValues = [
            $filterName => [
                'min_field' => '1.5924',
                'max_field' => '1.5924',
            ],
        ];

        $filterApplicator->apply($qb, $sqlFilters, $filterValues);

        $this->assertContains(
            'p.`id_product` = :id_product',
            $qb->getSQL()
        );
    }
}
