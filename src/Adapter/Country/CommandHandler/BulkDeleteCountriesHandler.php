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
use PrestaShop\PrestaShop\Core\Domain\Country\Command\BulkDeleteCountriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\CommandHandler\BulkDeleteCountriesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\BulkCountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;

#[AsCommandHandler]
final class BulkDeleteCountriesHandler extends AbstractBulkCommandHandler implements BulkDeleteCountriesHandlerInterface
{
    public function __construct(
        private readonly CountryRepository $countryRepository,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteCountriesCommand $command): void
    {
        $this->handleBulkAction($command->getCountryIds(), CountryException::class);
    }

    /**
     * @param CountryId $id
     */
    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        $this->countryRepository->delete($id);
    }

    /**
     * {@inheritDoc}
     */
    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkCountryException(
            $caughtExceptions,
            'Errors occurred during country bulk delete action',
            BulkCountryException::FAILED_BULK_DELETE
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
