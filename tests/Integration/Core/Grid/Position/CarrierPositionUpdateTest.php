<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Grid\Position;

use Db;
use PrestaShop\PrestaShop\Core\Grid\Position\GridPositionUpdaterInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdateFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class CarrierPositionUpdateTest extends KernelTestCase
{
    private const DELETED_CARRIER_ID = 9501;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::resetDatabase();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::resetDatabase();
    }

    protected static function resetDatabase(): void
    {
        DatabaseDump::restoreTables(['carrier']);
    }

    public function testDraggingACarrierOverADeletedOneKeepsTheDroppedOrder(): void
    {
        self::bootKernel();

        $visibleIds = $this->reindexCarriersLeavingRoomForADeletedOne();
        $storedPositions = $this->getVisibleCarrierPositions();

        /** @var PositionUpdateFactoryInterface $factory */
        $factory = self::getContainer()->get(PositionUpdateFactoryInterface::class);
        /** @var GridPositionUpdaterInterface $updater */
        $updater = self::getContainer()->get(GridPositionUpdaterInterface::class);
        /** @var PositionDefinition $positionDefinition */
        $positionDefinition = self::getContainer()->get('prestashop.core.grid.carrier.position_definition');

        // The last visible carrier is dropped in second place; like the grid, every row is posted with
        // its stored position and the stored position of the slot it lands in.
        $droppedOrder = $visibleIds;
        $lastVisibleId = array_pop($droppedOrder);
        array_splice($droppedOrder, 1, 0, [$lastVisibleId]);
        $slotPositions = array_values($storedPositions);
        $positions = [];
        foreach ($droppedOrder as $slot => $id) {
            $positions[] = [
                'rowId' => $id,
                'oldPosition' => $storedPositions[$id],
                'newPosition' => $slotPositions[$slot],
            ];
        }

        $updater->update($factory->buildPositionUpdate(['positions' => $positions], $positionDefinition));

        $positions = $this->getVisibleCarrierPositions();
        self::assertSame($droppedOrder, array_keys($positions));
        self::assertSame(range(0, count($visibleIds) - 1), array_values($positions));
    }

    /**
     * @return int[] the ids of the visible carriers, in position order
     */
    private function reindexCarriersLeavingRoomForADeletedOne(): array
    {
        $db = Db::getInstance();
        $carriers = $db->executeS(
            'SELECT id_carrier FROM ' . _DB_PREFIX_ . 'carrier WHERE deleted = 0 ORDER BY position ASC'
        );
        $visibleIds = array_map('intval', array_column($carriers, 'id_carrier'));
        self::assertGreaterThan(2, count($visibleIds), 'the fixture needs a few carriers to reorder');

        // Position 1 is taken by a deleted row, so the visible ones start out at 0, 2, 3, 4...
        $db->execute(sprintf(
            'INSERT INTO `%scarrier` (`id_carrier`, `id_reference`, `name`, `url`, `active`, `deleted`, `position`)
             VALUES (%d, %d, \'Superseded carrier\', \'\', 0, 1, 1)',
            _DB_PREFIX_,
            self::DELETED_CARRIER_ID,
            self::DELETED_CARRIER_ID
        ));

        $position = 0;
        foreach ($visibleIds as $id) {
            if (1 === $position) {
                ++$position;
            }
            $db->execute(sprintf(
                'UPDATE `%scarrier` SET `position` = %d WHERE `id_carrier` = %d',
                _DB_PREFIX_,
                $position,
                $id
            ));
            ++$position;
        }

        return $visibleIds;
    }

    /**
     * @return array<int, int> carrier id => position, in position order
     */
    private function getVisibleCarrierPositions(): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT id_carrier, position FROM ' . _DB_PREFIX_ . 'carrier WHERE deleted = 0 ORDER BY position ASC'
        );

        $positions = [];
        foreach ($rows as $row) {
            $positions[(int) $row['id_carrier']] = (int) $row['position'];
        }

        return $positions;
    }
}
