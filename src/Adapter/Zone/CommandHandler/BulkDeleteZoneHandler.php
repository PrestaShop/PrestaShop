<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Zone\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\BulkDeleteZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\CommandHandler\BulkDeleteZoneHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\DeleteZoneException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use Zone;

/**
 * Handles command that bulk delete zones
 */
#[AsCommandHandler]
final class BulkDeleteZoneHandler implements BulkDeleteZoneHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteZoneCommand $command): void
    {
        foreach ($command->getZoneIds() as $zoneId) {
            $zone = new Zone($zoneId->getValue());

            if (0 >= $zone->id) {
                throw new ZoneNotFoundException(sprintf('Unable to find zone with id "%d" for deletion', $zoneId->getValue()));
            }

            if (!$zone->delete()) {
                throw new DeleteZoneException(sprintf('An error occurred when deleting zone with id "%d"', $zoneId->getValue()), DeleteZoneException::FAILED_BULK_DELETE);
            }
        }
    }
}
