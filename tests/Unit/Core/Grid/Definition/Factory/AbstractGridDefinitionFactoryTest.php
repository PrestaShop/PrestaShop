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

namespace Tests\Unit\Core\Grid\Definition\Factory;

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
            ->expects($this->once())
            ->method('getName')
            ->willReturn('Test name')
        ;
        $definitionFactory
            ->expects($this->once())
            ->method('getId')
            ->willReturn('test_id')
        ;
        $definitionFactory
            ->expects($this->once())
            ->method('getColumns')
            ->willReturn($this->getColumns())
        ;

        $this->definitionFactory = $definitionFactory;
    }

    public function testItCreatesDefinitionAndDispatchesHookToAllowDefinitionModification()
    {
        $hookDispatcherMock = $this->createMock(HookDispatcherInterface::class);
        $hookDispatcherMock
            ->expects($this->once())
            ->method('dispatchWithParameters')
            ->withConsecutive(
                [$this->equalTo('actiontest_idGridDefinitionModifier')],
                [$this->isType('array'), $this->arrayHasKey('definition')]
            )
        ;

        $this->definitionFactory->setHookDispatcher($hookDispatcherMock);

        $definition = $this->definitionFactory->getDefinition();

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
            ->add($this->createColumnMock('test_3'))
        ;
    }

    private function createColumnMock($id)
    {
        $column = $this->createMock(ColumnInterface::class);
        $column->method('getId')
            ->willReturn($id);

        return $column;
    }
}
