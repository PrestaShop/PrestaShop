<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
            ->willReturnCallback(function (string $hookName, array $parameters) {
                $this->assertEquals('actionTestIdGridDefinitionModifier', $hookName);
                $this->assertArrayHasKey('definition', $parameters);
            });

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
