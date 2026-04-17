<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Presenter;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\ViewOptionsCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridDataInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShop\PrestaShop\Core\Grid\Presenter\GridPresenter;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class GridPresenterTest extends TestCase
{
    /**
     * @var GridPresenter
     */
    private $gridPresenter;

    protected function setUp(): void
    {
        $hookDispatcherMock = $this->createMock(HookDispatcherInterface::class);
        $this->gridPresenter = new GridPresenter($hookDispatcherMock);
    }

    public function testGridInstanceIsPresentedAsArray()
    {
        $presentedGrid = $this->gridPresenter->present($this->createGridMock());

        $expectedPresentedGrid = [
            'id' => [],
            'name' => [],
            'filter_form' => [],
            'columns' => [],
            'column_filters' => [],
            'actions' => ['grid', 'bulk'],
            'data' => ['records', 'records_total', 'query'],
            'pagination' => ['offset', 'limit'],
            'sorting' => ['order_by', 'order_way'],
            'filters' => [],
            'view_options' => [],
        ];

        $this->assertIsArray($presentedGrid);

        foreach ($expectedPresentedGrid as $itemName => $innerStruct) {
            $this->assertArrayHasKey($itemName, $presentedGrid);

            foreach ($innerStruct as $innerItemName) {
                $this->assertArrayHasKey($innerItemName, $presentedGrid[$itemName]);
            }
        }
    }

    private function createGridMock()
    {
        $data = $this->createMock(GridDataInterface::class);
        $data->method('getRecords')
            ->willReturn(new RecordCollection([]))
        ;
        $data->method('getRecordsTotal')
            ->willReturn(0)
        ;

        $definition = $this->createMock(GridDefinitionInterface::class);
        $definition->method('getColumns')
            ->willReturn(
                (new ColumnCollection())
                    ->add($this->createColumnMock('test_1'))
                    ->add($this->createColumnMock('test_2'))
                    ->add($this->createColumnMock('test_3'))
            );
        $definition->method('getBulkActions')
            ->willReturn(new BulkActionCollection());
        $definition->method('getGridActions')
            ->willReturn(new GridActionCollection());
        $definition->method('getViewOptions')
            ->willReturn(new ViewOptionsCollection());
        $definition->method('getFilters')
            ->willReturn(new FilterCollection());
        $definition->method('getId')
            ->willReturn('');

        $criteria = $this->createMock(SearchCriteriaInterface::class);

        $filterForm = $this->createMock(FormInterface::class);
        $filterForm->method('createView')
            ->willReturn(new FormView());

        $grid = $this->createMock(GridInterface::class);
        $grid->method('getData')
            ->willReturn($data);
        $grid->method('getDefinition')
            ->willReturn($definition);
        $grid->method('getSearchCriteria')
            ->willReturn($criteria);
        $grid->method('getFilterForm')
            ->willReturn($filterForm);

        return $grid;
    }

    private function createColumnMock($id)
    {
        $column = $this->createMock(ColumnInterface::class);
        $column->method('getId')
            ->willReturn($id);

        return $column;
    }
}
