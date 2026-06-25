<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Country\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Country\Repository\CountryRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\ToggleCountryStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\CommandHandler\ToggleCountryStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CannotEditCountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CannotToggleCountryStatusException;

#[AsCommandHandler]
final class ToggleCountryStatusHandler implements ToggleCountryStatusHandlerInterface
{
    public function __construct(
        private readonly CountryRepository $countryRepository
    ) {
    }

    public function handle(ToggleCountryStatusCommand $command): void
    {
        $country = $this->countryRepository->get($command->getCountryId());
        $country->active = !$country->active;

        try {
            $this->countryRepository->update($country);
        } catch (CannotEditCountryException $e) {
            throw new CannotToggleCountryStatusException(
                sprintf('Failed to toggle status for country with id "%d"', $command->getCountryId()->getValue()),
                0,
                $e
            );
        }
    }
}
