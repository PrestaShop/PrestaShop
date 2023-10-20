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

namespace Tests\Unit\Core\Grid\Column;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\Exception\ColumnNotFoundException;

class ColumnCollectionTest extends TestCase
{
    public function testItAddsColumnsToCollection(): ColumnCollection
    {
        $columns = (new ColumnCollection())
            ->add($this->createColumnMock('first'))
            ->add($this->createColumnMock('second'))
            ->add($this->createColumnMock('third'));

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
    public function testItAddsColumnsBeforeGivenColumn(ColumnCollection $columns): ColumnCollection
    {
        $columns
            ->addBefore('first', $this->createColumnMock('before_first'))
            ->addBefore('second', $this->createColumnMock('before_second'))
            ->addBefore('third', $this->createColumnMock('before_third'));

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
    public function testItAddsColumnsAfterGivenColumn(ColumnCollection $columns): ColumnCollection
    {
        $columns
            ->addAfter('first', $this->createColumnMock('after_first'))
            ->addAfter('second', $this->createColumnMock('after_second'))
            ->addAfter('third', $this->createColumnMock('after_third'));

        $this->assertEquals([
            'before_first',
            'first',
            'after_first',
            'before_second',
            'second',
            'after_second',
            'before_third',
            'third',
            'after_third',
        ], $this->getColumnPositions($columns));

        return $columns;
    }

    /**
     * @depends testItAddsColumnsAfterGivenColumn
     */
    public function testItRemovesColumnById(ColumnCollection $columns): void
    {
        $columns->remove('first');
        $columns->remove('third');
        $columns->remove('non_existing');

        $this->assertCount(7, $columns);
    }

    public function testMixingMethodsProducesCollectionWithCorrectColumnPositions(): void
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
            ->addAfter('second', $this->createColumnMock('after_second'));

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

    public function testNumericColumnIdAreAccepted(): void
    {
        $columns = (new ColumnCollection())
            ->add($this->createColumnMock(3))
            ->addAfter('3', $this->createColumnMock(1))
            ->addBefore('3', $this->createColumnMock(2))
            ->add($this->createColumnMock('second'))
            ->addAfter('second', $this->createColumnMock(9))
            ->addBefore('second', $this->createColumnMock('7'))
            ->add($this->createColumnMock(5));

        $this->assertEquals([
            2,
            3,
            1,
            '7',
            'second',
            9,
            5,
        ], $this->getColumnPositions($columns));
    }

    public function testItThrowsExceptionWhenAddingColumnAfterNonExistingColumn(): void
    {
        $this->expectException(ColumnNotFoundException::class);

        (new ColumnCollection())->addAfter('non_existing', $this->createColumnMock('first'));
    }

    public function testItThrowsExceptionWhenAddingColumnBeforeNonExistingColumn(): void
    {
        $this->expectException(ColumnNotFoundException::class);

        (new ColumnCollection())->addBefore('non_existing', $this->createColumnMock('first'));
    }

    public function testColumnsCanBeRetrievedAsArray(): void
    {
        $columns = (new ColumnCollection())
            ->add($this->createColumnMock('test_1'))
            ->add($this->createColumnMock('test_2'))
            ->add($this->createColumnMock('test_3'));

        $columnsArray = $columns->toArray();

        $this->assertIsArray($columnsArray);
        $this->assertCount(3, $columnsArray);
    }

    public function testAColumnCanBeMoved(): void
    {
        $columns = (new ColumnCollection())
            ->add($this->createColumnMock('test_1'))
            ->add($this->createColumnMock('test_2'))
            ->add($this->createColumnMock('test_3'))
            ->add($this->createColumnMock('test_4'))
            ->add($this->createColumnMock('test_5'))
            ->add($this->createColumnMock('test_6'))
            ->add($this->createColumnMock('test_7'))
        ;

        $columns
            ->move('test_1', 1)
            ->move('test_5', 0)
            ->move('test_2', 4)
        ;

        $this->assertValidColumnWithId($columns, 'test_5');
        $columns->next();
        $this->assertValidColumnWithId($columns, 'test_1');
        $columns->next();
        $columns->next();
        $columns->next();
        $this->assertValidColumnWithId($columns, 'test_2');
        $columns->next();
        $this->assertValidColumnWithId($columns, 'test_6');

        $this->assertCount(7, $columns);
    }

    public function testColumnMoveWithInvalidIdWillThrowsAnException(): void
    {
        $this->expectException(ColumnNotFoundException::class);

        $columns = (new ColumnCollection())
            ->add($this->createColumnMock('test_1'))
            ->add($this->createColumnMock('test_2'))
            ->add($this->createColumnMock('test_3'));

        $columns->move('undefined_id', 10);
    }

    /**
     * @param int|string $id
     *
     * @return ColumnInterface
     */
    private function createColumnMock($id): ColumnInterface
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
    private function getColumnPositions(ColumnCollection $columns): array
    {
        $positions = [];

        foreach ($columns as $column) {
            $positions[] = $column->getId();
        }

        return $positions;
    }

    /**
     * Helper assertion.
     *
     * @param ColumnCollection $columnCollection
     * @param string $columnId
     */
    private function assertValidColumnWithId(ColumnCollection $columnCollection, string $columnId): void
    {
        $this->assertSame($columnCollection->current()->getId(), $columnId);
    }
}
