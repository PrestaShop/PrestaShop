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

namespace Tests\Unit\Core\Grid\Position;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Position\GridPositionUpdater;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionModification;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionModificationCollection;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdate;
use PrestaShop\PrestaShop\Core\Grid\Position\UpdateHandler\PositionUpdateHandlerInterface;

class GridPositionUpdaterTest extends TestCase
{
    public function testUpdate()
    {
        $positionUpdate = $this->createPositionUpdate();
        $updateHandler = $this->createUpdateHandlerMock();
        $gridUpdater = new GridPositionUpdater($updateHandler);

        $gridUpdater->update($positionUpdate);
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
            $this->getDefinition()
        );

        return $positionUpdate;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PositionUpdateHandlerInterface
     */
    private function createUpdateHandlerMock()
    {
        $updaterMock = $this->createMock(PositionUpdateHandlerInterface::class);
        $updaterMock
            ->method('getCurrentPositions')
            ->willReturn([
                1 => 0,
                5 => 1,
                42 => 2,
            ]);

        $updaterMock
            ->method('updatePositions')
            ->with(
                $this->anything(),
                $this->equalTo([
                    1 => 0,
                    5 => 2,
                    42 => 1,
                ])
            );

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
            'position'
        );
    }
}
