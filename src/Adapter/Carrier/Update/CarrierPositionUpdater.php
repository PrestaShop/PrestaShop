<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier\Update;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CannotUpdateCarrierException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionDataException;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Grid\Position\GridPositionUpdaterInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdateFactoryInterface;

/**
 * Moves a carrier to a new position, and updates the position of the other carriers accordingly.
 *
 * The same services as the carriers list are used, so assigning a position through a command has the same effect as
 * dragging and dropping the carrier in the list: the carriers between the old and the new position are shifted, and no
 * two carriers end up sharing a position.
 */
class CarrierPositionUpdater
{
    public function __construct(
        private readonly PositionUpdateFactoryInterface $positionUpdateFactory,
        private readonly PositionDefinition $positionDefinition,
        private readonly GridPositionUpdaterInterface $positionUpdater,
    ) {
    }

    /**
     * @throws CannotUpdateCarrierException
     */
    public function updatePosition(CarrierId $carrierId, int $oldPosition, int $newPosition): void
    {
        if ($oldPosition === $newPosition) {
            return;
        }

        $positionsData = [
            'positions' => [
                [
                    'rowId' => $carrierId->getValue(),
                    'oldPosition' => $oldPosition,
                    'newPosition' => $newPosition,
                ],
            ],
        ];

        try {
            $positionUpdate = $this->positionUpdateFactory->buildPositionUpdate($positionsData, $this->positionDefinition);
            $this->positionUpdater->update($positionUpdate);
        } catch (PositionDataException|PositionUpdateException $e) {
            throw new CannotUpdateCarrierException(
                'Cannot update carrier position',
                CannotUpdateCarrierException::FAILED_UPDATE_CARRIER,
                $e
            );
        }
    }
}
