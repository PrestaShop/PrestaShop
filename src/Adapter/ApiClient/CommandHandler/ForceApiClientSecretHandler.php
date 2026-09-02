<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\ApiClient\CommandHandler;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NoResultException;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\ForceApiClientSecretCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\CommandHandler\ForceApiClientSecretHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\CannotGenerateApiClientSecretException;
use PrestaShopBundle\Entity\Repository\ApiClientRepository;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

#[AsCommandHandler]
class ForceApiClientSecretHandler implements ForceApiClientSecretHandlerInterface
{
    public function __construct(
        private readonly ApiClientRepository $repository,
        private readonly PasswordHasherInterface $passwordHasher
    ) {
    }

    public function handle(ForceApiClientSecretCommand $command): void
    {
        try {
            $apiClient = $this->repository->getById($command->getApiClientId()->getValue());
        } catch (NoResultException $e) {
            throw new ApiClientNotFoundException(sprintf('Could not find Api client with ID %s', $command->getApiClientId()->getValue()), 0, $e);
        }

        try {
            $apiClient->setClientSecret($this->passwordHasher->hash($command->getSecret()->getValue()));
            $this->repository->save($apiClient);
        } catch (ORMException $e) {
            throw new CannotGenerateApiClientSecretException('Could not generate new token Api client', 0, $e);
        }
    }
}
