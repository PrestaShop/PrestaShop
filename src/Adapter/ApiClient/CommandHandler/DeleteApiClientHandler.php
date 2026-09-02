<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\ApiClient\CommandHandler;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NoResultException;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\DeleteApiClientCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\CommandHandler\DeleteApiClientHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\CannotDeleteApiClientException;
use PrestaShopBundle\Entity\Repository\ApiClientRepository;

#[AsCommandHandler]
class DeleteApiClientHandler implements DeleteApiClientHandlerInterface
{
    public function __construct(
        private readonly ApiClientRepository $repository,
    ) {
    }

    public function handle(DeleteApiClientCommand $command): void
    {
        try {
            $apiClient = $this->repository->getById($command->getApiClientId()->getValue());
        } catch (NoResultException $e) {
            throw new ApiClientNotFoundException(sprintf('Could not find Api client with ID %s', $command->getApiClientId()->getValue()), 0, $e);
        }

        try {
            $this->repository->delete($apiClient);
        } catch (ORMException $e) {
            throw new CannotDeleteApiClientException('Could not delete Api client', 0, $e);
        }
    }
}
