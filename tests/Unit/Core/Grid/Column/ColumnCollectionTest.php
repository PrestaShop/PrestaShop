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

namespace Tests\Unit\Core\Grid\Column;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\Exception\ColumnNotFoundException;

class ColumnCollectionTest extends TestCase
{
    public function testItAddsColumnsToCollection()
    {
        $columns = (new ColumnCollection())
            ->add($this->createColumnMock('first'))
            ->add($this->createColumnMock('second'))
            ->add($this->createColumnMock('third'))
        ;

        $this->assertEquals([
            'first',
            'second',
            'third',
        ], $this->getColumnPositions($columns));

        return $columns;
    }

    /**
     * @depends testItAddsColumnsToCollection
     */
    public function testItAddsColumnsBeforeGivenColumn(ColumnCollection $columns)
    {
        $columns
            ->addBefore('first', $this->createColumnMock('before_first'))
            ->addBefore('second', $this->createColumnMock('before_second'))
            ->addBefore('third', $this->createColumnMock('before_third'))
        ;

        $this->assertEquals([
            'before_first',
            'first',
            'before_second',
            'second',
            'before_third',
            'third',
        ], $this->getColumnPositions($columns));

        return $columns;
    }

    /**
     * @depends testItAddsColumnsBeforeGivenColumn
     */
    public function testItAddsColumnsAfterGivenColumn(ColumnCollection $columns)
    {
        $columns
            ->addAfter('first', $this->createColumnMock('after_first'))
            ->addAfter('second', $this->createColumnMock('after_second'))
            ->addAfter('third', $this->createColumnMock('after_third'))
        ;

        $this->assertEquals([
            'before_first',
            'first',
            'after_first',
            'before_second',
            'second',
            'after_second',
            'before_third',
            'third',
            'after_third'
        ], $this->getColumnPositions($columns));

        return $columns;
    }

    /**
     * @depends testItAddsColumnsAfterGivenColumn
     */
    public function testItRemovesColumnById(ColumnCollection $columns)
    {
        $columns->remove('first');
        $columns->remove('third');
        $columns->remove('non_existing');

        $this->assertCount(7, $columns);
    }

    public function testMixingMethodsProducesCollectionWithCorrectColumnPositions()
    {
        $columns = (new ColumnCollection())
            ->add($this->createColumnMock('first'))
            ->addAfter('first', $this->createColumnMock('to_be_removed'))
            ->addBefore('first', $this->createColumnMock('before_first'))
            ->addBefore('first', $this->createColumnMock('before_first_2'))
            ->addAfter('first', $this->createColumnMock('after_first'))
            ->addBefore('first', $this->createColumnMock('before_first_3'))
            ->remove('non_existing')
            ->addAfter('after_first', $this->createColumnMock('after_first_2'))
            ->add($this->createColumnMock('second'))
            ->remove('to_be_removed')
            ->add($this->createColumnMock('third'))
            ->addAfter('second', $this->createColumnMock('after_second'))
        ;

        $this->assertEquals([
            'before_first',
            'before_first_2',
            'before_first_3',
            'first',
            'after_first',
            'after_first_2',
            'second',
            'after_second',
            'third',
        ], $this->getColumnPositions($columns));
    }

    public function testItThrowsExceptionWhenAddingColumnAfterNonExistingColumn()
    {
        $this->expectException(ColumnNotFoundException::class);

        (new ColumnCollection())->addAfter('non_existing', $this->createColumnMock('first'));
    }

    public function testItThrowsExceptionWhenAddingColumnBeforeNonExistingColumn()
    {
        $this->expectException(ColumnNotFoundException::class);

        (new ColumnCollection())->addBefore('non_existing', $this->createColumnMock('first'));
    }

    /**
     * @param string $id
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|ColumnInterface
     */
    private function createColumnMock($id)
    {
        $column = $this->createMock(ColumnInterface::class);
        $column->method('getId')
            ->willReturn($id);

        return $column;
    }

    /**
     * @param ColumnCollection $columns
     *
     * @return string[]
     */
    private function getColumnPositions(ColumnCollection $columns)
    {
        $positions= [];

        foreach ($columns as $column) {
            $positions[] = $column->getId();
        }

        return $positions;
    }
}
