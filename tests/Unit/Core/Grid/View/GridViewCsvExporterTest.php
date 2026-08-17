<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\View;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\GridView\Exception\GridViewException;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryProvider;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\View\DynamicDateRangeComputer;
use PrestaShop\PrestaShop\Core\Grid\View\DynamicDateRuleApplier;
use PrestaShop\PrestaShop\Core\Grid\View\GridViewCsvExporter;
use PrestaShop\PrestaShop\Core\Grid\View\GridViewSearchCriteria;
use PrestaShop\PrestaShop\Core\Grid\View\GridViewSearchCriteriaFactory;
use PrestaShopBundle\Entity\AdminGridConfiguration;
use PrestaShopBundle\Entity\AdminGridView;
use Psr\Container\ContainerInterface;

class GridViewCsvExporterTest extends TestCase
{
    public function testItExportsGridColumnsWithSanitizedValues(): void
    {
        $records = [
            ['id_customer' => '1', 'email' => 'john@example.com', 'note' => '=HYPERLINK("evil")', 'passwd' => 'hash', 'total' => '-5'],
            ['id_customer' => '2', 'email' => '+not-a-number', 'note' => null, 'passwd' => 'hash2', 'total' => '12.5'],
        ];
        $columns = [
            ['id' => 'id_customer', 'name' => 'ID'],
            ['id' => 'email', 'name' => 'Email'],
            ['id' => 'note', 'name' => 'Note'],
            ['id' => 'passwd', 'name' => 'Password'],
            ['id' => 'actions', 'name' => 'Actions'],
        ];

        $criteriaHolder = new CriteriaHolder();
        $exporter = $this->buildExporter($records, $columns, $criteriaHolder);
        $exportedData = $exporter->export($this->buildGridView());

        $this->assertSame(
            ['id_customer' => 'ID', 'email' => 'Email', 'note' => 'Note'],
            $exportedData['headers']
        );
        $this->assertSame(
            [
                ['id_customer' => '1', 'email' => 'john@example.com', 'note' => "'=HYPERLINK(\"evil\")"],
                ['id_customer' => '2', 'email' => "'+not-a-number", 'note' => ''],
            ],
            $exportedData['rows_provider'](0, GridViewCsvExporter::CHUNK_SIZE)
        );

        $this->assertInstanceOf(GridViewSearchCriteria::class, $criteriaHolder->criteria);
        $this->assertSame(GridViewCsvExporter::CHUNK_SIZE, $criteriaHolder->criteria->getLimit());
        $this->assertNull($criteriaHolder->criteria->getOffset());
        $this->assertSame(1, $criteriaHolder->criteria->getShopConstraint()->getShopId()->getValue());
        $this->assertSame('customer', $criteriaHolder->criteria->getFilterId());
        $this->assertSame(['email' => 'john'], $criteriaHolder->criteria->getFilters());
    }

    public function testItThrowsWhenNoFactoryIsRegisteredForTheGrid(): void
    {
        $locator = $this->createMock(ContainerInterface::class);
        $locator->method('has')->willReturn(false);

        $exporter = new GridViewCsvExporter(
            new GridFactoryProvider($locator),
            new GridViewSearchCriteriaFactory(new DynamicDateRuleApplier(new DynamicDateRangeComputer()))
        );

        $this->expectException(GridViewException::class);
        $this->expectExceptionCode(GridViewException::UNSUPPORTED_GRID);

        $exporter->export($this->buildGridView());
    }

    private function buildExporter(array $records, array $columns, CriteriaHolder $criteriaHolder): GridViewCsvExporter
    {
        $columnMocks = [];
        foreach ($columns as $column) {
            $columnMock = $this->createMock(ColumnInterface::class);
            $columnMock->method('getId')->willReturn($column['id']);
            $columnMock->method('getName')->willReturn($column['name']);
            $columnMocks[] = $columnMock;
        }

        $definition = $this->createMock(GridDefinitionInterface::class);
        $definition->method('getColumns')->willReturn(new ArrayIterator($columnMocks));

        $grid = $this->createMock(GridInterface::class);
        $grid->method('getDefinition')->willReturn($definition);
        $grid->method('getData')->willReturn(new GridData(new RecordCollection($records), count($records)));

        $factory = new class($grid, $criteriaHolder) implements GridFactoryInterface {
            public function __construct(
                private readonly GridInterface $grid,
                private readonly CriteriaHolder $criteriaHolder,
            ) {
            }

            public function getGrid(SearchCriteriaInterface $searchCriteria): GridInterface
            {
                $this->criteriaHolder->criteria = $searchCriteria;

                return $this->grid;
            }
        };

        $locator = $this->createMock(ContainerInterface::class);
        $locator->method('has')->with('customer')->willReturn(true);
        $locator->method('get')->with('customer')->willReturn($factory);

        return new GridViewCsvExporter(
            new GridFactoryProvider($locator),
            new GridViewSearchCriteriaFactory(new DynamicDateRuleApplier(new DynamicDateRangeComputer()))
        );
    }

    private function buildGridView(): AdminGridView
    {
        $configuration = new AdminGridConfiguration();
        $configuration
            ->setEmployeeId(1)
            ->setShopId(1)
            ->setGridId('customer')
            ->setFilterId('customer')
            ->setControllerRoute('admin_customers_index')
        ;

        $gridView = new AdminGridView();
        $gridView
            ->setGridConfiguration($configuration)
            ->setName('My view')
            ->setFilterId('customer')
            ->setFilters((string) json_encode(['limit' => 50, 'filters' => ['email' => 'john']]))
        ;

        return $gridView;
    }
}

class CriteriaHolder
{
    public ?SearchCriteriaInterface $criteria = null;
}
