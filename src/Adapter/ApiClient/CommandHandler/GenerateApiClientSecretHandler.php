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
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\GenerateApiClientSecretCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\CommandHandler\GenerateApiClientSecretHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\CannotGenerateApiClientSecretException;
use PrestaShop\PrestaShop\Core\Util\String\RandomString;
use PrestaShopBundle\Entity\Repository\ApiClientRepository;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

#[AsCommandHandler]
class GenerateApiClientSecretHandler implements GenerateApiClientSecretHandlerInterface
{
    public function __construct(
        private readonly ApiClientRepository $repository,
        private readonly PasswordHasherInterface $passwordHasher
    ) {
    }

    public function handle(GenerateApiClientSecretCommand $command): string
    {
        try {
            $apiClient = $this->repository->getById($command->getApiClientId()->getValue());
        } catch (NoResultException $e) {
            throw new ApiClientNotFoundException(sprintf('Could not find Api client with ID %s', $command->getApiClientId()->getValue()), 0, $e);
        }

        try {
            $secret = RandomString::generate();
            $apiClient->setClientSecret($this->passwordHasher->hash($secret));
            $this->repository->save($apiClient);

            return $secret;
        } catch (ORMException $e) {
            throw new CannotGenerateApiClientSecretException('Could not generate new token Api client', 0, $e);
        }
    }
}
