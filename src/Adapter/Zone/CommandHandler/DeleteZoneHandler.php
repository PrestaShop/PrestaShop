<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Zone\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\DeleteZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\CommandHandler\DeleteZoneHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\DeleteZoneException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use Zone;

/**
 * Handles command that delete zone
 */
#[AsCommandHandler]
final class DeleteZoneHandler implements DeleteZoneHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(DeleteZoneCommand $command): void
    {
        $zone = new Zone($command->getZoneId()->getValue());

        if (0 >= $zone->id) {
            throw new ZoneNotFoundException(sprintf('Unable to find zone with id "%d" for deletion', $command->getZoneId()->getValue()));
        }

        if (!$zone->delete()) {
            throw new DeleteZoneException(sprintf('Cannot delete zone with id "%d"', $command->getZoneId()->getValue()), DeleteZoneException::FAILED_DELETE);
        }
    }
}
