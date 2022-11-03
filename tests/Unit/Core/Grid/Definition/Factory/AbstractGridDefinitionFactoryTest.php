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

namespace Tests\Unit\Core\Grid\Definition\Factory;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;

class AbstractGridDefinitionFactoryTest extends TestCase
{
    public function testItCreatesDefinitionAndDispatchesHookToAllowDefinitionModification()
    {
        $hookDispatcherMock = $this->createMock(HookDispatcherInterface::class);
        $hookDispatcherMock
            ->expects($this->once())
            ->method('dispatchWithParameters')
            ->withConsecutive(
                [$this->equalTo('actionTestIdGridDefinitionModifier')],
                [$this->isType('array'), $this->arrayHasKey('definition')]
            )
        ;

        $definitionFactory = $this->getMockForAbstractClass(AbstractGridDefinitionFactory::class, [$hookDispatcherMock]);

        $definitionFactory
            ->expects($this->once())
            ->method('getName')
            ->willReturn('Test name');
        $definitionFactory
            ->expects($this->once())
            ->method('getId')
            ->willReturn('test_id');
        $definitionFactory
            ->expects($this->once())
            ->method('getColumns')
            ->willReturn($this->getColumns());

        $definition = $definitionFactory->getDefinition();

        $this->assertInstanceOf(GridDefinitionInterface::class, $definition);
        $this->assertInstanceOf(BulkActionCollectionInterface::class, $definition->getBulkActions());
        $this->assertInstanceOf(GridActionCollectionInterface::class, $definition->getGridActions());

        $this->assertEquals($definition->getId(), 'test_id');
        $this->assertEquals($definition->getName(), 'Test name');
        $this->assertCount(3, $definition->getColumns());
        $this->assertCount(0, $definition->getGridActions());
        $this->assertCount(0, $definition->getBulkActions());
        $this->assertCount(0, $definition->getFilters()->all());
    }

    private function getColumns()
    {
        return (new ColumnCollection())
            ->add($this->createColumnMock('test_1'))
            ->add($this->createColumnMock('test_2'))
            ->add($this->createColumnMock('test_3'));
    }

    private function createColumnMock($id)
    {
        $column = $this->createMock(ColumnInterface::class);
        $column->method('getId')
            ->willReturn($id);

        return $column;
    }
}
