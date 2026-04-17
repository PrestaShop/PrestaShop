<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Query\Filter;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\DoctrineFilterApplicator;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\SqlFilters;

class DoctrineFilterApplicatorTest extends TestCase
{
    /**
     * @var DoctrineFilterApplicator
     */
    private $doctrineFilterApplicator;

    public function setUp(): void
    {
        $this->doctrineFilterApplicator = new DoctrineFilterApplicator();
    }

    /** Tests min max price filter when both min and max are used */
    public function testMinMaxFilterBoth(): void
    {
        $priceMinMaxFilters = new SqlFilters();
        $priceMinMaxFilters->addFilter(
            'price_tax_excluded',
            'ps.`price`',
            5
        );

        $filterValues = [
            'price_tax_excluded' => [
                'min_field' => '10',
                'max_field' => '20',
            ],
        ];

        $queryBuilder = new QueryBuilder($this->createMock(Connection::class));

        $this->doctrineFilterApplicator->apply($queryBuilder, $priceMinMaxFilters, $filterValues);
        $wherePart = $queryBuilder->getQueryPart('where');
        self::assertSame('ps.`price` >= :price_tax_excluded_min AND ps.`price` <= :price_tax_excluded_max', (string) $wherePart);
    }

    /** Tests min max price filter when only min is present */
    public function testMinMaxPriceFilterMin(): void
    {
        $priceMinMaxFilters = new SqlFilters();
        $priceMinMaxFilters->addFilter(
            'price_tax_excluded',
            'ps.`price`',
            5
        );
        $filterValues = [
            'price_tax_excluded' => [
                'min_field' => '10',
            ],
        ];

        $queryBuilder = new QueryBuilder($this->createMock(Connection::class));

        $this->doctrineFilterApplicator->apply($queryBuilder, $priceMinMaxFilters, $filterValues);
        $wherePart = $queryBuilder->getQueryPart('where');
        self::assertSame('ps.`price` >= :price_tax_excluded_min', (string) $wherePart);
    }

    /** Tests min max price filter when only max is used */
    public function testMinMaxPriceFilterMax(): void
    {
        $priceMinMaxFilters = new SqlFilters();
        $priceMinMaxFilters->addFilter(
            'price_tax_excluded',
            'ps.`price`',
            5
        );

        $filterValues = [
            'price_tax_excluded' => [
                'max_field' => '10',
            ],
        ];

        $queryBuilder = new QueryBuilder($this->createMock(Connection::class));

        $this->doctrineFilterApplicator->apply($queryBuilder, $priceMinMaxFilters, $filterValues);
        $wherePart = $queryBuilder->getQueryPart('where');
        self::assertSame('ps.`price` <= :price_tax_excluded_max', (string) $wherePart);
    }
}
