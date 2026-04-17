<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Zone\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\BulkToggleZoneStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\CommandHandler\BulkToggleZoneStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\CannotToggleZoneStatusException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use PrestaShopException;
use Zone;

/**
 * Handles command that toggles zones status in bulk action
 */
#[AsCommandHandler]
final class BulkToggleZoneStatusHandler implements BulkToggleZoneStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkToggleZoneStatusCommand $command): void
    {
        foreach ($command->getZoneIds() as $zoneId) {
            $zone = new Zone($zoneId->getValue());

            if (0 >= $zone->id) {
                throw new ZoneNotFoundException(sprintf('Zone object with id "%d" has been not found for status changing', $zoneId->getValue()));
            }

            $zone->active = $command->getExpectedStatus();

            try {
                if (!$zone->save()) {
                    throw new CannotToggleZoneStatusException(sprintf('Unable to toggle status for zone with id "%d"', $zoneId->getValue()));
                }
            } catch (PrestaShopException) {
                throw new ZoneException(sprintf('An error occurred while updating zone status with id "%d"', $zoneId->getValue()));
            }
        }
    }
}
