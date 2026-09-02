<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Zone\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Zone\Command\ToggleZoneStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Zone\CommandHandler\ToggleZoneStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\CannotToggleZoneStatusException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use PrestaShopException;
use Zone;

#[AsCommandHandler]
final class ToggleZoneStatusHandler implements ToggleZoneStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws ZoneException
     */
    public function handle(ToggleZoneStatusCommand $command): void
    {
        try {
            $zone = new Zone($command->getZoneId()->getValue());

            if (0 >= $zone->id) {
                throw new ZoneNotFoundException(sprintf('Zone object with id "%d" has been not found for status changing', $command->getZoneId()->getValue()));
            }

            if (false === $zone->toggleStatus()) {
                throw new CannotToggleZoneStatusException(sprintf('Unable to toggle status of zone with id "%d"', $command->getZoneId()->getValue()));
            }
        } catch (PrestaShopException) {
            throw new ZoneException(sprintf('An error occurred when toggling status for zone with id "%d"', $command->getZoneId()->getValue()));
        }
    }
}
