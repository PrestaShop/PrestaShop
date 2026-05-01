<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Country\CommandHandler;

use Country;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\ToggleCountryStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\CommandHandler\ToggleCountryStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CannotToggleCountryStatusException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShopException;

#[AsCommandHandler]
class ToggleCountryStatusHandler implements ToggleCountryStatusHandlerInterface
{
    public function handle(ToggleCountryStatusCommand $command): void
    {
        try {
            $country = new Country($command->getCountryId()->getValue());

            if (0 >= $country->id) {
                throw new CountryNotFoundException(sprintf('Country object with id "%d" has not been found for status changing', $command->getCountryId()->getValue()));
            }

            if (false === $country->toggleStatus()) {
                throw new CannotToggleCountryStatusException(sprintf('Unable to toggle status of country with id "%d"', $command->getCountryId()->getValue()));
            }
        } catch (PrestaShopException $e) {
            throw new CountryException(
                sprintf('An error occurred when toggling status for country with id "%d"', $command->getCountryId()->getValue()),
                0,
                $e
            );
        }
    }
}
