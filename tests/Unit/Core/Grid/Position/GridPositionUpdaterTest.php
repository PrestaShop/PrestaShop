<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Position;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionException;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Grid\Position\GridPositionUpdater;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionModification;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionModificationCollection;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdate;
use PrestaShop\PrestaShop\Core\Grid\Position\UpdateHandler\PositionUpdateHandlerInterface;

class GridPositionUpdaterTest extends TestCase
{
    public function testUpdate()
    {
        $positionUpdate = $this->createPositionUpdate();
        // Most of the assertions are actually in the mock
        $updateHandler = $this->createUpdateHandlerMockWithAssertions();
        $gridUpdater = new GridPositionUpdater($updateHandler);

        $this->assertNull($gridUpdater->update($positionUpdate));
    }

    public function testUpdateException()
    {
        $positionUpdate = $this->createPositionUpdate();
        $updateHandler = $this->createUpdateHandlerMockThrowingException();
        $gridUpdater = new GridPositionUpdater($updateHandler);

        $caughtException = null;

        try {
            $gridUpdater->update($positionUpdate);
        } catch (PositionException $e) {
            $caughtException = $e;
        }

        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(PositionUpdateException::class, $caughtException);
        $this->assertEquals('Could not update #%i', $caughtException->getKey());
        $this->assertEquals('Admin.Catalog.Notification', $caughtException->getDomain());
        $this->assertSame([5], $caughtException->getParameters());
    }

    /**
     * @return PositionUpdate
     */
    private function createPositionUpdate()
    {
        $collection = new PositionModificationCollection();
        $collection->add(new PositionModification(1, 0, 0));
        $collection->add(new PositionModification(5, 1, 2));
        $collection->add(new PositionModification(42, 2, 1));

        $positionUpdate = new PositionUpdate(
            $collection,
            $this->getDefinition(),
            '42'
        );

        return $positionUpdate;
    }

    /**
     * @return MockObject|PositionUpdateHandlerInterface
     */
    private function createUpdateHandlerMockWithAssertions()
    {
        $updaterMock = $this->createMock(PositionUpdateHandlerInterface::class);
        $updaterMock
            ->method('getCurrentPositions')
            ->with(
                $this->isInstanceOf(PositionDefinitionInterface::class),
                $this->equalTo(42)
            )
            ->willReturn([
                1 => 0,
                5 => 1,
                42 => 2,
            ]);

        $updaterMock
            ->method('updatePositions')
            ->with(
                $this->isInstanceOf(PositionDefinitionInterface::class),
                $this->equalTo([
                    1 => 0,
                    5 => 2,
                    42 => 1,
                ])
            );

        return $updaterMock;
    }

    private function createUpdateHandlerMockThrowingException()
    {
        $updaterMock = $this->createMock(PositionUpdateHandlerInterface::class);
        $updaterMock
            ->method('getCurrentPositions')
            ->with(
                $this->isInstanceOf(PositionDefinitionInterface::class),
                $this->equalTo(42)
            )
            ->willReturn([
                1 => 0,
                5 => 1,
                42 => 2,
            ]);

        $updaterMock
            ->method('updatePositions')
            ->with(
                $this->isInstanceOf(PositionDefinitionInterface::class),
                $this->equalTo([
                    1 => 0,
                    5 => 2,
                    42 => 1,
                ])
            )
            ->willThrowException(new PositionUpdateException(
                'Could not update #%i',
                'Admin.Catalog.Notification',
                [5]
            ));

        return $updaterMock;
    }

    /**
     * @return PositionDefinition
     */
    private function getDefinition()
    {
        return new PositionDefinition(
            'product',
            'id_product',
            'position',
            'id_category'
        );
    }
}
