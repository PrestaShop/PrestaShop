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

namespace Tests\Unit\Core\Grid\Presenter;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\DataProvider\GridDataInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\DefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShop\PrestaShop\Core\Grid\Presenter\GridPresenter;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class GridPresenterTest extends TestCase
{
    /**
     * @var GridPresenter
     */
    private $gridPresenter;

    public function setUp()
    {
        $this->gridPresenter = new GridPresenter();
    }

    public function testGridInstanceIsPresentedAsArray()
    {
        $presentedGrid = $this->gridPresenter->present($this->createGridMock());

        $struct = [
            'id' => [],
            'name' => [],
            'filter_form' => [],
            'columns' => [],
            'actions' => ['grid', 'bulk'],
            'data' => ['records', 'records_total', 'query'],
            'pagination' => ['offset', 'limit'],
            'sorting' => ['order_by', 'order_way'],
            'filters' => [],
        ];

        $this->assertInternalType('array', $presentedGrid);

        foreach ($struct as $itemName => $innerStruct) {
            $this->assertArrayHasKey($itemName, $presentedGrid);

            foreach ($innerStruct as $innerItemName) {
                $this->assertArrayHasKey($innerItemName, $presentedGrid[$itemName]);
            }
        }
    }

    private function createGridMock()
    {
        $data = $this->createMock(GridDataInterface::class);
        $definition = $this->createMock(DefinitionInterface::class);
        $definition->method('getColumns')
            ->willReturn((new ColumnCollection())
                ->add($this->createColumnMock('test_1'))
                ->add($this->createColumnMock('test_2'))
                ->add($this->createColumnMock('test_3'))
            );
        $definition->method('getBulkActions')
            ->willReturn(new BulkActionCollection());
        $definition->method('getGridActions')
            ->willReturn(new GridActionCollection());
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
