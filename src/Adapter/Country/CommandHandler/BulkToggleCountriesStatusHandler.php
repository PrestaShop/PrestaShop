<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Country\CommandHandler;

use Country;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\BulkToggleCountriesStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\CommandHandler\BulkToggleCountriesStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CannotToggleCountryStatusException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShopException;

#[AsCommandHandler]
class BulkToggleCountriesStatusHandler implements BulkToggleCountriesStatusHandlerInterface
{
    public function handle(BulkToggleCountriesStatusCommand $command): void
    {
        foreach ($command->getCountryIds() as $countryId) {
            $country = new Country($countryId->getValue());

            if (0 >= $country->id) {
                throw new CountryNotFoundException(sprintf('Country object with id "%d" has not been found for status changing', $countryId->getValue()));
            }

            $country->active = $command->getExpectedStatus();

            try {
                if (!$country->save()) {
                    throw new CannotToggleCountryStatusException(sprintf('Unable to toggle status for country with id "%d"', $countryId->getValue()));
                }
            } catch (PrestaShopException $e) {
                throw new CountryException(
                    sprintf('An error occurred while updating country status with id "%d"', $countryId->getValue()),
                    0,
                    $e
                );
            }
        }
    }
}
