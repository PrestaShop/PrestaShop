<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Country\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Country\Repository\CountryRepository;
use PrestaShop\PrestaShop\Adapter\Zone\Repository\ZoneRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\BulkUpdateCountryZoneCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\CommandHandler\BulkUpdateCountryZoneHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\BulkCountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use PrestaShop\PrestaShop\Core\Domain\Zone\ValueObject\ZoneId;

#[AsCommandHandler]
final class BulkUpdateCountryZoneHandler extends AbstractBulkCommandHandler implements BulkUpdateCountryZoneHandlerInterface
{
    public function __construct(
        private readonly CountryRepository $countryRepository,
        private readonly ZoneRepository $zoneRepository,
    ) {
    }

    public function handle(BulkUpdateCountryZoneCommand $command): void
    {
        $zoneId = $command->getNewZoneId();
        $this->zoneRepository->assertZoneExists(new ZoneId($zoneId));

        $this->handleBulkAction($command->getCountryIds(), CountryException::class, $command);
    }

    /**
     * @param CountryId $id
     * @param BulkUpdateCountryZoneCommand $command
     */
    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        $country = $this->countryRepository->get($id);
        $country->id_zone = $command->getNewZoneId();

        $this->countryRepository->update($country);
    }

    /**
     * {@inheritDoc}
     */
    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkCountryException(
            $caughtExceptions,
            'Errors occurred during country bulk update zone action',
            BulkCountryException::FAILED_BULK_UPDATE_ZONE
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
