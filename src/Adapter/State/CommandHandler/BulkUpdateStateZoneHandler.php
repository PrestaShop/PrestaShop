<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\State\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\State\Command\BulkUpdateStateZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\State\CommandHandler\BulkUpdateStateZoneHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\CannotUpdateStateException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use PrestaShopException;
use State;
use Zone;

/**
 * Handles command which updates zone for multiple states
 */
#[AsCommandHandler]
class BulkUpdateStateZoneHandler implements BulkUpdateStateZoneHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkUpdateStateZoneCommand $command): void
    {
        $zoneId = $command->getNewZoneId();
        $stateIds = $command->getStateIds();

        try {
            $zone = new Zone($zoneId);
            if (!$zone->id) {
                throw new ZoneNotFoundException(sprintf('Zone with id "%d" was not found.', $zoneId));
            }
        } catch (PrestaShopException $e) {
            throw new ZoneNotFoundException(sprintf('Zone with id "%d" was not found.', $zoneId));
        }

        try {
            $state = new State();
            $result = $state->affectZoneToSelection($stateIds, $zoneId);

            if (!$result) {
                throw new CannotUpdateStateException(
                    sprintf('Failed to update zone for states: %s', implode(', ', $stateIds))
                );
            }
        } catch (PrestaShopException $e) {
            throw new CannotUpdateStateException(
                sprintf('An error occurred when updating zone for states: %s', $e->getMessage()),
                0,
                $e
            );
        }
    }
}
