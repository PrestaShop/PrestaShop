<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Country\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Country\Repository\CountryRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\BulkToggleCountriesStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\CommandHandler\BulkToggleCountriesStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\BulkCountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;

#[AsCommandHandler]
final class BulkToggleCountriesStatusHandler extends AbstractBulkCommandHandler implements BulkToggleCountriesStatusHandlerInterface
{
    public function __construct(
        private readonly CountryRepository $countryRepository,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkToggleCountriesStatusCommand $command): void
    {
        $this->handleBulkAction($command->getCountryIds(), CountryException::class, $command);
    }

    /**
     * @param CountryId $id
     * @param BulkToggleCountriesStatusCommand $command
     */
    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        $country = $this->countryRepository->get($id);
        $country->active = $command->getExpectedStatus();

        $this->countryRepository->update($country);
    }

    /**
     * {@inheritDoc}
     */
    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkCountryException(
            $caughtExceptions,
            'Errors occurred during country bulk change status action',
            BulkCountryException::FAILED_BULK_UPDATE_STATUS
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function supports($id): bool
    {
        return $id instanceof CountryId;
    }
}
