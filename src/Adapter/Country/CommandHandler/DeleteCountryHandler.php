<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Country\CommandHandler;

use Country;
use PrestaShop\PrestaShop\Adapter\Country\Repository\CountryRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\DeleteCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\CommandHandler\DeleteCountryHandlerInterface;

/**
 * Handles country deletion
 */
#[AsCommandHandler]
class DeleteCountryHandler implements DeleteCountryHandlerInterface
{
    /**
     * @var CountryRepository
     */
    private $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteCountryCommand $command): void
    {
        $this->countryRepository->delete($command->getCountryId());
    }
}
