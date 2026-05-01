<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Country\CommandHandler;

use Country;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\BulkUpdateCountryZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\CommandHandler\BulkUpdateCountryZoneHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CannotEditCountryException;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use PrestaShopException;
use Zone;

#[AsCommandHandler]
class BulkUpdateCountryZoneHandler implements BulkUpdateCountryZoneHandlerInterface
{
    public function handle(BulkUpdateCountryZoneCommand $command): void
    {
        $zoneId = $command->getNewZoneId();
        $countryIds = $command->getCountryIds();

        try {
            $zone = new Zone($zoneId);
            if (!$zone->id) {
                throw new ZoneNotFoundException(sprintf('Zone with id "%d" was not found.', $zoneId));
            }
        } catch (PrestaShopException $e) {
            throw new ZoneNotFoundException(sprintf('Zone with id "%d" was not found.', $zoneId));
        }

        try {
            $country = new Country();
            $result = $country->affectZoneToSelection($countryIds, $zoneId);

            if (!$result) {
                throw new CannotEditCountryException(
                    sprintf('Failed to update zone for countries: %s', implode(', ', $countryIds)),
                    CannotEditCountryException::FAILED_TO_UPDATE_COUNTRY
                );
            }
        } catch (PrestaShopException $e) {
            throw new CannotEditCountryException(
                sprintf('An error occurred when updating zone for countries: %s', $e->getMessage()),
                CannotEditCountryException::UNKNOWN_EXCEPTION,
                $e
            );
        }
    }
}
