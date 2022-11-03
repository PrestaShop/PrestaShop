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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
