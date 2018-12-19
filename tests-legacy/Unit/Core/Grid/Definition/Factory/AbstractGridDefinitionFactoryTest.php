<?php
/**
 * 2007-2018 PrestaShop.
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

namespace LegacyTests\Unit\Core\Grid\Definition\Factory;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;

class AbstractGridDefinitionFactoryTest extends TestCase
{
    /**
     * @var AbstractGridDefinitionFactory
     */
    private $definitionFactory;

    public function setUp()
    {
        $definitionFactory = $this->getMockForAbstractClass(AbstractGridDefinitionFactory::class);

        $definitionFactory
            ->expects(static::once())
            ->method('getName')
            ->willReturn('Test name');
        $definitionFactory
            ->expects(static::once())
            ->method('getId')
            ->willReturn('test_id');
        $definitionFactory
            ->expects(static::once())
            ->method('getColumns')
            ->willReturn($this->getColumns());

        $this->definitionFactory = $definitionFactory;
    }

    public function testItCreatesDefinitionAndDispatchesHookToAllowDefinitionModification()
    {
        $hookDispatcherMock = $this->createMock(HookDispatcherInterface::class);
        $hookDispatcherMock
            ->expects(static::once())
            ->method('dispatchWithParameters')
            ->withConsecutive(
                [static::equalTo('actionTestIdGridDefinitionModifier')],
                [static::isType('array'), static::arrayHasKey('definition')]
            );

        $this->definitionFactory->setHookDispatcher($hookDispatcherMock);

        $definition = $this->definitionFactory->getDefinition();

        static::assertInstanceOf(GridDefinitionInterface::class, $definition);
        static::assertInstanceOf(BulkActionCollectionInterface::class, $definition->getBulkActions());
        static::assertInstanceOf(GridActionCollectionInterface::class, $definition->getGridActions());

        static::assertEquals($definition->getId(), 'test_id');
        static::assertEquals($definition->getName(), 'Test name');
        static::assertCount(3, $definition->getColumns());
        static::assertCount(0, $definition->getGridActions());
        static::assertCount(0, $definition->getBulkActions());
        static::assertCount(0, $definition->getFilters()->all());
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
