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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        //Most of the assertions are actually in the mock
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
            42
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
